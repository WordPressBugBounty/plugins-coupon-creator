<?php
use Cctor\Coupon\Templates\Admin_Template;

/**
 * Class Cctor__Coupon__Admin__Meta
 */
class Cctor__Coupon__Admin__Meta extends Pngx__Admin__Meta {

	//fields id prefix
	protected $fields_prefix = 'cctor_';

	//post type
	protected $post_type = array( 'cctor_coupon' );

	//user capability
	protected $user_capability = 'edit_cctor_coupon';

	/**
	 * An instance of the admin template handler.
	 *
	 * @since 0.1.0
	 *
	 * @var Admin_Template
	 */
	protected $admin_template;

	/**
	 * Meta constructor.
	 *
	 * @since 3.4.0
	 *
	 * @param Admin_Template $template An instance of the backend template handler.
	 */
	public function __construct( Admin_Template $admin_template ) {
		$this->admin_template = $admin_template;
		parent::__construct();
	}

	/**
	 * Admin Init
	 */
	public function setup() {

		//Setup Menu Meta Boxes
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		//Coupon Expiration Information
		add_action( 'edit_form_after_title', array( $this, 'coupon_messages' ), 5 );
		add_action( 'edit_form_after_title', array( $this, 'coupon_information_box' ) );

		// Add default template
		add_filter( 'pngx-default-template', array( $this, 'default_template' ) );

		// Sync derived ignore_expiration value after the engine's meta save loop runs.
		add_action( 'save_post_cctor_coupon', array( $this, 'sync_ignore_expiration' ), 11 );

		// Scope the Artifex admin UI to our own metaboxes only (never <body>, so
		// nothing leaks onto the surrounding post-edit chrome or any 3rd-party
		// overlay mounted in the footer). The class rides on each .postbox.
		add_filter( 'postbox_classes_cctor_coupon_coupon_creator_meta_box', array( $this, 'add_afx_postbox_class' ) );
		add_filter( 'postbox_classes_cctor_coupon_coupon_creator_shortcode', array( $this, 'add_afx_postbox_class' ) );

		$this->set_tabs();
		$this->set_fields();
	}

	/**
	 * Add the Artifex theme class to a Coupon Creator metabox wrapper.
	 *
	 * @param string[] $classes Existing postbox classes.
	 *
	 * @return string[]
	 */
	public function add_afx_postbox_class( $classes ) {
		$classes[] = 'afx-theme-coupon-creator';

		return $classes;
	}

	/**
	 * Add Hook on Coupon CPT Editor
	 */
	public function coupon_information_box() {

		$current_screen = $this->get_screen_variables();

		//Display Message on Coupon Edit Screen, but not on a new coupon until saved
		if ( 'post-new.php' != $current_screen['pagenow'] && in_array( $current_screen['type'], $this->get_post_types() ) ) {

			/**
			 * Display Message on Individual Coupon Editor Page
			 *
			 * @since 1.90
			 *
			 * @param int $coupon_id
			 *
			 */
			do_action( 'pngx_meta_message', $current_screen['post'] );

		}

	}

	/**
	 * Add Messages to Coupon Message Hook
	 */
	public function coupon_messages() {

		if ( class_exists( 'Cctor__Coupon__Pro__Expiration' ) ) {
			$coupon_expiration = new Cctor__Coupon__Pro__Expiration();
		} else {
			$coupon_expiration = new Cctor__Coupon__Expiration();
		}

		add_action( 'pngx_meta_message', array( $coupon_expiration, 'get_coupon_status' ), 15, 1 );
		add_action( 'pngx_meta_message', array( $coupon_expiration, 'the_coupon_status_msg' ), 20, 1 );

	}

