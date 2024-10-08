<?php


abstract class Pngx__Blocks__Abstract implements Pngx__Blocks__Interface {

	/**
	 * Namespace for Blocks from pngx
	 *
	 * @since  3.2.0
	 *
	 * @var string
	 */
	private $namespace = 'pngx';

	/**
	 * Builds the name of the Block
	 *
	 * @since  3.2.0
	 *
	 * @return string
	 */
	public function name() {
		return $this->namespace . '/' . $this->slug();
	}

	/*
	 * Return the block attributes
	 *
	 * @since  3.2.0
	 *
	 * @param  array $attributes
	 *
	 * @return array
	*/
	public function attributes( $params = array() ) {

		// get the default attributes
		$default_attributes = $this->default_attributes();

		// parse the attributes with the default ones
		$attributes = wp_parse_args( $params, $default_attributes );

		/**
		 * Filters the default attributes for the block
		 *
		 * @param array  $attributes The attributes
		 * @param object $this       The current object
		 */
		$attributes = apply_filters( 'pngx_block_attributes_defaults_' . $this->slug(), $attributes, $this );

		return $attributes;
	}

	/*
	 * Return the block default attributes
	 *
	 * @since  3.2.0
	 *
	 * @param  array $attributes
	 *
	 * @return array
	*/
	public function default_attributes() {

		$attributes = array();

		/**
		 * Filters the default attributes
		 *
		 * @param array  $params The attributes
		 * @param object $this   The current object
		 */
		$attributes = apply_filters( 'pngx_block_attributes_defaults', $attributes, $this );

		return $attributes;
	}

	/**
	 * Since we are dealing with a Dynamic type of Block we need a PHP method to render it
	 *
	 * @since  3.2.0
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function render( $attributes = array() ) {
		if ( version_compare( phpversion(), '5.4', '>=' ) ) {
			$json_string = json_encode( $attributes, JSON_PRETTY_PRINT );
		} else {
			$json_string = json_encode( $attributes );
		}

		return '<pre class="pngx-placeholder-text-' . $this->name() . '">' . 'Block Name: ' . $this->name() . "\n" . 'Block Attributes: ' . "\n" . $json_string . '</pre>';
	}

	/**
	 * Sends a valid JSON response to the AJAX request for the block contents
	 *
	 * @since  3.2.0
	 *
	 * @return void
	 */
	public function ajax() {
		wp_send_json_error( esc_attr__( 'Problem loading the block, please remove this block to restart.', 'plugin-engine' ) );
	}

	/**
	 * Fetches which ever is the plugin we are dealing with
	 *
	 * @since  3.2.0
	 *
	 * @return mixed
	 */
	public function plugin() {
		return pngx( 'pngx.gutenberg' );
	}

	/**
	 * Does the registration for PHP rendering for the Block, important due to been
	 * an dynamic Block
	 *
	 * @since  3.2.0
	 *
	 * @return void
	 */
	public function register() {
		$block_args = array(
			'render_callback' => array( $this, 'render' ),
		);

		register_block_type( $this->name(), $block_args );

		add_action( 'wp_ajax_' . $this->get_ajax_action(), array( $this, 'ajax' ) );

		$this->assets();
	}

	/**
	 * Fetches the name for the block we are working with and converts it to the
	 * correct `wp_ajax_{$action}` string for us to Hook
	 *
	 * @since  3.2.0
	 *
	 * @return string
	 */
	public function get_ajax_action() {
		return str_replace( 'pngx/', 'pngx_editor_block_', $this->name() );
	}

	/**
	 * Used to include any Assets for the Block we are registering
	 *
	 * @since  3.2.0
	 *
	 * @return void
	 */
	public function assets() {

	}

	/**
	 * Attach any particular hook for the specif block.
	 *
	 * @since 3.0
	 */
	public function hook() {
	}
}

