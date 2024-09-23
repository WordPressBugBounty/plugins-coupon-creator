<?php
/**
 * Cron Setup.
 *
 * @since   4.0.0
 *
 * @package Pngx\Session
 */

namespace Pngx\Install;

/**
 * Class Cron
 *
 * @since   4.0.0
 *
 * @package Pngx\Install
 */
class Cron  {

	/**
	 * The session cron hook.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	public static $session_cron_hook = 'pngx_cleanup_sessions';

	/**
	 * Create crons for Plugin Engine.
	 *
	 * @since 4.0.0
	 */
	public static function create_crons() {
		// Clear cron jobs to reduce conflict.
		wp_clear_scheduled_hook( static::$session_cron_hook );

		//Setup crons.
		wp_schedule_event( time() + ( 6 * HOUR_IN_SECONDS ), 'twicedaily', static::$session_cron_hook );
	}
}