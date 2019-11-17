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
	} catch ( \Sgdg\Vendor\Google_Service_Exception $e ) {
		if ( 'userRateLimitExceeded' === $e->getErrors()[0]['reason'] ) {
			wp_send_json( array( 'error' => esc_html__( 'The maximum number of requests has been exceeded. Please try again in a minute.', 'skaut-google-drive-gallery' ) ) );
		} else {
			wp_send_json( array( 'error' => $e->getErrors()[0]['message'] ) );
		}
	} catch ( \Exception $e ) {
		wp_send_json( array( 'error' => $e->getMessage() ) );
	}
}

/**
 * Actually handles the "gallery" AJAX endpoint.
 *
 * Returns the names of the directories along the user-selected path and the first page of the gallery.
 */
function ajax_handler_body() {
	list( $client, $dir, $options ) = \Sgdg\Frontend\Page\get_context();

	$ret = array();
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['path'] ) && '' !== $_GET['path'] ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$ret['path'] = path_names( $client, explode( '/', sanitize_text_field( wp_unslash( $_GET['path'] ) ) ), $options );
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$page      = isset( $_GET['page'] ) ? max( 1, intval( $_GET['page'] ) ) : 1;
	$remaining = $options->get( 'page_size' ) * $page;
	$ret       = array_merge( $ret, \Sgdg\Frontend\Page\get_page( $client, $dir, 0, $remaining, $options ) );
	wp_send_json( $ret );
}

/**
 * Adds names to a path represented as a list of directory IDs
 *
 * @param \Sgdg\Vendor\Google_Service_Drive $client A Google Drive API client.
 * @param array                             $path A list of directory IDs.
 * @param \Sgdg\Frontend\Options_Proxy      $options Gallery options.
 *
 * @throws \Sgdg\Vendor\Google_Service_Exception A Google Drive API exception.
 *
 * @return array A list of records in the format `['id' => 'id', 'name' => 'name']`.
 */
function path_names( $client, array $path, $options ) {
	$client->getClient()->setUseBatch( true );
	$batch = $client->createBatch();
	foreach ( $path as $segment ) {
		$request = $client->files->get(
			$segment,
			array(
				'supportsAllDrives' => true,
				'fields'            => 'name',
			)
		);
		// @phan-suppress-next-line PhanTypeMismatchArgument
		$batch->add( $request, $segment );
	}
	$responses = $batch->execute();
	$client->getClient()->setUseBatch( false );
	$ret = array();
	foreach ( $path as $segment ) {
		$response = $responses[ 'response-' . $segment ];
		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		$name = $response->getName();
		$pos  = false;
		if ( '' !== $options->get( 'dir_prefix' ) ) {
			$pos = mb_strpos( $name, $options->get( 'dir_prefix' ) );
		}
		$ret[] = array(
			'id'   => $segment,
			'name' => mb_substr( $name, false !== $pos ? $pos + 1 : 0 ),
		);
	}
	return $ret;
}
