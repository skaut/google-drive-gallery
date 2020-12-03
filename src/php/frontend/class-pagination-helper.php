<?php
/**
 * Contains the Pagination_Helper class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend;

/**
 * Stores pagination info and provides methods to access and use it easily.
 */
class Pagination_Helper implements Pagination_Helper_Interface {
	/**
	 * How many items remain to be skipped (to get to the desired page).
	 *
	 * @var int $to_skip
	 */
	private $to_skip;

	/**
	 * How many items remain to be shown.
	 *
	 * @var int $to_show
	 */
	private $to_show;

	/**
	 * Whether the given gallery has more items to show after finishing the current page. Null means undecided.
	 *
	 * @var bool|null $has_more
	 */
	private $has_more;

	/**
	 * Pagination_Helper class constructor
	 */
	public function __construct() {
		$this->to_skip  = 0;
		$this->to_show  = 0;
		$this->has_more = null;
	}

	/**
	 * Pagination_Helper class populator
	 *
	 * Call as `new Pagination_Helper()->withOptions()`
	 *
	 * @param \Sgdg\Frontend\Options_Proxy $options Gallery options.
	 * @param bool                         $show_previous Whether to also show all previous pages.
	 *
	 * @return $this The instance.
	 */
	public function withOptions( $options, $show_previous ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page          = isset( $_GET['page'] ) ? intval( max( 1, intval( $_GET['page'] ) ) ) : 1;
		$page_size     = intval( $options->get( 'page_size' ) );
		$this->to_skip = $show_previous ? 0 : $page_size * ( $page - 1 );
		$this->to_show = $show_previous ? $page_size * $page : $page_size;
		return $this;
	}

	/**
	 * Pagination_Helper class populator
	 *
	 * Call as `new Pagination_Helper()->withOptions()`
	 *
	 * @param int $to_skip The number of items to skip before showing any.
	 * @param int $to_show The number of items to show.
	 *
	 * @return $this The instance.
	 */
	public function withValues( $to_skip, $to_show ) {
		$this->to_skip = $to_skip;
		$this->to_show = $to_show;
		return $this;
	}

	/**
	 * Returns how many items the next list API call should fetch.
	 *
	 * @param int $maximum The maximum allowed size for this type of request.
	 *
	 * @return int The number of items.
	 */
	public function next_list_size( $maximum ) {
		return min( $maximum, $this->to_skip + $this->to_show + ( is_null( $this->has_more ) ? 1 : 0 ) );
	}

	/**
	 * Iterates through a list, skipping items where appropriate.
	 *
	 * @param \ArrayAccess|\Countable $list The list to go through.
	 * @param callable                $iterator The function to call on each unskipped item.
	 */
	public function iterate( $list, $iterator ) {
		$list_size = count( $list );
		if ( $list_size <= $this->to_skip ) {
			$this->to_skip -= $list_size;
			return;
		}
		$start         = $this->to_skip;
		$this->to_skip = 0;
		if ( $list_size - $start > $this->to_show ) {
			$this->has_more = true;
		}
		$stop           = intval( min( $list_size, $start + $this->to_show ) );
		$this->to_show -= $stop - $start;
		for ( $i = $start; $i < $stop; ++$i ) {
			$iterator( $list[ $i ] );
		}
	}

	/**
	 * Whether the algorithm should continue to fetch new data.
	 *
	 * @return bool True if the data is not complete.
	 */
	public function should_continue() {
		return 0 < $this->to_show || is_null( $this->has_more );
	}

	/**
	 * Returns the final value of `$has_more`.
	 *
	 * @return bool True if there are more items.
	 */
	public function has_more() {
		if ( is_null( $this->has_more ) ) {
			return false;
		}
		return $this->has_more;
	}
}
