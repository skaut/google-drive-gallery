<?php
/**
 * Contains the Boolean_Option class
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

require_once __DIR__ . '/class-option.php';

/**
 * An option representing a true/false value
 *
 * @see Option
 */
class Boolean_Option extends Option {
	/**
	 * Boolean_Option class constructor.
	 *
	 * @param string $name The name of the option to be used as the key to reference it. The prefix `sgdg_` will be added automatically.
	 * @param bool   $default_value The default value of the option to be returned if the option is not set.
	 * @param string $page The page in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $section The section (within the selected page) in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $title A human-readable name of the option to be displayed to the user.
	 */
	public function __construct( $name, $default_value, $page, $section, $title ) {
		parent::__construct( $name, ( $default_value ? '1' : '0' ), $page, $section, $title );
	}

	/**
	 * Registers the option with WordPress.
	 */
	public function register() {
		register_setting(
			$this->page,
			$this->name,
			array(
				'type'              => 'boolean',
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
	 * @return int The sanitized value to be written to the database.
	 */
	public function sanitize( $value ) {
		if ( isset( $value ) && ( '1' === $value || 1 === $value ) ) {
			return 1;
		}
		return 0;
	}

	/**
	 * Renders the UI for updating the option.
	 *
	 * This function renders (by calling `echo()`) the UI for updating the option, including the current value.
	 */
	public function html() {
		echo( '<input type="checkbox" name="' . esc_attr( $this->name ) . '" value="1"' );
		checked( get_option( $this->name, $this->default_value ), '1' );
		echo( '>' );
	}

	/**
	 * Gets the value of the option.
	 *
	 * Returns the value of the option, or a default value if it isn't defined.
	 *
	 * @see $default_value
	 *
	 * @param string|null $default_value The default value to be returned if the option isn't defined. If it is null, the $default_value property will be used instead. Default null.
	 * @return string The value of the option.
	 */
	public function get( $default_value = null ) {
		return ( parent::get( $default_value ) === '1' ? 'true' : 'false' );
	}
}
