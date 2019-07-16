<?php
/**
 * Plugin uninstallation file.
 *
 * Deletes all the plugin options so that the database is clean after uninstall.
 *
 * @package skaut-google-drive-gallery
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die( 'Die, die, die!' );
}

delete_option( 'sgdg_client_id' );
delete_option( 'sgdg_client_secret' );
delete_option( 'sgdg_access_token' );

delete_option( 'sgdg_root_path' );

delete_option( 'sgdg_grid_height' );
delete_option( 'sgdg_grid_spacing' );
delete_option( 'sgdg_dir_title_size' );
delete_option( 'sgdg_dir_counts' );
delete_option( 'sgdg_image_ordering_order' );
delete_option( 'sgdg_image_ordering_by' );
delete_option( 'sgdg_dir_ordering_order' );
delete_option( 'sgdg_dir_ordering_by' );

delete_option( 'sgdg_preview_size' );
delete_option( 'sgdg_preview_speed' );
delete_option( 'sgdg_preview_arrows' );
delete_option( 'sgdg_preview_closebutton' );
delete_option( 'sgdg_preview_loop' );
delete_option( 'sgdg_preview_activity' );
delete_option( 'sgdg_preview_captions' );

// Deprecated.
delete_option( 'sgdg_image_ordering' );
delete_option( 'sgdg_thumbnail_size' );
delete_option( 'sgdg_thumbnail_size_value' );
delete_option( 'sgdg_thumbnail_size_unit' );
delete_option( 'sgdg_thumbnail_spacing' );
delete_option( 'sgdg_date_ordering_order' );
delete_option( 'sgdg_date_ordering_by' );
delete_option( 'sgdg_grid_mode' );
delete_option( 'sgdg_grid_width' );
delete_option( 'sgdg_grid_columns' );
delete_option( 'sgdg_grid_min_width' );