	/*
	* Add Meta Boxes
	*/
	public function add_meta_boxes() {

		$current_screen = $this->get_screen_variables();

		if ( in_array( $current_screen['pagenow'], array( 'post.php', 'post-new.php' ) ) && in_array( $current_screen['type'], $this->get_post_types() ) ) {

			add_meta_box( 'coupon_creator_meta_box', // id
				__( 'Coupon Fields', 'coupon-creator' ), // title
				array( $this, 'display_fields' ), // callback
				$this->get_post_types(), // post_type
				'normal', // context
				'high' // priority
			);

			if ( 'post-new.php' != $current_screen['pagenow'] ) {
				add_meta_box( 'coupon_creator_shortcode', // id
					__( 'Coupon Shortcode', 'coupon-creator' ), // title
					array( $this, 'show_coupon_shortcode' ), // callback
					$this->get_post_types(), // post_type
					'side' // context
				);
			}

			/**
			 * Additional Coupons Hook
			 */
			do_action( 'cctor_add_meta_box', $current_screen['post'] );

		}
	}

	/**
	 * Load Shortcode
	 *
	 * @param $post
	 */
	public function show_coupon_shortcode( $post ) {
		?>
		<p><?php esc_html_e( 'Place this coupon in a post, page, or widget with the shortcode below.', 'coupon-creator' ); ?></p>
		<?php
		$usability = pngx( 'cctor.admin.usability' );
		$usability->display_coupon_actions( $post, true );

	}

	/*
	* Set Tabs
	*/
	public function set_tabs() {

		//CPT Fields Tabs
		$tabs['content']      = __( 'Content', 'coupon-creator' );
		$tabs['style']        = __( 'Border & Background', 'coupon-creator' );
		$tabs['expiration']   = __( 'Expiration', 'coupon-creator' );
		! defined( 'CCTOR_HIDE_UPGRADE' ) || ! CCTOR_HIDE_UPGRADE ? $tabs['links'] = __( 'Links', 'coupon-creator' ) : null;
		$tabs['help'] = __( 'Help', 'coupon-creator' );


		//Filter Option Tabs
		if ( has_filter( 'cctor_filter_meta_tabs' ) ) {

			/**
			 * Filter the Coupon Creator Meta Tab Header
			 *
			 *
			 * @param array $tabs an array of tab headings.
			 *
			 */
			$tabs = apply_filters( 'cctor_filter_meta_tabs', $tabs );
		}

		$this->tabs = $tabs;
	}

	/*
	* Load Fields
	*
	*/
	public function set_fields() {
		$this->fields = pngx( 'cctor.meta' )->get_fields();
	}


	/**
	 * Add default template
	 *
	 * @param $template
	 *
	 * @return bool|null
	 */
	public function default_template( $template ) {
		// The third argument is what makes the ticket the default on a site that has never
		// saved the setting. Without it this returns false, the engine falls back to its own
		// 'default', and a new coupon opens on Classic no matter what the option field's
		// `std` says -- `std` only seeds the settings screen, not the value read here.
		$default = cctor_options( 'cctor_default_template', false, 'ticket' );

		if ( $default ) {
			$template = $default;
		}

		return $template;
	}

	/**
	 * Sync the derived cctor_ignore_expiration meta from cctor_expiration_option.
	 *
	 * ignore_expiration is not a user-editable checkbox; its state is derived from the
	 * selected expiration_option. This runs on save_post_cctor_coupon, which WordPress
	 * fires *before* the plugin-engine's save_meta() on save_post — so we cannot rely on
	 * the engine's nonce/cap check having run first. This write verifies the engine's
	 * meta nonce and the user's capability itself.
	 *
	 * @param int $post_id Coupon post ID.
	 */
	public function sync_ignore_expiration( $post_id ) {
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Only act when the meta form was actually submitted.
		if ( ! isset( $_POST['cctor_expiration_option'] ) ) {
			return;
		}

		// Verify the engine's meta nonce and the user's capability directly — do not
		// depend on another save_post callback having gated the request.
		if (
			! isset( $_POST['pngx_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pngx_nonce'] ) ), 'pngx_save_fields' )
		) {
			return;
		}

		if ( ! current_user_can( 'edit_cctor_coupon', $post_id ) ) {
			return;
		}

		$expiration_option = absint( $_POST['cctor_expiration_option'] );

		if ( 1 === $expiration_option ) {
			update_post_meta( $post_id, 'cctor_ignore_expiration', 'on' );
		} else {
			delete_post_meta( $post_id, 'cctor_ignore_expiration' );
		}
	}

}
