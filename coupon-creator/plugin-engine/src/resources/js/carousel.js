var pngx = pngx || {};
pngx.carousels = pngx.carousels || {};

(function ( $, obj ) {
	'use strict';

	document.addEventListener(
		'DOMContentLoaded',
		function () {

			pngx.carousels.forEach( function ( carousel ) {
				var objName = '#carousel_obj_' + carousel.id;

				$( objName ).slick( {
					accessibility: carousel.accessibility,
					autoplay: carousel.autoplay,
					autoplaySpeed: Number( carousel.autoplaySpeed ),
					arrows: carousel.arrows,
					prevArrow: carousel.prevArrow,
					nextArrow: carousel.nextArrow,
					draggable: carousel.draggable,
					focusOnSelect: carousel.focusOnSelect,
					infinite: carousel.infinite,
					pauseOnFocus: carousel.pauseOnFocus,
					pauseOnHover: carousel.pauseOnHover,
					responsive: carousel.responsive,
					slidesToShow: Number( carousel.slidesToShow ),
					slidesToScroll: Number( carousel.slidesToScroll )
				} );
			} );
		}
	)
})( jQuery, pngx.carousel );
