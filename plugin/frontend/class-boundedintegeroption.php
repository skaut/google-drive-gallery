<?php
namespace Sgdg\Frontend;

require_once __DIR__ . '/class-option.php';

class BoundedIntegerOption extends IntegerOption {
	protected $minimum;

	public function __construct( $name, $default_value, $minimum, $page, $section, $title ) {
		parent::__construct( $name, $default_value, $page, $section, $title );
		$this->minimum = $minimum;
	}

	public function sanitize( $value ) {
		if ( is_numeric( $value ) ) {
			return max( intval( $value ), $this->minimum );
		}
		return $this->default_value;
	}
}
