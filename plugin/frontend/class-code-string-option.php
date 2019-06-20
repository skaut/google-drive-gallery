<?php
/**
 * Contains the Code_String_Option class
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

/**
 * An option representing a code which the user has to fill in, with the option for the code to be locked to be read-only.
 *
 * @see String_Option
 */
class Code_String_Option extends String_Option {
	/**
	 * Whether the option should be rendered as read-only.
	 *
	 * @var bool $readonly
	 */
	private $readonly;

	/**
	 * Code_String_Option class constructor.
	 *
	 * @param string $name The name of the option to be used as the key to reference it. The prefix `sgdg_` will be added automatically.
	 * @param string $default_value The default value of the option to be returned if the option is not set.
	 * @param string $page The page in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $section The section (within the selected page) in which the option will be accessible to the user. The prefix `sgdg_` will be added automatically.
	 * @param string $title A human-readable name of the option to be displayed to the user.
	 */
	public function __construct( $name, $default_value, $page, $section, $title ) {
		parent::__construct( $name, $default_value, $page, $section, $title );
		$this->readonly = false;
	}

	/**
	 * Adds the option to the WordPress UI.
	 *
	 * This function adds the the option to the WordPress settings on page `$page` in section `$section`. The option is drawn by the `html()` method. Additionaly, the option can be set to be rendered as read-only.
	 *
	 * @see $page
	 * @see $section
	 * @see $readonly
	 * @see html()
	 *
	 * @param bool $readonly Sets whether the option should be read-only.
	 */
	public function add_field( $readonly = false ) {
		$this->readonly = $readonly;
		parent::add_field();
	}

	/**
	 * Renders the UI for updating the option.
	 *
	 * This function renders (by calling `echo()`) the UI for updating the option, including the current value. The option will be rendered as read-only, depending on the value of the `$readonly` property.
	 *
	 * @see $readonly
	 */
	public function html() {
		echo( '<input type="text" name="' . esc_attr( $this->name ) . '" value="' . esc_attr( get_option( $this->name, $this->default_value ) ) . '" ' . ( $this->readonly ? 'readonly ' : '' ) . 'class="regular-text code">' );
	}
}
