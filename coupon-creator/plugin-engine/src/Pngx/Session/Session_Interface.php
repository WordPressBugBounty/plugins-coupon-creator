<?php
/**
 * Interface Session
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 */

namespace Pngx\Session;

/**
 * Interface Session_Interface
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 *
 */
interface Session_Interface {

	/**
	 * Magic get method.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $key Name of the key to get.
	 *
	 * @return mixed The value or null if not found.
	 */
	public function __get( $key );

	/**
	 * Magic set method.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $key   Name of the key to set.
	 * @param mixed $value Value to set.
	 */
	public function __set( $key, $value );

	/**
	 * Magic isset method.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $key Name of the key to check.
	 *
	 * @return bool Whether the key is set or not.
	 */
	public function __isset( $key );

	/**
	 * Magic unset method.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $key Name of the key to unset.
	 */
	public function __unset( $key );

	/**
	 * Get a session variable.
	 *
	 * @since 4.0.0
	 *
	 * @param string $key     Name of the key to get.
	 * @param mixed  $default Optional default variable if it is not set.
	 *
	 * @return array<string|mixed> The requested value of the session data or maybe a default.
	 */
	public function get( $key, $default = null );

	/**
	 * Set a session variable.
	 *
	 * @since 4.0.0
	 *
	 * @param string $key   Name of the key to set.
	 * @param mixed  $value Value to set.
	 */
	public function set( $key, $value );

	/**
	 * Get the table name where sessions are stored.
	 *
	 * @since 4.0.0
	 *
	 * @return string The table name.
	 */
	public function get_table_name();

	/**
	 * Get the unique id of the session.
	 *
	 * @since 4.0.0
	 *
	 * @return int The unique session for the user|visitor session,
	 */
	public function get_user_id();

	/**
	 * Return if there is an active session.
	 *
	 * @since 4.0.0
	 */
	public function has_session();

	/**
	 * Get a unique id for the session, use current user id or generate a unique hash.
	 *
	 * Utilizes Portable PHP password hashing framework to generate unique hash.
	 *
	 * @since 4.0.0
	 *
	 * @return string The current id of the user or a hashed string.
	 */
	public function generate_unique_id();

	/**
	 * Get the unique id of the session.
	 *
	 * @since 4.0.0
	 *
	 * @return string The current id of the user or a hashed string.
	 */
	public function get_session_id();

	/**
	 * Get session data.
	 *
	 * @since 4.0.0
	 *
	 * @return array An array of session data.
	 */
	public function get_session_data();

	/**
	 * Get the session for a user.
	 *
	 * @since 4.0.0
	 *
	 * @param string $user_id The user id to get the session for.
	 * @param mixed  $default Default session value.
	 *
	 * @return boolean|array<mixed|mixed> False if on setup or the default value or an array of session data.
	 */
	public function get_session( $user_id, $default = false );

	/**
	 * Save Data
	 *
	 * @since 4.0.0
	 *
	 * @param int $logged_out_key The session ID if user was previously logged out.
	 */
	public function save_data( $logged_out_key = 0 );

	/**
	 * Set session expiration.
	 *
	 * @since 4.0.0
	 *
	 */
	public function set_expiration_timestamp();

	/**
	 * Get the expiration timestamp for session.
	 *
	 * @since 4.0.0
	 *
	 * @return string The expiration timestamp for the session.
	 */
	public function get_expiration_timestamp();

	/**
	 * Get the expiring soon timestamp for session.
	 *
	 * @since 4.0.0
	 *
	 * @return string The expiring soon timestamp for the session.
	 */
	public function get_expiring_soon_timestamp();

	/**
	 * Update the session expiry timestamp.
	 *
	 * @since 4.0.0
	 *
	 * @param string $user_id User ID.
	 * @param int    $timestamp Timestamp to expire the session.
	 */
	public function update_session_timestamp( $user_id, $timestamp );

	/**
	 * Cleanup from database and cache the session data.
	 *
	 * @since 4.0.0
	 */
	public function cleanup_sessions();

	/**
	 * Delete a session from the cache and database.
	 *
	 * @since 4.0.0
	 *
	 * @param int $user_id The user to delete the session for.
	 */
	public function delete_session( $user_id );

	/**
	 * Forget all session data without destroying it.
	 *
	 * @since 4.0.0
	 */
	public function forget_session();

	/**
	 * Destroy all session data.
	 *
	 * @since 4.0.0
	 */
	public function destroy_session();
}
