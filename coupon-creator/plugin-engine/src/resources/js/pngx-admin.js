/**
 * Fields
 * @type {{}}
 */
var pngx_admin_fields_init = pngx_admin_fields_init || {};
(function ( $, obj ) {
	'use strict';

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 4.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		passwordInput: '.pngx-engine-options-control__password-input',
		passwordToggleWrap: '.pngx-engine-options-control-password-input__toggle-wrap',
		passwordToggle: '.pngx-engine-options-control-password-input__toggle-type',
		passwordLabel: '.pngx-engine-options-control-password-input__toggle-label',
	};

	obj.init = function () {

		$( 'html' ).addClass( 'pngx-js' );

		obj.date_picker();

		obj.color_picker();

		obj.init_copy();

		obj.toggle_password();

		// Load Visual Editor
		$( function () {
			obj.visual_editor();
		} );

		/*
		 * Hide Default Label
		 */
		$( "tbody tr th:contains(Default)" ).css( "display", "none" );

		/*
		 * Hide Row if Label is Empty
		 */
		$( ".form-table label:empty" ).parent().hide();

		obj.init_repeater();
	};

	/*
	 * WP Date Picker
	 */
	obj.date_picker = function () {

		$( '.pngx-datepicker' ).datepicker( {
			dateFormat: "mm/dd/yy",
			beforeShow: function ( input, inst ) {
				$( "#ui-datepicker-div" ).addClass( "pngx-ui" )
			}
		} );

		// FlatPicker Date Picker for Date V2 Field.
		$( '.pngx-engine-options-control__date-input' ).each( function() {
			let dateTimeFormat = $(this).data('dateFormat') || "Y-m-d";
			let altFormat = $( this ).data( 'altFormat' );
			let defaultDate = $(this).data('defaultDate');

			let flatpickrConfig = {
				dateFormat: dateTimeFormat,
				enableTime: true,
			};

			// If altFormat is defined, add altInput and altFormat to the configuration
			if ( altFormat ) {
				flatpickrConfig.altInput = true;
				flatpickrConfig.altFormat = altFormat;
			}

			// If defaultDate is defined, add it to the configuration
			if ( defaultDate ) {
				flatpickrConfig.defaultDate = defaultDate;
			}

			$( this ).flatpickr( flatpickrConfig );
		} );


	};

	/*
	 * Color Picker
	 */
	obj.color_picker = function ( helpid ) {

		$( '.pngx-color-picker' ).wpColorPicker();

	};

	/*
	 * Visual Editor
	 */
	obj.visual_editor = function ( context ) {
		var editors = document.getElementsByClassName( "pngx-ajax-wp-editor" );
		var selector;
		for ( var i = 0; i < editors.length; i++ ) {
			selector = '#' + editors[i].id;
			$( selector ).wp_editor( false, editors[i].id, false, context );
		}
	};

	/*
	 * Hide or Display Help Images
	 */
	obj.toggle_field_help = function ( helpid ) {

		var toggleImage = document.getElementById( helpid );

		if ( toggleImage.style.display == "inline" ) {
			document.getElementById( helpid ).style.display = 'none';
		} else {
			document.getElementById( helpid ).style.display = 'inline';
		}

		return false;
	};

	/*
	 * Password Toggle
	 */
	obj.toggle_password = function() {
		$( obj.selectors.passwordToggle ).change( function() {
			var passwordInput = $( this ).closest( '.pngx-field-wrap' ).find( obj.selectors.passwordInput );
			var passwordInputWrap = $( this ).closest( obj.selectors.passwordToggleWrap );
			if ( $( this ).is( ':checked' ) ) {
				passwordInput.attr( 'type', 'text' );
				passwordInputWrap.find( obj.selectors.passwordLabel ).text( $( this ).data( 'hideText' ) );
			} else {
				passwordInput.attr( 'type', 'password' );
				passwordInputWrap.find( obj.selectors.passwordLabel ).text( $( this ).data( 'showText' ) );
			}
		} );

		return false;
	};

	/**
	 * Initialize system info opt in copy
	 */
	obj.init_copy = function () {

		var clipboard = new Clipboard( '.pngx-system-info-copy-btn' );
		var button_icon = '<span class="dashicons dashicons-clipboard license-btn"></span>';
		var button_text = pngx_admin.clipboard_btn_text;

		//Prevent Button From Doing Anything Else
		$( ".pngx-system-info-copy-btn" ).on( 'click', function ( e ) {
			e.preventDefault();
		} );

		clipboard.on( 'success', function ( event ) {
			event.clearSelection();
			event.trigger.innerHTML = button_icon + '<span class="pngx-success-msg">' + pngx_admin.clipboard_copied_text + '<span>';
			window.setTimeout( function () {
				event.trigger.innerHTML = button_icon + button_text;
			}, 5000 );
		} );

		clipboard.on( 'error', function ( event ) {
			event.trigger.innerHTML = button_icon + '<span class="pngx-error-msg">' + pngx_admin.clipboard_fail_text + '<span>';
			window.setTimeout( function () {
				event.trigger.innerHTML = button_icon + button_text;
			}, 5000 );
		} );

	};

	/**
	 * Initialize repeater fields.
	 *
	 * @since 4.1.0
	 */
	obj.init_repeater = function() {
		$( '.pngx-repeater-container' ).repeater( {
			initEmpty: false,
			defaultValues: {},
			show: function() {
				$( this ).slideDown( function() {
					// Remove the 'pngx-dropdown-created' class from the cloned item's select
					$( this ).find( '.pngx-dropdown' ).removeClass( 'pngx-dropdown-ignore' );

					// Initialize dropdowns in the new row
					$( this ).find( '.pngx-dropdown' ).pngx_dropdowns();

					// Trigger dependencies in the new row
					$( this ).find( '.pngx-dependency' ).trigger( 'change' );
					$( document ).trigger( 'pngx.dependencies-run' );
				} );
			},
			hide: function( deleteElement ) {
				$( this ).slideUp( deleteElement );
			}
		} );
	};

	$( function () {
		obj.init();
	} );

})( jQuery, pngx_admin_fields_init );


