<?php
namespace Sgdg\Frontend;

require_once __DIR__ . '/class-option.php';

class OrderingOption extends Option {
	public function __construct( $name, $default_by, $default_order, $page, $section, $title ) {
		parent::__construct(
			$name,
			[
				'by'    => ( 'name' === $default_by ? 'name' : 'time' ),
				'order' => ( 'ascending' === $default_order ? 'ascending' : 'descending' ),
			],
			$page,
			$section,
			$title
		);
	}

	public function register() {
		register_setting(
			$this->page,
			$this->name . '_order',
			[
				'type'              => 'string',
				'sanitize_callback' => [ $this, 'sanitize_order' ],
			]
		);
		register_setting(
			$this->page,
			$this->name . '_by',
			[
				'type'              => 'string',
				'sanitize_callback' => [ $this, 'sanitize' ],
			]
		);
	}

	public function sanitize_order( $value ) {
		if ( 'ascending' === $value ) {
			return 'ascending';
		}
		if ( 'descending' === $value ) {
			return 'descending';
		}
		return $this->default_value['order'];
	}

	public function sanitize( $value ) {
		if ( 'time' === $value ) {
			return 'time';
		}
		if ( 'name' === $value ) {
			return 'name';
		}
		return $this->default_value['by'];
	}

	public function add_field() {
		$this->register();
		add_settings_field( $this->name . '_order', $this->title, [ $this, 'html_order' ], $this->page, $this->section );
		add_settings_field( $this->name . '_by', '', [ $this, 'html' ], $this->page, $this->section );
	}

	public function html_order() {
		echo( '<select name="' . esc_attr( $this->name ) . '_order">' );
		echo( '<option value="ascending"' . ( $this->get_order() === 'ascending' ? ' selected' : '' ) . '>' . esc_html__( 'Ascending', 'skaut-google-drive-gallery' ) . '</option>' );
		echo( '<option value="descending"' . ( $this->get_order() === 'descending' ? ' selected' : '' ) . '>' . esc_html__( 'Descending', 'skaut-google-drive-gallery' ) . '</option>' );
		echo( '</select>' );
	}

	public function html() {
		echo( '<label for="sgdg-' . esc_attr( $this->name ) . '-by-time"><input type="radio" id="sgdg-' . esc_attr( $this->name ) . '-by-time" name="' . esc_attr( $this->name ) . '_by" value="time"' . ( $this->get_by() === 'time' ? ' checked' : '' ) . '>' . esc_html__( 'By time', 'skaut-google-drive-gallery' ) . '</label><br>' );
		echo( '<label for="sgdg-' . esc_attr( $this->name ) . '-by-name"><input type="radio" id="sgdg-' . esc_attr( $this->name ) . '-by-name" name="' . esc_attr( $this->name ) . '_by" value="name"' . ( $this->get_by() === 'name' ? ' checked' : '' ) . '>' . esc_html__( 'By name', 'skaut-google-drive-gallery' ) . '</label>' );
	}

	public function get_order( $default_value = null ) {
		return get_option( $this->name . '_order', ( isset( $default_value ) ? $default_value : $this->default_value['order'] ) );
	}

	public function get_by( $default_value = null ) {
		return get_option( $this->name . '_by', ( isset( $default_value ) ? $default_value : $this->default_value['by'] ) );
	}

	public function get( $default_value = null ) {
		return ( $this->get_by( $default_value['by'] ) === 'name' ? 'name_natural' : 'modifiedTime' ) . ( $this->get_order( $default_value['order'] ) === 'ascending' ? '' : ' desc' );
	}
}
