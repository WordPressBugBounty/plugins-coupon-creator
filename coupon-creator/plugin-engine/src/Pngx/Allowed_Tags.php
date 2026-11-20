<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Allowed Tags in Fields
 *
 *  Used with kses();
 */
class Pngx__Allowed_Tags {

	/**
	 * Filters HTML down to an allowed-tags string such as `<p><img><a>`.
	 *
	 * Replaces strip_tags( $content, $tags ): strip_tags keeps every attribute
	 * on an allowed tag, so `<img onerror=...>` got through. This keeps the same
	 * tags, with the attributes and URL protocols wp_kses_post() allows for each
	 * one. A tag outside that list, such as `<style>`, is kept with no attributes.
	 *
	 * @since 4.0.12
	 *
	 * @param mixed $content HTML to filter.
	 * @param mixed $tags    Allowed tags, as a string of `<tag>` names.
	 *
	 * @return string
	 */
	public static function kses( $content, $tags ) {
		$post_tags = wp_kses_allowed_html( 'post' );
		$allowed   = array();

		preg_match_all( '/<([a-z][a-z0-9]*)>/i', is_string( $tags ) ? $tags : '', $matches );

		foreach ( $matches[1] as $tag ) {
			$tag             = strtolower( $tag );
			$allowed[ $tag ] = isset( $post_tags[ $tag ] ) ? $post_tags[ $tag ] : array();
		}

		return wp_kses( (string) $content, $allowed );
	}

	/*
	* Allowed Tags for content without links support
	*/
	public static function title() {

		$terms_tags = '<b><br><em><i><span><strong><sub><sup>';

		return $terms_tags;

	}

	/*
	* Allowed Tags for content
	*/
	public static function content() {

		$terms_tags = '<h1><h2><h3><h4><h5><h6><p><blockquote><div><pre><code><span><br><b><strong><em><img><del><ins><sub><sup><ul><ol><li><hr><i><button><caption><cite><datalist><dd><dl><dt><figcaption><figure><a><style>';

		return $terms_tags;

	}

	/*
	* Allowed Tags for content without link support
	*/
	public static function content_no_link() {

		$terms_tags = '<h1><h2><h3><h4><h5><h6><p><blockquote><div><pre><code><span><br><b><strong><em><img><del><ins><sub><sup><ul><ol><li><hr><i><button><caption><cite><datalist><dd><dl><dt><figcaption><figure><style>';

		return $terms_tags;

	}

}