<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

use Pngx\Vendor\Artifex\License\Control;

/**
 * Surfaces the EDD SL SDK "Manage License" control on the Coupon Creator options
 * page (Licenses tab), in addition to the SDK's own Plugins-screen modal.
 *
 * The rendering + modal bootstrap live in the shared artifexrouting/edd-license-ui
 * library (Strauss-prefixed here as Pngx\Vendor\Artifex\License\Control); this
 * class is just the Coupon-Creator-specific wiring: the options-page sections/
 * fields and the `cctor_sdk_licenses` filter that paid plugins (Pro, Add-ons)
 * register into.
 *
 * @since 3.6
 */
class Cctor__Coupon__Admin__SDK_License_UI {

	/**
	 * Hook suffix / screen id of the coupon options page.
	 */
	const SCREEN_ID = 'cctor_coupon_page_coupon-options';

	/**
	 * Register all hooks. Safe to call once from the admin bootstrap.
	 */
	public static function register() {
		add_filter( 'cctor_option_sections', array( __CLASS__, 'add_section' ), 60, 1 );
		add_filter( 'cctor_option_filter', array( __CLASS__, 'add_field' ), 20, 1 );
		add_filter( 'pngx_field_types', array( __CLASS__, 'display_field' ), 20, 5 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'admin_footer', array( __CLASS__, 'render_overlay' ) );
	}

	/**
	 * Paid-plugin license descriptors, each:
	 * [ 'item_id' => int, 'slug' => string, 'name' => string, 'status_option' => string ].
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_licenses() {
		return (array) apply_filters( 'cctor_sdk_licenses', array() );
	}

	/**
	 * Whether the SDK library is available and at least one paid plugin registered.
	 *
	 * @return bool
	 */
	public static function is_available() {
		return class_exists( Control::class )
			&& Control::is_available()
			&& ! empty( self::get_licenses() );
	}

	/**
	 * Ensure the Licenses tab exists when there is something to show.
	 *
	 * @param array<string, string> $sections
	 * @return array<string, string>
	 */
	public static function add_section( $sections ) {
		if ( self::is_available() && empty( $sections['license'] ) ) {
			$sections['license'] = __( 'Licenses', 'coupon-creator' );
		}

		return $sections;
	}

	/**
	 * Add the license-control field to the Licenses section.
	 *
	 * @param array<string, mixed> $options
	 * @return array<string, mixed>
	 */
	public static function add_field( $options ) {
		if ( self::is_available() ) {
			$options['cctor_sdk_licenses'] = array(
				'type'    => 'cctor_sdk_licenses',
				'section' => 'license',
				'title'   => __( 'Licenses', 'coupon-creator' ),
				'desc'    => '',
			);
		}

		return $options;
	}

	/**
	 * Render the custom field type through the plugin-engine field dispatcher.
	 *
	 * @param array<string, mixed> $field
	 * @return array<string, mixed>
	 */
	public static function display_field( $field, $options = array(), $options_id = null, $meta = null, $repeat = null ) {
		if ( isset( $field['type'] ) && 'cctor_sdk_licenses' === $field['type'] ) {
			echo self::render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the library.
		}

		return $field;
	}

	/**
	 * The license rows (status + Manage License button per plugin), rendered by
	 * the shared library with Coupon Creator's translated labels.
	 *
	 * @return string
	 */
	public static function render() {
		if ( ! class_exists( Control::class ) ) {
			return '';
		}

		$licenses = array();
		foreach ( self::get_licenses() as $license ) {
			$license['label']         = __( 'Manage License', 'coupon-creator' );
			$license['status_labels'] = array(
				'active'   => __( 'Active', 'coupon-creator' ),
				'inactive' => __( 'Not activated', 'coupon-creator' ),
				/* translators: %s: license expiration date. */
				'expires'  => __( '(expires %s)', 'coupon-creator' ),
				'lifetime' => __( '(lifetime)', 'coupon-creator' ),
			);
			$licenses[] = $license;
		}

		return Control::rows( $licenses );
	}

	/**
	 * Bootstrap the SDK modal assets on the coupon options screen.
	 *
	 * @param string $hook
	 */
	public static function enqueue_assets( $hook ) {
		if ( self::SCREEN_ID === $hook && self::is_available() ) {
			Control::enqueue(
				array(
					'activating'   => __( 'Activating…', 'coupon-creator' ),
					'deactivating' => __( 'Deactivating…', 'coupon-creator' ),
					'error'        => __( 'An error occurred, please try again.', 'coupon-creator' ),
				)
			);
		}
	}

	/**
	 * Output the modal overlay in the options-screen footer.
	 */
	public static function render_overlay() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && self::SCREEN_ID === $screen->id && self::is_available() ) {
			Control::overlay();
		}
	}
}
