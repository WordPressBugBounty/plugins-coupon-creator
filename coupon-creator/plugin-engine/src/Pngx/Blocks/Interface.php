<?php


interface Pngx__Blocks__Interface {

	/**
	 * Which is the name/slug of this block
	 *
	 * @since  3.2.0
	 *
	 * @return string
	 */
	public function slug();

	/**
	 * Which is the name/slug of this block
	 *
	 * @since  3.2.0
	 *
	 * @return string
	 */
	public function name();

	/**
	 * What are the default attributes for this block
	 *
	 * @since  3.2.0
	 *
	 * @return array
	 */
	public function default_attributes();

	/**
	 * Since we are dealing with a Dynamic type of Block we need a PHP method to render it
	 *
	 * @since  3.2.0
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function render( $attributes = array() );

	/**
	 * Does the registration for PHP rendering for the Block, important due to been
	 * an dynamic Block
	 *
	 * @since  3.2.0
	 *
	 * @return void
	 */
	public function register();

	/**
	 * Used to include any Assets for the Block we are registering
	 *
	 * @since  3.2.0
	 *
	 * @return void
	 */
	public function assets();

	/**
	 * Fetches which ever is the plugin we are dealing with
	 *
	 * @since  3.2.0
	 *
	 * @return mixed
	 */
	public function plugin();

	/**
	 * Attach any specific hook to the current block.
	 *
	 * @since 3.0
	 *
	 * @return mixed
	 */
	public function hook();
}