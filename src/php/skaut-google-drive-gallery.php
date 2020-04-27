<?php
/**
 * Main plugin file
 *
 * Contains plugin init function, activation logic and some helpers for script/style enqueueing.
 *
 * @package skaut-google-drive-gallery
 */

/*
Plugin Name:	Image and video gallery from Google Drive
Plugin URI:		https://github.com/skaut/skaut-google-drive-gallery/
Description:	A WordPress gallery using Google Drive as file storage
Version:		2.7.8
Author:			Junák - český skaut
Author URI:		https://github.com/skaut
License:		MIT
License URI:	https://github.com/skaut/skaut-google-drive-gallery/blob/master/LICENSE
Text Domain:	skaut-google-drive-gallery

MIT License

Copyright (c) Marek Dědič

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

namespace Sgdg;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Die, die, die!' );
}

require_once __DIR__ . '/bundled/vendor-includes.php';

require_once __DIR__ . '/class-options.php';

require_once __DIR__ . '/frontend/class-options-proxy.php';
require_once __DIR__ . '/frontend/google-api-lib.php';
require_once __DIR__ . '/frontend/block.php';
require_once __DIR__ . '/frontend/shortcode.php';
require_once __DIR__ . '/frontend/page.php';
require_once __DIR__ . '/frontend/gallery.php';

require_once __DIR__ . '/admin/google-api-lib.php';
require_once __DIR__ . '/admin/admin-pages.php';
require_once __DIR__ . '/admin/tinymce.php';

/**
 * Initializes the plugin
 */
function init() {
	register_activation_hook( __FILE__, '\\Sgdg\\activate' );
	add_action( 'plugins_loaded', array( '\\Sgdg\\Options', 'init' ) );
	add_action( 'admin_notices', '\\Sgdg\\activation_notice' );
	\Sgdg\Frontend\Shortcode\register();
	\Sgdg\Frontend\Block\register();
	\Sgdg\Frontend\Page\register();
	\Sgdg\Frontend\Gallery\register();
	\Sgdg\Admin\AdminPages\register();
	\Sgdg\Admin\TinyMCE\register();
}

/**
 * Plugin activation function
 *
 * This function is called on plugin activation (i.e. usually once right after the user has installed the plugin). It checks whether the version of PHP and WP is sufficient and deactivates the plugin if they aren't.
 */
function activate() {
	if ( ! isset( $GLOBALS['wp_version'] ) || version_compare( $GLOBALS['wp_version'], '4.9.6', '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( esc_html__( 'Google Drive gallery requires at least WordPress 4.9.6', 'skaut-google-drive-gallery' ) );
	}
	if ( version_compare( phpversion(), '5.6', '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( esc_html__( 'Google Drive gallery requires at least PHP 5.6', 'skaut-google-drive-gallery' ) );
	}
	set_transient( 'sgdg_activation_notice', true, 30 );
}

/**
 * Renders the post-activation notice
 *
 * This function is called after the plugin has been successfully activated and points the user to the docs.
 */
function activation_notice() {
	if ( false !== get_transient( 'sgdg_activation_notice' ) ) {
		echo( '<div class="notice notice-info is-dismissible"><p>' );
		$help_link = 'https://napoveda.skaut.cz/dobryweb/' . substr( get_locale(), 0, 2 ) . '-skaut-google-drive-gallery';
		/* translators: 1: Start of a link to the settings 2: End of the link to the settings 3: Start of a help link 4: End of the help link */
		printf( esc_html__( 'Google Drive gallery needs to be %1$sconfigured%2$s before it can be used. See the %3$sdocumentation%4$s for more information.', 'skaut-google-drive-gallery' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sgdg_basic' ) ) . '">', '</a>', '<a href="' . esc_url( $help_link ) . '" target="_blank">', '</a>' );
		echo( '</p></div>' );
		delete_transient( 'sgdg_activation_notice' );
	}
}

/**
 * Registers a script file
 *
 * Registers a script so that it can later be enqueued by `wp_enqueue_script()`.
 *
 * @param string $handle A unique handle to identify the script with. This handle should be passed to `wp_enqueue_script()`.
 * @param string $src Path to the file, relative to the plugin directory.
 * @param array  $deps A list of dependencies of the script. These can be either system dependencies like jquery, or other registered scripts. Default [].
 */
function register_script( $handle, $src, $deps = array() ) {
	$file = plugin_dir_path( __FILE__ ) . $src;
	wp_register_script( $handle, plugin_dir_url( __FILE__ ) . $src, $deps, file_exists( $file ) ? filemtime( $file ) : false, true );
}

/**
 * Registers a style file
 *
 * Registers a style so that it can later be enqueued by `wp_enqueue_style()`.
 *
 * @param string $handle A unique handle to identify the style with. This handle should be passed to `wp_enqueue_style()`.
 * @param string $src Path to the file, relative to the plugin directory.
 * @param array  $deps A list of dependencies of the style. These can be either system dependencies or other registered styles. Default [].
 */
function register_style( $handle, $src, $deps = array() ) {
	$file = plugin_dir_path( __FILE__ ) . $src;
	wp_register_style( $handle, plugin_dir_url( __FILE__ ) . $src, $deps, file_exists( $file ) ? filemtime( $file ) : false );
}

/**
 * Enqueues a script file
 *
 * Registers and immediately enqueues a script. Note that you should **not** call this function if you've previously registered the script using `register_script()`.
 *
 * @param string $handle A unique handle to identify the script with.
 * @param string $src Path to the file, relative to the plugin directory.
 * @param array  $deps A list of dependencies of the script. These can be either system dependencies like jquery, or other registered scripts. Default [].
 */
function enqueue_script( $handle, $src, $deps = array() ) {
	register_script( $handle, $src, $deps );
	wp_enqueue_script( $handle );
}

/**
 * Enqueues a style file
 *
 * Registers and immediately enqueues a style. Note that you should **not** call this function if you've previously registered the style using `register_style()`.
 *
 * @param string $handle A unique handle to identify the style with.
 * @param string $src Path to the file, relative to the plugin directory.
 * @param array  $deps A list of dependencies of the style. These can be either system dependencies or other registered styles. Default [].
 */
function enqueue_style( $handle, $src, $deps = array() ) {
	register_style( $handle, $src, $deps );
	wp_enqueue_style( $handle );
}

init();
