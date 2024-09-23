<?php
/**
 * Handles the Read Only Fields.
 *
 * @since   4.0.0
 *
 * @package Pngx\Admin\Field
 */

namespace Pngx\Admin\Field\V2;

/**
 * Class Read_Only
 *
 * @since 4.0.0
 */
class Read_Only {

	public static function display( $field = [], $options = [], $options_id = null, $meta = null, $var = null, $template = null ) {
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
			'template_name'  => 'read-only',
			'template_echo'  => true,
			'template_args'  => [
				'id'            => $field['id'],
				'label'         => $field['label'],
				'screen_reader' => $field['label'],
				'description'   => ! empty( $field['description'] ) ? $field['description'] : '',
				'placeholder'   => ! empty( $field['placeholder'] ) ? $field['placeholder'] : '',
				'classes_wrap'  => ! empty( $field['classes_wrap'] ) ? (array) $field['classes_wrap'] : [],
				'classes_input' => ! empty( $field['classes_input'] ) ? (array) $field['classes_input'] : [ 'pngx-meta-field' ],
				'classes_label' => ! empty( $field['classes_label'] ) ? (array) $field['classes_label'] : [ 'screen-reader-text' ],
				'name'          => $name,
				'value'         => $value,
				'attrs'         => ! empty( $field['attrs'] ) ? (array) $field['attrs'] : [],
				'wrap_attrs'    => ! empty( $field['wrap_attrs'] ) ? (array) $field['wrap_attrs'] : [],
			],
		] );
	}
}
