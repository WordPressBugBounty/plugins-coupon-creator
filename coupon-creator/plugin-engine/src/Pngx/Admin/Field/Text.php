<?php


/**
 * Class Pngx__Admin__Field__Text
 * Text Field
 */
class Pngx__Admin__Field__Text {

	public static function display( $field = [], $options = [], $options_id = null, $meta = null, $var = null ) {

		if ( ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
			if ( ! $value && isset( $field['value'] ) ) {
				$value = $field['value'];
			}
		}

		$size       = isset( $field['size'] ) ? $field['size'] : 30;
		$class      = isset( $field['class'] ) ? $field['class'] : '';
		$std        = isset( $field['std'] ) ? $field['std'] : '';
		$condition  = isset( $field['condition'] ) ? $field['condition'] : '';
		$attributes = empty( $field['field_attributes'] ) ? '' : Pngx__Admin__Field_Methods::instance()->set_field_attributes( $field['field_attributes'] );
		$bumpdown   = empty( $field['bumpdown'] ) ? '' : Pngx__Admin__Field_Methods::instance()->set_bumpdown( $field['bumpdown'] );

		if ( ! empty( $var['name'] ) ) {
			$name = $var['name'];
		}

		if ( isset( $field['alert'] ) && '' != $field['alert'] && 1 == $condition ) {
			?>
			<div class="pngx-error">&nbsp;&nbsp;<?php echo esc_html( $field['alert'] ); ?></div>
			<?php
		}
		?>

		<input
			type="text"
			id="<?php echo esc_attr( $field['id'] ); ?>"
			class="regular-text <?php echo esc_attr( $class ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			placeholder="<?php echo esc_attr( $std ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			size="<?php echo absint( $size ); ?>"
			<?php echo $attributes; ?>
			<?php echo ! empty( $field['post_title'] ) ? 'data-post-title="' . esc_attr( $field['post_title'] ) . '"' : ''; ?>
		/>

		<?php
		echo $bumpdown;

		if ( isset( $field['desc'] ) && ! empty( $field['desc'] ) ) {
			?>
			<span class="description"><?php echo esc_html( $field['desc'] ); ?></span>
			<?php
		}
	}
}
