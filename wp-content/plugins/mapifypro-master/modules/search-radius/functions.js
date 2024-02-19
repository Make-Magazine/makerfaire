(function($, $window, $document){

$( 'body' ).on( 'mapify.map.created', function(e, map ) {
	var $searchDropdown = map.$container.find( 'select[name="mpfy_search_radius"]:first' );
	if ( $searchDropdown.length == 0 ) {
		return;
	}

	$searchDropdown.on('change', function(){
		map.settings.search.radius = parseInt( $( this ).val() );
	}).trigger( 'change' );
});

})(jQuery, jQuery(window), jQuery(document));