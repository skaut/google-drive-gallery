<?php
/**
 * Contains the Ordering_Option class
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

require_once __DIR__ . '/class-option.php';

/**
 * An option representing an ordering of items in a gallery. Items can be ordered either by name or by time in ascending or descending order.
 *
 * @see Option
 */
class Ordering_Option extends Option {
	/**
	 * Ordering_Option class constructor.
	 *
	 * @param string $name The name of the option to be used as the key to reference it. The prefix `sgdg_` will be added automatically.
	 * @param string $default_by What to order by by default. Accepts `name` or `time`.
	 * @param string $default_order Which way to order by default. Accepts `ascending` or `descending`.
	 * @param string $page The page in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $section The section (within the selected page) in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $title A human-readable name of the option to be displayed to the user.
	 */
	public function __construct( $name, $default_by, $default_order, $page, $section, $title ) {
		parent::__construct(
			$name,
			array(
				'by'    => ( 'name' === $default_by ? 'name' : 'time' ),
				'order' => ( 'ascending' === $default_order ? 'ascending' : 'descending' ),
			),
			$page,
			$section,
			$title
		);
	}

	/**
	 * Registers the option with WordPress.
	 */
	public function register() {
		register_setting(
			$this->page,
			$this->name . '_order',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_order' ),
			)
		);
		register_setting(
			$this->page,
			$this->name . '_by',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize' ),
			)
		);
	}

	/**
	 * Sanitizes user input.
	 *
	 * This function sanitizes user input for the option (invalid values, values outside bounds etc.). This function should be passed as a `sanitize_callback` when registering the option. this function is used to sanitize the "order" field of the option.
	 *
	 * @see register()
	 *
	 * @param mixed $value The unsanitized user input.
	 *
	 * @return string The sanitized value to be written to the database.
	 */
	public function sanitize_order( $value ) {
		if ( 'ascending' === $value ) {
			return 'ascending';
		}
		if ( 'descending' === $value ) {
			return 'descending';
		}
		return $this->default_value['order'];
	}

	/**
	 * Sanitizes user input.
	 *
	 * This function sanitizes user input for the option (invalid values, values outside bounds etc.). This function should be passed as a `sanitize_callback` when registering the option. this function is used to sanitize the "by" field of the option.
	 *
	 * @see register()
	 *
	 * @param mixed $value The unsanitized user input.
	 *
	 * @return string The sanitized value to be written to the database.
	 */
	public function sanitize( $value ) {
		if ( 'time' === $value ) {
			return 'time';
		}
		if ( 'name' === $value ) {
			return 'name';
		}
		return $this->default_value['by'];
	}

	/**
	 * Adds the option to the WordPress UI.
	 *
	 * This function adds the the option to the WordPress settings on page `$page` in section `$section`. The option is drawn by the `html()` method.
	 *
	 * @see $page
	 * @see $section
	 * @see html()
	 */
	public function add_field() {
		$this->register();
		add_settings_field( $this->name . '_order', $this->title, array( $this, 'html_order' ), $this->page, $this->section );
		add_settings_field( $this->name . '_by', '', array( $this, 'html' ), $this->page, $this->section );
	}

	/**
	 * Renders the UI for updating the option.
	 *
	 * This function renders (by calling `echo()`) the UI for updating the option, including the current value. This function renders the "order" field of the option.
	 */
	public function html_order() {
		echo( '<select name="' . esc_attr( $this->name ) . '_order">' );
		echo( '<option value="ascending"' . ( $this->get_order() === 'ascending' ? ' selected' : '' ) . '>' . esc_html__( 'Ascending', 'skaut-google-drive-gallery' ) . '</option>' );
		echo( '<option value="descending"' . ( $this->get_order() === 'descending' ? ' selected' : '' ) . '>' . esc_html__( 'Descending', 'skaut-google-drive-gallery' ) . '</option>' );
		echo( '</select>' );
	}

	/**
	 * Renders the UI for updating the option.
	 *
	 * This function renders (by calling `echo()`) the UI for updating the option, including the current value. This function renders the "by" field of the option.
	 */
	public function html() {
		echo( '<label for="sgdg-' . esc_attr( $this->name ) . '-by-time"><input type="radio" id="sgdg-' . esc_attr( $this->name ) . '-by-time" name="' . esc_attr( $this->name ) . '_by" value="time"' . ( $this->get_by() === 'time' ? ' checked' : '' ) . '>' . esc_html__( 'By time', 'skaut-google-drive-gallery' ) . '</label><br>' );
		echo( '<label for="sgdg-' . esc_attr( $this->name ) . '-by-name"><input type="radio" id="sgdg-' . esc_attr( $this->name ) . '-by-name" name="' . esc_attr( $this->name ) . '_by" value="name"' . ( $this->get_by() === 'name' ? ' checked' : '' ) . '>' . esc_html__( 'By name', 'skaut-google-drive-gallery' ) . '</label>' );
	}

	/**
	 * Gets the value of the option.
	 *
	 * Returns the value of the option, or a default value if it isn't defined. This function returns the "order" field of the option.
	 *
	 * @see $default_value
	 *
	 * @param string|null $default_value The default value to be returned if the option isn't defined. If it is null, the $default_value property will be used instead. Default null.
	 * @return string The value of the option.
	 */
	public function get_order( $default_value = null ) {
		return get_option( $this->name . '_order', ( isset( $default_value ) ? $default_value : $this->default_value['order'] ) );
	}

	/**
	 * Gets the value of the option.
	 *
	 * Returns the value of the option, or a default value if it isn't defined. This function returns the "by" field of the option.
	 *
	 * @see $default_value
	 *
	 * @param string|null $default_value The default value to be returned if the option isn't defined. If it is null, the $default_value property will be used instead. Default null.
	 * @return string The value of the option.
	 */
	public function get_by( $default_value = null ) {
		return get_option( $this->name . '_by', ( isset( $default_value ) ? $default_value : $this->default_value['by'] ) );
	}

	/**
	 * Gets the value of the option.
	 *
	 * Returns the value of the option, or a default value if it isn't defined. This function return a string describing the option, which can be passed as a query to the Google Drive API.
	 *
	 * @see $default_value
	 *
	 * @param null|array $default_value {
	 *     The default value to be returned if the option isn't defined. If it is null, the $default_value property will be used instead. Default null.
	 *
	 *     @type string $by Accepts `name` or `time`.
	 *     @type string order Accepts `ascending` or `descending`.
	 * }
	 * @return string The value of the option.
	 */
	public function get( $default_value = null ) {
		if ( ! isset( $default_value ) ) {
			$default_value = array(
				'by'    => null,
				'order' => null,
			);
		}
		$by_value    = $this->get_by( $default_value['by'] ) === 'name' ? 'name_natural' : 'modifiedTime';
		$order_value = $this->get_order( $default_value['order'] ) === 'ascending' ? '' : ' desc';
		return $by_value . $order_value;
	}
}
