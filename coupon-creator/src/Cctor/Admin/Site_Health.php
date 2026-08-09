<?php
/**
 * Coupon Creator family details in Tools > Site Health > Info.
 *
 * @package Coupon_Creator
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Reports what support needs to answer "why isn't this working" without a database dump.
 *
 * The questions this exists to answer, in the order they usually get asked: which versions of
 * the three plugins are installed, whether core switched one of them off for being out of date,
 * whether the licences are valid, and when WordPress last managed to check for updates. Every
 * one of those had to be reconstructed by hand the last time a site stopped seeing updates.
 *
 * Read-only. No options are written and nothing here changes plugin behaviour.
 *
 * @since 3.6.2
 */
class Cctor__Coupon__Admin__Site_Health {

	/**
	 * The paid siblings, as main class => [ plugin basename, display name ].
	 *
	 * Keyed by main class to match Cctor__Coupon__Main::gate_outdated_extensions().
	 */
	const EXTENSIONS = array(
		'Cctor__Coupon__Pro__Main'    => array(
			'file'   => 'coupon-creator-pro/coupon-creator-pro.php',
			'name'   => 'Coupon Creator Pro',
			'sdk'    => 'coupon-creator-pro_license',
			'legacy' => 'cctor_pro_license',
		),
		'Cctor__Coupon__Addons__Main' => array(
			'file'   => 'coupon-creator-add-ons/coupon-creator-add-ons.php',
			'name'   => 'Coupon Creator Add-ons',
			'sdk'    => 'coupon-creator-add-ons_license',
			'legacy' => 'cctor_addons_license',
		),
	);

	/**
	 * Register hooks.
	 */
	public function hook() {
		add_filter( 'debug_information', array( $this, 'add_debug_information' ) );
	}

	/**
	 * Add the Coupon Creator section to Site Health's Info tab.
	 *
	 * @param array<string, mixed> $info Existing debug sections.
	 *
	 * @return array<string, mixed>
	 */
	public function add_debug_information( $info ) {

		$fields = array(
			'cctor_version' => array(
				'label' => __( 'Coupon Creator version', 'coupon-creator' ),
				'value' => Cctor__Coupon__Main::VERSION_NUM,
			),
			'pngx_version'  => array(
				'label' => __( 'Plugin Engine version', 'coupon-creator' ),
				'value' => class_exists( 'Pngx__Main' ) ? Pngx__Main::VERSION : __( 'not loaded', 'coupon-creator' ),
			),
		);

		$minimums = array(
			'Cctor__Coupon__Pro__Main'    => Cctor__Coupon__Main::MIN_PRO_VERSION,
			'Cctor__Coupon__Addons__Main' => Cctor__Coupon__Main::MIN_ADDONS_VERSION,
		);

		foreach ( self::EXTENSIONS as $main_class => $extension ) {

			$version = $this->get_installed_version( $extension['file'] );

			if ( null === $version ) {
				$fields[ $extension['file'] ] = array(
					'label' => $extension['name'],
					'value' => __( 'not installed', 'coupon-creator' ),
				);

				continue;
			}

			// Whether core switched it off, and why, is the whole point of this section.
			$minimum = isset( $minimums[ $main_class ] ) ? $minimums[ $main_class ] : '';
			$gated   = $minimum && version_compare( $version, $minimum, '<' );

			$value = $version;

			if ( $gated ) {
				$value .= ' — ' . sprintf(
					/* translators: %s: the minimum version this core requires. */
					__( 'switched off, needs %s or later', 'coupon-creator' ),
					$minimum
				);
			}

			$value .= ' — ' . sprintf(
				/* translators: %s: licence status, e.g. valid or expired. */
				__( 'licence: %s', 'coupon-creator' ),
				$this->get_license_status( $extension )
			);

			$fields[ $extension['file'] ] = array(
				'label' => $extension['name'],
				'value' => $value,
			);
		}

		$fields['cctor_store'] = array(
			'label' => __( 'Licensing and update store', 'coupon-creator' ),
			'value' => Cctor__Coupon__Main::instance()->get_shop_url(),
		);

		$fields['cctor_last_checked'] = array(
			'label' => __( 'WordPress last checked for plugin updates', 'coupon-creator' ),
			'value' => $this->get_last_checked(),
		);

		$info['coupon-creator'] = array(
			'label'       => __( 'Coupon Creator', 'coupon-creator' ),
			'description' => __( 'Versions, licence status, and update checks for Coupon Creator and its extensions. Paste this section into a support request.', 'coupon-creator' ),
			'fields'      => $fields,
		);

		return $info;
	}

	/**
	 * Installed version from a sibling's plugin header.
	 *
	 * Read from disk rather than the dependency registry: a switched-off extension never
	 * registers, which is exactly the case this section has to describe.
	 *
	 * @param string $plugin_file Plugin basename.
	 *
	 * @return string|null Null when the plugin is not installed.
	 */
	protected function get_installed_version( $plugin_file ) {

		$path = Cctor__Coupon__Main::instance()->plugins_path . $plugin_file;

		if ( ! file_exists( $path ) ) {
			return null;
		}

		$headers = get_file_data( $path, array( 'Version' => 'Version' ) );

		return empty( $headers['Version'] ) ? __( 'unknown', 'coupon-creator' ) : $headers['Version'];
	}

	/**
	 * Licence status for an extension, never the key itself.
	 *
	 * Two storage shapes are in play: the EDD SL SDK writes a status string to its own option
	 * from 3.6.0, and everything before that kept an array under the legacy option. Sites mid
	 * upgrade have one, the other, or both.
	 *
	 * @param array<string, string> $extension Entry from self::EXTENSIONS.
	 *
	 * @return string
	 */
	protected function get_license_status( $extension ) {

		$sdk = get_option( $extension['sdk'] );

		if ( ! empty( $sdk ) && is_string( $sdk ) ) {
			return $sdk;
		}

		if ( is_array( $sdk ) && ! empty( $sdk['license'] ) ) {
			return $sdk['license'];
		}

		$legacy = get_option( $extension['legacy'] );

		if ( is_array( $legacy ) && ! empty( $legacy['status'] ) ) {
			return $legacy['status'];
		}

		return __( 'none stored', 'coupon-creator' );
	}

	/**
	 * When WordPress last ran a plugin update check.
	 *
	 * @return string
	 */
	protected function get_last_checked() {

		$transient = get_site_transient( 'update_plugins' );

		if ( empty( $transient->last_checked ) ) {
			return __( 'never', 'coupon-creator' );
		}

		return sprintf(
			/* translators: %s: human-readable time difference, e.g. "2 hours". */
			__( '%s ago', 'coupon-creator' ),
			human_time_diff( $transient->last_checked )
		);
	}
}
