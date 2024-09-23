<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Plugin Engine Repeater Front End Methods Handler
 *
 *
 */
class Pngx__Repeater__Handler__Front_End {

	/**
	 * Front End Start HTML Wrap
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_open( $i, $field_type ) {

		if ( 'section' === $field_type ) {
			echo '<div
					id="' . $i . '-repeater"
					class="pngx-repeater repeating-section"
		>';
		} elseif ( 'column' === $field_type ) {

			echo '<div
					id="' . $i . '"
					class="pngx-repeater repeating-column"
					>';
		} elseif ( 'field' === $field_type ) {

			echo '<div
					id="' . $i . '"
					class="pngx-repeater repeating-field"
					>';
		}


		return;


	}

	/**
	 * Front End End HTML Wrap
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_close( $i ) {

		echo '</div>';

		return;

	}

	/**
	 * Front End Repeater HTML Start Wrap
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_item_open( $i, $field_type, $class = null, $is_template = false ) {

		if ( 'section' === $field_type ) {
			echo '<div class="repeater-item repeater-section">';
		} elseif ( 'column' === $field_type ) {
			echo '<div class="repeater-item repeater-column 0">';
		}


		return;


	}

	/**
	 * Front End Repeater HTML End Wrap
	 *
	 * @param $i
	 * @param $field_type
	 */
	public function display_repeater_item_close( $i, $field_type ) {

		echo '</div>';

		return;

	}

	public function display_repeater_field_open( $class = null, $is_template = false ) {

		return false;

	}

	/**
	 * Display Field Value and Wrap on Front End
	 *
	 * @param $field
	 * @param $value
	 * @param $name
	 * @param $post_id
	 */
	public function display_field( $field, $value, $name, $post_id ) {

		$repeater_meta = array(
			'repeating' => true,
			'value'     => $value,
			'name'      => $name,
		);

		Pngx__Fields::display_field( $field, $post_id, false, $repeater_meta );

		return;

	}


	/**
	 * Display Repeating Field Value and Wrap on Front End
	 *
	 * @param $field
	 * @param $value
	 * @param $name
	 * @param $post_id
	 */
	public function display_repeater_field( $field, $value, $name, $post_id ) {

		$repeater_meta = array(
			'repeating' => true,
			'value'     => $value,
			'name'      => $name,
		);

		Pngx__Fields::display_field( $field, $post_id, false, $repeater_meta );

		return;

	}

	public function post_cycle( $post_id, $id, $new_meta ) {

		return;
	}

}