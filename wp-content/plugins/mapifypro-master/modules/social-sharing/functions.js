( function( $ ) {

	/**
	 * Reinitialize sharethis API after ajax calls
	 */
	$( 'body' ).on( 'mpfy_popup_opened', function( e ) {
		if ( $( '.mpfy-p-social' ).length && 'undefined' !== typeof window.__sharethis__ ) {
			window.__sharethis__.initialize();
		}
	});

})( jQuery );