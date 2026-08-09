<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Central asset registry for Coupon Creator.
 *
 * Every compiled script/stylesheet is declared once in register_assets() via the
 * StellarWP Assets library. Sources live under src/ and are compiled by
 * @wordpress/scripts into build/{js,css}/ (each paired with a *.asset.php that
 * carries its dependency list + content-hash version). The library inserts the
 * js/ or css/ sub-directory by type, so assets are added by bare filename
 * (e.g. 'blocks.js' -> build/js/blocks.js). The library is configured in
 * Cctor__Coupon__Main::bootstrap() (set_relative_asset_path( 'build/' )).
 *
 * Enqueue timing preserves the pre-migration behavior:
 *  - Front-end coupon.css is registered only; Cctor__Coupon__Shortcode enqueues
 *    it by handle when a coupon actually renders.
 *  - Editor scripts/styles enqueue on enqueue_block_editor_assets.
 *  - Admin assets (plus the WP-core handles they rely on) enqueue on the coupon
 *    and options screens.
 */
class Cctor__Coupon__Assets {

	public function __construct() {

		// Register every asset with the library once, early.
		add_action( 'init', array( $this, 'register_assets' ), 5 );

		// Editor context.
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );

		// Inline custom CSS (front-end + editor) attaches to the coupon stylesheet.
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_style' ), 100 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'inline_style' ), 100 );

