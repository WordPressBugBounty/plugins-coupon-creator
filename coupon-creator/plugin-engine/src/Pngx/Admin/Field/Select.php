<?php
/**
 * Handles the select in the admin.
 *
 * @since 4.0.0
 *
 * @package Pngx\Coupon_Creator\Admin;
 */

/**
 * Class Pngx__Admin__Field__Select
 * Select Field
 */
class Pngx__Admin__Field__Select {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $repeat_obj = null ) {
		global $pagenow;
		$selected = '';

		if ( ! empty( $options_id ) ) {
			$name     = $options_id;
			$selected = $options[ $field['id'] ] ? $options[ $field['id'] ] : $field['std'];
		} else {
			$name = $field['id'];
		}

		if ( $meta ) {
			$selected = $meta;
		} elseif ( $pagenow == 'post-new.php' && isset( $field['value'] ) ) {
			$selected = $field['value'];
		}

		$class = isset( $field['class'] ) ? $field['class'] : '';

		if ( $repeat_obj ) {
			$name = $repeat_obj->get_field_name( $name );
		}

		?>
		<div class="pngx-default-select pngx-default <?php echo esc_attr( $class ); ?>">
			<select
				id="<?php echo esc_attr( $field['id'] ); ?>"
				class="select"
				name="<?php echo esc_attr( $name ); ?>"
				<?php echo isset( $field['data'] ) ? Pngx__Admin__Fields::toggle( $field['data'], null ) : ''; ?>
			>
				<?php
				foreach ( $field['choices'] as $value => $label ) {

					$disabled = '';
					if ( is_array( $label ) ) {
						$disabled = empty( $label['disabled'] ) ? '' : 'disabled';
						$label    = $label['text'];
					}

					$style = isset( $field['class'] ) && 'css-select' == $field['class'] ? 'style="' . esc_attr( $value ) . '"' : '';

					echo '<option ' . esc_textarea( $style ) . '
						value="' . esc_attr( $value ) . '"' . selected( $selected, $value, false ) . $disabled . '>' . esc_attr( $label ) . '</option>';

				}
				?>
			</select>
		</div>
		<?php

		if ( isset( $field['desc'] ) && ! empty( $field['desc'] ) ) {
			echo '<span class="description">' . esc_html( $field['desc'] ) . '</span>';
		}

	}

}