/**
 * Media Upload Object
 * @type {{}}
 */
/**
 *
 * @param $
 * @param field_id
 * @param upload_title
 * @param button_text
 * @constructor
 */
function PNGX__Media( $, field_id ) {
	this.field_id = field_id;
	upload_title = 'Choose Image';
	button_text = 'Use Image';

	this.init = function () {
		this.upload();
		this.clear();
	};

	this.upload = function () {
		/*
		 * Media Manager 3.5
		 */
		$( 'button#' + this.field_id ).on( 'click', function ( e ) {
			//Create Media Manager On Click to allow multiple on one Page
			var img_uploader, attachment;

			e.preventDefault();

			field_data = $( this ).data();

			if ( "undefined" !== typeof field_data ) {
				upload_title = field_data.toggleUpload_title;
				button_text = field_data.toggleButton_text;
			}

			//Setup the Variables based on the Button Clicked to enable multiple
			var img_input_id = '#' + this.id + '.pngx-upload-image';
			var img_src = 'img#' + this.id + '.pngx-image';
			var default_msg = 'div#' + this.id + '.pngx-default-image';

			//If the uploader object has already been created, reopen the dialog
			if ( img_uploader ) {
				img_uploader.open();
				return;
			}

			//Extend the wp.media object
			img_uploader = wp.media.frames.file_frame = wp.media( {
				title: upload_title,
				button: {
					text: button_text
				},
				multiple: false
			} );

			//When a file is selected, grab the URL and set it as the text field's value
			img_uploader.on( 'select', function () {
				attachment = img_uploader.state().get( 'selection' ).first().toJSON();
				//Set the Field with the Image ID
				$( img_input_id ).val( attachment.id );
				//Set the Sample Image with the URL
				$( img_src ).attr( 'src', attachment.url );
				//Show Image
				$( img_src ).show();
				//Hide Message
				$( default_msg ).hide();
				//Trigger New Image Uploaded
				$( 'input#' + this.field_id ).trigger( 'display' );
			} );

			//Open the uploader dialog
			img_uploader.open();
		} );
	};

	this.clear = function () {
		/*
		 * Remove Image and replace with default and Erase Image ID
		 */
		$( '.pngx-clear-image' ).on( 'click', function ( e ) {
			e.preventDefault();
			var remove_input_id = 'input#' + this.id + '.pngx-upload-image';
			var img_src = 'img#' + this.id + '.pngx-image';

			$( remove_input_id ).val( '' );
			$( img_src ).hide();
			$( 'div#' + this.id + '.pngx-default-image' ).show();
			$( 'input#' + this.field_id ).trigger( 'display' );
		} );
	};

	this.init()
}
/**
 * Scan for Image Upload Fields and Setup Upload Script
 */
