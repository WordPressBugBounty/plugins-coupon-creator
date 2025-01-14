<?php
Use Pngx\Utilities\Arr;

/**
 * Class Pngx__Context
 *
 * @since 3.0
 *
 */
class Pngx__Context {
	/**
	 * Whether we are currently creating a new post, a post of post type(s) or not.
	 *
	 * @since 3.0
	 *
	 * @param null $post_type The optional post type to check.
	 *
	 * @return bool Whether we are currently creating a new post, a post of post type(s) or not.
	 */
	public function is_new_post( $post_type = null ) {
		global $pagenow;
		$is_new = 'post-new.php' === $pagenow;

		return $is_new && $this->is_editing_post( $post_type );
	}

	/**
	 * Whether we are currently editing a post(s), post type(s) or not.
	 *
	 * @since 3.0
	 *
	 * @param null|array|string|int $post_or_type A post ID, post type, an array of post types or post IDs, `null`
	 *                                            to just make sure we are currently editing a post.
	 *
	 * @return bool
	 */
	public function is_editing_post( $post_or_type = null ) {
		global $pagenow;
		$is_new  = 'post-new.php' === $pagenow;
		$is_post = 'post.php' === $pagenow;
		$is_editing = 'edit.php' === $pagenow;

		if ( ! ( $is_new || $is_post || $is_editing ) ) {
			return false;
		}

		if ( null !== $post_or_type ) {
			$lookup = array( $_GET, $_POST, $_REQUEST );

			$current_post = Arr::get_in_any( $lookup, 'post', get_post() );

			if ( is_numeric( $post_or_type ) ) {

				$post = $is_post ? get_post( $post_or_type ) : null;

				return ! empty( $post ) && $post == $current_post;
			}

			$post_types = is_array( $post_or_type ) ? $post_or_type : array( $post_or_type );

			$post = $is_post ? $current_post : null;

			if ( count( array_filter( $post_types, 'is_numeric' ) ) === count( $post_types ) ) {
				return ! empty( $post ) && in_array( $post->ID, $post_types );
			}

			if ( $is_post && $post instanceof WP_Post ) {
				$post_type = $post->post_type;
			} else {
				$post_type = Arr::get_in_any( $lookup, 'post_type', 'post' );
			}

			return (bool) count( array_intersect( $post_types, array( $post_type ) ) );
		}

		return $is_new || $is_post;
	}

	/**
	 * Whether the current request is for a PHP-rendered initial state or not.
	 *
	 * This method is a shortcut to make sure we're not doing an AJAX, REST or Cron request.
	 *
	 * @since 4.1.0
	 *
	 * @return bool Whether the current request is for a PHP-rendered initial state or not.
	 */
	public function doing_php_initial_state() {
		return ! $this->doing_rest() && ! $this->doing_ajax() && ! $this->doing_cron();
	}

	/**
	 * Checks whether the current request is a REST API one or not.
	 *
	 * @since 4.1.0
	 *
	 * @return bool Whether the current request is a REST API one or not.
	 */
	public function doing_rest() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}

	/**
	 * Helper function to indicate whether the current execution context is AJAX.
	 *
	 * This method exists to allow us test code that behaves differently depending on the execution
	 * context.
	 *
	 * @since 4.1.0
	 *
	 * @return boolean
	 */
	public function doing_ajax() {
		return function_exists( 'wp_doing_ajax' )
			? wp_doing_ajax()
			: defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Checks whether the context of the current HTTP request is a Cron one or not.
	 *
	 * @since 4.1.0
	 *
	 * @return bool Whether the context of the current HTTP request is a Cron one or not.
	 */
	public function doing_cron() {
		return function_exists( 'wp_doing_cron' )
			? wp_doing_cron()
			: defined( 'DOING_CRON' ) && DOING_CRON;
	}

}
