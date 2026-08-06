<?php
declare( strict_types=1 );

namespace Pngx\Vendor\Artifex\Telemetry;

/**
 * Stable, site-wide telemetry install identifier.
 *
 * Generated once and shared by every plugin on the site (all Strauss-prefixed
 * copies of this library read the same option), so the server can key a site's
 * identity on a value that survives url changes, migrations and http/https/www
 * moves — unlike the mutable site url it keyed on before.
 *
 * Known limitation: a full DB clone copies this option, so a staging copy shares
 * the production identity. That is inherent to opt-in, best-effort telemetry; a
 * "reset" simply deletes the option and the next report mints a fresh id.
 *
 * @since 2.1.0
 */
final class Identity {

	public const OPT_INSTALL_ID = 'artifex_telemetry_install_id';

	/**
	 * Return the site's telemetry install id, creating it on first use.
	 */
	public static function install_id(): string {
		$id = get_option( self::OPT_INSTALL_ID );

		if ( is_string( $id ) && self::is_uuid( $id ) ) {
			return $id;
		}

		$id = function_exists( 'wp_generate_uuid4' ) ? wp_generate_uuid4() : self::fallback_uuid();
		update_option( self::OPT_INSTALL_ID, $id );

		return $id;
	}

	private static function is_uuid( string $value ): bool {
		return (bool) preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value );
	}

	/**
	 * UUID v4 fallback for the (pre-4.7) case where wp_generate_uuid4 is absent.
	 */
	private static function fallback_uuid(): string {
		return sprintf(
			'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			random_int( 0, 0xffff ), random_int( 0, 0xffff ),
			random_int( 0, 0xffff ),
			random_int( 0, 0x0fff ) | 0x4000,
			random_int( 0, 0x3fff ) | 0x8000,
			random_int( 0, 0xffff ), random_int( 0, 0xffff ), random_int( 0, 0xffff )
		);
	}
}
