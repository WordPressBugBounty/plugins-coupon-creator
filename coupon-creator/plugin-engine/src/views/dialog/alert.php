<?php
/**
 * Dialog Alert View Template
 * The alert template for pngx-dialogs.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/pngx/dialogs/alert.php
 *
 * @since 3.2.0
 *
 * @package Pngx
 * @version 3.2.0
 */

/** @var \Pngx\Dialog\View $dialog_view */
$dialog_view = pngx( 'pngx.dialog.view' );
// grab allthevars!
$vars        = get_defined_vars();
?>
<?php $dialog_view->template( 'button', $vars, true ); ?>
<script data-js="<?php echo esc_attr( 'dialog-content-' . $id ); ?>" type="text/template">
	<div <?php pngx_classes( $content_classes ) ?>>
		<?php if ( ! empty( $title ) ) : ?>
			<h2 <?php pngx_classes( $title_classes ) ?>><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>

		<?php echo $content; ?>
		<div class="pngx-dialog__button_wrap">
			<button class="pngx-button pngx-alert__continue"><?php echo esc_html( $alert_button_text ); ?></button>
		</div>
	</div>
</script>
