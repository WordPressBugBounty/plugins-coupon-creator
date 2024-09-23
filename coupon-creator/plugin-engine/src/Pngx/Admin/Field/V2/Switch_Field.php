<?php
/**
 * Handles the Switch Field.
 *
 * @since   4.0.0
 *
 * @package Pngx\Admin\Field
 */

namespace Pngx\Admin\Field\V2;

/**
 * Class Switch_Field
 *
 * @since 4.0.0
 */
class Switch_Field {

	public static function display( $field = [], $option_value = [], $options_id = null, $meta = null, $var = null, $template = null ) {
		global $pagenow;
		$selected = '';

		if ( ! empty( $options_id ) ) {
			$name     = $options_id;
			$selected = $option_value[ $field['id'] ];
		} else {
			$name = $field['id'];

			//Set Meta Default
			if ( $meta ) {
				$selected = $meta;
			} elseif ( $pagenow == 'post-new.php' && isset( $field['value'] ) ) {
				$selected = $field['value'];
			}
		}

		if ( $meta ) {
			$selected = $meta;
		} elseif ( $pagenow == 'post-new.php' && isset( $field['value'] ) ) {
			$selected = $field['value'];
		}

		$field_wrap = isset( $field['fieldset_wrap'] ) ? $field['fieldset_wrap'] : [];

		$template->template( 'components/field', [
			'classes_wrap'   => [ "pngx-engine-field__{$field['id']}-wrap", ...$field_wrap ],
			'id'             => $field['id'],
			'label'          => $field['label'],
			'tooltip'        => $field['tooltip'] ?? null,
			'fieldset_attrs' => ! empty( $field['fieldset_attrs'] ) ? (array) $field['fieldset_attrs'] : [],
			'template_name'  => 'switch',
			'template_echo'  => true,
			'template_args'  => [
				'id'                 => $field['id'],
				'label'              => $field['label'],
				'switch_description' => isset( $field['switch_description'] ) ? $field['switch_description'] : '',
				'description'        => isset( $field['description'] ) ? $field['description'] : '',
				'classes_wrap'       => ! empty( $field['classes_wrap'] ) ? (array) $field['classes_wrap'] : [],
				'classes_input'      => ! empty( $field['classes_input'] ) ? (array) $field['classes_input'] : [ 'pngx-meta-field' ],
				'classes_label'      => ! empty( $field['classes_label'] ) ? (array) $field['classes_label'] : [],
				'name'               => $name,
				'value'              => 1,
				'checked'            => $selected,
				'attrs'              => ! empty( $field['attrs'] ) ? (array) $field['attrs'] : [],
				'wrap_attrs'         => ! empty( $field['wrap_attrs'] ) ? (array) $field['wrap_attrs'] : [],
			],
		] );
	}
}
