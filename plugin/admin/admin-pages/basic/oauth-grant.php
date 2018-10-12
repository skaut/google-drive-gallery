<?php
namespace Sgdg\Admin\AdminPages\Basic\OAuthGrant;

if ( ! is_admin() ) {
	return;
}

function register() {
	add_action( 'admin_init', '\\Sgdg\\Admin\\AdminPages\\Basic\\OAuthGrant\\add' );
}

function add() {
	add_settings_section( 'sgdg_auth', esc_html__( 'Step 1: Authorization', 'skaut-google-drive-gallery' ), '\\Sgdg\\Admin\\AdminPages\\Basic\\OAuthGrant\\html', 'sgdg_basic' );
	\Sgdg\Options::$authorized_domain->add_field();
	\Sgdg\Options::$authorized_origin->add_field();
	\Sgdg\Options::$redirect_uri->add_field();
	\Sgdg\Options::$client_id->add_field();
	\Sgdg\Options::$client_secret->add_field();
}

function html() {
	echo( '<p>' . esc_html__( 'Create a Google app and provide the following details:', 'skaut-google-drive-gallery' ) . '</p>' );
	echo( '<a class="button button-primary" href="' . esc_url_raw( wp_nonce_url( admin_url( 'admin.php?page=sgdg_basic&action=oauth_grant' ) ), 'oauth_grant' ) . '">' . esc_html__( 'Grant Permission', 'skaut-google-drive-gallery' ) . '</a>' );
}
