<?php
namespace Sgdg\Frontend;

require_once __DIR__ . '/class-option.php';

class IntegerOption extends Option {
	public function register() {
		register_setting(
			$this->page,
			$this->name,
			[
				'type'              => 'integer',
				'sanitize_callback' => [ $this, 'sanitize' ],
			]
		);
	}

	public function sanitize( $value ) {
		if ( is_numeric( $value ) ) {
			return intval( $value );
		}
		return $this->default_value;
	}

	public function html() {
		echo( '<input type="text" name="' . esc_attr( $this->name ) . '" value="' . esc_attr( get_option( $this->name, $this->default_value ) ) . '" class="regular-text">' );
	}
}
