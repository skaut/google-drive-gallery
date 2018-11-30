<?php
namespace Sgdg\Admin\AdminPages\Advanced\Grid;

if ( ! is_admin() ) {
	return;
}

function register() {
	add_action( 'admin_init', '\\Sgdg\\Admin\\AdminPages\\Advanced\\Grid\\add' );
	add_action( 'admin_enqueue_scripts', '\\Sgdg\\Admin\\AdminPages\\Advanced\\Grid\\register_scripts_styles' );
}

function add() {
	add_settings_section( 'sgdg_grid', esc_html__( 'Image grid', 'skaut-google-drive-gallery' ), '\\Sgdg\\Admin\\AdminPages\\Advanced\\Grid\\html', 'sgdg_advanced' );
	\Sgdg\Options::$grid_height->add_field();
	\Sgdg\Options::$grid_spacing->add_field();
	\Sgdg\Options::$dir_title_size->add_field();
	\Sgdg\Options::$dir_counts->add_field();
	\Sgdg\Options::$image_ordering->add_field();
	\Sgdg\Options::$dir_ordering->add_field();
	\Sgdg\Options::$dir_prefix->add_field();
}

function register_scripts_styles() {
	\Sgdg\enqueue_style( 'sgdg_options_grid', '/admin/css/options-grid.css' );
}

function html() {}
