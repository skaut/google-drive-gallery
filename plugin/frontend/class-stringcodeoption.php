<?php
namespace Sgdg\Frontend;

require_once __DIR__ . '/class-stringoption.php';

class StringCodeOption extends StringOption {
	private $readonly;

	public function __construct( $name, $default_value, $page, $section, $title ) {
		parent::__construct( $name, $default_value, $page, $section, $title );
		$this->readonly = false;
	}

	public function add_field( $readonly = false ) {
		$this->readonly = $readonly;
		parent::add_field();
	}

	public function html() {
		echo( '<input type="text" name="' . esc_attr( $this->name ) . '" value="' . esc_attr( get_option( $this->name, $this->default_value ) ) . '" ' . ( $this->readonly ? 'readonly ' : '' ) . 'class="regular-text code">' );
	}
}
