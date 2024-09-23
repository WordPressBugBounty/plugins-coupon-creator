<?php
namespace Pngx\Duplicate;

/**
 * Class Post_Types
 *
 * @since   3.1
 *
 * @package Pngx\Duplicate
 */
class Post_Types extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations and registers the required filters.
	 *
	 * @since 3.2.0
	 */
	public function register() {
		$this->container->singleton( 'pngx.duplicate.post_types', $this );
		$this->container->singleton( static::class, $this );
	}

	/**
	 * Duplicate
	 */
	public function duplicate( $post_type ) {
		global $wpdb;

		$post_id = ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
		$post    = get_post( $post_id );

		// If post is empty then kick out.
		if ( empty( $post ) ) {
			wp_die( 'Process creation failed, could not find original post: ' . $post_id );
		}

		/**
		 * Fires before duplicating a post type.
		 *
		 * @since 3.1
		 *
		 * @param int $post_id The post id for the post type being duplicated.
		 * @param string $post_type The name of the post type.
		 */
		do_action( 'pngx_before_duplicate_post_type', $post_id, $post_type );

		$current_user    = wp_get_current_user();
		$new_post_author = $current_user->ID;

		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'publish',
			'post_title'     => $post->post_title,
			'post_type'      => $post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
		);

		$new_post_id = wp_insert_post( $args );

		// Add Taxonomies
		$taxonomies = get_object_taxonomies( $post->post_type ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
		foreach ( $taxonomies as $taxonomy ) {
			$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
			wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
		}

		// Duplicate Custom Fields
		$post_field_keys = get_post_custom_keys( $post_id );
		foreach ( $post_field_keys as $meta_name ) {
			$meta_value = get_post_meta( $post_id, $meta_name, true );
			update_post_meta( $new_post_id, $meta_name, $meta_value );
		}

		/**
		 * Fires after duplicating a post type.
		 *
		 * @since 3.1
		 *
		 * @param int $new_post_id The new post id for the post type duplicated.
		 * @param int $post_id The post id for the post type being duplicated.
		 * @param string $post_type The name of the post type.
		 */
		do_action( 'pngx_after_duplicate_post_type', $new_post_id, $post_id, $post_type );

		// Redirect to Edit Screen
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );

		exit;
	}
}