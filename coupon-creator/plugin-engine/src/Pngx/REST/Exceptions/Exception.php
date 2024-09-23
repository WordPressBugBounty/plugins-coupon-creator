<?php

/**
 * Class Pngx__REST__Exceptions__Exception
 */
class Pngx__REST__Exceptions__Exception extends Exception {
	/**
	 * @var int
	 */
	protected $status;

	public function __construct( $message, $code, $status ) {
		$this->message = $message;
		$this->code    = $code;
		$this->status  = $status;
	}

	/**
	 * Return the error status.
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}
}