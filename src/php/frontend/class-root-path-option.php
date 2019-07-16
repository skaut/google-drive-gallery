<?php
/**
 * Contains the Root_Path_Option class
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

/**
 * An option representing the root path of the plugin
 *
 * @see Array_Option
 */
class Root_Path_Option extends Array_Option {
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
		$value = parent::sanitize( $value );
		if ( count( $value ) === 0 ) {
			$value = $this->default_value;
		}
		return $value;
	}
}
