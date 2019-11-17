<?php
/**
 * Contains the Readonly_String_Option class
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Admin;

if ( ! is_admin() ) {
	return;
}

/**
 * An option representing a string value which is never changed, just read. The value isn't actually stored in the database, it just follows the UI of other options.
 *
 * @see Option
 */
class Readonly_String_Option {
	/**
	 * The name of the option to be used as the key to reference it.
	 *
	 * @var string $name
	 */
	private $name;
	/**
	 * The value of the option.
	 *
	 * @var string $value
	 */
	private $value;
	/**
	 * The page in which the option will be accessible to the user.
	 *
	 * @var string $page
	 */
	private $page;
	/**
	 * The section (within the selected page) in which the option will be accessible to the user.
	 *
	 * @var string $section
	 */
	private $section;
	/**
	 * A human-readable name of the option to be displayed to the user.
	 *
	 * @var string $title
	 */
	private $title;

	/**
	 * Readonly_String_Option class constructor.
	 *
	 * @param string $name The name of the option to be used as the key to reference it. The prefix `sgdg_` will be added automatically.
	 * @param string $value The value of the option.
	 * @param string $page The page in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $section The section (within the selected page) in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $title A human-readable name of the option to be displayed to the user.
	 */
	public function __construct( $name, $value, $page, $section, $title ) {
		$this->name    = 'sgdg_' . $name;
		$this->value   = $value;
		$this->page    = 'sgdg_' . $page;
		$this->section = 'sgdg_' . $section;
		$this->title   = $title;
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
		add_settings_field( $this->name, $this->title, array( $this, 'html' ), $this->page, $this->section );
	}

	/**
	 * Renders the UI for updating the option.
	 *
	 * This function renders (by calling `echo()`) the UI for the option.
	 */
	public function html() {
		echo( '<input type="text" value="' . esc_attr( $this->value ) . '" readonly class="regular-text code">' );
	}
}
