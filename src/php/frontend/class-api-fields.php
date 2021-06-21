<?php
/**
 * Contains the API_Fields class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

/**
 * API file fields
 */
class API_Fields {
	/**
	 * The fields
	 *
	 * @var array $fields
	 */
	private $fields;

	/**
	 * Returns the fields with added provisions for IDs of shortcuts.
	 *
	 * @return array The enriched fields.
	 */
	private function fix_shortcuts() {
		$fields = $this->fields;
		if ( in_array( 'id', $fields, true ) ) {
			if ( ! in_array( 'mimeType', $fields, true ) ) {
				$fields[] = 'mimeType';
			}
			if ( array_key_exists( 'shortcutDetails', $fields ) ) {
				if ( ! in_array( 'targetId', $fields['shortcutDetails'], true ) ) {
					$fields['shortcutDetails'][] = 'mimeType';
				}
			} else {
				$fields['shortcutDetails'] = array( 'targetId' );
			}
		}
		return $fields;
	}

	/**
	 * API_Fields class constructor.
	 *
	 * @param array $fields The fields as an array. Use associative keys to express nested parameters. Example: `array( 'id', 'name', 'imageMediaMetadata' => array( 'width', 'height' ) )`.
	 */
	public function __construct( $fields ) {
		$this->fields = $fields;
	}

	/**
	 * Check that the fields match the prototype.
	 *
	 * @param array $prototype The prototype in the same format as the fields.
	 *
	 * @return bool True if the fields match.
	 */
	public function check( $prototype ) {
		foreach ( $this->fields as $key => $value ) {
			if ( is_string( $key ) ) {
				if ( ! array_key_exists( $key, $prototype ) ) {
					return false;
				}
				if ( ! empty( array_diff( $value, $prototype[ $key ] ) ) ) {
					return false;
				}
			} else {
				if ( ! in_array( $value, $prototype, true ) ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Returns the fields in the correct format for Google API
	 *
	 * @return string The formatted fields.
	 */
	public function format() {
		$fields = $this->fix_shortcuts();

		$ret = '';
		foreach ( $fields as $key => $value ) {
			if ( is_string( $key ) ) {
				$ret .= ', ' . $key . '(' . implode( ', ', $value ) . ')';
			} else {
				$ret .= ', ' . $value;
			}
		}
		$ret = substr( $ret, 2 );
		return false === $ret ? '' : $ret;
	}

	/**
	 * Parses a Google API response according to the fields.
	 *
	 * @param \Sgdg\Vendor\Google\Service\Drive\DriveFile $response The API response.
	 *
	 * @return array The parsed response
	 */
	public function parse_response( $response ) {
		$ret = array();
		foreach ( $this->fields as $key => $value ) {
			if ( is_string( $key ) ) {
				foreach ( $value as $subvalue ) {
					$ret[ $key ][ $subvalue ] = $response->$key->$subvalue;
				}
			} else {
				if ( 'id' === $value ) {
					$ret['id'] = $response->getMimeType() === 'application/vnd.google-apps.shortcut' ? $response->getShortcutDetails()->getTargetId() : $response->getId();
				} else {
					$ret[ $value ] = $response->$value;
				}
			}
		}
		return $ret;
	}
}
