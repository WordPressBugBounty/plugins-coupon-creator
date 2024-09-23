<?php
/**
 * View: Common Number Input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/number.php
 *
 * See more documentation about our views templating system.
 *
 * @since   4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://pngx.ink/RYIOh
 *
 * @var array<string,string> $classes_wrap  An array of classes for the text wrap.
 * @var array<string,string> $classes_label An array of classes for the label.
 * @var array<string,string> $classes_input An array of classes for the text input.
 * @var string               $label         The label for the text input.
 * @var string               $id            ID of the text input.
 * @var string               $name          The name for the text input.
 * @var string               $placeholder   The placeholder for the text input.
 * @var array<string|mixed>  $page          The page data.
 * @var string               $value         The value of the text field.
 * @var array<string,string> $attrs         Associative array of attributes of the text input.
 * @var array<string,string> $wrap_attrs    Associative array of attributes of the field wrap.
 * @var int                  $min           The minimum value of the number input.
 * @var int                  $max           The maximum value of the number input.
 * @var int                  $step          The step value of the number input.
 */

$wrap_classes = [ 'pngx-engine-options-control', 'pngx-engine-options-control__number-wrap' ];
if ( ! empty( $classes_wrap ) ) {
	$wrap_classes = array_merge( $wrap_classes, $classes_wrap );
}

if ( empty( $wrap_attrs ) ) {
	$wrap_attrs = [];
}

$label_classes = [ 'pngx-engine-options-control__label' ];
if ( ! empty( $classes_label ) ) {
	$label_classes = array_merge( $label_classes, $classes_label );
}

$input_classes = [ 'pngx-engine-options-control__number-input' ];
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
		type="number"
		name="<?php echo esc_html( $name ); ?>"
		placeholder="<?php echo esc_html( $placeholder ); ?>"
		value="<?php echo esc_html( $value ); ?>"
		min="<?php echo esc_html( $min ); ?>"
		max="<?php echo esc_html( $max ); ?>"
		step="<?php echo esc_html( $step ); ?>"
		<?php pngx_attributes( $attrs ) ?>
	>
</div>