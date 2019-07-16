<?php
/**
 * Contains WordPress function stubs for PHAN
 *
 * @package skaut-google-drive-gallery
 */

// phpcs:disable Squiz.Commenting.FunctionComment.Missing
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

const DAY_IN_SECONDS = null;
const WP_PLUGIN_DIR  = null;

function __( $a, $b ) {
}

function add_action( $a, $b ) {
}

function add_menu_page( $a, $b, $c, $d, $e, $f ) {
}

function add_settings_error( $a, $b, $c, $d ) {
}

function add_settings_field( $a, $b, $c, $d, $e ) {
}

function add_settings_section( $a, $b, $c, $d ) {
}

function add_shortcode( $a, $b ) {
}

function add_submenu_page( $a, $b, $c, $d, $e, $f ) {
}

function add_thickbox() {
}

function admin_url( $a ) {
}

function check_ajax_referer( $a ) {
}

function checked( $a, $b ) {
}

function current_user_can( $a ) {
	return false;
}

function deactivate_plugins( $a ) {
}

function delete_option( $a ) {
}

function delete_transient( $a ) {
}

function do_settings_sections( $a ) {
}

function esc_attr( $a ) {
	return '';
}

function esc_html( $a ) {
	return '';
}

function esc_html__( $a, $b ) {
	return '';
}

function esc_url( $a ) {
	return '';
}

function esc_url_raw( $a ) {
	return '';
}

function get_admin_page_title() {
}

function get_locale() {
	return '';
}

function get_option( $a, $b ) {
	switch ( wp_rand( 0, 2 ) ) {
		case 0:
			return [];
		case 1:
			return false;
		case 2:
			return '';
	}
}

function get_settings_errors() {
	return [];
}

function get_site_url() {
}

function get_transient( $a ) {
	switch ( wp_rand( 0, 1 ) ) {
		case 0:
			return [
				'root'      => '',
				'overriden' => [],
			];
		case 1:
			return false;
	}
}

function get_user_option( $a ) {
}

function is_admin() {
	return false;
}

function plugin_basename( $a ) {
}

function plugins_url( $a ) {
}

function register_activation_hook( $a, $b ) {
}

function register_block_type( $a, $b ) {
}

function register_setting( $a, $b, $c ) {
}

function sanitize_text_field( $a ) {
	return '';
}

function set_transient( $a, $b, $c ) {
}

function settings_errors() {
}

function submit_button( $a ) {
}

function tests_add_filter( $a, $b ) {
}

function update_option( $a, $b ) {
}

function wp_add_inline_style( $a, $b ) {
}

function wp_create_nonce( $a ) {
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
function wp_die( $a ) {
}

function wp_enqueue_script( $a ) {
}

function wp_enqueue_style( $a ) {
}

function wp_json_encode( $a, $b ) {
}

function wp_localize_script( $a, $b, $c ) {
}

function wp_nonce_field( $a ) {
}

function wp_nonce_url( $a, $b ) {
}

function wp_parse_url( $a ) {
	return [
		'host'   => '',
		'scheme' => '',
	];
}

function wp_rand( $a, $b ) {
}

function wp_register_script( $a, $b, $c, $d, $e ) {
}

function wp_register_style( $a, $b, $c, $d ) {
}

function wp_send_json( $a ) {
}

function wp_unslash( $a ) {
	switch ( wp_rand( 0, 1 ) ) {
		case 0:
			return [];
		case 1:
			return '';
	}
}

function wp_verify_nonce( $a, $b ) {
}
