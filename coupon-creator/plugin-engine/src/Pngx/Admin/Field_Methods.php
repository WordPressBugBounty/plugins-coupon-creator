<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field_Methods' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field_Methods
 * Fields for Meta and Options
 */
class Pngx__Admin__Field_Methods {

	protected static $instance;

	/**
	 * Static Singleton Factory Method
	 *
	 * @return self
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Pngx__Admin__Field_Methods constructor.
	 */
	protected function __construct() {

	}

	/**
	 * Generate String of Attributes
	 *
	 * @since 2.5.5
	 *
	 * @return mixed|void
	 */
	public function set_field_attributes( $field_attributes ) {

		$return = '';
		if ( ! empty( $field_attributes ) && is_array( $field_attributes ) ) {
			foreach ( $field_attributes as $key => $value ) {

				if ( empty( $value ) ) {
					$return .= ' ' . esc_attr( $key );
					continue;
				}

				$return .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}
		}

		/**
		 * Filter the Field Attributes
		 *
		 * @since 2.5.5
		 *
		 * @param $return           string|void a string of attributes
		 * @param $field_attributes array an array of attributes
		 */
		return apply_filters( 'pngx_field_attributes', $return, $field_attributes );
	}

	/**
	 * Generate Bumpdown
	 *
	 * @since 2.5.5
	 *
	 * @return mixed|void
	 *
	 */
	public function set_bumpdown( $bumpdown ) {

		$return = '';
		if ( ! empty( $bumpdown ) && is_array( $bumpdown ) ) {

			$return = '<span class="' . ( empty( $bumpdown['class'] ) ? '' : esc_attr( $bumpdown['class'] ) ) . '"';
			$return .= empty( $bumpdown['data'] ) ? '' : $this->set_field_attributes( $bumpdown['data'] );
			$return .= '>' . ( empty( $bumpdown['text'] ) ? '' : esc_attr( $bumpdown['text'] ) ) . '</span>';
		}

		/**
		 * Filter the Bumpdown HTML
		 *
		 * @since 2.5.5
		 *
		 * @param $return   string|void a string of bumpdown
		 * @param $bumpdown array an array of bumpdown attributes
		 */
		return apply_filters( 'pngx_bumpdown', $return, $bumpdown );
	}

}
