window.gravitykit = window.gravitykit || {};
window.gravitykit.maps = window.gravitykit.maps || {};
window.gravitykit.maps.adminMapIcons = window.gravitykit.maps.adminMapIcons || {};

( function ( $, obj ) {
	'use strict';

	// Just don't do anything if gform is not loaded.
	if ( typeof gform === 'undefined' ) {
		return;
	}

	/**
	 * Stores the Object for translations from PHP.
	 *
	 * @since 1.9
	 *
	 * @var {object}
	 */
	obj.i18n = window.gravitykit_maps_admin_map_icons_data;

	/**
	 * Gets the choice admin Row HTML.
	 *
	 * @since 1.9
	 *
	 * @param {number|string} index
	 * @param {string} icon
	 * @param {string} icon_id
	 *
	 * @returns {string}
	 */
	obj.getChoicesAdminRowHTML = ( index = 0, icon = '', icon_id = '' ) => {
		return `
			<button type="button" id="gk-gravitymaps-custom-icon-option-upload-button-${index}" class="button gk-gravitymaps-custom-icon-option-upload-button" title="${obj.i18n.uploadIcon}">
				<i class="dashicons dashicons-format-image"></i>
			</button>
			<span id="gk-gravitymaps-custom-icon-option-preview-${index}" class="gk-gravitymaps-custom-icon-option-preview-wrap">
				<span class="gk-gravitymaps-custom-icon-option-preview" style="background-image:url(${icon});"></span>
				<a class="gk-gravitymaps-custom-icon-option-icon-remove" title="${obj.i18n.removeImage}"><i class="dashicons dashicons-no"></i></a>
			</span>
			<input type="hidden" id="gk-gravitymaps-custom-icon-option-icon-${index}" class="gk-gravitymaps-custom-icon-option-icon" value="${icon}" />
			<input type="hidden" id="gk-gravitymaps-custom-icon-option-icon-id-${index}" class="gk-gravitymaps-custom-icon-option-icon-id" value="${icon_id}" />`;
	};

	/**
	 * Event bound to the click on the upload button.
	 *
	 * @param {Event} event
	 *
	 * @return {void}
	 */
	obj.onClickUploadButton = ( event ) => {
		obj.openMediaLibrary( $( event.target ), event.data.$settings );
	};

	/**
	 * Event bound to the click on the remove icon button.
	 *
	 * @param {Event} event
	 *
	 * @return void
	 */
	obj.onClickRemoveIcon = ( event ) => {
		event.preventDefault();
		obj.removePreview( $( event.target ) );
	};

	/**
	 * Initialize the choices in the current field settings.
	 *
	 * @since 1.9
	 *
	 * @param {object} field
	 * @param {jQuery} $settings
	 *
	 * @return {void}
	 */
	obj.getChoices = function ( field, $settings ) {
		if ( typeof field === 'undefined' ) {
			field = GetSelectedField();
		}

		if ( ! obj.fieldCanHaveIcons( field ) ) {
			return;
		}

		if ( typeof $settings === 'undefined' ) {
			$settings = obj.getSettingsElement();
		}

		$settings.find( '.field-choice-row' ).each( function () {
			const $row = $( this );
			const index = $row.data( 'index' );

			const icon = ( field.choices.length && field.choices[ index ].gk_custom_map_icon !== undefined ) ? field.choices[ index ].gk_custom_map_icon : '';
			const icon_id = ( field.choices.length && field.choices[ index ].gk_custom_map_icon_id !== undefined ) ? field.choices[ index ].gk_custom_map_icon_id : '';

			if ( ! $row.find( '.gk-gravitymaps-custom-icon-option-icon' ).length ) {
				$row.find( '.field-choice-text' ).before( obj.getChoicesAdminRowHTML( index, icon, icon_id ) );
			}

			$row.find( '.gk-gravitymaps-custom-icon-option-upload-button' ).on( { click: obj.onClickUploadButton }, null, {$settings: $settings} );
			$row.find( '.gk-gravitymaps-custom-icon-option-icon-remove' ).on( 'click', obj.onClickRemoveIcon );

			const $iconInput = $row.find( '.gk-gravitymaps-custom-icon-option-icon' );
			if ( $iconInput.val() !== '' ) {
				$row.addClass( 'gk-gravitymaps-custom-icon-has-icon' );
			}
		} );
	};

	/**1
	 * Removes the preview for custom icon.
	 *
	 * @since 1.9
	 *
	 * @param {Element} btnEl
	 *
	 * @return {void}
	 */
	obj.removePreview = ( btnEl ) => {
		if ( typeof btnEl === 'undefined' ) {
			return;
		}

		const $choice = $( btnEl ).closest( '[class*="-choice-row"]' );
		$choice.find( '.gk-gravitymaps-custom-icon-option-icon' ).val( '' );
		$choice.find( '.gk-gravitymaps-custom-icon-option-icon-id' ).val( '' );
		$choice.find( '.gk-gravitymaps-custom-icon-option-preview' ).css( 'background-image', '' );
		$choice.removeClass( 'gk-gravitymaps-custom-icon-has-icon' );

		obj.updateFieldChoicesObject();
	};

	/**
	 * Update the field choices object in the GForm settings.
	 *
	 * @since 1.9
	 *
	 * @return {void}
	 */
	obj.updateFieldChoicesObject = () => {
		const field = GetSelectedField();

		const $fieldSettings = obj.getSettingsElement();
		$fieldSettings.find( '[class*="-choice-row"]' ).each( function ( index ) {
			const $choice = $( this );
			const icon = $choice.find( '.gk-gravitymaps-custom-icon-option-icon' ).val();
			const icon_id = $choice.find( '.gk-gravitymaps-custom-icon-option-icon-id' ).val();
			const i = $choice.data( "index" );
			if ( icon !== '' ) {
				$choice.addClass( 'gk-gravitymaps-custom-icon-has-icon' );
				$choice.find( '.gk-gravitymaps-custom-icon-option-preview' ).css( 'background-image', 'url(' + icon + ')' );
			} else {
				$choice.removeClass( 'gk-gravitymaps-custom-icon-has-icon' );
			}
			field.choices[ i ].gk_custom_map_icon = icon;
			field.choices[ i ].gk_custom_map_icon_id = icon_id;
		} );
	};

	/**
	 * Get the current select field in GForm.
	 *
	 * @since 1.9
	 *
	 * @returns {*|jQuery|HTMLElement}
	 */
	obj.getFieldElement = () => {
		const field = GetSelectedField();
		return $( '#field_' + field.id );
	};

	/**
	 * Toggle the Enable icon setting for this field.
	 *
	 * @since 1.9
	 *
	 * @param {boolean} enable
	 *
	 * @return {void}
	 */
	obj.toggleEnableIcons = ( enable ) => {
		const $settings = obj.getSettingsElement();
		const $toggle = $settings.find( 'input.gk_gravitymaps_choice_marker_icons_enabled' );

		if ( enable === undefined ) {
			enable = $toggle.is( ':checked' );
		}

		const $field = obj.getFieldElement();

		SetFieldProperty( 'gk_custom_map_icon_enabled', enable );

		$toggle.prop( 'checked', enable );

		const $choicesContainer = $( '#gfield_settings_choices_container' );
		$choicesContainer.toggleClass( 'gk-gravitymaps-custom-icon-enabled', enable );

		$field.toggleClass( 'gk-gravitymaps-custom-icon-use-icons', enable );
		$settings.toggleClass( 'gk-gravitymaps-custom-icon-use-icons', enable );

	};

	/**
	 * Modify the WordPress media Modal to fit the copy needed for GK Maps.
	 *
	 * @since 1.9
	 *
	 * @return {void}
	 */
	obj.modifyWPMediaModal = () => {
		$( 'button.media-button-insert, button.media-button-select' ).text( obj.i18n.useIcon );
	};

	/**
	 * Opens the WordPress media library for the user to select an existing or upload a new image.
	 *
	 * @since 1.9
	 *
	 * @param {Element} btnEl
	 * @param {jQuery} $settings
	 *
	 * @return {void}
	 */
	obj.openMediaLibrary = ( btnEl, $settings ) => {
		if ( typeof btnEl === 'undefined' ) {
			return;
		}

		const $choice = $( btnEl ).closest( '[class*="-choice-row"]' );

		let fileFrame = $choice.data( 'file-frame' );

		// If the media frame already exists, reopen it.
		if ( fileFrame ) {
			// Open frame
			fileFrame.open();
			return;
		}

		// Create the media frame.
		fileFrame = wp.media( {
			title: 'Select an icon to upload',
			button: {
				text: 'Use this icon'
			},
			frame: 'post',
			state: 'insert',
			multiple: false	// Set to true to allow multiple files to be selected
		} );

		setTimeout( obj.modifyWPMediaModal, 100 );

		// When an image is selected, run a callback.
		fileFrame.on( 'insert', ( selection ) => {
			const state = fileFrame.state();
			selection = selection || state.get( 'selection' );
			if ( ! selection ) {
				return;
			}

			let attachment = selection.first();
			const display = state.display( attachment ).toJSON();
			attachment = attachment.toJSON();

			const iconUrl = attachment.sizes[ display.size ].url;// You could force this to full, replace display.size with 'full'

			$choice.find( '.gk-gravitymaps-custom-icon-option-icon' ).val( iconUrl );
			$choice.find( '.gk-gravitymaps-custom-icon-option-icon-id' ).val( attachment.id );
			$choice.find( '.gk-gravitymaps-custom-icon-option-preview' ).css( 'background-image', `url('${iconUrl}')` );
			$choice.addClass( 'gk-gravitymaps-custom-icon-has-icon' );

			obj.updateFieldChoicesObject();

			$settings.find( '[data-js="choices-ui-trigger"]' ).trigger( 'click' );
		} );

		// Finally, open the modal
		fileFrame.open();

		$choice.data( 'file-frame', fileFrame );
	};

	/**
	 * Determines if the current field can have custom icons.
	 *
	 * @since 1.9
	 *
	 * @param {HTMLElement} field
	 *
	 * @returns {boolean}
	 */
	obj.fieldCanHaveIcons = ( field ) => {

		if ( typeof field === 'undefined' || ! field.hasOwnProperty( 'type' ) ) {
			return false;
		}

		const fieldTypes = [ 'select', 'radio' ];

		return fieldTypes.indexOf( field.type ) !== -1;
	};

	/**
	 * Get the Field Settings container.
	 *
	 * @since 1.9
	 *
	 * @returns {*|jQuery|HTMLElement}
	 */
	obj.getSettingsElement = () => $( '#field_settings_container, #choices-ui-flyout' );

	/**
	 * Triggers when the Gravity Forms Field Settings are loaded.
	 *
	 * @since 1.9
	 *
	 * @param {Event} event
	 * @param {object} field
	 * @param {object} form
	 *
	 * @return {void}
	 */
	obj.onFieldLoadSettings = ( event, field, form ) => {

		obj.unbindEvents( field );

		obj.includeSettings( field );

		if ( ! obj.fieldCanHaveIcons( field ) ) {
			return;
		}

		obj.bindEvents( field );
	};

	/**
	 * Bound to the GForms dropdown choices generation.
	 *
	 * @since 1.9
	 *
	 * @param {array} fields
	 *
	 * @return {void}
	 */
	obj.onFieldChoicesLoad = ( fields ) => {
		const field = ( typeof fields !== 'undefined' && fields.length ) ? fields[ 0 ] : GetSelectedField();

		obj.unbindEvents( field );

		obj.includeSettings( field );

		if ( ! obj.fieldCanHaveIcons( field ) ) {
			return;
		}
		obj.bindEvents( field );
	};

	/**
	 * Includes the HTML for the Settings related to custom Icon maps.
	 *
	 * @since 1.9
	 *
	 * @param {object} field
	 *
	 * @return {void}
	 */
	obj.includeSettings = ( field ) => {
		const $field = $( '#field_' + field.id );
		const $fieldSettings = obj.getSettingsElement();

		if ( obj.fieldCanHaveIcons( field ) ) {
			$field.addClass( 'gk-gravitymaps-custom-icon-admin-field' );
			$fieldSettings.addClass( 'gk-gravitymaps-custom-icon-field-settings' );

			obj.toggleEnableIcons( field.gk_custom_map_icon_enabled === true );
		} else {

			$field.removeClass( 'gk-gravitymaps-custom-icon-admin-field' );
			$fieldSettings.removeClass( 'gk-gravitymaps-custom-icon-field-settings' );
			obj.toggleEnableIcons( false );
		}

		obj.getChoices( field );
	};

	/**
	 * Binds the events with a gkMapsIcons to the GForm dropdown fields.
	 *
	 * @since 1.9
	 *
	 * @param {object} field
	 *
	 * @return {void}
	 */
	obj.bindEvents = ( field ) => {
		const $fieldSettings = obj.getSettingsElement();

		$fieldSettings.find( '[data-js="choices-ui-trigger"]' ).on( 'click.gkMapsIcons', ( event ) => {
			const $choicesFlyout = $( '[data-js="choices-ui-flyout"]:visible' );

			if ( obj.fieldCanHaveIcons( field ) ) {

				$choicesFlyout.addClass( 'gk-gravitymaps-custom-icon-admin-field' );
				$fieldSettings.addClass( 'gk-gravitymaps-custom-icon-field-settings' );

				obj.toggleEnableIcons( field.gk_custom_map_icon_enabled === true );

				obj.getChoices( field, $choicesFlyout );
			}
		} );

		$( '.gk_gravitymaps_choice_marker_icons_enabled' ).on( 'change.gkMapsIcons', ( event ) => {
			const $field = $( event.target );

			obj.toggleEnableIcons( $field.is( ':checked' ) );
		} );
	};

	/**
	 * Unbinds the events with a gkMapsIcons to the GForm dropdown fields.
	 *
	 * @since 1.9
	 *
	 * @param {object} field
	 *
	 * @return {void}
	 */
	obj.unbindEvents = ( field ) => {
		const $fieldSettings = obj.getSettingsElement();

		$fieldSettings.find( '[data-js="choices-ui-trigger"]' ).off( 'click.gkMapsIcons' );

		$( '.gk_gravitymaps_choice_marker_icons_enabled' ).off( 'change.gkMapsIcons');
	};

	gform.addAction( 'gform_load_field_choices', obj.onFieldChoicesLoad );

	$( document ).on( 'gform_load_field_settings', obj.onFieldLoadSettings );

	$( document ).ready( () => {
		$( 'body' ).on( 'click', 'li.attachment, .media-menu-item', obj.modifyWPMediaModal );
	} )
} )( jQuery, window.gravitykit.maps.adminMapIcons );
