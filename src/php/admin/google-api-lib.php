<?php
/**
 * Contains all the OAuth redirect handling functions, called by \Sgdg\Admin\AdminPages\action_handler()
 *
 * @see \Sgdg\Admin\AdminPages\action_handler()
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Admin\GoogleAPILib;

if ( ! is_admin() ) {
	return;
}

/**
 * Redirects to the OAuth granting URL
 */
function oauth_grant() {
	$client   = \Sgdg\Frontend\GoogleAPILib\get_raw_client();
	$auth_url = $client->createAuthUrl();
	header( 'Location: ' . esc_url_raw( $auth_url ) );
}

/**
 * Handles the redirect back from Google app permission granting and redirects back to basic settings
 */
function oauth_redirect() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $_GET['code'] ) ) {
		add_settings_error( 'general', 'oauth_failed', esc_html__( 'Google API hasn\'t returned an authentication code. Please try again.', 'skaut-google-drive-gallery' ), 'error' );
	}
	if ( count( get_settings_errors() ) === 0 && false === get_option( 'sgdg_access_token', false ) ) {
		$client = \Sgdg\Frontend\GoogleAPILib\get_raw_client();
		try {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$client->fetchAccessTokenWithAuthCode( sanitize_text_field( wp_unslash( $_GET['code'] ) ) );
			$access_token = $client->getAccessToken();

			$drive_client = new \Sgdg\Vendor\Google_Service_Drive( $client );
			\Sgdg\Admin\AdminPages\Basic\RootSelection\list_drives( $drive_client );
			update_option( 'sgdg_access_token', $access_token );
		} catch ( \Sgdg\Vendor\Google_Service_Exception $e ) {
			if ( 'accessNotConfigured' === $e->getErrors()[0]['reason'] ) {
				/* translators: %s: Link to the Google developers console */
				add_settings_error( 'general', 'oauth_failed', sprintf( esc_html__( 'Google Drive API is not enabled. Please enable it at %s and try again after a while.', 'skaut-google-drive-gallery' ), '<a href="https://console.developers.google.com/apis/library/drive.googleapis.com" target="_blank">https://console.developers.google.com/apis/library/drive.googleapis.com</a>' ), 'error' );
			} else {
				add_settings_error( 'general', 'oauth_failed', esc_html__( 'An unknown error has been encountered:', 'skaut-google-drive-gallery' ) . ' ' . $e->getErrors()[0]['message'], 'error' );
			}
		} catch ( \Sgdg\Vendor\GuzzleHttp\Exception\TransferException $e ) {
			add_settings_error( 'general', 'oauth_failed', esc_html__( 'An unknown error has been encountered:', 'skaut-google-drive-gallery' ) . ' ' . $e->getMessage(), 'error' );
		}
	}
	if ( count( get_settings_errors() ) === 0 ) {
		add_settings_error( 'general', 'oauth_updated', esc_html__( 'Permission granted.', 'skaut-google-drive-gallery' ), 'updated' );
	}
	set_transient( 'settings_errors', get_settings_errors(), 30 );
	header( 'Location: ' . esc_url_raw( admin_url( 'admin.php?page=sgdg_basic&settings-updated=true' ) ) );
}

/**
 * Revokes and deletes the OAuth token and redirects back to basic settings
 */
function oauth_revoke() {
	$client = \Sgdg\Frontend\GoogleAPILib\get_raw_client();
	try {
		$client->revokeToken();
		delete_option( 'sgdg_access_token' );
	} catch ( \Sgdg\Vendor\GuzzleHttp\Exception\TransferException $e ) {
		add_settings_error( 'general', 'oauth_failed', esc_html__( 'An unknown error has been encountered:', 'skaut-google-drive-gallery' ) . ' ' . $e->getMessage(), 'error' );
	}
	if ( count( get_settings_errors() ) === 0 ) {
		add_settings_error( 'general', 'oauth_updated', __( 'Permission revoked.', 'skaut-google-drive-gallery' ), 'updated' );
	}
	set_transient( 'settings_errors', get_settings_errors(), 30 );
	header( 'Location: ' . esc_url_raw( admin_url( 'admin.php?page=sgdg_basic&settings-updated=true' ) ) );
}