(function ( $ ) {
	var image_upload = $( ".pngx-upload-image" );
	var selector_img;
	for ( i = 0; i < image_upload.length; i++ ) {
		selector_img = $( image_upload[i] ).attr( 'id' );
		new PNGX__Media( $, selector_img );
	}
})( jQuery );

/**
 * Help
 * @type {{}}
 */
var pngx_admin_help_scripts = pngx_admin_help_scripts || {};
(function ( $, obj ) {
	'use strict';

	obj.init = function () {
		this.init_scripts();
	};

	obj.init_scripts = function () {

		/*
		 * Help Slideout
		 */
		$( ".pngx-section-help-container-toggle" ).on( "click", function ( event ) {

			event.preventDefault();
			var $help_wrap = $( this ).parent();
			var $help_section = $help_wrap.find( '.pngx-section-help-slideout' );

			$help_section.animate( {
				height: "toggle",
				opacity: "toggle"
			}, "fast" );

		} );

		/*
		 * Color Box Init for Help Videos
		 */
		$( ".youtube_colorbox" ).colorbox( {
			rel: "how_to_videos",
			current: "video {current} of {total}",
			iframe: true,
			width: "90%",
			height: "90%"
		} );

	};

	$( function () {
		obj.init();
	} );

})( jQuery, pngx_admin_help_scripts );

/**
 * jQuery UI Tabs
 * @type {{}}
 */
