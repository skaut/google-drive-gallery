<?php
/**
 * Contains the Infinite_Pagination_Helper class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

/**
 * Enables infinite pagination (load all available pages).
 */
class Infinite_Pagination_Helper implements Pagination_Helper_Interface {
	/**
	 * Returns `$maximum`.
	 *
	 * @param int $maximum The maximum allowed size for this type of request.
	 *
	 * @return int The number of items.
	 */
	public function next_list_size( $maximum ) {
		return $maximum;
	}

	/**
	 * Iterates through a list.
	 *
	 * @param \ArrayAccess|\Countable $list The list to go through.
	 * @param callable                $iterator The function to call on each item.
	 */
	public function iterate( $list, $iterator ) {
		$list_size = count( $list );
		for ( $i = 0; $i < $list_size; ++$i ) {
			$iterator( $list[ $i ] );
		}
	}

	/**
	 * Returns `true`.
	 *
	 * @return bool `true`.
	 */
	public function should_continue() {
		return true;
	}
}
