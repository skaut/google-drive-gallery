<?php
/**
 * Contains the Path_Not_Found_Exception class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Exceptions;

/**
 * The requested path doesn't exist in this gallery.
 */
class Path_Not_Found_Exception extends Exception {
	/**
	 * Path_Not_Found_Exception class constructor
	 */
	public function __construct() {
		parent::__construct( esc_html__( 'No such directory found in this gallery - it may have been deleted or renamed.', 'skaut-google-drive-gallery' ) );
	}
}
