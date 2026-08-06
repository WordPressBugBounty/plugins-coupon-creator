<?php
/**
 * Coupon Creator Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Cctor__Coupon__Main' ) ) {
	return;
}

/**
 * Build a URL to a guide on the Artifex documentation site.
 *
 * Central place for the docs base URL so it can be remapped in one spot (e.g. if the
 * BetterDocs archive base changes). Guides live at
 * https://artifexrouting.com/docs/{plugin-slug}-{guide-slug}/, so pass the full namespaced
 * slug, e.g. cctor_guide_url( 'coupon-creator-getting-started' ).
 *
 * @param string $slug Namespaced doc slug (plugin-slug + '-' + guide-slug), no surrounding slashes.
 *
 * @return string Absolute URL to the guide, trailing-slashed.
 */
function cctor_guide_url( $slug ) {
	$base = apply_filters( 'cctor_docs_base_url', 'https://artifexrouting.com/docs/' );

	return trailingslashit( trailingslashit( $base ) . ltrim( $slug, '/' ) );
}

/**
 * Coupon Type Test
 *
 * Checks type of $postId to determine if it is an Coupon
 *
 * @category Coupons
 *
 * @param int $postId (optional)
 *
 * @return bool true if this post is an Coupon post type
 */
function cctor_is_coupon( $postId = null ) {
	return apply_filters( 'cctor_is_coupon', pngx( 'cctor' )->is_coupon( $postId ), $postId );
}

/**
 * Conditional tag to check if current page is an coupon category page
 *
 * @return bool
 **/
function cctor_is_coupon_category() {
	global $wp_query;
	$cctor_is_coupon_category = ! empty( $wp_query->cctor_is_coupon_category );

	return apply_filters( 'cctor_query_is_coupon_category', $cctor_is_coupon_category );
}

/**
 * Conditional tag to check if current page is displaying coupon query
 *
 * @return bool
 **/
function cctor_is_coupon_query() {
	global $wp_query;
	$cctor_is_coupon_query = ! empty( $wp_query->cctor_is_coupon_query );

	return apply_filters( 'cctor_query_is_coupon_query', $cctor_is_coupon_query );
}

/**
 * Conditional tag to check if current page is displaying coupon taxonomy
 *
 * @return bool
 **/
if ( ! function_exists( 'cctor_is_coupon_taxonomy' ) ) {
	function cctor_is_coupon_taxonomy() {
		global $wp_query;
		$cctor_is_coupon_taxonomy = ! empty( $wp_query->cctor_is_coupon_taxonomy );

		return apply_filters( 'cctor_query_is_coupon_taxonomy', $cctor_is_coupon_taxonomy );
	}
}