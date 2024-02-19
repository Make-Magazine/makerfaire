jQuery( document ).ready( function( $ ) {
	if ( mpfy_verify_license_object.is_new_license_version ) {		
		var data = {
			action     : 'verify_license',
			mpfy_nonce : $('input[name=mpfy_nonce]').val()
		}

		$.post( mpfy_verify_license_object.ajaxurl, data, function( new_notice ) {
			$('.mpfy-notice').replaceWith( new_notice );
		});
	}
} );