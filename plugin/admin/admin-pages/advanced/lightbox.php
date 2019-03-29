<?php
namespace Sgdg\Admin\AdminPages\Advanced\Lightbox;

if ( ! is_admin() ) {
	return;
}

function register() {
	add_action( 'admin_init', '\\Sgdg\\Admin\\AdminPages\\Advanced\\Lightbox\\add' );
}

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

function html() {}
