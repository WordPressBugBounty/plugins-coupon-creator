<?php
/**
 * View: Dropdown Input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/dropdown.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://pngx.ink/RYIOh
 *
 * @since 4.0.0
 *
 * @version 4.0.0
 *
 * @var string               $label           Label for the dropdown input.
 * @var string               $id              ID of the dropdown input.
 * @var array<string,string> $classes_wrap    An array of classes for the dropdown wrap.
 * @var array<string,string> $classes_label   An array of classes for the label.
 * @var array<string,string> $classes_select  An array of classes for the dropdown input.
 * @var string               $name            Name attribute for the dropdown input.
 * @var string|int           $selected        The selected option id.
 * @var array<string,string> $selected_option A selected option used with ajax options.
 * @var array<string,string> $attrs           Associative array of attributes of the dropdown.
 * @var array<string,string> $wrap_attrs      Associative array of attributes of the field wrap.
 * @var array<string,string> $tooltip         An optional associative array containing information to display in a tooltip.
 */

$wrap_classes = [ 'pngx-default-select2', 'pngx-default' ];
if ( ! empty( $classes_wrap ) ) {
	$wrap_classes = array_merge( $wrap_classes, $classes_wrap );
}

$label_classes = [ 'pngx-settings-control__label' ];
if ( ! empty( $classes_label ) ) {
	$label_classes = array_merge( $label_classes, $classes_label );
}

$select_classes = [ 'pngx-dropdown', 'pngx-settings-control__dropdown' ];
if ( ! empty( $classes_select ) ) {
	$select_classes = array_merge( $select_classes, $classes_select );
}

if ( empty( $wrap_attrs ) ) {
	$wrap_attrs = [];
}
?>
<div
	<?php pngx_classes( $wrap_classes ); ?>
	<?php pngx_attributes( $wrap_attrs ) ?>
>
	<label
		<?php pngx_classes( $label_classes ); ?>
		for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label><?php // Move opening and closing php tags next to html tags to prevent whitespace and alignment issues.
	if ( ! empty( $tooltip['message'] ) ) {
		$this->template( 'components/tooltip', $tooltip );
	}
	?><select
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		<?php pngx_classes( $select_classes ); ?>
		value="<?php echo esc_attr( $selected ); ?>"
		style="width: 100%;" <?php /* This is required for selectWoo styling to prevent select box overflow */ ?>
		<?php pngx_attributes( $attrs ) ?>
	>
		<?php if ( is_array( $selected_option ) ) { ?>
			<?php foreach( $selected_option as $option ) { ?>
				<option selected="selected" value="<?php echo esc_attr( $option['id'] ); ?>">
					<?php echo esc_html( $option['text'] ); ?>
				</option>
			<?php } ?>
		<?php } ?>
	</select>
</div>
