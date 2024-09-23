<?php
/**
 * Plugin Engine AJAX Operations.
 *
 * @since 4.0.0
 */
namespace Pngx\Ajax;


/**
 * Class Operations
 *
 * Handles plugin engine AJAX operations.
 *
 * @since 4.0.0
 */
class Operations {

	public function verify_or_exit( $nonce, $action, $exit_data = [] ) {
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			exit( $exit_data );
		}

		return true;
	}

	public function exit_data( $data = [] ) {
		exit( $data );
	}
}
