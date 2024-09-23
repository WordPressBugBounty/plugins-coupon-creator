<?php
/**
 * Handles the Text fields.
 *
 * @since   4.0.0
 *
 * @package Pngx\Admin\Field
 */

namespace Pngx\Admin\Field\V2;

/**
 * Class Text
 *
 * @since 4.0.0
 */
class Text {

	/**
	 * Display the Text field.
	 *
	 * @param array        $field      Field data.
	 * @param array        $options    Options data.
	 * @param string       $options_id Options ID.
	 * @param string       $meta       Meta data.
	 * @param array        $var        Variable data.
	 * @param Template     $template   Template data.
	 * @param WP_Post|null $post       WP_Post object.
	 */
	public static function display( $field = [], $options = [], $options_id = null, $meta = null, $var = null, $template = null, $post = null ) {

		if ( ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
			if ( ! $value
				&& isset( $field['value'] )
				&& (
					isset ( $post->ID )
					&& ! metadata_exists( 'post', $post->ID, $field['id'] )
				)
			) {
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
				'template_name'  => 'text',
				'template_echo'  => true,
				'template_args'  => [
					'id'            => $field['id'],
					'label'         => $field['label'],
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