function Pngx_Admin_Tabs( $, obj ) {

	obj.tab_wrap = '';
	obj.data = obj.tabs = obj.updated_tab = obj.page_id = '';
	obj.tab_text = obj.tab_total_length = 0;
	obj.tab_count = 1;

	this.init = function ( wrap ) {

		if ( wrap ) {
			obj.tab_wrap = wrap;
		}

		//Set Tabs
		obj.data = $( obj.tab_wrap ).data();

		if ( "undefined" !== typeof obj.data ) {

			obj.tabs = obj.data.toggleTabs;
			obj.updated_tab = obj.data.toggleUpdate_message;
			obj.id = obj.data.toggleId;

			this.init_tabs();
		}

	};

	this.init_tabs = function () {

		this.wrap_tab_areas();

		this.setup_tabs();

		this.tab_breakpoint();

		this.setup_responsive_accordion();

		//On Resize or Load check if Tabs will fit
		$( window ).on( 'resize load', function ( e ) {
			// 40px per tab for padding
			obj.tab_total_length = obj.tab_text + ( obj.tab_count * 35 );

			if ( obj.tab_total_length > $( obj.tab_wrap ).width() ) {
				$( obj.tab_wrap + '-nav' ).addClass( 'pngx-tabs-accordian' );
				$( obj.tab_wrap + '-nav-mobile' ).addClass( 'show' );
			} else {
				$( obj.tab_wrap + '-nav' ).fadeIn( 'fast', function () {
					$( obj.tab_wrap + '-nav' ).removeClass( 'pngx-tabs-accordian' );
				} );
				$( obj.tab_wrap + '-nav-mobile' ).removeClass( 'show' );
			}
		} );

		//Open Tabs in Responsive Mode
		$( document ).on( 'click', obj.tab_wrap + ' ' + obj.tab_wrap + '-nav-mobile', function ( event ) {

			$( this ).siblings( ".pngx-tabs-accordian" ).slideToggle();

		} )
	};

	/*
	 * Wrap Tab Areas
	 */
	this.wrap_tab_areas = function () {

		var wrapped = $( obj.tab_wrap ).find( 'h2' ).wrap( '<div class="' + obj.tab_wrap.replace( /\./g, ' ' ) + '-panel">' );

		wrapped.each( function () {
			$( this ).parent().append( $( this ).parent().nextUntil( 'div' + obj.tab_wrap + '-panel' ) );
		} );

		$( obj.tab_wrap + '-panel' ).each( function ( index ) {
			$( this ).attr( 'id', obj.tabs[$( this ).children( 'h2' ).text()] );
			if ( index > 0 )
				$( this ).addClass( obj.tab_wrap + '-hide' );
		} );

	};

	/*
	 * Init Tabs
	 */
	this.setup_tabs = function () {
		/*
		 *	Coding Built from the following resources
		 *  http://stackoverflow.com/questions/4299435/remember-which-tab-was-active-after-refresh
		 *  http://jqueryui.com/upgrade-guide/1.10/#removed-cookie-option
		 *  http://api.jqueryui.com/tabs/#option-active
		 *  http://api.jqueryui.com/tabs/#event-activate
		 *  http://balaarjunan.wordpress.com/2010/11/10/html5-session-storage-key-things-to-consider/
		 */

		//  Define friendly index name
		var index = obj.tab_wrap + '-index-' + obj.id;

		//  Define friendly data store name
		var data_store = window.sessionStorage;

		//old index value
		var old_index = '';

		//If Saved then use tab index, otherwise default to first tab
		if ( obj.updated_tab ) {
			try {
				// getter: Fetch previous value
				old_index = data_store.getItem( index );
			} catch ( e ) {
				// getter: Always default to first tab in error state
				old_index = 0;
			}
		} else {
			old_index = 0;
		}

		// Tab initialization
		$( obj.tab_wrap ).tabs( {
			// The zero-based index of the panel that is active (open)
			active: old_index,
			// Triggered after a tab has been activated
			activate: function ( event, ui ) {
				//  Get new value
				var new_index = ui.newTab.parent().children().index( ui.newTab );
				//  Set future value
				data_store.setItem( index, new_index );

				//Set Responsive Menu Text to Current Tab
				var selectedTab = $( obj.tab_wrap ).tabs( 'option', 'active' );
				$( obj.tab_wrap + '-nav-mobile' ).text( $( obj.tab_wrap + ' ul li a' ).eq( selectedTab ).text() );
			},
			fx: {opacity: "toggle", duration: "fast"}
		} );

		$( obj.tab_wrap + ' h3, ' + obj.tab_wrap + 'table' ).show();

	};

	/*
	 * Responsive Tab Breakpoint
	 */
	this.tab_breakpoint = function () {

		$( obj.tab_wrap + '-nav li' ).each( function () {

			obj.tab_text = obj.tab_text + $( this ).find( 'a' ).width();

			obj.tab_count = obj.tab_count + 1;

		} );

	};

	/*
	 * Setup Responsive Accordion
	 */
	this.setup_responsive_accordion = function () {

		$( obj.tab_wrap + '-nav' ).before( '<div class="' + obj.tab_wrap.replace( /\./g, ' ' ) + '-nav-mobile">Menu</div>' );

		//Change Menu Text on Creation of Tabs
		$( obj.tab_wrap ).on( 'tabscreate', function ( event, ui ) {
			var selectedTab = $( obj.tab_wrap ).tabs( 'option', 'active' );
			$( obj.tab_wrap + '-nav-mobile' ).text( $( obj.tab_wrap + ' ul li a' ).eq( selectedTab ).text() );
		} );

	};

}


/**
 * Fields Toggle
 * @type {{}}
 */
