<?php
/**
 * Contains wrappers around the Google API.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend\GoogleAPILib;

/**
 * Returns a fully set-up Google client.
 *
 * @return \Sgdg\Vendor\Google_Client
 */
function get_raw_client() {
	$client = new \Sgdg\Vendor\Google_Client();
	$client->setAuthConfig(
		array(
			'client_id'     => \Sgdg\Options::$client_id->get(),
			'client_secret' => \Sgdg\Options::$client_secret->get(),
			'redirect_uris' => array( esc_url_raw( admin_url( 'admin.php?page=sgdg_basic&action=oauth_redirect' ) ) ),
		)
	);
	$client->setAccessType( 'offline' );
	$client->setApprovalPrompt( 'force' );
	$client->addScope( \Sgdg\Vendor\Google_Service_Drive::DRIVE_READONLY );
	return $client;
}

/**
 * Returns a fully set-up Google Drive API client.
 *
 * @throws \Exception Not authorized.
 *
 * @return \Sgdg\Vendor\Google_Service_Drive
 */
function get_drive_client() {
	$client       = \Sgdg\Frontend\GoogleAPILib\get_raw_client();
	$access_token = get_option( 'sgdg_access_token', false );
	if ( false === $access_token ) {
		throw new \Exception( esc_html__( 'Not authorized.', 'skaut-google-drive-gallery' ) );
	}
	$client->setAccessToken( $access_token );

	if ( $client->isAccessTokenExpired() ) {
		$client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken() );
		$new_access_token    = $client->getAccessToken();
		$merged_access_token = array_merge( $access_token, $new_access_token );
		update_option( 'sgdg_access_token', $merged_access_token );
	}

	return new \Sgdg\Vendor\Google_Service_Drive( $client );
}
