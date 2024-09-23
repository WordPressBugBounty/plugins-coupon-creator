<?php
/**
 * Plugin Engine Functions
 */

//If Direct Access Kill the Script
if ( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'Pngx__Main' ) ) {
	return;
}

if ( ! function_exists( 'pngx_sanitize_url' ) ) {
	/**
	 * Sanitize URL for display on Front End
	 *
	 * @param $url
	 *
	 * @return mixed
	 */
	function pngx_sanitize_url( $url ) {

		$encode        = array( '&amp;', '&#038;', '&' );
		$replacement   = '%%PNGXAMP%%';
		$sanitized_url = $url;

		//replace ampersand before escaping to prevent breaking url
		$sanitized_url = str_replace( $encode, $replacement, $sanitized_url );
		$sanitized_url = esc_url( $sanitized_url );

		//add back ampersand after escaping
		return str_replace( $replacement, '&', $sanitized_url );
	}
}


if ( ! function_exists( 'pngx_detect_change' ) ) {
	/**
	 * Detect Changes to Meta Settings
	 *
	 * @param $_POST_arr
	 * @param $post_id
	 * @param $meta_key
	 * @param $post_key
	 *
	 * @return bool
	 */
	function pngx_detect_change( $_POST_arr, $post_id, $meta_key, $post_key ) {

		$current_val = get_post_meta( $post_id, $meta_key, true );
		$updated_val = ! empty( $_POST_arr[$post_key] ) ? esc_attr( $_POST_arr[$post_key] ) : '';

		if ( $current_val !== $updated_val ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'pngx_wp_strtotime' ) ) {
	/**
	 * Set Time Based off WordPress Timezone
	 *
	 * https://gist.github.com/anthonyeden/0cf8eb86f7e634b3d5ded4debc59cb84#file-wordpress-strtotime-php
	 *
	 * @param $str
	 *
	 * @return string
	 */
	function pngx_wp_strtotime( $str ) {
		// This function behaves a bit like PHP's StrToTime() function, but taking into account the Wordpress site's timezone
		// CAUTION: It will throw an exception when it receives invalid input - please catch it accordingly
		// From https://mediarealm.com.au/
		$tz_string = get_option( 'timezone_string' );
		$tz_offset = get_option( 'gmt_offset', 0 );
		if ( ! empty( $tz_string ) ) {
			// If site timezone option string exists, use it
			$timezone = $tz_string;
		} elseif ( $tz_offset == 0 ) {
			// get UTC offset, if it isn’t set then return UTC
			$timezone = 'UTC';
		} else {
			$timezone = $tz_offset;
			if ( substr( $tz_offset, 0, 1 ) != "-" && substr( $tz_offset, 0, 1 ) != "+" && substr( $tz_offset, 0, 1 ) != "U" ) {
				$timezone = "+" . $tz_offset;
			}
		}
		$datetime = new DateTime( $str, new DateTimeZone( $timezone ) );

		return $datetime->format( 'U' );
	}
}

if ( ! function_exists( 'pngx_register_plugin' ) ) {
	/**
	 * Checks if this plugin has permission to run, if not it notifies the admin
	 *
	 * Based off Modern Tribe's pngx_register_plugin
	 *
	 * @since 3.0
	 *
	 * @param string $file_path    Full file path to the base plugin file
	 * @param string $main_class   The Main/base class for this plugin
	 * @param string $version      The version
	 * @param array  $classes_req  Any Main class files/pngx plugins required for this to run
	 * @param array  $dependencies an array of dependencies to check
	 *
	 * @return bool Indicates if plugin should continue initialization
	 */
	function pngx_register_plugin( $file_path, $main_class, $version, $classes_req = array(), $dependencies = array() ) {

		$pngx_dependency  = Pngx__Dependency::instance();
		$pngx_dependency->register_plugin( $file_path, $main_class, $version, $classes_req, $dependencies );
	}
}

if ( ! function_exists( 'pngx_check_plugin' ) ) {
	/**
	 * Checks if this plugin has permission to run, if not it notifies the admin
	 *
	 * Based off Modern Tribe's pngx_check_plugin
	 *
	 * @since 3.0
	 *
	 * @param string $main_class   The Main/base class for this plugin
	 *
	 * @return bool Indicates if plugin should continue initialization
	 */
	function pngx_check_plugin( $main_class ) {

		$pngx_dependency    = Pngx__Dependency::instance();
		return $pngx_dependency->check_plugin( $main_class );

	}
}

if ( ! function_exists( 'pngx_notice' ) ) {
	/**
	 * Shortcut for Pngx__Admin__Notices::register(), create a Admin Notice easily
	 *
	 * Based off Modern Tribe's pngx_notice
	 *
	 * @since 3.0
	 *
	 * @param string          $slug             Slug to save the notice
	 * @param callable|string $callback         A callable Method/Fuction to actually display the notice
	 * @param array           $arguments        Arguments to Setup a notice
	 * @param callable|null   $active_callback  An optional callback that should return bool values
	 *                                          to indicate whether the notice should display or not.
	 *
	 * @return stdClass Which notice was registered
	 */
	function pngx_notice( $slug, $callback, $arguments = array(), $active_callback = null ) {
		return Pngx__Admin__Notices::instance()->register( $slug, $callback, $arguments, $active_callback );
	}
}

if ( ! function_exists( 'pngx_asset' ) ) {
	/**
	 * Shortcut for Pngx__Assets::register(), include a single asset
	 *
	 * Based off Modern Tribe's pngx_asset
	 *
	 * @since 3.0
	 *
	 * @param object $origin    The main Object for the plugin you are enqueueing the script/style for
	 * @param string $slug      Slug to save the asset
	 * @param string $file      Which file will be loaded, either CSS or JS
	 * @param array  $deps      Dependencies
	 * @param string $action    A WordPress Action, needs to happen after: `wp_enqueue_scripts`, `admin_enqueue_scripts`, or `login_enqueue_scripts`
	 * @param array  $arguments Look at `Pngx__Assets::register()` for more info
	 *
	 * @return array             Which Assets was registered
	 */
	function pngx_asset( $origin, $slug, $file, $deps = [], $action = null, $arguments = [] ) {
		return pngx( 'pngx.assets' )->register( $origin, $slug, $file, $deps, $action, $arguments );
	}
}

if ( ! function_exists( 'pngx_resource_url' ) ) {
	/**
	 * Returns or echoes a url to a file in the Events Calendar plugin resources directory
	 *
	 * Based off Modern Tribe's pngx_resource_url
	 *
	 * @since 3.0
	 *
	 * @param string $resource the filename of the resource
	 * @param bool   $echo     whether or not to echo the url
	 * @param string $root_dir directory to hunt for resource files (null or the actual path)
	 * @param object $origin   Which plugin we are dealing with
	 *
	 * @return string
	 **/
	function pngx_resource_url( $resource, $echo = false, $root_dir = null, $origin = null ) {
		$extension = pathinfo( $resource, PATHINFO_EXTENSION );
		$resource_path = $root_dir;

		if ( is_null( $resource_path ) ) {
			$resources_path = 'src/resources/';
			switch ( $extension ) {
				case 'css':
					$resource_path = $resources_path .'css/';
					break;
				case 'js':
					$resource_path = $resources_path .'js/';
					break;
				case 'scss':
					$resource_path = $resources_path .'scss/';
					break;
				default:
					$resource_path = $resources_path;
					break;
			}
		}

		$path = $resource_path . $resource;

		if ( is_object( $origin ) ) {
			$plugin_path = trailingslashit( ! empty( $origin->plugin_path ) ? $origin->plugin_path : '' );
		} else {
			$plugin_path = trailingslashit( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) );
		}

		$file = wp_normalize_path( $plugin_path . $path );

		// Turn the Path into a URL
		$url = plugins_url( basename( $file ), $file );

		/**
		 * Filters the resource URL
		 *
		 * @since 3.0
		 *
		 * @param $url
		 * @param $resource
		 */
		$url = apply_filters( 'pngx_resource_url', $url, $resource );

		if ( $echo ) {
			echo $url;
		}

		return $url;
	}
}

if ( ! function_exists( 'pngx_setcookie' ) ) {
	/**
	 * Create a cookie using WordPress contants witha  check for headers sent.
	 *
	 * Based off WooCommerce's wc_setcookie();
	 *
	 * @since 3.2.0
	 *
	 * @param string  $name     Name of the cookie being set.
	 * @param string  $value    Value of the cookie.
	 * @param integer $expire   Expiry of the cookie.
	 * @param bool    $secure   Whether the cookie should be served only over https.
	 * @param bool    $httponly Whether the cookie is only accessible over HTTP, not scripting languages like JavaScript.
	 */
	function pngx_setcookie( $name, $value, $expire = 0, $secure = false, $httponly = false ) {
		if ( ! headers_sent() ) {

			setcookie(
				$name,
				$value,
				$expire,
				COOKIEPATH ? COOKIEPATH : '/',
				COOKIE_DOMAIN,
				$secure,
				apply_filters( 'pngx_cookie_httponly', $httponly, $name, $value, $expire, $secure )
			);

		} elseif ( defined( 'WP_DEBUG' ) ) {

			headers_sent( $file, $line );
			trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
		}
	}
}