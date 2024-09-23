<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Message' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Message
 * Message Field
 */
class Pngx__Admin__Field__Message {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		$attributes = empty( $field['field_attributes'] ) ? '' : Pngx__Admin__Field_Methods::instance()->set_field_attributes( $field['field_attributes'] );

		echo '<div class="pngx-message-field" ' . $attributes . '>' . strip_tags( $field['desc'], apply_filters( 'cctor_filter_terms_tags', '' ) ) . '</div>';

	}

}
