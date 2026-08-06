<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Display Expiration Date
 *
 * @param null $coupon_expiration
 *
 * @return bool/string
 */
function cctor_show_expiration( $coupon_id, $coupon_expiration = null ) {

	if ( ! is_object( $coupon_expiration ) ) {
		return false;
	}

	$expiration_date = $coupon_expiration->get_display_expiration();

	if ( ! empty( $expiration_date ) ) {
		?>
		<div class="cctor_expiration core"><?php echo __( 'Expires on:', 'coupon-creator' ); ?>
			&nbsp;<?php echo esc_html( $expiration_date ); ?></div>
		<?php
	}
}

/**
 * Add expiration date to html comment for expired coupon
 *
 * @param $coupon_id
 * @param $coupon_expiration
 *
 * @return bool/string
 */
function cctor_show_no_coupon_comment( $coupon_id, $coupon_expiration ) {

	if ( ! is_object( $coupon_expiration ) ) {
		return false;
	}

	$expiration_date = $coupon_expiration->get_display_expiration();

	if ( ! empty( $expiration_date ) ) {

		?><!--<?php echo sprintf( '%1s %2s %3s %4s', __( 'Coupon', 'coupon-creator' ), get_the_title( $coupon_id ), __( 'expired on', 'coupon-creator' ), esc_html( $expiration_date ) ) ?>--><?php

	}

}

/**
 * Whether the current user may see a coupon rendering diagnostic.
 *
 * Diagnostics can appear on public-facing requests, so restrict them to site
 * administrators instead of every role that can edit coupons.
 *
 * @param int $coupon_id Coupon ID, when one can be resolved.
 *
 * @return bool
 */
function cctor_current_user_can_view_diagnostic( $coupon_id = 0 ) {
	return current_user_can( 'manage_options' );
}

/**
 * Display an editor-only expired-coupon diagnostic.
 *
 * @since 3.0
 *
 * @param $coupon_id
 * @param $coupon_expiration
 *
 * @return bool/string
 */
function cctor_show_no_coupon_notice_admin( $coupon_id, $coupon_expiration, $coupon_align = null ) {

	if ( ! is_object( $coupon_expiration ) ) {
		return false;
	}

	if ( ! cctor_current_user_can_view_diagnostic( $coupon_id ) ) {
		return false;
	}

	$expiration_date = $coupon_expiration->get_display_expiration();

	if ( ! empty( $expiration_date ) ) {
		?>
		<div id="coupon_creator_<?php echo absint( $coupon_id ); ?>" class="coupon-creator-<?php echo absint( $coupon_id ); ?> type-cctor_coupon cctor_coupon_container coupon-border cctor-editor-diagnostic <?php echo esc_html( $coupon_align ); ?>" role="status">
			<div class="cctor_coupon cctor-coupon">
				<div class="cctor_coupon_content cctor-coupon-content" style="border-color:#dd3333">
					<h3 class="cctor-deal" style="background-color:#dd3333; color:#000000;">
						<?php esc_html_e( 'Coupon Expired', 'coupon-creator' ); ?>
					</h3>
					<div class="cctor-terms">
						<p style="font-size: 14px;">
							<?php
							echo esc_html(
								sprintf(
									/* translators: 1: coupon title, 2: expiration date. */
									__( '%1$s expired on %2$s and is not showing to visitors.', 'coupon-creator' ),
									get_the_title( $coupon_id ),
									$expiration_date
								)
							);
							?>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

}
