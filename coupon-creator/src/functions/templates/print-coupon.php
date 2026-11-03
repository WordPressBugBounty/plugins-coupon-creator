<?php
/*
* Single Template For Custom Post Type Coupon
* Custom Template with WordPress loaded no header, footer, or theme styling
*/
	//Do Not Cache Print Templates
	if ( ! defined( 'DONOTCACHEPAGE' ) )
        define( 'DONOTCACHEPAGE', true);

	$coupon_id = get_the_ID();

	/**
	 * Fires at the top of the single-coupon print template, before any output.
	 *
	 * Pro redirects away here when the coupon disables its print view, and
	 * Add-ons read the dynamic code cookie before the coupon renders.
	 *
	 * @param int $coupon_id Coupon post ID.
	 */
	do_action( 'cctor_action_print_template', $coupon_id );

	if ( class_exists( 'Cctor__Coupon__Pro__Expiration' ) ) {
		$coupon_expiration = new Cctor__Coupon__Pro__Expiration( $coupon_id );
	} else {
		$coupon_expiration = new Cctor__Coupon__Expiration( $coupon_id );
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<?php

		/**
		 * Fires inside the <head> of the print template, before the head hook.
		 *
		 * Core prints the charset and viewport meta, the title, the base print
		 * CSS, and the coupon stylesheet here.
		 */
		do_action( 'coupon_print_meta' );

		/**
		 * Fires inside the <head> of the print template, after the meta hook.
		 *
		 * Core prints the custom CSS from the options page here.
		 */
		do_action( 'coupon_print_head' );

		?>
	</head>
<body class="print_coupon print-coupon">
<?php
if ( have_posts() ) while ( have_posts() ) : the_post();

	$coupon_id = get_the_ID();

	/**
	 * Fires before each coupon renders in the print template, inside the loop.
	 *
	 * @param int $coupon_id Coupon post ID.
	 */
	do_action( 'cctor_print_before_coupon', $coupon_id );

		//Check to show the Coupon
	if ( $coupon_expiration->check_expiration() ) {

			/**
			 * Coupon types backed by a view file render through the template loader,
			 * looking for `type/<type>-print.php` first and falling back to the shared
			 * `type/<type>.php`. `default` and `image` keep the legacy chain below.
			 */
			$coupon_print_type = get_post_meta( $coupon_id, 'cctor_coupon_type', true );

			if (
				! in_array( $coupon_print_type, array( '', 'default', 'image' ), true ) &&
				// A coupon carrying an image has always printed as an image
				// regardless of the selected type. Keep that precedence.
				/** This filter is documented in src/functions/templates/print-coupon.php */
				! apply_filters( 'cctor_print_image_url', $coupon_id, 'print_coupon' )
				&& Cctor__Coupon__Templates::type_has_view( $coupon_print_type )
			) {

				cctor_core_get_template_part(
					'type/' . $coupon_print_type,
					'print',
					array(
						'coupon_id'         => $coupon_id,
						'coupon_expiration' => $coupon_expiration,
						'is_print_view'     => true,
						'fields'            => pngx( 'cctor.meta.order' )->get_ordered_template_fields( array() ),
					)
				);

				/** This action is documented in src/functions/templates/print-coupon.php */
				do_action( 'cctor_print_after_coupon', $coupon_id );

				continue;
			}

			/**
			 * Filters the outer wrapper markup of a printed coupon.
			 *
			 * The value passed in is the coupon ID. The default callback returns an
			 * array with 'start_wrap' and 'end_wrap' HTML strings that enclose the
			 * whole coupon.
			 *
			 * @param int $coupon_id Coupon post ID.
			 */
			$outer_print_coupon_wrap  = apply_filters( 'cctor_print_outer_content_wrap' , $coupon_id  );

			echo $outer_print_coupon_wrap['start_wrap'];

				/**
				 * Filters the image URL used when a coupon prints as an image.
				 *
				 * The value passed in is the coupon ID. The default callback returns the
				 * URL of the coupon image, or an empty string when the coupon has no
				 * image and should print its deal and terms instead.
				 *
				 * @param int    $coupon_id Coupon post ID.
				 * @param string $size      Image size name handed to the callback. The print template passes 'print_coupon'.
				 */
				$couponimage = apply_filters( 'cctor_print_image_url' , $coupon_id, 'print_coupon'  );

				if ($couponimage) {

					/**
					 * Fires to output an image coupon in the print template.
					 *
					 * @param int    $coupon_id   Coupon post ID.
					 * @param string $couponimage URL of the coupon image.
					 */
					do_action( 'cctor_print_image_coupon' ,  $coupon_id, $couponimage );

					/**
					 * Fires to output the expiration date of a printed coupon.
					 *
					 * @param int                       $coupon_id         Coupon post ID.
					 * @param Cctor__Coupon__Expiration $coupon_expiration Expiration object for the coupon. Pro passes its own subclass.
					 */
					do_action( 'cctor_print_coupon_expiration', $coupon_id , $coupon_expiration );

				} else {

					/**
					 * Filters the inner wrapper markup of a printed coupon.
					 *
					 * The value passed in is the coupon ID. The default callback returns an
					 * array with 'start_wrap' and 'end_wrap' HTML strings that enclose the
					 * deal, the terms, and the expiration date.
					 *
					 * @param int $coupon_id Coupon post ID.
					 */
					$inner_print_coupon_wrap  = apply_filters( 'cctor_print_inner_content_wrap' , $coupon_id  );

					echo  $inner_print_coupon_wrap['start_wrap'];

						/**
						 * Fires to output the deal text of a printed coupon.
						 *
						 * @param int $coupon_id Coupon post ID.
						 */
						do_action( 'cctor_print_coupon_deal' , $coupon_id );

						/**
						 * Fires to output the terms of a printed coupon.
						 *
						 * @param int $coupon_id Coupon post ID.
						 */
						do_action( 'cctor_print_coupon_terms' , $coupon_id );

					/** This action is documented in src/functions/templates/print-coupon.php */
					do_action( 'cctor_print_coupon_expiration', $coupon_id , $coupon_expiration );

					echo $inner_print_coupon_wrap['end_wrap'];

				}

				/**
				 * Fires to output the Click to Print button under a printed coupon.
				 *
				 * @param int $coupon_id Coupon post ID.
				 */
				do_action( 'cctor_click_to_print_coupon' , $coupon_id );

			echo $outer_print_coupon_wrap['end_wrap'];

		} else {
			/**
			 * Fires in place of a coupon that is expired or hidden in the print template.
			 *
			 * Core prints an HTML comment with the expiration date, plus a visible
			 * notice for users who may view diagnostics.
			 *
			 * @param int                       $coupon_id         Coupon post ID.
			 * @param Cctor__Coupon__Expiration $coupon_expiration Expiration object for the coupon. Pro passes its own subclass.
			 */
			do_action( 'cctor_print_no_show_coupon' , $coupon_id, $coupon_expiration );
		}

	/**
	 * Fires after each coupon renders in the print template, inside the loop.
	 *
	 * Pro prints the print-only content here, and Add-ons output the email
	 * form or the email gate here.
	 *
	 * @param int $coupon_id Coupon post ID.
	 */
	do_action( 'cctor_print_after_coupon' , $coupon_id );

endwhile; // end the coupon creator loop

/**
 * Fires at the end of the print template body, after the coupon loop.
 *
 * Pro prints its iframe resize script here.
 */
do_action( 'coupon_footer' );

?>
</body>
</html>