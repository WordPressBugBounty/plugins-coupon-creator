<?php
/**
 * Listen for plugin engine connected post types and update their timestamps
 *
 *  * - using Pngx__Cache_Listener from The Events Calendar as the basis
 */
class Pngx__Cache_Listener {

	/**
	 * The name of the trigger that will be fired when a post is saved.
	 */
	const TRIGGER_SAVE_POST = 'save_post';

	private static $instance = null;
	private        $cache    = null;

	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->cache = new Pngx__Cache();
	}

	/**
	 * Run the init functionality (like add_hooks).
	 *
	 * @return void
	 */
	public function init() {
		$this->add_hooks();
	}

	/**
	 * Add the hooks necessary.
	 *
	 * @return void
	 */
	private function add_hooks() {
		add_action( 'save_post', array( $this, 'save_post' ), 0, 2 );
		add_action( 'updated_option', array( $this, 'update_last_save_post' ), 10, 3 );
	}

	/**
	 * Run the caching functionality that is executed on save post.
	 *
	 * @param int     $post_id The post_id.
	 * @param WP_Post $post    The current post object being saved.
	 */
	public function save_post( $post_id, $post ) {
		if ( in_array( $post->post_type, Pngx__Main::instance()->get_post_types() ) ) {
			$this->cache->set_last_occurrence( 'save_post' );
		}
	}

	/**
	 * Run the caching functionality that is executed on saving options.
	 *
	 * @see 'updated_option'
	 *
	 * @param string $option_name Name of the updated option.
	 * @param mixed  $old_value   The old option value.
	 * @param mixed  $value       The new option value.
	 */
	public function update_last_save_post( $option_name, $old_value, $value ) {
		$triggers = array(
			'permalink_structure',
			'rewrite_rules',
		);
		if ( in_array( $option_name, $triggers, true ) ) {
			$this->cache->set_last_occurrence( 'save_post' );
		}
	}

	/**
	 * For any hook that doesn't need any additional filtering
	 *
	 * @param $method
	 * @param $args
	 */
	public function __call( $method, $args ) {
		$this->cache->set_last_occurrence( $method );
	}

	/**
	 * Instance method of the cache listener.
	 *
	 * @return Pngx__Cache_Listener
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = self::create_listener();
		}

		return self::$instance;
	}

	/**
	 * Create a cache listener.
	 *
	 * @return Pngx__Cache_Listener
	 */
	private static function create_listener() {
		$listener = new self();
		$listener->init();

		return $listener;
	}
}
