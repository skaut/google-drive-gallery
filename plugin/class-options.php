<?php
namespace Sgdg;

require_once __DIR__ . '/frontend/class-integeroption.php';
require_once __DIR__ . '/frontend/class-boundedintegeroption.php';
require_once __DIR__ . '/frontend/class-booleanoption.php';
require_once __DIR__ . '/frontend/class-stringoption.php';
require_once __DIR__ . '/frontend/class-stringcodeoption.php';
require_once __DIR__ . '/frontend/class-arrayoption.php';
require_once __DIR__ . '/frontend/class-rootpathoption.php';
require_once __DIR__ . '/frontend/class-orderingoption.php';
require_once __DIR__ . '/admin/class-readonlystringoption.php';

class Options {
	public static $authorized_domain;
	public static $authorized_origin;
	public static $redirect_uri;
	public static $client_id;
	public static $client_secret;

	public static $root_path;

	public static $grid_height;
	public static $grid_spacing;
	public static $dir_title_size;
	public static $dir_counts;
	public static $page_size;
	public static $page_autoload;
	public static $image_ordering;
	public static $dir_ordering;
	public static $dir_prefix;

	public static $preview_size;
	public static $preview_speed;
	public static $preview_arrows;
	public static $preview_close_button;
	public static $preview_loop;
	public static $preview_activity_indicator;

	public static function init() {
		$url                     = wp_parse_url( get_site_url() );
		self::$authorized_domain = new \Sgdg\Admin\ReadonlyStringOption( 'authorized_domain', $url['host'], 'basic', 'auth', esc_html__( 'Authorised domain', 'skaut-google-drive-gallery' ) );
		self::$authorized_origin = new \Sgdg\Admin\ReadonlyStringOption( 'origin', $url['scheme'] . '://' . $url['host'], 'basic', 'auth', esc_html__( 'Authorised JavaScript origin', 'skaut-google-drive-gallery' ) );
		self::$redirect_uri      = new \Sgdg\Admin\ReadonlyStringOption( 'redirect_uri', esc_url_raw( admin_url( 'admin.php?page=sgdg_basic&action=oauth_redirect' ) ), 'basic', 'auth', esc_html__( 'Authorised redirect URI', 'skaut-google-drive-gallery' ) );
		self::$client_id         = new \Sgdg\Frontend\StringCodeOption( 'client_id', '', 'basic', 'auth', esc_html__( 'Client ID', 'skaut-google-drive-gallery' ) );
		self::$client_secret     = new \Sgdg\Frontend\StringCodeOption( 'client_secret', '', 'basic', 'auth', esc_html__( 'Client secret', 'skaut-google-drive-gallery' ) );

		self::$root_path = new \Sgdg\Frontend\RootPathOption( 'root_path', [ 'root' ], 'basic', 'root_selection', '' );

		self::$grid_height    = new \Sgdg\Frontend\BoundedIntegerOption( 'grid_height', 250, 1, 'advanced', 'grid', esc_html__( 'Row height', 'skaut-google-drive-gallery' ) );
		self::$grid_spacing   = new \Sgdg\Frontend\IntegerOption( 'grid_spacing', 10, 'advanced', 'grid', esc_html__( 'Item spacing', 'skaut-google-drive-gallery' ) );
		self::$dir_title_size = new \Sgdg\Frontend\StringOption( 'dir_title_size', '1.2em', 'advanced', 'grid', esc_html__( 'Directory title size', 'skaut-google-drive-gallery' ) );
		self::$dir_counts     = new \Sgdg\Frontend\BooleanOption( 'dir_counts', true, 'advanced', 'grid', esc_html__( 'Directory item counts', 'skaut-google-drive-gallery' ) );
		self::$page_size      = new \Sgdg\Frontend\BoundedIntegerOption( 'page_size', 50, 1, 'advanced', 'grid', esc_html__( 'Items per page', 'skaut-google-drive-gallery' ) );
		self::$page_autoload  = new \Sgdg\Frontend\BooleanOption( 'page_autoload', true, 'advanced', 'grid', esc_html__( 'Autoload new images', 'skaut-google-drive-gallery' ) );
		self::$image_ordering = new \Sgdg\Frontend\OrderingOption( 'image_ordering', 'date', 'ascending', 'advanced', 'grid', esc_html__( 'Image ordering', 'skaut-google-drive-gallery' ) );
		self::$dir_ordering   = new \Sgdg\Frontend\OrderingOption( 'dir_ordering', 'date', 'descending', 'advanced', 'grid', esc_html__( 'Directory ordering', 'skaut-google-drive-gallery' ) );
		self::$dir_prefix     = new \Sgdg\Frontend\StringOption( 'dir_prefix', '', 'advanced', 'grid', esc_html__( 'In folder names, hide everything before the first occurence of', 'skaut-google-drive-gallery' ) );

		self::$preview_size               = new \Sgdg\Frontend\BoundedIntegerOption( 'preview_size', 1920, 1, 'advanced', 'lightbox', esc_html__( 'Image size', 'skaut-google-drive-gallery' ) );
		self::$preview_speed              = new \Sgdg\Frontend\BoundedIntegerOption( 'preview_speed', 250, 0, 'advanced', 'lightbox', esc_html__( 'Animation speed (ms)', 'skaut-google-drive-gallery' ) );
		self::$preview_arrows             = new \Sgdg\Frontend\BooleanOption( 'preview_arrows', true, 'advanced', 'lightbox', esc_html__( 'Navigation arrows', 'skaut-google-drive-gallery' ) );
		self::$preview_close_button       = new \Sgdg\Frontend\BooleanOption( 'preview_closebutton', true, 'advanced', 'lightbox', esc_html__( 'Close button', 'skaut-google-drive-gallery' ) );
		self::$preview_loop               = new \Sgdg\Frontend\BooleanOption( 'preview_loop', false, 'advanced', 'lightbox', esc_html__( 'Loop images', 'skaut-google-drive-gallery' ) );
		self::$preview_activity_indicator = new \Sgdg\Frontend\BooleanOption( 'preview_activity', true, 'advanced', 'lightbox', esc_html__( 'Activity indicator', 'skaut-google-drive-gallery' ) );
	}
}
