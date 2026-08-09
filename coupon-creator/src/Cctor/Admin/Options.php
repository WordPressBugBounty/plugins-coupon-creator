<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

use Cctor\Coupon\Templates\Admin_Template;

/**
 * Class Cctor__Coupon__Admin__Options
 */
class Cctor__Coupon__Admin__Options Extends Pngx__Admin__Options {

	/*
	* Options Page Slug
	*/
	protected $options_slug = 'coupon-options';

	/*
	* Options ID
	*/
	protected $options_id = Cctor__Coupon__Main::OPTIONS_ID;

	/*
	* Field Prefix
	*/
	protected $field_prefix = 'cctor_';

	/**
	 * An instance of the admin template handler.
	 *
	 * @since 3.4.0
	 *
	 * @var Admin_Template
	 */
	protected $admin_template;

	/**
	 * Cctor__Coupon__Admin__Options constructor.
	 *
	 * @since 3.4.0 - Add Admin Template.
	 *
	 * @param Admin_Template $admin_template An instance of the admin template handler.
	 */
	public function __construct( Admin_Template $admin_template ) {
		$this->admin_template = $admin_template;
		$this->checkboxes = [];
		add_action( 'init', array( 'Pngx__Admin__Fields', 'flush_permalinks' ) );
	}

	/**
	 * Admin Init Options
	 */
	public function admin_init() {
		add_action( 'admin_init', array( $this, 'register_options' ), 15 );

		//Filter Options Field Name ID
		add_filter( 'pngx_options_name_id', array( $this, 'filter_options_field_id' ) );

		add_action( 'pngx_flush_permalinks', array( $this, 'flush_coupon_permalinks' ) );

		if ( ! get_option( $this->options_id ) ) {
			add_action( 'admin_init', array( &$this, 'set_defaults' ), 10 );
		}

		add_action( 'pngx_before_option_form', array( $this, 'display_options_header' ), 5 );
		add_action( 'pngx_after_option_form', array( $this, 'cctor_newsletter_signup' ) );

		//add license key for support
		add_filter( 'pngx-system-info-options-coupon', array( $this, 'add_options' ) );

		//add option fields
		add_filter( 'pngx-option-fields-coupon', array( $this, 'add_fields' ) );

		//add option fields
		add_filter( 'pngx-support-info-coupon', array( $this, 'add_system_items' ) );

		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
	}

	/*
	* Admin Options Page
	*/
	public function options_page() {
		$admin_page = add_submenu_page( 'edit.php?post_type=cctor_coupon', // parent_slug
			__( 'Coupon Creator Options', 'coupon-creator' ), // page_title
			__( 'Options', 'coupon-creator' ), // menu_title
			'manage_options', // capability
			$this->options_slug, // menu_slug
			array( $this, 'display_fields' ) // function
		);

		add_action( 'admin_print_scripts-' . $admin_page, pngx_callback( pngx( 'cctor.assets' ), 'enqueue_admin_assets' ) );

	}

	/*
	* Register Options
	*/
	public function register_options() {
		//Set options and sections here so they can be translated
		$this->fields = $this->get_option_fields();
		$this->set_sections();

		register_setting( $this->options_id, $this->options_id, array( $this, 'validate_options' ) );

		foreach ( $this->sections as $slug => $title ) {
			add_settings_section( $slug, $title, array( $this, 'display_section' ), $this->options_slug );
		}

		foreach ( $this->fields as $id => $option ) {
			$option['id'] = $id;
			$this->create_field( $option );
		}

	}

	/*
	* Option Tabs
	*/
	public function set_sections() {
		//Section Tab Headings
		$this->sections['defaults']   = __( 'Defaults', 'coupon-creator' );
		$this->sections['permalinks'] = __( 'Link Attributes / Permalinks', 'coupon-creator' );
		$this->sections['display']    = __( 'Display', 'coupon-creator' );
		! defined( 'CCTOR_HIDE_UPGRADE' ) || ! CCTOR_HIDE_UPGRADE ? $this->sections['templating'] = __( 'Templating', 'coupon-creator' ) : '';
		$this->sections['help']       = __( 'Help', 'coupon-creator' );
		$this->sections['license']    = __( 'Licenses', 'coupon-creator' );
		$this->sections['systeminfo'] = __( 'System Info', 'coupon-creator' );
		$this->sections['reset']      = __( 'Reset', 'coupon-creator' );

		unset( $this->sections['license'] );

		/**
		 * Filter Option Tabs
		 *
		 * @param array $sections an array of Option tab names and ids
		 *
		 */
		if ( has_filter( 'cctor_option_sections' ) ) {
			/**
			 * Filter the Coupon Creator Option Tab Header
			 *
			 * @param array $meta_tabs an array of tab headings.
			 *
			 */
			$this->sections = apply_filters( 'cctor_option_sections', $this->sections );
		}

	}

