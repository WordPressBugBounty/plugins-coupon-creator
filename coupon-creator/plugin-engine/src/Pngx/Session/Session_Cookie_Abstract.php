<?php
/**
 * Class Session
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 */

namespace Pngx\Session;

/**
 * Class Session
 *
 * Based off WooCommerce's WC_Session_Handler.
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 */
abstract class Session_Cookie_Abstract extends Session_Abstract implements Session_Cookie_Interface {

	/**
	 * Cookie name for the session.
	 *
	 * @since 4.0.0
	 *
	 * @var string Name of the cookie.
	 */
	protected $cookie_name;

	/**
	 * Whether the session has a cookie.
	 *
	 * @since 4.0.0
	 *
	 * @var boolean True or false if a cookie for the session exists, defaults to false.
	 */
	protected $has_cookie = false;

	/**
	 * @inheritDoc
	 */
	public function has_session() {
		return isset( $_COOKIE[ $this->cookie_name ] ) || $this->has_cookie || is_user_logged_in();
	}

	/**
	 * Check if cookie session should be secure.
	 *
	 * @since 4.0.0
	 *
	 * @return bool Whether to use a secure cookie.
	 */
	protected function use_secure_cookie() {
		return apply_filters( 'pngx_session_use_secure_cookie', strstr( get_option( 'home' ), 'https:' ) && is_ssl() );
	}

	/**
	 * @inheritDoc
	 */
	public function init_session_cookie() {
		$cookie = $this->get_session_cookie();

		if ( $cookie ) {
			$this->user_id                 = $cookie[0];
			$this->expiration_timestamp    = $cookie[1];
			$this->expiring_soon_timestamp = $cookie[2];
			$this->has_cookie              = true;
			$this->data                    = $this->get_session_data();

			if ( ! $this->is_session_cookie_valid() ) {
				$this->destroy_session();
				$this->set_expiration_timestamp();
			}

			// If the user logs in, update session.
			if ( is_user_logged_in() && strval( get_current_user_id() ) !== $this->user_id ) {
				$guest_session_id = $this->user_id;
				$this->user_id    = strval( get_current_user_id() );
				$this->unsaved    = true;
				$this->save_data( $guest_session_id );
				$this->set_customer_session_cookie( true );
			}

			// Update session if its close to expiring.
			if ( time() > $this->expiring_soon_timestamp ) {
				$this->set_expiration_timestamp();
				$this->update_session_timestamp( $this->user_id, $this->expiration_timestamp );
			}
		} else {
			$this->set_expiration_timestamp();
			$this->user_id = $this->generate_unique_id();
			$this->data    = $this->get_session_data();
		}
	}

	/**
	 * Set the cookie name with a filter to be able to modify it.
	 *
	 * @since 4.0.0
	 */
	protected function set_cookie_name() {

		/**
		 * Allow filtering of the session cookie name.
		 *
		 * The cookie name is prepended with wp, cache systems like batcache will not cache pages when set.
		 *
		 * @since 4.0.0
		 *
		 * @param string The default cooke name session.
		 * @param Session_Cookie_Abstract $this The current session object.
		 */
		$this->cookie_name = apply_filters( 'wp_pngx_session_cookie_name', 'pngx_session_' . COOKIEHASH, $this );
	}

	/**
	 * @inheritDoc
	 */
	public function set_customer_session_cookie( $set ) {
		if ( $set ) {
			$to_hash          = $this->user_id . '|' . $this->expiration_timestamp;
			$cookie_hash      = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );
			$cookie_value     = $this->user_id . '||' . $this->expiration_timestamp . '||' . $this->expiring_soon_timestamp . '||' . $cookie_hash;
			$this->has_cookie = true;

			// Cookies set only if called before headers are sent.
			if ( ! isset( $_COOKIE[ $this->cookie_name ] ) || $_COOKIE[ $this->cookie_name ] !== $cookie_value ) {
				pngx_setcookie( $this->cookie_name, $cookie_value, $this->expiration_timestamp, $this->use_secure_cookie(), true );
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function get_cookie_name() {
		return $this->cookie_name;
	}

	/**
	 * @inheritDoc
	 */
	public function get_session_cookie() {
		$cookie_value = isset( $_COOKIE[ $this->cookie_name ] ) ? wp_unslash( $_COOKIE[ $this->cookie_name ] ) : false;

		if ( empty( $cookie_value ) || ! is_string( $cookie_value ) ) {
			return false;
		}

		list( $unique_id, $expiration_timestamp, $expiring_soon_timestamp, $cookie_hash ) = explode( '||', $cookie_value );

		if ( empty( $unique_id ) ) {
			return false;
		}

		// Validate hash.
		$to_hash = $unique_id . '|' . $expiration_timestamp;
		$hash    = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );

		if ( empty( $cookie_hash ) || ! hash_equals( $hash, $cookie_hash ) ) {
			return false;
		}

		return [ $unique_id, $expiration_timestamp, $expiring_soon_timestamp, $cookie_hash ];
	}

	/**
	 * @inheritDoc
	 */
	public function forget_cookie() {
		pngx_setcookie( $this->cookie_name, '', time() - YEAR_IN_SECONDS, $this->use_secure_cookie(), true );
	}

	/**
	 * Checks if session cookie is expired, or belongs to a logged out user.
	 *
	 * @return bool Whether session cookie is valid.
	 */
	private function is_session_cookie_valid() {
		// If session is expired, session cookie is invalid.
		if ( time() > $this->expiration_timestamp ) {
			return false;
		}

		// If user has logged out, session cookie is invalid.
		if ( ! is_user_logged_in() && ! $this->is_user_guest( $this->user_id ) ) {
			return false;
		}

		// Session from a different user is not valid. (Although from a guest user will be valid)
		if ( is_user_logged_in() && ! $this->is_user_guest( $this->user_id ) && strval( get_current_user_id() ) !== $this->user_id ) {
			return false;
		}

		return true;
	}
}
