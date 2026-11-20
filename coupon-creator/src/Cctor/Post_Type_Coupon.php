<?php


/**
 * Class Cctor_Coupon_Post_Type_Coupon
 */
class Cctor__Coupon__Post_Type_Coupon {

	public $post_type;
	public $text_domain;
	public $taxonomy;

	public $singular_coupon_label;
	public $plural_coupon_label;
	public $singular_coupon_label_lowercase;
	public $plural_coupon_label_lowercase;
	public $singular_category_label;
	public $singular_category_label_lowercase;
	public $plural_category_label;
	public $plural_category_label_lowercase;

	/**
	 * Cctor__Coupon__Post_Type_Coupon constructor.
	 *
	 * @param $post_type
	 * @param $taxonomy
	 * @param $text_domain
	 */
	public function __construct( $post_type, $taxonomy, $text_domain ) {

		$this->post_type   = $post_type;
		$this->taxonomy    = $taxonomy;
		$this->text_domain = $text_domain;

	}

	/**
	 * Initialize labels with translations.
	 * Called on init action to ensure text domain is loaded.
	 *
	 * @since 3.4.2.1
	 */
	protected function init_labels() {
		$this->singular_coupon_label           = $this->get_coupon_label_singular();
		$this->singular_coupon_label_lowercase = $this->get_coupon_label_singular_lowercase();
		$this->plural_coupon_label             = $this->get_coupon_label_plural();
		$this->plural_coupon_label_lowercase   = $this->get_coupon_label_plural_lowercase();

		$this->singular_category_label           = $this->get_coupon_category_label_singular();
		$this->singular_category_label_lowercase = $this->get_coupon_category_label_singular_lowercase();
		$this->plural_category_label             = $this->get_coupon_category_label_plural();
		$this->plural_category_label_lowercase   = $this->get_coupon_category_label_plural_lowercase();
	}

	/**
	 * Register Post Type and Taxonomy on Init
	 *
	 * @since 3.0
	 *
	 */
	public function register() {
		$this->init_labels();
		$this->register_post_types();
		$this->register_taxonomies();
	}

	/**
	 * Returns the string to be used as the taxonomy slug.
	 *
	 * @return string
	 */
	public function get_coupon_slug() {

		/**
		 * Filters the coupon post type slug.
		 *
		 * @param string $slug The sanitized coupon slug from the options page.
		 *
		 * @psalm-var string
		 */
		return apply_filters( 'cctor_coupon_slug', sanitize_title( cctor_options( 'cctor_coupon_base', false, $this->post_type ) ) );
	}

