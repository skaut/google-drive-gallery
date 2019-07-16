<?php
/**
 * Contains all the functions for the lightbox section of the advanced settings page
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Admin\AdminPages\Advanced\Lightbox;

if ( ! is_admin() ) {
	return;
}

/**
 * Register all the hooks for this section.
 */
function register() {
	add_action( 'admin_init', '\\Sgdg\\Admin\\AdminPages\\Advanced\\Lightbox\\add' );
}

/**
 * Adds the settings section and all the fields in it.
 */
function add() {
	add_settings_section( 'sgdg_lightbox', esc_html__( 'Image popup', 'skaut-google-drive-gallery' ), '\\Sgdg\\Admin\\AdminPages\\Advanced\\Lightbox\\html', 'sgdg_advanced' );
	\Sgdg\Options::$preview_size->add_field();
	\Sgdg\Options::$preview_speed->add_field();
	\Sgdg\Options::$preview_arrows->add_field();
	\Sgdg\Options::$preview_close_button->add_field();
	\Sgdg\Options::$preview_loop->add_field();
	\Sgdg\Options::$preview_activity_indicator->add_field();
	\Sgdg\Options::$preview_captions->add_field();
}

/**
 * Renders the header for the section.
 *
 * Currently no-op.
 */
function html() {}
