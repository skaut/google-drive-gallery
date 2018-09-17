<?php
namespace Sgdg\Frontend\Block;

function register() {
	if ( function_exists( 'register_block_type' ) ) {
		add_action( 'init', '\\Sgdg\\Frontend\\Block\\add' );
	}
}

function add() {
	\Sgdg\register_script( 'sgdg_block', '/frontend/js/block.js', [ 'wp-blocks', 'wp-element' ] );
	wp_localize_script(
		'sgdg_block',
		'sgdgBlockLocalize',
		[
			'block_name'        => esc_html__( 'Google Drive gallery', 'skaut-google-drive-gallery' ),
			'block_description' => esc_html__( 'A WordPress gallery using Google Drive as file storage', 'skaut-google-drive-gallery' ),
			'root_name'         => esc_html__( 'Google Drive gallery', 'skaut-google-drive-gallery' ),
			'ajax_url'          => admin_url( 'admin-ajax.php' ),
			'nonce'             => wp_create_nonce( 'sgdg_editor_plugin' ),
		]
	);
	\Sgdg\enqueue_style( 'sgdg_block', '/frontend/css/block.css' );
	register_block_type(
		'skaut-google-drive-gallery/gallery',
		[
			'editor_script'   => 'sgdg_block',
			'render_callback' => '\\Sgdg\\Frontend\\Block\\html',
		]
	);
}

function html( $attributes ) {
	return \Sgdg\Frontend\Shortcode\html( $attributes );
}
