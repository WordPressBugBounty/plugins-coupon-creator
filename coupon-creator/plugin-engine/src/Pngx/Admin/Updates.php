<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Plugin Version Update and Data Updater
 *
 */
class Pngx__Admin__Updates {

	protected $plugin_name;
	protected $version_key;
	protected $version_num;
	protected $license;
	protected $store_url;
	protected $plugin_path;

	/**
	 * Setup Version Update
	 *
	 * @param $plugin_name
	 * @param $key
	 * @param $num
	 * @param $license
	 * @param $store_url
	 * @param $plugin_path
	 */
	public function __construct( $plugin_name, $key, $num, $license, $store_url, $plugin_path ) {

		$this->plugin_name = $plugin_name;
		$this->version_key = $key;
		$this->version_num = $num;
		$this->license     = get_option( $license );
		$this->store_url   = $store_url;
		$this->plugin_path = $plugin_path;

		$this->update_version_number();
		$this->plugin_updater();

	}

	/**
	 * Update Version Number
	 */
	public function update_version_number() {
		//Update Version Number
		if ( get_option( $this->version_key ) != $this->version_num ) {

			do_action( 'pngx_update_plugin_version', $this->version_key, $this->version_num );

			// Then update the version value
			update_option( $this->version_key, $this->version_num );

		}
	}

	/**
	 * Get the update API endpoint url.
	 *
	 * Mirrors Pngx__Admin__EDD_License::get_update_url() so a single
	 * `pngx_update_url` filter moves BOTH halves of the licensing wire:
	 * the license activate/check/deactivate GETs, and the get_version POST
	 * that drives plugin updates. Without this the update path was the only
	 * unfilterable one, so relocating the store meant shipping a new release
	 * to every installed site before updates could follow.
	 *
	 * @since 4.0.8
	 *
	 * @return string
	 */
	public function get_update_url() {
		return apply_filters( 'pngx_update_url', $this->store_url );
	}

	/*
	 * Setup Automatic Updater for Pro Using EDD
	 *
	 */
	public function plugin_updater() {

		//Check if the License has changed and deactivate
		if ( ( isset( $this->license['key'] ) && '' != $this->license['key'] ) && ( isset( $this->license['status'] ) && 'valid' == $this->license['status'] ) ) {

			$edd_updater = new Pngx__Admin__EDD_Plugin_Updater( $this->get_update_url(), $this->plugin_path, array(
				'version'   => get_option( $this->version_key ),
				'license'   => trim( $this->license['key'] ),
				'item_name' => $this->plugin_name,
				'author'    => 'Brian Jessee'
			) );

		}

	}

}