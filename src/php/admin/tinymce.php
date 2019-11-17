<?php
/**
 * Contains all the functions for the TinyMCE plugin.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Admin\TinyMCE;

if ( ! is_admin() ) {
	return;
}

/**
 * Registers all the hooks for the TinyMCE plugin and the "list_gallery_dir" AJAX endpoint
 */
function register() {
	add_action( 'media_buttons', '\\Sgdg\\Admin\\TinyMCE\\add' );
	add_action( 'wp_enqueue_media', '\\Sgdg\\Admin\\TinyMCE\\register_scripts_styles' );
	add_action( 'wp_ajax_list_gallery_dir', '\\Sgdg\\Admin\\TinyMCE\\handle_ajax' );
}

/**
 * Adds the Google Drive gallery button to TinyMCE and enables the use of ThickBox
 */
function add() {
	if ( ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) || 'true' !== get_user_option( 'rich_editing' ) ) {
		return;
	}
	echo( '<a href="#" id="sgdg-tinymce-button" class="button"><img class="sgdg-tinymce-button-icon" src="' . esc_attr( plugins_url( '/skaut-google-drive-gallery/admin/icon.png' ) ) . '">' . esc_html__( 'Google Drive gallery', 'skaut-google-drive-gallery' ) . '</a>' );
	add_thickbox();
}

/**
 * Enqueues the scripts and styles used by the Tiny MCE plugin.
 */
function register_scripts_styles() {
	if ( ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) || 'true' !== get_user_option( 'rich_editing' ) ) {
		return;
	}
	\Sgdg\enqueue_style( 'sgdg_tinymce', 'admin/css/tinymce.min.css' );
	\Sgdg\enqueue_script( 'sgdg_tinymce', 'admin/js/tinymce.min.js' );
	wp_localize_script(
		'sgdg_tinymce',
		'sgdgTinymceLocalize',
		array(
			'dialog_title'  => esc_html__( 'Google Drive gallery', 'skaut-google-drive-gallery' ),
			'root_name'     => esc_html__( 'Google Drive gallery', 'skaut-google-drive-gallery' ),
			'insert_button' => esc_html__( 'Insert', 'skaut-google-drive-gallery' ),
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'sgdg_editor_plugin' ),
		)
	);
}

/**
 * Handles errors for the "list_gallery_dir" AJAX endpoint.
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
 * Actually handles the "list_gallery_dir" AJAX endpoint.
 *
 * Returns a list of all directories inside the last directory of a path.
 *
 * @throws \Exception An invalid path or a Google Drive API exception or the plugin isn't configured properly.
 */
function ajax_handler_body() {
	check_ajax_referer( 'sgdg_editor_plugin' );
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		throw new \Exception( esc_html__( 'Insufficient role for this action.', 'skaut-google-drive-gallery' ) );
	}
	if ( false === get_option( 'sgdg_access_token', false ) ) {
		/* translators: 1: Start of link to the settings 2: End of link to the settings */
		throw new \Exception( sprintf( esc_html__( 'Google Drive gallery hasn\'t been granted permissions yet. Please %1$sconfigure%2$s the plugin and try again.', 'skaut-google-drive-gallery' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sgdg_basic' ) ) . '">', '</a>' ) );
	}

	$client = \Sgdg\Frontend\GoogleAPILib\get_drive_client();

	$path = isset( $_GET['path'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_GET['path'] ) ) : array();
	$ret  = walk_path( $client, $path );

	wp_send_json( array( 'directories' => $ret ) );
}

/**
 * Returns a list of all directories inside the last directory of a path
 *
 * @param \Sgdg\Vendor\Google_Service_Drive $client A Google Drive API client.
 * @param array                             $path a path represented as an array of directory names.
 * @param string|null                       $root The root directory relative to which the path is taken. If null, the root directory of the plugin is used. Default null.
 *
 * @throws \Exception An invalid path or a Google Drive API exception.
 *
 * @return array A list of directory names.
 */
function walk_path( $client, array $path, $root = null ) {
	if ( ! isset( $root ) ) {
		$root_path = \Sgdg\Options::$root_path->get();
		$root      = end( $root_path );
	}
	if ( 0 === count( $path ) ) {
		return list_files( $client, $root );
	}
	$page_token = null;
	do {
		$params   = array(
			'q'                         => '"' . $root . '" in parents and mimeType = "application/vnd.google-apps.folder" and trashed = false',
			'supportsAllDrives'         => true,
			'includeItemsFromAllDrives' => true,
			'pageToken'                 => $page_token,
			'pageSize'                  => 1000,
			'fields'                    => 'nextPageToken, files(id, name)',
		);
		$response = $client->files->listFiles( $params );
		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		foreach ( $response->getFiles() as $file ) {
			if ( $file->getName() === $path[0] ) {
				array_shift( $path );
				return walk_path( $client, $path, $file->getId() );
			}
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );
	throw new \Exception( esc_html__( 'No such directory found - it may have been deleted or renamed. ', 'skaut-google-drive-gallery' ) );
}

/**
 * Lists all directories inside a directory
 *
 * @param \Sgdg\Vendor\Google_Service_Drive $client A Google Drive API client.
 * @param string                            $root A directory to list the subdirectories of.
 *
 * @throws \Sgdg\Vendor\Google_Service_Exception A Google Drive API exception.
 *
 * @return array A list of directory names.
 */
function list_files( $client, $root ) {
	$ret        = array();
	$page_token = null;
	do {
		$params   = array(
			'q'                         => '"' . $root . '" in parents and mimeType = "application/vnd.google-apps.folder" and trashed = false',
			'supportsAllDrives'         => true,
			'includeItemsFromAllDrives' => true,
			'pageToken'                 => $page_token,
			'pageSize'                  => 1000,
			'fields'                    => 'nextPageToken, files(id, name)',
		);
		$response = $client->files->listFiles( $params );
		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		foreach ( $response->getFiles() as $file ) {
			$ret[] = $file->getName();
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );
	return $ret;
}
