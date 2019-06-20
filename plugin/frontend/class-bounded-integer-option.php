<?php
/**
 * Contains the Bounded_Integer_Option class
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

require_once __DIR__ . '/class-option.php';

/**
 * An option representing an integer with a minimal value
 *
 * @see Integer_Option
 */
class Bounded_Integer_Option extends Integer_Option {
	/**
	 * The minimum value that this option can have.
	 *
	 * @var int $minimum
	 */
	protected $minimum;

	/**
	 * Bounded_Integer_Option class constructor.
	 *
	 * @param string $name The name of the option to be used as the key to reference it. The prefix `sgdg_` will be added automatically.
	 * @param int    $default_value The default value of the option to be returned if the option is not set.
	 * @param int    $minimum The minimum value that this option can have.
	 * @param string $page The page in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $section The section (within the selected page) in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $title A human-readable name of the option to be displayed to the user.
	 */
	public function __construct( $name, $default_value, $minimum, $page, $section, $title ) {
		parent::__construct( $name, $default_value, $page, $section, $title );
		$this->minimum = $minimum;
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
		if ( is_numeric( $value ) ) {
			return max( intval( $value ), $this->minimum );
		}
		return $this->default_value;
	}
}
