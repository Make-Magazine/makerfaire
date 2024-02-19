'use strict';

import { OpenStreetMapProvider } from 'leaflet-geosearch';
import 'leaflet-control-geocoder';
import OSMSearch from './osm-search';
const $ = jQuery;
const $document = $( document );

$document.ready(function(){

	const LeafletMap = function( element, fieldObject ) {
		element.data( 'exposed_field_object', fieldObject );
		var field = element.find( '.acf-map-field' ),
			mapContainer = element.find( '.acf-map-canvas' ),
			exists = 0,
			marker = false,
			zoom = field.data( 'zoom' ),
			coords = field.val(),
			temp,
			lat,
			lng;

		if ( coords !== '' || coords.split( ',' ).length == 2 ) {
			temp = coords.split( ',' );
			lat = parseFloat( temp[0] );
			lng = parseFloat( temp[1] );

			exists = temp[0] !== '0' && temp[1] !== '0';
		}

		if ( ! exists || isNaN( lat ) || isNaN( lng ) ) {
			lat = field.data( 'default-lat' );
			lng = field.data( 'default-lng' );
		}

		var layers = {
			'road': [
				L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					attribution: 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
				} ),
			],
		};

		var attributionControl = L.control.attribution( {
			prefix: false
		} );

		var map = L.map( mapContainer.get( 0 ), {
			'attributionControl': false,
			'zoomControl': true,
			'doubleClickZoom': false,
			'scrollWheelZoom': true,
		} );
		map.setView( [lat, lng], zoom );
		map.crb = {'markers': []};
		fieldObject.map = map;

		var currentLayerType = 'road';
		fieldObject.setMapType = function( type, newLayers ) {
			var layerGroup = layers[ currentLayerType ];
			for (var i = 0; i < layerGroup.length; i++) {
				map.removeLayer( layerGroup[ i ] );
			}

			currentLayerType = type;
			if ( newLayers ) {
				layers[ currentLayerType ] = newLayers;
			}

			for (var i = 0; i < layers[ currentLayerType ].length; i++) {
				map.addLayer( layers[ currentLayerType ][ i ] );
			}

			if ( ['road', 'image'].indexOf( type ) === -1 ) {
				map.addControl( attributionControl );
			} else {
				map.removeControl( attributionControl );
			}
		};
		fieldObject.setMapType( currentLayerType );

		fieldObject.update_marker_position = function( point ) {
			var latLng = point.latLng || point;
			if ( marker ) {
				marker.setLatLng( latLng );
				map.panTo( latLng, {
					reset: true,
					animate: false,
					noMoveStart: true
				} );
			} else {
				marker = L.marker( latLng, {
					draggable: true
				});
				map.addLayer( marker );
				map.crb.markers.push( marker );

				marker.on( 'dragend', update_value );
			}
			update_value();
		}

		// if we had coords in input field, put a marker on that spot
		if ( exists == 1 ) {
			fieldObject.update_marker_position( [lat, lng] );
		}

		// on click move marker and set new position
		map.on( 'dblclick', function( e ) {
			fieldObject.update_marker_position( e.latlng );
		} );
		map.on( 'zoomend', update_value );

		function update_value() {
			field.val( marker.getLatLng().lat + ',' + marker.getLatLng().lng + ',' + map.getZoom() );

			// update on individual fields
			element.find( '#centered_lat' ).val( marker.getLatLng().lat );
			element.find( '#centered_lng' ).val( marker.getLatLng().lng );
			element.find( '#zoom_level' ).val( map.getZoom() );
		}

		fieldObject.enableImageMode = function( imageSourcePattern ) {
			fieldObject.setMapType( 'image', [ L.tileLayer( imageSourcePattern, {
				minZoom: 0,
				maxZoom: 4,
				noWrap: true
			} ) ] );
		};
	};

	const Pretty_Routes_Map = function( element, fieldObject ) {
		element.data( 'exposed_field_object', fieldObject );
		var search_field = element.find( '.acf-map-location-search' );
		var geocoder = new OpenStreetMapProvider();

		// Initialize the base map field
		LeafletMap( element, fieldObject );

		// Decorate the base field with a geo coder
		element.find( '.acf-map-search-btn' ).on( 'click', geocode_address );

		// Disable the form submission with enter key; instead, initiate address geocoding
		search_field.on('keypress', function (e) {
			var enter_keycode = 13;
			if ( e.keyCode !== enter_keycode ) {
				return true;
			}

			search_field.attr( 'disabled', true );
			geocode_address( search_field.val() )
				.then( (results) => {
					if (results.length > 0) {
						fieldObject.update_marker_position( results[0] );
					} else {
						alert( crbl10n.geocode_not_successful + 'No results found' );
					}
					search_field.attr( 'disabled', false );
				} );
			return false;
		});

		function geocode_address( query ) {
			return geocoder.search( { query: query } )
				.then( ( results ) => {
					var filteredResults = [];
					for (var i = 0; i < results.length; i++) {
						var result = results[i]
						filteredResults.push( [result.y, result.x] );
					}
					return Promise.resolve( filteredResults );
				} );
		};
	};

	const Pretty_Routes_Map_With_Route = function( element, fieldObject ) {
		element.data( 'exposed_field_object', fieldObject );
		const field = element.find( '.acf-map-field' );
		const $mapContainer = element.find( '.acf-map-canvas' );
		const $typeInput = $('.acf-field[data-name="_route_type"] .acf-input').children('select');
		const $colorInput = $('.acf-field[data-name="_route_color"] .acf-color-picker').children('input');
		const $complex = $('.acf-field[data-name="acf_prettyroutes_waypoints"]:first');
		const $complexAddButton = $complex.find('.acf-button[data-event="add-row"]:first');
		const $complexDescription = $complex.find('.acf-input').children('.description');
		const complexRowsSelector = '.acf-repeater .acf-table .acf-row:not(.acf-clone)';		
		const zoom = field.data( 'zoom' );
		const coords = field.val();
		let lat;
		let lng;
		const router = L.Routing.mapbox( 'pk.eyJ1IjoianNlYXJzMzEiLCJhIjoiY2o3bG5obHZqMmdvcDJxcW15bzFpdTB5NSJ9.lfom0YaF2Siy0-1T0y-EJw' );
		const geocoder = L.Control.Geocoder.nominatim();
		const search_form = {
			'button'    : $('#acf-osm-search-button'),
			'field'     : $('#acf-osm-search-keywords'),
			'results'   : $('#acf-osm-map-search-results'),
			'container' : $('.acf-osm-search'),
		};

		if ( coords !== '' || coords.split( ',' ).length == 2 ) {
			let temp = coords.split( ',' );
			lat = parseFloat( temp[0] );
			lng = parseFloat( temp[1] );
		}

		if ( ! lat || ! lng || isNaN( lat ) || isNaN( lng ) ) {
			lat = field.data( 'default-lat' );
			lng = field.data( 'default-lng' );
		}

		let waypoints = [];

		let value = field.val();
		if ( /\d/.test( value ) ) {
			let values = value.split( '|' );
			for ( let i = 0; i < values.length; i++ ) {
				let temp = values[ i ].split( ',' );
				waypoints.push( L.latLng( temp[1], temp[2]) );
			}
		}

		let plan = L.Routing.plan(waypoints, {
			createMarker: function(i, wp) {
				return L.marker(wp.latLng, {
					draggable: true,
				});
			},
			geocoder: geocoder,
			routeWhileDragging: true,
		});

		let routingControl = null;
		let offroadLine = null;

		const maxWaypoints = 25;
		const nonceField = document.getElementById('prettyroutes_acf_admin_nonce');		
		const bottomBarCounter = $('.prettyroutes-bottom-bar-counter');
		const createConnectedRouteButton = $('.prettyroutes-create-connected-route-button');

		const updateWaypoints = function() {
			let waypointsOnly = waypoints.slice(0);
			waypointsOnly.shift();
			waypointsOnly.pop();

			const addRow = function() {
				const $rows = $complex.find( complexRowsSelector );

				if ( $rows.length >= waypointsOnly.length ) {
					removeRow();
					return;
				} else {
					// Add necessary waypoint field
					$complexAddButton.trigger('click');
					setTimeout(addRow, 100);
				}
			};

			const removeRow = function() {
				const $rows = $complex.find( complexRowsSelector );

				if ( $rows.length <= waypointsOnly.length ) {
					return;
				} else {
					// Remove unnecessary waypoint field
					$complex.find( complexRowsSelector ).last().remove();
					setTimeout(removeRow, 100);
				}				
			};

			const toggleDescription = function() {
				if ( waypointsOnly.length ) {
					$complexDescription.hide();
				} else {
					$complexDescription.show();
				}
			}

			addRow();
			toggleDescription();			
			updateWaypointsCounter();
		}

		const addWaypoint = (latlng) => {
			const routingWaypoints = getWaypoints();

			console.log('latlng', latlng);
			// Add the new wayponts
			routingWaypoints.push( L.Routing.waypoint( latlng ) );
			plan.setWaypoints( routingWaypoints );
		}

		const updateValue = function() {
			var values = [];
			for (var i = 0; i < waypoints.length; i++) {
				let waypoint = waypoints[i];
				let type = `waypoint-${i - 1}`;
				if (i === waypoints.length - 1) {
					type = 'destination';
				}
				if (i === 0) {
					type = 'origin';
				}
				let value = `${type},${waypoint.lat},${waypoint.lng},${map.getZoom()}`
				values.push( value );
			}
			field.val(values.join('|'));
			field.trigger('change'); // to include on ACF fields to save
		}

		const refreshOffroad = function( type, color ) {
			if ( offroadLine ) {
				offroadLine.remove();
			}

			if ( type === 'offroad' ) {
				offroadLine = L.polyline( waypoints, {
					color: color,
				} );
				offroadLine.addTo( map );
			}
		}

		const refreshRoutingControl = function() {
			const type = $typeInput.val();
			const color = $colorInput.val() ? $colorInput.val() : '#00AAFF';
			let roadColor = color;

			if (type === 'offroad') {
				roadColor = 'transparent';
			}

			const routingControlOptions = {
				router: router,
				plan: plan,
				routeWhileDragging: true,
				lineOptions: {
					addWaypoints: false,
					styles: [{
						color: roadColor,
					}],
				}
			};

			// destroy old control
			if ( routingControl ) {
				routingControl.off( 'waypointschanged' );
				routingControl.remove();
			}

			// add new control
			routingControl = L.Routing.control( routingControlOptions );
			routingControl.addTo( map );
			routingControl.on( 'waypointschanged', function( event ) {
				waypoints = event.waypoints.map( waypoint => waypoint.latLng ).filter( latLng => !!latLng );
				map.crb.waypoints = waypoints;
				updateValue();
				refreshOffroad( type, color );
			} );

			routingControl.on( 'waypointschanged', updateWaypoints );
			routingControl.on( 'waypointsspliced', updateWaypoints );
			routingControl.on( 'waypointgeocoded', updateWaypoints );
			refreshOffroad( type, color );
			updateWaypointsCounter();
			return routingControl;
		}

		const updateMapInfo = function( centered_lat, centered_lng, zoom_level ) {
			element.find( '#centered_lat' ).val( centered_lat );
			element.find( '#centered_lng' ).val( centered_lng );
			element.find( '#zoom_level' ).val( zoom_level );
		}

		const getWaypoints = () => {
			const routingWaypoints = plan.getWaypoints();

			// Remove any null waypoints
			return routingWaypoints.filter((waypoint) => {
				return !!waypoint?.latLng;
			});
		}

		createConnectedRouteButton.on('click', function() {
			var data = {
				action: 'create_connected_route',
				wpnonce: nonceField.value,
				id: document.getElementById('post_ID').value,
			}

			jQuery.post(wp_ajax_object.ajax_url, data, function(response){
				// Do save the map. The new route should be created after it and we will redirected into it.
				$('input[type=submit]#publish').trigger('click');
			});
		});

		const updateWaypointsCounter = () => {			
			const routingWaypoints = getWaypoints();
			const type = $typeInput.val();
			const percentWaypoints = (routingWaypoints.length / maxWaypoints) * 100;
			const geocodersSidebar = $( ".leaflet-routing-geocoders" ).get(0);

			// Set the bar length.
			if (percentWaypoints <= 100) {
				bottomBarCounter.css('width', `${percentWaypoints}%`);
			} else {
				bottomBarCounter.css('width', `100%`);
			}

			// Set the bar color.
			if(routingWaypoints.length <= maxWaypoints) {
				bottomBarCounter.removeClass('overloaded');
			} else {
				bottomBarCounter.addClass('overloaded');
			}				

			// Set the button appearance.
			if(routingWaypoints.length < 2) {
				createConnectedRouteButton.hide();
			} else {
				createConnectedRouteButton.show();
			}

			// Ignore when it is an offroad route
			if (type === 'offroad') {
				$('.prettyroutes-waypoints-counter').hide();
			} else {
				$('.prettyroutes-waypoints-counter').show();
			}

			// Scrolldown the waypoints sidebar
			geocodersSidebar.scrollTop = geocodersSidebar.scrollHeight;
		}

		const map = L.map( $mapContainer.get( 0 ), {
			'attributionControl': false,
			'zoomControl': true,
			'doubleClickZoom': false,
			'scrollWheelZoom': true,
		} );

		map.setView( [lat, lng], zoom );

		map.addLayer( L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
		} ) );

		// Init OSMSearch
		new OSMSearch(map, search_form, (location) => addWaypoint(location));

		map.crb = {waypoints: waypoints};

		fieldObject.map = map;

		// On double click
		map.on( 'dblclick', function(event) {
			addWaypoint(event.latlng);
		} );

		// On zoom map
		map.on( 'zoomend', function(e) {
			var centered   = map.getCenter();
			var zoom_level = e.target._zoom;

			// update map info
			updateMapInfo( centered.lat, centered.lng, zoom_level );
		});

		// On move/drag the map
		map.on( 'dragend', function(e) {
			var centered   = map.getCenter();
			var zoom_level = e.target._zoom;

			// update map info
			updateMapInfo( centered.lat, centered.lng, zoom_level );
		});

		$mapContainer.on('keypress', function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
			}
		});

		$mapContainer.on('click', '.leaflet-routing-add-waypoint ', function() {
			// Scrolldown the waypoints sidebar
			const geocodersSidebar = $( ".leaflet-routing-geocoders" ).get(0);
			geocodersSidebar.scrollTop = geocodersSidebar.scrollHeight;
		});

		$document.ready(function(){
			refreshRoutingControl();
			updateWaypoints();

			let routeType = $typeInput.val();
			let routeColor = $colorInput.val();
			setInterval(function() {
				if (routeType !== $typeInput.val() || routeColor !== $colorInput.val()) {
					routeType = $typeInput.val();
					routeColor = $colorInput.val();
					refreshRoutingControl();
				}
			}, 2000);
		});
	};

	// Init map route-map input
	if ( $( '.acf-prettyroutes-route-field' ).length ) {
		Pretty_Routes_Map_With_Route( $( '.acf-prettyroutes-route-field' ), {} );
	}
	
	// Init map map-location input
	if ( $( '.acf-prettyroutes-map-location-field' ).length ) {
		Pretty_Routes_Map( $( '.acf-prettyroutes-map-location-field' ), {} );
	}

});