var pngx_fields_toggle = pngx_fields_toggle || {};
(function ( $, obj ) {
	'use strict';
	obj.id = [];
	obj.field = [];
	obj.type = [];
	obj.priority = [];
	obj.connection = [];
	obj.field_group = [];
	obj.field_display = [];
	obj.message = [];

	obj.set_fields = function ( id, field, type, priority, connection, field_group, field_display, field_value, message ) {
		obj.id = id;
		obj.field[id] = field;
		obj.type[id] = type;
		obj.priority[id] = priority;
		obj.connection[id] = connection;
		obj.field_group[id] = field_group;
		obj.field_display[id] = field_display;
		obj.message[id] = message;
	};

	obj.init = function ( id ) {

		//Initial Load
		obj.toggle( id, $( obj.field[id] ).val() );

		//Change of Fields
		obj.toggle_change( id );

		if ( 'image' == obj.type[id] ) {
			obj.img_change( id );
		}

		if ( 'file' == obj.type[id] ) {
			obj.file_change( id );
		}
	};

	obj.toggle = function ( id, field_value ) {

		if ( !obj.field[id] || !obj.field_group[id] ) {
			return;
		}

		var display = obj.toggle_field_manager( id, obj.field[id] );
		var current_value = obj.field_display[id] + field_value;

		if ( 'hide' === display || 'toggle' === display ) {

			$( obj.field_group[id] ).each( function () {
				$( this ).fadeOut();
			} );

			if ( current_value ) {
				$( current_value ).each( function () {
					$( this ).fadeIn( 'fast' );
				} );
			}

			$( obj.field[id] ).attr( 'data-active', true );

		} else {
			$( obj.field_group[id] ).each( function () {
				$( this ).fadeIn( 'fast' );
			} );

			$( obj.field[id] ).attr( 'data-active', false );
		}

		//Only Run if Variable is an Object
		if ( 'object' === typeof obj.message[id] ) {
			obj.toggle_msg( id, display );
		}

	};

	obj.toggle_field_manager = function ( id, field ) {

		var $field_type = $( field ).prop( 'nodeName' );

		if ( 'INPUT' == $field_type && 'checkbox' == obj.type[id] ) {
			if ( $( obj.field[id] ).is( ':checked' ) ) {
				return 'hide';
			} else {
				return 'show';
			}
		} else if ( 'INPUT' == $field_type ) {
			if ( $( obj.field[id] ).val() ) {
				return 'hide';
			} else {
				return 'show';
			}
		} else if ( 'SELECT' == $field_type ) {
			return 'toggle';
		}

	};

	obj.toggle_msg = function ( id, display ) {
		//Remove Message
		$.each( obj.message[id], function ( key, value ) {
			var div_class = '.pngx-tab-heading-' + key;
			$( div_class ).next( 'div.pngx-error' ).remove();
		} );
		//Add Message
		if ( 'hide' === display ) {
			$.each( obj.message[id], function ( key, value ) {
				if ( key && value ) {
					var div_class = '.pngx-tab-heading-' + key;
					$( div_class ).after( '<div class="pngx-error">' + value + '</div>' );
				}
			} );
		}
	};

	/**
	 * Toggle Connected Fields by Priority Only if Field Is Active
	 *
	 * @param id
	 * @param field_value
	 */
	obj.priority_toggle = function ( id, field_value ) {

		var $data = '';

		if ( obj.connection[id] ) {
			$.each( obj.field, function ( key, value ) {
				$data = $( obj.field[key] ).data();
				if ( value === obj.connection[id] && obj.priority[key] >= obj.priority[id] && true == $data.active ) {
					obj.toggle(
						key,
						$( obj.field[key] ).val()
					);
				}
			} );
		}

		obj.toggle(
			id,
			field_value
		);

		if ( obj.connection[id] ) {
			$.each( obj.field, function ( key, value ) {
				$data = $( obj.field[key] ).data();
				if ( value == obj.connection[id] && obj.priority[key] < obj.priority[id] && true == $data.active ) {
					obj.toggle(
						key,
						$( obj.field[key] ).val()
					);
				}
			} );
		}

	};

	obj.toggle_change = function ( id ) {

		$( document ).on( 'change', obj.field[id], function () {
			obj.priority_toggle(
				id,
				$( this ).val()
			);
		} );

	};

	obj.img_change = function ( id ) {

		$( document ).on( 'display', obj.field[id], function () {
			obj.toggle(
				id,
				$( this ).val()
			);
		} );

		$( document ).on( "click", ".pngx-clear-image", function () {
			obj.toggle(
				id,
				$( this ).val()
			);
		} );

	};

	obj.file_change = function ( id ) {

		$( document ).on( 'display', obj.field[id], function () {
			obj.toggle(
				id,
				$( this ).val()
			);
		} );

		$( document ).on( "click", ".pngx-clear-file", function () {
			obj.toggle(
				id,
				$( this ).val()
			);
		} );

	};

})( jQuery, pngx_fields_toggle );

