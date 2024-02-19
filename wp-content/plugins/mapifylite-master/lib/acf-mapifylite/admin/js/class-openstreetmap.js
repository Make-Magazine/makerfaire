/**
 * References:
 * https://leafletjs.com/
 * https://github.com/derickr/osm-tools/tree/master/leaflet-nominatim-example
 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Classes
 */

class OpenStreetMap {
	/**
	 * The map leaflet map object
	 * 
	 * @var object
	 */
	map;

	/**
	 * The map leaflet map layer object
	 * 
	 * @var object
	 */
	layer;

	/**
	 * The map markers
	 * 
	 * @var object
	 */
	marker;

	/**
	 * The map selected location
	 * 
	 * @var array
	 */
	selected_location = [-1.634541720929286, 119.9030508161835];

	/**
	 * The map centered location
	 * Inilially will be the same as selected_location
	 * 
	 * @var array
	 */
	centered_location = this.selected_location;

	/**
	 * The map zoom level
	 * 
	 * @var int
	 */
	zoom_level = 4;

	/**
	 * Mouse handler status
	 * 
	 * @var bool
	 */
	enable_map_clicking = true;

	/**
	 * Map info fields object
	 * 
	 * @var object
	 */
	map_info_fields;
	
	/**
	 * Search form elements
	 * 
	 * @var object
	 */
	search_form;

	/**
	 * The search results
	 * 
	 * @var array
	 */
	search_results;
	
	/**
	 * The location selector dropdown
	 * 
	 * @var object
	 */
	location_dropdown;
	
	/**
	 * The custom pin image url
	 * 
	 * @var mixed string|null
	 */
	pin_image_url;

	/**
	 * The constructor
	 * 
	 * @param mixed  el    As leafletjs required element, this variable can be either:
	 *                     - string html id, e.g 'map-canvas'
	 *                     - HTMLElement, e.g jQuery( '#map-canvas' )[0]
	 * @param object layer
	 * @param object args
	 * 
	 * @return void
	 */
	constructor ( el, layer, args ) {
		this.map                 = new L.Map(el, { zoomControl: true });
		this.layer               = layer;
		this.map_info_fields     = args.map_info_fields;
		this.search_form         = args.search_form;
		this.location_dropdown   = args.location_dropdown;
		this.pin_image_url       = args.pin_image_url;
		this.enable_map_clicking = args.enable_map_clicking;

		// set initial selected_location
		if ( 'undefined' !== typeof args.selected_location ) {
			this.selected_location = args.selected_location;
		}

		// set initial centered_location
		if ( 'undefined' !== typeof args.centered_location ) {
			this.centered_location = args.centered_location;
		}

		// set initial zoom_level
		if ( 'undefined' !== typeof args.zoom_level ) {
			this.zoom_level = args.zoom_level;
		}

		// Inial map load
		this.map.setView( this.centered_location, this.zoom_level ).addLayer( this.layer );

		// Set initial marker
		this.set_marker( this.selected_location );

		// Set map actions
		this.set_map_actions();
	}

	/**
	 * All of the map actions functions lies here
	 * Except for the `on marker dragend`, which is lies on function set_marker
	 *  
	 * @param void
	 * 
	 * @return void	
	 */
	set_map_actions() {
		var this_class = this;
				
		// On click map		
		this.map.on( 'click', function(e) {	
			if ( this_class.enable_map_clicking ) {
				var location = [e.latlng.lat, e.latlng.lng];
				this_class.set_marker( location );
			}
		});

		// On zoom map
		this.map.on( 'zoomend', function(e) {
			this_class.zoom_level = e.target._zoom;

			// update map info
			this_class.update_map_info();
		});

		// On move/drag the map
		this.map.on( 'dragend', function(e) {
			var centered = this_class.map.getCenter();
			var location = [centered.lat, centered.lng];
			
			this_class.set_centered_location(location);
		});

		// on click search button
		this.search_form.button.on('click', function(e) {
			e.preventDefault();
			this_class.keywords_search();
		});

		// on enter search input
		this.search_form.field.keypress(function(e) {			
			var key = e.which;
			if ( 13 === key ) {
				e.preventDefault();
				this_class.keywords_search();
				return false
			}
		}); 

		// on click address selection
		this.search_form.results.on('click', 'ul li a', function() {
			var key = jQuery(this).attr('key');
			this_class.choose_address_result(key);
		});
		
		// On click dropdown location selector
		this.location_dropdown.on('change', function() {
			var lat = jQuery(this).find(':selected').data('lat');
			var lng = jQuery(this).find(':selected').data('lng');

			if ( 'undefined' === typeof lat || 'undefined' === typeof lng ) {
				this_class.enable_mouse_dragging();
				this_class.search_form_visibility( true );
			} else {
				// set selected marker location
				var location   = [lat, lng];
				var zoom_level = this.zoom_level;
		
				this_class.set_view_and_marker( location, zoom_level );
				this_class.disable_mouse_dragging();
				this_class.search_form_visibility( false );
			}		
		});

	}

	/**
	 * Set the map layer on the fly
	 * 
	 * @param object new_layer
	 * 
	 * @return void
	 */
	change_map_layer( new_layer ) {
		// replace the layer
		this.map.removeLayer( this.layer );
		this.map.addLayer( new_layer );

		// set the current layer
		this.layer = new_layer;
	}

	/**
	 * Clean the map from marker
     *
	 * @param void
	 * 
	 * @return void	
	 */
	clean_marker() {
		if ( this.marker ) {
			this.map.removeLayer( this.marker );
		}
	}

