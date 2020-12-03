<?php
/**
 * Contains the Unsupported_Value_Exception class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Exceptions;

/**
 * A value that is not supported was passed.
 */
class Unsupported_Value_Exception extends Exception {
	/**
	 * Unsupported_Value_Exception class constructor
	 *
	 * @param string|array|\Sgdg\Frontend\API_Fields $value The name of the value(s).
	 * @param string                                 $fn_name The name of the function the value was passed to.
	 */
	public function __construct( $value, $fn_name ) {
		if ( is_array( $value ) ) {
			$value = implode( ', ', $value );
		}
		if ( $value instanceof \Sgdg\Frontend\API_Fields ) {
			$value = $value->format();
		}
		/* translators: 1: The name of the value that isn't supported 2: The name of the function the value was passed to */
		parent::__construct( sprintf( esc_html__( 'The value "%1$s" has been passed to the function "%2$s" but is not supported by it.', 'skaut-google-drive-gallery' ), $value, $fn_name ) );
	}
}
