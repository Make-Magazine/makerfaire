( ( $, obj, GravityMaps ) => {
	"use strict";

	// Create a global Reference for this globally.
	GravityMaps.searchAddressAutocomplete = obj;

	// Run some magic to allow a better handling of class names for jQuery.hasClass type of methods
	obj.className = ( value ) => {
		// Prevent Non Strings to be included
		if (
			(
				'string' !== typeof value
				&& ! this instanceof String
			)
			|| 'function' !== typeof value.replace
		) {
			return value;
		}

		return value.replace( '.', '' );
	};

	obj.selectors = {
		fields: '[data-js-gk-autocomplete]',
		searchBox: '.gv-search-box',
		searchWidget: '.gv-widget-search',
		defaultLatField: '.gk-maps-search-geolocation-lat',
		defaultLngField: '.gk-maps-search-geolocation-lng',
		hiddenAddressSearch: 'input[type="hidden"][name="address_search"], input[type="hidden"][disabled-name="address_search"]',
	};

	obj.$fields = null;

	/**
	 * @inheritDoc
	 */
	obj.hooks = GravityMaps.hooks;

	/**
	 * Triggers when Ready of the document.
	 *
	 * @since 2.2
	 *
	 * @return {void}
	 */
	obj.ready = () => {
		obj.initAutoComplete();
	};

	/**
	 * Initializes all the Autocomplete fields in the screen.
	 *
	 * @since 2.2
	 *
	 * @return {void}
	 */
	obj.initAutoComplete = () => {

		obj.$fields = $( obj.selectors.fields );

		obj.$fields.each( ( index, element ) => {
			const $field = $( element );

			// @todo allow adding options here.
			obj.initField( $field, {} );
		} );
	};

	/**
	 * Initializes one field.
	 *
	 * @since 2.2
	 *
	 * @param {Object} field
	 *
	 * @return {void}
	 */
	obj.initField = ( $field, options ) => {
		/**
		 * Allows third-party inclusion of actions on initializing the field for auto complete
		 *
		 * @since 2.2
		 */
		obj.hooks.doAction( 'gravitykit/maps/view_search/autocomplete_init', $field );

		const defaultOptions = {
			fields: [
				"address_components",
				"geometry",
			],
			strictBounds: false,
		};

		// @todo apply filter
		options = $.extend( {}, defaultOptions, options );

		const autocomplete = new google.maps.places.Autocomplete( $field[ 0 ], options );

		$field.data( 'gkAutocomplete', autocomplete );

		autocomplete.addListener( 'place_changed', () => obj.onPlaceSelection( $field ) );

		// This allows you to select the location with Enter.
		$field.on( 'keydown', ( event ) => {
			if ( event.keyCode === 13 ) {
				event.preventDefault();
			}
		} )
			.on( 'change', () => {
				const fields = obj.getDefaultFieldCallbacks();
				$field.data( 'gkAutoCompletePreventClear', 0 );

				obj.toggleGeolocationFields( $field, $field.val() != '' );
				obj.repopulateSearchWidgetState( $field );
 			} )
			.on( 'focusin', ( event ) => {
				$field.one( 'focusout', ( event ) => {
					const preventClear = $field.data( 'gkAutoCompletePreventClear' );

					if ( ! preventClear ) {
						const fields = obj.getDefaultFieldCallbacks();
						$field.val( '' );
						$field.data( 'gkAutoCompletePreventClear', 0 );

						fields.lat( $field ).val( '' );
						fields.lng( $field ).val( '' );
						fields.hiddenAddressSearch( $field ).val( '' );

						obj.repopulateSearchWidgetState( $field );
					}
				} );
			} );
	};

	obj.toggleGeolocationFields = ( $field, toggleTo ) => {
		const $searchWidget = obj.getSearchWidget( $field );
		let $fields = $searchWidget.find( '.gk-search-geolocation-field' ).find( '[name], [disabled-name]' );

		$fields = $fields.add( $searchWidget.find( '[name="address_search"], [name="long"], [name="lat"], [disabled-name="address_search"], [disabled-name="long"], [disabled-name="lat"]' ) );

		return $fields.each( ( index, field ) => {
			const $fieldToModify = $( field );
			if ( toggleTo ) {
				$fieldToModify
					.attr( 'name', $fieldToModify.attr( 'disabled-name' ) )
					.attr( 'disabled-name', null );
			} else {
				$fieldToModify
					.attr( 'disabled-name', $fieldToModify.attr( 'name' ) )
					.attr( 'name', null );
			}
		} );
	};

	obj.repopulateSearchWidgetState = ( $field ) => {
		const $searchWidget = obj.getSearchWidget( $field );
		return $searchWidget.attr( 'data-state', $searchWidget.serialize() );
	};

	obj.getSearchWidget = ( $field ) => {
		return $field.parents( obj.selectors.searchWidget ).eq( 0 );
	};

	obj.getDefaultFieldCallbacks = () => {
		return {
			lat: ( $field ) => {
				return $field.parents( obj.selectors.searchBox ).eq( 0 ).find( obj.selectors.defaultLatField );
			},
			lng: ( $field ) => {
				return $field.parents( obj.selectors.searchBox ).eq( 0 ).find( obj.selectors.defaultLngField );
			},
			hiddenAddressSearch: ( $field ) => {
				return obj.getSearchWidget( $field ).find( obj.selectors.hiddenAddressSearch );
			},
		};
	};

	/**
	 * Normally triggered when a place is selected on the autocomplete dropdown.
	 *
	 * @todo Untangle this method from GoogleMaps so we use on third-party maps.
	 *
	 * @since 2.2
	 *
	 * @param {Object} field
	 */
	obj.onPlaceSelection = ( $field, fields ) => {
		const autocomplete = $field.data( 'gkAutocomplete' );
		// Verify that the autocomplete field is existent on GMaps.
		if ( 'undefined' === typeof autocomplete ) {
			return;
		}

		fields = $.extend( fields, obj.getDefaultFieldCallbacks() );

		if ( typeof window.gravitykit.maps.searchFields !== 'undefined' ) {
			window.gravitykit.maps.searchFields.resetCurrentLocation( $field );
		}

		// Get the place details from the autocomplete object.
		const place = autocomplete.getPlace();

		let address1 = '';
		let postcode = '';

		// Get each component of the address from the place details,
		// and then fill-in the corresponding field on the form.
		// place.address_components are google.maps.GeocoderAddressComponent objects
		// which are documented at http://goo.gle/3l5i5Mr
		for ( const component of place.address_components ) {
			const componentType = component.types[ 0 ];

			switch ( componentType ) {
				case "street_number": {
					address1 = `${component.long_name} ${address1}`;
					break;
				}

				case "route": {
					address1 += component.short_name;
					break;
				}

				case "postal_code": {
					postcode = `${component.long_name}${postcode}`;
					break;
				}

				case "postal_code_suffix": {
					postcode = `${postcode}-${component.long_name}`;
					break;
				}

				case "locality":
					// component.long_name
					break;

				case "administrative_area_level_1": {
					// component.long_name
					break;
				}
				case "country":
					// component.long_name
					break;
			}
		}

		$field.data( 'gkAutoCompletePreventClear', 1 );

		fields.lat( $field ).val( place.geometry.location.lat() );
		fields.lng( $field ).val( place.geometry.location.lng() );
		fields.hiddenAddressSearch( $field ).remove();

		// After filling the form with address components from the Autocomplete
		// prediction, set cursor focus on the second address line to encourage
		// entry of subpremise information such as apartment, unit, or floor number.

		$field.trigger( 'focus' );

		obj.toggleGeolocationFields( $field, true );

		obj.repopulateSearchWidgetState( $field );
	};

	$( document ).ready( obj.ready );
} )( window.jQuery, {}, window.GravityKit.GravityMaps );

