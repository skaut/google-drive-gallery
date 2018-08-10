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

function render( $atts = [] ) {
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	wp_enqueue_script( 'sgdg_imagelightbox_script' );
	wp_enqueue_style( 'sgdg_imagelightbox_style' );
	wp_enqueue_script( 'sgdg_imagesloaded' );
	wp_enqueue_script( 'sgdg_justified-layout' );

	wp_enqueue_script( 'sgdg_gallery_init' );
	wp_localize_script( 'sgdg_gallery_init', 'sgdgShortcodeLocalize', [
		'ajax_url'            => admin_url( 'admin-ajax.php' ),
		'grid_height'         => \Sgdg\Options::$grid_height->get(),
		'grid_spacing'        => \Sgdg\Options::$grid_spacing->get(),
		'preview_speed'       => \Sgdg\Options::$preview_speed->get(),
		'preview_arrows'      => \Sgdg\Options::$preview_arrows->get(),
		'preview_closebutton' => \Sgdg\Options::$preview_close_button->get(),
		'preview_quitOnEnd'   => \Sgdg\Options::$preview_loop->get_inverted(),
		'preview_activity'    => \Sgdg\Options::$preview_activity_indicator->get(),
		'breadcrumbs_top'     => esc_html__( 'Gallery', 'skaut-google-drive-gallery' ),
	]);
	wp_enqueue_style( 'sgdg_gallery_css' );
	wp_add_inline_style( 'sgdg_gallery_css', '.sgdg-dir-name {font-size: ' . \Sgdg\Options::$dir_title_size->get() . ';}' );

	$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$nonce    = '';
	for ( $i = 0; $i < 128; $i++ ) {
		$nonce .= $keyspace[ wp_rand( 0, strlen( $keyspace ) - 1 ) ];
	}
	$value = isset( $atts['path'] ) ? $atts['path'] : '';

	set_transient( 'sgdg_nonce_' . $nonce, $value, 2 * HOUR_IN_SECONDS );
	return '<div class="sgdg-gallery-container" data-sgdg-hash="' . substr( hash( 'sha256', $value ), 0, 8 ) . '" data-sgdg-nonce="' . $nonce . '"><div class="sgdg-spinner"></div></div>';
}
