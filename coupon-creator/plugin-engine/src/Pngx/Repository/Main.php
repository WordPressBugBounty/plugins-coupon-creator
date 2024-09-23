<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Class Pngx__Posts__Main
 *
 * based off https://carlalexander.ca/designing-class-manage-wordpress-posts/
 */
class Pngx__Repository__Main {

	/**
	 * WordPress query object.
	 *
	 * @var WP_Query
	 */
	private $query;

	/**
	 * Constructor.
	 *
	 * @param WP_Query $query
	 */
	public function __construct( WP_Query $query ) {
		$this->query = $query;
	}

	/**
	 * Initialize the repository.
	 *
	 * @uses PHP 5.3
	 *
	 * @return self
	 */
	public static function init() {
		return new self( new WP_Query() );
	}

	/**
	 * Find posts written by the given author.
	 *
	 * @param WP_User $author
	 * @param int     $limit
	 *
	 * @return WP_Post[]
	 */
	public function find_by_author( WP_User $author, $limit = 10 ) {
		return $this->find( array(
			'author'         => $author->ID,
			'posts_per_page' => $limit,
		) );
	}

	/**
	 * Find a post using the given post ID.
	 *
	 * @param int $id
	 *
	 * @return WP_Post|null
	 */
	public function find_by_id( $id ) {
		return $this->find_one( array( 'p' => $id ) );
	}

	/**
	 * Save a post into the repository. Returns the post ID or a WP_Error.
	 *
	 * @param array $post
	 *
	 * @return int|WP_Error
	 */
	public function save( array $post ) {
		if ( ! empty( $post['ID'] ) ) {
			return wp_update_post( $post, true );
		}

		return wp_insert_post( $post, true );
	}

	/**
	 * Find all post objects for the given query.
	 *
	 * @param array $query
	 *
	 * @return WP_Post[]
	 */
	private function find( array $query ) {
		$query = array_merge( array(
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		), $query );

		return $this->query->query( $query );
	}

	/**
	 * Find a single post object for the given query. Returns null
	 * if it doesn't find one.
	 *
	 * @param array $query
	 *
	 * @return WP_Post|null
	 */
	private function find_one( array $query ) {
		$query = array_merge( $query, array(
			'posts_per_page' => 1,
		) );

		$posts = $this->find( $query );

		return ! empty( $posts[0] ) ? $posts[0] : null;
	}
}