/**
 * Init Conditionals
 */
(function ( $ ) {

	var $data = [];
	var priority = {};
	var count = 0;

	$( '.pngx-meta-field-wrap' ).each( function () {
		if ( !$.isEmptyObject( $( this ).data() ) ) {

			$data[count] = ( $( this ).data() );

			pngx_fields_toggle.set_fields(
				count,
				$data[count].toggleField,
				$data[count].toggleType,
				$data[count].togglePriority,
				$data[count].toggleConnection,
				$data[count].toggleGroup,
				$data[count].toggleShow,
				$( $data[count].toggleField ).val(),
				$data[count].toggleMsg
			);

			priority[$data[count].togglePriority] = count;

			count++;
		}
	} );

	/**
	 * Sort by Priority and then Toggle Fields with Highest Priority First
	 */
	var keys = Object.keys( priority ),
		i, len = keys.length;

	keys.sort( function ( a, b ) {
		return b - a
	} );

	for ( i = 0; i < len; i++ ) {
		k = keys[i];
		pngx_fields_toggle.init(
			priority[k]
		);
	}

})( jQuery );

/**
 * Fields Dialog
 * @type {{}}
 */
var pngx_dialog = pngx_dialog || {};
(function ( $, obj ) {
	'use strict';

	obj.init = function ( message ) {

		$( '<div id="pngx-dialog"></div>' )
			.html( message )
			.dialog( {
				autoOpen: true,
				modal: true,
				width: 300,
				height: 150,
				position: {my: "center", at: "center", of: window},
				draggable: false,
				resizable: false,
				dialogClass: 'pngx-ui',
				buttons: {
					"OK": function () {

						$( this ).dialog( "close" );
						$( this ).dialog( 'destroy' ).remove();

					}
				}
			} );

		//Center Dialog on Screen Resize
		$( window ).on( "resize scroll", function ( e ) {

			$( "#pngx-dialog" ).dialog( "option", "position", {my: "center", at: "center", of: window} );

		} );
	};

})( jQuery, pngx_dialog );


/**
 * Load Scripts
 * @type {{}}
 */
var pngx_loadScript = pngx_loadScript || {};
(function ( $, obj ) {
	/*
	 * loadScript Function instead of jQuery getScript
	 * @version 2.0
	 * https://gist.github.com/bradvin/2313262
	 * Author: bradvin
	 */
	obj.init = function ( url, arg1, arg2 ) {
		var cache = false, callback = null;
		//arg1 and arg2 can be interchangable as either the callback function or the cache bool
		if ( typeof arg1 === "function" ) {
			callback = arg1;
			cache = arg2 || cache;
		} else {
			cache = arg1 || cache;
			callback = arg2 || callback;
		}

		var load = true;
		//check all existing script tags in the page for the url we are trying to load
		$( 'script[type="text/javascript"]' ).each( function () {
			return load = (url != $( this ).attr( 'src' ));
		} );
		if ( load ) {
			//didn't find it in the page, so load it
			//equivalent to a $.getScript but with control over cacheing
			$.ajax( {
				type: 'GET',
				url: url,
				success: callback,
				dataType: 'script',
				cache: cache
			} );
		} else {
			//already loaded so just call the callback
			if ( typeof callback === "function" ) {
				callback.call( this );
			}
		}
	};

})( jQuery, pngx_loadScript );

/**
 * File & Image Upload Object
 *
 * @since 4.0.0
 *
 * @param {Object} $
 * @param {string} field_id - Field ID for the file input
 *
 * @returns {void}
 */
class PNGX__File {
	/**
	 * Create a new instance of PNGX__File.
	 *
	 * @since 4.0.0
	 *
	 * @param {Object} $ - jQuery instance
	 * @param {string} field_id - Field ID for the file input
	 */
	constructor( $, field_id ) {
		this.$ = $;
		this.field_id = field_id;
		this.upload_title = 'Choose File';
		this.button_text = 'Use File';
		this.upload_type = '';
		this.init();
	}

