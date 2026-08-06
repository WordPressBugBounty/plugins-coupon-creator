<?php
/**
 * Modern Ticket Coupon Type
 *
 * Template override @ [your-theme]/cctor-coupons/type/ticket.php
 *
 * Renders through the shared coupon actions rather than writing markup for each
 * field directly. That matters: when Coupon Creator Pro is active it has already
 * swapped its own richer implementations onto these same actions, so this one
 * view serves free and Pro without being written twice.
 *
 * Two things do not travel on those actions and so are handled explicitly: the
 * colorbox popup anchor, which Pro builds inside an outer wrap this view replaces,
 * and the field-driven add-ons, which opt in through `cctor_ticket_extra_fields`.
 *
 * @package CouponCreator
 *
 * @since 3.6.0
 *
 * @var int    $coupon_id
 * @var object $coupon_expiration
 * @var string $coupon_align
 * @var bool   $is_print_view
 * @var array  $fields
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$is_print_view = isset( $is_print_view ) ? (bool) $is_print_view : false;
$coupon_align  = isset( $coupon_align ) ? $coupon_align : '';

$business  = get_post_meta( $coupon_id, 'cctor_business_name', true );
$token_css = cctor_get_token_style( $coupon_id );
$fields    = isset( $fields ) ? (array) $fields : array();

/*
 * Popup print view.
 *
 * Pro builds the colorbox anchor as part of `get_cctor_pro_outer_coupon_wrap()`, which
 * the ticket does not use -- it writes its own container. Without this the popup option
 * degrades to unclickable plain text, because `cctor_pro_show_link()` deliberately emits
 * only the label when `open_in_iframe` is set and expects the wrap link to do the work.
 */
$cctor_ticket_popup_open  = '';
$cctor_ticket_popup_close = '';

if ( ! $is_print_view && function_exists( 'cctor_pro_get_link' ) ) {
	$cctor_ticket_links = cctor_pro_get_link( $coupon_id );

	if ( ! empty( $cctor_ticket_links['popup_markup_open'] ) ) {
		$cctor_ticket_popup_open  = $cctor_ticket_links['popup_markup_open'];
		$cctor_ticket_popup_close = isset( $cctor_ticket_links['popup_markup_close'] )
			? $cctor_ticket_links['popup_markup_close']
			: '';
	}
}

/**
 * Filter the field IDs the ticket renders through `Pngx__Fields`.
 *
 * The ticket writes its own markup instead of looping every field, so field-driven
 * add-ons -- click-reveal, dynamic codes -- have nowhere to land. They have no action
 * of their own to hook, so each one opts in here by ID and is rendered in the body.
 * Deal, terms and expiration are deliberately absent: the ticket already fires their
 * shared actions below, and listing them here would render them twice.
 *
 * @since 3.6.0
 *
 * @param array<string> $field_ids Field IDs to render inside the ticket body.
 * @param int           $coupon_id The coupon post ID.
 */
$cctor_ticket_extra_fields = (array) apply_filters( 'cctor_ticket_extra_fields', array(), $coupon_id );

$container_classes = array(
	'coupon_creator_' . $coupon_id,
	'coupon-creator-' . $coupon_id,
	'type-cctor_coupon',
	'cctor_coupon_container',
	'cctor-coupon-container',
	'cctor-ticket',
);

$category_class = cctor_return_coupon_categories( $coupon_id );
if ( $category_class ) {
	$container_classes[] = $category_class;
}

if ( $coupon_align ) {
	$container_classes[] = $coupon_align;
}

if ( $is_print_view ) {
	$container_classes[] = 'cctor-ticket-print';
}

