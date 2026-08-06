<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by Jessee Productions using {@see https://github.com/BrianHenryIE/strauss}.
 */
declare( strict_types=1 );

namespace Pngx\Vendor\Artifex\Telemetry;

/**
 * Opt-in and marketing admin notices.
 *
 * @since 1.0.0
 */
class Notices {

	private const NONCE_ACTION = 'artifex_telemetry_optin';

	public function __construct(
		private readonly Config $config,
		private readonly Consent $consent,
		private readonly Tracker $tracker,
	) {}

	public function register(): void {
		add_action( 'admin_notices', [ $this, 'optin_notice' ] );
		add_action( 'admin_notices', [ $this, 'marketing_notice' ] );
	}

	/**
	 * Ask the user to allow tracking (and, in combined marketing mode, email).
	 */
	public function optin_notice(): void {
		$this->handle_optin_action();

		if ( ! $this->consent->is_notification_time() ) {
			return;
		}

		if ( $this->consent->is_notice_blocked() ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Never prompt on non-production / local sites.
		if ( $this->is_local_environment() ) {
			$this->consent->block_notice();
			return;
		}

		$plugin_data = $this->tracker->get_plugin_data();
		$plugin_name = $plugin_data['Name'] ?? $this->config->slug;

		$yes_args = [
			'plugin'        => $this->config->slug,
			'plugin_action' => 'yes',
		];
		if ( Config::MARKETING_COMBINED === $this->config->marketing ) {
			$yes_args['marketing_optin'] = 'yes';
		} elseif ( Config::MARKETING_FOLLOWUP === $this->config->marketing ) {
			$yes_args['marketing'] = 'yes';
		}

		$url_yes = wp_nonce_url( add_query_arg( $yes_args ), self::NONCE_ACTION );
		$url_no  = wp_nonce_url(
			add_query_arg(
				[
					'plugin'        => $this->config->slug,
					'plugin_action' => 'no',
				]
			),
			self::NONCE_ACTION
		);

		if ( Config::MARKETING_COMBINED === $this->config->marketing ) {
			$notice_text = 'Thank you for installing our plugin. We\'d like your permission to track its usage on your site and subscribe you to our newsletter. We won\'t record any sensitive data, only information regarding the WordPress environment and plugin settings, which we will use to help us make improvements to the plugin. Tracking is completely optional.';
		} else {
			$notice_text = 'Thank you for installing our plugin. We would like to track its usage on your site. We don\'t record any sensitive data, only information regarding the WordPress environment and plugin settings, which we will use to help us make improvements to the plugin. Tracking is completely optional.';
		}
		$notice_text = (string) apply_filters( 'artifex_telemetry_notice_text_' . $this->config->slug, $notice_text );
		?>
		<div class="notice notice-info atx-telemetry-notice">
			<p><strong><?php echo esc_html( $plugin_name ); ?></strong></p>
			<p><?php echo esc_html( $notice_text ); ?></p>
			<p>
				<a href="<?php echo esc_url( $url_yes ); ?>" class="button-secondary"><?php echo esc_html( 'Allow' ); ?></a>
				<a href="<?php echo esc_url( $url_no ); ?>" class="button-secondary"><?php echo esc_html( 'Do Not Allow' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Follow-up notice asking permission to collect the email address
	 * (marketing mode 2), plus the handler for its response.
	 */
	public function marketing_notice(): void {
		// Response handler — also serves the combined (mode 1) opt-in link.
		if ( isset( $_GET['marketing_optin'], $_GET['plugin'] ) && $this->config->slug === $_GET['plugin'] ) {
			if (
				! isset( $_GET['_wpnonce'] )
				|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), self::NONCE_ACTION )
				|| ! current_user_can( 'manage_options' )
			) {
				return;
			}

			// The legacy tracker passed the raw value into set_can_collect_email(),
			// so declining ('no', a truthy string) still enabled collection.
			$this->consent->set_can_collect_email( 'yes' === $_GET['marketing_optin'] );
			$this->tracker->do_tracking( true );

			return;
		}

		if (
			Config::MARKETING_FOLLOWUP !== $this->config->marketing
			|| ! isset( $_GET['marketing'], $_GET['plugin'] )
			|| 'yes' !== $_GET['marketing']
			|| $this->config->slug !== $_GET['plugin']
		) {
			return;
		}

		if (
			! isset( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), self::NONCE_ACTION )
			|| ! current_user_can( 'manage_options' )
		) {
			return;
		}

		$plugin_data = $this->tracker->get_plugin_data();
		$plugin_name = $plugin_data['Name'] ?? $this->config->slug;

		$url_yes = wp_nonce_url(
			add_query_arg(
				[
					'plugin'          => $this->config->slug,
					'marketing_optin' => 'yes',
				]
			),
			self::NONCE_ACTION
		);
		$url_no  = wp_nonce_url(
			add_query_arg(
				[
					'plugin'          => $this->config->slug,
					'marketing_optin' => 'no',
				]
			),
			self::NONCE_ACTION
		);

		$marketing_text = 'Thank you for opting in to tracking. Would you like to receive occasional news about this plugin, including details of new features and special offers?';
		$marketing_text = (string) apply_filters( 'artifex_telemetry_marketing_text_' . $this->config->slug, $marketing_text );
		?>
		<div class="notice notice-info atx-telemetry-notice">
			<p><strong><?php echo esc_html( $plugin_name ); ?></strong></p>
			<p><?php echo esc_html( $marketing_text ); ?></p>
			<p>
				<a href="<?php echo esc_url( $url_yes ); ?>" class="button-secondary"><?php echo esc_html( 'Yes Please' ); ?></a>
				<a href="<?php echo esc_url( $url_no ); ?>" class="button-secondary"><?php echo esc_html( 'No Thank You' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Process the Allow / Do Not Allow response.
	 */
	private function handle_optin_action(): void {
		if ( ! isset( $_GET['plugin'], $_GET['plugin_action'] ) ) {
			return;
		}

		if ( $this->config->slug !== $_GET['plugin'] ) {
			return;
		}

		if (
			! isset( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), self::NONCE_ACTION )
			|| ! current_user_can( 'manage_options' )
		) {
			return;
		}

		if ( 'yes' === $_GET['plugin_action'] ) {
			$this->consent->set_tracking_allowed( true );
			// Track straight away so the opting user's email is captured.
			add_action( 'admin_init', [ $this->tracker, 'force_tracking' ] );
		} else {
			$this->consent->set_tracking_allowed( false );
		}

		$this->consent->block_notice();
	}

	/**
	 * Local/dev environment detection. The legacy check missed .local and
	 * .test entirely, so dev sites were prompted to opt in.
	 */
	private function is_local_environment(): bool {
		$is_local = 'production' !== wp_get_environment_type();

		if ( ! $is_local ) {
			$url = network_site_url( '/' );
			foreach ( [ '.local', '.test', '.dev', 'localhost', ':8888' ] as $needle ) {
				if ( false !== stripos( $url, $needle ) ) {
					$is_local = true;
					break;
				}
			}
		}

		return (bool) apply_filters( 'artifex_telemetry_is_local_' . $this->config->slug, $is_local );
	}
}
