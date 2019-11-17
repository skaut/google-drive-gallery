<?php
/**
 * Contains the Options class
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg;

require_once __DIR__ . '/frontend/class-boolean-option.php';
require_once __DIR__ . '/frontend/class-integer-option.php';
require_once __DIR__ . '/frontend/class-bounded-integer-option.php';
require_once __DIR__ . '/frontend/class-string-option.php';
require_once __DIR__ . '/frontend/class-code-string-option.php';
require_once __DIR__ . '/frontend/class-array-option.php';
require_once __DIR__ . '/frontend/class-ordering-option.php';
require_once __DIR__ . '/frontend/class-root-path-option.php';
require_once __DIR__ . '/admin/class-readonly-string-option.php';

/**
 * A container for all the configuration of the plugin.
 *
 * Contains all the options for the plugin as static properties.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Options {
	/**
	 * Shows the authorized domain which the user needs for registering the Google app.
	 *
	 * @var \Sgdg\Admin\Readonly_String_Option $authorized_domain
	 */
	public static $authorized_domain;
	/**
	 * Shows the authorized JavaScript origin which the user needs for registering the Google app.
	 *
	 * @var \Sgdg\Admin\Readonly_String_Option $authorized_origin
	 */
	public static $authorized_origin;
	/**
	 * Shows the authorized redirect URI which the user needs for registering the Google app.
	 *
	 * @var \Sgdg\Admin\Readonly_String_Option $redirect_uri
	 */
	public static $redirect_uri;
	/**
	 * The client ID of the Google app.
	 *
	 * @var \Sgdg\Frontend\Code_String_Option $client_id
	 */
	public static $client_id;
	/**
	 * The client secret of the Google app.
	 *
	 * @var \Sgdg\Frontend\Code_String_Option $client_secret
	 */
	public static $client_secret;

	/**
	 * The root path of the plugin. This is the only directory the plugin should ever touch.
	 *
	 * @var \Sgdg\Frontend\Root_Path_Option $root_path
	 */
	public static $root_path;

	/**
	 * The height of a row in the image grid.
	 *
	 * @var \Sgdg\Frontend\Bounded_Integer_Option $grid_height
	 */
	public static $grid_height;
	/**
	 * Item spacing in the image grid.
	 *
	 * @var \Sgdg\Frontend\Integer_Option $grid_spacing
	 */
	public static $grid_spacing;
	/**
	 * Directory title size, including CSS units.
	 *
	 * @var \Sgdg\Frontend\String_Option $dir_title_size
	 */
	public static $dir_title_size;
	/**
	 * Whether to show directory item counts. Accepts `true`, `false`.
	 *
	 * @var \Sgdg\Frontend\Boolean_Option $dir_counts
	 */
	public static $dir_counts;
	/**
	 * Number of items per 1 page.
	 *
	 * @var \Sgdg\Frontend\Bounded_Integer_Option $page_size
	 */
	public static $page_size;
	/**
	 * Whether to autoload new images. Accepts `true`, `false`.
	 *
	 * @var \Sgdg\Frontend\Bounded_Integer_Option $page_autoload
	 */
	public static $page_autoload;
	/**
	 * How to order images and videos in the gallery.
	 *
	 * @var \Sgdg\Frontend\Ordering_Option $image_ordering
	 */
	public static $image_ordering;
	/**
	 * How to order directories in the gallery.
	 *
	 * @var \Sgdg\Frontend\Ordering_Option $dir_ordering
	 */
	public static $dir_ordering;
	/**
	 * A prefix separator to cut a prefix from the start of all directory names.
	 *
	 * @var \Sgdg\Frontend\String_Option $dir_prefix
	 */
	public static $dir_prefix;

	/**
	 * Maximum size of an image in the lightbox.
	 *
	 * @var \Sgdg\Frontend\Bounded_Integer_Option $preview_size
	 */
	public static $preview_size;
	/**
	 * Lightbox animation speed.
	 *
	 * @var \Sgdg\Frontend\Bounded_Integer_Option $preview_speed
	 */
	public static $preview_speed;
	/**
	 * Whether to show lightbox navigation arrows.
	 *
	 * @var \Sgdg\Frontend\Boolean_Option $preview_arrows
	 */
	public static $preview_arrows;
	/**
	 * Whether to show lightbox close button.
	 *
	 * @var \Sgdg\Frontend\Boolean_Option $preview_close_button
	 */
	public static $preview_close_button;
	/**
	 * Whether to loop the images in the lightbox.
	 *
	 * @var \Sgdg\Frontend\Boolean_Option $preview_loop
	 */
	public static $preview_loop;
	/**
	 * Whether to show an activity indicator while the lightbox is loading.
	 *
	 * @var \Sgdg\Frontend\Boolean_Option $preview_activity_indicator
	 */
	public static $preview_activity_indicator;
	/**
	 * Whether to show image captions in the lightbox.
	 *
	 * @var \Sgdg\Frontend\Boolean_Option $preview_captions
	 */
	public static $preview_captions;

	/**
	 * Options class initializer.
	 *
	 * Initializes all the properties of this class. Serves as a sort-of static constructor.
	 */
	public static function init() {
		$url                     = wp_parse_url( get_site_url() );
		self::$authorized_domain = new \Sgdg\Admin\Readonly_String_Option( 'authorized_domain', $url['host'], 'basic', 'auth', esc_html__( 'Authorised domain', 'skaut-google-drive-gallery' ) );
		self::$authorized_origin = new \Sgdg\Admin\Readonly_String_Option( 'origin', $url['scheme'] . '://' . $url['host'], 'basic', 'auth', esc_html__( 'Authorised JavaScript origin', 'skaut-google-drive-gallery' ) );
		self::$redirect_uri      = new \Sgdg\Admin\Readonly_String_Option( 'redirect_uri', esc_url_raw( admin_url( 'admin.php?page=sgdg_basic&action=oauth_redirect' ) ), 'basic', 'auth', esc_html__( 'Authorised redirect URI', 'skaut-google-drive-gallery' ) );
		self::$client_id         = new \Sgdg\Frontend\Code_String_Option( 'client_id', '', 'basic', 'auth', esc_html__( 'Client ID', 'skaut-google-drive-gallery' ) );
		self::$client_secret     = new \Sgdg\Frontend\Code_String_Option( 'client_secret', '', 'basic', 'auth', esc_html__( 'Client secret', 'skaut-google-drive-gallery' ) );

		self::$root_path = new \Sgdg\Frontend\Root_Path_Option( 'root_path', array( 'root' ), 'basic', 'root_selection', '' );

		self::$grid_height    = new \Sgdg\Frontend\Bounded_Integer_Option( 'grid_height', 250, 1, 'advanced', 'grid', esc_html__( 'Row height', 'skaut-google-drive-gallery' ) );
		self::$grid_spacing   = new \Sgdg\Frontend\Integer_Option( 'grid_spacing', 10, 'advanced', 'grid', esc_html__( 'Item spacing', 'skaut-google-drive-gallery' ) );
		self::$dir_title_size = new \Sgdg\Frontend\String_Option( 'dir_title_size', '1.2em', 'advanced', 'grid', esc_html__( 'Directory title size', 'skaut-google-drive-gallery' ) );
		self::$dir_counts     = new \Sgdg\Frontend\Boolean_Option( 'dir_counts', true, 'advanced', 'grid', esc_html__( 'Directory item counts', 'skaut-google-drive-gallery' ) );
		self::$page_size      = new \Sgdg\Frontend\Bounded_Integer_Option( 'page_size', 50, 1, 'advanced', 'grid', esc_html__( 'Items per page', 'skaut-google-drive-gallery' ) );
		self::$page_autoload  = new \Sgdg\Frontend\Boolean_Option( 'page_autoload', true, 'advanced', 'grid', esc_html__( 'Autoload new images', 'skaut-google-drive-gallery' ) );
		self::$image_ordering = new \Sgdg\Frontend\Ordering_Option( 'image_ordering', 'time', 'ascending', 'advanced', 'grid', esc_html__( 'Image and video ordering', 'skaut-google-drive-gallery' ) );
		self::$dir_ordering   = new \Sgdg\Frontend\Ordering_Option( 'dir_ordering', 'time', 'descending', 'advanced', 'grid', esc_html__( 'Directory ordering', 'skaut-google-drive-gallery' ) );
		self::$dir_prefix     = new \Sgdg\Frontend\String_Option( 'dir_prefix', '', 'advanced', 'grid', esc_html__( 'In folder names, hide everything before the first occurence of', 'skaut-google-drive-gallery' ) );

		self::$preview_size               = new \Sgdg\Frontend\Bounded_Integer_Option( 'preview_size', 1920, 1, 'advanced', 'lightbox', esc_html__( 'Image size', 'skaut-google-drive-gallery' ) );
		self::$preview_speed              = new \Sgdg\Frontend\Bounded_Integer_Option( 'preview_speed', 250, 0, 'advanced', 'lightbox', esc_html__( 'Animation speed (ms)', 'skaut-google-drive-gallery' ) );
		self::$preview_arrows             = new \Sgdg\Frontend\Boolean_Option( 'preview_arrows', true, 'advanced', 'lightbox', esc_html__( 'Navigation arrows', 'skaut-google-drive-gallery' ) );
		self::$preview_close_button       = new \Sgdg\Frontend\Boolean_Option( 'preview_closebutton', true, 'advanced', 'lightbox', esc_html__( 'Close button', 'skaut-google-drive-gallery' ) );
		self::$preview_loop               = new \Sgdg\Frontend\Boolean_Option( 'preview_loop', false, 'advanced', 'lightbox', esc_html__( 'Loop images', 'skaut-google-drive-gallery' ) );
		self::$preview_activity_indicator = new \Sgdg\Frontend\Boolean_Option( 'preview_activity', true, 'advanced', 'lightbox', esc_html__( 'Activity indicator', 'skaut-google-drive-gallery' ) );
		self::$preview_captions           = new \Sgdg\Frontend\Boolean_Option( 'preview_captions', true, 'advanced', 'lightbox', esc_html__( 'Show captions', 'skaut-google-drive-gallery' ) );
	}
}
