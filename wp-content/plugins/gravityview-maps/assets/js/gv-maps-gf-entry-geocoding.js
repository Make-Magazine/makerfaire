/* global ajaxurl,jQuery,commonL10n */
/**
 * Custom JS for Gravity Forms entry view (admin) page
 *
 * @package   GravityView Maps
 * @license   GPL2+
 * @author    GravityView <hello@gravityview.co>
 * @link      https://gravityview.co
 * @copyright Copyright 2018, Katz Web Services, Inc.
 *
 * @since 1.6
 */

( function( $ ) {

	'use strict';

	var gvGFEntryMaps = {

		options: window.GV_MAPS_GEOCODING || false,

		/**
		 * Configure address click/change events
		 */
		init: function() {

			if ( ! gvGFEntryMaps.options ) {
				return;
			}

			gvGFEntryMaps.bindGeocodeOrSaveCoordinatesClickEvent();
			gvGFEntryMaps.bindUpdateClickEvent();
			gvGFEntryMaps.bindInputFieldEvent();
			gvGFEntryMaps.bindGoogleMapsClickEvent();
		},

		/**
		 * Validate latitude/longitude
		 */
		bindInputFieldEvent: function() {
			$( '.gv-maps-geocoding-container' ).find( '.geocoding_info input' ).on( 'keyup keypress', function( e ) {
				var $parent              = $( this ).parents( '.gv-maps-geocoding-container' ),
						$updateButton        = $parent.find( '.button.update' ),
						$googleMapsContainer = $parent.find( '.gv-maps-google_map_link' ),
						$googleMapsLink      = $googleMapsContainer.find( 'a' ),
						$geocodeButton       = $parent.find( '.button.geocode-address' ),
						meta                 = gvGFEntryMaps.getAddressFieldMeta( $parent ),
						lat                  = $( '#lat_' + meta.fieldId ).val().trim(),
						long                 = $( '#long_' + meta.fieldId ).val().trim(),
						isValidInput         = /^(|[\-0-9.]+)$/.test( $( this ).val().trim() ),
						addressExists        = $parent.hasClass( 'address_exists' ),
						combinedChanges      = lat + long,
						areValidCoordinates  = /^([\-0-9.]+)$/.test( lat ) && /^([\-0-9.]+)$/.test( long ) && combinedChanges.length,
						fieldChanged         = $updateButton.attr( 'data-original-data' ) != combinedChanges;

				// Display "update" button only when valid coordinates are entered or cleared altogether
				if ( fieldChanged && addressExists ) {
					var showUpdateButton = false;

					if ( areValidCoordinates || ( ! lat && ! long ) ) {
						showUpdateButton = true;
					}

					$updateButton.toggleClass( 'hidden', ! showUpdateButton );
				}

				$googleMapsContainer.toggleClass( 'hidden', ! isValidInput || ! lat || ! long );

				if ( ! addressExists ) {
					$geocodeButton
						.addClass( 'save_coordinates' )
						.find( '.label' ).text( gvGFEntryMaps.options.localization.save_coordinates_label );
				}

				$( this ).toggleClass( 'invalid', ! isValidInput );

				// Update Google Maps anchor href with changed lat/long
				if ( fieldChanged && isValidInput ) {
					$googleMapsLink.attr( 'href', 'https://www.google.com/maps/search/' + parseFloat( lat, 10 ) + ',' + parseFloat( long, 10 ) );
				}

				// Return key pressed, submitting changes
				if ( e.keyCode === 13 ) {
					e.preventDefault();

					if ( fieldChanged && isValidInput ) {
						$updateButton.trigger( 'click' );
					}

					return false;
				}

			} ).trigger( 'keypress' );
		},

		/**
		 * Open position on Google Maps
		 */
		bindGoogleMapsClickEvent: function() {
			$( '.gv-maps-google_map_link' ).on( 'click', 'a', function( e ) {
				e.preventDefault();

				if ( $( this ).hasClass( 'disabled' ) ) {
					return;
				}

				// Bypass browser popup blockers by creating and clicking a new anchor element
				$( '<a>', {
					href: $( this ).attr( 'href' ),
					target: '_blank',
					rel: 'noopener noreferrer'
				} )[ 0 ].click();
			} );

		},

		/**
		 * Fetch or save latitude/longitude for field address and cache on the server
		 */
		bindGeocodeOrSaveCoordinatesClickEvent: function() {
			$( '.button.geocode' ).on( 'click', function( e ) {
				e.preventDefault();

				if ( $( this ).hasClass( 'disabled' ) ) {
					return;
				}

				var el              = this,
						gf_field        = $( el ).parents( '.entry-view-field-value' ),
						parent          = $( el ).parents( '.gv-maps-geocoding-container' ),
						meta            = gvGFEntryMaps.getAddressFieldMeta( parent ),
						saveCoordinates = $( this ).hasClass( 'save_coordinates' ),
						lat             = $( '#lat_' + meta.fieldId ).val(),
						long            = $( '#long_' + meta.fieldId ).val(),
						data            = {
							action: saveCoordinates ? gvGFEntryMaps.options.action_save : gvGFEntryMaps.options.action_geocode,
							nonce: gvGFEntryMaps.options.nonce,
							meta: meta,
							lat: lat,
							long: long,
						};

				$.when( gvGFEntryMaps.doRemoteRequest( data, this ) ).then( function( data ) {
					var message;

					if (  saveCoordinates ) {
						if ( 0 === data.lat.length ) {
							message = gvGFEntryMaps.options.localization.cleared_success_notice;
						} else {
							message = gvGFEntryMaps.options.localization.saved_success_notice;
						}
					} else {
						message = gvGFEntryMaps.options.localization.geocoded_success_notice;
					}

					gvGFEntryMaps.displayRemoteRequestStatusMessage(
						el,
						'success',
						message
					);

					$( '#lat_' + meta.fieldId, parent ).removeClass( 'invalid' ).val( data.lat );
					$( '#long_' + meta.fieldId, parent ).removeClass( 'invalid' ).val( data.long );

					$( '.gv-maps-geocoding-position', gf_field ).text( gvGFEntryMaps.getGeocodingText( data ) );

					$( '.input_field_container', gf_field ).addClass( 'visible' );

					if ( data.lat.length ) {
						// Update Google Maps anchor href with changed lat/long
						$googleMapsLink.attr( 'href', 'https://www.google.com/maps/search/' + lat + ',' + long );
					}

					parent.find( '.geocoding_info' ).removeClass( 'hidden' );
					parent.find( '.gv-maps-google_map_link' ).toggleClass( 'hidden', ( 0 === data.lat.length ) );
					parent.find( '.geocoding_not_available' ).addClass( 'hidden' );
					parent.find( '.button.update' ).attr( 'data-original-data', String( data.lat ) + String( data.long ) ).addClass( 'hidden' );
				} ).fail( function( message ) {
					gvGFEntryMaps.displayRemoteRequestStatusMessage(
						el,
						'error',
						message ? message : ( saveCoordinates ) ? gvGFEntryMaps.options.localization.saved_error_notice : gvGFEntryMaps.options.localization.geocoded_error_notice
					);
				} );
			} );
		},

		/**
		 * Update latitude/longitude for field address and cache on the server
		 */
		bindUpdateClickEvent: function() {
			$( '.gv-maps-geocoding-container .button.update' ).on( 'click', function( e ) {
				e.preventDefault();

				if ( $( this ).hasClass( 'disabled' ) ) {
					return;
				}

				var el       = this,
						gf_field = $( el ).parents( '.entry-view-field-value' ),
						parent   = $( el ).parents( '.gv-maps-geocoding-container' ),
						meta     = gvGFEntryMaps.getAddressFieldMeta( parent ),
						data     = {
							action: gvGFEntryMaps.options.action_save,
							nonce: gvGFEntryMaps.options.nonce,
							lat: $( '#lat_' + meta.fieldId ).val(),
							long: $( '#long_' + meta.fieldId ).val(),
							meta: meta,
						};

				$.when( gvGFEntryMaps.doRemoteRequest( data, this ) ).then( function() {

					gvGFEntryMaps.displayRemoteRequestStatusMessage( el, 'success', gvGFEntryMaps.options.localization.updated_success_notice );

					$( '.gv-maps-geocoding-position', gf_field ).text( gvGFEntryMaps.getGeocodingText( data ) );
					parent.find( '.button.update' ).attr( 'data-original-data', String( data.lat ) + String( data.long ) ).addClass( 'hidden' );
				} ).fail( function( message ) {
					gvGFEntryMaps.displayRemoteRequestStatusMessage( el, 'error', message || gvGFEntryMaps.options.localization.updated_error_notice );
				} );
			} );
		},

		/**
		 * Make AJAX request
		 *
		 * @param {Object} data Server request data
		 * @param {Object} el Element that triggered the action
		 *
		 * @returns {Promise}
		 */
		doRemoteRequest: function( data, el ) {
			var defer   = $.Deferred(),
					spinner = $( el ).find( '.spinner' );

			spinner.show();
			$( el ).addClass( 'disabled' );

			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: data,
				async: true,
			} )
				.success( function( response ) {
					if ( ! response.success ) {
						// Check if WP_Error message is defined (response.data[0].message)
						var error_object = ( response.data || [] );
						var error = ( error_object[ 0 ] || error_object ).message || null;

						defer.reject( error );
					}
					defer.resolve( response.data );
				} )
				.fail( function() {
					defer.reject( gvGFEntryMaps.options.localization.remote_request_fail_notice );
				} )
				.always( function() {
					$( el ).removeClass( 'disabled' );
					spinner.hide();
				} );

			return defer.promise();
		},

		/**
		 * Display notice with remote request status message
		 *
		 * @param el {Object} Element that triggered the action
		 * @param type {string} Notice type (e.g., error or success)
		 * @param message {string} Notice message
		 */
		displayRemoteRequestStatusMessage: function( el, type, message ) {

			var parent = $( el ).parents( '.entry-view-field-value' ),
					status = parent.find( '.gv-maps-geocoding-status' ).first();

			status
			// Only display one notice at a time
				.hide()
				// We copy the status and modify the status DIV because dismissing a notice destroys the element
				.clone()
				.html( '<p>' + message + '</p>' )
				.prependTo( parent )
				.removeClass( 'notice-error notice-success' )
				.addClass( 'notice-' + type )
				.show()
				.css( 'opacity', '1' );

			$( document ).trigger( 'wp-updates-notice-added' );
		},

		/**
		 * Get form ID, field ID and entry ID data attributes from an element
		 *
		 * @param {Object} el Element with data attributes
		 *
		 * @returns {{formId: (*|null), fieldId: (*|null), entryId: (*|null)}}
		 */
		getAddressFieldMeta: function( el ) {
			return {
				formId: $( el ).attr( 'data-form-id' ) || null,
				fieldId: $( el ).attr( 'data-field-id' ) || null,
				entryId: $( el ).attr( 'data-entry-id' ) || null,
			};
		},

		/**
		 * Generate the text for the "Geocoding" line in the field's address output
		 *
		 * @param {object} data `action`, `nonce`, `lat`, `long`, `meta` values
		 *
		 * @return {string} If lat/long are empty, reset displayed value to "not yet geocoded"
		 */
		getGeocodingText: function( data ) {

			if ( '' === data.lat ) {
				return gvGFEntryMaps.options.localization.geocoding_not_yet;
			}

			return data.lat + ', ' + data.long;
		},
	};

	gvGFEntryMaps.init();

}( jQuery ) );
