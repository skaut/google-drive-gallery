<?php

class Readonly_String_Option_Test extends WP_UnitTestCase {

	/**
	 * @covers Sgdg\Admin\Readonly_String_Option::__construct()
	 */
	public function test_ctor() {
		$o = new \Sgdg\Admin\Readonly_String_Option('name', 'value', 'page', 'section', 'title');
		$this->assertInstanceOf( '\Sgdg\Admin\Readonly_String_Option', $o );
	}
}
