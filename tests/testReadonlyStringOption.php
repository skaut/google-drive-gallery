<?php

class ReadonlyStringOptionTest extends WP_UnitTestCase {

	/**
	 * @covers Sgdg\Admin\ReadonlyStringOption::__construct()
	 */
	public function test_ctor() {
		$o = new \Sgdg\Admin\ReadonlyStringOption('name', 'value', 'page', 'section', 'title');
		$this->assertInstanceOf( '\Sgdg\Admin\ReadonlyStringOption', $o );
	}
}
