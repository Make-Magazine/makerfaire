jQuery(function() {
    jQuery('.js-dce-select').select2();
});

// Checkbox for all features on Dashboard
jQuery( document ).on( 'click', '#dce-feature-all', function() {
	if ( jQuery( this ).is( ':checked' ) ) {		
		jQuery( this ).closest( '.dce-container' ).find( 'input.dce-checkbox' ).prop( 'checked', true );
	} else {
		jQuery( this ).closest( '.dce-container' ).find( 'input.dce-checkbox' ).prop( 'checked', false );
	}
} );
