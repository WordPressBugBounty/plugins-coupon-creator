<?php
/**
 * Handles the WooSelect Dropdown fields.
 *
 * @since   4.0.0
 *
 * @package Pngx\Admin\Field
 */

namespace Pngx\Admin\Field\V2;

/**
 * Class Wooselect
 *
 * @since 4.0.0
 */
class Wooselect {

	public static function display( $field = [], $option_value = [], $options_id = null, $meta = null, $var = null, $template = null ) {
		global $pagenow;
		$selected = '';

		if ( ! empty( $var['repeating'] ) && ! empty( $var['value'] ) ) {
			$meta = $var['value'];
		}
		$name = '';
		if ( ! empty( $options_id ) ) {
			$name     = $options_id;
			$selected = $option_value[ $field['id'] ] ? $option_value[ $field['id'] ] : $field['std'];
		} else {
			$name = $field['id'];
		}
		if ( ! empty( $field['attrs']['multiple'] ) ) {
			$name .= '[]';
		}

		if ( ! empty( $var['name'] ) ) {
			$field['name'] = $var['name'];
		}

		if ( $meta ) {
			$selected = $meta;
		} elseif ( $pagenow == 'post-new.php' && isset( $field['value'] ) ) {
			$selected = $field['value'];
		}

		$selected_option = [];
		if ( ! empty( $field['attrs']['data-source'] ) ) {
			if ( is_array( $selected ) ) {
				foreach ( $selected as $sel_option ) {
					if ( empty( $sel_option ) ) {
						continue;
					}

					if ( $field['attrs']['data-source-type'] === 'post' ) {
						$selected_text = empty( $sel_option ) ? '' : get_the_title( $sel_option );
						$selected_text = empty( $selected_text ) ? _x( 'Content Not Found', 'Post title not found for dropdown option name.', 'plugin-engine' ) : $selected_text;
					} elseif ( $field['attrs']['data-source-type'] === 'term' ) {
						$selected_text = empty( $sel_option ) ? '' : get_term( $sel_option )->name;
						$selected_text = empty( $selected_text ) ? _x( 'Term Not Found', 'Term not found for dropdown option name.', 'plugin-engine' ) : $selected_text;
					}

					$selected_option[] = [
						'id'   => $sel_option,
						'text' => $selected_text,
					];
				}
			} else {
				if ( $field['attrs']['data-source-type'] === 'post' ) {
					$selected_text = empty( $selected ) ? '' : get_the_title( $selected );
					$selected_text = empty( $selected_text ) ? _x( 'Content Not Found', 'Post title not found for dropdown option name.', 'plugin-engine' ) : $selected_text;
				} elseif ( $field['attrs']['data-source-type'] === 'term' ) {
					$selected_text = empty( $selected ) ? '' : get_term( $selected )->name;
					$selected_text = empty( $selected_text ) ? _x( 'Term Not Found', 'Term not found for dropdown option name.', 'plugin-engine' ): $selected_text;
				}

				$selected_option[] = [
					'id'   => $selected,
					'text' => $selected_text,
				];
			}

			$field['options'] = $selected_option;
		}

		if ( is_array( $field['options'] ) ) {
			foreach ( $field['options'] as $key => $option ) {
				if ( is_array( $selected ) ) {
					$field['options'][ $key ]['selected'] = in_array( $option['id'], $selected, true );
				} else {
					$field['options'][ $key ]['selected'] = $option['id'] === $selected ? true : false;
				}
			}
		}

		$selected = is_array( $selected ) ? implode( ',', $selected ) : $selected;

		$selected_attrs = [
			'data-selected' => $selected,
			'data-options'  => json_encode( $field['options'] ),
		];
		$attrs          = array_merge( $field['attrs'], $selected_attrs );
		$field_wrap     = isset( $field['fieldset_wrap'] ) ? $field['fieldset_wrap'] : [];

		$template->template( 'components/field', [
			'classes_wrap'   => [ "pngx-engine-field__{$field['id']}-wrap", ...$field_wrap ],
			'id'             => $field['id'],
			'label'          => $field['label'],
			'tooltip'        => $field['tooltip'] ?? null,
			'fieldset_attrs' => ! empty( $field['fieldset_attrs'] ) ? (array) $field['fieldset_attrs'] : [],
			'template_name'  => 'dropdown',
			'template_echo'  => true,
			'template_args'  => [
				'label'           => $field['label'],
				'id'              => $field['id'],
				'classes_wrap'    => ! empty( $field['classes_wrap'] ) ? (array) $field['classes_wrap'] : [],
				'classes_input'   => ! empty( $field['classes_input'] ) ? (array) $field['classes_input'] : [ 'pngx-meta-field' ],
				'classes_label'   => ! empty( $field['classes_label'] ) ? (array) $field['classes_label'] : [ 'screen-reader-text' ],
				'classes_select'  => ! empty( $field['classes_select'] ) ? (array) $field['classes_select'] : [],
				'name'            => $name,
				'selected'        => $selected,
				'selected_option' => $selected_option,
				'attrs'           => $attrs,
				'wrap_attrs'      => ! empty( $field['wrap_attrs'] ) ? (array) $field['wrap_attrs'] : [],
			],
		] );
	}
}
