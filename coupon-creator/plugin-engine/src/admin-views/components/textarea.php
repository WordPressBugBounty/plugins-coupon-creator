<?php
/**
 * View: Common Textarea Input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/textarea.php
 *
 * See more documentation about our views templating system.
 *
 * @since 4.0.0
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
 * @var number               $rows          The number of rows for the textarea.
 * @var number               $cols          The number of cols for the textarea.
 * @var array<string,string> $attrs         Associative array of attributes of the text input.
 * @var array<string,string> $wrap_attrs      Associative array of attributes of the field wrap.
 */

$wrap_classes = [ 'pngx-engine-options-control', 'pngx-engine-options-control__textarea-wrap' ];
if ( ! empty( $classes_wrap ) ) {
	$wrap_classes = array_merge( $wrap_classes, $classes_wrap );
}

$label_classes = [ 'pngx-engine-options-control__label' ];
if ( ! empty( $classes_label ) ) {
	$label_classes = array_merge( $label_classes, $classes_label );
}

$input_classes = [ 'pngx-engine-options-control__textarea-input' ];
if ( ! empty( $classes_input ) ) {
	$input_classes = array_merge( $input_classes, $classes_input );
}

// Keep value with no spaces before or after the php code to prevent spaces in the output.
?>
<div <?php pngx_classes( $wrap_classes ); ?> >
	<label
		<?php pngx_classes( $classes_label ); ?>
		for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label>
	<textarea
		id="<?php echo esc_attr( $id ); ?>"
		<?php pngx_classes( $input_classes ); ?>
		name="<?php echo esc_html( $name ); ?>"
		placeholder="<?php echo esc_attr( $placeholder ); ?>"
		rows="<?php echo absint( $rows ); ?>"
		cols="<?php echo absint( $cols ); ?>"
		<?php pngx_attributes( $attrs ) ?>
	><?php echo format_for_editor( $value ); ?></textarea>
</div>