<?php
/**
 * Handles the Number fields.
 *
 * @since   4.0.0
 *
 * @package Pngx\Admin\Field
 */

namespace Pngx\Admin\Field\V2;

/**
 * Class Number
 *
 * @since 4.0.0
 */
class Number {

	public static function display( $field = [], $options = [], $options_id = null, $meta = null, $var = null, $template = null ) {
		if ( ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
			if ( ! $value && isset( $field['value'] ) ) {
				$value = $field['value'];
			}
		}

		if ( ! empty( $var['name'] ) ) {
			$name = $var['name'];
		}

		$field_wrap = isset( $field['fieldset_wrap'] ) ? $field['fieldset_wrap'] : [];

		$template->template( 'components/field', [
			'classes_wrap'   => [ "pngx-engine-field__{$field['id']}-wrap", ...$field_wrap ],
			'id'             => $field['id'],
			'label'          => $field['label'],
			'tooltip'        => $field['tooltip'] ?? null,
			'fieldset_attrs' => ! empty( $field['fieldset_attrs'] ) ? (array) $field['fieldset_attrs'] : [],
			'template_name'  => 'number',
			'template_echo'  => true,
			'template_args'  => [
				'id'            => $field['id'],
				'label'         => $field['label'],
				'description'   => ! empty( $field['description'] ) ? $field['description'] : '',
				'classes_wrap'  => ! empty( $field['classes_wrap'] ) ? (array) $field['classes_wrap'] : [],
				'classes_input' => ! empty( $field['classes_input'] ) ? (array) $field['classes_input'] : [ 'pngx-meta-field' ],
				'classes_label' => ! empty( $field['classes_label'] ) ? (array) $field['classes_label'] : [ 'screen-reader-text' ],
				'name'          => $name,
				'value'         => $value,
				'min'           => isset( $field['min'] ) ? $field['min'] : '',
				'max'           => isset( $field['max'] ) ? $field['max'] : '',
				'step'          => isset( $field['step'] ) ? $field['step'] : '',
				'placeholder'   => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
				'attrs'         => ! empty( $field['attrs'] ) ? (array) $field['attrs'] : [],
				'wrap_attrs'    => ! empty( $field['wrap_attrs'] ) ? (array) $field['wrap_attrs'] : [],
			],
		] );
	}
}
