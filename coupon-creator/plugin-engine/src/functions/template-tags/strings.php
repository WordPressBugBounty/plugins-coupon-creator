<?php
/**
 * Plugin Engine String Functions
 */

//If Direct Access Kill the Script
if ( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) {
	die( 'Access denied.' );
}

/**
 * Polyfills for PHP8 Functions handling strings.
 *
 * source: Laravel Framework
 * https://github.com/laravel/framework/blob/8.x/src/Illuminate/Support/Str.php
 */
if ( ! function_exists( 'str_starts_with' ) ) {
	function str_starts_with( $haystack, $needle ) {
		return (string) $needle !== '' && strncmp( $haystack, $needle, strlen( $needle ) ) === 0;
	}
}
if ( ! function_exists( 'str_ends_with' ) ) {
	function str_ends_with( $haystack, $needle ) {
		return $needle !== '' && substr( $haystack, - strlen( $needle ) ) === (string) $needle;
	}
}
if ( ! function_exists( 'str_contains' ) ) {
	function str_contains( $haystack, $needle ) {
		return $needle !== '' && mb_strpos( $haystack, $needle ) !== false;
	}
}