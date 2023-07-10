( ( $, obj, wp, GravityMaps ) => {
    "use strict";

	// Create a global Reference for this globally.
	GravityMaps.formAddressAutocomplete = obj;

    /**
     * Pull the data from the PHP localize variable.
     *
     * @since 2.2
     *
     * @var {Object}
     */
    obj.data = window.GKMapsFormAddressAutocompleteData;

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
     * Initializes all the Autocomplete fields, pulling from the localized data.
     *
     * @since 2.2
     *
     * @return {void}
     */
    obj.initAutoComplete = () => {
        if ( 'undefined' === typeof obj.data.fields || ! Array.isArray( obj.data.fields ) ) {
            return;
        }

        obj.data.fields.forEach( obj.initField );
    };

    /**
     * Builds the field ID given a field object.
     *
     * @since 2.2
     *
     * @param {Object} field
     *
     * @return {`#input_${string}_${string}`}
     */
    obj.getFieldId = ( field ) => {
        // For now, we only trigger on the street name part of the address.
        return  obj.hooks.applyFilters( 'gk.maps.autocomplete_field_id', `#input_${field.formId}_${field.id}`, field );
    };

    /**
     * Builds the Individual input ID.
     *
     * @since 2.2
     *
     * @param {Object} field
     * @param {string} inputId
     *
     * @return {`#input_${string}_${string}_${number}`}
     */
    obj.getInputId = ( field, inputId ) => {
        if ( 'undefined' === typeof inputId ) {
            inputId = 1;
        }
        const fieldId = obj.getFieldId( field );

        // For now, we only trigger on the street name part of the address.
        return obj.hooks.applyFilters( 'gk.maps.autocomplete_input_id', `${fieldId}_${inputId}`, field, inputId );
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
    obj.initField = ( field ) => {
        const $input = $( obj.getInputId( field, 1 ) );
        if ( ! $input.length ) {
            return;
        }

        /**
         * Allows third-party inclusion of actions on initializing the field for auto complete
         *
         * @since 2.2
         */
        obj.hooks.doAction( 'gk.maps.autocomplete_field_init', field, $input );

        // @todo move the items below to a separate file and use the action above.
        const options = {
            fields: [
                "address_components",
                "geometry",
            ],
            strictBounds: false,
        };

        switch ( field.addressType ) {
            case 'international':
                break;
            case 'canadian':
                options.componentRestrictions = { country: window.gravitykit.maps.iso_3166_1_alpha_2.getCode( 'canada' ) };
                break;
            default:
                options.componentRestrictions = { country: window.gravitykit.maps.iso_3166_1_alpha_2.getCode( field.addressType ) };
                break;
        }

        // This will prevent the Form from Submitting when Enter is pressed.
        $input.on( 'focus', () => {
            $input.on( 'keypress.gkAutocomplete', ( event ) => {
                if ( event.keyCode !== 13 ){
                    return;
                }

                event.preventDefault(); // Ensure it is only this code that runs

                $input.off( 'keypress.gkAutocomplete' );
            } );
        } );

        field.autocomplete = new google.maps.places.Autocomplete( $input[0], options );

        field.autocomplete.addListener( 'place_changed', () => obj.onPlaceSelection( field ) );
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
    obj.onPlaceSelection = ( field ) => {
        // Verify that the autocomplete field is existent on GMaps.
        if (
            'undefined' === typeof field.autocomplete ||
            'undefined' === typeof field.autocomplete.fields
        ) {
            return;
        }

        const $address1Field = $( obj.getInputId( field, 1 ) );
        const $address2Field = $( obj.getInputId( field, 2 ) );
        const $postalField = $( obj.getInputId( field, 5 ) );

        // Get the place details from the autocomplete object.
        const place = field.autocomplete.getPlace();

        let address1 = '';
        let postcode = '';

        // Get each component of the address from the place details,
        // and then fill-in the corresponding field on the form.
        // place.address_components are google.maps.GeocoderAddressComponent objects
        // which are documented at http://goo.gle/3l5i5Mr
        for ( const component of place.address_components ) {
            const componentType = component.types[0];
            let $field;

			console.log( component );

            switch (componentType) {
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
                case "administrative_area_level_2":
                    $field = $( obj.getInputId( field, 3 ) );
                    if ( ! $field.length ) {
                        break;
                    }

                    $field.val( component.long_name );
                    break;

                case "administrative_area_level_1":
                    $field = $( obj.getInputId( field, 4 ) );
                    if ( ! $field.length ) {
                        break;
                    }

					console.log( $field );
                    $field.val( component.long_name );
                    break;
                case "country":
                    $field = $( obj.getInputId( field, 6 ) );
                    if ( ! $field.length ) {
                        break;
                    }

                    $field.val( component.long_name );
                    break;
            }
        }

        // There are cases where the Address will be empty, Princeton Stadium and JFK are examples.
        if ( $address1Field.length && address1.length ) {
             $address1Field.val( address1 );
        }

		console.log( $address1Field, address1 );

        if ( $postalField.length && postcode.length ) {
             $postalField.val( postcode );
        }

        const $inputLat = $( '<input>' ).attr( {
            type: 'hidden',
            value: place.geometry.location.lat(),
            name: `gk-gravitymap-geolocation[${field.id}][latitude]`
        } );

        const $inputLng = $( '<input>' ).attr( {
            type: 'hidden',
            value: place.geometry.location.lng(),
            name: `gk-gravitymap-geolocation[${field.id}][longitude]`
        } );

        const data = {
            address_components: place.address_components,
            geometry: {
                location: place.geometry.location.toJSON(),
                viewport: place.geometry.viewport.toJSON(),
            }
        };

        const $inputData = $( '<input>' ).attr( {
            type: 'hidden',
            value: JSON.stringify( data ),
            name: `gk-gravitymap-geolocation[${field.id}][data]`
        } );

        $( obj.getFieldId( field ) ).append( $inputLat, $inputLng, $inputData );

        // After filling the form with address components from the Autocomplete
        // prediction, set cursor focus on the second address line to encourage
        // entry of subpremise information such as apartment, unit, or floor number.

        if ( $address2Field.length ) {
            $address2Field.trigger( 'focus' );
        } else if ( $address1Field.length ) {
            $address1Field.trigger( 'focus' );
        }
    };

    $( document ).ready( obj.ready );
} )( window.jQuery, {}, window.wp, window.GravityKit.GravityMaps );

