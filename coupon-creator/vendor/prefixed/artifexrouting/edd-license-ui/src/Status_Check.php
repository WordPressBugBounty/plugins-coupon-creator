<?php
/**
 * EDD Software Licensing SDK — license status check.
 *
 * The SDK saves the license status only on activation and in a weekly WP-Cron
 * check, and after one failed request it blocks every license request to the
 * endpoint for an hour. A renewal can therefore show the old expiration date for
 * weeks on a site whose outgoing requests often fail (support case AX-1027).
 *
 * This class checks the license against the server itself:
 *
 * - once a day when an admin opens a screen that shows the license (an hour
 *   after a failed check, not a day);
 * - on demand from the "Check now" button that Control renders;
 * - with its own request, so the SDK's one-hour block never stops it, and the
 *   real error (timeout, HTTP status) is kept for the admin to see.
 *
 * Every check stores its time and its error, if any, in `<slug>_license_checked`.
 * Control::checked() shows that as the "Last checked" line.
 *
 * @package Artifex\License
 *
 * Modified by Artifex Routing Co using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare( strict_types=1 );

namespace Pngx\Vendor\Artifex\License;

class Status_Check {

	/**
	 * AJAX action prefix for the "Check now" button. The full action, and its
	 * nonce action, is this prefix plus the plugin slug.
	 */
	public const AJAX_PREFIX = 'artifex_license_check_';

	/**
	 * Messages, or a closure that returns them (resolved on first use, so a
	 * plugin can build the object before its text domain loads).
	 *
	 * @var array<string, string>|\Closure
	 */
	private $strings;

	/**
	 * @param string                $slug          SDK integration id (also the option prefix).
	 * @param int                   $item_id       EDD download ID.
	 * @param string                $store_url     Licensing endpoint, as registered with the SDK.
	 * @param string                $version       Plugin version sent with the check.
	 * @param string                $key_option    Option holding the license key.
	 * @param string                $status_option Option the SDK stores the status object in.
	 * @param array<string, string>|\Closure $strings Optional translated messages, or a
	 *        closure that returns them: no_key, forbidden, http_status (one %d),
	 *        bad_response, unknown_key.
	 */
	public function __construct(
		private string $slug,
		private int $item_id,
		private string $store_url,
		private string $version,
		private string $key_option,
		private string $status_option,
		array|\Closure $strings = []
	) {
		$this->strings = $strings;
	}

	/**
	 * One message, with the English default when the plugin gives none.
	 */
	private function message( string $key ): string {
		if ( $this->strings instanceof \Closure ) {
			$this->strings = (array) ( $this->strings )();
		}

		$defaults = [
			'no_key'       => 'No license key is saved. Activate the license first.',
			'forbidden'    => 'You are not allowed to check the license.',
			'http_status'  => 'The license server answered with HTTP status %d.',
			'bad_response' => 'The license server sent an answer that could not be read.',
			'unknown_key'  => 'The license server does not recognize this license key.',
		];

		return (string) ( $this->strings[ $key ] ?? $defaults[ $key ] );
	}

	/**
	 * Hook the lockout clear, the "Check now" handler, and the daily check on
	 * the given admin screens (load-{$screen}).
	 *
	 * @param array<int, string> $screens
	 */
	public function register_hooks( array $screens ): void {
		add_action( 'wp_ajax_edd_sl_sdk_activate_' . $this->slug, [ $this, 'clear_request_lockout' ], 1 );
		add_action( 'wp_ajax_edd_sl_sdk_deactivate_' . $this->slug, [ $this, 'clear_request_lockout' ], 1 );
		add_action( 'wp_ajax_' . self::ajax_action( $this->slug ), [ $this, 'ajax_check' ] );
		foreach ( $screens as $screen ) {
			add_action( 'load-' . $screen, [ $this, 'maybe_refresh' ] );
		}
	}

	/**
	 * The AJAX (and nonce) action for a plugin's "Check now" button.
	 */
	public static function ajax_action( string $slug ): string {
		return self::AJAX_PREFIX . $slug;
	}

	/**
	 * Option that stores the last check: [ 'time' => int, 'error' => string ].
	 */
	public static function checked_option_for( string $slug ): string {
		return $slug . '_license_checked';
	}

	public function checked_option(): string {
		return self::checked_option_for( $this->slug );
	}

	/**
	 * Transient that holds back the next automatic check.
	 */
	public function throttle_transient(): string {
		return $this->slug . '_license_refreshed';
	}

	/**
	 * Option the SDK sets after a failed request to block every license request
	 * to the endpoint for an hour (Requests\API::log_failed_request()).
	 */
	public function request_lockout_option(): string {
		return 'eddsdk_failed_request_' . md5( $this->store_url );
	}

	/**
	 * Clear the SDK's one-hour request block before a manual Activate or
	 * Deactivate. Hooked ahead of the SDK's own AJAX handler, which still does
	 * the nonce and token checks.
	 */
	public function clear_request_lockout(): void {
		if ( current_user_can( 'manage_options' ) ) {
			delete_option( $this->request_lockout_option() );
		}
	}

	/**
	 * Check an activated license when an admin opens a screen that shows it:
	 * at most once a day, or once an hour after a failed check.
	 */
	public function maybe_refresh(): void {
		if ( ! current_user_can( 'manage_options' ) || false !== get_transient( $this->throttle_transient() ) ) {
			return;
		}

		$key = get_option( $this->key_option );
		if ( empty( $key ) || ! is_string( $key ) || ! is_object( get_option( $this->status_option ) ) ) {
			return;
		}

		$this->check();
	}

	/**
	 * "Check now" handler. Answers with the result; the button reloads the page
	 * so the status and "Last checked" lines show it.
	 */
	public function ajax_check(): void {
		check_ajax_referer( self::ajax_action( $this->slug ) );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => $this->message( 'forbidden' ) ], 403 );
		}

		$result = $this->check();
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		}

		wp_send_json_success();
	}

	/**
	 * Ask the license server for the current status and save it.
	 *
	 * A status is saved only when the server knows the key, so a server-side
	 * problem never replaces a good status with "invalid". Every result is
	 * recorded in the checked option.
	 *
	 * @return true|\WP_Error
	 */
	public function check() {
		$key = get_option( $this->key_option );
		if ( empty( $key ) || ! is_string( $key ) ) {
			return $this->record( new \WP_Error( 'artifex_license_no_key', $this->message( 'no_key' ) ) );
		}

		$response = wp_remote_get(
			add_query_arg(
				[
					'edd_action'  => 'check_license',
					'license'     => $key,
					'item_id'     => $this->item_id,
					'version'     => $this->version,
					'url'         => rawurlencode( home_url() ),
					'environment' => wp_get_environment_type(),
				],
				$this->store_url
			),
			[
				'timeout'   => 15,
				'sslverify' => (bool) apply_filters( 'https_ssl_verify', true, $this->store_url ),
			]
		);

		if ( is_wp_error( $response ) ) {
			return $this->record( $response );
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 !== $code ) {
			return $this->record( new \WP_Error( 'artifex_license_http_status', sprintf( $this->message( 'http_status' ), $code ) ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );
		if ( ! is_object( $data ) || empty( $data->license ) ) {
			return $this->record( new \WP_Error( 'artifex_license_bad_response', $this->message( 'bad_response' ) ) );
		}

		if ( empty( $data->success ) ) {
			return $this->record( new \WP_Error( 'artifex_license_unknown_key', $this->message( 'unknown_key' ) ) );
		}

		update_option( $this->status_option, $data );
		// The server answered, so let the SDK's own requests (updates) run again.
		delete_option( $this->request_lockout_option() );

		return $this->record( true );
	}

	/**
	 * Store the result of a check and hold back the next automatic one.
	 *
	 * @param true|\WP_Error $result
	 * @return true|\WP_Error
	 */
	private function record( $result ) {
		$failed = is_wp_error( $result );

		update_option(
			$this->checked_option(),
			[
				'time'  => time(),
				'error' => $failed ? $result->get_error_message() : '',
			],
			false
		);
		set_transient( $this->throttle_transient(), 1, $failed ? HOUR_IN_SECONDS : DAY_IN_SECONDS );

		return $result;
	}
}
