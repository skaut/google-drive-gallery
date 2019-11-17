<?php
/**
 * Contains the String_Option class
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

require_once __DIR__ . '/class-option.php';

/**
 * An option representing a string value
 *
 * @see Option
 */
class String_Option extends Option {
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
	 * Renders the UI for updating the option.
	 *
	 * This function renders (by calling `echo()`) the UI for updating the option, including the current value.
	 */
	public function html() {
		echo( '<input type="text" name="' . esc_attr( $this->name ) . '" value="' . esc_attr( get_option( $this->name, $this->default_value ) ) . '" class="regular-text">' );
	}
}
