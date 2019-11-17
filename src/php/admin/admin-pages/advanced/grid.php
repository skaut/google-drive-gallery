<?php
/**
 * Contains all the functions for the image grid section of the advanced settings page
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Admin\AdminPages\Advanced\Grid;

if ( ! is_admin() ) {
	return;
}

/**
 * Register all the hooks for the section.
 */
function register() {
	add_action( 'admin_init', '\\Sgdg\\Admin\\AdminPages\\Advanced\\Grid\\add' );
	add_action( 'admin_enqueue_scripts', '\\Sgdg\\Admin\\AdminPages\\Advanced\\Grid\\register_scripts_styles' );
}

/**
 * Adds the settings section and all the fields in it.
 */
function add() {
	add_settings_section( 'sgdg_grid', esc_html__( 'Image grid', 'skaut-google-drive-gallery' ), '\\Sgdg\\Admin\\AdminPages\\Advanced\\Grid\\html', 'sgdg_advanced' );
	\Sgdg\Options::$grid_height->add_field();
	\Sgdg\Options::$grid_spacing->add_field();
	\Sgdg\Options::$dir_title_size->add_field();
	\Sgdg\Options::$dir_counts->add_field();
	\Sgdg\Options::$page_size->add_field();
	\Sgdg\Options::$page_autoload->add_field();
	\Sgdg\Options::$image_ordering->add_field();
	\Sgdg\Options::$dir_ordering->add_field();
	\Sgdg\Options::$dir_prefix->add_field();
}

/**
 * Enqueues styles for the section.
 */
function register_scripts_styles() {
	\Sgdg\enqueue_style( 'sgdg_options_grid', 'admin/css/options-grid.min.css' );
}

/**
 * Renders the header for the section.
 *
 * Currently no-op.
 */
function html() {}
