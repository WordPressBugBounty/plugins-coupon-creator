<?php
/**
 * View: Common File Input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/file.php
 *
 * See more documentation about our views templating system.
 *
 * @since 4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://pngx.ink/RYIOh
 *
 * @var array<string,string> $classes_wrap        An array of classes for the text wrap.
 * @var array<string,string> $classes_label       An array of classes for the label.
 * @var array<string,string> $classes_input       An array of classes for the text input.
 * @var array<string,string> $classes_file_none   An array of classes for the file none selected.
 * @var array<string,string> $classes_file_chosen An array of classes for the chosen file text.
 * @var string               $label               The label for the text input.
 * @var string               $id                  ID of the text input.
 * @var string               $name                The name for the text input.
 * @var string               $placeholder         The placeholder for the text input.
 * @var array<string|mixed>  $page                The page data.
 * @var string               $value               The value of the text field.
 * @var string               $file_name           The name of the file.
 * @var string               $no_chosen_text      The optional override to the no chosen text.
 * @var array<string,string> $attrs               Associative array of attributes of the text input.
 * @var array<string,string> $wrap_attrs          Associative array of attributes of the field wrap.
 */

$wrap_classes = [ 'pngx-engine-options-control', 'pngx-engine-options-control__text-wrap' ];
if ( ! empty( $classes_wrap ) ) {
	$wrap_classes = array_merge( $wrap_classes, $classes_wrap );
}

$label_classes = [ 'pngx-engine-options-control__label', 'screen-reader-text' ];
if ( ! empty( $classes_label ) ) {
	$label_classes = array_merge( $label_classes, $classes_label );
}

$input_classes = [ 'pngx-engine-options-control__file-input', 'pngx-engine-options-control__upload-file-input' ];
if ( ! empty( $classes_input ) ) {
	$input_classes = array_merge( $input_classes, $classes_input );
}

$file_none_classes = [ 'pngx-engine-options-control__upload-file-none-text' ];
if ( ! empty( $classes_file_none ) ) {
	$file_none_classes = array_merge( $file_none_classes, $classes_file_none );
}

$file_chosen_classes = [ 'pngx-engine-options-control__upload-file-text' ];
if ( ! empty( $classes_file_chosen ) ) {
	$file_chosen_classes = array_merge( $file_chosen_classes, $classes_file_chosen );
}

$no_chosen_text = ! empty( $no_chosen_text ) ? $no_chosen_text : _x( 'No chosen file.', 'No file chosen message', 'plugin-engine' );
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
		type="hidden"
		name="<?php echo esc_html( $name ); ?>"
		placeholder="<?php echo esc_html( $placeholder ); ?>"
		value="<?php echo esc_html( $value ); ?>"
		<?php pngx_attributes( $attrs ) ?>
	>
	<div class="pngx-engine-options-control__upload-file-ui-wrap">
		<button
			id="<?php echo esc_attr( $id ); ?>-button"
			class="pngx-engine-options-control__upload-file-button"
		>
			<?php echo esc_html_x( 'Upload File', 'Upload file button text.', 'plugin-engine' ); ?>
		</button>

		<div
			id="<?php echo esc_attr( $id ); ?>-filename"
			class="pngx-file-upload-name"
		>
			<span <?php pngx_classes( $file_none_classes ); ?>>
				<?php echo esc_html( $no_chosen_text ); ?>
			</span>
			<span <?php pngx_classes( $file_chosen_classes ); ?>><?php echo esc_html( $file_name ); ?></span>
		</div>
	</div>
	<div class="pngx-engine-options-control__clear-wrap pngx-engine-options-control__clear-image-wrap">
		<a
			id="<?php echo esc_attr( $id ); ?>-remove"
			href="#"
			class="pngx-engine-options-control__upload-clear-button"
		>
			<?php echo esc_html_x( 'Remove File', 'Remove file button text.', 'plugin-engine' ); ?>
		</a>
	</div>
</div>