/**
 * Initialize the map on page loaded
 */
jQuery(document).ready(function($){
	// Available map layers
	var layers = {
		'osm': new L.TileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			minZoom     : 0,
			maxZoom     : 18,
			attribution : 'Map data &copy; 2012 <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
		}),
		'maptiler': new L.TileLayer( 'https://api.maptiler.com/maps/toner/{z}/{x}/{y}.png?key=WCsP7XxYPRXFpRJMaMe4', {
			minZoom     : 0,
			maxZoom     : 18,
			attribution : 'Map data &copy; 2012 <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
		}),
		'image': new L.TileLayer( osm_var.upload_dir + '/mpfy/' + osm_var.post_id + '/z{z}-tile_{y}_{x}.png?cache-buster={cacheBuster}', {
			minZoom     : 0,
			maxZoom     : 4,
			noWrap      : true,
			cacheBuster : mapify_get_random_integer(0,1000)
		})
	};

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

	// Initialize the map
	var osm = new OpenStreetMap( canvas_id, layers[ layer_id ], args );	

	// Run actions after map initiation
	mapify_actions_after_openstreetmap_iniation( $, osm, layers );
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

	// Change AND ALSO SET the map layer on the fly
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
			osm.change_map_layer( layers[ 'osm' ] );
		}
	} );
}