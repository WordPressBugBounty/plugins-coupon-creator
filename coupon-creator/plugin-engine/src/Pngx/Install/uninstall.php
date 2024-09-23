<?php
/**
 * Plugin Engine Uninstall
 *
 * Uninstalling Plugin Engine Database, Cron, Etc...
 *
 * @package Pngx\Uninstaller
 *
 * @since 4.0.0
 */

if ( ! defined( 'PNGX_UNINSTALL_PLUGIN' ) ) {
	return;
}

global $wpdb, $wp_version;

wp_clear_scheduled_hook( 'pngx_cleanup_sessions' );

/*
 * Remove only if WC_REMOVE_ALL_DATA is set in the wp-config.php.
 */
if ( defined( 'PNGX_REMOVE_ALL_DATA' ) && true === PNGX_REMOVE_ALL_DATA ) {

	// Delete options.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'pngx\_%';" );

	// Delete usermeta.
	$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'pngx\_%';" );

	// Clear any cached data that has been removed.
	wp_cache_flush();
}
