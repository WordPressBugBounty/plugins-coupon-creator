<?php
/**
 * Handles the Fields in the options and meta.
 *
 * @since 4.0.0
 *
 * @package Pngx\Admin;
 */

use Pngx\Admin\Field\V2\Date;
use Pngx\Admin\Field\V2\File;
use Pngx\Admin\Field\V2\Image;
use Pngx\Admin\Field\V2\Number;
use Pngx\Admin\Field\V2\Read_Only;
use Pngx\Admin\Field\V2\Repeater;
use Pngx\Admin\Field\V2\Switch_Field;
use Pngx\Admin\Field\V2\Text;
use Pngx\Admin\Field\V2\Textarea;
use Pngx\Admin\Field\V2\Wooselect;
use Pngx\Admin\Field\V2\WYSIWYG;
use Pngx\Template;

/**
 * Class Pngx__Admin__Fields
 * Fields for Meta and Options
 */
class Pngx__Admin__Fields {

	/**
	 * An instance of the admin template handler.
	 *
	 * @since 4.0.0
	 *
	 * @var Template
	 */
	protected static $admin_template;

	/**
	 * Template_Modifications constructor.
	 *
	 * @since 4.0.0
	 *
	 * @param Template $template An instance of the backend template handler.
	 */
	public function __construct( Template $admin_template ) {
		static::$admin_template = $admin_template;
		static::$admin_template->set_template_context_extract( true );
	}

	/*
	* Toggle Field Data Setup
	*/
	public static function toggle( $toggle_fields, $id ) {

		if ( isset( $toggle_fields ) && is_array( $toggle_fields ) ) {

			$data = '';

			foreach ( $toggle_fields as $key => $toggle_data ) {
				$toggle = '';
				if ( 'field' == $key ) {
					$toggle = esc_html( $toggle_data ) . '#' . esc_attr( $id );
				} elseif ( 'group' == $key || 'show' == $key || 'update_message' == $key || 'type' == $key || 'connection' == $key ) {
					//handle options page update message in array
					if ( is_array( $toggle_data ) ) {
						if ( isset( $toggle_data[0]['code'] ) ) {
							$toggle = esc_html( $toggle_data[0]['code'] );
						}
					} else {
						$toggle = esc_html( $toggle_data );
					}
				} elseif ( 'id' == $key || 'wp_version' == $key || 'priority' == $key ) {
					$toggle = absint( $toggle_data );
				} elseif ( 'msg' == $key || 'tabs' == $key ) {
					$toggle = json_encode( $toggle_data, JSON_HEX_APOS );
				} else {
					$toggle = esc_html( $toggle_data );
				}

				$data .= 'data-toggle-' . esc_attr( $key ) . '=\'' . $toggle . '\' ';
			}

			return $data;

		}

		return false;
	}

	/**
	 * Flush Permalink on Permalink Field Change.
	 *
	 * @since 2.0.0
	 */
	public static function flush_permalinks() {
		if ( true == get_option( 'pngx_permalink_change' ) ) {

			do_action( 'pngx_flush_permalinks' );

			flush_rewrite_rules();

			update_option( 'pngx_permalink_flush', date( 'l jS \of F Y h:i:s A' ) );
			update_option( 'pngx_permalink_change', false );
		}
	}

	/**
	 * Display the field.
	 *
	 * @param array        $field       The field to display.
	 * @param array        $options     The options array.
	 * @param string|null  $options_id  The options id.
	 * @param mixed        $meta        The meta value.
	 * @param mixed        $repeat_vars The repeat vars.
	 * @param WP_Post|null $post        The post object or null.
	 */
	public static function display_field( $field = [], $options = [], $options_id = null, $meta = null, $repeat_vars = null, $post = null ) {

		//Create Different name attribute for Option Fields and Not Meta Fields
		if ( $options && $options_id ) {
			$options_id = $options_id . '[' . $field['id'] . ']';
		}

		if ( isset( $field['before'] ) && ! empty( $field['before'] ) ) {
			echo '<span class="before">' . $field['before'] . '</span>';
		}

		if ( ! isset( $field['version'] ) ) {
			switch ( $field['type'] ) {

				case 'checkbox':

					Pngx__Admin__Field__Checkbox::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'color':

					Pngx__Admin__Field__Color::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'date':

					Pngx__Admin__Field__Date::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'file':

					Pngx__Admin__Field__File::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'heading':

					Pngx__Admin__Field__Heading::display( $field, $options_id );

					break;

				case 'hidden':

					Pngx__Admin__Field__Hidden::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'html':

					echo $field['html'];

					break;

				case 'icon':

					Pngx__Admin__Field__Icon::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'image':

					Pngx__Admin__Field__Image::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'license':

					Pngx__Admin__Field__License::display( $field, $options_id );

					break;

				case 'license_status':

					Pngx__Admin__Field__License_Status::display( $field, $options, $options_id, $meta );

					break;

				case 'list':

					Pngx__Admin__Field__List::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'message':

					Pngx__Admin__Field__Message::display( $field, $options, $options_id, $meta );

					break;

				case 'number':

					Pngx__Admin__Field__Number::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'post_id':

					Pngx__Admin__Field__Post_ID::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'radio':

					Pngx__Admin__Field__Radio::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'read-only':

					Read_Only::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;

				case 'repeater':

					Pngx__Admin__Field__Repeater::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'dropdown':

					Pngx__Admin__Field__Dropdown::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'select':

					Pngx__Admin__Field__Select::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'wooselect':

					Wooselect::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;

				case 'switch':

					Switch_Field::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;

				case 'systeminfo':

					Pngx__Admin__Support::getInstance()->get_system_info_copy();
					echo Pngx__Admin__Support::getInstance()->formatted_support_stats( $field );

					break;

				case 'template_chooser':

					Pngx__Admin__Field__Template::display();

					break;

				case 'text':

					Pngx__Admin__Field__Text::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'textarea':

					Pngx__Admin__Field__Textarea::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;


				case 'url':

					Pngx__Admin__Field__Url::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'variety':

					Pngx__Admin__Field__Variety::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;

				case 'wysiwyg':

					Pngx__Admin__Field__Wysiwyg::display( $field, $options, $options_id, $meta, $repeat_vars );

					break;
			}
		}

		if ( isset( $field['version'] ) && $field['version'] ===  'v2' ) {
			switch ( $field['type'] ) {

				case 'date':

					Date::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;

				case 'file':

					File::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;

				case 'image':

					Image::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;

				case 'number':

					Number::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;

				case 'read-only':

					Read_Only::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;

				case 'repeater':

					Repeater::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;

				case 'switch':

					Switch_Field::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;

				case 'text':

					Text::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template, $post );

					break;

				case 'textarea':

					Textarea::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template, $post );

					break;

				case 'wooselect':

					Wooselect::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;

				case 'wysiwyg':

					WYSIWYG::display( $field, $options, $options_id, $meta, $repeat_vars, static::$admin_template );

					break;
			}
		}

		if ( has_filter( 'pngx_field_types' ) ) {
			/**
			 * Filter the Plugin Engine Fields for Meta and Options
			 *
			 * @param array $options current field being displayed.
			 * @param array $field   current value of option saved.
			 */
			apply_filters( 'pngx_field_types', $field, $options, $options_id, $meta, $repeat_vars );
		}

		if ( isset( $field['after'] ) && ! empty( $field['after'] ) ) {
			echo '<span class="after description">' . $field['after'] . '</span>';
		}

	}

}
