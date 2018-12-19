<?php
namespace Sgdg\Frontend\Block;

function register() {
	if ( function_exists( 'register_block_type' ) ) {
		add_action( 'init', '\\Sgdg\\Frontend\\Block\\add' );
	}
}

function add() {
	\Sgdg\register_script( 'sgdg_block_icon', '/frontend/js/iconSvg.js', [ 'wp-element' ] );
	\Sgdg\register_script( 'sgdg_block_settings_component', '/frontend/js/SgdgSettingsComponent.js', [ 'wp-element' ] );
	\Sgdg\register_script( 'sgdg_block_boolean_settings_component', '/frontend/js/SgdgBooleanSettingsComponent.js', [ 'wp-element', 'sgdg_block_settings_component' ] );
	\Sgdg\register_script( 'sgdg_block_integer_settings_component', '/frontend/js/SgdgIntegerSettingsComponent.js', [ 'wp-element', 'sgdg_block_settings_component' ] );
	\Sgdg\register_script( 'sgdg_block_ordering_settings_component', '/frontend/js/SgdgOrderingSettingsComponent.js', [ 'wp-element' ] );
	\Sgdg\register_script( 'sgdg_block_settings_override_component', '/frontend/js/SgdgSettingsOverrideComponent.js', [ 'wp-element', 'sgdg_block_boolean_settings_component', 'sgdg_block_integer_settings_component', 'sgdg_block_ordering_settings_component' ] );
	\Sgdg\register_script( 'sgdg_block_editor_component', '/frontend/js/SgdgEditorComponent.js', [ 'wp-element', 'sgdg_block_settings_override_component' ] );
	\Sgdg\register_script( 'sgdg_block', '/frontend/js/block.js', [ 'wp-blocks', 'wp-components', 'wp-editor', 'wp-element', 'sgdg_block_icon', 'sgdg_block_editor_component' ] );

	$options             = new \Sgdg\Frontend\Options_Proxy();
	$get_option          = function( $name ) use ( $options ) {
		return [
			'default' => $options->get( $name ),
			'name'    => $options->get_title( $name ),
		];
	};
	$get_ordering_option = function( $name ) use ( $options ) {
		return [
			'default_by'    => $options->get_by( $name ),
			'default_order' => $options->get_order( $name ),
			'name'          => $options->get_title( $name ),
		];
	};

	wp_localize_script(
		'sgdg_block',
		'sgdgBlockLocalize',
		[
			'ajax_url'                   => admin_url( 'admin-ajax.php' ),
			'nonce'                      => wp_create_nonce( 'sgdg_editor_plugin' ),
			'block_name'                 => esc_html__( 'Google Drive gallery', 'skaut-google-drive-gallery' ),
			'block_description'          => esc_html__( 'A WordPress gallery using Google Drive as file storage', 'skaut-google-drive-gallery' ),
			'root_name'                  => esc_html__( 'Google Drive gallery', 'skaut-google-drive-gallery' ),
			'settings_override'          => esc_html__( 'Settings override', 'skaut-google-drive-gallery' ),
			'grid_section_name'          => esc_html__( 'Image grid', 'skaut-google-drive-gallery' ),
			'lightbox_section_name'      => esc_html__( 'Image popup', 'skaut-google-drive-gallery' ),
			'ordering_option_ascending'  => esc_html__( 'Ascending', 'skaut-google-drive-gallery' ),
			'ordering_option_descending' => esc_html__( 'Descending', 'skaut-google-drive-gallery' ),
			'ordering_option_by_time'    => esc_html__( 'By time', 'skaut-google-drive-gallery' ),
			'ordering_option_by_name'    => esc_html__( 'By name', 'skaut-google-drive-gallery' ),
			'grid_height'                => $get_option( 'grid_height' ),
			'grid_spacing'               => $get_option( 'grid_spacing' ),
			'dir_counts'                 => $get_option( 'dir_counts' ),
			'page_size'                  => $get_option( 'page_size' ),
			'page_autoload'              => $get_option( 'page_autoload' ),
			'image_ordering'             => $get_ordering_option( 'image_ordering' ),
			'dir_ordering'               => $get_ordering_option( 'dir_ordering' ),
			'preview_size'               => $get_option( 'preview_size' ),
			'preview_loop'               => $get_option( 'preview_loop' ),
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
