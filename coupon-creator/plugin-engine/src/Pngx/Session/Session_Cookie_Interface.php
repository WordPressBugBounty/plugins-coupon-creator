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
interface Session_Cookie_Interface {

	/**
	 * Initialize the Session Cookie.
	 *
	 * @since 4.0.0
	 */
	public function init_session_cookie();

	/**
	 * Sets the session cookie on-demand.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $set Should the session cookie be set.
	 */
	public function set_customer_session_cookie( $set );

	/**
	 * Get the cookie name for the session.
	 *
	 * @since 4.0.0
	 *
	 * @return string The cookie name.
	 */
	public function get_cookie_name();

	/**
	 * Get the session cookie when set or return false.
	 *
	 * @since 4.0.0
	 *
	 * A unique ID is required for cookie to be valid.
	 *
	 * @return bool|array<string|mixed> False if not a valid cookie or an array of session information.
	 */
	public function get_session_cookie();

	/**
	 * Forget all cookie data.
	 *
	 * @since 4.0.0
	 */
	public function forget_cookie();
}
