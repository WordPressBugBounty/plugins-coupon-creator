<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Plugin Engine Repeater Save Methods Handler
 *
 *
 */
class Pngx__Repeater__Handler__Save {

	public function display_repeater_open( $i, $field_type ) {

		return false;

	}

	public function display_repeater_close( $i ) {

		return false;

	}

	public function display_repeater_item_open( $i, $field_type, $class = null, $is_template = false ) {

		return false;

	}


	public function display_repeater_item_close( $i, $field_type ) {

		return false;

	}

	public function display_field( $field, $value, $name, $post_id ) {

		return false;

	}

	public function display_repeater_field( $field, $value ) {

		return false;

	}

	public function display_repeater_field_open( $class = null, $is_template = false ) {

		return false;

	}

	/**
	 * Update Post Meta on Save
	 *
	 * @param $post_id
	 * @param $id
	 * @param $new_meta
	 */
	public function post_cycle( $post_id, $id, $new_meta ) {

		$old_meta = get_post_meta( $post_id, $id, true );

		if ( ! is_null( $new_meta[ $id ] ) && $new_meta[ $id ] != $old_meta ) {
			update_post_meta( $post_id, $id, $new_meta[ $id ] );
		} elseif ( '' == $new_meta[ $id ] && $old_meta ) {
			delete_post_meta( $post_id, $id, $old_meta );
		}

	}
}