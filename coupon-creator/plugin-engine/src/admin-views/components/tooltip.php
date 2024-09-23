<?php
/**
 * View: Tooltip Input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/tooltip.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://pngx.ink/RYIOh
 *
 * @since   4.0.0
 *
 * @version 4.0.0
 *
 * @var array<string,string> $classes_wrap An array of classes for the tooltip wrap.
 * @var string               $message  The message to add to the tooltip.
 *
 */
$wrap_classes = [ 'pngx-tooltip-hover', 'pngx-helper-text' ];
if ( ! empty( $classes_wrap ) ) {
	$wrap_classes = array_merge( $wrap_classes, $classes_wrap );
}

?>
<div
	<?php pngx_classes( $wrap_classes ); ?>
	aria-expanded="false"
>
	<span class="dashicons dashicons-info"></span>
	<div class="down">
		<p>
			<?php
				echo wp_kses(
					$message,
					[
						'a' => [ 'href' => [] ],
						'ul' => [],
						'li' => [],
					]
				);
			?>
		</p>
	</div>
</div>