<?php

const DAY_IN_SECONDS = null;
const WP_PLUGIN_DIR  = null;

function add_menu_page( $a, $b, $c, $d, $e, $f ) {
}

function wp_verify_nonce( $a, $b ) {
}

function add_submenu_page( $a, $b, $c, $d, $e, $f ) {
}

function settings_errors() {
}

function esc_html( $a ) {
	return '';
}

function get_admin_page_title() {
}

function wp_nonce_field( $a ) {
}

function do_settings_sections( $a ) {
}

function submit_button( $a ) {
}

function wp_nonce_url( $a, $b ) {
}

function add_settings_section( $a, $b, $c, $d ) {
}

function __( $a, $b ) {
}

function add_settings_error( $a, $b, $c, $d ) {
}

function get_settings_errors() {
	return [];
}

function is_admin() {
	return false;
}

function add_thickbox() {
}

function get_user_option( $a ) {
}

function check_ajax_referer( $a ) {
}

function current_user_can( $a ) {
	return false;
}

function get_site_url() {
}

function wp_parse_url( $a ) {
	return [
		'host'   => '',
		'scheme' => '',
	];
}

function wp_create_nonce( $a ) {
}

function register_block_type( $a, $b ) {
}

function checked( $a, $b ) {
}

function wp_json_encode( $a, $b ) {
}

function add_settings_field( $a, $b, $c, $d, $e ) {
}

function register_setting( $a, $b, $c ) {
}

function esc_attr( $a ) {
	return '';
}

function esc_url_raw( $a ) {
	return '';
}

function get_option( $a, $b ) {
	if ( 1 === rand( 0, 1 ) ) {
		return [];
	}
	return false;
}

function update_option( $a, $b ) {
}

function wp_send_json( $a ) {
}

function add_shortcode( $a, $b ) {
}

function wp_localize_script( $a, $b, $c ) {
}

function wp_add_inline_style( $a, $b ) {
}

function register_activation_hook( $a, $b ) {
}

function add_action( $a, $b ) {
}

function deactivate_plugins( $a ) {
}

function plugin_basename( $a ) {
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
function wp_die( $a ) {
}

function set_transient( $a, $b, $c ) {
}

function get_transient( $a ) {
	if ( 1 === rand( 0, 1 ) ) {
		return [
			'root'      => '',
			'overriden' => '',
		];
	}
	return false;
}

function get_locale() {
	return '';
}

function admin_url( $a ) {
}

function esc_html__( $a, $b ) {
	return '';
}

function esc_url( $a ) {
	return '';
}

function delete_transient( $a ) {
}

function plugins_url( $a ) {
}

function wp_register_script( $a, $b, $c, $d, $e ) {
}

function wp_register_style( $a, $b, $c, $d ) {
}

function wp_enqueue_script( $a ) {
}

function wp_enqueue_style( $a ) {
}

function delete_option( $a ) {
}
