<?php
/**
 * View: Caret Down Icon.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/icons/caret-down.php
 *
 * See more documentation about our views templating system.
 *
 * @since 4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://pngx.ink/RYIOh
 *
 */
$svg_classes = [ 'pngx-control-svgicon', 'pngx-control-svgicon--caret-down' ];

if ( ! empty( $classes ) ) {
	$svg_classes = array_merge( $svg_classes, $classes );
}
?>
<svg <?php pngx_classes( $svg_classes ); ?> viewBox="0 0 10 7" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.008.609L5 4.6 8.992.61l.958.958L5 6.517.05 1.566l.958-.958z" class="pngx-control-svgicon__svg-fill"/></svg>
