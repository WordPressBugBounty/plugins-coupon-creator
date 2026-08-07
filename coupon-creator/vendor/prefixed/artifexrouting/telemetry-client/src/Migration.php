<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by Artifex Routing Co using {@see https://github.com/BrianHenryIE/strauss}.
 */
declare( strict_types=1 );

namespace Pngx\Vendor\Artifex\Telemetry;

/**
 * One-time, site-local migration from the legacy Wisdom tracker
 * (Plugin_Usage_Tracker v1.2.4) option keys to the artifex_telemetry_* keys.
 *
 * Carries over opt-in state, notice dismissals and track times so existing
 * sites are never re-prompted and never silently stop reporting. The legacy
 * wisdom_* options are left in place for rollback; deleting them is a later
 * cleanup once all plugins ship this client.
 *
 * @since 1.0.0
 */
class Migration {

	private const MAP = [
		'wisdom_allow_tracking'     => Consent::OPT_ALLOW_TRACKING,
		'wisdom_collect_email'      => Consent::OPT_COLLECT_EMAIL,
		'wisdom_admin_emails'       => Consent::OPT_ADMIN_EMAILS,
		'wisdom_block_notice'       => Consent::OPT_BLOCK_NOTICE,
		'wisdom_notification_times' => Consent::OPT_NOTIFICATION_TIMES,
		'wisdom_last_track_time'    => Consent::OPT_LAST_TRACK_TIME,
	];

	/**
	 * Legacy shared cron hook. The old tracker scheduled one global event for
	 * every plugin; the new client schedules per-slug events, so once any
	 * plugin migrates, the global hook is stale.
	 */
	private const LEGACY_CRON_HOOK = 'put_do_weekly_action';

	public static function maybe_migrate(): void {
		// One-shot: skip when the new store already exists (either migrated
		// earlier or this is a fresh site with no legacy data).
		if ( false !== get_option( Consent::OPT_ALLOW_TRACKING ) ) {
			return;
		}

		$legacy = get_option( 'wisdom_allow_tracking' );
		if ( false === $legacy ) {
			return;
		}

		foreach ( self::MAP as $old => $new ) {
			$value = get_option( $old );
			if ( false !== $value ) {
				update_option( $new, $value );
			}
		}

		// Per-slug deactivation records: the option arrays above tell us which
		// slugs the legacy tracker knew about.
		$slugs = is_array( $legacy ) ? array_keys( $legacy ) : [];
		foreach ( $slugs as $slug ) {
			foreach ( [ 'reason', 'details' ] as $kind ) {
				$value = get_option( "wisdom_deactivation_{$kind}_{$slug}" );
				if ( false !== $value ) {
					update_option( "artifex_telemetry_deactivation_{$kind}_{$slug}", $value );
				}
			}
		}

		wp_clear_scheduled_hook( self::LEGACY_CRON_HOOK );
	}
}
