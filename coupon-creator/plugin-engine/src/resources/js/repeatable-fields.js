/*
 * jQuery Repeatable Fields v1.4.8
 * http://www.rhyzz.com/repeatable-fields.html
 *
 * Copyright (c) 2014-2015 Rhyzz
 * License MIT
*/
var pngx_repeatable = pngx_repeatable || {};
(function ( $, obj ) {

	$.fn.repeatable_fields = function ( custom_settings ) {

		var default_settings = {
			wrapper: '.pngx-wrapper',
			container: '.pngx-repeater-container',
			row: '.repeater-item',
			add: '.add-repeater',
			remove: '.remove-repeater',
			move: '.repeater-sort',
			template: '.repeater-template',
			is_sortable: true,
			before_add: null,
			after_add: after_add,
			before_remove: null,
			after_remove: null,
			sortable_options: null,
			row_count_placeholder: '{{row-count-placeholder}}'
		};

		var settings = $.extend( {}, default_settings, custom_settings );

		// Initialize all repeatable field wrappers
		initialize( this );

		function initialize( parent ) {
			$( settings.wrapper, parent ).each( function ( index, element ) {
				var wrapper = this;

				var container = $( wrapper ).children( settings.container );

				// Disable all form elements inside the row template
				$( container ).children( settings.template ).hide().find( ':input' ).each( function () {
					$( this ).prop( 'disabled', true );
				} );

				var row_count = $( container ).children( settings.row ).filter( function () {
					return !$( this ).hasClass( settings.template.replace( '.', '' ) );
				} ).length;

				$( container ).attr( 'data-rf-row-count', row_count );

				$( wrapper ).on( 'click', settings.add, function ( event ) {
					event.stopImmediatePropagation();

					var row_template = $( $( container ).children( settings.template ).clone().removeClass( settings.template.replace( '.', '' ) )[0].outerHTML );

					// Enable all form elements inside the row template
					$( row_template ).find( ':input' ).each( function () {
						$( this ).prop( 'disabled', false );
					} );

					if ( typeof settings.before_add === 'function' ) {
						settings.before_add( container );
					}

					var new_row = $( row_template ).show().appendTo( container );

					if ( typeof settings.after_add === 'function' ) {
						settings.after_add( container, new_row, after_add );
					}

					// The new row might have it's own repeatable field wrappers so initialize them too
					initialize( new_row );
				} );

				$( wrapper ).on( 'click', settings.remove, function ( event ) {
					event.stopImmediatePropagation();

					var row = $( this ).parents( settings.row ).first();

					if ( typeof settings.before_remove === 'function' ) {
						settings.before_remove( container, row );
					}

					row.remove();

					if ( typeof settings.after_remove === 'function' ) {
						settings.after_remove( container );
					}
				} );

				if ( settings.is_sortable === true && typeof $.ui !== 'undefined' && typeof $.ui.sortable !== 'undefined' ) {
					var sortable_options = settings.sortable_options !== null ? settings.sortable_options : {};

					sortable_options.handle = settings.move;

					$( wrapper ).find( settings.container ).sortable( sortable_options );
				}
			} );
		}

		function after_add( container, new_row ) {
			var row_count = $( container ).attr( 'data-rf-row-count' );

			row_count++;

			$( '*', new_row ).each( function () {
				$.each( this.attributes, function ( index, element ) {
					this.value = this.value.replace( settings.row_count_placeholder, row_count - 1 );
				} );
			} );

			$( container ).attr( 'data-rf-row-count', row_count );
		}
	};

	obj.init = function () {


		// save the repeating price field
		// load meta values from post id and not from page meta
		// add loading indicator on saving field


		// pro - select field to choose existing menu items - use ajax to load, cache result on page and add to it as new items are created
		// pro - default to new, but always show the select

		$( document ).on( 'change', '.repeater-item select, .repeater-item input, .repeater-item textarea', function ( e ) {

			console.log( 'change2', $( this ).parent( '.repeater-item' ).find( '.pngx-post-id' ).data() );

			var $parent = $( this ).parent( '.repeater-item' );
			var post_id = $( this ).parent( '.repeater-item' ).find( '.pngx-post-id' ).val();
			var post_type = $( this ).parent( '.repeater-item' ).find( '.pngx-post-id' ).data( 'postType' );
			var post_title_default = $( this ).parent( '.repeater-item' ).find( '.pngx-post-id' ).data( 'defaultTitle' );
			var value = $( this ).val();
			var field = $( this ).attr( 'id' );
			var title_field = $( this ).data( 'postTitle' );

			obj.ajax_menu_item( $parent, post_id, post_type, post_title_default, value, field, title_field );

		} );

	};

	obj.ajax_menu_item = function ( $parent, post_id, post_type, post_title_default, value, field, title_field ) {

		$.ajax( {
			url: pngx_repeatable.ajaxurl,
			type: 'post',
			cache: false,
			dataType: 'json',
			data: {
				nonce: pngx_repeatable.nonce,
				post_id: post_id,
				post_type: post_type,
				post_title_default: post_title_default,
				field_value: value,
				field_name: field,
				title_field: title_field,
				action: 'pngx_repeatable'
			},
			success: function ( results ) {

				if ( results.success ) {

					if ( 'post' === results.data.type && results.data.ID ) {
						console.log( 'post', results.data.type, results.data.ID );
						$parent.find( '.pngx-post-id' ).val( results.data.ID )
					}

					if ( 'meta' === results.data.type ) {
						console.log( 'meta', results.data.type, results.data.ID );
					}

				} else {


				}
			}
		} );
	};

	$( function () {
		obj.init();
	} );


})( jQuery, pngx_repeatable );