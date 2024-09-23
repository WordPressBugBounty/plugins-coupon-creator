<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Field__Dropdown' ) ) {
	return;
}


/**
 * Class Pngx__Field__Dropdown
 * Dropdown
 */
class Pngx__Field__Dropdown {

	public static function display( $field = array(), $post_id = null, $meta = null, $template_fields = array(), $var = array() ) {

		if ( ! empty( $var['repeating'] ) && ! empty( $var['value'] ) ) {
			$meta = $var['value'];
		}

		$classes[] = $field['display']['class'] ? $field['display']['class'] : '';
		$classes[] = 'rngx-dropdown';
		if ( ! empty( $meta ) ) {
			$classes[] = 'rngx--has-selection';
		}
		?>
		<div <?php pngx_classes( $classes ); ?>>
			<select
				class="rngx-planner-fields rngx-dropdown__input"
				id="<?php echo esc_attr( $field['id'] ); ?>"
				name="<?php echo esc_attr( $field['name'] ); ?>"
				type="select"
			>
				<?php
					foreach( $field['options'] as $option ) {
						?>
						<option
							value="<?php echo esc_attr( $option['value'] ); ?>"
							<?php selected( $meta, (string) $option['value'], true ) ?>
						>
							<?php echo esc_html( $option['text'] ); ?>
						</option>
						<?php
					}
				?>
			</select>
		</div>
		<?php

	}

}
