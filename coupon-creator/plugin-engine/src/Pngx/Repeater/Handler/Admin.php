<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Plugin Engine Repeater Admin Methods Handler
 *
 *
 */
class Pngx__Repeater__Handler__Admin {

	/**
	 * Display Opening HTML Wrap for Admin Fields
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_open( $i, $field_type, $field ) {

		$add_text = ! empty( $field['add_button'] ) ? $field['add_button'] : $field_type;
		$wrap_class = ! empty( $field['class'] ) ? ' ' . $field['class'] : '';

		echo '<div class="pngx-wrapper ' . esc_attr( $field_type ) . esc_attr( $wrap_class ) . '">
			<span class="add-repeater pngx-btn"
			   data-repeater="' . $i . '>-repeater"
			>' . esc_attr( $add_text ) . '<i class="fa fa-plus btn-icon-right"></i></span>';

		if ( ! empty( $field['label'] ) ) {
			echo '<label for="' . esc_attr( $field['id'] ) . '" class="pngx-' . esc_attr( $field_type ) . '-label">
					' . esc_attr( $field['label'] ) . '
				</label>';
		}
		if ( 'section' === $field_type ) {
			echo '
				<div
					id="' . esc_attr( $i ) . '-repeater"
					class="pngx-repeater pngx-repeater-container repeating-section"
				>';
		} elseif ( 'column' === $field_type ) {
			echo '
				<div
					id="' . esc_attr( $i ) . '"
					class="pngx-repeater pngx-repeater-container repeating-column"
				>';
		} elseif ( 'field' === $field_type ) {
			echo '
				<div
					id="' . esc_attr( $i ) . '"
					class="pngx-repeater pngx-repeater-container repeating-field"
				>';
		}

	}

	/**
	 * Display Closing HTML Wrap for Admin Fields
	 *
	 * @param $i
	 */
	public function display_repeater_close( $i ) {

		echo '
				</div>
			</div>
			';

	}

	/**
	 * Display Open Item HTML Wrap and Sort Handler
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_item_open( $i, $field_type, $class = null, $is_template = false ) {

		if ( 'field' === $field_type && $is_template ) {
			return;
		} elseif ( 'section' === $field_type ) {
			echo '<div class="repeater-item repeater-section ' . esc_attr( $class ) . '">
					<span class="repeater-sort"><i class="fa fa-arrows"></i></span>';
		} elseif ( 'column' === $field_type ) {
			echo '<div class="repeater-item repeater-column ' . esc_attr( $class ) . '">
					<span class="repeater-sort"><i class="fa fa-arrows"></i></span>';
		} elseif ( 'field' === $field_type && 'repeater-template' === $class ) {
			echo '<div class="repeater-item repeating-field ' . esc_attr( $class ) . '">
					<span class="repeater-sort"><i class="fa fa-arrows"></i></span>';
		}

	}

	/**
	 * Display Closing Item HTML Wrap
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_item_close( $i, $field_type, $is_template = false ) {

		if ( 'field' === $field_type && $is_template ) {
			return;
		}

		if ( 'field' === $field_type && ! $is_template ) {
			echo '</li>';

			return;
		}

		echo '
			<span class="remove-repeater pngx-btn pngx-round-btn"
			   data-repeater="' . $i . '-repeater"
			><i class="fa fa-times"></i></span>
		</div>';
	}


	/**
	 * Display Closing Item HTML Wrap
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_item_clone_close( $i, $field_type ) {

		return '
			<span class="remove-repeater pngx-btn pngx-round-btn"
			   data-repeater="' . $i . '-repeater"
			><i class="fa fa-times"></i></span>
		</div>';

	}

	/**
	 * Display Admin Field
	 *
	 * @param $field
	 * @param $value
	 * @param $name
	 */
	public function display_field( $field, $value, $name ) {

		Pngx__Admin__Fields::display_field( $field, false, false, $value, $name );

	}

	public function display_repeater_field_open( $class = null, $is_template = false ) {

		if ( $is_template ) {
			$class = $class . ' repeater-template';
		}

		echo '<div class="repeating-field repeater-item ' . esc_attr( $class ) . '">
				<span class="repeater-sort"><i class="fa fa-arrows"></i></span>';

	}

	/**
	 * Display Admin Repeating Value Field
	 *
	 * @param $field
	 * @param $value
	 * @param $name
	 */
	public function display_repeater_field( $field, $value, $name, $post_id ) {

		echo Pngx__Admin__Fields::display_field( $field, false, false, $value, $name );

	}

	/**
	 * Handler used for Saving
	 *
	 * @param $post_id
	 * @param $id
	 * @param $new_meta
	 */
	public function post_cycle( $post_id, $id, $new_meta ) {

	}

}