jQuery(document).ready(function($){

	// On select mapify_location_maps multiple selections ACF relationship
	$( 'div[data-name=mapify_location_maps] .acf-input' ).on( 'click', '.choices-list li span', function() {
		setTimeout(function(){ 
			get_selected_relationship(); 
		}, 1);
	} );

	$( 'div[data-name=mapify_location_maps] .acf-input' ).on( 'click', '.values li span a', function() {
		setTimeout(function(){ 
			get_selected_relationship(); 
		}, 1);
	} );

	function get_selected_relationship() {
		var selected_ids = [];
		
		$( 'div[data-name=mapify_location_maps] .acf-input .values li' ).each(function( index ) {
			var id = $( this ).children( 'span' ).data('id');
			selected_ids.push( parseInt( id ) );
		});

		// Ajax calls after we done collecting the selected_ids
		setTimeout(function(){ 
			var data = {
				action     : 'acf_mapifypro_get_maps_tags',
				ajax_nonce : $('#acf_mapifypro_maps_tags_nonce').val(),
				map_ids    : selected_ids
			}
	
			$.post(ajaxurl, data, function(response){
				$('#acf-mapifypro-maps-tags').html(response);
			});

		}, 1);

	}

	// On select generated tag
	$('#acf-mapifypro-maps-tags').on('click', '.acf-map-tag-link', function() {
		var tag_id   = $( this ).data( 'id' );
		var tag_name = $( this ).data( 'name' );

		if ( typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined' ) {
			var is_tax_panel_opened = wp.data.select( 'core/edit-post' ).isEditorPanelOpened( 'taxonomy-panel-location-tag' );
			var selected_ids        = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'location-tag' );			
			var new_ids             = [ tag_id ];

			// add selected ids if any
			for ( var i = 0; i < selected_ids.length; i++ ) {
				var new_id = selected_ids[ i ];

				if ( ! new_ids.includes( new_id ) ) {
					new_ids.push( new_id ); 
				}
			}

			// update tags
			wp.data.dispatch( 'core/editor' ).editPost( { 'location-tag': new_ids } );

			// close and re-open the panel to reload the term data
			if ( is_tax_panel_opened ) {
				wp.data.dispatch( 'core/edit-post' ).toggleEditorPanelOpened( 'taxonomy-panel-location-tag' );
				wp.data.dispatch( 'core/edit-post' ).toggleEditorPanelOpened( 'taxonomy-panel-location-tag' );
			}
		} else {
			$( 'input[name="newtag[location-tag]"]' ).val( tag_name ).next().click();
		}

		return false;
	});

});