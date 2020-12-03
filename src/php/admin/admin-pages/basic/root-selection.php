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
	} catch ( \Sgdg\Exceptions\File_Not_Found_Exception $_ ) {
		wp_send_json( array( 'resetWarn' => esc_html__( 'Root directory wasn\'t found. The plugin may be broken until a new one is chosen.', 'skaut-google-drive-gallery' ) ) );
	} catch ( \Sgdg\Exceptions\Exception $e ) {
		wp_send_json( array( 'error' => $e->getMessage() ) );
	} catch ( \Exception $_ ) {
		wp_send_json( array( 'error' => esc_html__( 'Unknown error.', 'skaut-google-drive-gallery' ) ) );
	}
}


/**
 * Handles ajax requests for the root selector.
 *
 * Returns a list of all subdirectories of a directory, or a list of all drives if a directory is not provided. Additionaly, returns all the directory names for the current path.
 *
 * @throws \Sgdg\Exceptions\Cant_Manage_Exception Insufficient role.
 */
function ajax_handler_body() {
	check_ajax_referer( 'sgdg_root_selection' );
	if ( ! current_user_can( 'manage_options' ) ) {
		throw new \Sgdg\Exceptions\Cant_Manage_Exception();
	}

	$path_ids = isset( $_GET['path'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_GET['path'] ) ) : array();

	$path_id_promise   = path_ids_to_names( $path_ids );
	$directory_promise = count( $path_ids ) === 0 ? list_drives() : \Sgdg\API_Client::list_directories( end( $path_ids ), new \Sgdg\Frontend\API_Fields( array( 'id', 'name' ) ) );

	wp_send_json(
		\Sgdg\API_Client::execute(
			array(
				'path'        => $path_id_promise,
				'directories' => $directory_promise,
			)
		)
	);
}

/**
 * Converts an array of directory IDs to directory names.
 *
 * @param array $path An array of Gooogle Drive directory IDs.
 *
 * @return \Sgdg\Vendor\GuzzleHttp\Promise\Promise An array of directory names.
 */
function path_ids_to_names( $path ) {
	$promises = array();
	if ( count( $path ) > 0 ) {
		if ( 'root' === $path[0] ) {
			$promises[] = new \Sgdg\Vendor\GuzzleHttp\Promise\FulfilledPromise( esc_html__( 'My Drive', 'skaut-google-drive-gallery' ) );
		} else {
			$promises[] = \Sgdg\API_Client::get_drive_name( $path[0] );
		}
	}
	foreach ( array_slice( $path, 1 ) as $path_element ) {
		$promises[] = \Sgdg\API_Client::get_file_name( $path_element );
	}
	return \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all( $promises );
}

/**
 * Lists all the drives for a user.
 *
 * Returns a list of all Shared drives plus "My Drive".
 *
 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface An array of drive records in the format `['name' => '', 'id' => '']`
 */
function list_drives() {
	return \Sgdg\API_Client::list_drives()->then(
		static function( $drives ) {
			return array_merge(
				array(
					array(
						'name' => esc_html__( 'My Drive', 'skaut-google-drive-gallery' ),
						'id'   => 'root',
					),
				),
				$drives
			);
		}
	);
}
