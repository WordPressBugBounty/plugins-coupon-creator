<?php
/**
 * Class Pngx__Admin__Field__Textarea
 * Textarea Field
 */
class Pngx__Admin__Field__Textarea {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $var = null ) {

		if ( ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		$rows  = isset( $field['rows'] ) ? $field['rows'] : 12;
		$cols  = isset( $field['cols'] ) ? $field['cols'] : 50;
		$class = isset( $field['class'] ) ? $field['class'] : '';
		$std   = isset( $field['std'] ) ? $field['std'] : '';

		if ( ! empty( $var['name'] ) ) {
			$name = $var['name'];
		}

		// Keep value with no spaces before or after the php code to prevent spaces in the output.
		?>
		<textarea
			class="<?php echo esc_attr( $class ); ?>"
			id="<?php echo esc_attr( $field['id'] ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			placeholder="<?php echo esc_attr( $std ); ?>"
			rows="<?php echo absint( $rows ); ?>"
			cols="<?php echo absint( $cols ); ?>"
		><?php echo format_for_editor( $value ); ?></textarea>

		<?php
		if ( isset( $field['desc'] ) && ! empty( $field['desc'] ) ) {
			?>
			<span class="description"><?php echo esc_html( $field['desc'] ); ?></span>
			<?php
		}
	}

}
