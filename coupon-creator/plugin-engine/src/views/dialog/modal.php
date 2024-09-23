<?php
/**
 * Dialog Modal View Template
 * The base template for Pngx Dialog Modals.
 *
 * Override this template in your own theme by creating a file at [your-theme]/pngx/dialogs/modal.php
 *
 * @since TBD
 *
 * @package Pngx
 * @version TBD
 */

/** @var \Pngx\Dialog\View $dialog_view */
$dialog_view = pngx( 'pngx.dialog.view' );
// grab allthevars!
$vars        = get_defined_vars();
?>
<?php $dialog_view->template( 'button', $vars, true ); ?>
<template data-js="<?php echo esc_attr( 'dialog-content-' . $id ); ?>" type="text/template">
	<?php if ( ! empty( $title ) ) : ?>
		<h2 <?php pngx_classes( $title_classes ) ?>><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>
	<div <?php pngx_classes( $content_classes ) ?>>
		<?php echo $content; ?>
	</div>
</template>

