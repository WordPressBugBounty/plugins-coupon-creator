<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Repeater' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Repeater
 * Repeater Field
 */
class Pngx__Admin__Field__Repeater {

	/**
	 * Repeating Field Admin Display
	 *
	 * @param array $field
	 * @param array $options
	 * @param null  $options_id
	 * @param null  $meta
	 * @param null  $repeat_obj
	 */
	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $repeat_obj = null ) {

		if ( ! isset( $field['repeater_fields'] ) || ! is_array( $field['repeater_fields'] ) ) {
			return;
		}

		global $post;

		if ( ! $meta ) {
			$meta = Pngx__Admin__Field__Repeater::get_empty_fields();
		}

		/*		$repeater_meta[] = array(

					'wpe_menu_column' => array(

						'0' => array(

							'wpe_menu_items' => array(
								'0' => array(
									'wpe_menu_name'        => 'Menu Item 1',
									'wpe_menu_description' => 'Menu Descrtion 1',
									'wpe_menu_r_price'     => array(
										'0' => array(
											'wpe_menu_price' => array(
												'0' => '14.00',
												'1' => '10.00',
											)
										)
									)
								)
							)
						),
						'1' => array(

							'wpe_menu_items' => array(
								'0' => array(
									'wpe_menu_name'        => 'Col 2 Menu Item 2',
									'wpe_menu_description' => 'Col 2 Menu Descrtion 2',
									'wpe_menu_r_price'     => array(
										'0' => array(
											'wpe_menu_price' => array(
												'0' => '24.00',
												'1' => '20.00',
											)
										)
									)
								)
							)
						)
					)

				);*/

		if ( ! $repeat_obj ) {
			$repeat_obj = new Pngx__Repeater__Main( $field['id'], $meta, $post->ID, 'admin' );
		}

	}

	/**
	 * Empty Array to Use to build the initial menu fields
	 * todo add a auto generated initial state that does not rely on hard coded fields
	 *
	 * @return array
	 */
	public static function get_empty_fields() {

		$repeater_meta['wpe_menu_section'] = array(

			'0' => array(

				'wpe_menu_column' => array(

					'0' => array(

						'wpe_menu_items' => array(

							'0' => array(
								'wpe_menu_name'        => '',
								'wpe_menu_description' => '',
								'wpe_menu_r_price'     => array(
									'0' => array(
										'wpe_menu_price' => array(
											'0' => '',
										),
									),
								),
							),

						),
					),
				),
			),
		);

		return $repeater_meta;
	}

}
