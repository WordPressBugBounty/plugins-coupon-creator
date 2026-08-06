<?php
/**
 * View: Common Color Picker Input.
 *
 * Renders a text input wired to the WordPress color picker via the
 * `pngx-color-picker` class (see `pngx-admin.js`). The consuming screen must
 * enqueue `wp-color-picker`.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/color.php
 *
 * @since 4.0.8
 *
 * @version 4.0.8
 *
 * @var array<string,string> $classes_wrap  An array of classes for the color wrap.
 * @var array<string,string> $classes_label An array of classes for the label.
 * @var array<string,string> $classes_input An array of classes for the color input.
 * @var string               $label         The label for the color input.
 * @var string               $id            ID of the color input.
 * @var string               $name          The name for the color input.
 * @var string               $placeholder   The placeholder for the color input.
 * @var string               $std           The default color used by the picker's reset control.
 * @var bool                 $alpha         Whether the picker allows alpha transparency.
 * @var string               $value         The value of the color field.
 * @var array<string,string> $attrs         Associative array of attributes of the color input.
 * @var array<string,string> $wrap_attrs    Associative array of attributes of the field wrap.
 */

$wrap_classes = [ 'pngx-engine-options-control', 'pngx-engine-options-control__color-wrap' ];
if ( ! empty( $classes_wrap ) ) {
	$wrap_classes = array_merge( $wrap_classes, $classes_wrap );
}

$label_classes = [ 'pngx-engine-options-control__label' ];
if ( ! empty( $classes_label ) ) {
	$label_classes = array_merge( $label_classes, $classes_label );
}

// The picker is initialized against the pngx-color-picker class.
$input_classes = [ 'pngx-engine-options-control__color-input', 'pngx-color-picker' ];
if ( ! empty( $classes_input ) ) {
	$input_classes = array_merge( $input_classes, $classes_input );
}
?>
<div
	<?php pngx_classes( $wrap_classes ); ?>
	<?php pngx_attributes( $wrap_attrs ) ?>
>
	<label
		<?php pngx_classes( $classes_label ); ?>
		for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label>
	<input
		id="<?php echo esc_attr( $id ); ?>"
		<?php pngx_classes( $input_classes ); ?>
		type="text"
		name="<?php echo esc_html( $name ); ?>"
		placeholder="<?php echo esc_html( $placeholder ); ?>"
		value="<?php echo esc_attr( $value ); ?>"
		data-default-color="<?php echo esc_attr( $std ); ?>"
		data-alpha-enabled="<?php echo esc_attr( $alpha ? 'true' : 'false' ); ?>"
		<?php pngx_attributes( $attrs ) ?>
	>
</div>