		// Admin context (folds in the former Cctor__Coupon__Admin__Assets).
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		}
	}

	/**
	 * Declare every Coupon Creator asset with the StellarWP Assets library.
	 *
	 * @since 3.6
	 */
	public function register_assets() {

		// Front-end coupon stylesheet — registered only; enqueued at render by the shortcode.
		\Pngx\Vendor\StellarWP\Assets\Asset::add( 'coupon_creator_css', 'coupon.css' )
			->use_asset_file()
			->register();

		// Block-editor stylesheets. Distinct slug for the blocks style — the library
		// keys assets by slug across both types, so it can't reuse the block script's handle.
		\Pngx\Vendor\StellarWP\Assets\Asset::add( 'cctor-coupon-editor-element', 'elements.css' )
			->use_asset_file()
			->register();

		\Pngx\Vendor\StellarWP\Assets\Asset::add( 'cctor-coupon-editor-blocks-style', 'blocks.css' )
			->use_asset_file()
			->register();

		// Block-editor scripts. wp-blocks/wp-components/wp-i18n/wp-element/react arrive via
		// the .asset.php sidecar; the globals the source reads without an ES import
		// (wp.blockEditor, wp.serverSideRender, wp.data, wp.api*) are declared by hand so
		// they load first.
		\Pngx\Vendor\StellarWP\Assets\Asset::add( 'cctor-coupon-editor-blocks', 'blocks.js' )
			->set_dependencies(
				'react', 'react-dom', 'wp-block-editor', 'wp-components', 'wp-editor',
				'wp-api', 'wp-api-request', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-server-side-render'
			)
			->register();

		\Pngx\Vendor\StellarWP\Assets\Asset::add( 'cctor-coupon-editor', 'editor.js' )
			->set_dependencies(
				'react', 'react-dom', 'wp-components', 'wp-editor',
				'wp-api', 'wp-api-request', 'wp-blocks', 'wp-i18n', 'wp-element'
			)
			->register();

		\Pngx\Vendor\StellarWP\Assets\Asset::add( 'cctor-coupon-editor-elements', 'elements.js' )
			->set_dependencies(
				'react', 'react-dom', 'wp-components', 'wp-editor',
				'wp-api', 'wp-api-request', 'wp-blocks', 'wp-i18n', 'wp-element'
			)
			->register();

		// Admin stylesheet + script — registered here, enqueued by enqueue_admin_assets().
		\Pngx\Vendor\StellarWP\Assets\Asset::add( 'coupon-admin-style', 'admin-style.css' )
			->use_asset_file()
			->set_dependencies( 'pngx-admin' )
			->register();

		// Artifex admin UI layer. afx-tokens holds the :root + .afx-theme-coupon-creator
		// custom properties; afx-components re-themes the header band, tabs, cards,
		// fields, buttons and pills, scoped under .afx-theme-coupon-creator so it can
		// never leak onto WP core or the engine's own options screen. Components load
		// AFTER coupon-admin-style so the scoped rules win by both specificity and order.
		\Pngx\Vendor\StellarWP\Assets\Asset::add( 'cctor-afx-tokens', 'afx-tokens.css' )
			->use_asset_file()
			->register();

		\Pngx\Vendor\StellarWP\Assets\Asset::add( 'cctor-afx-components', 'afx-components.css' )
			->use_asset_file()
			->set_dependencies( 'cctor-afx-tokens', 'coupon-admin-style' )
			->register();

		\Pngx\Vendor\StellarWP\Assets\Asset::add( 'cctor_admin_js', 'coupon-admin.js' )
			->set_dependencies( 'jquery', 'media-upload', 'thickbox', 'farbtastic', 'pngx-admin' )
			->register();
	}

	/**
	 * Enqueue block-editor styles + scripts.
	 *
	 * @since 3.0
	 */
	public function enqueue_editor_assets() {

		// Styles.
		wp_enqueue_style( 'cctor-coupon-editor-element' );
		wp_enqueue_style( 'coupon_creator_css' );
		wp_enqueue_style( 'cctor-coupon-editor-blocks-style' );

		// Scripts.
		wp_enqueue_script( 'cctor-coupon-editor-blocks' );

		$localized_data = array(
			'data'      => get_option( pngx( 'cctor' )->OPTIONS_ID ),
			'constants' => array(
				'hide_upgrade' => ( defined( 'CCTOR_HIDE_UPGRADE' ) && CCTOR_HIDE_UPGRADE ) ? 'true' : 'false',
			),
		);
		wp_localize_script( 'cctor-coupon-editor-blocks', 'pngx_blocks_editor_settings', $localized_data );

		wp_enqueue_script( 'cctor-coupon-editor' );
		wp_enqueue_script( 'cctor-coupon-editor-elements' );
	}

	/**
	 * Enqueue admin assets on the coupon + options screens, plus the WP-core
	 * handles they depend on. (Formerly Cctor__Coupon__Admin__Assets::load_assets.)
	 *
	 * @since 3.6
	 */
	public function enqueue_admin_assets() {

		$screen = get_current_screen();

		if ( ! isset( $screen->id ) ) {
			return;
		}

		if (
			'cctor_coupon' !== $screen->id &&
			'edit-cctor_coupon' !== $screen->id &&
			'settings_page_plugin-engine-options' !== $screen->id &&
			'cctor_coupon_page_coupon-options' !== $screen->id
		) {
			return;
		}

		// Our admin stylesheet (registered in register_assets()).
		wp_enqueue_style( 'coupon-admin-style' );

		// Artifex admin UI layer + the self-hosted Jost identity face. Scoped to
		// Coupon Creator's own screens: the coupon list, the coupon post-edit
		// screen, and our Options page — NOT the engine's shared
		// settings_page_plugin-engine-options screen, whose markup we don't own.
		if (
			'cctor_coupon' === $screen->id ||
			'edit-cctor_coupon' === $screen->id ||
			'cctor_coupon_page_coupon-options' === $screen->id
		) {
			wp_enqueue_style( 'cctor-afx-tokens' );
			wp_enqueue_style( 'cctor-afx-components' );
			$this->enqueue_brand_font();
		}

		// Style for WP Color Picker + Image Upload.
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'thickbox' );

		// jQuery UI style, served from the bundled copy.
		//
		// This used to prefer ajax.googleapis.com and fall back to the local file only when
		// detect_external_asset() said the CDN was unreachable. That check is @get_headers():
		// a blocking, uncached socket call on every one of these admin screens, made outside
		// the WP HTTP API, so it ignores proxy configuration and WP_HTTP_BLOCK_EXTERNAL and
		// hangs until default_socket_timeout whenever Google is slow or firewalled. A
		// third-party request from wp-admin is also a plugin-guideline and privacy problem,
		// and the CDN bought nothing: the same stylesheet has shipped in plugin-engine since
		// 2022.
		wp_enqueue_style( 'jquery-ui-style', Pngx__Main::instance()->resource_url . 'css/jquery-ui.min.css' );

		// Media Manager.
		wp_enqueue_media();

		// Core scripts.
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-dialog' );

		// Our admin script (registered in register_assets()).
		wp_enqueue_script( 'cctor_admin_js' );

		// Hook to Load New Styles and Scripts
		do_action( 'cctor_admin_assets' );
	}

	/**
	 * Load the Jost identity face on Coupon Creator's own admin screens only.
	 *
	 * Jost speaks only where the brand does — the header wordmark, postbox
	 * lockups, card labels — so it is admin-only and never enqueued on the front
	 * end. Self-hosted (bundled woff2, SIL OFL) rather than loaded from Google
	 * Fonts so customer admin pageviews never hit a third party. A single
	 * variable woff2 (weights 100–900) covers the 500/600/700 the UI uses. The
	 * @font-face rides as an inline style so no build-time url() resolution is
	 * needed; the file ships static under src/resources/fonts/.
	 *
	 * @since 3.6
	 */
	private function enqueue_brand_font() {

		$handle = 'cctor-afx-jost';

		if ( ! wp_style_is( $handle, 'registered' ) ) {
			wp_register_style( $handle, false, array(), Cctor__Coupon__Main::VERSION_NUM );
		}

		wp_enqueue_style( $handle );

		$font_url = pngx( 'cctor' )->resource_url . 'fonts/Jost-VariableFont_wght.woff2';

		$css = "@font-face{font-family:'Jost';font-style:normal;font-weight:100 900;font-display:swap;src:url('" . esc_url( $font_url ) . "') format('woff2');}";

		wp_add_inline_style( $handle, $css );
	}

	/*
	* Add Inline Style From Coupon Options
	*/
	public function inline_style() {

		$cctor_option_css = "";

		if ( has_filter( 'cctor_filter_inline_css' ) ) {
			$coupon_css = "";
			/**
			 * Filter Coupon Inline Styles
			 *
			 *
			 * @param string $coupon_css .
			 *
			 */
			$cctor_option_css = apply_filters( 'cctor_filter_inline_css', $coupon_css );
		}
		//Add Custom CSS from Options
		if ( cctor_options( 'cctor_custom_css' ) ) {

			$cctor_option_css .= cctor_options( 'cctor_custom_css' );
		}

		wp_add_inline_style( 'coupon_creator_css', wp_kses_post( $cctor_option_css ) );
	}

}
