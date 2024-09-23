/**
 * Makes sure we have all the required levels on the pngx Object
 *
 * @since 0.1.0
 *
 * @type {PlainObject}
 */
pngx.tooltip = pngx.tooltip || {};

(( $, obj ) => {
	'use strict';

	/**
	 * Object containing the relevant selectors
	 *
	 * @since 4.0.0
	 *
	 * @return {Object}
	 */
	obj.selectors = {
		tooltip: '.pngx-tooltip-hover',
		active: 'active',
	};

	/**
	 * Setup the live listener to anything that lives inside of the document
	 * that matches the tooltip selector for a click action.
	 *
	 * @since 4.0.0
	 *
	 * @return {void}
	 */
	obj.setup = () => {
		$( document ).on( 'click', obj.selectors.tooltip, obj.onClick );

		$( document ).on( 'click', event => {
			const tooltip = event.target.closest( obj.selectors.tooltip );
			if ( !tooltip ) {
				$( obj.selectors.tooltip ).each( function() {
					$( this ).removeClass( obj.selectors.active ).attr( 'aria-expanded', false );
				} );
			}
		} );
	};

	/**
	 * When a tooltip is clicked, we setup A11y for the element
	 *
	 * @since 4.0.0
	 *
	 * @return {void}
	 */
	obj.onClick = function() {
		const $tooltip = $( this ).closest( obj.selectors.tooltip );
		const add = !$tooltip.hasClass( obj.selectors.active );

		$( obj.selectors.tooltip ).each( function() {
			$( this ).removeClass( obj.selectors.active ).attr( 'aria-expanded', false );
		} );

		if ( add ) {
			$tooltip.addClass( obj.selectors.active ).attr( 'aria-expanded', true );
		}
	};

	$( document ).ready( obj.setup );

})( jQuery, pngx.tooltip );
