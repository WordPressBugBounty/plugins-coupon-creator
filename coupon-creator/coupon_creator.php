<?php
/*
Plugin Name: Coupon Creator
Plugin URI: https://artifexrouting.com/coupon-creator
Description: Designed, printable coupons for WordPress. Put a real coupon on your site.
Version: 3.6.2
Author: Artifex Routing Co
Author URI: https://artifexrouting.com
Text Domain: coupon-creator
Requires PHP: 8.2
License: GPLv2 or later
*/
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'COUPON_CREATOR_DIR', dirname( __FILE__ ) );
define( 'COUPON_CREATOR_MAIN_PLUGIN_FILE', __FILE__ );

// Load the Composer autoload file.
require_once dirname( COUPON_CREATOR_MAIN_PLUGIN_FILE ) . '/vendor/autoload.php';

// Load the Strauss-prefixed dependencies (Pngx\Vendor\...). This was
// previously a hand-patch inside vendor/autoload.php, which composer
// regenerates — require it explicitly so dumps can't break autoloading.
if ( file_exists( COUPON_CREATOR_DIR . '/vendor/prefixed/autoload.php' ) ) {
	require_once COUPON_CREATOR_DIR . '/vendor/prefixed/autoload.php';
}

// the main plugin class
require_once dirname( __FILE__ ) . '/src/Cctor/Main.php';
Cctor__Coupon__Main::instance();
register_activation_hook( __FILE__, [ 'Cctor__Coupon__Main', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'Cctor__Coupon__Main', 'deactivate' ] );

/**
 * Register uninstall script.
 *
 * @since 3.4.0
 */
register_uninstall_hook( __FILE__, 'cctor_uninstall_script' );
function cctor_uninstall_script() {
	require_once dirname( __FILE__ ) . '/src/Cctor/Main.php';
}

/**
 * Get Options from Array
 *
 * echo cctor_options('cctor_coupon_base');
 *
 * @param      $option
 * @param null $falseable
 * @param null $default
 *
 * @return bool|null
 */
function cctor_options( $option, $falseable = null, $default = null ) {
	$options = get_option( Cctor__Coupon__Main::OPTIONS_ID );

	if ( isset( $options[ $option ] ) && $options[ $option ] != '' ) {
		return $options[ $option ];
	} elseif ( $falseable ) {
		return false;
	} elseif ( $default ) {
		return $default;
	} else {
		return false;
	}
}

if ( ! function_exists( 'coupon_creator_start_plugin_tracking' ) ) {
	/**
	 * Start usage tracking (Artifex Telemetry client — prefixed fork of the
	 * Wisdom tracker; same wire contract and slug as the legacy class).
	 *
	 * Returns the Tracker so a check-in can be forced outside the cron cadence,
	 * e.g. `wp eval 'coupon_creator_start_plugin_tracking()->force_tracking();'`.
	 * The instance is cached: the Tracker's constructor registers hooks, so a
	 * second call must hand back the same object rather than build another.
	 *
	 * @since 3.6
	 *
	 * @return \Pngx\Vendor\Artifex\Telemetry\Tracker|null Null if the prefixed
	 *                                                     library is unavailable.
	 */
	function coupon_creator_start_plugin_tracking() {
		static $tracker = null;

		if ( null !== $tracker ) {
			return $tracker;
		}

		if ( ! class_exists( \Pngx\Vendor\Artifex\Telemetry\Tracker::class ) ) {
			return null;
		}

		$tracker = new \Pngx\Vendor\Artifex\Telemetry\Tracker(
			new \Pngx\Vendor\Artifex\Telemetry\Config(
				// Canonical Artifex telemetry host. Dedicated subdomain so ingestion
				// is independent of the storefront's access rules and can move without
				// another plugin release. Override per-slug with the
				// artifex_telemetry_endpoint_coupon_creator filter.
				endpoint: 'https://telemetry.artifexrouting.com',
				plugin_file: __FILE__,
				slug: 'coupon_creator',
				options: array( 'coupon_creator_options' ),
				require_optin: true,
				include_goodbye_form: true,
				marketing: \Pngx\Vendor\Artifex\Telemetry\Config::MARKETING_COMBINED,
				// Per-product HMAC secret so reports are signed (server verification is
				// off until the whole fleet ships signatures). Match this on the server
				// via artifex_ingest_secret_coupon_creator when enforcement is enabled.
				secret: '9403bb2ecf10e174a4ee923b858a3936223b699ced23d83dc134fd0b9c72dc96',
			)
		);

		return $tracker;
	}

	coupon_creator_start_plugin_tracking();
}

/**
 * Report the Coupon Creator extensions installed alongside core.
 *
 * Coupon Creator, Pro and Add-ons are one product group, so they check in under
 * the single `coupon_creator` slug — one opt-in notice, one consent, one record
 * per site. Pro and Add-ons therefore run no Tracker of their own; they
 * announce themselves on cctor_telemetry_extensions and ride along here.
 *
 * The payload already carries active_plugins, which reveals that an extension
 * is present; this adds the version, which it does not.
 *
 * @since 3.6
 *
 * @param array  $body The outgoing telemetry payload.
 * @param string $slug The reporting plugin slug.
 *
 * @return array
 */
