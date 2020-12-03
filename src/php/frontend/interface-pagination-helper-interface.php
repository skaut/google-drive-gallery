<?php
/**
 * Contains the Pagination_Helper_Interface interface.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

/**
 * Stores pagination info and provides methods to access and use it easily.
 */
interface Pagination_Helper_Interface {
	/**
	 * Returns how many items the next list API call should fetch.
	 *
	 * @param int $maximum The maximum allowed size for this type of request.
	 *
	 * @return int The number of items.
	 */
	public function next_list_size( $maximum );

	/**
	 * Iterates through a list, skipping items where appropriate.
	 *
	 * @param \ArrayAccess|\Countable $list The list to go through.
	 * @param callable                $iterator The function to call on each unskipped item.
	 */
	public function iterate( $list, $iterator );

	/**
	 * Whether the algorithm should continue to fetch new data.
	 *
	 * @return bool True if the data is not complete.
	 */
	public function should_continue();
}
