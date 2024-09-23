<?php
/**
 * Handles the Repeater Fields.
 *
 * @since   4.1.0
 *
 * @package Pngx\Admin\Field
 */

namespace Pngx\Admin\Field\V2;

use Pngx__Admin__Fields;

/**
 * Class Repeater
 *
 * @since 4.1.0
 */
class Repeater {

	public static function display( $field = [], $options = [], $options_id = null, $meta = null, $var = null, $template = null ) {
		wp_enqueue_script( 'jquery-repeater', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js', [ 'jquery' ], '1.2.1', true );

		$name  = $field['id'];
		$value = $meta;
		if ( ! $value && isset( $field['value'] ) ) {
			$value = $field['value'];
		}

		if ( ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		}

		$field_wrap = isset( $field['fieldset_wrap'] ) ? $field['fieldset_wrap'] : [];

		$template->template( 'components/field', [
			'classes_wrap'   => [ "pngx-engine-field__{$field['id']}-wrap", ...$field_wrap ],
			'id'             => $field['id'],
			'label'          => $field['label'],
			'tooltip'        => $field['tooltip'] ?? null,
			'fieldset_attrs' => ! empty( $field['fieldset_attrs'] ) ? (array) $field['fieldset_attrs'] : [],
			'template_name'  => 'repeater',
			'template_echo'  => true,
			'template_args'  => [
				'id'              => $field['id'],
				'label'           => $field['label'],
				'description'     => ! empty( $field['description'] ) ? $field['description'] : '',
				'classes_wrap'    => ! empty( $field['classes_wrap'] ) ? (array) $field['classes_wrap'] : [],
				'classes_label'   => ! empty( $field['classes_label'] ) ? (array) $field['classes_label'] : [ 'screen-reader-text' ],
				'name'            => $name,
				'value'           => $value,
				'repeater_fields' => $field['repeater_fields'],
				'attrs'           => ! empty( $field['attrs'] ) ? (array) $field['attrs'] : [],
				'wrap_attrs'      => ! empty( $field['wrap_attrs'] ) ? (array) $field['wrap_attrs'] : [],
			],
		] );
	}

	public static function display_repeater_field( $field, $name, $value, $template ) {
		$field['name']  = $name;
		$field['value'] = $value;

		return Pngx__Admin__Fields::display_field( $field, false, false, $value, $name );
	}
}