<?php
/**
 * Slider View Template
 * The base template for Pngx Sliders.
 *
 * Override this template in your own theme by creating a file at [your-theme]/pngx/carousels/slider.php
 *
 * @since 3.2.0
 *
 * @package Pngx
 * @version 3.2.0
 */

// grab allthevars!
$vars        = get_defined_vars();
?>
<div id="carousel_obj_<?php echo esc_html( $vars['id'] ) ?>" <?php pngx_classes(  $wrapper_classes ) ?>>
	<?php echo $content; ?>
</div>
