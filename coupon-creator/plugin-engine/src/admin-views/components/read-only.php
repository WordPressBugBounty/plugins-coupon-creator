<?php
/**
 * View: Read only field.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/read-only.php
 *
 * See more documentation about our views templating system.
 *
 * @since   4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://pngx.ink/RYIOh
 *
 * @var array<string,string> $classes_wrap  An array of classes for the read only field.
 * @var array<string,string> $classes_input An array of classes for the input.
 * @var string               $label         The label for the hidden input.
 * @var string               $screen_reader The screen reader instructions for the text input.
 * @var string               $id            ID of the hidden input.
 * @var string               $name          The name for the hidden input.
 * @var string               $value         The value of the ready only field.
 * @var string               $display_value The display value of the ready only field.
 */

if ( empty( $display_value ) ) {
	$display_value = $value;
}
if ( empty( $classes_input ) ) {
	$classes_input = [];
}
?>
<div <?php pngx_classes( $classes_wrap ); ?> >
	<fieldset class="pngx-engine-options-details__read-only-field">
		<legend class="pngx-engine-options-details__label pngx-field-label screen-reader-text">
			<?php echo esc_html( $label ); ?>
		</legend>
		<div class="pngx-engine-options-details__field-wrap pngx-field-wrap">
			<div class="pngx-engine-options-details-field-read-only__value-wrap">
				<?php echo esc_html( $display_value ); ?>
			</div>
			<input
				id="<?php echo esc_attr( $id ); ?>"
				type="hidden"
				name="<?php echo esc_html( $name ); ?>"
				value="<?php echo esc_html( $value ); ?>"
				<?php pngx_classes( $classes_input ); ?>
			>
			<?php if ( $screen_reader ) { ?>
				<label class="screen-reader-text">
					<?php echo esc_html( $screen_reader ); ?>
				</label>
			<?php } ?>
		</div>
	</fieldset>
</div>