<?php
/**
 * Handles the Image fields.
 *
 * @since   4.0.0
 *
 * @package Pngx\Admin\Field
 */

namespace Pngx\Admin\Field\V2;

/**
 * Class Image
 *
 * @since 4.0.0
 */
class Image {

	public static function display( $field = [], $options = [], $options_id = null, $meta = null, $repeat_obj = null, $template = null ) {

		global $pagenow;

		if ( ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		if ( 'post-new.php' == $pagenow && ! $value && isset( $field['std'] ) ) {
			$value = $field['std'];
		}

		$image_msg  = isset( $field['image_msg'] ) ? $field['image_msg'] : '';

		$file_name = '';
		$image_src  = '';
		if ( is_numeric( $value ) ) {
			$file_url                     = get_attached_file( absint( $value ) );
			$file_name                    = basename( $file_url );
			$image_src                    = wp_get_attachment_image_src( absint( $value ), 'medium' );
			$image_src                    = isset( $image_src[0] ) ? wp_normalize_path( $image_src[0] ) : '';
			$field['classes_image_msg'][] = 'pngx-a11y-hidden';
			$field['classes_file_none'][] = 'pngx-a11y-hidden';
		} else {
			$field['classes_image_preview'][] = 'pngx-a11y-hidden';
			$field['classes_file_chosen'][] = 'pngx-a11y-hidden';
		}

		$field_wrap = isset( $field['fieldset_wrap'] ) ? $field['fieldset_wrap'] : [];

		$template->template( 'components/field', [
			'classes_wrap'   => [ "pngx-engine-field__{$field['id']}-wrap", ...$field_wrap ],
			'id'             => $field['id'],
			'label'          => $field['label'],
			'tooltip'        => $field['tooltip'] ?? null,
			'fieldset_attrs' => ! empty( $field['fieldset_attrs'] ) ? (array) $field['fieldset_attrs'] : [],
			'template_name'  => 'image',
			'template_echo'  => true,
			'template_args'  => [
				'id'                    => $field['id'],
				'label'                 => $field['label'],
				'description'           => ! empty( $field['description'] ) ? $field['description'] : '',
				'placeholder'           => ! empty( $field['placeholder'] ) ? $field['placeholder'] : '',
				'classes_wrap'          => ! empty( $field['classes_wrap'] ) ? (array) $field['classes_wrap'] : [],
				'classes_image_msg'     => ! empty( $field['classes_image_msg'] ) ? (array) $field['classes_image_msg'] : [],
				'classes_image_preview' => ! empty( $field['classes_image_preview'] ) ? (array) $field['classes_image_preview'] : [],
				'classes_file_none'     => ! empty( $field['classes_file_none'] ) ? (array) $field['classes_file_none'] : [],
				'classes_file_chosen'   => ! empty( $field['classes_file_chosen'] ) ? (array) $field['classes_file_chosen'] : [],
				'classes_input'         => ! empty( $field['classes_input'] ) ? (array) $field['classes_input'] : [ 'pngx-meta-field' ],
				'classes_label'         => ! empty( $field['classes_label'] ) ? (array) $field['classes_label'] : [ 'screen-reader-text' ],
				'name'                  => $name,
				'value'                 => $value,
				'file_name'             => $file_name,
				'image_msg'             => $image_msg,
				'image_src'             => $image_src,
				'attrs'                 => ! empty( $field['attrs'] ) ? (array) $field['attrs'] : [],
				'wrap_attrs'            => ! empty( $field['wrap_attrs'] ) ? (array) $field['wrap_attrs'] : [],
			],
		] );
	}

}
