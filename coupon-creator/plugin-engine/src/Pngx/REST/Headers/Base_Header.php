<?php

abstract class Pngx__REST__Headers__Base_Header {

	/**
	 * @var Pngx__REST__Headers__Base_Interface
	 */
	protected $base;

	/**
	 * Pngx__REST__Headers__Base_Header constructor.
	 *
	 * @param Pngx__REST__Headers__Base_Interface $base
	 */
	public function __construct( Pngx__REST__Headers__Base_Interface $base ) {
		$this->base = $base;
	}
}