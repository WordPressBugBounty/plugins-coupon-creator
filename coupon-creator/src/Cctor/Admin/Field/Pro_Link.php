<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Cctor__Coupon__Admin__Field__Pro_Link' ) ) {
	return;
}


/**
 * Class Cctor__Coupon__Admin__Field__Pro_Link
 * Coupon Creator Pro Field Header
 */
class Cctor__Coupon__Admin__Field__Pro_Link {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		if ( ! defined( 'CCTOR_HIDE_UPGRADE' ) || ! CCTOR_HIDE_UPGRADE ) {

			if ( isset( $options_id ) && ! empty( $options_id ) ) {
				echo '</td></tr><tr valign="top"><td colspan="2">';
			}

			echo '<div class="cctor-pro-link"><strong>' . esc_html__( 'Coupons that expire on their own.', 'coupon-creator' ) . '</strong> <a target="_blank" rel="noopener noreferrer" href="http://cctor.link/CjZX2">' . esc_html__( 'See how Coupon Creator Pro handles the features listed above.', 'coupon-creator' ) . '</a></div>';
		}

	}
}
