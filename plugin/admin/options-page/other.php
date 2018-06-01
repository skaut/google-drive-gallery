<?php
namespace Sgdg\Admin\OptionsPage\Other;

if ( ! is_admin() ) {
	return;
}

function register() {
	add_action( 'admin_init', '\\Sgdg\\Admin\\OptionsPage\\Other\\add' );
}

function add() {
	add_settings_section( 'sgdg_options', esc_html__( 'Step 3: Other options', 'skaut-google-drive-gallery' ), '\\Sgdg\\Admin\\OptionsPage\\Other\\html', 'sgdg' );
	\Sgdg\Options::$thumbnail_size->add_field();
	\Sgdg\Options::$thumbnail_spacing->add_field();
	\Sgdg\Options::$preview_size->add_field();
	\Sgdg\Options::$preview_speed->add_field();
	\Sgdg\Options::$dir_counts->add_field();
	\Sgdg\Options::$preview_arrows->add_field();
	\Sgdg\Options::$preview_close_button->add_field();
	\Sgdg\Options::$preview_loop->add_field();
	\Sgdg\Options::$preview_activity_indicator->add_field();
	\Sgdg\Options::$image_ordering->add_field();
	\Sgdg\Options::$dir_ordering->add_field();
}

function html() {}
