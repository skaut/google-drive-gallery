<?php
/**
 * Contains the Not_Found_Exception class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Exceptions;

/**
 * The requested resource wasn't found.
 */
class Not_Found_Exception extends Exception {
	/**
	 * Not_Found_Exception class constructor
	 */
	public function __construct() {
		parent::__construct( esc_html__( "The requested resource couldn't be found.", 'skaut-google-drive-gallery' ) );
	}
}
