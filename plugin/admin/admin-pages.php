<?php
/**
 * Contains all the functions for the settings pages.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Admin\AdminPages;

require_once __DIR__ . '/admin-pages/basic.php';
require_once __DIR__ . '/admin-pages/advanced.php';

if ( ! is_admin() ) {
	return;
}

/**
 * Register the administration pages of the plugin.
 *
 * Registers all the hooks all the pages, registers the plugin into the WordPress admin menu and register a handler for OAuth redirect.
 */
function register() {
	Basic\register();
	add_action( 'admin_menu', '\\Sgdg\\Admin\\AdminPages\\add' );
	Advanced\register();
	add_action( 'admin_init', '\\Sgdg\\Admin\\AdminPages\\action_handler' );
}

/**
 * Adds the admin menu section.
 */
function add() {
	add_menu_page( __( 'Google Drive gallery', 'skaut-google-drive-gallery' ), esc_html__( 'Google Drive gallery', 'skaut-google-drive-gallery' ), 'manage_options', 'sgdg_basic', '\\Sgdg\\Admin\\AdminPages\\Basic\\html', plugins_url( '/skaut-google-drive-gallery/admin/icon.png' ) );
}

/**
 * Handles OAuth redirects.
 */
function action_handler() {
	if ( ! check_action_handler_context() ) {
		return;
	}
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.NonceVerification.Recommended
	switch ( $_GET['action'] ) {
		case 'oauth_grant':
			if ( check_nonce( 'oauth_grant' ) ) {
				\Sgdg\Admin\GoogleAPILib\oauth_grant();
			}
			break;
		case 'oauth_revoke':
			if ( false !== get_option( 'sgdg_access_token', false ) && check_nonce( 'oauth_revoke' ) ) {
				\Sgdg\Admin\GoogleAPILib\oauth_revoke();
			}
			break;
		case 'oauth_redirect':
			\Sgdg\Admin\GoogleAPILib\oauth_redirect();
			break;
	}
}

/**
 * Verifies the correct context for the action handler.
 *
 * @see action_handler
 *
 * @return bool Whether the context is valid.
 */
function check_action_handler_context() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return isset( $_GET['page'] ) && 'sgdg_basic' === $_GET['page'] && isset( $_GET['action'] );
}

/**
 * Checks the presence and validity of a WordPress nonce
 *
 * @param string $action The action for which the nonce should be valid.
 *
 * @return bool Whether the nonce is valid.
 */
function check_nonce( $action ) {
	return isset( $_GET['_wpnonce'] ) && false !== wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), $action );
}
