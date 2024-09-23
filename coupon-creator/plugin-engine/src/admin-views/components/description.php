<?php
/**
 * View: Common Description.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/description.php
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
 * @var string               $description   The description text.
 * @var array<string|mixed>  $page          The page data.
 * @var string               $value         The value of the text field.
 * @var number               $rows          The number of rows for the textarea.
 * @var number               $cols          The number of cols for the textarea.
 * @var array<string,string> $attrs         Associative array of attributes of the text input.
 */

if ( empty( $description ) ) {
	return;
}

?>
<span class="description"><?php echo wp_kses_post( $description ); ?></span>