// The deal sits on the ticket head, which carries the accent itself, so the
// per-element inline colours are suppressed for the duration of this template.
$cctor_ticket_drop_deal_style = static function () {
	return '';
};
add_filter( 'cctor_deal_inline_style', $cctor_ticket_drop_deal_style, 99 );
?>
<!--start coupon container here -->
<?php echo $cctor_ticket_popup_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped in cctor_pro_get_link(). ?>
<div id="coupon_creator_<?php echo esc_attr( $coupon_id ); ?>"
	class="<?php echo esc_attr( implode( ' ', $container_classes ) ); ?>"
	<?php echo $token_css ? 'style="' . esc_attr( $token_css ) . '"' : ''; ?>>

	<?php
	/**
	 * Before Core Shortcode Individual Coupon Wrap
	 *
	 * @param int $coupon_id
	 */
	do_action( 'cctor_before_coupon_inner_wrap', $coupon_id );
	?>

	<div class="cctor-ticket-shadow">
		<div class="cctor-ticket-card">

			<div class="cctor-ticket-head">
				<?php if ( $business ) : ?>
					<p class="cctor-ticket-eyebrow"><?php echo esc_html( $business ); ?></p>
				<?php endif; ?>
				<?php
				/**
				 * Coupon Deal Hook
				 *
				 * @param int $coupon_id
				 */
				do_action( $is_print_view ? 'cctor_print_coupon_deal' : 'cctor_coupon_deal', $coupon_id );
				?>
			</div>

			<div class="cctor-ticket-body">
				<?php
				/**
				 * Coupon Terms Hook
				 *
				 * @param int $coupon_id
				 */
				do_action( $is_print_view ? 'cctor_print_coupon_terms' : 'cctor_coupon_terms', $coupon_id );

				// Field-driven add-ons that opted in above -- reveal, dynamic codes.
				if ( $cctor_ticket_extra_fields && $fields && class_exists( 'Pngx__Fields' ) ) {
					foreach ( $fields as $cctor_ticket_field ) {

						if (
							empty( $cctor_ticket_field['id'] )
							|| ! in_array( $cctor_ticket_field['id'], $cctor_ticket_extra_fields, true )
						) {
							continue;
						}

						Pngx__Fields::display_field(
							$cctor_ticket_field,
							$coupon_id,
							$fields,
							array( 'expiration' => $coupon_expiration )
						);
					}
				}
				?>

				<div class="cctor-ticket-foot">
					<?php
					/**
					 * Coupon Expiration Display Hook
					 *
					 * @param int    $coupon_id
					 * @param object $coupon_expiration
					 */
					do_action(
						$is_print_view ? 'cctor_print_coupon_expiration' : 'cctor_coupon_expiration',
						$coupon_id,
						$coupon_expiration
					);

					if ( $is_print_view ) {
						/**
						 * Click to Print Hook
						 *
						 * @param int $coupon_id
						 */
						do_action( 'cctor_click_to_print_coupon', $coupon_id );
					} else {
						/**
						 * Individual Coupon Link
						 *
						 * @param int $coupon_id
						 */
						do_action( 'cctor_coupon_link', $coupon_id );
					}
					?>
				</div>
			</div>

		</div>
	</div>

	<?php
	/*
	 * The shared click region: Pro's view counter, the click-to-print button, and the
	 * colorbox script loader all hang off this action, and every other type view fires
	 * it. It sits outside the card because the card is masked -- anything inside it gets
	 * clipped at the notches.
	 *
	 * `cctor_pro_show_link` is hooked to both this action and `cctor_coupon_link`, which
	 * the ticket fires in its foot. That placement is the design, so the duplicate here
	 * is suppressed for the duration of the call and restored at its original priority.
	 */
	$cctor_ticket_pro_link_priority = has_action( 'cctor_click_actions', 'cctor_pro_show_link' );

	if ( false !== $cctor_ticket_pro_link_priority ) {
		remove_action( 'cctor_click_actions', 'cctor_pro_show_link', $cctor_ticket_pro_link_priority );
	}

	/**
	 * Coupon Click Actions
	 *
	 * @param int    $coupon_id
	 * @param object $coupon_expiration
	 */
	do_action( 'cctor_click_actions', $coupon_id, $coupon_expiration );

	if ( false !== $cctor_ticket_pro_link_priority ) {
		add_action( 'cctor_click_actions', 'cctor_pro_show_link', $cctor_ticket_pro_link_priority, 1 );
	}
	?>
</div><!--end #cctor_coupon_container -->
<?php
echo $cctor_ticket_popup_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static closing tag from cctor_pro_get_link().

remove_filter( 'cctor_deal_inline_style', $cctor_ticket_drop_deal_style, 99 );
