<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/*
* Coupon Creator Shortcode Class
*
*/


class Cctor__Coupon__Shortcode {

	/*
	* Coupon Creator Shortcode
	*/
	public function core_shortcode( $atts ) {

		//Load Stylesheet for Coupon Creator when Shortcode Called
		if ( ! wp_style_is( 'coupon_creator_css' ) ) {
			wp_enqueue_style( 'coupon_creator_css' );
		}
		/**
		 * Core Coupon Shortcode Starting Hook
		 *
		 */
		do_action( 'cctor_shortcode_start' );

		//Coupon ID is the Custom Post ID
		$cctor_atts = shortcode_atts( array(
			"totalcoupons"  => '-1',
			"couponid"      => '',
			"coupon_align"  => 'cctor_alignnone',
			"couponorderby" => 'date',
			"category"      => '',
			"bordertheme"   => '',
			"filterid"      => ''
		), $atts, 'coupon' );

		// Guard the unbounded default. A bare [coupon] historically defaulted to totalcoupons="-1",
		// which runs WP_Query with posts_per_page => -1 and no pager — every published coupon on one
		// page. Fine for a handful, a page-killer at hundreds. When totalcoupons is not explicitly set
		// on the shortcode, fall back to a sane, filterable cap. Passing totalcoupons (including -1)
		// explicitly is still honored for anyone who wants the old behavior.
		if ( ! isset( $atts['totalcoupons'] ) ) {
			/**
			 * Filter the default number of coupons a bare [coupon] shortcode renders when
			 * totalcoupons is not specified. Return -1 to restore the legacy "show all" behavior.
			 *
			 * @param int $default_totalcoupons Default cap when totalcoupons is omitted.
			 */
			$cctor_atts['totalcoupons'] = apply_filters( 'cctor_default_totalcoupons', 50 );
		}

		$filterid     = $cctor_atts['filterid'];
		$coupon_align = $cctor_atts['coupon_align'];

		// Setup Query for Either Single Coupon or a Loop
		$cctor_args = array(
			'p'                     => esc_attr( $cctor_atts['couponid'] ),
			'posts_per_page'        => esc_attr( $cctor_atts['totalcoupons'] ),
			'cctor_coupon_category' => $cctor_atts['category'],
			'post_type'             => 'cctor_coupon',
			'post_status'           => 'publish',
			'orderby'               => esc_attr( $cctor_atts['couponorderby'] ),
			// Prime meta + term caches for the whole result set in one query each, so the
			// per-coupon get_post_meta()/get_the_terms() calls in the render loop hit cache
			// instead of querying per coupon. These are WP_Query defaults, pinned here so a
			// cctor_shortcode_query_args filter (or a future edit) can't silently drop them.
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		);

		//Filter for all Shortcodes
		if ( has_filter( 'cctor_shortcode_query_args' ) ) {
			/**
			 * Filter Core ShortCode Query Arguments
			 *
			 * @param array $cctor_args .
			 *
			 */
			$cctor_args = apply_filters( 'cctor_shortcode_query_args', $cctor_args );
		}

		//Custom Filter ID Set in Shortcode
		if ( $filterid ) {
			if ( has_filter( 'cctor_shortcode_query_args_' . $filterid ) ) {
				/**
				 * Filter Core ShortCode Query Arguments by ID
				 *
				 * @param array $cctor_args .
				 *
				 */
				$cctor_args = apply_filters( 'cctor_shortcode_query_args_' . $filterid, $cctor_args );
			}
		}

		$coupons = new WP_Query( $cctor_args );

		ob_start();

		/**
		 * Before Core Coupon Shortcode Wrap
		 *
		 * @since 1.90
		 *
		 */
		do_action( 'cctor_before_coupon_wrap' );

		if ( $coupons->have_posts() ) {

			// The Coupon Loop
			while ( $coupons->have_posts() ) {

				$coupons->the_post();

				$coupon_id = $coupons->post->ID;


				if ( class_exists( 'Cctor__Coupon__Pro__Expiration' ) ) {
					$coupon_expiration = new Cctor__Coupon__Pro__Expiration( $coupon_id );
				} else {
					$coupon_expiration = new Cctor__Coupon__Expiration( $coupon_id );
				}
				/**
				 * Before Core Coupon Shortcode Individual Coupon
				 *
				 * @param int $coupon_id
				 *
				 */
				do_action( 'cctor_before_coupon', $coupon_id );
				//Check to show the Coupon
				if ( $coupon_expiration->check_expiration() ) {

					/**
					 * Coupon types backed by a view file in src/views/type/ render through
					 * the template loader. The `default` and `image` types deliberately keep
					 * using the legacy action chain below so their markup, and every third
					 * party hooked into it, stays byte-for-byte unchanged.
					 */
					$coupon_type = get_post_meta( $coupon_id, 'cctor_coupon_type', true );

					if (
						! in_array( $coupon_type, array( '', 'default', 'image' ), true )
						// A coupon carrying an image has always rendered as an image
						// regardless of the selected type. Keep that precedence.
						&& ! apply_filters( 'cctor_image_url', $coupon_id, 'single_coupon' )
						&& Cctor__Coupon__Templates::type_has_view( $coupon_type )
					) {

						cctor_core_get_template_part(
							'type/' . $coupon_type,
							null,
							array(
								'coupon_id'         => $coupon_id,
								'coupon_align'      => $coupon_align,
								'coupon_expiration' => $coupon_expiration,
								'cctor_atts'        => $cctor_atts,
								'fields'            => pngx( 'cctor.meta.order' )->get_ordered_template_fields( array() ),
							)
						);

						/**
						 * After Core Shortcode Wrap
						 *
						 * @param int $coupon_id
						 */
						do_action( 'cctor_after_coupon', $coupon_id );

						continue;
					}

					/**
					 * Filter Individual Coupon Outer Wrap
					 *
					 * @param int         $coupon_id    .
					 * @param string|null $coupon_align .
					 * @param string      $cctor_atts   ['bordertheme'] .
					 *
					 */
					$outer_coupon_wrap = apply_filters( 'cctor_outer_content_wrap', $coupon_id, $coupon_align, $cctor_atts['bordertheme'] );

					echo $outer_coupon_wrap['start_wrap'];

					/**
					 * Before Core Shortcode Individual Coupon Wrap
					 *
					 * @param int $coupon_id
					 *
					 */
					do_action( 'cctor_before_coupon_inner_wrap', $coupon_id );

					//Return If Not Passed Expiration Date
					/**
					 * Filter Individual Image Coupon URL
					 *
					 * @param int $coupon_id
					 * @param string (image size)
					 *
					 */
					$couponimage = apply_filters( 'cctor_image_url', $coupon_id, 'single_coupon' );

					if ( $couponimage ) {
						/**
						 * Display Coupon Image Hook
						 *
						 * @param int    $coupon_id
						 * @param string $couponimage
						 * @param array  $cctor_atts ['bordertheme']
						 */
						do_action( 'cctor_img_coupon', $coupon_id, $couponimage, $cctor_atts['bordertheme'] );

						/**
						 * Coupon Expiration Display Hook
						 *
						 * Image coupons gate on the expiration date like text coupons,
						 * so the date has to be visible on them too.
						 *
						 * @param int    $coupon_id
						 * @param object $coupon_expiration
						 */
						do_action( 'cctor_coupon_expiration', $coupon_id, $coupon_expiration );

					} else {
						/**
						 * Filter Individual Coupon Inner Wrap
						 *
						 * @param int         $coupon_id    .
						 * @param string|null $coupon_align .
						 * @param string      $cctor_atts   ['bordertheme'] .
						 *
						 */
						$inner_coupon_wrap = apply_filters( 'cctor_inner_content_wrap', $coupon_id, $cctor_atts['bordertheme'] );

						echo $inner_coupon_wrap['start_wrap'];
						/**
						 * Coupon Deal Hook
						 *
						 * @param int $coupon_id
						 *
						 */
						do_action( 'cctor_coupon_deal', $coupon_id );
						/**
						 * Coupon Terms Hook
						 *
						 * @param int $coupon_id
						 *
						 */
						do_action( 'cctor_coupon_terms', $coupon_id );

						/**
						 * Coupon Expiration Display Hook
						 *
						 * @param int    $coupon_id
						 * @param object $coupon_expiration
						 */
						do_action( 'cctor_coupon_expiration', $coupon_id, $coupon_expiration );

						echo $inner_coupon_wrap['end_wrap'];

					}
					/**
					 * Individual Coupon Link
					 *
					 * @param int $coupon_id
					 *
					 */
					do_action( 'cctor_coupon_link', $coupon_id );

					echo $outer_coupon_wrap['end_wrap'];

				} else {
					/**
					 * coupon Expired Hook
					 * Only Shows for expired coupon
					 *
					 * @param int    $coupon_id
					 * @param object $coupon_expiration
					 */
					do_action( 'cctor_no_show_coupon', $coupon_id, $coupon_expiration, $coupon_align );
				}
				/**
				 * After Core Shortcode Wrap
				 *
				 * @param int $coupon_id
				 *
				 */
				do_action( 'cctor_after_coupon', $coupon_id );

			} //End While

		} else {
			echo '<!-- ' . esc_html__( 'Coupon shortcode did not find a published coupon.', 'coupon-creator' ) . ' -->';
			echo $this->get_query_diagnostic( $cctor_atts );
		}
		/**
		 * End Core Shortcode
		 *
		 */
		do_action( 'cctor_shortcode_end' );

		/* Restore original Post Data */
		wp_reset_postdata();

		// Return Variables
		return ob_get_clean();

	}

	/**
	 * Build an editor-only diagnostic for an empty coupon query.
	 *
	 * @param array $attributes Parsed shortcode attributes.
	 *
	 * @return string
	 */
	protected function get_query_diagnostic( $attributes ) {
		$coupon_id = absint( $attributes['couponid'] );

		if ( ! cctor_current_user_can_view_diagnostic( $coupon_id ) ) {
			return '';
		}

		if ( $coupon_id && Cctor__Coupon__Main::POSTTYPE === get_post_type( $coupon_id ) ) {
			$status  = get_post_status( $coupon_id );
			$message = sprintf(
				/* translators: %s: coupon post status. */
				__( 'This coupon is %s and is not showing to visitors.', 'coupon-creator' ),
				$status ? $status : __( 'unavailable', 'coupon-creator' )
			);
		} elseif ( $coupon_id ) {
			$message = __( 'The selected coupon could not be found. Choose another coupon or update the shortcode.', 'coupon-creator' );
		} else {
			$message = __( 'No published coupons matched this selection. Publish a coupon or adjust the category and shortcode settings.', 'coupon-creator' );
		}

		return '<div class="pngx-message pngx-notice cctor-editor-diagnostic" role="status"><p>' . esc_html( $message ) . '</p></div>';
	}

}
