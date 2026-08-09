<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/*
* Coupon Creator Image Coupon Link
* @version 1.90
*/
function cctor_show_img_coupon( $coupon_id, $couponimage ) {
	//Build Click to Print Link for the Image - First Check if Option to Hide is Checked

	$new_tab = ! defined( 'CCTOR_PREVENT_OPEN_IN_NEW_TAB' ) || ! CCTOR_PREVENT_OPEN_IN_NEW_TAB;

	$rel = array();
	if ( cctor_options( 'cctor_nofollow_print_link', true, 1 ) == 1 ) {
		$rel[] = 'nofollow';
	}
	if ( $new_tab ) {
		$rel[] = 'noopener';
	}
	$rel_attr    = $rel ? 'rel="' . esc_attr( implode( ' ', $rel ) ) . '"' : '';
	$target_attr = $new_tab ? 'target="_blank"' : '';

	if ( ! cctor_options( 'cctor_hide_print_link' ) ) {

		//Set Image Link
		?>
		<a class="coupon_link" <?php echo $target_attr; ?> <?php echo $rel_attr; ?> href='<?php echo esc_url( get_permalink( $coupon_id ) ); ?>' title='<?php echo esc_attr__( 'Click to Open in Print View', 'coupon-creator' ); ?>'>
		<img class='cctor_coupon_image' src='<?php echo esc_url( $couponimage ); ?>' alt='<?php echo esc_attr( get_the_title( $coupon_id ) ); ?>' title='<?php echo esc_attr__( 'Coupon', 'coupon-creator' ); ?> <?php echo esc_attr( get_the_title( $coupon_id ) ); ?>'>
		</a><?php
	} else {
		//No Links for Image Coupon or Click to Print
		?><img class='cctor_coupon_image' src='<?php echo esc_url( $couponimage ); ?>' alt='<?php echo get_the_title( $coupon_id ); ?>' title='<?php echo get_the_title( $coupon_id ); ?>'><?php
	}

}

/*
* Coupon Creator Click to Open in Print View Link
* @version 1.90
*/
function cctor_show_link( $coupon_id ) {

	$new_tab = ! defined( 'CCTOR_PREVENT_OPEN_IN_NEW_TAB' ) || ! CCTOR_PREVENT_OPEN_IN_NEW_TAB;

	$rel = array();
	if ( cctor_options( 'cctor_nofollow_print_link', true, 1 ) == 1 ) {
		$rel[] = 'nofollow';
	}
	if ( $new_tab ) {
		$rel[] = 'noopener';
	}
	$rel_attr    = $rel ? 'rel="' . esc_attr( implode( ' ', $rel ) ) . '"' : '';
	$target_attr = $new_tab ? 'target="_blank"' : '';

	//Build Click to Print Link For Coupon - First Check if Option to Hide is Checked
	if ( ! cctor_options( 'cctor_hide_print_link' ) ) {

		?>
		<div class='cctor_opencoupon cctor-opencoupon'>
		<a class="print-link" <?php echo $rel_attr; ?> href='<?php echo esc_url( get_permalink( $coupon_id ) ); ?>' <?php echo $target_attr; ?>><?php echo esc_html__( 'Click to Open in Print View', 'coupon-creator' ); ?></a>
		</div><!--end .opencoupon --><?php

	} else {
		?>
		<div class='cctor_opencoupon cctor-opencoupon'></div><?php
	}
}

/*
* Coupon Creator Print Template Click to Print
* @version 1.90
*/
function cctor_show_print_click( $coupon_id ) {
	?>
	<div class="cctor_opencoupon cctor-opencoupon"> <!-- We Need a Click to Print Button -->
		<button type="button" class="print-link" data-cctor-print="1" aria-label="<?php esc_attr_e( 'Print this coupon', 'coupon-creator' ); ?>"><?php echo esc_html__( 'Click to Print', 'coupon-creator' ); ?></button>

	</div> <!--end .opencoupon -->
	<script>
		document.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '[data-cctor-print]' ) ) {
				window.print();
			}
		} );
	</script>
	<?php

}