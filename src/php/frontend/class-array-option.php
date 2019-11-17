<?php
/**
 * Contains the Array_Option class
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

require_once __DIR__ . '/class-option.php';

/**
 * An option containing an array of values
 *
 * @see Option
 */
class Array_Option extends Option {
	/**
	 * Registers the option with WordPress.
	 */
	public function register() {
		register_setting(
			$this->page,
			$this->name,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize' ),
			)
		);
	}

	/**
	 * Sanitizes user input.
	 *
	 * This function sanitizes user input for the option (invalid values, values outside bounds etc.). This function should be passed as a `sanitize_callback` when registering the option.
	 *
	 * @see register()
	 *
	 * @param mixed $value The unsanitized user input.
	 *
	 * @return array The sanitized value to be written to the database.
	 */
	public function sanitize( $value ) {
		if ( is_string( $value ) ) {
			$value = json_decode( $value, true );
		}
		if ( null === $value ) {
			$value = $this->default_value;
		}
		if ( is_array( $value ) ) {
			return $value;
		}
		return $this->default_value;
	}

	/**
	 * Renders the UI for updating the option.
	 *
	 * This function renders (by calling `echo()`) the UI for updating the option, including the current value.
	 */
	public function html() {
		echo( '<input id="' . esc_attr( $this->name ) . '" type="hidden" name="' . esc_attr( $this->name ) . '" value="' . esc_attr( wp_json_encode( $this->get(), JSON_UNESCAPED_UNICODE ) ) . '">' );
	}
}
