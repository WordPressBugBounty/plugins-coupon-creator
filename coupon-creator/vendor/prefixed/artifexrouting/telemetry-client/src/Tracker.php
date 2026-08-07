<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by Artifex Routing Co using {@see https://github.com/BrianHenryIE/strauss}.
 */
declare( strict_types=1 );

namespace Pngx\Vendor\Artifex\Telemetry;

/**
 * Usage-tracking client. Wire-compatible fork of the Wisdom
 * Plugin_Usage_Tracker v1.2.4: POSTs the same body keys to
 * {endpoint}/?usage_tracker=hello.
 *
 * @since 1.0.0
 */
class Tracker {

	/**
	 * Client version. Sent both as the legacy `wisdom_version` field (server
	 * stores it as-is) and the cleanly-named `client_version` field.
	 */
	public const CLIENT_VERSION = '2.2.0';

	/**
	 * Payload schema version. Bump when the field set changes so the server can
	 * branch on client generation.
	 */
	public const CONTRACT_VERSION = 2;

	public const CRON_HOOK = 'artifex_telemetry_track';

	private Consent $consent;
	private Notices $notices;
	private Goodbye_Form $goodbye_form;

	public function __construct( private readonly Config $config ) {
		$this->consent      = new Consent( $config );
		$this->notices      = new Notices( $config, $this->consent, $this );
		$this->goodbye_form = new Goodbye_Form( $config, $this->consent, $this );

		register_activation_hook( $config->plugin_file, [ $this, 'schedule_tracking' ] );
		register_deactivation_hook( $config->plugin_file, [ $this, 'on_deactivate' ] );

		$this->init();
	}

	public function get_config(): Config {
		return $this->config;
	}

	public function get_consent(): Consent {
		return $this->consent;
	}

	private function init(): void {
		if ( ! $this->config->require_optin ) {
			$this->consent->set_can_collect_email( true );
			$this->consent->set_tracking_allowed( true );
			$this->consent->block_notice();
			$this->do_tracking();
		}

		add_filter( 'cron_schedules', [ $this, 'add_cron_schedules' ] );
		// Per-product event: the slug travels as an event arg so several
		// plugins (each with its own prefixed copy of this class) never
		// clobber each other's schedules.
		add_action( self::CRON_HOOK, [ $this, 'cron_tracking' ] );

		add_action( 'admin_init', [ Migration::class, 'maybe_migrate' ], 4 );
		add_action( 'admin_init', [ $this->consent, 'set_notification_time' ], 5 );

		$this->notices->register();
		$this->goodbye_form->register();
	}

	/**
	 * @param array<string, array{interval: int, display: string}> $schedules
	 * @return array<string, array{interval: int, display: string}>
	 */
	public function add_cron_schedules( $schedules ) {
		if ( ! isset( $schedules['weekly'] ) ) {
			$schedules['weekly'] = [
				'interval' => WEEK_IN_SECONDS,
				'display'  => 'Once Weekly',
			];
		}
		if ( ! isset( $schedules['monthly'] ) ) {
			$schedules['monthly'] = [
				'interval' => 2635200,
				'display'  => 'Once Monthly',
			];
		}

		return $schedules;
	}

	/**
	 * Tracking cadence: daily, weekly or monthly.
	 */
	public function get_schedule(): string {
		$schedule = (string) apply_filters( 'artifex_telemetry_schedule_' . $this->config->slug, $this->config->schedule );

		return in_array( $schedule, [ 'daily', 'weekly', 'monthly' ], true ) ? $schedule : 'monthly';
	}

	/**
	 * On plugin activation: schedule this plugin's recurring event and track now.
	 */
	public function schedule_tracking(): void {
		if ( ! wp_next_scheduled( self::CRON_HOOK, [ $this->config->slug ] ) ) {
			wp_schedule_event( time(), $this->get_schedule(), self::CRON_HOOK, [ $this->config->slug ] );
		}
		$this->do_tracking( true );
	}

	/**
	 * Cron callback. Every plugin's copy of this class listens on the same
	 * hook, so ignore events scheduled for other slugs.
	 */
	public function cron_tracking( string $slug = '' ): void {
		if ( $slug !== $this->config->slug ) {
			return;
		}
		$this->do_tracking();
	}

	/**
	 * Collect and send tracking data if consent and cadence allow it.
	 */
	public function do_tracking( bool $force = false ): void {
		if ( ! $this->config->endpoint ) {
			return;
		}

		if ( ! $this->consent->is_tracking_allowed() ) {
			return;
		}

		if ( ! $force && ! $this->is_time_to_track() ) {
			return;
		}

		$this->consent->set_admin_email();

		$this->send_data( $this->get_data() );
	}

	/**
	 * Immediate tracking run — hooked to admin_init right after an opt-in so
	 * the current user's email is available.
	 */
	public function force_tracking(): void {
		$this->do_tracking( true );
	}

	/**
	 * POST the payload to the telemetry server.
	 *
	 * @param array<string, mixed> $body
	 */
	public function send_data( array $body ): void {
		/**
		 * Filter the telemetry endpoint (testing / server migration).
		 *
		 * @param string $endpoint
		 */
		$endpoint = (string) apply_filters( 'artifex_telemetry_endpoint_' . $this->config->slug, $this->config->endpoint );

		$headers = [ 'X-Artifex-Telemetry-Client' => 'telemetry-client/' . self::CLIENT_VERSION ];

		// Sign the report if a per-product secret is configured. The signature
		// covers the identity tuple + a timestamp; the server recomputes it from
		// the received fields and can optionally require it. See Config::$secret
		// for the (deliberately modest) threat model.
		if (
			! empty( $this->config->secret )
			&& isset( $body['install_id'], $body['plugin_slug'], $body['url'], $body['client_version'] )
		) {
			$ts             = (string) time();
			$canonical      = implode( '|', [ $body['install_id'], $body['plugin_slug'], $body['url'], $body['client_version'], $ts ] );
			$body['sig_ts'] = $ts;
			$body['sig']    = hash_hmac( 'sha256', $canonical, $this->config->secret );
			$headers['X-Artifex-Signature'] = $body['sig'];
		}

		wp_remote_post(
			trailingslashit( $endpoint ) . '?usage_tracker=hello',
			[
				'method'      => 'POST',
				'timeout'     => 20,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking'    => true,
				'body'        => $body,
				'headers'     => $headers,
				'user-agent'  => 'ATX/' . self::CLIENT_VERSION . '; ' . home_url(),
			]
		);

		$this->consent->set_last_track_time();
	}

	/**
	 * Build the payload. Key set is the v1.2.4 wire contract — do not rename
	 * or remove keys; the server whitelists them.
	 *
	 * @return array<string, mixed>
	 */
	public function get_data(): array {
		$body = [
			'plugin_slug'    => sanitize_text_field( $this->config->slug ),
			'url'            => home_url(),
			'site_name'      => get_bloginfo( 'name' ),
			'site_version'   => get_bloginfo( 'version' ),
			'site_language'  => get_bloginfo( 'language' ),
			'charset'        => get_bloginfo( 'charset' ),
			'wisdom_version' => self::CLIENT_VERSION,
			// Stable site identity (survives url changes) + clean version fields.
			// Additive to the frozen v1.2.4 contract; the server keys dedup on
			// install_id when present and falls back to url for legacy clients.
			'install_id'       => Identity::install_id(),
			'client_version'   => self::CLIENT_VERSION,
			'contract_version' => self::CONTRACT_VERSION,
			'php_version'    => phpversion(),
			'multisite'      => is_multisite(),
			'file_location'  => $this->config->plugin_file,
			'product_type'   => 'plugin',
			'message'        => '',
		];

		if ( $this->consent->can_collect_email() ) {
			$body['email'] = $this->consent->get_admin_email();
		}
		$body['marketing_method'] = $this->config->marketing;

		$body['server'] = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

		if ( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins        = array_keys( get_plugins() );
		$active_plugins = get_option( 'active_plugins', [] );

		$body['active_plugins']   = $active_plugins;
		$body['inactive_plugins'] = array_values( array_diff( $plugins, $active_plugins ) );

		$body['text_direction'] = is_rtl() ? 'RTL' : 'LTR';

		$plugin_data    = $this->get_plugin_data();
		$body['status'] = 'Active'; // Never translated.
		if ( empty( $plugin_data ) ) {
			$body['message'] .= 'We can\'t detect any product information.';
			$body['status']   = 'Data not found'; // Never translated.
		} else {
			if ( isset( $plugin_data['Name'] ) ) {
				$body['plugin'] = sanitize_text_field( $plugin_data['Name'] );
			}
			if ( isset( $plugin_data['Version'] ) ) {
				$body['version'] = sanitize_text_field( $plugin_data['Version'] );
			}
		}

		// Report tracked options, but only those flagged as registered
		// (legacy wisdom_registered_setting key — shipped-settings contract).
		$plugin_options = [];
		foreach ( $this->config->options as $option_name ) {
			$fields = get_option( $option_name );
			if ( isset( $fields['wisdom_registered_setting'] ) ) {
				foreach ( $fields as $key => $value ) {
					$plugin_options[ $key ] = $value;
				}
			}
		}
		$body['plugin_options']        = $this->config->options;
		$body['plugin_options_fields'] = $plugin_options;

		$theme = wp_get_theme();
		if ( $theme->Name ) {
			$body['theme'] = sanitize_text_field( $theme->Name );
		}
		if ( $theme->Version ) {
			$body['theme_version'] = sanitize_text_field( $theme->Version );
		}
		if ( $theme->Template ) {
			$body['theme_parent'] = sanitize_text_field( $theme->Template );
		}

		/**
		 * Filter the outgoing telemetry payload — e.g. to redact specific keys
		 * before it is sent. Removing `url` or `plugin_slug` makes the server
		 * reject the report; removing `install_id` disables request signing.
		 *
		 * @param array<string, mixed> $body The payload.
		 * @param string               $slug The plugin slug.
		 */
		return apply_filters( 'artifex_telemetry_payload', $body, $this->config->slug );
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_plugin_data(): array {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			include ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return (array) get_plugin_data( $this->config->plugin_file );
	}

	/**
	 * On plugin deactivation: send the goodbye payload, then remove only this
	 * plugin's schedule (the legacy tracker cleared the shared hook and killed
	 * every other plugin's tracking with it).
	 */
	public function on_deactivate(): void {
		if ( $this->consent->is_tracking_allowed() ) {
			$body                     = $this->get_data();
			$body['status']           = 'Deactivated'; // Never translated.
			$body['deactivated_date'] = time();

			$reason = get_option( Goodbye_Form::OPT_REASON_PREFIX . $this->config->slug );
			if ( false !== $reason ) {
				$body['deactivation_reason'] = $reason;
			}
			$details = get_option( Goodbye_Form::OPT_DETAILS_PREFIX . $this->config->slug );
			if ( false !== $details ) {
				$body['deactivation_details'] = $details;
			}
			// Additive since 2.2.0: per-reason follow-up answers as a JSON
			// object of reason key => short text.
			$followup = get_option( Goodbye_Form::OPT_FOLLOWUP_PREFIX . $this->config->slug );
			if ( false !== $followup ) {
				$body['deactivation_followup'] = $followup;
			}

			$this->send_data( $body );

			// One survey, one send: a reactivate -> deactivate cycle must not
			// resend a stale survey from a previous goodbye.
			delete_option( Goodbye_Form::OPT_REASON_PREFIX . $this->config->slug );
			delete_option( Goodbye_Form::OPT_DETAILS_PREFIX . $this->config->slug );
			delete_option( Goodbye_Form::OPT_FOLLOWUP_PREFIX . $this->config->slug );
		}

		wp_clear_scheduled_hook( self::CRON_HOOK, [ $this->config->slug ] );
		$this->consent->clear_last_track_time();
	}

	/**
	 * Whether the cadence period has elapsed since the last report.
	 */
	private function is_time_to_track(): bool {
		$last = $this->consent->get_last_track_time();
		if ( null === $last ) {
			return true;
		}

		$period = match ( $this->get_schedule() ) {
			'daily'  => 'day',
			'weekly' => 'week',
			default  => 'month',
		};

		return $last < strtotime( '-1 ' . $period );
	}
}
