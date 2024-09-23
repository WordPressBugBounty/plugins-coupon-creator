<?php


class Pngx__REST__Headers__Unsupported extends Pngx__REST__Headers__Base_Header implements Pngx__REST__Headers__Headers_Interface {

	/**
	 * @var Pngx__REST__Main
	 */
	protected $main;

	/**
	 * Pngx__REST__Headers__Unsupported constructor.
	 *
	 * @param Pngx__REST__Headers__Base_Interface $base
	 * @param Pngx__REST__Main                    $main
	 */
	public function __construct( Pngx__REST__Headers__Base_Interface $base, Pngx__REST__Main $main ) {
		parent::__construct( $base );
		$this->main = $main;
	}

	/**
	 * Prints PNGX REST API related meta on the site.
	 */
	public function add_header() {
		// no-op
	}

	/**
	 * Sends PNGX REST API related headers.
	 */
	public function send_header() {
		if ( headers_sent() ) {
			return;
		}

		header( $this->base->get_api_version_header() . ': unsupported' );
	}
}