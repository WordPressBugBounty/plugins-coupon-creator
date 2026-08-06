<?php
declare( strict_types=1 );

namespace Pngx\Vendor\Artifex\Telemetry;

/**
 * Site-local consent and bookkeeping storage.
 *
 * Options are arrays keyed by plugin slug (same shape as the legacy wisdom_*
 * options), so any number of plugins — including Strauss-prefixed copies of
 * this library — share one consent store per site.
 *
 * @since 1.0.0
 */
class Consent {

	public const OPT_ALLOW_TRACKING     = 'artifex_telemetry_allow_tracking';
	public const OPT_COLLECT_EMAIL      = 'artifex_telemetry_collect_email';
	public const OPT_ADMIN_EMAILS       = 'artifex_telemetry_admin_emails';
	public const OPT_BLOCK_NOTICE       = 'artifex_telemetry_block_notice';
	public const OPT_NOTIFICATION_TIMES = 'artifex_telemetry_notification_times';
	public const OPT_LAST_TRACK_TIME    = 'artifex_telemetry_last_track_time';

	public function __construct( private readonly Config $config ) {}

	/**
	 * Whether tracking is allowed for this plugin.
	 */
	public function is_tracking_allowed(): bool {
		// An opt-out flag inside a tracked option always wins.
		if ( $this->has_user_opted_out() ) {
			$this->set_tracking_allowed( false );
			return false;
		}

		$allowed = get_option( self::OPT_ALLOW_TRACKING );

		return isset( $allowed[ $this->config->slug ] );
	}

	public function set_tracking_allowed( bool $is_allowed ): void {
		if ( $this->has_user_opted_out() ) {
			$is_allowed = false;
		}

		if ( ! $is_allowed && ! $this->config->require_optin ) {
			$is_allowed = true;
		}

		$this->set_slug_flag( self::OPT_ALLOW_TRACKING, $is_allowed );
	}

	/**
	 * An opt-out setting stored inside any tracked option (legacy
	 * wisdom_opt_out key — part of the shipped-settings contract).
	 */
	public function has_user_opted_out(): bool {
		foreach ( $this->config->options as $option_name ) {
			$option = get_option( $option_name );
			if ( ! empty( $option['wisdom_opt_out'] ) ) {
				return true;
			}
		}

		return false;
	}

	public function can_collect_email(): bool {
		$collect = get_option( self::OPT_COLLECT_EMAIL );

		return isset( $collect[ $this->config->slug ] );
	}

	public function set_can_collect_email( bool $can_collect ): void {
		$this->set_slug_flag( self::OPT_COLLECT_EMAIL, $can_collect );
	}

	public function get_admin_email(): string {
		$emails = get_option( self::OPT_ADMIN_EMAILS );

		return $emails[ $this->config->slug ] ?? '';
	}

	/**
	 * Record the reporting email address (first admin wins).
	 */
	public function set_admin_email( ?string $email = null ): void {
		if ( empty( $email ) && function_exists( 'wp_get_current_user' ) ) {
			$email = wp_get_current_user()->user_email;
		}

		$emails = get_option( self::OPT_ADMIN_EMAILS );
		if ( ! is_array( $emails ) ) {
			$emails = [];
		}

		if ( empty( $emails[ $this->config->slug ] ) && ! empty( $email ) ) {
			$emails[ $this->config->slug ] = sanitize_email( $email );
			update_option( self::OPT_ADMIN_EMAILS, $emails );
		}
	}

	public function is_notice_blocked(): bool {
		$blocked = get_option( self::OPT_BLOCK_NOTICE );

		return isset( $blocked[ $this->config->slug ] );
	}

	/**
	 * Stop showing the opt-in notice (the user answered, or we're on a dev site).
	 */
	public function block_notice(): void {
		$this->set_slug_flag( self::OPT_BLOCK_NOTICE, true );
	}

	/**
	 * Set the earliest time the opt-in notice may display (filterable delay).
	 */
	public function set_notification_time(): void {
		$times = get_option( self::OPT_NOTIFICATION_TIMES, [] );
		if ( ! is_array( $times ) ) {
			$times = [];
		}

		if ( ! isset( $times[ $this->config->slug ] ) ) {
			$delay                         = (int) apply_filters( 'artifex_telemetry_delay_notification_' . $this->config->slug, 0 );
			$times[ $this->config->slug ] = time() + absint( $delay );
			update_option( self::OPT_NOTIFICATION_TIMES, $times );
		}
	}

	public function is_notification_time(): bool {
		$times = get_option( self::OPT_NOTIFICATION_TIMES, [] );

		return isset( $times[ $this->config->slug ] ) && time() >= $times[ $this->config->slug ];
	}

	public function get_last_track_time(): ?int {
		$times = get_option( self::OPT_LAST_TRACK_TIME, [] );

		return isset( $times[ $this->config->slug ] ) ? (int) $times[ $this->config->slug ] : null;
	}

	public function set_last_track_time(): void {
		$times = get_option( self::OPT_LAST_TRACK_TIME, [] );
		if ( ! is_array( $times ) ) {
			$times = [];
		}
		$times[ $this->config->slug ] = time();
		update_option( self::OPT_LAST_TRACK_TIME, $times );
	}

	public function clear_last_track_time(): void {
		$times = get_option( self::OPT_LAST_TRACK_TIME, [] );
		if ( isset( $times[ $this->config->slug ] ) ) {
			unset( $times[ $this->config->slug ] );
			update_option( self::OPT_LAST_TRACK_TIME, $times );
		}
	}

	/**
	 * Add or remove this plugin's slug in a slug-keyed array option.
	 */
	private function set_slug_flag( string $option_name, bool $on ): void {
		$value = get_option( $option_name );
		if ( ! is_array( $value ) ) {
			$value = [];
		}

		if ( $on ) {
			$value[ $this->config->slug ] = $this->config->slug;
		} else {
			unset( $value[ $this->config->slug ] );
		}

		update_option( $option_name, $value );
	}
}
