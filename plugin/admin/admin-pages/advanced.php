<?php
/**
 * Contains all the functions for the advanced settings page
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Admin\AdminPages\Advanced;

require_once __DIR__ . '/advanced/grid.php';
require_once __DIR__ . '/advanced/lightbox.php';

if ( ! is_admin() ) {
	return;
}

/**
 * Register all the hooks for the page.
 */
function register() {
	add_action( 'admin_menu', '\\Sgdg\\Admin\\AdminPages\\Advanced\\add' );
	Grid\register();
	Lightbox\register();
}

/**
 * Adds the settings page.
 */
function add() {
	add_submenu_page( 'sgdg_basic', __( 'Advanced options', 'skaut-google-drive-gallery' ), esc_html__( 'Advanced options', 'skaut-google-drive-gallery' ), 'manage_options', 'sgdg_advanced', '\\Sgdg\\Admin\\AdminPages\\Advanced\\html' );
}

/**
 * Renders the settings page.
 */
function html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$help_link = 'https://napoveda.skaut.cz/dobryweb/' . substr( get_locale(), 0, 2 ) . '-skaut-google-drive-gallery';
	/* translators: 1: Start of a help link 2: End of the help link */
	add_settings_error( 'general', 'help', sprintf( esc_html__( 'See the %1$sdocumentation%2$s for more information about how to configure the plugin.', 'skaut-google-drive-gallery' ), '<a href="' . esc_url( $help_link ) . '" target="_blank">', '</a>' ), 'notice-info' );

	settings_errors();
	echo( '<div class="wrap">' );
	echo( '<h1>' . esc_html( get_admin_page_title() ) . '</h1>' );
	echo( '<form action="options.php?action=update&option_page=sgdg_advanced" method="post">' );
	wp_nonce_field( 'sgdg_advanced-options' );
	do_settings_sections( 'sgdg_advanced' );
	submit_button( esc_html__( 'Save Changes', 'skaut-google-drive-gallery' ) );
	echo( '</form>' );
	echo( '</div>' );
}
