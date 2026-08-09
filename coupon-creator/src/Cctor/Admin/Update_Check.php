<?php
/**
 * On-demand update check for the Coupon Creator family.
 *
 * @package Coupon_Creator
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Lets an admin ask for a fresh update check instead of waiting out two caches.
 *
 * Nothing here changes when updates are delivered; it only shortens the wait for someone who
 * knows a release is out. Two timers stack up between publishing and a site noticing:
 *
 * - WordPress throttles wp_update_plugins() to 12 hours on ordinary admin pages and 2 under
 *   cron (wp-includes/update.php), measured from the update_plugins transient's last_checked.
 * - The EDD updater caches each plugin's version response for 3 hours in an option named
 *   "edd_sl_" followed by an md5, so even a forced run can be answered from that cache.
 *
 * Together they meant a customer who had just updated core could sit for most of a day before
 * Pro and Add-ons appeared, which read as the update being broken rather than pending. Clearing
 * both and running the check makes it immediate.
 *
 * @since 3.6.2
 */
class Cctor__Coupon__Admin__Update_Check {

	/**
	 * admin-post.php action, and the nonce that guards it.
	 */
	const ACTION = 'cctor_check_updates';

	/**
	 * Query argument carrying the result back to the screen the request came from.
	 */
	const RESULT = 'cctor-updates-checked';

	/**
	 * Register hooks.
	 */
	public function hook() {
		add_action( 'admin_post_' . self::ACTION, array( $this, 'handle_request' ) );
		add_action( 'admin_notices', array( $this, 'render_result_notice' ) );
	}

	/**
	 * Nonced URL that triggers a check and returns to the current screen.
	 *
	 * @return string
	 */
	public function get_url() {
		return wp_nonce_url(
			add_query_arg( 'action', self::ACTION, admin_url( 'admin-post.php' ) ),
			self::ACTION
		);
	}

	/**
	 * Ready-made link, for callers that just want to drop one into a notice or a plugin row.
	 *
	 * Returns an empty string for users who could not act on an update anyway.
	 *
	 * @param string|null $text Link text. Defaults to "Check for updates".
	 *
	 * @return string
	 */
	public function get_link( $text = null ) {

		if ( ! current_user_can( 'update_plugins' ) ) {
			return '';
		}

		if ( null === $text ) {
			$text = __( 'Check for updates', 'coupon-creator' );
		}

		return sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $this->get_url() ),
			esc_html( $text )
		);
	}

	/**
	 * Handle the admin-post request, then send the user back where they came from.
	 */
	public function handle_request() {

		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_die(
				esc_html__( 'You are not allowed to check for plugin updates on this site.', 'coupon-creator' ),
				'',
				array( 'response' => 403 )
			);
		}

		check_admin_referer( self::ACTION );

		$cleared = $this->run();

		// Back to the screen the link was clicked on. wp_get_referer() is already validated
		// against the site, and the Plugins screen is the useful fallback because that is where
		// any update this just found will be waiting.
		$referer = wp_get_referer();

		if ( ! $referer ) {
			$referer = admin_url( 'plugins.php' );
		}

		wp_safe_redirect( add_query_arg( self::RESULT, $cleared, $referer ) );
		exit;
	}

	/**
	 * Clear both caches and run the check.
	 *
	 * Order matters: the cached version responses have to go before the transient, or the
	 * update check that follows is answered from them and nothing changes.
	 *
	 * @return int How many cached version responses were discarded.
	 */
	public function run() {

		$cleared = $this->clear_version_caches();

		// last_checked lives on this transient, and it is what wp_update_plugins() measures its
		// timeout against. Removing it is what makes the next call actually go out.
		delete_site_transient( 'update_plugins' );

		wp_update_plugins();

		return $cleared;
	}

	/**
	 * Delete every cached EDD version response.
	 *
	 * Both updaters in play here key their cache the same way, which is why one sweep covers
	 * the family: "edd_sl_" plus an md5. The SDK hashes wp_json_encode( [ slug, key, beta ] )
	 * and the older Pngx updater hashes serialize( $slug . $key . $beta ), so the hashes differ
	 * but the option-name shape does not.
	 *
	 * The shape is checked in PHP rather than in SQL so this stays portable, and so a real
	 * Easy Digital Downloads settings option -- those have readable names, not hashes -- can
	 * never be caught by it.
	 *
	 * @return int
	 */
	protected function clear_version_caches() {
		global $wpdb;

		$names = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( 'edd_sl_' ) . '%'
			)
		);

		if ( empty( $names ) ) {
			return 0;
		}

		$cleared = 0;

		foreach ( $names as $name ) {

			if ( ! preg_match( '/^edd_sl_[0-9a-f]{32}$/', $name ) ) {
				continue;
			}

			delete_option( $name );
			$cleared ++;
		}

		return $cleared;
	}

	/**
	 * Report the outcome after the redirect.
	 */
	public function render_result_notice() {

		if ( ! isset( $_GET[ self::RESULT ] ) ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$found = $this->get_family_updates();

		echo '<div class="notice notice-success is-dismissible"><p>';

		if ( empty( $found ) ) {
			esc_html_e( 'Coupon Creator checked for updates. Everything in the family is up to date.', 'coupon-creator' );
		} else {
			printf(
				/* translators: %s: comma-separated list of plugin names with updates waiting. */
				esc_html__( 'Coupon Creator checked for updates and found one for %s.', 'coupon-creator' ),
				esc_html( implode( ', ', $found ) )
			);

			printf(
				' <a href="%1$s">%2$s</a>',
				esc_url( admin_url( 'plugins.php' ) ),
				esc_html__( 'Go to Plugins', 'coupon-creator' )
			);
		}

		echo '</p></div>';
	}

	/**
	 * Which family plugins have an update waiting after the check.
	 *
	 * @return array<int, string> Display names.
	 */
	protected function get_family_updates() {

		$transient = get_site_transient( 'update_plugins' );

		if ( empty( $transient->response ) ) {
			return array();
		}

		$family = array(
			'coupon-creator/coupon_creator.php' => __( 'Coupon Creator', 'coupon-creator' ),
		);

		foreach ( Cctor__Coupon__Admin__Site_Health::EXTENSIONS as $extension ) {
			$family[ $extension['file'] ] = $extension['name'];
		}

		$found = array();

		foreach ( $family as $file => $name ) {
			if ( isset( $transient->response[ $file ] ) ) {
				$found[] = $name;
			}
		}

		return $found;
	}
}
