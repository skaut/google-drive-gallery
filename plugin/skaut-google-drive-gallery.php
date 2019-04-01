<?php
namespace Sgdg;

/*
Plugin Name:	Google Drive gallery
Plugin URI:     https://github.com/skaut/skaut-google-drive-gallery/
Description:	A WordPress gallery using Google Drive as file storage
Version:	2.5.0
Author:		Junák - český skaut
Author URI:	https://github.com/skaut
License:	MIT
License URI:	https://raw.githubusercontent.com/skaut/skaut-google-drive-gallery/master/LICENSE.md
Text Domain:	skaut-google-drive-gallery

MIT License

Copyright (c) 2018 Marek Dědič

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

defined( 'ABSPATH' ) || die( 'Die, die, die!' );

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

function init() {
	register_activation_hook( __FILE__, '\\Sgdg\\activate' );
	add_action( 'plugins_loaded', [ '\\Sgdg\\Options', 'init' ] );
	add_action( 'admin_notices', '\\Sgdg\\activation_notice' );
	\Sgdg\Frontend\Shortcode\register();
	\Sgdg\Frontend\Block\register();
	\Sgdg\Frontend\Page\register();
	\Sgdg\Frontend\Gallery\register();
	\Sgdg\Admin\AdminPages\register();
	\Sgdg\Admin\TinyMCE\register();
}

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

function activation_notice() {
	if ( get_transient( 'sgdg_activation_notice' ) ) {
		echo( '<div class="notice notice-info is-dismissible"><p>' );
		$help_link = 'https://napoveda.skaut.cz/dobryweb/' . substr( get_locale(), 0, 2 ) . '-skaut-google-drive-gallery';
		// translators: 1: Start of a link to the settings 2: End of the link to the settings 3: Start of a help link 4: End of the help link
		printf( esc_html__( 'Google Drive gallery needs to be %1$sconfigured%2$s before it can be used. See the %3$sdocumentation%4$s for more information.', 'skaut-google-drive-gallery' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sgdg_basic' ) ) . '">', '</a>', '<a href="' . esc_url( $help_link ) . '" target="_blank">', '</a>' );
		echo( '</p></div>' );
		delete_transient( 'sgdg_activation_notice' );
	}
}

function register_script( $handle, $src, $deps = [] ) {
	$file = WP_PLUGIN_DIR . '/skaut-google-drive-gallery' . $src;
	wp_register_script( $handle, plugins_url( '/skaut-google-drive-gallery' . $src ), $deps, file_exists( $file ) ? filemtime( $file ) : false, true );
}

function register_style( $handle, $src, $deps = [] ) {
	$file = WP_PLUGIN_DIR . '/skaut-google-drive-gallery' . $src;
	wp_register_style( $handle, plugins_url( '/skaut-google-drive-gallery' . $src ), $deps, file_exists( $file ) ? filemtime( $file ) : false );
}

function enqueue_script( $handle, $src, $deps = [] ) {
	register_script( $handle, $src, $deps );
	wp_enqueue_script( $handle );
}

function enqueue_style( $handle, $src, $deps = [] ) {
	register_style( $handle, $src, $deps );
	wp_enqueue_style( $handle );
}

init();
