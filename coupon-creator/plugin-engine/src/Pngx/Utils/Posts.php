<?php


/**
 * Post Utilities
 *
 * @since 4.0.0
 *
 */
class Pngx__Utils__Posts {

	/**
	 * Helper function for getting Post Id. Accepts null or a post id. If no $post object exists, returns false to avoid a PHP NOTICE
	 *
	 * @since 4.0.0
	 *
	 * @param int $post (optional)
	 *
	 * @return int post ID or False
	 */
	public static function post_id_helper( $post = null ) {
		if ( ! is_null( $post ) && is_numeric( $post ) > 0 ) {
			return (int) $post;
		} elseif ( is_object( $post ) && ! empty( $post->ID ) ) {
			return (int) $post->ID;
		} else {
			if ( ! empty( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof WP_Post ) {
				return get_the_ID();
			} else {
				return false;
			}
		}
	}
}
