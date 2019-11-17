<?php
/**
 * Contains the Option iterface
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

/**
 * An interface for all plugin options
 *
 * This class serves as an interface for all options of the plugin - each option is configurable in some section of some page of the settings, has a name, default value, getter etc.
 */
abstract class Option {
	/**
	 * The name of the option to be used as the key to reference it.
	 *
	 * @var string $name
	 */
	protected $name;
	/**
	 * The default value of the option to be returned if the option is not set.
	 *
	 * @var mixed $default_value
	 */
	protected $default_value;
	/**
	 * The page in which the option will be accessible to the user.
	 *
	 * @var string $page
	 */
	protected $page;
	/**
	 * The section (within the selected page) in which the option will be accessible to the user.
	 *
	 * @var string $section
	 */
	protected $section;
	/**
	 * A human-readable name of the option to be displayed to the user.
	 *
	 * @var string $title
	 */
	protected $title;

	/**
	 * Option class constructor.
	 *
	 * This constructor is intended to be used by sub-classes as the Option class is abstract.
	 *
	 * @param string $name The name of the option to be used as the key to reference it. The prefix `sgdg_` will be added automatically.
	 * @param mixed  $default_value The default value of the option to be returned if the option is not set.
	 * @param string $page The page in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $section The section (within the selected page) in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $title A human-readable name of the option to be displayed to the user.
	 */
	public function __construct( $name, $default_value, $page, $section, $title ) {
		$this->name          = 'sgdg_' . $name;
		$this->default_value = $default_value;
		$this->page          = 'sgdg_' . $page;
		$this->section       = 'sgdg_' . $section;
		$this->title         = $title;
	}

	/**
	 * Registers the option with WordPress.
	 */
	abstract public function register();

	/**
	 * Sanitizes user input.
	 *
	 * This function sanitizes user input for the option (invalid values, values outside bounds etc.). This function should be passed as a `sanitize_callback` when registering the option.
	 *
	 * @see register()
	 *
	 * @param mixed $value The unsanitized user input.
	 *
	 * @return mixed The sanitized value to be written to the database.
	 */
	public function sanitize( $value ) {
		return $value;
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
		add_settings_field( $this->name, $this->title, array( $this, 'html' ), $this->page, $this->section );
	}

	/**
	 * Renders the UI for updating the option.
	 *
	 * This function renders (by calling `echo()`) the UI for updating the option, including the current value.
	 */
	abstract public function html();

	/**
	 * Gets the value of the option.
	 *
	 * Returns the value of the option, or a default value if it isn't defined.
	 *
	 * @see $default_value
	 *
	 * @param mixed $default_value The default value to be returned if the option isn't defined. If it is null, the $default_value property will be used instead. Default null.
	 *
	 * @return mixed The value of the option.
	 */
	public function get( $default_value = null ) {
		return get_option( $this->name, ( isset( $default_value ) ? $default_value : $this->default_value ) );
	}

	/**
	 * Gets the title of the option.
	 *
	 * @see $title
	 *
	 * @return string The title in English.
	 */
	public function get_title() {
		return $this->title;
	}
}
