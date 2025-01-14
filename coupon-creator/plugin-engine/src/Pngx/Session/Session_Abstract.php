<?php
/**
 * Class Session
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 */

namespace Pngx\Session;

use Pngx__Cache;

/**
 * Class Session
 *
 * Based off WooCommerce's WC_Session_Handler.
 *
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 */
abstract class Session_Abstract implements Session_Interface {

	/**
	 * Cache Name
	 *
	 * @since 4.0.0
	 *
	 * @var string The cache name.
	 */
	protected $cache_name = 'pngx_session';

	/**
	 * Cache Group Name
	 *
	 * @since 4.0.0
	 *
	 * @var string The cache name.
	 */
	protected $cache_group_name = 'pngx_sessions_group';

	/**
	 * Session Data.
	 *
	 * @since 4.0.0
	 *
	 * @var array<mixed|mixed> The associative data array for the session.
	 */
	protected $data = [];

	/**
	 * Expiration Date.
	 *
	 * @since 4.0.0
	 *
	 * @var string The expiration timestamp.
	 */
	protected $expiration_timestamp;

	/**
	 * Expiring Soon Date.
	 *
	 * @since 4.0.0
	 *
	 * @var string The expiring soon timestamp.
	 */
	protected $expiring_soon_timestamp;

	/**
	 * Prefix for the nonce.
	 *
	 * @since 4.0.0
	 *
	 * @var string The prefix name of the nonce.
	 */
	protected $nonce_prefix = 'pngx';

	/**
	 * Name of table for the session data.
	 *
	 * @since 4.0.0
	 *
	 * @var string Table name for session data.
	 */
	protected $table_name_suffix = 'pngx_sessions';

	/**
	 * Name of table for the session data.
	 *
	 * @since 4.0.0
	 *
	 * @var string Table name for session data.
	 */
	protected $table_name;

	/**
	 * User ID.
	 *
	 * @since 4.0.0
	 *
	 * @var int The user id for the session.
	 */
	protected $user_id;

	/**
	 * Unique ID.
	 *
	 * @since 4.0.0
	 *
	 * @var int The unique id for the session.
	 */
	protected $unique_id;

	/**
	 * When the session as data unsaved.
	 *
	 * @since 4.0.0
	 *
	 * @var boolean Whether there is data unsaved.
	 */
	protected $unsaved = false;

	/**
	 * Pngx cache
	 *
	 * @since 4.0.0
	 *
	 * @var Pngx__Cache $cache The class handler for pngx cache.
	 */
	protected $cache;

	/**
	 * @inheritDoc
	 */
	public abstract function has_session();

	/**
	 * @inheritDoc
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * @inheritDoc
	 */
	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * @inheritDoc
	 */
	public function __isset( $key ) {
		return isset( $this->data[ sanitize_key( $key ) ] );
	}

