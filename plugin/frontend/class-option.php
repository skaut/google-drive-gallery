<?php
namespace Sgdg\Frontend;

abstract class Option {
	protected $name;
	protected $default_value;
	protected $page;
	protected $section;
	protected $title;

	public function __construct( $name, $default_value, $page, $section, $title ) {
		$this->name = 'sgdg_' . $name;
		// @phan-suppress-next-line PhanTypeMismatchProperty
		$this->default_value = $default_value;
		$this->page          = 'sgdg_' . $page;
		$this->section       = 'sgdg_' . $section;
		$this->title         = $title;
	}

	abstract public function register();

	public function sanitize( $value ) {
		return $value;
	}

	public function add_field() {
		$this->register();
		add_settings_field( $this->name, $this->title, [ $this, 'html' ], $this->page, $this->section );
	}

	abstract public function html();

	public function get( $default_value = null ) {
		return get_option( $this->name, ( isset( $default_value ) ? $default_value : $this->default_value ) );
	}

	public function get_title() {
		return $this->title;
	}
}