	/*
	* Options Header
	*/
	public function display_options_header( $slug ) {
		if ( $slug !== $this->options_slug ) {
			return;
		}

		$js_troubleshoot_url = cctor_guide_url( 'coupon-creator-troubleshooting-javascript-errors' );
		$docs_url            = cctor_guide_url( 'coupon-creator' );
		$mark_url            = pngx( 'cctor' )->resource_url . 'images/coupon-creator-mark.svg';

		// Consolidated version string for the header band. Core uses the constant
		// (always accurate); active tiers append their own via the filter — see
		// Pro's pro_version() / Add-ons' version(). Renders as
		// "v3.6.0 · Pro 3.6.0 · Add-ons 3.6.0".
		$version_parts = apply_filters( 'cctor_options_header_versions', array( 'v' . Cctor__Coupon__Main::VERSION_NUM ) );
		$version_line  = implode( ' · ', array_filter( (array) $version_parts ) );

		// Artifex header band (A1): plugin mark + Jost wordmark + endorsement,
		// version + Docs link right-aligned. Replaces the old 40px icon title.
		echo '<div class="afx-header">
			<div class="afx-header__brand">
				<img class="afx-header__mark" src="' . esc_url( $mark_url ) . '" alt="" />
				<span class="afx-header__names">
					<span class="afx-header__wordmark">' . esc_html__( 'Coupon Creator', 'coupon-creator' ) . '</span>
					<span class="afx-header__promise">' . esc_html__( 'Put a real coupon on your site.', 'coupon-creator' ) . '</span>
					<span class="afx-header__endorse">' . esc_html__( 'By Artifex Routing Co', 'coupon-creator' ) . '</span>
				</span>
			</div>
			<div class="afx-header__meta">';
		if ( $version_line ) {
			echo '<span class="afx-header__version">' . esc_html( $version_line ) . '</span>';
		}
		echo '<a class="afx-header__docs" href="' . esc_url( $docs_url ) . '" target="_blank" rel="noopener">' . esc_html__( 'Docs', 'coupon-creator' ) . ' &#8599;</a>
			</div>
		</div>

		<hr class="wp-header-end">

		<div class="javascript-conflict pngx-error"><p>' . wp_kses(
			sprintf(
				/* translators: %s: URL of the JavaScript troubleshooting guide. */
				__( 'There maybe a javascript conflict preventing some features from working.  <a href="%s" target="_blank" >Please check this guide to narrow down the cause.</a>', 'coupon-creator' ),
				esc_url( $js_troubleshoot_url )
			),
			// The link is part of the translated string, so this cannot be esc_html__(). A
			// translator supplying different markup gets it stripped rather than rendered.
			array(
				'a' => array(
					'href'   => array(),
					'target' => array(),
					'rel'    => array(),
				),
			)
		) . '</p></div>';

		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true ) {
			echo '<div class="updated fade"><p>' . esc_html__( 'Coupon Creator Options updated.', 'coupon-creator' ) . '</p></div>';
		}

		$this->admin_template->template( '/components/loader', [ 'loader_classes' => [ 'pngx-loader__dots' ] ] );
	}

	/*
	* Filter Options Field ID for Display of Fields
	*/
	public function filter_options_field_id( $id ) {
		$id = $this->options_id;

		return $id;
	}

	/*
	* Option Fields
	*/
	public function get_option_fields() {

		//defaults
		$fields['defaults_help']   = array(
			'section' => 'defaults',
			'type'    => 'help'
		);
		$fields['header_defaults'] = array(
			'section' => 'defaults',
			'title'   => '',
			'alert'   => __( '*These are defaults for new coupons only and do not change existing coupons.', 'coupon-creator' ),
			'type'    => 'heading'
		);

		//Template
		$fields['header_template'] = array(
			'section' => 'defaults',
			'title'   => '',
			'desc'    => __( 'Template', 'coupon-creator' ),
			'type'    => 'heading'
		);
		$template_options          = array(
			'ticket'  => __( 'Modern Ticket', 'coupon-creator' ),
			'default' => __( 'Classic', 'coupon-creator' ),
			'image'   => __( 'Image', 'coupon-creator' ),
		);
		if ( class_exists( 'Cctor__Coupon__Addons__Main' ) && 1 == cctor_options( 'cctor_advanced_templates', true, 1 ) ) {
			$template_options = array(
				'ticket'      => __( 'Modern Ticket', 'coupon-creator' ),
				'default'     => __( 'Classic', 'coupon-creator' ),
				'image'       => __( 'Image', 'coupon-creator' ),
				'modern'      => __( 'Modern', 'coupon-creator' ),
				'two-column'  => __( 'Two Columns', 'coupon-creator' ),
				'lower-third' => __( 'Lower Third', 'coupon-creator' ),
				'highlight'   => __( 'Highlight', 'coupon-creator' ),
			);
		}

		$fields['cctor_default_template'] = array(
			'section' => 'defaults',
			'title'   => __( 'Template Option', 'coupon-creator' ),
			'desc'    => __( 'Choose a default template for new coupons', 'coupon-creator' ),
			// New coupons default to the ticket. Existing coupons keep whatever
			// cctor_coupon_type they already have, so nothing on a live site changes.
			'std'     => 'ticket',
			'type'    => 'select',
			'choices' => $template_options,
		);

		//Expiration
		$fields['header_expiration'] = array(
			'section' => 'defaults',
			'title'   => '',
			'desc'    => __( 'Expiration', 'coupon-creator' ),
			'type'    => 'heading'
		);

		$fields['cctor-add-ons-expiration-display'] = array(
			'type'    => '',
			'section' => ''
		);

		$expiration_options = array(
			'1' => __( 'Ignore Expiration', 'coupon-creator' ),
			'2' => __( 'Expiration Date', 'coupon-creator' )
		);
		if ( class_exists( 'Cctor__Coupon__Pro__Main' ) ) {
			$expiration_options = array(
				'1' => __( 'Ignore Expiration', 'coupon-creator' ),
				'2' => __( 'Expiration Date', 'coupon-creator' ),
				'3' => __( 'Recurring Expiration', 'coupon-creator' ),
				'4' => __( 'Expires in X Days', 'coupon-creator' ),
				'5' => __( 'Range Expiration', 'coupon-creator' ),
			);
		}

		$fields['cctor_expiration_option'] = array(
			'section' => 'defaults',
			'title'   => __( 'Expiration Option', 'coupon-creator' ),
			'desc'    => __( 'Choose the expiration method for this coupon', 'coupon-creator' ),
			'std'     => '1',
			'type'    => 'select',
			'choices' => $expiration_options,
		);

		$fields['cctor_default_date_format']                  = array(
			'section' => 'defaults',
			'title'   => __( 'Date Format', 'coupon-creator' ),
			'desc'    => __( 'Select the Date Format to show for all Coupons*', 'coupon-creator' ),
			'type'    => 'select',
			'std'     => '0',
			'choices' => array(
				'0' => __( 'Month First - MM/DD/YYYY', 'coupon-creator' ),
				'1' => __( 'Day First - DD/MM/YYYY', 'coupon-creator' )
			)
		);
		$fields['cctor_pro_recurrence_pattern_default']       = array(
			'type'    => '',
			'section' => ''
		);
		$fields['cctor_pro_recurrence_pattern_limit_default'] = array(
			'type'    => '',
			'section' => ''
		);
		$fields['cctor_pro_x_days_default']                   = array(
			'type'    => '',
			'section' => ''
		);
		$fields['cctor_pro_status_heading']                   = array(
			'type'    => '',
			'section' => ''
		);
		$fields['cctor_pro_status']                           = array(
			'type'    => '',
			'section' => ''
		);

		//Outer Border
		$fields['cctor_pro_heading_outer_border'] = array(
			'type'    => '',
			'section' => ''
		);
		$fields['cctor_pro_default_border_style'] = array(
			'type'    => '',
			'section' => ''
		);
		$fields['cctor_outer_border_color']       = array(
			'type'    => '',
			'section' => ''
		);
		$fields['cctor_pro_outer_border_default'] = array(
			'type'    => '',
			'section' => ''
		);

		//Inner Border
		$fields['header_inner_border']            = array(
			'section' => 'defaults',
			'title'   => '',
			'desc'    => __( 'Inner Border', 'coupon-creator' ),
			'type'    => 'heading'
		);
		$fields['cctor_border_color']             = array(
			'title'   => __( 'Inside Border Color', 'coupon-creator' ),
			'desc'    => __( 'Choose default inside border color*', 'coupon-creator' ),
			'std'     => '#81d742',
			'type'    => 'color', // color
			'section' => 'defaults'
		);
		$fields['cctor_pro_inner_border_default'] = array(
			'type'    => '',
			'section' => ''
		);

		//Discount Field Colors
		$fields['header_discount']           = array(
			'section' => 'defaults',
			'title'   => '', // Not used for headings.
			'desc'    => __( 'Default Template', 'coupon-creator' ),
			'type'    => 'heading'
		);
		$fields['cctor_discount_bg_color']   = array(
			'title'   => __( 'Deal Background Color', 'coupon-creator' ),
			'desc'    => __( 'Choose default background color*', 'coupon-creator' ),
			'std'     => '#4377df',
			'type'    => 'color', // color
			'section' => 'defaults'
		);
		$fields['cctor_discount_text_color'] = array(
			'title'   => __( 'Deal Text Color', 'coupon-creator' ),
			'desc'    => __( 'Choose default text color*', 'coupon-creator' ),
			'std'     => '#000000',
			'type'    => 'color', // color
			'section' => 'defaults'
		);
		$fields['cctor_terms_text_color']    = array(
			'type'    => '',
			'section' => ''
		);


		if ( ! defined( 'CCTOR_HIDE_UPGRADE' ) || ! CCTOR_HIDE_UPGRADE ) {
			$fields['pro_feature_defaults_heading'] = array(
				'section' => 'defaults',
				'title'   => '', // Not used for headings.
				'desc'    => __( 'Pro Default Features', 'coupon-creator' ),
				'type'    => 'pro_heading'
			);
			$fields['pro_feature_defaults']         = array(
				'section' => 'defaults',
				'title'   => '',
				'desc'    => '',
				'type'    => 'list',
				'std'     => '',
				'choices' => array(
					'0' => __( 'Save time by setting default options for the expiration fields for all new coupons' ),
					'1' => __( 'Remove your coupons from the front end by having them set to draft after expired' ),
					'2' => __( 'Create a standard look with default styling fields such as color, radius, border type, and background fields' ),
				)
			);
			$fields['pro_feature_defaults_link']    = array(
				'section' => 'defaults',
				'title'   => '', // Not used for headings.
				'desc'    => __( 'Pro Link', 'coupon-creator' ),
				'type'    => 'pro_link'
			);
		}

		//LinkAttributes - Permalinks
		$fields['permalinks_help']               = array(
			'section' => 'permalinks',
			'type'    => 'help'
		);
		$fields['no_follow_heading']             = array(
			'section' => 'permalinks',
			'title'   => '', // Not used for headings.
			'desc'    => __( 'Link Attribute Options', 'coupon-creator' ),
			'type'    => 'heading'
		);
		$fields['cctor_nofollow_print_link']     = array(
			'section' => 'permalinks',
			'title'   => __( 'Print View Links', 'coupon-creator' ),
			'desc'    => __( 'Add nofollow to all the "Click to Open in Print View" links', 'coupon-creator' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$fields['cctor_hide_print_link']         = array(
			'section' => 'permalinks',
			'title'   => __( 'Disable Print View', 'coupon-creator' ),
			'desc'    => __( 'Check to turn off printing everywhere: the "Click to Open in Print View" links under coupons, custom links, and the Pro popup. Visitors will see coupons but will not be able to open or print them.', 'coupon-creator' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$fields['cctor_nofollow_print_template'] = array(
			'section' => 'permalinks',
			'title'   => __( 'Print Template No Follow', 'coupon-creator' ),
			'desc'    => __( 'Add nofollow and noindex to the print template', 'coupon-creator' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$fields['header_permalink']              = array(
			'section' => 'permalinks',
			'title'   => '', // Not used for headings.
			'desc'    => __( 'Permalink Options', 'coupon-creator' ),
			'type'    => 'heading'
		);
		$fields['cctor_coupon_base']             = array(
			'title'   => __( 'Coupon Print Template Slug', 'coupon-creator' ),
			'desc'    => __( 'default: cctor_coupon', 'coupon-creator' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'permalinks',
			'class'   => 'permalink'
		);
		$fields['cctor_coupon_category_base']    = array(
			'type'    => '',
			'section' => '',
			'class'   => ''
		);
		$fields['cctor_print_page_path']    = array(
			'type'    => '',
			'section' => '',
			'class'   => ''
		);
		if ( ! defined( 'CCTOR_HIDE_UPGRADE' ) || ! CCTOR_HIDE_UPGRADE ) {
			$fields['pro_feature_permalink_heading'] = array(
				'section' => 'permalinks',
				'title'   => '', // Not used for headings.
				'desc'    => __( 'Pro Default Features', 'coupon-creator' ),
				'type'    => 'pro_heading'
			);
			$fields['pro_feature_permalink']         = array(
				'section' => 'permalinks',
				'title'   => '',
				'desc'    => '',
				'type'    => 'list',
				'std'     => '',
				'choices' => array(
					'0' => __( 'Use Google Analytics to Track Print Views' ),
					'1' => __( 'Choose all new coupons to start as Pop Up Coupons or hide the "Click to Open in Print View" links' ),
				)
			);
			$fields['pro_feature_permalink_link']    = array(
				'section' => 'permalinks',
				'title'   => '', // Not used for headings.
				'desc'    => __( 'Pro Link', 'coupon-creator' ),
				'type'    => 'pro_link'
			);
		}

		//Display
		$fields['display_help'] = array(
			'section' => 'display',
			'type'    => 'help'
		);

		//Custom CSS
		$fields['cctor_custom_css'] = array(
			'title'   => __( 'Custom Coupon Styles', 'coupon-creator' ),
			'desc'    => sprintf(
				/* translators: 1: opening link tag to the customizing guide, 2: closing link tag. */
				__( 'Enter any custom CSS here to apply to the coupons for the shortcode and the print template (without &#60;style&#62; tags). %1$sSee the customizing guide%2$s.', 'coupon-creator' ),
				'<a href="' . esc_url( cctor_guide_url( 'coupon-creator-pro-customizing-coupons' ) ) . '" target="_blank" rel="noopener">',
				'</a>'
			),
			'std'     => 'e.g. .cctor_coupon_container { width: 000px; }',
			'type'    => 'textarea',
			'section' => 'display',
			'class'   => 'code'
		);
		//wpautop
		$fields['cctor_wpautop'] = array(
			'section' => 'display',
			'title'   => __( 'Auto P Filter', 'coupon-creator' ),
			'desc'    => __( 'Check to stop WordPress from adding automatic paragraph tags (<a href="http://codex.wordpress.org/Function_Reference/wpautop" target="_blank">wpautop</a>) to the Coupon Terms field. Uncheck if you want your terms text to keep automatic paragraph spacing.', 'coupon-creator' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		//wpautop
		$fields['cctor_print_base_css'] = array(
			'section' => 'display',
			'title'   => __( 'Print View Base CSS', 'coupon-creator' ),
			'desc'    => __( 'Check to turn off the built-in typography styles on the print view. Leave unchecked to keep the readable default print styling.', 'coupon-creator' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);

		//Search
		$fields['search_heading'] = array(
			'section' => 'display',
			'title'   => '',
			'desc'    => __( 'WordPress Search', 'coupon-creator' ),
			'type'    => 'heading'
		);
		$fields['coupon-search']  = array(
			'section' => 'display',
			'title'   => __( 'Coupon Search', 'coupon-creator' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => '',
			'desc'    => __( 'Check to include coupons in WordPress search results. By default, Coupon Creator keeps coupons out of site searches.', 'coupon-creator' )
		);
		if ( ! defined( 'CCTOR_HIDE_UPGRADE' ) || ! CCTOR_HIDE_UPGRADE ) {
			$fields['pro_feature_display_heading'] = array(
				'section' => 'display',
				'title'   => '', // Not used for headings.
				'desc'    => __( 'Pro Default Features', 'coupon-creator' ),
				'type'    => 'pro_heading'
			);
			$fields['pro_feature_display']         = array(
				'section' => 'display',
				'title'   => '',
				'desc'    => '',
				'type'    => 'list',
				'std'     => '',
				'choices' => array(
					'0' => __( 'Customize "Expires on:", "Click to Open in Print View", Valid thru, and "Print the Coupon" for all Coupons' ),
					'1' => __( 'Change default font and font weights for the Print Template' ),
				)
			);
			$fields['pro_feature_display_link']    = array(
				'section' => 'display',
				'title'   => '', // Not used for headings.
				'desc'    => __( 'Pro Link', 'coupon-creator' ),
				'type'    => 'pro_link'
			);
		}

		//Pro Template Tab UpSell
		if ( ! defined( 'CCTOR_HIDE_UPGRADE' ) || ! CCTOR_HIDE_UPGRADE ) {
			$fields['pro_feature_templating_heading'] = array(
				'section' => 'templating',
				'title'   => '', // Not used for headings.
				'desc'    => __( 'Pro Default Features', 'coupon-creator' ),
				'type'    => 'pro_heading'
			);
			$fields['pro_feature_templating']         = array(
				'section' => 'templating',
				'title'   => '',
				'desc'    => '',
				'type'    => 'list',
				'std'     => '',
				'choices' => array(
					'0' => __( 'Set a custom size for both views of the coupon for coupons and the image coupon' ),
					'1' => __( 'With the Pro &#91;couponloop&#93; shortcode change default settings such as per page, order, and columns' ),
					'2' => __( 'Customize to your theme the responsive breakpoints for the &#91;couponloop&#93; shortcode' ),
					'3' => __( 'Easily build all the attributes of the &#91;couponloop&#93; shortcode and insert it into content using the Pro inserter' ),
				)
			);
			$fields['pro_feature_templating_link']    = array(
				'section' => 'templating',
				'title'   => '', // Not used for headings.
				'desc'    => __( 'Pro Link', 'coupon-creator' ),
				'type'    => 'pro_link'
			);
		}

		//Help
		$fields['cctor_all_help'] = array(
			'section' => 'help',
			'title'   => __( 'Support: ', 'coupon-creator' ),
			'type'    => 'help',
			'std'     => 0,
			'desc'    => ''
		);

		$fields['systeminfo_heading'] = array(
			'section' => 'systeminfo',
			'title'   => '', // Not used for headings.
			'desc'    => __( 'System Info', 'coupon-creator' ),
			'type'    => 'heading'
		);

		$fields['systeminfo'] = [
			'section'   => 'systeminfo',
			'type'      => 'systeminfo',
			'plugin_id' => '-coupon',
		];

		$fields['reset_heading'] = array(
			'section' => 'reset',
			'title'   => '', // Not used for headings.
			'desc'    => __( 'Coupon Creator Option Reset', 'coupon-creator' ),
			'type'    => 'heading'
		);

		$fields['license_help'] = array(
			'section' => 'license',
			'type'    => 'help'
		);

		//Reset
		$fields['reset_theme'] = array(
			'section' => 'reset',
			'title'   => __( 'Reset', 'coupon-creator' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset all coupon creator options to their defaults. This does not change any existing coupon settings or remove your licenses.', 'coupon-creator' )
		);

		//Filter Option Fields
		if ( has_filter( 'cctor_option_filter' ) ) {
			/**
			 * Filter the options fields from Coupon Creator
			 *
			 *
			 * @param array $this ->options an array of fields to display in option tabs.
			 *
			 */
			$fields = apply_filters( 'cctor_option_filter', $fields );
		}

		return $fields;
	}

	/*
	* Coupon Creator Display Review Promo
	*
	* One promo card per screen, ever (Artifex A4): the Rate-It card only. The
	* former MailChimp "Sign Me Up" box is retired — the newsletter lives in the
	* docs footer now.
	*/
	public function cctor_newsletter_signup( $slug ) {

		if ( 'coupon-options' == $slug ) {

			echo '<div class="afx-promo">
					<span class="afx-promo__label">' . esc_html__( 'Enjoying Coupon Creator?', 'coupon-creator' ) . '</span>
					<span class="afx-promo__body">' . wp_kses_post( __( 'Every time you rate <strong>5 stars</strong>, it shows your support and helps other independents find the Coupon Creator.', 'coupon-creator' ) ) . '</span>
					<p><a href="https://wordpress.org/support/view/plugin-reviews/coupon-creator?filter=5" target="_blank" rel="noopener" class="afx-btn afx-btn--secondary">' . esc_html__( 'Rate it', 'coupon-creator' ) . ' &#9733;&#9733;&#9733;&#9733;&#9733;</a></p>
				</div>';
		}
	}

	/*
	* Flush Permalink on Permalink Field Change
	*
	*/
	public function flush_coupon_permalinks() {
		//setup coupon cpt when flushing permalinks
		pngx( 'cctor' )->register_post_types();
	}

	/**
	 * Add Coupon Options to System Info
	 *
	 * @param $keys
	 *
	 * @return mixed
	 */
	public function add_options( $options ) {

		$options[ Cctor__Coupon__Main::PLUGIN_NAME ] = get_option( Cctor__Coupon__Main::OPTIONS_ID );

		return $options;
	}

	/**
	 * Add Coupon Option Fields for System Info
	 *
	 * @param $keys
	 *
	 * @return mixed
	 */
	public function add_fields() {

		$fields = $this->get_option_fields();

		return $fields;
	}

	/**
	 * Add Coupon Option Fields to System Info
	 *
	 * @param $keys
	 *
	 * @return mixed
	 */
	public function add_system_items( $systeminfo ) {
		$post_type = Cctor__Coupon__Main::POSTTYPE;
		$systeminfo['Coupon Creator License Keys'] = Pngx__Admin__Support::getInstance()->get_key( '-coupon' );
		$systeminfo['Coupon Creator Options']      = Pngx__Admin__Support::getInstance()->get_plugin_settings( '-coupon' );

		$options = [
			'Coupon Post Type Capabilities'   => $post_type . '_capabilities_register',
			'Coupon Updated Version'          => 'coupon_update_version',
			'Coupon Update Ignore Field'      => 'coupon_update_ignore_expiration',
			'Coupon Update Image Border Meta' => 'coupon_update_image_border_meta',
			'Coupon Update Expiration Type'   => 'coupon_update_expiration_type',
			'Schema Version'                  => 'cctor_addons_schema_version',
			'DB Version'                      => 'cctor_addons_db_version',
			'Missing Custom Tables'           => 'cctor_addons_database_missing_tables',
			'Permalinks Flushed'              => 'pngx_permalink_flush',
		];

		$coupon_system_info = [];

		foreach ( $options as $k => $v ) {
			if ( $option_val = get_option( $v ) ) {
				$coupon_system_info[ $k ] = esc_attr( $option_val );
			}
		}

		$systeminfo = array_merge( $systeminfo, $coupon_system_info );

		return $systeminfo;
	}

	/**
	 * Add Plugin Engine Body Class
	 *
	 * @param string $classes A string of body classes.
	 *
	 * @return string A string of body classes.
	 */
	public function add_body_class( $classes ) {
		$screen = get_current_screen();
		if ( ! isset( $screen->id ) ) {
			return $classes;
		}

		if (
			'settings_page_plugin-engine-options' !== $screen->id &&
			'cctor_coupon_page_coupon-options' !== $screen->id
		) {
			return $classes;
		}

		$classes .= ' pngx-admin-body';

		// Scope the Artifex admin UI layer to our own Options page only. The
		// engine's shared settings_page_plugin-engine-options screen is left
		// unthemed — we don't own its markup.
		if ( 'cctor_coupon_page_coupon-options' === $screen->id ) {
			$classes .= ' afx-theme-coupon-creator';
		}

		return $classes;
	}
}
