<?php
/**
 * Contains the Readonly_String_Option_Test class
 *
 * @package skaut-google-drive-gallery
 */

/**
 * Contains unit tests for the Readonly_String_Option class
 */
class Readonly_String_Option_Test extends WP_UnitTestCase {
	/**
	 * Tests the constructor
	 *
	 * @covers Sgdg\Admin\Readonly_String_Option::__construct()
	 */
	public function test_ctor() {
		$option = new \Sgdg\Admin\Readonly_String_Option( 'name', 'value', 'page', 'section', 'title' );
		$this->assertInstanceOf( '\Sgdg\Admin\Readonly_String_Option', $option );
	}
}
