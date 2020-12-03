<?php
/**
 * Contains the File_Not_Found_Exception class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Exceptions;

/**
 * The requested file wasn't found.
 */
class File_Not_Found_Exception extends Exception {
	/**
	 * File_Not_Found_Exception class constructor
	 */
	public function __construct() {
		parent::__construct( esc_html__( "The requested file couldn't be found.", 'skaut-google-drive-gallery' ) );
	}
}
