<?php


/**
 * Class Pngx__Admin__Field__File
 * File Field
 */
class Pngx__Admin__Field__File {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $repeat_obj = null ) {

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

		$file_name = '';
		$class     = isset( $field['class'] ) ? $field['class'] : '';
		$repeating = isset( $field['repeating'] ) ? '[]' : '';


		if ( is_numeric( $value ) ) {
			$file_url  = get_attached_file( absint( $value ) );
			$file_name = basename( $file_url );
		}

		echo '<div
				id="' . esc_attr( $field['id'] ) . '-filename"
				class="pngx-file-upload-name"
				>'.
				__( 'File:', 'plugin-engine' ) .
				' <span>' .
				esc_html( $file_name )
				. '</span></div><br>';

		?>

		<input
			class="pngx-upload-file <?php echo esc_attr( $class ); ?> pngx-engine-options-control__upload-file-input"
			type="hidden"
			id="<?php echo esc_attr( $field['id'] ); ?>"
			name="<?php echo esc_attr( $name ) . $repeating; ?>"
			value="<?php echo esc_attr( $value ); ?>"
			data-upload-type="<?php echo esc_attr( $field['upload_type'] ); ?>"
			<?php echo isset( $field['function'] ) ? Pngx__Admin__Fields::toggle( $field['function'], $field['id'] ) : null; ?>
		/>

		<button
			id="<?php echo esc_attr( $field['id'] ); ?>-button"
			class="pngx-file-button pngx-engine-options-control__upload-file-button"
		>
			<?php echo __( 'Upload File', 'plugin-engine' ); ?>
		</button>

		<small>
			<a
				href="#"
				id="<?php echo esc_attr( $field['id'] ); ?>-remove"
				class="pngx-clear-file pngx-engine-options-control__upload-clear-button"
			>
				<?php echo __( 'Remove File', 'plugin-engine' ); ?>
			</a>
		</small>

		<?php
		if ( ! empty( $field['desc'] ) ) {
			echo '<span class="description">' . esc_html( $field['desc'] ) . '</span>';
		}

	}

}
