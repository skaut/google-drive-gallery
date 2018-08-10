<?php
namespace Sgdg\Frontend;

require_once __DIR__ . '/class-option.php';

class BooleanOption extends Option {
	public function __construct( $name, $default_value, $page, $section, $title ) {
		parent::__construct( $name, ( $default_value ? '1' : '0' ), $page, $section, $title );
	}

	public function register() {
		register_setting( $this->page, $this->name, [
			'type'              => 'boolean',
			'sanitize_callback' => [ $this, 'sanitize' ],
		]);
	}

	public function sanitize( $value ) {
		if ( isset( $value ) && ( '1' === $value || 1 === $value ) ) {
			return 1;
		}
		return 0;
	}

	public function html() {
		echo( '<input type="checkbox" name="' . esc_attr( $this->name ) . '" value="1"' );
		checked( get_option( $this->name, $this->default_value ), '1' );
		echo( '>' );
	}

	public function get( $default_value = null ) {
		return ( parent::get( $default_value ) === '1' ? 'true' : 'false' );
	}

	public function get_inverted( $default_value = null ) {
		return ( parent::get( $default_value ) === '1' ? 'false' : 'true' );
	}
}