	/**
	 * @inheritDoc
	 */
	public function __unset( $key ) {
		if ( isset( $this->data[ $key ] ) ) {
			unset( $this->data[ $key ] );
			$this->unsaved = true;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function get( $key, $default = null ) {
		$key = sanitize_key( $key );

		return isset( $this->data[ $key ] ) ? maybe_unserialize( $this->data[ $key ] ) : $default;
	}

	/**
	 * @inheritDoc
	 */
	public function set( $key, $value ) {
		if ( $value !== $this->get( $key ) ) {
			$key = sanitize_key( $key );
			$this->data[ $key ] = maybe_serialize( $value );
			$this->unsaved      = true;
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function set_table_name() {
		$this->table_name = $GLOBALS['wpdb']->prefix . $this->table_name_suffix;
	}

	/**
	 * @inheritDoc
	 */
	public function get_table_name() {
		return $this->table_name;
	}

	/**
	 * Gets the cache prefix.
	 *
	 * @return string The cache prefix set in the class.
	 */
	public function get_cache_prefix() {
		return $this->cache->get_cache_prefix( $this->cache_name );
	}

	/**
	 * Gets the cache prefix.
	 *
	 * @return string The cache prefix set in the class.
	 */
	public function get_cache_group() {
		return $this->cache_group_name;
	}

	/**
	 * @inheritDoc
	 */
	public function generate_unique_id() {
		$unique_id = '';

		if ( is_user_logged_in() ) {
			$unique_id = strval( get_current_user_id() );
		}

		if ( empty( $unique_id ) ) {
			require_once( ABSPATH . 'wp-includes/class-phpass.php' );
			$hasher    = new \PasswordHash( 8, false );
			$unique_id = md5( $hasher->get_random_bytes( 32 ) );
		}

		return $unique_id;
	}

	/**
	 * @inheritDoc
	 */
	public function get_user_id() {
		return $this->user_id;
	}

	/**
	 * Checks if this is an auto-generated user ID.
	 *
	 * @param string|int $customer_id user ID to check.
	 *
	 * @return bool Whether customer ID is randomly generated.
	 */
	protected function is_user_guest( $user_id ) {
		$user_id = strval( $user_id );

		if ( empty( $user_id ) ) {
			return true;
		}

		if ( 't_' === substr( $user_id, 0, 2 ) ) {
			return true;
		}

		/**
		 * Legacy checks. This is to handle sessions that were created from a previous release.
		 * Maybe we can get rid of them after a few releases.
		 */

		// Almost all random $user_ids will have some letters in it, while all actual ids will be integers.
		if ( strval( (int) $user_id ) !== $user_id ) {
			return true;
		}

		// Performance hack to potentially save a DB query, when same user as $user_id is logged in.
		if ( is_user_logged_in() && strval( get_current_user_id() ) === $user_id ) {
			return false;
		}

		return false;
	}

	/**
	 * Update nonce for logged out to ensure they have a unique nonce to manage a cart and more using the unique ID.
	 * Runs on 'wp_verify_nonce()' and 'wp_create_nonce()'.
	 *
	 * @since 4.0.0
	 *
	 * @param int    $uid    User ID.
	 * @param string $action The nonce action.
	 *
	 * @return int|string A unique ID.
	 */
	public function maybe_update_nonce_of_logged_out_user( $uid, $action ) {
		if ( str_starts_with( $action, $this->nonce_prefix ) ) {
			return $this->has_session() && $this->user_id ? $this->user_id : $uid;
		}

		return $uid;
	}

	/**
	 * @inheritDoc
	 */
	public function get_session_id() {
		$unique_id = '';

		if ( $this->has_session() && $this->unique_id ) {
			return $this->unique_id;
		}

		if ( is_user_logged_in() ) {
			return (string) get_current_user_id();
		}

		return $unique_id;
	}

	/**
	 * @inheritDoc
	 */
	public function get_session_data() {
		return $this->has_session() ?
			(array) $this->get_session( $this->user_id, [] ) :
			[];
	}

	/**
	 * @inheritDoc
	 */
	public function get_session( $user_id, $default = false ) {
		global $wpdb;

		if ( defined( 'WP_SETUP_CONFIG' ) ) {
			return false;
		}

		// Use cache class to attempt to get session.
		$value = $this->cache->get( $this->get_cache_prefix() . $user_id, $this->cache_group_name );

		if ( false === $value ) {
			$query = $wpdb->prepare( "
				SELECT session_value
				FROM {$this->get_table_name()}
				WHERE session_key = %s",
				$user_id
			);
			$value = $wpdb->get_var( $query );

			if ( is_null( $value ) ) {
				$value = $default;
			}

			$cache_duration = $this->expiration_timestamp - time();
			if ( 0 < $cache_duration ) {
				$this->cache->get( $this->get_cache_prefix() . $user_id, $this->cache_group_name );
			}
		}

		return maybe_unserialize( $value );
	}

	/**
	 * @inheritDoc
	 */
	public function save_data( $logged_out_key = 0 ) {
		// Only save if there is something to save.
		if ( ! $this->unsaved || ! $this->has_session() ) {
			return;
		}

		global $wpdb;

		// Prepare session data for saving.
		$session_query = $wpdb->prepare( "
			INSERT INTO {$this->get_table_name()} (`session_key`, `session_value`, `session_expiry`)
			VALUES (%s, %s, %d)
            ON DUPLICATE KEY
            UPDATE `session_value` = VALUES(`session_value`), `session_expiry` = VALUES(`session_expiry`)
            ",
			$this->user_id,
			maybe_serialize( $this->data ),
			$this->expiration_timestamp
		);

		$response = $wpdb->query( $session_query );
		// If Unknown error 1146 change option and display notice to run the custom table install.
		if ( ! $response ) {
			$last_error = $wpdb->last_error;
			if ( $last_error === 'Unknown error 1146' ) {
				update_option( 'pngx_database_missing_tables', true );
			}
		}

		$this->cache->set( $this->get_cache_prefix() . $this->user_id, $this->data, $this->expiration_timestamp - time(), $this->cache_group_name );

		$this->unsaved = false;
		if (
			get_current_user_id() != $logged_out_key
			&&
			! is_object( get_user_by( 'id', $logged_out_key ) )
		) {
			$this->delete_session( $logged_out_key );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function set_expiration_timestamp() {
		/**
		 * Filter the Session Expiration Date.
		 *
		 * @since 4.0.0
		 *
		 * @param int The time in seconds to set the expiring soon date, default 48 hours.
		 */
		$expiration_timestamp       = (int) apply_filters( 'pngx_expiration_timestamp', HOUR_IN_SECONDS * 48 );
		$this->expiration_timestamp = time() + $expiration_timestamp;

		/**
		 * Filter the Session Expiring Soon Date.
		 *
		 * @since 4.0.0
		 *
		 * @param int The time in seconds to set the expiring soon date, default 47 hours.
		 */
		$expiring_soon_timestamp       = (int) apply_filters( 'pngx_expiring_soon_timestamp', $expiration_timestamp - HOUR_IN_SECONDS );
		$this->expiring_soon_timestamp = time() + $expiring_soon_timestamp;
	}

	/**
	 * @inheritDoc
	 */
	public function get_expiration_timestamp() {
		return $this->expiration_timestamp;
	}

	/**
	 * @inheritDoc
	 */
	public function get_expiring_soon_timestamp() {
		return $this->expiring_soon_timestamp;
	}

	/**
	 * @inheritDoc
	 */
	public function update_session_timestamp( $user_id, $timestamp ) {
		global $wpdb;

		$wpdb->update(
			$this->get_table_name(),
			[ 'session_expiry' => $timestamp, ],
			[ 'session_key' => $user_id, ],
			[ '%d', ]
		);
	}

	/**
	 * @inheritDoc
	 */
	public function cleanup_sessions() {
		global $wpdb;

		$query = $wpdb->prepare( "
			DELETE FROM {$this->get_table_name()}
			WHERE session_expiry < %d",
			time()
		);
		$wpdb->query( $query );

		$this->cache->invalidate_cache_group( $this->cache_group_name );
	}

	/**
	 * @inheritDoc
	 */
	public function delete_session( $user_id ) {
		global $wpdb;

		$this->cache->delete( $this->get_cache_prefix() . $user_id, $this->cache_group_name );

		$wpdb->delete(
			$this->get_table_name(),
			array(
				'session_key' => $user_id,
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function forget_session() {
		/**
		 * Hook to run when forgetting a session.
		 *
		 * Session cookies are hooked in here to be removed.
		 *
		 * @since 4.0.0
		 */
		do_action( 'pngx_forget_session', $this );

		$this->data    = [];
		$this->unsaved = false;
		$this->user_id = $this->generate_unique_id();
	}

	/**
	 * @inheritDoc
	 */
	public function destroy_session() {
		$this->delete_session( $this->user_id );
		$this->forget_session();
	}
}
