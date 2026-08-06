<?php

namespace Pngx\Service_Providers;

/**
 * Class Carousel
 *
 * Based off Modern Tribe's Tribe\Service_Providers\Carousel
 *
 * @since 3.2.0
 *
 * Handles the registration and creation of our async process handlers.
 */
class Carousel extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 3.2.0
	 */
	public function register() {
		pngx_singleton( 'pngx.carousel.view', '\Pngx\Carousel\View' );

		/**
		 * Allows plugins to hook into the register action to register views, etc
		 *
		 * @since 3.2.0
		 *
		 * @param Pngx\Service_Providers\Carousel $carousel
		 */
		do_action( 'pngx_carousel_register', $this );

		$this->hooks();
	}

	/**
	 * Set up hooks for classes.
	 *
	 * @since 3.2.0
	 */
	private function hooks() {
		add_action( 'pngx_engine_loaded', [ $this, 'register_carousel_assets' ] );
		add_filter( 'pngx_template_public_namespace', [ $this, 'template_public_namespace' ], 10, 2 );

		/**
		 * Allows plugins to hook into the hooks action to register their own hooks
		 *
		 * @since 3.2.0
		 *
		 * @param Pngx\Service_Providers\Carousel $carousel
		 */
		do_action( 'pngx_carousel_hooks', $this );
	}

	/**
	  * @inheritDoc
	 *
	 * @since  3.2.0
	 */
	public function template_public_namespace( $namespace, $obj ) {
		if ( ! empty( $obj->template_namespace ) && 'carousel' === $obj->template_namespace ) {
			array_push( $namespace, 'carousel' );
		}

		return $namespace;
	}

	/**
	 * Register assets associated with carousel
	 *
	 * @since 3.2.0
	 */
	public function register_carousel_assets() {
		$main = \Pngx__Main::instance();

/*		pngx_asset(
			$main,
			'pngx-carousel',
			'carousel.css',
			[],
			[],
			[ 'groups' => 'pngx-carousel' ]
		);

		pngx_asset(
			$main,
			'mt-a11y-carousel',
			'vendor/faction23/a11y-carousel/a11y-carousel.js',
			[],
			[],
			[ 'groups' => 'pngx-carousel' ]
		);
		*/

		pngx_asset(
			$main,
			'pngx-carousel-js',
			'carousel.js',
			[],
			[],
			[ 'groups' => 'pngx-carousel' ]
		);

		/**
		 * Allows plugins to hook into the assets action to register their own assets
		 *
		 * @since 3.2.0
		 *
		 * @param Pngx\Service_Providers\Carousel $carousel
		 */
		do_action( 'pngx_carousel_assets_registered', $this );
	}
}
