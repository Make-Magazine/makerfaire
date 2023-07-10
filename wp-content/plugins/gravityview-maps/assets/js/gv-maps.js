/**
 * Part of GravityView_Maps plugin. This script is enqueued from
 * front-end view that has Maps setting enabled.
 *
 * globals jQuery, GV_MAPS, google
 */

// make sure GV_MAPS exists
window.GV_MAPS = window.GV_MAPS || {};

function gm_authFailure() {
	gvMapsDisplayErrorNotice( window.GV_MAPS.google_maps_api_error );
}

/**
 * Inserts an error message before each map
 *
 * @param {string} error_text The API error message to display.
 *
 * @since 1.7
 *
 * @return {void}
 */
function gvMapsDisplayErrorNotice( error_text ) {
	var notice;

	if ( window.GV_MAPS.display_errors ) {
		notice = jQuery( '<div/>', {
			text: error_text,
			'class': 'gv-notice gv-error error'
		} );
	} else {
		notice = jQuery( document.createComment( error_text ) );
	}

	jQuery( '.gv-map-canvas' ).hide().each( function () {
		notice.insertBefore( jQuery( this ) );
	} );
}

( function ( $ ) {

	'use strict';

	/**
	 * Passed by wp_localize_script() with some settings
	 * @type {object}
	 */
	var self = $.extend( {
		'did_scroll': false,
		'map_offset': 0,
		'map_sticky_container': null,
		'map_entries_container_selector': '.gv-map-entries',
		'markers': [],
		'maps': [], // Google Map object, set up in `self.setup_maps`
		'is_single_entry': false,
		'infowindow': {
			'no_empty': true,
			'max_width': 300,
		},
		'mobile_breakpoint': 600, // # of pixels to be considered mobile
	}, window.GV_MAPS );

	/**
	 * Attach all the hooks into the Actions and Filters used by the maps system.
	 *
	 * @since 2.2
	 */
	self.hook = () => {
		self.hooks.addAction( 'gk.maps.after_process_map_markers', 'gravitykit/maps', self.processCluster );
		self.hooks.addAction( 'gk.maps.after_process_map_markers', 'gravitykit/maps', self.processSpider );
	};

	/**
	 * Set up the map functionality
	 *
	 * @since ??
	 */
	self.init = () => {
		const $maps = self.getMaps();
		if ( ! $maps.length ) {
			return;
		}


		//check if it is a single entry view
		self.is_single_entry = $( '.gv-map-single-container' ).length > 0;

		self.hooks.doAction( 'gk.maps.before_maps_init', self );

		// make sure map canvas is less than 50% of the window height (default 400px)
		self.sticky_canvas_prepare();

		self.setup_map_options();
		self.initMaps();

		// mobile behaviour
		self.mobile_init();

		self.start_scroll_check();

		// bind markers animations
		self.markers_animate_init();

		self.hooks.doAction( 'gk.maps.after_maps_init', self );
	};

	/**
	 *
	 */
	self.setup_map_options = () => {

		self.MapOptions.zoom = parseInt( self.MapOptions.zoom, 10 );
		self.MapOptions.mapTypeId = google.maps.MapTypeId[ self.MapOptions.mapTypeId ];

		if ( self.MapOptions.hasOwnProperty( 'zoomControl' ) && true === self.MapOptions.zoomControl && self.MapOptions.zoomControlOptions && self.MapOptions.zoomControlOptions.hasOwnProperty( 'position' ) ) {

			/**
			 * Convert map type setting into google.maps object
			 *
			 * With style and position keys.
			 *
			 * For the position value, see [Google V3 API grid of positions](https://developers.google.com/maps/documentation/javascript/reference#ControlPosition)
			 * Options include: BOTTOM_CENTER, BOTTOM_LEFT, BOTTOM_RIGHT, LEFT_BOTTOM, LEFT_CENTER, LEFT_TOP, RIGHT_BOTTOM, RIGHT_CENTER, RIGHT_TOP, TOP_CENTER, TOP_LEFT, TOP_RIGHT
			 */
			self.MapOptions.zoomControlOptions = {
				'position': google.maps.ControlPosition[ self.MapOptions.zoomControlOptions.position ],
			};
		}
	};

	/**
	 * The storage for the scroll observer, used for loading maps conditionally.
	 *
	 * @since 2.2
	 *
	 * @type {null|IntersectionObserver}
	 */
	self.observer = null;

	self.onMapIntersection = ( entries, opts ) => {
		const $entries = $( entries );
		entries.forEach( entry => {
			const map = entry.target;
			const $map = $( map );
			const data = $map.data( 'gkMap' );

			entry.target.classList.toggle( 'gk-map-visible', entry.isIntersecting );

			if ( entry.isIntersecting ) {
				if ( ! $map.hasClass( 'gk-map-generated' ) ) {
					self.initMap( $entries.index( $map ), map );
				}

				if ( ! $map.hasClass( 'gk-map-markers-generated' ) ) {
					self.processMapMarker( map );
				}
			}
		} );
	};

	/**
	 * Initiate the map object, stored in map
	 *
	 * @since 2.2
	 *
	 * @return {void}
	 */
	self.initMaps = ( $container ) => {
		const $maps = self.getMaps( $container );

		self.hooks.doAction( 'gk.maps.init_maps', $maps, $container );

		$maps.each( ( key, map ) => self.getObserver().observe( map ) );
	};

	/**
	 * Gets the observer stored for this page, if not set it will create a new one.
	 *
	 * @since 2.2
	 *
	 * @returns {IntersectionObserver}
	 */
	self.getObserver = () => {
		if ( ! self.observer ) {
			// define an observer instance
			self.observer = new IntersectionObserver( self.onMapIntersection, {
				root: null,   // default is the viewport
				rootMargin: '-50px',
				threshold: 0.01, // percentage of target's visible area. Triggers "onMapIntersection"
			} );
		}

		return self.observer;
	};

	/**
	 * Initializes a single Map.
	 *
	 * @param index
	 * @param mapEl
	 */
	self.initMap = ( index, mapEl ) => {
		const $map = $( mapEl );
		let data;

		// Prevents loading of all maps.
		if ( ! $map.hasClass( 'gk-map-visible' ) ) {
			return;
		}

		if ( $map.hasClass( 'gk-map-generated' ) ) {
			return;
		}

		data = $map.data( 'gkMap' );

		self.hooks.doAction( 'gk.maps.before_init_map', $map );

		const options = $.extend( {}, self.MapOptions, {
			_index: index,
			_entryId: data.entry_id,
			_element: $map,
			_bounds: new google.maps.LatLngBounds()
		} );

		data.map = new google.maps.Map( mapEl, options );

		// Store the Data for the map on a data prop.
		$map.data( 'gkMap', data );

		if ( 1 === self.layers.bicycling ) {
			const bicyclingLayer = new google.maps.BicyclingLayer();
			bicyclingLayer.setMap( data.map );
		}
		if ( 1 === self.layers.transit ) {
			const transitLayer = new google.maps.TransitLayer();
			transitLayer.setMap( data.map );
		}
		if ( 1 === self.layers.traffic ) {
			const trafficLayer = new google.maps.TrafficLayer();
			trafficLayer.setMap( data.map );
		}

		self.set_zoom_and_center( data.map, $map );

		$map.addClass( 'gk-map-generated' );

		self.hooks.doAction( 'gk.maps.after_init_map', $map );
	};

	/**
	 * Fixes issue where fitBounds() zooms in too far after adding markers
	 *
	 * @see http://stackoverflow.com/a/4065006/480856
	 *
	 * @since 1.3
	 * @since 1.7 added centering
	 *
	 * @param map Google map object
	 */
	self.set_zoom_and_center = ( map, $map ) => {
		google.maps.event.addListenerOnce( map, 'idle', () => {
			if (
				(
					typeof $map !== 'undefined'
					&& $map.hasClass( ' gk-multi-entry-map' )
				)
				&& map.getZoom() > self.MapOptions.zoom
			) {
				map.setZoom( self.MapOptions.zoom );
			}

			if ( 'undefined' !== typeof self.MapOptions.center && self.MapOptions.center.lat && self.MapOptions.center.lng ) {
				map.setCenter( self.MapOptions.center );
			}
		} );
	};

	/**
	 * Process all markers for a given Map.
	 *
	 * It will use the DOM element to determine which map we need to process the markers for.
	 *
	 * @since 2.2
	 *
	 * @param {Element} map
	 */
	self.processMapMarker = ( map ) => {
		const $map = $( map );
		const data = $map.data( 'gkMap' );

		self.hooks.doAction( 'gk.maps.before_process_map_markers', $map, data, self );

		data.markers_data.forEach( ( marker ) => self.addMarker( $map, data, marker ) );

		self.hooks.doAction( 'gk.maps.after_process_map_markers', $map, data, self );

		$map.addClass( 'gk-map-markers-generated' );

		// It's important to have the timout here to avoid race conditions around clustering.
		setTimeout( () => google.maps.event.trigger( data.map, 'idle' ), 50 );
	};


	/**
	 * Sets up the Clustering of a given set of maps.
	 *
	 * Note: This is not native to Google Maps, it uses a Google Supported API but not native.
	 *
	 * @since 2.2
	 *
	 * @link https://developers.google.com/maps/documentation/javascript/marker-clustering
	 *
	 * @param {jQuery} $map
	 * @param {object} data
	 */
	self.processCluster = ( $map, data ) => {
		$map = $( $map );
		if ( ! $map.is( '.gk-multi-entry-map' ) ) {
			return;
		}

		if ( ! data || ! data.markers ) {
			return;
		}

		// Cluster markers if option is set and map contains markers
		if ( ! self.MapOptions.markerClustering || ! data.markers ) {
			return;
		}

		// remove if this particular map already has a cluster.
		if ( data.cluster ) {
			data.cluster.clearMarkers();
		}

		google.maps.event.addListenerOnce( data.map, 'idle', () => {
			data.cluster = new MarkerClusterer( data.map, data.markers, {
				imagePath: self.markerClusterIconPath,
				maxZoom: self.MapOptions.markerClusteringMaxZoom || self.MapOptions.zoom,
			} );

			$map.data( 'gkMap', data );
		} );
	};

	/**
	 * Configures the Spider, which handles when too many markers are on top of each other.
	 *
	 * Note: This uses a third party API, not default to Google API.
	 *
	 * @since 2.2
	 *
	 * @link https://github.com/jawj/OverlappingMarkerSpiderfier
	 *
	 * @param {jQuery} $map
	 * @param {object} data
	 */
	self.processSpider = ( $map, data ) => {
		// It's important to have the timout here to avoid race conditions around clustering.
		setTimeout( () => {
			google.maps.event.addListener( data.map, 'idle', () => {
				// Spiderfy markers
				const oms = new OverlappingMarkerSpiderfier( data.map, {
					markersWontMove: true,
					markersWontHide: true,
					keepSpiderfied: true,
				} );

				data.markers.forEach( marker => oms.addMarker( marker ) );
			} );
		}, 55 );
	};

	/**
	 * Given a Container gets all maps.
	 *
	 * @since 2.2
	 *
	 * @param {jQuery} $container
	 * @param {boolean} filterByGenerated
	 *
	 * @returns {jQuery}
	 */
	self.getMaps = ( $container, filterByGenerated = false ) => {
		if ( ! $container || 0 === $container.length ) {
			$container = $( document );
		}

		let $maps = $container.find( '.gv-map-canvas' );

		if ( filterByGenerated ) {
			$maps = $maps.filter( '.gk-map-generated' );
		}

		return self.hooks.applyFilters( 'gk.maps.get_maps', $maps, filterByGenerated );
	};

	/**
	 * Given a Container gets all Search enabled Maps.
	 *
	 * @since 2.2
	 *
	 * @param {jQuery} $container
	 * @param {boolean} filterByGenerated
	 *
	 * @returns {jQuery}
	 */
	self.getSearchMaps = ( $container, filterByGenerated = false ) => {
		const $maps = self.getMaps( $container, filterByGenerated );

		return self.hooks.applyFilters( 'gk.maps.get_search_maps', $maps.filter( '.gk-multi-entry-map' ), filterByGenerated );
	};

	/**
	 * Adds an individual marker to a particular map, it will use the jQuery/DOM element of the map.
	 * Will also store that given marker into the `gkMap` data object.
	 *
	 * @since 2.2
	 *
	 * @param {jQuery} $map
	 * @param {object} data
	 * @param {object} markerData
	 */
	self.addMarker = ( $map, data, markerData ) => {
		if ( ! data.markers ) {
			data.markers = [];
		}

		let marker;

		if ( data.is_multi_entry_map ) {
			const index = self.markerExists( $map, markerData );

			if ( false !== index && data.markers[ index ] ) {
				marker = data.markers[ index ];
				marker.setMap( data.map );
				marker.set( 'gkVisible', true );
				marker.setVisible( true );
			}
		}

		if ( ! marker ) {
			const geo = new google.maps.LatLng( markerData.lat, markerData.long );
			const icon = {
				url: self.icon,
			};

			if ( markerData.icon ) {

				if ( markerData.icon.url && markerData.icon.url.length ) {
					icon.url = markerData.icon.url;
				}

				if ( markerData.icon.scaledSize && markerData.icon.scaledSize.length === 2 ) {
					icon.size = new google.maps.Size( markerData.icon.scaledSize[ 0 ], markerData.icon.scaledSize[ 1 ] );
				}

				if ( markerData.icon.origin && markerData.icon.origin.length === 2 ) {
					icon.origin = new google.maps.Point( markerData.icon.origin[ 0 ], markerData.icon.origin[ 1 ] );
				}

				if ( markerData.icon.anchor && markerData.icon.anchor.length === 2 ) {
					icon.anchor = new google.maps.Point( markerData.icon.anchor[ 0 ], markerData.icon.anchor[ 1 ] );
				}

				if ( markerData.icon.scaledSize && markerData.icon.scaledSize.length === 2 ) {
					icon.scaledSize = new google.maps.Size( markerData.icon.scaledSize[ 0 ], markerData.icon.scaledSize[ 1 ] );
				}
			}

			/**
			 * Enables the ability to filter the options used to build a marker.
			 *
			 * @since 2.2
			 *
			 * @param {jQuery} $map
			 * @param {object} data
			 * @param {object} markerData
			 *
			 */
			const markerOptions = self.hooks.applyFilters( 'gk.maps.marker_options', {
				map: data.map,
				icon: icon,
				url: markerData.url,
				position: geo,
				gkVisible: true,
				entryId: markerData.entry_id,
				content: markerData.content,
			}, $map, data, markerData );

			marker = new google.maps.Marker( markerOptions );

			data.markers.push( marker );
			self.bindMarkerEvents( marker, data.map );
		}

		$map.data( 'gkMap', data );

		// Extend map bounds using marker position
		data.map._bounds.extend( marker.position );

		if ( ! $map.hasClass( 'gk-multi-entry-map-avoid-rebound' ) ) {
			data.map.fitBounds( data.map._bounds );
		}

		// Add this particular marker to all the search maps in the container.
		if ( ! data.is_multi_entry_map ) {
			self.getSearchMaps( data.$container, true ).each( ( index, searchMap ) => {
				const $searchMap = $( searchMap );
				const searchMapData = $searchMap.data( 'gkMap' );

				if ( ! self.markerExists( $searchMap, marker ) ) {
					searchMapData.markers.concat( marker );
					searchMapData.markers_data.concat( markerData );
				}

				$searchMap.data( 'gkMap', searchMapData );
			} );
		}

		/**
		 * Enables the ability to hook into the add marker event.
		 *
		 * @since 2.2
		 *
		 * @param {jQuery} $map
		 * @param {object} data
		 * @param {window.google.maps.Marker} marker
		 * @param {object} markerData
		 *
		 */
		self.hooks.doAction( 'gk.maps.after_add_marker', $map, data, marker, markerData );
	};

	/**
	 * Given a Map jQuery object test to see if we have a particular marker already set, uses the entryId for check.
	 *
	 * @since 2.2
	 *
	 * @param {jQuery} $map
	 * @param {object} checkMarker
	 *
	 * @returns {boolean|int}
	 */
	self.markerExists = ( $map, checkMarker ) => {
		const data = $map.data( 'gkMap' );
		let exists = false;

		data.markers.forEach( ( marker, index ) => {
			if ( checkMarker.entryId && checkMarker.entryId == marker.entryId ) {
				exists = index;
			}
			if ( checkMarker.entry_id && checkMarker.entry_id == marker.entryId ) {
				exists = index;
			}
		} );

		return exists;
	};

	/**
	 * Given a Map jQuery object test to see if we have a particular marker data already set, uses the entryId for check.
	 *
	 * Marker data is used before the marker is configured with Google Maps API.
	 *
	 * @since 2.2
	 *
	 * @param {jQuery} $map
	 * @param {object} checkMarker
	 *
	 * @returns {boolean|int}
	 */
	self.markerDataExists = ( $map, checkMarker ) => {
		const data = $map.data( 'gkMap' );
		let exists = false;

		data.markers_data.forEach( ( marker, index ) => {
			if ( checkMarker.entryId && checkMarker.entryId == marker.entry_id ) {
				exists = index;
			}
			if ( checkMarker.entry_id && checkMarker.entry_id == marker.entry_id ) {
				exists = index;
			}
		} );

		return exists;
	};

	/**
	 * Add event listeners to Markers.
	 *
	 * @since 2.2
	 *
	 * @param {object} marker google.maps.Marker
	 * @param {object} map google.maps.Map
	 */
	self.bindMarkerEvents = ( marker, map ) => {
		if ( self.is_single_entry ) {
			return;
		}

		// The marker has been clicked.
		google.maps.event.addListener( marker, 'spider_click', () => self.onMarkerClick( marker ) );

		// on Mouse over
		google.maps.event.addListener( marker, 'mouseover', self.onMarkerMouseOver( marker ) );

		// on mouseout
		google.maps.event.addListener( marker, 'mouseout', self.onMarkerMouseOut( marker ) );

		// Close infowindow when clicking the map
		google.maps.event.addListener( map, 'click', () => infowindow.close() );
	};

	/**
	 * Open infowindow or go to entry link when marker has been clicked
	 *
	 * @since 2.2
	 *
	 * @param {object} marker google.maps.Marker Google maps marker object
	 * @param {string} marker.content Infowindow markup string
	 * @param {object} marker.map A google.maps.Map object
	 * @param {string} marker.url Full URL to the marker's single entry page
	 * @param {object} marker.position A google.maps.LatLng object
	 * @param {int|string} marker.entryId Entry ID # or slug
	 */
	self.onMarkerClick = ( marker ) => {
		var content = self.infowindow_get_content( marker.content );

		// Open infowindow if content is set
		if ( content ) {
			infowindow.setContent( content );
			infowindow.open( marker.map, marker );

			return;
		}

		// Go to entry link
		infowindow.close();
		window.open( marker.url, self.marker_link_target );
	};

	/**
	 * Check if the infowindow content is empty and if so add a link to the single entry (by default)
	 *
	 * @param {string} content Infowindow markup string
	 *
	 * @returns {string} Prepared Infowindow HTML, with empty image tags removed and default text added to empty links
	 */
	self.infowindow_get_content = ( content ) => {

		/**
		 * Do we accept empty infowindows?
		 * @see \GravityKit\GravityMaps\Render_Map::parse_map_options
		 */
		if ( ! self.infowindow.no_empty ) {
			return content;
		}

		var $content = $( content );

		$content
			.find( 'img[src=""]' ).remove() // Remove empty images
			.end()
			.addClass( function () {
				if ( 0 === $content.find( 'img' ).length ) {
					return 'gv-infowindow-no-image';
				}
			} )
			.find( 'a.gv-infowindow-entry-link:not([allow-empty]):empty' ).text( self.infowindow.empty_text ); // Empty links get some text, unless "allow-empty" attribute is set

		return $content.prop( 'outerHTML' );
	};

	/**
	 * Highlights the assigned entry on mouse over a Marker
	 *
	 * @since 2.2
	 *
	 * @param marker google.maps.Marker Google maps marker object
	 *
	 * @returns {Function}
	 */
	self.onMarkerMouseOver = ( marker ) => () => $( '#gv_map_' + marker.entryId ).addClass( 'gv-highlight-entry' );

	/**
	 * Remove the highlight of the assigned entry on mouse out a Marker
	 *
	 * @since 2.2
	 *
	 * @param marker google.maps.Marker Google maps marker object
	 *
	 * @returns {Function}
	 */
	self.onMarkerMouseOut = ( marker ) => () => $( '#gv_map_' + marker.entryId ).removeClass( 'gv-highlight-entry' );

	// Animate markers when mouse is over an entry

	/**
	 *  Bind events when mouse is over an entry
	 */
	self.markers_animate_init = () => {
		if ( self.is_single_entry || '' === self.icon_bounce ) {
			return;
		}
		$( '.gv-map-view' ).on( 'mouseenter', self.marker_animate );
	};

	/**
	 * Starts and Stops the marker animation
	 *
	 * @param event object Event
	 */
	self.marker_animate = function ( event ) { // Dont convert to a => function just uet.
		var id = this.id.replace( 'gv_map_', '' );

		self.markers.forEach( self.marker_animation_start, id );
	};

	/**
	 * Starts Bounce marker animation for the marker associated with the Entry
	 *
	 * @param marker google.maps.Marker Google maps marker object
	 * @param i
	 * @param array
	 */
	self.marker_animation_start = ( marker, i, array ) => {
		if ( marker.entryId === this ) {

			// Don't interrupt something beautiful
			if ( marker.animating ) {
				return;
			}

			marker.setAnimation( google.maps.Animation.BOUNCE );

			// stop the animation after one bounce
			setTimeout( self.marker_animation_stop, 750, marker );
		}
	};

	/**
	 * Stops all the marker animations
	 *
	 * @param marker google.maps.Marker Google maps marker object
	 * @param i
	 */
	self.marker_animation_stop = ( marker, i ) => {
		marker.setAnimation( null );
	};

	// sticky maps functions
	/**
	 * Set properties for sticky map and make sure Map Canvas height is less than 50% of window height viewport
	 * Default Canvas height = 400 px (@see assets/css/gv-maps.css )
	 */
	self.sticky_canvas_prepare = () => {
		// set map container (just for sticky purposes)
		self.map_sticky_container = $( '.gv-map-sticky-container' );

		const windowHeight = $( window ).height();
		const doubleCanvasHeight = self.map_sticky_container.height() * 2;

		// if viewport height is less than 2x 400 px
		if ( windowHeight < doubleCanvasHeight ) {
			$( '.gv-map-canvas' ).height( windowHeight / 2 );
		}

	};

	self.window_scroll_init_offset = () => {
		self.map_offset = self.map_sticky_container.offset().top;
	};

	self.scroll_set = () => {
		self.did_scroll = true;
	};

	self.start_scroll_check = () => {
		if ( self.map_sticky_container.length > 0 ) {
			$( window ).one( 'scroll', self.window_scroll_init_offset );
			setInterval( self.window_on_scroll, 250 );
		}
	};

	self.window_on_scroll = () => {
		if ( self.did_scroll ) {
			self.did_scroll = false;
			var scroll = $( window ).scrollTop();
			var canvasObj = self.map_sticky_container.find( '.' + self.map_id_prefix );
			var listObj = $( self.map_entries_container_selector );
			var canvasWidth = canvasObj.width(),
				canvasHeight = canvasObj.height();
			if ( scroll >= self.map_offset ) {
				canvasObj.width( canvasWidth );
				self.map_sticky_container.addClass( 'gv-sticky' );
				if ( self.template_layout === 'top' ) {
					listObj.css( 'margin-top', canvasHeight + 'px' );
				}

			} else {
				canvasObj.width( '100%' );
				self.map_sticky_container.removeClass( 'gv-sticky' );
				if ( self.template_layout === 'top' ) {
					listObj.css( 'margin-top', '' );
				}
			}

		}
	};

	// Mobile

	/**
	 * Check if the page is being loaded in a tablet/mobile environment,
	 *  and if yes, run special functions
	 * $mobile-portrait: 320px;
	 * $mobile-landscape: 480px;
	 * $small-tablet: 600px;
	 */
	self.mobile_init = () => {
		// only apply this logic for the map template containing the sticky map (even if it is not pinned)
		if ( self.map_sticky_container.length <= 0 ) {
			return;
		}

		if ( $( window ).width() <= parseInt( self.mobile_breakpoint, 10 ) ) {
			self.mobile_map_to_top();
		}
	};

	/**
	 * Move the sticky map to the top, when aligned to the right.
	 */
	self.mobile_map_to_top = () => {
		var parent = self.map_sticky_container.parent(),
			grandpa = $( '.gv-map-container' );

		if ( parent.hasClass( 'gv-grid-col-1-3' ) && 1 === parent.index() ) {
			parent.detach().prependTo( grandpa );
		}

	};

	// Initialize maps
	if ( ! self.api_key ) {
		gvMapsDisplayErrorNotice( self.google_maps_api_key_not_found );
	} else if ( 'undefined' === typeof window.google ) {
		gvMapsDisplayErrorNotice( self.google_maps_script_not_loaded );
	} else if ( ! self.can_use_rest ) {
		gvMapsDisplayErrorNotice( self.cannot_use_rest_error );
	} else if ( self.address_field_missing ) {
		gvMapsDisplayErrorNotice( self.address_field_missing );
	} else if ( self.hide_until_searched !== '1' && ! self.markers_info.length && ! window.GV_MAPS.is_search ) {
		gvMapsDisplayErrorNotice( self.entries_missing_coordinates );
	} else {

		var infowindow = new google.maps.InfoWindow( {
			content: '',
			maxWidth: parseInt( self.infowindow.max_width, 10 ),
		} );
	}

	/**
	 * @inheritDoc
	 */
	self.hooks = window.GravityKit.GravityMaps.hooks;

	// Update global variable reference
	window.GV_MAPS = self;

	/**
	 * We need to make sure the callbacks are registered to their specific hooks as early as we load the file.
	 */
	self.hook();

	// We will possibly need to use `window.GV_MAPS.init` as the callback from Google Maps API.
	$( document ).ready( self.init );

	// Window scroll
	$( window ).scroll( self.scroll_set );

}( jQuery ) );
