<?php


class Pngx__REST__Headers__Disabled extends Pngx__REST__Headers__Base_Header implements Pngx__REST__Headers__Headers_Interface {

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

		header( $this->base->get_api_version_header() . ': disabled' );
	}
}