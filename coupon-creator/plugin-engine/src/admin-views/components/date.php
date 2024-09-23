<?php
/**
 * View: Common Date Input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/date.php
 *
 * See more documentation about our views templating system.
 *
 * @since 4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://pngx.ink/RYIOh
 *
 * @var array<string,string> $classes_wrap  An array of classes for the date wrap.
 * @var array<string,string> $classes_label An array of classes for the label.
 * @var array<string,string> $classes_input An array of classes for the date input.
 * @var string               $label         The label for the date input.
 * @var string               $id            ID of the date input.
 * @var string               $name          The name for the date input.
 * @var string               $placeholder   The placeholder for the date input.
 * @var array<string|mixed>  $page          The page data.
 * @var string               $value         The value of the date field.
 * @var array<string,string> $attrs         Associative array of attributes of the date input.
 * @var array<string,string> $wrap_attrs      Associative array of attributes of the field wrap.
 */

$wrap_classes = [ 'pngx-engine-options-control', 'pngx-engine-options-control__date-wrap' ];
if ( ! empty( $classes_wrap ) ) {
	$wrap_classes = array_merge( $wrap_classes, $classes_wrap );
}

$label_classes = [ 'pngx-engine-options-control__label' ];
if ( ! empty( $classes_label ) ) {
	$label_classes = array_merge( $label_classes, $classes_label );
}

$input_classes = [ 'pngx-engine-options-control__date-input', 'pngx-engine-options-control-icon__has-trailing' ];
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
		value="<?php echo esc_html( $value ); ?>"
		<?php pngx_attributes( $attrs ) ?>
	>
	<?php $this->template( '/components/icons/calendar', [ 'classes' => [ 'pngx-engine-options-control-icon__input', 'pngx-engine-options-control-icon__trailing', 'pngx-engine-options-control__date-icon' ] ] ); ?>
</div>