<?php
/**
 * Contains the No_Access_Token_Exception class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Exceptions;

/**
 * No access token found
 */
class No_Access_Token_Exception extends Exception {
	/**
	 * No_Access_Token_Exception class constructor
	 */
	public function __construct() {
		/* translators: 1: Start of link to the settings 2: End of link to the settings */
		parent::__construct( sprintf( esc_html__( 'Google Drive gallery hasn\'t been granted permissions yet. Please %1$sconfigure%2$s the plugin and try again.', 'skaut-google-drive-gallery' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sgdg_basic' ) ) . '">', '</a>' ) );
	}
}