	/**
	 * Set marker on the map
	 * Also handle on marker dragend
     *
	 * @param array location
	 * 
	 * @return object marker	
	 */
	set_marker( location ) {
		var this_class      = this;
		var marker_settings = { draggable: true };

		// clean existing marker
		this.clean_marker();

		// set selected location
		this.set_selected_location( location );

		// if has custom pin url
		if ( this.pin_image_url ) {
			var pin_icon    = L.icon({iconUrl: this.pin_image_url});
			marker_settings = { draggable: true, icon: pin_icon };
		}

		// set marker		
		this.marker = L.marker(this.selected_location, marker_settings).addTo(this.map);
		
		// on marker dragend
		this.marker.on( 'dragend', function(e) {	
			var lat      = e.target._latlng.lat;
			var lng      = e.target._latlng.lng;
			var location = [lat, lng];

			this_class.set_selected_location( location );
		});
	}

	/**
	 * Set view and the marker
     *
	 * @param array location
	 * 
	 * @return object marker	
	 */
	set_view_and_marker( location, zoom_level ) {
		// set marker
		this.set_marker( location );

		// set the view
		this.map.setView( location, zoom_level );
	
		// set centered location
		this.set_centered_location( location );
	}

	/**
	 * Set selected location then update the map info
     *
	 * @param array location
	 * 
	 * @return void	
	 */
	set_selected_location( location ) {
		// set selected location
		this.selected_location = location;

		// update map info
		this.update_map_info();
	}

	/**
	 * Set centered location then update the map info
     *
	 * @param array location
	 * 
	 * @return void	
	 */
	set_centered_location( location ) {
		// set selected location
		this.centered_location = location;

		// update map info
		this.update_map_info();
	}

	/**
	 * Update map info on the pre-defined text fields
     *
	 * @param void
	 * 
	 * @return void	
	 */
	update_map_info() {
		this.map_info_fields['selected_lat'].val( this.selected_location[0] );
		this.map_info_fields['selected_lng'].val( this.selected_location[1] );
		this.map_info_fields['centered_lat'].val( this.centered_location[0] );
		this.map_info_fields['centered_lng'].val( this.centered_location[1] );
		this.map_info_fields['zoom_level'].val( this.zoom_level );		
	}

	/**
	 * Search keyword on the map with nominatim
	 *
	 * @param void
	 * 
	 * @return void	
	 */
	keywords_search() {
		var keywords   = this.search_form.field.val();
		var this_class = this;

		this.set_loading();

		jQuery.getJSON('https://nominatim.openstreetmap.org/search?format=json&limit=5&q=' + keywords, function (data) {
			var items                = [];
			var search_results_label = acf._e('mapify_map_locator', 'search-results-label');
			var no_results_label     = acf._e('mapify_map_locator', 'no-results-label');
			
			// save to search_results
			this_class.search_results = data;

			jQuery.each(data, function (key, val) {
				items.push('<li><a href="javascript:;" key="' + key + '">' + val.display_name + '</a></li>');
			});

			this_class.search_form.results.empty();

			if (items.length != 0) {
				jQuery('<p>', { html: search_results_label + ":" }).appendTo(this_class.search_form.results);
				jQuery('<ul/>', {
					'class': 'my-new-list',
					html: items.join('')
				}).appendTo(this_class.search_form.results);
			} else {
				jQuery('<p>', { html: no_results_label }).appendTo(this_class.search_form.results);
			}

			this_class.unset_loading();
		});
	}

	/**
	 * On choose search result
	 *
	 * @param integer key
	 * 
	 * @return void	
	 */	
	choose_address_result(key) {
		var result     = this.search_results[key];
		var location   = [result.lat, result.lon];
		var zoom_level = this.zoom_level;

		this.set_view_and_marker( location, zoom_level );
	}

	/**
	 * Set loading on seach keywords
	 *
	 * @param void
	 * 
	 * @return void	
	 */
	set_loading() {
		var loading_label = acf._e('openstreetmap_locator', 'loading-label');

		this.search_form.field.attr('disabled', 'disabled');
		this.search_form.button.attr('disabled', 'disabled');
		this.search_form.results.empty();
		jQuery('<p>', { html: loading_label }).appendTo(this.search_form.results);
	}

	/**
	 * Unset search loading
	 *
	 * @param void
	 * 
	 * @return void	
	 */
	unset_loading() {
		this.search_form.field.removeAttr('disabled');
		this.search_form.button.removeAttr('disabled');
	}

	/**
	 * Handle the visibility of the search form
	 * 
	 * @param bool is_visible 
	 * 
	 * @return void	
	 */
	search_form_visibility( is_visible ) {
		if ( is_visible ) {
			this.search_form.container.css( 'cssText', 'display: block !important' );
		} else {
			this.search_form.container.hide();
			this.search_form.results.empty();
		}
	}

	/**
	 * Disable the map's mouse handler (except the zoom control)
	 * The control now is depends on the location selector dropdown
	 * 
	 * @param void
	 * 
	 * @return void	
	 */
	disable_mouse_dragging() {
		this.map.dragging.disable();
		this.marker.dragging.disable();
	}

	/**
	 * Enable the map's mouse handler
	 * 
	 * @param void
	 * 
	 * @return void	
	 */
	enable_mouse_dragging() {
		this.map.dragging.enable();
		this.marker.dragging.enable();
	}
	
}