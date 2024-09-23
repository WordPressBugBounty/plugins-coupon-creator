<?php
/**
 * View: Dot Icon
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/components/icons/dot.php
 *
 * See more documentation about our views templating system.
 *
 * @since 4.0.0
 *
 * @version 4.0.0
 *
 */
$svg_classes = [ 'pngx-loader-svgicon', 'pngx-loader-svgicon--dot' ];

if ( ! empty( $classes ) ) {
	$svg_classes = array_merge( $svg_classes, $classes );
}
?>
<svg <?php pngx_classes( $svg_classes ); ?> viewBox="0 0 15 15" xmlns="http://www.w3.org/2000/svg"><circle cx="7.5" cy="7.5" r="7.5"/></svg>
