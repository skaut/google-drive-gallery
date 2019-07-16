<?php
/**
 * Contains all the functions for the OAuth revocation section of the basic settings page
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Admin\AdminPages\Basic\OAuthRevoke;

if ( ! is_admin() ) {
	return;
}

/**
 * Register all the hooks for this section.
 */
function register() {
	add_action( 'admin_init', '\\Sgdg\\Admin\\AdminPages\\Basic\\OAuthRevoke\\add' );
}

/**
 * Adds the settings section and all the fields in it.
 */
function add() {
	add_settings_section( 'sgdg_auth', esc_html__( 'Step 1: Authorization', 'skaut-google-drive-gallery' ), '\\Sgdg\\Admin\\AdminPages\\Basic\\OAuthRevoke\\html', 'sgdg_basic' );
	\Sgdg\Options::$authorized_domain->add_field();
	\Sgdg\Options::$authorized_origin->add_field();
	\Sgdg\Options::$redirect_uri->add_field();
	\Sgdg\Options::$client_id->add_field( true );
	\Sgdg\Options::$client_secret->add_field( true );
}

/**
 * Renders the header for the section.
 */
function html() {
	echo( '<a class="button button-primary" href="' . esc_url_raw( wp_nonce_url( admin_url( 'admin.php?page=sgdg_basic&action=oauth_revoke' ), 'oauth_revoke' ) ) . '">' . esc_html__( 'Revoke Permission', 'skaut-google-drive-gallery' ) . '</a>' );
}
