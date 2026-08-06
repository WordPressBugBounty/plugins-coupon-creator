<?php
/**
 * Compact Coupon Creator Pro promotion card.
 *
 * @package Coupon_Creator
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Compact Coupon Creator Pro promotion card.
 */
class Cctor__Coupon__Admin__Field__Pro_Card {

	/**
	 * Display a compact promotion card.
	 *
	 * @param array $field Field configuration.
	 */
	public static function display( $field = array() ) {
		if ( defined( 'CCTOR_HIDE_UPGRADE' ) && CCTOR_HIDE_UPGRADE ) {
			return;
		}

		$features = isset( $field['features'] ) ? (array) $field['features'] : array();
		$link     = isset( $field['link'] ) ? $field['link'] : 'http://cctor.link/CjZX2';
		?>
		<aside class="cctor-pro-card" aria-label="<?php esc_attr_e( 'Coupon Creator Pro', 'coupon-creator' ); ?>">
			<p class="cctor-pro-card__product"><?php esc_html_e( 'Coupon Creator Pro', 'coupon-creator' ); ?></p>
			<h3><?php esc_html_e( 'Coupons that expire on their own.', 'coupon-creator' ); ?></h3>
			<p><?php esc_html_e( 'Set the rules once, then let Pro expire, renew, and enforce coupon limits for you.', 'coupon-creator' ); ?></p>
			<?php if ( $features ) : ?>
				<ul>
					<?php foreach ( $features as $feature ) : ?>
						<li><?php echo esc_html( $feature ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Explore Coupon Creator Pro', 'coupon-creator' ); ?></a>
		</aside>
		<?php
	}
}
