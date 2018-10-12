<?php
namespace Sgdg\Admin\AdminPages\Basic\OAuthRevoke;

if ( ! is_admin() ) {
	return;
}

function register() {
	add_action( 'admin_init', '\\Sgdg\\Admin\\AdminPages\\Basic\\OAuthRevoke\\add' );
}

function add() {
	add_settings_section( 'sgdg_auth', esc_html__( 'Step 1: Authorization', 'skaut-google-drive-gallery' ), '\\Sgdg\\Admin\\AdminPages\\Basic\\OAuthRevoke\\html', 'sgdg_basic' );
	\Sgdg\Options::$authorized_domain->add_field();
	\Sgdg\Options::$authorized_origin->add_field();
	\Sgdg\Options::$redirect_uri->add_field();
	\Sgdg\Options::$client_id->add_field( true );
	\Sgdg\Options::$client_secret->add_field( true );
}

function html() {
	echo( '<a class="button button-primary" href="' . esc_url_raw( wp_nonce_url( admin_url( 'admin.php?page=sgdg_basic&action=oauth_revoke' ) ), 'oauth_revoke' ) . '">' . esc_html__( 'Revoke Permission', 'skaut-google-drive-gallery' ) . '</a>' );
}
