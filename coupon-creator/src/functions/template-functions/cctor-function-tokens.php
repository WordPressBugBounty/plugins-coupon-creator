<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Coupon Creator design tokens
 *
 * Token-driven templates (ticket and everything that follows it) read their colour
 * from CSS custom properties set on the coupon container, instead of scattering
 * inline styles across individual elements. This keeps per-coupon colour working
 * exactly as before while letting the stylesheet own the design.
 *
 * @since 3.6.0
 */

/**
 * Normalise a user-supplied colour to a 6-digit hex value.
 *
 * @since 3.6.0
 *
 * @param string $color Colour value from post meta or options.
 *
 * @return string|false Six-digit hex without the leading hash, or false when unusable.
 */
function cctor_normalize_hex( $color ) {

	if ( ! is_string( $color ) ) {
		return false;
	}

	$color = trim( $color );
	$color = ltrim( $color, '#' );

	if ( 3 === strlen( $color ) && ctype_xdigit( $color ) ) {
		return $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
	}

	if ( 6 === strlen( $color ) && ctype_xdigit( $color ) ) {
		return $color;
	}

	return false;
}

/**
 * Pick a readable ink colour for text sitting on the given background.
 *
 * Uses WCAG relative luminance rather than a naive brightness average, so mid-tone
 * colours such as amber correctly get dark ink while navy gets light ink.
 *
 * @since 3.6.0
 *
 * @param string $background Background colour, hex with or without the leading hash.
 *
 * @return string Hex colour, including the leading hash.
 */
function cctor_contrast_ink( $background ) {

	$dark  = '#16181d';
	$light = '#ffffff';

	$hex = cctor_normalize_hex( $background );

	if ( false === $hex ) {
		return $light;
	}

	$channels = array(
		hexdec( substr( $hex, 0, 2 ) ) / 255,
		hexdec( substr( $hex, 2, 2 ) ) / 255,
		hexdec( substr( $hex, 4, 2 ) ) / 255,
	);

	foreach ( $channels as $i => $channel ) {
		$channels[ $i ] = $channel <= 0.03928
			? $channel / 12.92
			: pow( ( $channel + 0.055 ) / 1.055, 2.4 );
	}

	$luminance = ( 0.2126 * $channels[0] ) + ( 0.7152 * $channels[1] ) + ( 0.0722 * $channels[2] );

	$ink = $luminance > 0.42 ? $dark : $light;

	/**
	 * Filter the computed ink colour for a given background.
	 *
	 * @since 3.6.0
	 *
	 * @param string $ink        The chosen ink colour.
	 * @param string $background The background colour it was computed from.
	 * @param float  $luminance  The computed relative luminance.
	 */
	return apply_filters( 'cctor_contrast_ink', $ink, $background, $luminance );
}

/**
 * Relative luminance of a colour, per WCAG.
 *
 * @since 3.6.0
 *
 * @param string $hex Six-digit hex without the leading hash.
 *
 * @return float
 */
function cctor_relative_luminance( $hex ) {

	$channels = array(
		hexdec( substr( $hex, 0, 2 ) ) / 255,
		hexdec( substr( $hex, 2, 2 ) ) / 255,
		hexdec( substr( $hex, 4, 2 ) ) / 255,
	);

	foreach ( $channels as $i => $channel ) {
		$channels[ $i ] = $channel <= 0.03928
			? $channel / 12.92
			: pow( ( $channel + 0.055 ) / 1.055, 2.4 );
	}

	return ( 0.2126 * $channels[0] ) + ( 0.7152 * $channels[1] ) + ( 0.0722 * $channels[2] );
}

/**
 * Darken an accent until it reads acceptably as text on the light surface.
 *
 * The accent is chosen to sit *behind* white text on the ticket head, so bright
 * hues such as amber and lime are perfectly good there and nearly invisible when
 * reused as link text on white. This returns a darkened variant that clears
 * WCAG AA (4.5:1) against white, leaving already-dark accents untouched.
 *
 * @since 3.6.0
 *
 * @param string $hex Six-digit hex without the leading hash.
 *
 * @return string Hex colour including the leading hash.
 */
function cctor_readable_accent( $hex ) {

	// L <= 0.1833 is the point where contrast against white reaches 4.5:1.
	$target = 0.1833;

	if ( cctor_relative_luminance( $hex ) <= $target ) {
		return '#' . $hex;
	}

	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );

	// Scale the channels down together so the hue is preserved.
	$factor = 1.0;
	for ( $i = 0; $i < 40; $i ++ ) {
		$factor -= 0.025;
		$candidate = sprintf(
			'%02x%02x%02x',
			max( 0, (int) round( $r * $factor ) ),
			max( 0, (int) round( $g * $factor ) ),
			max( 0, (int) round( $b * $factor ) )
		);

		if ( cctor_relative_luminance( $candidate ) <= $target ) {
			return '#' . $candidate;
		}
	}

	return '#16181d';
}

/**
 * Build the CSS custom property declarations for one coupon.
 *
 * The accent comes from the coupon's own deal background, falling back to the
 * site-wide default. The deal ink is computed from that accent so it always
 * stays readable — unless the coupon has an explicit deal text colour saved, in
 * which case the author's choice wins.
 *
 * @since 3.6.0
 *
 * @param int $coupon_id The coupon post ID.
 *
 * @return string Attribute-safe declarations, or an empty string when there is nothing to set.
 */
function cctor_get_token_style( $coupon_id ) {

	$accent = get_post_meta( $coupon_id, 'cctor_colordiscount', true );

	if ( ! cctor_normalize_hex( $accent ) ) {
		$accent = cctor_options( 'cctor_discount_bg_color' );
	}

	$accent_hex = cctor_normalize_hex( $accent );

	if ( false === $accent_hex ) {
		return '';
	}

	// An explicitly saved deal text colour is a deliberate author choice and wins
	// over the computed value. Empty meta means "let the template decide".
	$manual_ink = get_post_meta( $coupon_id, 'cctor_colorheader', true );
	$manual_hex = cctor_normalize_hex( $manual_ink );

	$ink = false !== $manual_hex ? '#' . $manual_hex : cctor_contrast_ink( $accent_hex );

	$tokens = array(
		'--cc-accent'          => '#' . $accent_hex,
		'--cc-accent-ink'      => $ink,
		// For accent-coloured text on the light surface (the print link).
		'--cc-accent-readable' => cctor_readable_accent( $accent_hex ),
	);

	/*
	 * cctor_bordercolor is deliberately NOT mapped here. On the classic template it
	 * paints a solid inner border; the ticket has no such border, and routing it to
	 * the perforation instead turns a saturated brand colour into a garish dashed
	 * line. The perforation stays neutral. Templates that do want it can add it back
	 * through the cctor_coupon_tokens filter below.
	 */

	/**
	 * Filter the design tokens set on a coupon container.
	 *
	 * @since 3.6.0
	 *
	 * @param array $tokens    Map of custom property name to value.
	 * @param int   $coupon_id The coupon post ID.
	 */
	$tokens = apply_filters( 'cctor_coupon_tokens', $tokens, $coupon_id );

	$declarations = '';
	foreach ( $tokens as $property => $value ) {
		// Custom property names and colour values are both constrained here so the
		// result is safe to drop straight into a style attribute.
		if ( ! preg_match( '/^--[a-z0-9-]+$/i', $property ) ) {
			continue;
		}
		$declarations .= $property . ':' . preg_replace( '/[^a-z0-9#(),.%\s-]/i', '', (string) $value ) . ';';
	}

	return $declarations;
}
