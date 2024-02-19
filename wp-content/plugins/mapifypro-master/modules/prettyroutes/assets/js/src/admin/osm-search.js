/**
 * Class to handle the Open Street Map location search.
 */
export default class OSMSearch {
	/**
	 * The map leaflet map object
	 * 
	 * @var object
	 */
	map;

	/**
	 * Search form elements
	 * 
	 * @var object search_form
	 * @var object search_form.button
	 * @var object search_form.field
	 * @var object search_form.results
	 * @var object search_form.container
	 */
	search_form;

	/**
	 * The search results
	 * 
	 * @var array
	 */
	search_results;

	/**
	 * The constructor
	 * 
	 * @param object layer
	 * @param object args
	 * 
	 * @return void
	 */
	constructor ( map, search_form, location_click_callback = null ) {
		this.map = map;
		this.search_form = search_form;
		this.loading_label = 'Loading...';
		this.location_click_callback = location_click_callback;

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

		// set the view
		this.map.setView( location, zoom_level );

		// empty the search result
		this.search_form.results.empty();

		// action click callback.
		if (this.location_click_callback) {
			this.location_click_callback({
				lat: Number(location[0]),
				lng: Number(location[1]),
			});
		}
	}

	/**
	 * Set loading on seach keywords
	 *
	 * @param void
	 * 
	 * @return void	
	 */
	set_loading() {
		this.search_form.field.attr('disabled', 'disabled');
		this.search_form.button.attr('disabled', 'disabled');
		this.search_form.results.empty();
		jQuery('<p>', { html: this.loading_label }).appendTo(this.search_form.results);
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
}