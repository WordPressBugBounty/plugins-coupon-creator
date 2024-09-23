<?php
// Don't load directly
defined( 'WPINC' ) or die;


/**
 * Class Pngx__Plugins
 *
 * Basedd of Modern Tribe's Pngx__Plugin Class
 */
class Pngx__Plugins {

	/**
	 * A list of Plugin Engine plugin's details in this format:
	 *
	 * array(
	 *  'short_name'   => Common name for the plugin, used in places such as WP Admin messages
	 *  'class'        => Main plugin class
	 *  'thickbox_url' => Download or purchase URL for plugin from within /wp-admin/ thickbox
	 *  )
	 */
	private $pngx_plugins = array(
		array(
			'short_name'   => 'Coupon Creator',
			'class'        => 'Cctor__Coupon__Main',
			'thickbox_url' => 'plugin-install.php?tab=plugin-information&plugin=coupon-creator&TB_iframe=true',
		),
		array(
			'short_name'   => 'Coupon Creator Pro',
			'class'        => 'Cctor__Coupon__Pro__Main',
			'thickbox_url' => '//couponcreatorplugin.com/products/wordpress-coupon-creator-pro/?TB_iframe=true',
		),
		array(
			'short_name'   => 'Coupon Creator Add-ons',
			'class'        => 'Cctor__Coupon__Addons__Main',
			'thickbox_url' => '//couponcreatorplugin.com/products/wordpress-coupon-creator-pro/?TB_iframe=true',
		),
	);

	/**
	 * Searches the plugin list for key/value pair and return the full details for that plugin
	 *
	 * @param string $search_key The array key this value will appear in
	 * @param string $search_val The value itself
	 *
	 * @return array|null
	 */
	public function get_plugin_by_key( $search_key, $search_val ) {
		foreach ( $this->pngx_plugins as $plugin ) {
			if ( isset( $plugin[ $search_key ] ) && $plugin[ $search_key ] === $search_val ) {
				return $plugin;
			}
		}

		return null;
	}

	/**
	 * Retrieves plugins details by plugin name
	 *
	 * @param string $name Common name for the plugin, not necessarily the lengthy name in the WP Admin Plugins list
	 *
	 * @return array|null
	 */
	public function get_plugin_by_name( $name ) {
		return $this->get_plugin_by_key( 'short_name', $name );
	}

	/**
	 * Retrieves plugins details by class name
	 *
	 * @param string $main_class Main/base class for this plugin
	 *
	 * @return array|null
	 */
	public function get_plugin_by_class( $main_class ) {
		return $this->get_plugin_by_key( 'class', $main_class );
	}

	/**
	 * Retrieves the entire list
	 *
	 * @return array
	 */
	public function get_list() {
		/**
		 * Gives an opportunity to filter the list of pngx plugins
		 *
		 * @param array Contains a list of all pngx plugins
		 *
		 * @since 3.0
		 *
		 */
		return apply_filters( 'pngx_plugins_get_list', $this->pngx_plugins );
	}

}

