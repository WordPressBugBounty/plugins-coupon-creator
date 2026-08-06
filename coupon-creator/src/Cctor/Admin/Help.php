<?php

/*
* Admin Help Class
*
*/
class Cctor__Coupon__Admin__Help extends Pngx__Admin__Help {

	//Help Fields array()
	protected $fields = array();

	/**
	 * Set Fields on Class Initialize
	 */
	public function __construct() {
		$this->set_help_fields();
	}

	/**
	 * Array of All Help Fields
	 *
	 * Every entry is a plain guide link (the archived help videos were retired
	 * in 3.6 — the text guides at artifexrouting.com/docs are the maintained
	 * reference), so the engine renders a single "Guides" list per help panel.
	 */
	protected function set_help_fields() {

		//Content
		$this->fields['header_video_guides_content']  = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'Coupon Content',
			'type'    => 'heading'
		);
		$this->fields['link_creating_templates']      = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'Standard and Default Templates',
			'link'    => cctor_guide_url( 'coupon-creator-creating-a-coupon' ),
			'type'    => 'links'
		);
		$this->fields['link_creating_image_coupon']   = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'Overview of Creating an Image Coupon',
			'link'    => cctor_guide_url( 'coupon-creator-creating-an-image-coupon' ),
			'type'    => 'links'
		);
		$this->fields['link_click_reveal']            = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'How to use the Click Reveal',
			'link'    => cctor_guide_url( 'coupon-creator-add-ons-click-reveal' ),
			'pro'     => 'Add-ons',
			'type'    => 'links'
		);
		$this->fields['link_dynamic_code']            = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'How to use the Dynamic Code',
			'link'    => cctor_guide_url( 'coupon-creator-add-ons-dynamic-code' ),
			'pro'     => 'Add-ons',
			'type'    => 'links'
		);
		$this->fields['link_expiration_display']      = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'How to use the Expiration Display',
			'link'    => cctor_guide_url( 'coupon-creator-add-ons-expiration-display' ),
			'pro'     => 'Add-ons',
			'type'    => 'links'
		);
		$this->fields['link_pro_columns_rows']        = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'Using Columns and Rows in the Visual Editor',
			'link'    => cctor_guide_url( 'coupon-creator-pro-creating-a-pro-coupon' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_view_shortcode']      = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'How to use the View Shortcodes and Deal Display Options',
			'link'    => cctor_guide_url( 'coupon-creator-pro-coupon-loop-filter-bar' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_inserting_coupon']        = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'Inserter and Aligning Coupons',
			'link'    => cctor_guide_url( 'coupon-creator-displaying-coupons' ),
			'type'    => 'links'
		);
		$this->fields['link_print_only']              = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'How to use the Print Only Field',
			'link'    => cctor_guide_url( 'coupon-creator-pro-print-features' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_multiprint_addons']       = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'Multiprint Feature',
			'link'    => cctor_guide_url( 'coupon-creator-add-ons-multiprint' ),
			'pro'     => 'Add-ons',
			'type'    => 'links'
		);
		$this->fields['link_pro_hide_deal']           = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'How to Hide the Deal in any Coupon View',
			'link'    => cctor_guide_url( 'coupon-creator-pro-pro-options' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_shortcode_placement'] = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'Where does the shortcode go?',
			'link'    => cctor_guide_url( 'coupon-creator-where-does-the-shortcode-go' ),
			'type'    => 'links'
		);
		$this->fields['link_pro_shortcode_sidebar']   = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'How can I display coupons using the shortcode in a sidebar text widget?',
			'link'    => cctor_guide_url( 'coupon-creator-shortcode-in-sidebar-widget' ),
			'type'    => 'links'
		);
		$this->fields['link_pro_image_size']          = array(
			'section' => '',
			'tab'     => 'content',
			'text'    => 'What is the size of the image coupon?',
			'link'    => cctor_guide_url( 'coupon-creator-image-coupon-size' ),
			'type'    => 'links'
		);
		$this->fields['video_end_list_content']       = array(
			'section' => '',
			'tab'     => 'content',
			'type'    => 'end_list'
		);

		//Style
		$this->fields['header_video_guides_style'] = array(
			'section' => '',
			'tab'     => 'style',
			'text'    => 'Style Guides',
			'type'    => 'heading'
		);
		$this->fields['link_pro_border_styles']    = array(
			'section' => '',
			'tab'     => 'style',
			'text'    => 'How to use different Coupon Borders',
			'link'    => cctor_guide_url( 'coupon-creator-pro-customizing-coupons' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_background_image'] = array(
			'section' => '',
			'tab'     => 'style',
			'text'    => 'Using the Background Image',
			'link'    => cctor_guide_url( 'coupon-creator-pro-customizing-coupons' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['video_end_list_style']      = array(
			'section' => '',
			'tab'     => 'style',
			'type'    => 'end_list'
		);

		//Expiration
		$this->fields['header_video_guides_expiration'] = array(
			'section' => '',
			'tab'     => 'expiration',
			'text'    => 'Expiration Options',
			'type'    => 'heading'
		);
		$this->fields['link_expiration_features']       = array(
			'section' => '',
			'tab'     => 'expiration',
			'text'    => 'How to use the Expiration and Counter Features',
			'link'    => cctor_guide_url( 'coupon-creator-expiration-dates' ),
			'type'    => 'links'
		);
		$this->fields['link_pro_recurring_expiration']  = array(
			'section' => '',
			'tab'     => 'expiration',
			'text'    => 'How to setup or troubleshoot the Recurring Expiration in Pro',
			'link'    => cctor_guide_url( 'coupon-creator-pro-expiration-recurring' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_counter']               = array(
			'section' => '',
			'tab'     => 'expiration',
			'text'    => 'Using the Counter',
			'link'    => cctor_guide_url( 'coupon-creator-pro-coupon-counter' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_expiration_bulk_edit']      = array(
			'section' => '',
			'tab'     => 'expiration',
			'text'    => 'How to Bulk or Quick Edit the Expiration or Counter Fields',
			'link'    => cctor_guide_url( 'coupon-creator-pro-coupon-counter' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['video_end_list_expiration']      = array(
			'section' => '',
			'tab'     => 'expiration',
			'type'    => 'end_list'
		);

		//Links
		$this->fields['header_video_guides_links'] = array(
			'section' => '',
			'tab'     => 'links',
			'text'    => 'Coupon Link Attributes',
			'type'    => 'heading'
		);
		$this->fields['link_pro_popup']            = array(
			'section' => '',
			'tab'     => 'links',
			'text'    => 'How to Open the Print Template in a Pop Up Box',
			'link'    => cctor_guide_url( 'coupon-creator-pro-print-features' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['video_end_list_links']      = array(
			'section' => '',
			'tab'     => 'links',
			'type'    => 'end_list'
		);


		//WooCommerce
		$this->fields['header_video_guides_woo'] = array(
			'section' => '',
			'tab'     => 'cctor_woocommerce',
			'text'    => 'WooCommerce Coupons',
			'type'    => 'heading'
		);
		$this->fields['link_woocommerce']        = array(
			'section' => '',
			'tab'     => 'cctor_woocommerce',
			'text'    => 'How to Create a WooCommerce Coupon',
			'link'    => cctor_guide_url( 'coupon-creator-add-ons-woocommerce-coupons' ),
			'pro'     => 'Add-ons',
			'type'    => 'links'
		);
		$this->fields['video_end_list_woo']      = array(
			'section' => '',
			'tab'     => 'cctor_woocommerce',
			'type'    => 'end_list'
		);

		//Option Defaults
		$this->fields['header_video_guides_defaults'] = array(
			'section' => 'defaults',
			'tab'     => '',
			'text'    => 'Coupon Defaults',
			'type'    => 'heading'
		);
		$this->fields['link_coupon_options_overview']  = array(
			'section' => 'defaults',
			'tab'     => '',
			'text'    => 'An Overview of Coupon Creator Options',
			'link'    => cctor_guide_url( 'coupon-creator-coupon-options' ),
			'type'    => 'links'
		);
		$this->fields['link_defaults_bulk_edit']       = array(
			'section' => 'defaults',
			'tab'     => '',
			'text'    => 'How to Bulk or Quick Edit the Expiration or Counter Fields',
			'link'    => cctor_guide_url( 'coupon-creator-pro-coupon-counter' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_option_defaults']          = array(
			'section' => 'defaults',
			'tab'     => '',
			'text'    => 'A Guide to the Default Options',
			'link'    => cctor_guide_url( 'coupon-creator-coupon-options' ),
			'type'    => 'links'
		);
		$this->fields['video_end_list_defaults']       = array(
			'section' => 'defaults',
			'tab'     => '',
			'type'    => 'end_list'
		);

		//Option Links
		$this->fields['header_video_guides_permalinks'] = array(
			'section' => 'permalinks',
			'tab'     => '',
			'text'    => 'Links Attributes / Permalinks Options',
			'type'    => 'heading'
		);
		$this->fields['link_link_options']              = array(
			'section' => 'permalinks',
			'tab'     => '',
			'text'    => 'An Overview of the Link Options',
			'link'    => cctor_guide_url( 'coupon-creator-change-print-view-permalink' ),
			'type'    => 'links'
		);
		$this->fields['link_pro_google']                = array(
			'section' => 'permalinks',
			'tab'     => '',
			'text'    => 'Setup Google Analytics for Print View',
			'link'    => cctor_guide_url( 'coupon-creator-google-analytics-print-view' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['video_end_list_permalinks']      = array(
			'section' => 'permalinks',
			'tab'     => '',
			'type'    => 'end_list'
		);

		//Option Display
		$this->fields['header_video_guides_display'] = array(
			'section' => 'display',
			'tab'     => '',
			'text'    => 'Display Options',
			'type'    => 'heading'
		);
		$this->fields['link_display_options']        = array(
			'section' => 'display',
			'tab'     => '',
			'text'    => 'An Overview of the Display Options',
			'link'    => cctor_guide_url( 'coupon-creator-displaying-coupons' ),
			'type'    => 'links'
		);
		$this->fields['link_pro_text_overrides']     = array(
			'section' => 'display',
			'tab'     => '',
			'text'    => 'Using the Text Overrides',
			'link'    => cctor_guide_url( 'coupon-creator-pro-pro-options' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_wpautop']            = array(
			'section' => 'display',
			'tab'     => '',
			'text'    => 'How to turn on wpautop in the Coupon Creator',
			'link'    => cctor_guide_url( 'coupon-creator-enable-wpautop' ),
			'type'    => 'links'
		);
		$this->fields['link_pro_search_results']     = array(
			'section' => 'display',
			'tab'     => '',
			'text'    => 'How can I prevent coupons from appearing in a site search?',
			'link'    => cctor_guide_url( 'coupon-creator-hide-coupons-from-search' ),
			'type'    => 'links'
		);
		$this->fields['link_pro_multiprint']         = array(
			'section' => 'display',
			'tab'     => '',
			'text'    => 'How to use the Multi-Print Feature',
			'link'    => cctor_guide_url( 'coupon-creator-add-ons-multiprint' ),
			'pro'     => 'Add-ons',
			'type'    => 'links'
		);
		$this->fields['video_end_list_display']      = array(
			'section' => 'display',
			'tab'     => '',
			'type'    => 'end_list'
		);

		//Option Templating
		$this->fields['header_video_guides_templating']    = array(
			'section' => 'templating',
			'tab'     => '',
			'text'    => 'Templating Options',
			'type'    => 'heading'
		);
		$this->fields['link_template_overview']            = array(
			'section' => 'templating',
			'tab'     => '',
			'text'    => 'An Overview of Template Options',
			'link'    => cctor_guide_url( 'coupon-creator-add-ons-advanced-templates' ),
			'pro'     => 'Add-ons',
			'type'    => 'links'
		);
		$this->fields['link_pro_dimension']                = array(
			'section' => 'templating',
			'tab'     => '',
			'text'    => 'Using the Dimension Options',
			'link'    => cctor_guide_url( 'coupon-creator-pro-customizing-coupons' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_shortcode_filter_options'] = array(
			'section' => 'templating',
			'tab'     => '',
			'text'    => 'Pro\'s couponloop shortcode, filter bar, and template system to manage coupons',
			'link'    => cctor_guide_url( 'coupon-creator-pro-coupon-loop-filter-bar' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_themers_guide']            = array(
			'section' => 'templating',
			'tab'     => '',
			'text'    => 'Pro\'s Themer\'s Guide',
			'link'    => cctor_guide_url( 'coupon-creator-themers-guide' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_category_template']        = array(
			'section' => 'templating',
			'tab'     => '',
			'text'    => 'Pro\'s Coupon Category Templates',
			'link'    => cctor_guide_url( 'coupon-creator-pro-customizing-coupons' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['video_end_list_templating']         = array(
			'section' => 'templating',
			'tab'     => '',
			'type'    => 'end_list'
		);

		//License
		$this->fields['header_video_guides_license'] = array(
			'section' => 'license',
			'tab'     => '',
			'text'    => 'License Options',
			'type'    => 'heading'
		);
		$this->fields['link_pro_license']            = array(
			'section' => 'license',
			'tab'     => '',
			'text'    => 'How to Activate Your License',
			'link'    => cctor_guide_url( 'coupon-creator-pro-activate-your-license' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_where_license']      = array(
			'section' => 'license',
			'tab'     => '',
			'text'    => 'Where is my license key in my account?',
			'link'    => cctor_guide_url( 'coupon-creator-pro-find-your-license-key' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_add_license']        = array(
			'section' => 'license',
			'tab'     => '',
			'text'    => 'Where do I add my license key?',
			'link'    => cctor_guide_url( 'coupon-creator-pro-add-your-license-key' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_upgrade_license']    = array(
			'section' => 'license',
			'tab'     => '',
			'text'    => 'How to Upgrade Your License',
			'link'    => cctor_guide_url( 'coupon-creator-pro-upgrade-your-license' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_renew_license']      = array(
			'section' => 'license',
			'tab'     => '',
			'text'    => 'How do I renew my license for the Coupon Creator Pro?',
			'link'    => cctor_guide_url( 'coupon-creator-pro-renew-your-license' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_transfer_license']   = array(
			'section' => 'license',
			'tab'     => '',
			'text'    => 'How do I transfer my license key to another site?',
			'link'    => cctor_guide_url( 'coupon-creator-pro-transfer-your-license' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['link_pro_manual_update']      = array(
			'section' => 'license',
			'tab'     => '',
			'text'    => 'How do I manually update or downgrade a plugin?',
			'link'    => cctor_guide_url( 'coupon-creator-pro-manual-update-downgrade' ),
			'pro'     => 'Pro',
			'type'    => 'links'
		);
		$this->fields['video_end_list_license']      = array(
			'section' => 'license',
			'tab'     => '',
			'type'    => 'end_list'
		);

		//Resources
		$this->fields['header_video_guides_resources'] = array(
			'section' => '',
			'tab'     => '',
			'text'    => 'Resources',
			'type'    => 'heading'
		);
		$this->fields['link_pro_themersguide_intro']   = array(
			'section' => '',
			'tab'     => '',
			'text'    => 'Intro to Pro\'s Themer\'s Guide',
			'link'    => cctor_guide_url( 'coupon-creator-themers-guide' ),
			'type'    => 'links'
		);
		$this->fields['video_pro_documentation']       = array(
			'section' => '',
			'tab'     => '',
			'text'    => 'Documentation - Overview of CSS Selectors, Actions, Filters, Capabilities, and Post Types',
			'link'    => cctor_guide_url( 'coupon-creator-actions-filters' ),
			'type'    => 'links'
		);
		$this->fields['video_faq']                     = array(
			'section' => '',
			'tab'     => '',
			'text'    => 'Frequently Asked Question - Pre Sales, License, Requirements, and Setup Information',
			'link'    => cctor_guide_url( 'coupon-creator-getting-started' ),
			'type'    => 'links'
		);
		$this->fields['video_pro_troubleshooting']     = array(
			'section' => '',
			'tab'     => '',
			'text'    => 'Guides - User Guides and Troubleshooting Guides',
			// The free conflicts guide publishes under the -plugin-conflicts slug
			// (unlike Pro/Add-ons, whose guides use -troubleshooting-conflicts).
			'link'    => cctor_guide_url( 'coupon-creator-troubleshooting-plugin-conflicts' ),
			'type'    => 'links'
		);
		$this->fields['video_pro_tutorials']           = array(
			'section' => '',
			'tab'     => '',
			'text'    => 'Tutorials - Customization Tutorials and More',
			'link'    => cctor_guide_url( 'coupon-creator-pro-customizing-coupons' ),
			'type'    => 'links'
		);
		$this->fields['video_end_list_resources']      = array(
			'section' => '',
			'tab'     => '',
			'text'    => '',
			'type'    => 'end_list'
		);

	}

	/**
	 * Coupon Creator Help Tab Support Links
	 *
	 * @return string
	 */
	public function get_cctor_support_core_contact() {

		if ( class_exists( 'Cctor__Coupon__Pro__Main' ) ) {
			// Support form is a product/support page, not a doc — repointed via the cctor.link redirect.
			$support_url = 'http://cctor.link/pro-support';
			$url_404     = esc_url( cctor_guide_url( 'coupon-creator-pro-troubleshooting-404-errors' ) );
			$url_tc      = esc_url( cctor_guide_url( 'coupon-creator-pro-troubleshooting-conflicts' ) );
			$url_jscon   = esc_url( cctor_guide_url( 'coupon-creator-pro-troubleshooting-javascript-errors' ) );

			$support_html = '
				<h4 class="pngx-fields-heading">How to Contact Support</h4>
					<ul>
						<li>For Coupon Creator Pro users please use the <a class="pngx-support" target="_blank" href="' . esc_url( $support_url ) . '">Support Form on CouponCreatorPlugin.com</a> to get direct support.</li>

						<li><br>Before contacting support please try to narrow or solve your issue by using one or all of these troubleshooting guides:
							<ul>
							<li><br><a class="pngx-support" target="_blank" href="' . $url_404 . '">Troubleshooting 404 Errors</a></li>
							<li><a class="pngx-support" target="_blank" href="' . $url_tc . '">Troubleshooting Conflicts</a></li>
							<li><a class="pngx-support" target="_blank" href="' . $url_jscon . '">Troubleshooting Javascript Errors</a></li>
							</ul>
						</li>

					</ul>';
		} else {
			// WordPress.org support forum is not a doc — repointed via the cctor.link redirect.
			$forum_url = 'http://cctor.link/ZlQvh';
			$url_404   = esc_url( cctor_guide_url( 'coupon-creator-troubleshooting-404-errors' ) );
			$url_tc    = esc_url( cctor_guide_url( 'coupon-creator-troubleshooting-plugin-conflicts' ) );
			$url_jscon = esc_url( cctor_guide_url( 'coupon-creator-troubleshooting-javascript-errors' ) );

			$support_html = '
			<h4 class="pngx-fields-heading">How to Contact Support</h4>
			<ul>
				<li>Please use the <a target="_blank" class="pngx-support" href="' . esc_url( $forum_url ) . '">WordPress.org Support Forum for the Coupon Creator</a>.</li>
				<li><br>Before contacting support please try to narrow or solve your issue by using one or all of these troubleshooting guides:
					<ul>
					<li><br><a class="pngx-support" target="_blank" href="' . $url_404 . '">Troubleshooting 404 Errors</a></li>
					<li><a class="pngx-support" target="_blank" href="' . $url_tc . '">Troubleshooting Conflicts</a></li>
					<li><a class="pngx-support" target="_blank" href="' . $url_jscon . '">Troubleshooting Javascript Errors</a></li>
					</ul>
				</li>

			</ul>';

		}

		return $support_html;
	}

}
