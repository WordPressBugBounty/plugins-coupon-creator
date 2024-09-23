<?php
/**
 * View: Common Message.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/message.php
 *
 * See more documentation about our views templating system.
 *
 * @since 4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://pngx.ink/RYIOh
 *
 * @var string               $message     The message to display.
 * @var string               $type        The type of message, either standard or error.
 * @var array<string|string> $add_classes An array of message classes.
 */
// If not message, do not display.
if ( empty( $message ) ) {
	return;
}

$message_classes = [ 'pngx-engine-options-message__wrap' ];
if ( ! empty( $add_classes ) ) {
	array_push( $message_classes, $add_classes );
}

if ( ! empty( $message_classes ) ) {
	array_push( $message_classes, $type );
}
?>

<div
	id="pngx-engine-options-message"
	<?php pngx_classes( $message_classes ); ?>
>
	<?php echo wp_kses_post( $message ); ?>
</div>
