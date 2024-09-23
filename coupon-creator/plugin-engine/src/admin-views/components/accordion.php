<?php
/**
 * View: Accordion field.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/accordion.php
 *
 * See more documentation about our views templating system.
 *
 * @since 4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://pngx.ink/RYIOh
 *
 * @var string               $label          The accordion label.
 * @var string               $id             The id of the accordion contents.
 * @var string               $panel          The panel fields html.
 * @var array<string,string> $classes_wrap   An array of classes for the toggle wrap.
 * @var array<string,string> $classes_button An array of classes for the toggle button.
 * @var array<string,string> $classes_panel  An array of classes for the toggle content.
 * @var string               $panel_id       The id of the panel for the slide toggle.
 * @var string               $panel          The content of the panel for the slide toggle.
 * @var bool                 $expanded       Whether the panel starts open or closed.
 */
$accordion_wrap_classes = [ 'pngx-control-accordion__wrapper' ];
if ( ! empty( $classes_wrap ) ) {
	$accordion_wrap_classes = array_merge( $accordion_wrap_classes, $classes_wrap );
}

$accordion_button_classes = [ 'button', 'pngx-control-accordion__element', 'pngx-control-accordion__toggle' ];
if ( ! empty( $classes_button ) ) {
	$accordion_button_classes = array_merge( $accordion_button_classes, $classes_button );
}

$accordion_panel_classes = [ 'pngx-control-accordion__element', 'pngx-control-accordion__contents' ];
if ( ! empty( $classes_panel ) ) {
	$accordion_panel_classes = array_merge( $accordion_panel_classes, $classes_panel );
}

?>
<div
	<?php pngx_classes( $accordion_wrap_classes ); ?>
>
	<button
		type="button"
		<?php pngx_classes( $accordion_button_classes ); ?>
		data-js="pngx-accordion-trigger"
		aria-controls="<?php echo esc_html( $id ); ?>"
		aria-expanded="<?php echo $expanded ? 'true' : 'false'; ?>"
	>
		<span
			class="pngx-control-accordion__accordion__label"
		>
			<?php echo esc_html( $label ); ?>
		</span>
		<span
			class="pngx-control-accordion__accordion__arrow"
		>
			<?php
			$this->template( '/components/icons/caret-down', [ 'classes' => [ 'pngx-control-accordion__icon-caret-svg' ] ] );
			?>
		</span>
	</button>

	<div
		<?php pngx_classes( $accordion_panel_classes ); ?>
		aria-hidden="<?php echo $expanded ? 'false' : 'true'; ?>"
		id="<?php echo esc_html( $id ); ?>"
		<?php
		// Add inline style if expanded on initial load for the accordion to work correctly.
		echo $expanded ? 'style="display:block"' : '';
		?>
	>
		<?php echo $panel ?>
	</div>
</div>