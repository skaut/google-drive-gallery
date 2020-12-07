<?php
/**
 * Contains all the functions used to handle the "gallery" AJAX endpoint.
 *
 * The "gallery" AJAX endpoint gets called when the gallery is initialized and the each time the user navigates the directories of the gallery. The endpoint returns the info about the currently viewed directory and the first page of the content.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend\Gallery;

/**
 * Registers the "gallery" AJAX endpoint
 */
function register() {
	add_action( 'wp_ajax_gallery', '\\Sgdg\\Frontend\\Gallery\\handle_ajax' );
	add_action( 'wp_ajax_nopriv_gallery', '\\Sgdg\\Frontend\\Gallery\\handle_ajax' );
}

/**
 * Handles errors for the "gallery" AJAX endpoint.
 *
 * This function is a wrapper around `handle_ajax_body` that handles all the possible errors that can occur and sends them back as error messages.
 */
function handle_ajax() {
	try {
		ajax_handler_body();
	} catch ( \Sgdg\Exceptions\Exception $e ) {
		wp_send_json( array( 'error' => $e->getMessage() ) );
	} catch ( \Exception $_ ) {
		wp_send_json( array( 'error' => esc_html__( 'Unknown error.', 'skaut-google-drive-gallery' ) ) );
	}
}

/**
 * Actually handles the "gallery" AJAX endpoint.
 *
 * Returns the names of the directories along the user-selected path and the first page of the gallery.
 */
function ajax_handler_body() {
	list( $parent_id, $options, $path_verification ) = \Sgdg\Frontend\Page\get_context();
	$pagination_helper                               = ( new \Sgdg\Frontend\Pagination_Helper() )->withOptions( $options, true );
	$path_names                                      = null;
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['path'] ) && '' !== $_GET['path'] ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$path_names = path_names( explode( '/', sanitize_text_field( wp_unslash( $_GET['path'] ) ) ), $options );
	}
	$page_promise = \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all( array( \Sgdg\Frontend\Page\get_page( $parent_id, $pagination_helper, $options ), $path_names ) )->then(
		static function( $wrapper ) {
			list( $page, $path_names ) = $wrapper;
			if ( ! is_null( $path_names ) ) {
				$page['path'] = $path_names;
			}
			wp_send_json( $page );
		}
	);
	\Sgdg\API_Client::execute( array( $path_verification, $page_promise ) );
}

/**
 * Adds names to a path represented as a list of directory IDs
 *
 * @param array                        $path A list of directory IDs.
 * @param \Sgdg\Frontend\Options_Proxy $options Gallery options.
 *
 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to a list of records in the format `['id' => 'id', 'name' => 'name']`.
 */
function path_names( $path, $options ) {
	return \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all(
		array_map(
			static function( $segment ) use ( &$options ) {
				return \Sgdg\API_Facade::get_file_name( $segment )->then(
					static function( $name ) use ( $segment, &$options ) {
						$pos = false;
						if ( '' !== $options->get( 'dir_prefix' ) ) {
							$pos = mb_strpos( $name, $options->get( 'dir_prefix' ) );
						}
						return array(
							'id'   => $segment,
							'name' => mb_substr( $name, false !== $pos ? $pos + 1 : 0 ),
						);
					}
				);
			},
			$path
		)
	);
}
