<?php
/**
 * Dialog Button Template
 * The button template for Pngx Dialog trigger.
 *
 * Override this template in your own theme by creating a file at [your-theme]/pngx/dialogs/buton.php
 *
 * @since 3.2.0
 *
 * @package Pngx
 * @version 3.2.0
 */
$classes = $button_classes ?: 'pngx-button';
$classes = implode( ' ' , (array) $classes );
?>
<button
	class="<?php echo esc_attr( $classes ); ?>"
	data-content="<?php echo esc_attr( 'dialog-content-' . $id ); ?>"
	data-js="<?php echo esc_attr( 'trigger-dialog-' . $id ); ?>"
	<?php if ( ! empty( $button_id ) ) : ?>
		id="<?php echo esc_attr( $button_id ); ?>"
	<?php endif; ?>
	<?php if ( ! empty( $button_name ) ) : ?>
		name="<?php echo esc_attr( $button_name ); ?>"
	<?php endif; ?>
	<?php if ( ! empty( $button_type ) ) : ?>
		type="<?php echo esc_attr( $button_type ); ?>"
	<?php endif; ?>
	<?php if ( ! empty( $button_value ) && 0 !== absint( $button_value ) ) : ?>
		value="<?php echo esc_attr( $button_value ); ?>"
	<?php endif; ?>
><?php echo $button_text; ?></button>
