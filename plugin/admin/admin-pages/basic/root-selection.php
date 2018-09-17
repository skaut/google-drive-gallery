<?php
namespace Sgdg\Admin\AdminPages\Basic\RootSelection;

if ( ! is_admin() ) {
	return;
}

function register() {
	add_action( 'admin_init', '\\Sgdg\\Admin\\AdminPages\\Basic\\RootSelection\\add' );
	add_action( 'admin_enqueue_scripts', '\\Sgdg\\Admin\\AdminPages\\Basic\\RootSelection\\enqueue_ajax' );
	add_action( 'wp_ajax_list_gdrive_dir', '\\Sgdg\\Admin\\AdminPages\\Basic\\RootSelection\\handle_ajax' );
}

function add() {
	add_settings_section( 'sgdg_root_selection', esc_html__( 'Step 2: Root directory selection', 'skaut-google-drive-gallery' ), '\\Sgdg\\Admin\\AdminPages\\Basic\\RootSelection\\html', 'sgdg_basic' );
	\Sgdg\Options::$root_path->register();
}

function html() {
	\Sgdg\Options::$root_path->html();
	echo( '<table class="widefat">' );
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

function enqueue_ajax( $hook ) {
	if ( 'toplevel_page_sgdg_basic' === $hook ) {
		\Sgdg\enqueue_script( 'sgdg_root_selection_ajax', '/admin/js/root_selection.js', [ 'jquery' ] );
		wp_localize_script(
			'sgdg_root_selection_ajax',
			'sgdgRootpathLocalize',
			[
				'ajax_url'        => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'sgdg_root_selection' ),
				'root_dir'        => \Sgdg\Options::$root_path->get( [] ),
				'team_drive_list' => esc_html__( 'Team drive list', 'skaut-google-drive-gallery' ),
			]
		);
	}
}

function handle_ajax() {
	check_ajax_referer( 'sgdg_root_selection' );
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$client = \Sgdg\Frontend\GoogleAPILib\get_drive_client();

	$path = isset( $_GET['path'] ) ? $_GET['path'] : [];
	$ret  = [
		'path'     => path_ids_to_names( $client, $path ),
		'contents' => [],
	];

	if ( count( $path ) === 0 ) {
		$ret['contents'] = list_teamdrives( $client );
	} else {
		$ret['contents'] = list_files( $client, end( $path ) );
	}
	wp_send_json( $ret );
}

function path_ids_to_names( $client, $path ) {
	$ret = [];
	if ( count( $path ) > 0 ) {
		if ( 'root' === $path[0] ) {
			$ret[] = esc_html__( 'My Drive', 'skaut-google-drive-gallery' );
		} else {
			$response = $client->teamdrives->get( $path[0], [ 'fields' => 'name' ] );
			$ret[]    = $response->getName();
		}
	}
	foreach ( array_slice( $path, 1 ) as $path_element ) {
		$response = $client->files->get(
			$path_element,
			[
				'supportsTeamDrives' => true,
				'fields'             => 'name',
			]
		);
		$ret[]    = $response->getName();
	}
	return $ret;
}

function list_teamdrives( $client ) {
	$ret        = [
		[
			'name' => esc_html__( 'My Drive', 'skaut-google-drive-gallery' ),
			'id'   => 'root',
		],
	];
	$page_token = null;
	do {
		$params   = [
			'pageToken' => $page_token,
			'pageSize'  => 100,
			'fields'    => 'nextPageToken, teamDrives(id, name)',
		];
		$response = $client->teamdrives->listTeamdrives( $params );
		foreach ( $response->getTeamdrives() as $teamdrive ) {
			$ret[] = [
				'name' => $teamdrive->getName(),
				'id'   => $teamdrive->getId(),
			];
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );
	return $ret;
}

function list_files( $client, $root ) {
	$ret        = [];
	$page_token = null;
	do {
		$params   = [
			'q'                     => '"' . $root . '" in parents and mimeType = "application/vnd.google-apps.folder" and trashed = false',
			'supportsTeamDrives'    => true,
			'includeTeamDriveItems' => true,
			'pageToken'             => $page_token,
			'pageSize'              => 1000,
			'fields'                => 'nextPageToken, files(id, name)',
		];
		$response = $client->files->listFiles( $params );
		foreach ( $response->getFiles() as $file ) {
			$ret[] = [
				'name' => $file->getName(),
				'id'   => $file->getId(),
			];
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );
	return $ret;
}