	/**
	 * Initialize the file upload and clear functionality.
	 *
	 * @since 4.0.0
	 *
	 * @returns {void}
	 */
	init = () => {
		this.upload();
		this.clear();
	}

	/**
	 * Handles the file upload process.
	 *
	 * @since 4.0.0
	 *
	 * @returns {void}
	 */
	upload = () => {
		/**
		 * Media Manager 3.5
		 */
		this.$( `button#${this.field_id}-button` ).on( 'click', ( e ) => {
			//Create Media Manager On Click to allow multiple on one Page
			let file_uploader, attachment;

			e.preventDefault();

			//Setup the Variables based on the Button Clicked to enable multiple
			const fileInputId = `#${this.field_id}.pngx-engine-options-control__upload-file-input`;
			const fileName = `div#${this.field_id}-filename.pngx-file-upload-name`;

			// Get the data from the file input element
			let field_data = this.$( fileInputId ).data();

			// Update the values only if they exist in the file input element's data
			this.upload_title = field_data.toggleUpload_title || this.upload_title;
			this.button_text = field_data.toggleButton_text || this.button_text;
			this.upload_type = field_data.uploadType || this.upload_type;

			//If the uploader object has already been created, reopen the dialog
			if ( file_uploader ) {
				file_uploader.open();
				return;
			}

			//Extend the wp.media object
			file_uploader = wp.media.frames.file_frame = wp.media( {
				title: this.upload_title,
				button: {
					text: this.button_text
				},
				multiple: false,
				library: {
					type: this.upload_type,
				},
			} );

			//When a file is selected, grab the URL and set it as the text field's value
			file_uploader.on( 'select', () => {
				attachment = file_uploader.state().get( 'selection' ).first().toJSON();
				//Set the Field with the file id.
				this.$( fileInputId ).val( attachment.id );

				//Set the file name.
				this.$( fileName ).find( '.pngx-engine-options-control__upload-file-text' ).text( attachment.filename ).removeClass( 'pngx-a11y-hidden' );
				this.$( fileName ).find( '.pngx-engine-options-control__upload-file-none-text' ).addClass( 'pngx-a11y-hidden' );

				// Show preview image and hide image message.
				const imgSrc = 'img#' + this.field_id + '-preview';
				//Set the preview image with the URL.
				this.$( imgSrc ).attr( 'src', attachment.url ).removeClass( 'pngx-a11y-hidden' );
				this.$( `div#${this.field_id}-img-msg` ).addClass( 'pngx-a11y-hidden' );

				//Trigger New File Uploaded
				this.$( `input#${this.field_id}` ).trigger( 'display' );
			} );

			//Open the uploader dialog
			file_uploader.open();
		} );
	}

	/**
	 * Handles the file clearing process.
	 *
	 * @since 4.0.0
	 *
	 * @returns {void}
	 */
	clear = () => {
		/**
		 * Remove File and replace with default and Erase File ID
		 */
		this.$( `#${this.field_id}-remove` ).on( 'click', ( e ) => {
			e.preventDefault();
			const removeInputId = `input#${this.field_id}.pngx-engine-options-control__upload-file-input`;
			this.$( removeInputId ).val( '' );

			// Remove Filename.
			const fileName = this.$( `div#${this.field_id}-filename.pngx-file-upload-name` );
			fileName.find( '.pngx-engine-options-control__upload-file-none-text' ).removeClass( 'pngx-a11y-hidden' );
			fileName.find( '.pngx-engine-options-control__upload-file-text' ).text( '' ).addClass( 'pngx-a11y-hidden' );

			// Hide preview image and show image message.
			const imgSrc = 'img#' + this.field_id + '-preview';
			this.$( imgSrc ).addClass( 'pngx-a11y-hidden' );
			this.$( `div#${this.field_id}-img-msg` ).removeClass( 'pngx-a11y-hidden' );

			this.$( `input#${this.field_id}` ).trigger( 'display' );
		} );
	}
}

/**
 * Scan for File Upload Fields and Setup Upload Script
 */
(( $ ) => {
	const file_upload = $( ".pngx-engine-options-control__upload-file-input" );
	file_upload.each( function() {
		const selector_file = $( this ).attr( 'id' );
		new PNGX__File( $, selector_file );
	} );
})( jQuery );