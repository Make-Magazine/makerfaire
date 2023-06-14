window.gravitykit = window.gravitykit || {};
window.gravitykit.maps = window.gravitykit.maps || {};
window.gravitykit.maps.adminFields = window.gravitykit.maps.adminFields || {};

( ( $, obj ) => {
    /**
     * Initializes address autocomplete field,
     *
     * @param {Event} event
     * @param {Object} field
     * @param {Object} form
     *
     * @return {void}
     */
    obj.initAddressAutocomplete = ( event, field, form ) => {
        // Bail from fields that are not address.
        if ( 'address' !== field.type ) {
            return;
        }

        if ( 'boolean' !== typeof field.EnableGeolocationAutocomplete ) {
            return;
        }

        const $geolocationAutocomplete = $( '#field_enable_geolocation_autocomplete' );
        $geolocationAutocomplete.prop( 'checked', field.EnableGeolocationAutocomplete );
    };

    /**
     * Method that will be called when you change the autocomplete field.
     *
     * @since TBD
     *
     * @return {void}
     */
    obj.setAddressAutocomplete = ( event ) => {
        field = GetSelectedField();
        const $geolocationAutocomplete = $( event.target );
        const isChecked = $geolocationAutocomplete.is( ':checked' );
        SetFieldProperty( 'EnableGeolocationAutocomplete', isChecked );

        UpdateAddressFields();
    };

    /**
     * Triggers when Ready of the document.
     *
     * @since TBD
     *
     * @return {void}
     */
    obj.ready = () => {
      $( document ).on( 'gform_load_field_settings', obj.initAddressAutocomplete );
      $( document ).on( 'change', '.field_enable_geolocation_autocomplete', obj.setAddressAutocomplete );
    };

    $( document ).ready( obj.ready );
} )( jQuery, window.gravitykit.maps.adminFields );

