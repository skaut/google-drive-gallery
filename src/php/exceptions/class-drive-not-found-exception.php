<?php
/**
 * Contains the Drive_Not_Found_Exception class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Exceptions;

/**
 * The requested drive wasn't found.
 */
class Drive_Not_Found_Exception extends Exception {
	/**
	 * Drive_Not_Found_Exception class constructor
	 */
	public function __construct() {
		parent::__construct( esc_html__( "The requested shared drive couldn't be found.", 'skaut-google-drive-gallery' ) );
	}
}
