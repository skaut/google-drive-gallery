<?php
/**
 * Contains the Directory_Not_Found_Exception class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Exceptions;

/**
 * Directory not found
 */
class Directory_Not_Found_Exception extends Exception {
	/**
	 * Directory_Not_Found_Exception class constructor
	 *
	 * @param string|null $directory_name The name of the directory that wasn't found.
	 */
	public function __construct( $directory_name = null ) {
		if ( ! is_null( $directory_name ) ) {
			/* translators: 1: The name of the directory that wasn't found */
			parent::__construct( sprintf( esc_html__( 'Directory "%1$s" couldn\'t be found - it may have been deleted or renamed.', 'skaut-google-drive-gallery' ), $directory_name ) );
		} else {
			parent::__construct( esc_html__( 'The requested directory couldn\'t be found.', 'skaut-google-drive-gallery' ) );
		}
	}
}
