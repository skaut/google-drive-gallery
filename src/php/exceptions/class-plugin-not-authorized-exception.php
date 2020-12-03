<?php
/**
 * Contains the Plugin_Not_Authorized_Exception class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Exceptions;

/**
 * The requested path doesn't exist in this gallery.
 */
class Plugin_Not_Authorized_Exception extends Exception {
	/**
	 * Plugin_Not_Authorized_Exception class constructor
	 */
	public function __construct() {
		parent::__construct( esc_html__( 'The plugin hasn\'t been authorized with Google yet. If you are the website administrator, you can do this in the plugin settings.', 'skaut-google-drive-gallery' ) );
	}
}
