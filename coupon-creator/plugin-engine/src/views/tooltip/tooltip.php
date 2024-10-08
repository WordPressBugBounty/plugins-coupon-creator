<?php
/**
 * Tooltip Basic View Template
 * The base template for Pngx Tooltips.
 *
 * Override this template in your own theme by creating a file at [your-theme]/pngx/tooltips/tooltip.php
 *
 * @since 4.9.8
 *
 * @package Pngx
 * @version 4.9.8
 */

?>
<div class="pngx-tooltip <?php echo sanitize_html_class( $wrap_classes ); ?>" aria-expanded="false">
	<span class="dashicons dashicons-<?php echo sanitize_html_class( $icon ); ?> <?php echo sanitize_html_class( $classes ); ?>"></span>
	<div class="<?php echo sanitize_html_class( $direction ); ?>">
		<?php foreach( $messages as $message ) : ?>
			<p>
				<span><?php echo wp_kses_post( $message ); ?><i></i></span>
			</p>
		<?php endforeach; ?>
	</div>
</div>
