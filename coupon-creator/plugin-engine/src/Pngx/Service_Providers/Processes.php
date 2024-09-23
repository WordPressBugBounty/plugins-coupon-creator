<?php

/**
 * Class Pngx__Service_Providers__Processes
 *
 * Based off Modern Tribe's Pngx__Service_Providers__Processes
 *
 * @since 3.0
 *
 * Handles the registration and creation of our async process handlers.
 */
class Pngx__Service_Providers__Processes extends tad_DI52_ServiceProvider {

	/**
	 * An array of all handler actions registered in this HTTP request.
	 *
	 * This is an array cache with a request lifetime by design.
	 *
	 * @var array
	 */
	protected $handler_actions;

	/**
	 * An array of all queue actions registered in this HTTP request.
	 *
	 * This is an array cache with a request lifetime by design.
	 *
	 * @var array
	 */
	protected $queue_actions;

	/**
	 * An instance of the context abstraction layer.
	 *
	 * @var Pngx__Context
	 */
	protected $context;

	/**
	 * Hooks the filters and binds the implementations needed to handle processes.
	 */
	public function register() {
		$this->context = pngx( 'pngx.context' );

		// If the context of this request is neither AJAX or Cron bail.
		if ( ! ( $this->context->doing_ajax() || $this->context->doing_cron() ) ) {
			return;
		}

		/** @var Pngx__Feature_Detection $feature_detection */
		$feature_detection         = pngx( 'pngx.feature-detection' );
		$action                    = pngx_get_request_var( 'action', false );
		$testing_for_async_support = $action === $this->get_handler_action( 'Pngx__Process__Tester' );

		 // Dispatch in async mode if testing for it (w/o re-checking) or if async processes are supported.
		if ( $testing_for_async_support || $feature_detection->supports_async_process() ) {
			$this->dispatch_async();

			return;
		}

		$this->dispatch_cron();
	}

	/**
	 * Hooks the correct handler for the action.
	 *
	 * @since 3.0
	 *
	 * @param string $action
	 */
	protected function hook_handler_for( $action ) {
		if ( null === $this->handler_actions ) {
			$handlers = array(
				'Pngx__Process__Tester',
				'Pngx__Process__Post_Thumbnail_Setter',
			);

			/**
			 * Filters the process handler classes the Service Provider should handle.
			 *
			 * All handlers should extend the `Pngx__Process__Handler` base class.
			 *
			 * @since 4.7.12
			 *
			 * @param array $handlers
			 */
			$handlers = array_unique( apply_filters( 'pngx_process_handlers', $handlers ) );

			$this->handler_actions = array_combine(
				$handlers,
				array_map( array( $this, 'get_handler_action' ), $handlers )
			);
		}

		$array_search = array_search( $action, $this->handler_actions, true );

		if ( false === $handler_class = $array_search ) {
			return;
		}

		// the handler will handle the hooking
		$this->container->make( $handler_class );
	}

	/**
	 * Hooks the correct queue for the action.
	 *
	 * @since 3.0
	 *
	 * @param string $action
	 */
	protected function hook_queue_for( $action ) {
		if ( null === $this->queue_actions ) {
			$queues = array(
				'Pngx__Promise',
			);

			/**
			 * Filters the queue processing classes the Service Provider should handle.
			 *
			 * All queues should extend the `Pngx__Process__Queue` base class.
			 *
			 * @since 3.0
			 *
			 * @param array $queues An array of class names, each extending the `Pngx__Process__Queue` base class.
			 */
			$queues = array_unique( apply_filters( 'pngx_process_queues', $queues ) );

			$all_queues_actions = array_combine(
				$queues,
				array_map( array( $this, 'get_queue_action' ), $queues )
			);
		}

		$array_search = array_search( $action, $all_queues_actions, true );

		if ( false === $queue_class = $array_search ) {
			return;
		}

		// the queue will handle the hooking
		$this->container->make( $queue_class );
	}

	/**
	 * Returns the action for the handler.
	 *
	 * @since 3.0
	 *
	 * @param string $handler_class
	 *
	 * @return string
	 */
	protected function get_handler_action( $handler_class ) {
		/** @var Pngx__Process__Handler handler_class */
		return 'pngx_process_' . call_user_func( array( $handler_class, 'action' ) );
	}

	/**
	 * Returns the action for the queue.
	 *
	 * @since 3.0
	 *
	 * @param string $queue_class
	 *
	 * @return string
	 */
	protected function get_queue_action( $queue_class ) {
		/** @var Pngx__Process__Queue queue_class */
		return 'pngx_queue_' . call_user_func( array( $queue_class, 'action' ) );
	}

	/**
	 * Dispatches the request, if in AJAX context of a valid queue processing request,
	 *  to the correct handler.
	 *
	 * @since 3.0
	 */
	protected function dispatch_async() {
		if ( ! (
			$this->context->doing_ajax()
			&& false !== $action = pngx_get_request_var( 'action', false )
		) ) {
			return;
		}

		$this->hook_handler_for_action( $action );
	}

	/**
	 * Start the process handlers if in the context of a cron process and
	 * if any is registered.
	 *
	 * @since 3.0
	 */
	protected function dispatch_cron() {
		if ( ! $this->context->doing_cron() ) {
			return;
		}

		/*
		 * Here we parse the scheduled cron events to get those scheduled by a queue
		 * or process handler.
		 */
		$hooks = $this->get_scheduled_like( array( 'pngx_process_', 'pngx_queue_' ) );

		if ( empty( $hooks ) ) {
			return;
		}

		foreach ( $hooks as $action ) {
			/*
			 * Building the queue or process handler for an action will make it
			 * so the handler, in its `__construct` method, will hook on the action
			 * triggered by its cron event.
			 */
			$this->hook_handler_for_action( $action );
		}
	}

	/**
	 * Hooks the correct queue or process handler for an action if any.
	 *
	 * @since 3.0
	 *
	 * @param string $action The action to hook the handler, or queue, for.
	 */
	protected function hook_handler_for_action( $action ) {
		if (
			0 !== strpos( $action, 'pngx_process_' )
			&& 0 !== strpos( $action, 'pngx_queue_' )
		) {
			return;
		}

		if ( 0 === strpos( $action, 'pngx_process_' ) ) {
			$this->hook_handler_for( $action );
		} else {
			$this->hook_queue_for( $action );
		}
	}

	/**
	 * Parses the `cron` array to return the hook names starting with a pattern.
	 *
	 * @since 3.0
	 *
	 * @param string|array $needles A pattern to look for or an array of patterns; if
	 *                              this is an array then a match will be an hook that
	 *                              matches at least one pattern.
	 *
	 * @return array An array of hook names matching the pattern.
	 */
	protected function get_scheduled_like( $needles ) {
		$cron = get_option( 'cron', false );

		if ( empty( $cron ) ) {
			return array();
		}

		$needles  = (array) $needles;
		$matching = array();

		foreach ( $cron as $time ) {
			if ( ! is_array( $time ) ) {
				continue;
			}
			foreach ( $time as $hook => $entry ) {
				foreach ( $needles as $needle ) {
					if ( false !== strpos( $hook, $needle ) ) {
						$matching[] = $hook;
					}
				}
			}
		}

		return $matching;
	}
}
