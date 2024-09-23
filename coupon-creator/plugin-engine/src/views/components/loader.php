<?php
/**
 * View: Loader
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/components/loader.php
 *
 * See more documentation about our views templating system.
 *
 * @since 4.0.0
 *
 * @version 4.0.0
 *
 */
if ( empty( $text ) ) {
	$text = $this->get( 'text' ) ?: __( 'Loading...', 'plugin-engine' );
}

if ( empty( $loader_classes ) ) {
	$loader_classes = $this->get( 'classes' ) ?: [];
}

$spinner_classes = [
	'plugin-engine-loader__dots',
	'pngx-loader',
	'pngx-a11y-hidden',
];

if ( ! empty( $loader_classes ) ) {
	$spinner_classes = array_merge( $spinner_classes, (array) $loader_classes );
}

?>
<div class="pngx-loader-wrap">
	<div class="pngx">
		<div <?php pngx_classes( $spinner_classes ); ?> >
			<?php $this->template( '/components/icons/dot', [ 'classes' => [ 'pngx-loader__dot', 'pngx-loader__dot--first' ] ] ); ?>
			<?php $this->template( '/components/icons/dot', [ 'classes' => [ 'pngx-loader__dot', 'pngx-loader__dot--second' ] ] ); ?>
			<?php $this->template( '/components/icons/dot', [ 'classes' => [ 'pngx-loader__dot', 'pngx-loader__dot--third' ] ] ); ?>
		</div>
	</div>
</div>