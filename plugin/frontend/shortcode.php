<?php
namespace Sgdg\Frontend\Shortcode;

function register() {
	add_action( 'init', '\\Sgdg\\Frontend\\Shortcode\\add' );
	add_action( 'wp_enqueue_scripts', '\\Sgdg\\Frontend\\Shortcode\\register_scripts_styles' );
}

function add() {
	add_shortcode( 'sgdg', '\\Sgdg\\Frontend\\Shortcode\\render' );
}

function register_scripts_styles() {
	\Sgdg\register_script( 'sgdg_gallery_init', '/frontend/js/shortcode.js', [ 'jquery' ] );
	\Sgdg\register_style( 'sgdg_gallery_css', '/frontend/css/shortcode.css' );

	\Sgdg\register_script( 'sgdg_imagelightbox_script', '/bundled/imagelightbox.min.js', [ 'jquery' ] );
	\Sgdg\register_style( 'sgdg_imagelightbox_style', '/bundled/imagelightbox.min.css' );
	\Sgdg\register_script( 'sgdg_imagesloaded', '/bundled/imagesloaded.pkgd.min.js', [ 'jquery' ] );
	\Sgdg\register_script( 'sgdg_justified-layout', '/bundled/justified-layout.min.js' );
}

function render( $atts ) {
	if ( isset( $atts['path'] ) && '' !== $atts['path'] ) {
		$atts['path'] = explode( '/', trim( $atts['path'], " /\t\n\r\0\x0B" ) );
	}
	return html( $atts );
}

function html( $atts ) {
	wp_enqueue_script( 'sgdg_imagelightbox_script' );
	wp_enqueue_style( 'sgdg_imagelightbox_style' );
	wp_enqueue_script( 'sgdg_imagesloaded' );
	wp_enqueue_script( 'sgdg_justified-layout' );

	$options = new \Sgdg\Frontend\Options_Proxy( $atts );

	wp_enqueue_script( 'sgdg_gallery_init' );
	wp_localize_script(
		'sgdg_gallery_init',
		'sgdgShortcodeLocalize',
		[
			'ajax_url'            => admin_url( 'admin-ajax.php' ),
			'grid_height'         => $options->get( 'grid_height' ),
			'grid_spacing'        => $options->get( 'grid_spacing' ),
			'preview_speed'       => $options->get( 'preview_speed' ),
			'preview_arrows'      => $options->get( 'preview_arrows' ),
			'preview_closebutton' => $options->get( 'preview_close_button' ),
			'preview_quitOnEnd'   => $options->get( 'preview_loop' ) === 'true' ? 'false' : 'true',
			'preview_activity'    => $options->get( 'preview_activity_indicator' ),
			'breadcrumbs_top'     => esc_html__( 'Gallery', 'skaut-google-drive-gallery' ),
			'empty_gallery'       => esc_html__( 'The gallery is empty.', 'skaut-google-drive-gallery' ),
		]
	);
	wp_enqueue_style( 'sgdg_gallery_css' );
	wp_add_inline_style( 'sgdg_gallery_css', '.sgdg-dir-name {font-size: ' . $options->get( 'dir_title_size' ) . ';}' );

	$root_path = \Sgdg\Options::$root_path->get();
	$root      = end( $root_path );
	if ( isset( $atts['path'] ) && '' !== $atts['path'] && ! empty( $atts['path'] ) ) {
		$client = \Sgdg\Frontend\GoogleAPILib\get_drive_client();
		try {
			$root = find_dir( $client, $root, $atts['path'] );
		} catch ( \Exception $e ) {
			return '<div class="sgdg-gallery-container">' . $e->getMessage() . '</div>';
		}
	}
	$hash = hash( 'sha256', $root );
	set_transient(
		'sgdg_hash_' . $hash,
		[
			'root'      => $root,
			'overriden' => $options->overriden,
		],
		DAY_IN_SECONDS
	);

	return '<div class="sgdg-gallery-container" data-sgdg-hash="' . $hash . '"><div class="sgdg-loading"><div></div></div></div>';
}

function find_dir( $client, $root, array $path ) {
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
			if ( $file->getName() === $path[0] ) {
				if ( count( $path ) === 1 ) {
					return $file->getId();
				}
				array_shift( $path );
				return find_dir( $client, $file->getId(), $path );
			}
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );
	throw new \Exception( esc_html__( 'The root directory of the gallery doesn\'t exist - it may have been deleted or renamed.', 'skaut-google-drive-gallery' ) );
}