add_filter( 'artifex_telemetry_payload', 'cctor_add_extensions_to_telemetry', 10, 2 );
function cctor_add_extensions_to_telemetry( $body, $slug ) {
	if ( 'coupon_creator' !== $slug ) {
		return $body;
	}

	/**
	 * Extensions to report with core's check-in, as slug => version.
	 *
	 * Keys are permanent product slugs — they are what the server reports on,
	 * so they must not be renamed once shipped.
	 *
	 * @since 3.6
	 *
	 * @param array $extensions
	 */
	$extensions = (array) apply_filters( 'cctor_telemetry_extensions', array() );

	$body['extensions'] = array_map( 'strval', array_filter( $extensions ) );

	return $body;
}

/**
 * Report which Coupon Creator settings are in use.
 *
 * Deliberately NOT done with the library's `wisdom_registered_setting` flag:
 * that reports every key in the option array, so any setting added later would
 * start being transmitted with nobody deciding it should be. This is a
 * fail-closed allowlist — a key absent from it is never sent.
 *
 * Free-text settings (custom CSS, analytics IDs, rewrite bases) are reported as
 * a boolean "is it set", never their contents.
 *
 * @since 3.6
 *
 * @param array  $body The outgoing telemetry payload.
 * @param string $slug The reporting plugin slug.
 *
 * @return array
 */
add_filter( 'artifex_telemetry_payload', 'cctor_add_settings_to_telemetry', 10, 2 );
function cctor_add_settings_to_telemetry( $body, $slug ) {
	if ( 'coupon_creator' !== $slug ) {
		return $body;
	}

	$options = get_option( Cctor__Coupon__Main::OPTIONS_ID );

	if ( ! is_array( $options ) ) {
		return $body;
	}

	// Feature toggles and display choices — no free text, no identifiers.
	$allowed = array(
		'cctor_default_template',
		'cctor_template',
		'cctor_default_date_format',
		'cctor_expiration_option',
		'cctor_wpautop',
		'cctor_nofollow_print_link',
		'cctor_nofollow_print_template',
		'cctor_multi_print',
		'cctor_advanced_templates',
		'cctor_dynamic_code',
		'cctor_reveal',
		'cctor_location_filter',
		'cctor_vendor_filter',
		'cctor_woo_show_section',
		'cctor_email_delivery',
		'cctor_email_gate',
		'cctor_pro_status',
		'cctor_pro_default_border_style',
		'coupon-pro-loop-per-page',
		'coupon-pro-loop-order',
		'coupon-pro-loop-columns',
		'coupon-dimension',
		'coupon-dimension-h',
		'cctor-print-size',
	);

	$fields = array();

	foreach ( $allowed as $key ) {
		if ( isset( $options[ $key ] ) && is_scalar( $options[ $key ] ) ) {
			$fields[ $key ] = (string) $options[ $key ];
		}
	}

	// Whether these are customised is the useful signal; their contents are the
	// site owner's and must not leave the site.
	foreach ( array(
		'has_custom_css'          => 'cctor_custom_css',
		'has_custom_analytics'    => 'cctor_default_analytics',
		'has_custom_coupon_base'  => 'cctor_coupon_base',
	) as $reported => $source ) {
		$fields[ $reported ] = empty( $options[ $source ] ) ? '0' : '1';
	}

	$body['plugin_options_fields'] = $fields;

	return $body;
}

/**
 * Custom Deactivation Reasons
 *
 * Keyed format (telemetry-client 1.2.0): the array KEY is the stable wire
 * value and must match the server's canonical set (artifex-telemetry
 * includes/reason-keys.php) — never change a shipped key. Labels are
 * display-only and translatable. `followup` questions reveal when their
 * reason is checked and ship as the additive deactivation_followup field;
 * `links` are doc-deflection links shown the same way.
 */
add_filter( 'artifex_telemetry_form_text_coupon_creator', 'cctor_filter_deactivation_form' );
function cctor_filter_deactivation_form( $form ) {
	$form['heading'] = __( 'Sorry to see you go', 'coupon-creator' );

	$form['body'] = __( 'One thing worth knowing first: deactivating keeps all of your coupons and settings. Nothing is deleted — when your next promotion comes around, reactivate and everything will be right where you left it. Before you go, would you quickly give us your reason for deactivating?', 'coupon-creator' );

	$form['options'] = array(
		'could-not-create'       => __( 'Could not create a coupon', 'coupon-creator' ),
		'could-not-display'      => __( 'Could not display my coupons', 'coupon-creator' ),
		'affiliate-features'     => __( 'Looking for affiliate coupon features', 'coupon-creator' ),
		'getting-started'        => __( 'Could not find where to get started', 'coupon-creator' ),
		'needed-store-discounts' => __( 'I needed store discount codes (WooCommerce/EDD)', 'coupon-creator' ),
		'missing-features'       => __( 'Not the features I wanted', 'coupon-creator' ),
		'temporary'              => __( 'Only required temporarily', 'coupon-creator' ),
		'lack-documentation'     => __( 'Lack of technical documentation', 'coupon-creator' ),
		'better-plugin'          => __( 'Found a better plugin', 'coupon-creator' ),
	);

	$form['followup'] = array(
		'better-plugin'    => __( 'Which plugin did you choose?', 'coupon-creator' ),
		'missing-features' => __( 'What feature were you missing?', 'coupon-creator' ),
	);

	$form['links'] = array(
		'could-not-create' => array(
			'label' => __( 'See: creating your first coupon (2 min)', 'coupon-creator' ),
			'url'   => 'https://artifexrouting.com/docs/coupon-creator/',
		),
		'getting-started'  => array(
			'label' => __( 'See: Coupon Creator getting started guide', 'coupon-creator' ),
			'url'   => 'https://artifexrouting.com/docs/coupon-creator/',
		),
	);

	return $form;
}
