const mapStyles = new MapifyMapStyles(L);

/**
 * Initialize the map on page loaded
 */
jQuery(document).ready(function($){
	mapStyles.add({
		'image': new L.TileLayer( osm_var.upload_dir + '/mpfy/' + osm_var.post_id + '/z{z}-tile_{y}_{x}.png?cache-buster={cacheBuster}', {
			minZoom     : 0,
			maxZoom     : 4,
			noWrap      : true,
			cacheBuster : mapify_get_random_integer(0,1000)
		})
	});

	// Available map layers
	var layers = mapStyles.get();

	// Set initial location, marker, zoom level, etc
	var canvas_id  = 'acf-osm-map-canvas'
	var map_canvas = $( '#' + canvas_id );
	var layer_id   = 'osm';
	var args       = {
		'selected_location'   : [ map_canvas.data('selected-lat'), map_canvas.data('selected-lng') ],
		'centered_location'   : [ map_canvas.data('centered-lat'), map_canvas.data('centered-lng') ],
		'zoom_level'          : map_canvas.data('zoom-level'),
		'map_info_fields'     : {
			'selected_lat'    : $('#selected_lat'),
			'selected_lng'    : $('#selected_lng'),
			'centered_lat'    : $('#centered_lat'),
			'centered_lng'    : $('#centered_lng'),
			'zoom_level'      : $('#zoom_level'),
		},
		'search_form'         : {
			'button'          : $('#acf-osm-search-button'),
			'field'           : $('#acf-osm-search-keywords'),
			'results'         : $('#acf-osm-map-search-results'),
			'container'       : $('.acf-osm-search'),
		},
		'location_dropdown'   : $('#acf-osm-location-selector'),
		'pin_image_url'       : null,
		'enable_map_clicking' : true,
	};

	// Run som variable filters
	args     = mapify_openstreetmap_args_filter( $, args );
	layer_id = mapify_openstreetmap_layer_id_filter( $, layer_id );
	
	/**
	 * Initialize the map.
	 * This run inside the setTimeout function to make sure the map-container is loaded. 
	 */
	setTimeout(function(){
		var osm = new OpenStreetMap( canvas_id, layers[ layer_id ], args );	

		// Run actions after map initiation
		mapify_actions_after_openstreetmap_iniation( $, osm, layers );

		// Apply map areas if any
		if ( osm_var.map_areas ) {
			mapify_apply_map_areas( $, osm );
		}
	}, 1 );	
});

/**
 * Generate random integer
 * 
 * @param int min 
 * @param int max 
 * @returns int
 */
function mapify_get_random_integer(min, max) {
	return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Filter for OpenStreetMap class arguments
 *  
 * @param object $ 
 * @param object args 
 * @returns object
 */
function mapify_openstreetmap_args_filter( $, args ) {
	// Set the pin image if any
	if ( $( '#mapify-pin-image' ).length ) {
		var pin_url = $( '#mapify-pin-image' ).val();
		
		if ( "" !== $.trim( pin_url ) ) {
			args.pin_image_url = pin_url;
		}
	}

	// Disable map clicking on map selector
	if ( $( '#acf-osm-location-selector' ).length ) {
		args.enable_map_clicking = false;
	}

	return args;
}

/**
 * Filter for layer_id before map initiation
 * 
 * @param object $ 
 * @param string layer_id 
 * @returns string
 */
function mapify_openstreetmap_layer_id_filter( $, layer_id ) {
	// Get the map mode, which is can be `map` or `image`
	if ( osm_var.has_image_mode && 'image' === osm_var.map_mode ) {
		layer_id = 'image';
	} else if ( osm_var.map_layer ) {		
		layer_id = osm_var.map_layer;
	} 

	return layer_id;
}

/**
 * Actions to execute after map initiation
 * 
 * @param object $ 
 * @param object osm 
 */
function mapify_actions_after_openstreetmap_iniation( $, osm, layers ) {
	// Change the map layer on the fly
	$( '#acf-osm-map-mode' ).on( 'change', function() {
		var mode = $( this ).val();
		layer_id = 'image' === mode ? 'image' : 'osm';
		
		if ( osm_var.has_image_mode ) {
			osm.change_map_layer( layers[ layer_id ] );
		}
	} );

	// Disable map dragging on defined map selector
	if ( $( '#acf-osm-location-selector' ).length && '0' !== $( '#acf-osm-location-selector' ).val() ) {
		osm.disable_mouse_dragging();
	}

	// Change AND ALSO SET the map layer between map and image mode, on the fly
	$( '#acf-osm-map-dropdown' ).on( 'change', function() {
		var selected       = $(this).find('option:selected');
		var mode           = selected.data('mode');
		var has_image_mode = selected.data('has-image-mode');
		var map_id         = selected.val();
		
		if ( 'image' === mode && has_image_mode ) {
			var image_layer = new L.TileLayer( osm_var.upload_dir + '/mpfy/' + map_id + '/z{z}-tile_{y}_{x}.png?cache-buster={cacheBuster}', {
				minZoom     : 0,
				maxZoom     : 4,
				noWrap      : true,
				cacheBuster : mapify_get_random_integer(0,1000)
			})

			osm.change_map_layer( image_layer );
		} else {
			var map_mode = $( 'div[data-name=_map_google_mode] .acf-input select' ).find('option:selected');
			osm.change_map_layer( layers[ map_mode ] );
		}
	} );

	// Change AND ALSO SET the map mode layer on the fly
	$( 'div[data-name=_map_google_mode] .acf-input select' ).on( 'change', function() {
		var map_mode     = $(this).find('option:selected').val();
		var primary_mode = $('#acf-osm-map-mode').find('option:selected').val();
		
		if ( 'map' === primary_mode ) {
			osm.change_map_layer( layers[ map_mode ] );
		}
	} );

}

/**
 * Apply map areas
 *
 * @param object $ 
 * @param object osm 
 */
function mapify_apply_map_areas( $, osm ) {
	var map       = osm.get_map();	
	var map_areas = osm_var.map_areas;

	map_areas.forEach(element => {
		// parse coordinates string data to array
		element.coordinates = JSON.parse(element.coordinates);

		// polygon options
		var polygon_options = {
			color       : element.border_color, 
			fillColor   : element.fill_color, 
			fillOpacity : element.fill_opacity, 
		};
		
		// add new updated polygon
		var polygon = L.polygon(element.coordinates, polygon_options).addTo(map);

		// add tooltip to polygon
		if ( '' !== $.trim( element.image_url ) || '' !== $.trim( element.description ) ) {
			var tooltip_html = "<div style='max-width:250px;width:250px;'>";
			
			// image
			if ( '' !== $.trim( element.image_url ) ) {
				tooltip_html += "<img src='" + element.image_url + "' style='width:100%;max-width:100%;'>";
			}

			// description
			if ( '' !== $.trim( element.description ) ) {
				tooltip_html += "<div style='white-space:normal;'>" + element.description + "</div>";
			}

			tooltip_html += "</div>";

			// add tooltip to the map area
			polygon.bindTooltip( tooltip_html, {
				sticky    : true,
				direction : 'top'
			} ).addTo( map );
		}
	} );
}