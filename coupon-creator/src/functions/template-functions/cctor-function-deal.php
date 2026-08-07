<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/*
* Coupon Creator Print Template Title
* @version 1.90
*/
function cctor_show_deal( $coupon_id ) {

	// Values are escaped individually, exactly as before, so the assembled string
	// is already attribute-safe and must not be escaped a second time.
	$deal_style = 'background-color:' . esc_attr( get_post_meta( $coupon_id, 'cctor_colordiscount', true ) )
	              . '; color:' . esc_attr( get_post_meta( $coupon_id, 'cctor_colorheader', true ) ) . ';';

	/**
	 * Filter the inline style on the deal element.
	 *
	 * Templates that colour the deal through CSS custom properties rather than
	 * per-element inline styles (the ticket type, for example) return an empty
	 * string here so the inline values cannot beat the stylesheet.
	 *
	 * @since 3.6.0
	 *
	 * @param string $deal_style The already-escaped inline style attribute value.
	 * @param int    $coupon_id  The coupon post ID.
	 */
	$deal_style = apply_filters( 'cctor_deal_inline_style', $deal_style, $coupon_id );

	?><h3 class="cctor-deal"<?php echo $deal_style ? ' style="' . $deal_style . '"' : ''; ?>><?php echo esc_html( get_post_meta( $coupon_id, 'cctor_amount', true ) ); ?></h3><?php

}