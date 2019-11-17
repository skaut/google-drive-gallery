<?php
/**
 * Contains all the functions for the root directory section of the basic settings page
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Admin\AdminPages\Basic\RootSelection;

if ( ! is_admin() ) {
	return;
}

/**
 * Register all the hooks for this section.
 */
function register() {
	add_action( 'admin_init', '\\Sgdg\\Admin\\AdminPages\\Basic\\RootSelection\\add' );
	add_action( 'admin_enqueue_scripts', '\\Sgdg\\Admin\\AdminPages\\Basic\\RootSelection\\register_scripts_styles' );
	add_action( 'wp_ajax_list_gdrive_dir', '\\Sgdg\\Admin\\AdminPages\\Basic\\RootSelection\\handle_ajax' );
}

/**
 * Adds the settings section and all the fields in it.
 */
function add() {
	add_settings_section( 'sgdg_root_selection', esc_html__( 'Step 2: Root directory selection', 'skaut-google-drive-gallery' ), '\\Sgdg\\Admin\\AdminPages\\Basic\\RootSelection\\html', 'sgdg_basic' );
	\Sgdg\Options::$root_path->register();
}

/**
 * Renders the header for the section.
 */
function html() {
	\Sgdg\Options::$root_path->html();
	echo( '<table class="widefat sgdg_root_selection">' );
	echo( '<thead>' );
	echo( '<tr>' );
	echo( '<th class="sgdg_root_selection_path"></th>' );
	echo( '</tr>' );
	echo( '</thead>' );
	echo( '<tbody id="sgdg_root_selection_body"></tbody>' );
	echo( '<tfoot>' );
	echo( '<tr>' );
	echo( '<td class="sgdg_root_selection_path"></td>' );
	echo( '</tr>' );
	echo( '</tfoot>' );
	echo( '</table>' );
}

/**
 * Enqueues scripts and styles for the section.
 *
 * @param string $hook The current admin page.
 */
function register_scripts_styles( $hook ) {
	\Sgdg\enqueue_style( 'sgdg_options_root', 'admin/css/options-root.min.css' );
	if ( 'toplevel_page_sgdg_basic' === $hook ) {
		\Sgdg\enqueue_script( 'sgdg_root_selection_ajax', 'admin/js/root_selection.min.js', array( 'jquery' ) );
		wp_localize_script(
			'sgdg_root_selection_ajax',
			'sgdgRootpathLocalize',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'sgdg_root_selection' ),
				'root_dir'   => \Sgdg\Options::$root_path->get( array() ),
				'drive_list' => esc_html__( 'Shared drive list', 'skaut-google-drive-gallery' ),
			)
		);
	}
}

/**
 * Ajax call handler wrapper.
 *
 * This funtion is a wrapper for `ajax_handler_body)`. This function handles exceptions and returns them in a meaningful form.
 *
 * @see ajax_handler_body()
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
 * Handles ajax requests for the root selector.
 *
 * Returns a list of all subdirectories of a directory, or a list of all drives if a directory is not provided. Additionaly, returns all the directory names for the current path.
 *
 * @throws \Sgdg\Vendor\Google_Service_Exception Google API exception.
 * @throws \Exception Insufficient role.
 */
function ajax_handler_body() {
	check_ajax_referer( 'sgdg_root_selection' );
	if ( ! current_user_can( 'manage_options' ) ) {
		throw new \Exception( esc_html__( 'Insufficient role for this action.', 'skaut-google-drive-gallery' ) );
	}
	$client = \Sgdg\Frontend\GoogleAPILib\get_drive_client();

	$path = isset( $_GET['path'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_GET['path'] ) ) : array();
	$ret  = array(
		'directories' => array(),
	);

	try {
		$ret['path'] = path_ids_to_names( $client, $path );
	} catch ( \Sgdg\Vendor\Google_Service_Exception $e ) {
		if ( 'notFound' === $e->getErrors()[0]['reason'] ) {
			$path             = array();
			$ret['path']      = array();
			$ret['resetWarn'] = esc_html__( 'Root directory wasn\'t found. The plugin may be broken until a new one is chosen.', 'skaut-google-drive-gallery' );
		} else {
			throw $e;
		}
	}

	if ( count( $path ) === 0 ) {
		$ret['directories'] = list_drives( $client );
	} else {
		$ret['directories'] = list_directories( $client, end( $path ) );
	}
	wp_send_json( $ret );
}

/**
 * Converts an array of directory IDs to directory names.
 *
 * @param \Sgdg\Vendor\Google_Service_Drive $client A Google Drive API client.
 * @param array                             $path An array of Gooogle Drive directory IDs.
 *
 * @return array An array of directory names.
 */
function path_ids_to_names( $client, $path ) {
	$ret = array();
	if ( count( $path ) > 0 ) {
		if ( 'root' === $path[0] ) {
			$ret[] = esc_html__( 'My Drive', 'skaut-google-drive-gallery' );
		} else {
			$response = $client->drives->get( $path[0], array( 'fields' => 'name' ) );
			$ret[]    = $response->getName();
		}
	}
	foreach ( array_slice( $path, 1 ) as $path_element ) {
		$response = $client->files->get(
			$path_element,
			array(
				'supportsAllDrives' => true,
				'fields'            => 'name',
			)
		);
		$ret[]    = $response->getName();
	}
	return $ret;
}

/**
 * Lists all the drives for a user.
 *
 * Returns a list of all Shared drives plus "My Drive".
 *
 * @throws \Sgdg\Vendor\Google_Service_Exception An issue with the Drive API.
 *
 * @param \Sgdg\Vendor\Google_Service_Drive $client A Google Drive API client.
 *
 * @return array An array of drive records in the format `['name' => '', 'id' => '']`
 */
function list_drives( $client ) {
	$ret        = array(
		array(
			'name' => esc_html__( 'My Drive', 'skaut-google-drive-gallery' ),
			'id'   => 'root',
		),
	);
	$page_token = null;
	do {
		$params   = array(
			'pageToken' => $page_token,
			'pageSize'  => 100,
			'fields'    => 'nextPageToken, drives(id, name)',
		);
		$response = $client->drives->listDrives( $params );
		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		foreach ( $response->getDrives() as $drive ) {
			$ret[] = array(
				'name' => $drive->getName(),
				'id'   => $drive->getId(),
			);
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );
	return $ret;
}

/**
 * Lists all the subdirectories in a directory.
 *
 * @throws \Sgdg\Vendor\Google_Service_Exception An issue with the Drive API.
 *
 * @param \Sgdg\Vendor\Google_Service_Drive $client A Google Drive API client.
 * @param string                            $root A directory to list the subdirectories of.
 *
 * @return array An array of directory records in the format `['name' => '', 'id' => '']`
 */
function list_directories( $client, $root ) {
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
			$ret[] = array(
				'name' => $file->getName(),
				'id'   => $file->getId(),
			);
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );
	return $ret;
}
