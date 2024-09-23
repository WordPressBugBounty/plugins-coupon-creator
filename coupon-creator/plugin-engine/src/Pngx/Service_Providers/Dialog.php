<?php

namespace Pngx\Service_Providers;

/**
 * Class Dialog
 *
 * Based off Modern Tribe's Tribe\Service_Providers\Dialog
 *
 * @since 3.2.0
 *
 * Handles the registration and creation of our async process handlers.
 */
class Dialog extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 3.2.0
	 */
	public function register() {
		pngx_singleton( 'pngx.dialog.view', '\Pngx\Dialog\View' );

		/**
		 * Allows plugins to hook into the register action to register views, etc
		 *
		 * @since 3.2.0
		 *
		 * @param Pngx\Service_Providers\Dialog $dialog
		 */
		do_action( 'pngx_dialog_register', $this );

		$this->hooks();
	}

	/**
	 * Set up hooks for classes.
	 *
	 * @since 3.2.0
	 */
	private function hooks() {
		add_action( 'pngx_engine_loaded', [ $this, 'register_dialog_assets' ] );
		add_filter( 'pngx_template_public_namespace', [ $this, 'template_public_namespace' ], 10, 2 );

		/**
		 * Allows plugins to hook into the hooks action to register their own hooks
		 *
		 * @since 3.2.0
		 *
		 * @param Pngx\Service_Providers\Dialog $dialog
		 */
		do_action( 'pngx_dialog_hooks', $this );
	}

	/**
	  * @inheritDoc
	 *
	 * @since  3.2.0
	 */
	public function template_public_namespace( $namespace, $obj ) {
		if ( ! empty( $obj->template_namespace ) && 'dialog' === $obj->template_namespace ) {
			array_push( $namespace, 'dialog' );
		}

		return $namespace;
	}

	/**
	 * Register assets associated with dialog
	 *
	 * @since 3.2.0
	 */
	public function register_dialog_assets() {
		$main = \Pngx__Main::instance();

		pngx_asset(
			$main,
			'pngx-dialog',
			'dialog.css',
			[],
			[],
			[ 'groups' => 'pngx-dialog' ]
		);

		pngx_asset(
			$main,
			'mt-a11y-dialog',
			'vendor/faction23/a11y-dialog/a11y-dialog.js',
			[],
			[],
			[ 'groups' => 'pngx-dialog' ]
		);

		pngx_asset(
			$main,
			'pngx-dialog-js',
			'dialog.js',
			[ 'mt-a11y-dialog' ],
			[],
			[ 'groups' => 'pngx-dialog' ]
		);

		/**
		 * Allows plugins to hook into the assets action to register their own assets
		 *
		 * @since 3.2.0
		 *
		 * @param Pngx\Service_Providers\Dialog $dialog
		 */
		do_action( 'pngx_dialog_assets_registered', $this );
	}
}
