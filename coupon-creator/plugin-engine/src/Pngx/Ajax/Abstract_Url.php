<?php
/**
 * Plugin Engine AJAX Urls.
 *
 * @since 4.0.0
 */

namespace Pngx\Ajax;

use Pngx\Volt_Vectors\Plugin;

/**
 * Class Abstract_Url
 *
 * @since   4.0.0
 *
 * @package Pngx\Ajax
 */
class Abstract_Url {

	/**
	 * The internal id of the API integration.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	public $service_id = '';

	/**
	 * The current Actions handler instance.
	 *
	 * @since 4.0.0
	 *
	 * @var Actions
	 */
	protected $actions;

	/**
	 * Get the admin ajax url with parameters to enable an API action.
	 *
	 * @since 4.0.0
	 *
	 * @param string               $request_slug   The request slug.
	 * @param string               $action         The name of the action to add to the url.
	 * @param string               $nonce          The nonce to verify for the action.
	 * @param array<string|string> $additional_arg An array of arugments to add to the query string of the admin ajax url.
	 *
	 * @return string
	 */
	public function get_admin_ajax_url_with_parameters( string $request_slug, string $action, string $nonce, array $additional_arg ) {
		$args = [
			'action'      => $action,
			$request_slug => $nonce,
			'_ajax_nonce' => $nonce,
		];

		$query_args = array_merge( $args, $additional_arg );

		return add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Returns the URL that should be used to add an access profile fields in the options.
	 *
	 * @since 0.1.0
	 *
	 * @return string The URL to add an profile.
	 */
	public function to_add_profile_link() {
		$service_id = $this->service_id;
		$nonce  = wp_create_nonce( $this->actions::$add_profile_action );

		return $this->get_admin_ajax_url_with_parameters( Plugin::$request_slug, "pngx_volt_vectors_ev_{$service_id}_options_add_profile", $nonce, [] );
	}

	/**
	 * Returns the URL that should be used to save an access profile.
	 *
	 * @since 0.1.0
	 *
	 * @param string $profile_id The profile id.
	 *
	 * @return string The URL used to save an access profile.
	 */
	public function to_save_access_profile( $profile_id ) {
		$service_id = $this->service_id;
		$nonce  = wp_create_nonce( $this->actions::$save_action );

		return $this->get_admin_ajax_url_with_parameters( Plugin::$request_slug, "pngx_volt_vectors_ev_{$service_id}_options_save_profile", $nonce, [
			'api_key' => $profile_id
		] );
	}

	/**
	 * Returns the URL that should be used to update an access profile.
	 *
	 * @since 0.1.0
	 *
	 * @param string $profile_id The profile id.
	 *
	 * @return string The URL used to update save an access profile.
	 */
	public function to_update_profile( $profile_id ) {
		$service_id = $this->service_id;
		$nonce  = wp_create_nonce( $this->actions::$update_action );

		return $this->get_admin_ajax_url_with_parameters( Plugin::$request_slug, "pngx_volt_vectors_ev_{$service_id}_options_update_profile", $nonce, [
			'api_key' => $profile_id
		] );
	}

	/**
	 * Returns the URL that should be used to delete an access profile.
	 *
	 * @since 0.1.0
	 *
	 * @param string $profile_id The profile id.
	 *
	 * @return string The URL to delete an access profile.
	 */
	public function to_delete_profile_link( $profile_id ) {
		$service_id = $this->service_id;
		$nonce  = wp_create_nonce( $this->actions::$delete_action );

		return $this->get_admin_ajax_url_with_parameters( Plugin::$request_slug, "pngx_volt_vectors_ev_{$service_id}_options_delete_profile", $nonce, [
			'api_key' => $profile_id
		] );
	}

}
