;(function($) {
	var $doc = $( document );	

	$doc.ready(function($) {
		$('#mpfy-multi-map-shortcode-modal').dialog({
			'width'    : 600,
			'modal'    : true,
			'autoOpen' : false
		});

		// genefate multi-map shortcode
		$('#mpfy-multi-map-generate-shorcode').on('click', function() {
			var selected_map_ids = get_selected_map_ids();
			var map_ids          = selected_map_ids.join(',');
			var height           = parseInt( $('div[data-name=maps_height] .acf-input input').val() );
			var typed_label      = $('div[data-name=label] .acf-input input').val();
			var label            = typed_label.replace(/["]+/g, '')
			var label_bg_color   = $('div[data-name=label_color] .acf-input input').val();

			// generate shortcode
			$('#mpfy-multi-map-shortcode').val(
				'[mpfy-multi-map map_ids="' + map_ids + '" height="' + height + '" label="' + label + '" label_bg_color="' + label_bg_color + '" ]'
			);

			// show modal dialog
			$('#mpfy-multi-map-shortcode-modal').dialog('open');
		});
	});

	// Get selected map_ids from select ACF relationship input
	function get_selected_map_ids() {
		var multi_map_ids = [];

		$( 'div[data-name=maps_to_include] .acf-input .values li' ).each(function( index ) {
			var id = $( this ).children( 'span' ).data('id');
			multi_map_ids.push( parseInt( id ) );
		});

		return multi_map_ids;
	}
})(jQuery);