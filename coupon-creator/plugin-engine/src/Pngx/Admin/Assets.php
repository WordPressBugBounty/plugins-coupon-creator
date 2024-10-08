<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Register Admin Assets for Plugin Engine
 *
 *
 */
class Pngx__Admin__Assets {

	/*
	* Register Assets
	*/
	public function register_assets() {

		// @formatter:off
		wp_register_style(
			'pngx-bootstrap-iconpicker',
			Pngx__Main::instance()->vendor_url . 'bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css',
			false,
			filemtime( Pngx__Main::instance()->vendor_path . 'bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css' )
		);
		wp_register_style(
			'pngx-font-awesome',
			Pngx__Main::instance()->vendor_url . 'bootstrap-iconpicker/icon-fonts/font-awesome-4.3.0/css/font-awesome.css',
			false,
			filemtime( Pngx__Main::instance()->vendor_path . 'bootstrap-iconpicker/icon-fonts/font-awesome-4.3.0/css/font-awesome.css' )
		);
		wp_register_style(
			'pngx-select2-css',
			Pngx__Main::instance()->vendor_url . 'tribe-selectWoo/dist/css/selectWoo.css',
			false,
			filemtime( Pngx__Main::instance()->vendor_path . 'tribe-selectWoo/dist/css/selectWoo.css' )
		);
		wp_register_style(
			'pngx-colorbox',
			Pngx__Main::instance()->vendor_url . 'colorbox/colorbox.css',
			false,
			filemtime( Pngx__Main::instance()->vendor_path . 'colorbox/colorbox.css' )
		);
		wp_register_style(
			'pngx-flatpickr',
			Pngx__Main::instance()->vendor_url . 'flatpickr/flatpickr.min.css',
			false,
			filemtime( Pngx__Main::instance()->vendor_path . 'flatpickr/flatpickr.min.css' )
		);
		wp_register_style(
			'pngx-admin',
			Pngx__Main::instance()->resource_url . 'css/pngx-admin.css',
			[
				'pngx-select2-css',
				'pngx-select2-css',
				'pngx-colorbox',
				'pngx-bootstrap-iconpicker',
				'pngx-font-awesome',
				'pngx-bootstrap-iconpicker',
				'pngx-flatpickr',
			],
			filemtime( Pngx__Main::instance()->resource_path . 'css/pngx-admin.css' )
		);

		wp_register_script(
			'pngx-clipboard',
			Pngx__Main::instance()->vendor_url . 'clipboard/clipboard.js',
			[],
			filemtime( Pngx__Main::instance()->vendor_path . 'clipboard/clipboard.js' ),
			true
		);
		wp_register_script(
			'pngx-bootstrap',
			Pngx__Main::instance()->vendor_url . 'bootstrap-iconpicker/bootstrap-3.2.0/js/bootstrap.min.js',
			[],
			filemtime( Pngx__Main::instance()->vendor_path . 'bootstrap-iconpicker/bootstrap-3.2.0/js/bootstrap.min.js' ),
			true
		);
		wp_register_script(
			'pngx-bootstrap-iconpicker-fontawesome',
			Pngx__Main::instance()->vendor_url . 'bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.3.0.min.js',
			[],
			filemtime( Pngx__Main::instance()->vendor_path . 'bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.3.0.min.js' ),
			true
		);
		wp_register_script(
			'pngx-bootstrap-iconpicker',
			Pngx__Main::instance()->vendor_url . 'bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js',
			[],
			filemtime( Pngx__Main::instance()->vendor_path . 'bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js' ),
			true
		);

		wp_register_script(
			'pngx-color-picker-alpha',
			Pngx__Main::instance()->vendor_url . 'wp-color-picker-alpha/wp-color-picker-alpha.min.js',
			[],
			filemtime( Pngx__Main::instance()->vendor_path . 'wp-color-picker-alpha/wp-color-picker-alpha.min.js' ),
			true
		);
		wp_register_script(
			'pngx-flatpickr',
			Pngx__Main::instance()->vendor_url . 'flatpickr/flatpickr.min.js',
			[],
			filemtime( Pngx__Main::instance()->vendor_path . 'flatpickr/flatpickr.min.js' ),
			true
		);
		wp_register_script(
			'pngx-bumpdown',
			Pngx__Main::instance()->resource_url . 'js/bumpdown.js',
			[],
			filemtime( Pngx__Main::instance()->resource_path . 'js/bumpdown.js' ),
			true
		);
		wp_register_script(
			'pngx-select2',
			Pngx__Main::instance()->vendor_url . 'tribe-selectWoo/dist/js/selectWoo.full.js',
			[ 'jquery' ],
			filemtime( Pngx__Main::instance()->vendor_path . 'tribe-selectWoo/dist/js/selectWoo.full.js' ),
			true
		);
		wp_register_script(
			'pngx-engine',
			Pngx__Main::instance()->resource_url . 'js/pngx-engine.js',
			[],
			filemtime( Pngx__Main::instance()->resource_path . 'js/pngx-engine.js' ),
			true
		);
		wp_register_script(
			'pngx-accordion',
			Pngx__Main::instance()->resource_url . 'js/accordion.js',
			[],
			filemtime( Pngx__Main::instance()->resource_path . 'js/accordion.js' ),
			true
		);
		wp_register_script(
			'pngx-dropdowns',
			Pngx__Main::instance()->resource_url . 'js/dropdowns.js',
			[
				'jquery',
				'underscore',
				'pngx-select2'
			],
			filemtime( Pngx__Main::instance()->resource_path . 'js/dropdowns.js' ),
			true
		);
		wp_register_script(
			'pngx-dependency',
			Pngx__Main::instance()->resource_url . 'js/dependency.js',
			[],
			filemtime( Pngx__Main::instance()->resource_path . 'js/dependency.js' ),
			true
		);
		wp_register_script(
			'pngx-tooltip',
			Pngx__Main::instance()->resource_url . 'js/tooltip.js',
			[],
			filemtime( Pngx__Main::instance()->resource_path . 'js/tooltip.js' ),
			true
		);
		wp_register_script(
			'pngx-wp-editor',
			Pngx__Main::instance()->resource_url . 'js/wp_editor.js',
			[ 'wp-tinymce' ],
			filemtime( Pngx__Main::instance()->resource_path . 'js/wp_editor.js' ),
			true
		);

		wp_register_script(
			'pngx-load-template-ajax',
			Pngx__Main::instance()->resource_url . 'js/templates.js',
			[],
			filemtime( Pngx__Main::instance()->resource_path . 'js/templates.js' ),
			true
		);
		wp_register_script(
			'pngx-colorbox',
			Pngx__Main::instance()->vendor_url . 'colorbox/jquery.colorbox-min.js',
			array( 'jquery' ),
			filemtime( Pngx__Main::instance()->vendor_path . 'colorbox/jquery.colorbox-min.js' ),
			true
		);
		wp_register_script(
			'pngx-access-profiles',
			Pngx__Main::instance()->resource_url . 'js/access-profiles.js',
			[ 'jquery', 'pngx-accordion', 'pngx-dropdowns' ],
			filemtime( Pngx__Main::instance()->resource_path . 'js/access-profiles.js' ),
			true
		);
		wp_register_script(
			'pngx-admin',
			Pngx__Main::instance()->resource_url . 'js/pngx-admin.js',
				[
					'jquery',
					'pngx-engine',
					'pngx-accordion',
					'pngx-bumpdown',
					'pngx-dropdowns',
					'pngx-tooltip',
					'pngx-clipboard',
					'pngx-colorbox',
					'pngx-wp-editor',
					'pngx-load-template-ajax',
					'pngx-color-picker-alpha',
					'pngx-bootstrap',
					'pngx-bootstrap-iconpicker-fontawesome',
					'pngx-bootstrap-iconpicker',
					'jquery-ui-tabs',
					'pngx-access-profiles',
					'pngx-dependency',
					'pngx-flatpickr',
				],
			filemtime( Pngx__Main::instance()->resource_path . 'js/pngx-admin.js' ),
			true
		);

		wp_localize_script( 'pngx-admin', 'pngx_admin', array(
			'ajaxurl'    => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
			'sysinfo_optin_nonce'   => wp_create_nonce( 'sysinfo_optin_nonce' ),
			'clipboard_btn_text'    => __( 'Copy to clipboard', 'plugin-engine' ),
			'clipboard_copied_text' => __( 'System info copied', 'plugin-engine' ),
			'clipboard_fail_text'   => __( 'Press "Cmd + C" to copy', 'plugin-engine' ),
		) );

		global $wp_version;

		/**
		 * Localize wp-color-picker & wpColorPickerL10n.
		 *
		 * This is only needed in WordPress version >= 5.5 because wpColorPickerL10n has been removed.
		 *
		 * @see https://github.com/WordPress/WordPress/commit/7e7b70cd1ae5772229abb769d0823411112c748b
		 *
		 * This is should be removed once the issue is fixed from wp-color-picker-alpha repo.
		 * @see https://github.com/kallookoo/wp-color-picker-alpha/issues/35
		 *
		 * @since 3.1
		 */
		if ( version_compare( $wp_version, '5.4.99', '>=' ) ) {

			wp_localize_script(
				'wp-color-picker',
				'wpColorPickerL10n',
				array(
					'clear'            => __( 'Clear' ),
					'clearAriaLabel'   => __( 'Clear color' ),
					'defaultString'    => __( 'Default' ),
					'defaultAriaLabel' => __( 'Select default color' ),
					'pick'             => __( 'Select Color' ),
					'defaultLabel'     => __( 'Color value' ),
				)
			);
		}
		// @formatter:on

		/**
		 * Hook to Register New Scripts or Styles for the Admin
		 */
		do_action( 'pngx_admin_scripts_styles' );

	}

	/*
		* Register Assets
		*/
	public function register_plugin_list_assets() {

		// @formatter:off
			wp_register_script(
				'pngx-license',
				Pngx__Main::instance()->resource_url . 'js/pngx-license.js',
				array( 'jquery-ui-tabs' ),
				filemtime( Pngx__Main::instance()->resource_path . 'js/pngx-license.js' ),
				true
			);

			wp_localize_script( 'pngx-license', 'pngx_license', array(
				'ajaxurl'   => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
				'nonce'     => wp_create_nonce( 'pngx_license_updates' ),
			) );
			// @formatter:on

		/**
		 * Hook to Register New Scripts or Styles for the Admin
		 */
		do_action( 'pngx_admin_scripts_styles' );

	}

	/*
	* Enqueue Plugin Engine Assets
	*/
	public function load_assets() {

	}

	/*
	* Detect if External Asset is Available
	*/
	public function detect_external_asset( $file ) {

		$file_headers = @get_headers( $file );
		if ( false === $file_headers || 'HTTP/1.0 404 Not Found' == $file_headers[0] ) {
			return false;
		}

		return true;

	}

}