	/**
	 * Allow users to specify their own singular label for Coupons
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function get_coupon_label_singular() {
		/**
		 * Filters the singular label for coupons.
		 *
		 * @since 3.0
		 *
		 * @param string $label Translated label, 'Coupon' by default.
		 */
		return apply_filters( 'cctor_coupon_label_singular', esc_html__( 'Coupon', $this->text_domain ) );
	}

	/**
	 * Get Coupon Label Singular lowercase
	 *
	 * @since 3.0
	 *
	 * Returns the singular version of the Coupon Label
	 *
	 * @return string
	 */
	function get_coupon_label_singular_lowercase() {
		/**
		 * Filters the lowercase singular label for coupons.
		 *
		 * @since 3.0
		 *
		 * @param string $label Translated label, 'coupon' by default.
		 */
		return apply_filters( 'cctor_coupon_label_singular_lowercase', esc_html__( 'coupon', $this->text_domain ) );
	}

	/**
	 * Allow users to specify their own plural label for Coupons
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function get_coupon_label_plural() {
		/**
		 * Filters the plural label for coupons.
		 *
		 * @since 3.0
		 *
		 * @param string $label Translated label, 'Coupons' by default.
		 */
		return apply_filters( 'cctor_coupon_label_plural', esc_html__( 'Coupons', $this->text_domain ) );
	}

	/**
	 * Get Coupon Label Plural lowercase
	 *
	 * @since 3.0
	 *
	 * Returns the plural version of the Coupon Label
	 *
	 * @return string
	 */
	function get_coupon_label_plural_lowercase() {
		/**
		 * Filters the lowercase plural label for coupons.
		 *
		 * @since 3.0
		 *
		 * @param string $label Translated label, 'coupons' by default.
		 */
		return apply_filters( 'cctor_coupon_label_plural_lowercase', esc_html__( 'coupons', $this->text_domain ) );
	}

	/**
	 * Allow users to specify their own singular label for Coupon Category
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function get_coupon_category_label_singular() {
		/**
		 * Filters the singular label for coupon categories.
		 *
		 * @since 3.0
		 *
		 * @param string $label Translated label, 'Coupon Category' by default.
		 */
		return apply_filters( 'cctor_coupon_category_label_singular', esc_html__( 'Coupon Category', $this->text_domain ) );
	}

	/**
	 * Allow users to specify their own lowercase singular label for Coupon Category
	 *
	 * @return string
	 */
	public function get_coupon_category_label_singular_lowercase() {
		/**
		 * Filters the lowercase singular label for coupon categories.
		 *
		 * @param string $label Translated label, 'coupon category' by default.
		 */
		return apply_filters( 'cctor_coupon_category_label_singular_lowercase', esc_html__( 'coupon category', $this->text_domain ) );
	}

	/**
	 * Allow users to specify their own plural label for Coupon Categories
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function get_coupon_category_label_plural() {
		/**
		 * Filters the plural label for coupon categories.
		 *
		 * @since 3.0
		 *
		 * @param string $label Translated label, 'Coupon Categories' by default.
		 */
		return apply_filters( 'cctor_coupon_category_plural', esc_html__( 'Coupon Categories', $this->text_domain ) );
	}

	/**
	 * Allow users to specify their own plural label for coupon categories
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function get_coupon_category_label_plural_lowercase() {
		/**
		 * Filters the lowercase plural label for coupon categories.
		 *
		 * @since 3.0
		 *
		 * @param string $label Translated label, 'coupon categories' by default.
		 */
		return apply_filters( 'cctor_coupon_category_plural_lowercase', esc_html__( 'coupon categories', $this->text_domain ) );
	}


	/**
	 * Returns the string to be used as the taxonomy slug.
	 *
	 * @return string
	 */
	public function get_category_slug() {

		/**
		 * Filters the coupon category taxonomy slug.
		 *
		 * @param string $slug The sanitized category slug from the options page.
		 *
		 * @psalm-var string
		 */
		return apply_filters( 'cctor_category_slug', sanitize_title( cctor_options( 'cctor_coupon_category_base', false, 'coupon-category' ) ) );
	}

	/**
	 * Get the coupon post type
	 *
	 * @return string
	 */
	public function get_coupon_post_type() {
		return $this->post_type;
	}

	/**
	 * Get the coupon taxonomy
	 *
	 * @return string
	 */
	public function get_coupon_taxonomy() {
		return $this->taxonomy;
	}

	/**
	 * Marks every `cctor_` post meta key as protected.
	 *
	 * The coupon post type supports 'custom-fields', so without this any user who
	 * can edit a coupon could write these keys raw through core's Custom Fields box
	 * (admin-ajax add-meta / update-meta), skipping the coupon meta box sanitizers.
	 * Protected keys are hidden from that box and refused by those requests. The
	 * plugin's own saves use update_post_meta() directly and are not affected.
	 *
	 * @since 3.6.6
	 *
	 * @param bool        $protected Whether the key is protected.
	 * @param string      $meta_key  Meta key.
	 * @param string|null $meta_type Object type.
	 *
	 * @return bool
	 */
	public function protect_coupon_meta( $protected, $meta_key, $meta_type = null ) {
		if ( 'post' === $meta_type && is_string( $meta_key ) && 0 === strpos( $meta_key, 'cctor_' ) ) {
			return true;
		}

		return $protected;
	}

	/**
	 * Register the post types.
	 *
	 * @since 3.0
	 *
	 */
	public function register_post_types() {

		// Register Coupon Post Type and customize labels
		//  @formatter:off
			$labels = pngx( Pngx__Register_Post_Type::class )->generate_post_type_labels(
				$this->singular_coupon_label,
				$this->plural_coupon_label,
				$this->singular_coupon_label_lowercase,
				$this->plural_coupon_label_lowercase,
				$this->text_domain
			);

			pngx( Pngx__Register_Post_Type::class )->register_post_types(
				$this->post_type,
				$this->post_type,
				$this->singular_coupon_label,
				$labels,
				$this->get_coupon_slug(),
				$this->text_domain,
				array(
					'supports'  => array( 'title', 'coupon_creator_meta_box' ),
					'menu_icon' => $this->get_menu_icon(),
				)
			);

			new Pngx__Register_Post_Type(
				$this->post_type,
				__( 'Enter Coupon Admin Title', 'coupon-creator' )
			);
			// @formatter:on
	}

	/**
	 * Return the WordPress admin menu icon.
	 *
	 * @return string
	 */
	protected function get_menu_icon() {
		$icon_path = COUPON_CREATOR_DIR . '/src/resources/images/coupon-creator-mark-mono.svg';

		if ( ! is_readable( $icon_path ) ) {
			return 'dashicons-tickets-alt';
		}

		$icon = file_get_contents( $icon_path );

		return false === $icon
			? 'dashicons-tickets-alt'
			: 'data:image/svg+xml;base64,' . base64_encode( $icon );
	}

	/**
	 * Register the taxonomies.
	 */
	public function register_taxonomies() {

		// Register Coupon Taxonomy
		// @formatter:off
			$labels = pngx( Pngx__Register_Taxonomy::class )->generate_taxonomy_labels(
				$this->singular_category_label,
				$this->singular_category_label_lowercase,
				$this->plural_category_label,
				$this->plural_category_label_lowercase,
				$this->text_domain
			);

			pngx( Pngx__Register_Taxonomy::class )->register_taxonomy(
				$this->taxonomy,
				$this->post_type,
				$labels,
				$this->get_category_slug(),
				false
			);
			// @formatter:on
	}

}
