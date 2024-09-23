/* eslint-disable template-curly-spacing */
/**
 * Makes sure we have all the required levels on the pngx Object
 *
 * @since 0.1.0
 *
 * @type {PlainObject}
 */
pngx.access_profiles = pngx.access_profiles || {};

(function( $, obj, pngxLoader, pngxDropdowns, pngxAccordion ) {
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
		profileContainer: '.pngx-wrapper',
		profileAdd: '.pngx-engine-options__add-profile-button',
		messageWrap: '.pngx-engine-options-message__wrap',
		formTable: '.form-table',
		defaultProfile: '.pngx-engine-default-profile-select-field',
		dependent: '.pngx-dependent',

		// Individual Profiles.
		profileList: '.pngx-engine-options-items__wrap',
		profileItem: '.pngx-engine-grid-row',
		profileName: '.pngx-engine-options-details__name-input',
		profilePrimary: '.pngx-engine-options-details__primary-input',
		profileProperties: '.pngx-engine-options-profile-properties__input',
		profileSave: '.pngx-engine-options-details-action__save',
		profileUpdate: '.pngx-engine-options-details-action__update',
		profileDelete: '.pngx-engine-options-details-action__delete',

		// Loader
		pngxAdminLoader: '.pngx-admin-loader',
	};

	/**
	 * Scroll to bottom of list of Profiles.
	 *
	 * @since 0.1.0
	 *
	 * @param {jQuery} $container The jQuery object of the profile's setting container.
	 */
	obj.scrollToBottom = function( $container ) {
		let totalHeight = 0;
		$container.find( obj.selectors.profileItem ).each( function() {
			totalHeight += $( this ).outerHeight();
		} );

		$( obj.selectors.profileList ).animate( {
			scrollTop: totalHeight
		}, 500 );
	};

	/**
	 * Validates the description and users field is setup for the key pair.
	 *
	 * @since 0.1.0
	 *
	 * @param {jQuery} $profileItem The jQuery object of the profile item wrap.
	 *
	 * @returns {boolean} Whether the description and user field has values.
	 */
	obj.validateFields = function( $profileItem ) {
		const profileName = $profileItem.find( obj.selectors.profileName ).val();
		const intergrationPrimary = $profileItem.find( obj.selectors.profilePrimary ).val();
		if ( profileName && intergrationPrimary ) {
			return true;
		}

		return false;
	};

	/**
	 * Sends an ajax request and calls the onSuccess callback on successful response
	 *
	 * @since 0.1.0
	 *
	 * @param {string} url The URL for the AJAX request
	 * @param {object} requestData The data to send in the AJAX request
	 * @param {Function} onSuccess The callback function to call on successful response
	 * @param {object} event_target The target element of the event
	 */
	const sendAjaxRequest = ( url, requestData, onSuccess, event_target ) => {
		const $profileContainer = $( event_target ).closest( obj.selectors.profileContainer );
		pngxLoader.show( $profileContainer, obj.selectors.pngxAdminLoader );

		$.ajax( {
			url,
			contentType: 'application/json',
			context: $( obj.selectors.profileList ),
			data: requestData,
			success: ( html ) => onSuccess( html, event_target ),
		} );
	};

	/**
	 * Handles the successful response from the backend to add Profile fields.
	 *
	 * @since 0.1.0
	 *
	 * @param {string} html The HTML that adds a message and the page fields html.
	 * @param {object} event_target The target element of the event.
	 */
	obj.onAddProfileFieldsSuccess = function( html, event_target ) {
		const $this = $( event_target );
		const $table = $this.closest( obj.selectors.formTable );
		const $container = $this.closest( obj.selectors.profileContainer );
		const message = $( html ).filter( obj.selectors.messageWrap );
		const profileItemWrap = $( html ).filter( obj.selectors.profileItem );

		$table.find( obj.selectors.messageWrap ).html( message );

		pngxLoader.hide( $container, obj.selectors.pngxAdminLoader );

		if ( profileItemWrap.length > 0 ) {

			$table.find( obj.selectors.profileList ).append( profileItemWrap );

			// Setup dropdowns after loading profile fields.
			const $dropdowns = $table
				.find( pngxDropdowns.selector.dropdown )
				.not( pngxDropdowns.selector.created );

			$dropdowns.pngx_dropdowns();

			$table.find( obj.selectors.profilePrimary ).trigger( 'pngx.dependencies-run' );

			pngxAccordion.bindAccordionEvents( profileItemWrap );
		}
	};


	/**
	 * Handles adding a new profile fields.
	 *
	 * @since 0.1.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleAddProfile = ( event ) => {
		event.preventDefault();
		const url = $( event.target ).attr( 'href' );
		sendAjaxRequest( url, {}, obj.onAddProfileFieldsSuccess, event.target );
	};

	/**
	 * Handles setting up the default profile dropdown on save or delete of an profile.
	 *
	 * @since 0.1.
	 *
	 * @param $table    The jQuery object of the table.
	 * @param profileDefaultProfiles    The HTML of the default profile dropdown.
	 */
	obj.handleDefaultProfiles = function( $table, profileDefaultProfiles ) {
		if ( 0 === profileDefaultProfiles.length ) {
			return
		}

		$table.find( obj.selectors.defaultProfile ).html( profileDefaultProfiles );

		// Setup dropdowns after loading profile fields.
		const $dropdowns = $( obj.selectors.defaultProfile )
			.find( pngxDropdowns.selector.dropdown )
			.not( pngxDropdowns.selector.created );

		$dropdowns.pngx_dropdowns();
	};

	/**
	 * Replaces the profile item in the table.
	 *
	 * @since 0.1.0
	 *
	 * @param {object} $table The table jQuery object.
	 * @param {object} $profileItemWrap The profile item wrap jQuery object.
	 */
	obj.replaceProfileItem = function( $table, $profileItemWrap ) {
		if ( 0 === $profileItemWrap.length ) {
			return;
		}

		const uniqueId = $profileItemWrap.data( 'uniqueId' );
		const existingProfile = $table.find( `[data-unique-id='${uniqueId}']` );
		existingProfile.replaceWith( $profileItemWrap );

		// Setup dropdowns after loading profile fields.
		const $dropdowns = $profileItemWrap
			.find( pngxDropdowns.selector.dropdown )
			.not( pngxDropdowns.selector.created );

		$dropdowns.pngx_dropdowns();

		$document
			.find( `[data-unique-id='${uniqueId}']` )
			.find( obj.selectors.profilePrimary )
			.trigger( 'pngx.dependencies-run' );

		pngxAccordion.bindAccordionEvents( $profileItemWrap );
	};

	/**
	 * Handles the successful response from the backend to save or update profiles.
	 *
	 * @since 0.1.0
	 *
	 * @param {string} html The HTML that adds a message and the page fields html.
	 * @param {object} event_target The target element of the event.
	 */
	obj.onProfileSuccess = function( html, event_target ) {
		const $this = $( event_target );
		const $table = $this.closest( obj.selectors.formTable );
		const $container = $this.closest( obj.selectors.profileContainer );
		const $message = $( html ).filter( obj.selectors.messageWrap );
		const $profileItemWrap = $( html ).filter( obj.selectors.profileItem );
		const profileDefaultProfiles = $( html ).filter( obj.selectors.defaultProfile );

		$table.find( obj.selectors.messageWrap ).html( $message );
		pngxLoader.hide( $container, obj.selectors.pngxAdminLoader );

		obj.replaceProfileItem( $table, $profileItemWrap );
		obj.handleDefaultProfiles( $table, profileDefaultProfiles );
	};

	/**
	 * Handles generating consumer id and secret.
	 *
	 * @since 0.1.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleSave = function( event ) {
		event.preventDefault();

		const $this = $( event.target ).closest( obj.selectors.profileSave );
		const url = $this.data( 'ajaxSaveUrl' );
		const $profileItem = $this.closest( obj.selectors.profileItem );
/*		const is_valid = obj.validateFields( $profileItem );
		if ( !is_valid ) {
			window.alert( $this.data( 'profileError' ) );
			return;
		}*/

		const uniqueId = $profileItem.data( 'uniqueId' );
		const profileName = $profileItem.find( obj.selectors.profileName ).val();
		const $primaryInput = $profileItem.find(obj.selectors.profilePrimary);
		let intergrationPrimary;

		if ($primaryInput.is('select')) {
		  intergrationPrimary = $primaryInput.find('option:selected').val();
		} else if ($primaryInput.is('input[type="text"]')) {
		  intergrationPrimary = $primaryInput.val();
		}
		const intergrationProperties = obj.getInputValuesAndNames( obj.selectors.profileProperties, $profileItem );

		sendAjaxRequest( url, {
			unique_id: uniqueId,
			primary: intergrationPrimary,
			name: profileName,
			properties: intergrationProperties,
		}, obj.onProfileSuccess, $this );
	};

	/**
	 * Handles generating consumer id and secret.
	 *
	 * @since 0.1.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleUpdate = function( event ) {
		event.preventDefault();

		const $this = $( event.target ).closest( obj.selectors.profileUpdate );
		const url = $this.data( 'ajaxUpdateUrl' );
		const $profileItem = $this.closest( obj.selectors.profileItem );
		const uniqueId = $profileItem.data( 'uniqueId' );
		const intergrationProperties = obj.getInputValuesAndNames( obj.selectors.profileProperties, $profileItem );

		sendAjaxRequest( url, {
			unique_id: uniqueId,
			properties: intergrationProperties,
		}, obj.onProfileSuccess, $this );
	};

	/**
	 * Handles the successful response from the backend to delete an profile.
	 *
	 * @since 0.1.0
	 *
	 * @param {string} html The HTML that adds a message and the page fields html.
	 * @param {object} event_target The target element of the event.
	 */
	obj.onDeleteSuccess = function( html, event_target ) {
		const $this = $( event_target );
		const $message = $( html ).filter( obj.selectors.messageWrap );
		const $profileContainer = $( obj.selectors.profileContainer );
		const $table = $this.closest( obj.selectors.formTable );
		const profileDefaultProfiles = $( html ).filter( obj.selectors.defaultProfile );

		$table.find( obj.selectors.messageWrap ).html( $message );

		pngxLoader.hide( $profileContainer, obj.selectors.pngxAdminLoader );

		// Delete marked profile wrap.
		$( `${obj.selectors.profileItem}.to-delete` ).remove();

		obj.handleDefaultProfiles( $table, profileDefaultProfiles );
	};

	/**
	 * Handles revoking an profile key.
	 *
	 * @since 0.1.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleDelete = function( event ) {
		event.preventDefault();

		const $this = $( event.target ).closest( obj.selectors.profileDelete );
		const url = $this.data( 'ajaxDeleteUrl' );
		const $profileItem = $this.closest( obj.selectors.profileItem );
		const uniqueId = $profileItem.data( 'uniqueId' );
		const confirmed = confirm( $this.data( 'confirmation' ) );
		if ( !confirmed ) {
			return;
		}

		// Add a class to mark for deletion.
		$profileItem.addClass( 'to-delete' );

		sendAjaxRequest( url, {
			unique_id: uniqueId,
		}, obj.onDeleteSuccess, $this );
	};

	/**
	 * Get input values and names for elements with the specified class in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param {string} className The class name to search for in the container.
	 * @param {jQuery} $container The jQuery container in which to search for the class.
	 *
	 * @return {Object} An object with input names as keys and their values as values.
	 */
	obj.getInputValuesAndNames = function( className, $container ) {
		const inputs = $container.find( className ).filter( function() {
			const $element = $( this );
			return $element.is( 'input, select, textarea' ) &&
				$element.prop( 'type' ) !== 'button' &&
				$element.prop( 'type' ) !== 'submit';
		} );

		const results = Array.from( inputs ).reduce( function( acc, input ) {
			const $input = $( input );
			const fullName = $input.prop( 'name' );
			const nameMatch = fullName.match( /\['properties'\]\['([^']*)'\]/ );

			if ( nameMatch && nameMatch.length > 1 ) {
				const name = nameMatch[1];

				const inputType = $input.prop( 'type' );
				if ( inputType === 'checkbox' || inputType === 'radio' ) {
					if ( $input.prop( 'checked' ) ) {
						acc[name] = true;
					} else {
						acc[name] = '';
					}
				} else {
					acc[name] = $input.val();
				}
			}

			return acc;
		}, {} );

		return results;
	};

	/**
	 * Bind the profile events.
	 *
	 * @since 0.1.0
	 */
	obj.bindEvents = function() {
		$document
			.on( 'click', obj.selectors.profileUpdate, obj.handleUpdate )
			.on( 'click', obj.selectors.profileSave, obj.handleSave )
			.on( 'click', obj.selectors.profileDelete, obj.handleDelete )
			.on( 'click', obj.selectors.profileAdd, obj.handleAddProfile );

		pngxAccordion.bindAccordionEvents( $( obj.selectors.profileContainer ) );
	};

	/**
	 * Unbind the profile events.
	 *
	 * @since 0.1.0
	 */
	obj.unbindEvents = function() {
	};

	/**
	 * Handles the initialization of the admin when Document is ready
	 *
	 * @since 0.1.0
	 */
	obj.ready = function() {
		obj.bindEvents();
	};

	// Configure on document ready
	$( obj.ready );
})( jQuery, pngx.access_profiles, pngx.loader, pngx.dropdowns, pngx.pngxAccordion );
