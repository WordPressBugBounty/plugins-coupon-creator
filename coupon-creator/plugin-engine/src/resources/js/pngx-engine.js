// Run some magic to allow a better handling of class names for jQuery.hasClass type of methods
String.prototype.className = function() {
	// Prevent Non Strings to be included
	if (
		(
			'string' !== typeof this
			&& !this instanceof String
		)
		|| 'function' !== typeof this.replace
	) {
		return this;
	}

	return this.replace( '.', '' );
};

/**
 * Creates a global pngx Variable where we should start to store all the things
 * @type {object}
 */
var pngx = pngx || {};

/**
 * Makes sure we have all the required levels on the pngx object.
 *
 * @since 0.1.0
 *
 * @type {PlainObject}
 */
pngx.loader = pngx.loader || {};

(( $, obj ) => {
	'use-strict';
	const $document = $( document );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 0.1.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		pngxLoaderHiddenElement: '.pngx-a11y-hidden',
	};

	/**
	 * Show loader for the container.
	 *
	 * @since 4.0.0
	 *
	 * @param {jQuery} $container jQuery object of the container.
	 * @param {string} loader The string of the loader class.
	 */
	obj.show = ( $container, loader ) => {
		const $loader = $container.find( loader );

		if ( $loader.length ) {
			$loader.removeClass( obj.selectors.pngxLoaderHiddenElement.slice( 1 ) );
		}
	};

	/**
	 * Hide loader for the container.
	 *
	 * @since 4.0.0
	 *
	 * @param {jQuery} $container jQuery object of the container.
	 * @param {string} loader The string of the loader class.
	 */
	obj.hide = ( $container, loader ) => {
		const $loader = $container.find( loader );

		if ( $loader.length ) {
			$loader.addClass( obj.selectors.pngxLoaderHiddenElement.slice( 1 ) );
		}
	};
})( jQuery, pngx.loader );