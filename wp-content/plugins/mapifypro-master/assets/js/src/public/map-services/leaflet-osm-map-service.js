const $ = jQuery;
const $document = $( document );
const CustomOSMProvider = require('../../osm-provider');
const MapService = require( './map-service.js' );
const GeocodingResult = require( '../geocoding-result.js' );
const utils = require( '../utils.js' );
const mapStyles = new MapifyMapStyles(L);

class LeafletOSMMapService extends MapService {
	constructor( $canvas, settings ) {
		super( $canvas, settings );

		mapStyles.add({
			'image': new L.tileLayer( settings.map.tileset + 'z{z}-tile_{y}_{x}.png', {
				minZoom: 0,
				maxZoom: 4,
				noWrap: true
			} )
		});

		this.layers = mapStyles.get();		
		this.settings = settings;
		this.markers = [];
		this.mapifyPins = []; // array of Mapify pins by marker ID
		this.cluster = L.markerClusterGroup();
		this.geocoder = new CustomOSMProvider();
		this.$canvas = $canvas;

		this.worldBounds = [
			[-85, -180], // Southwest coordinates
			[85, 180]  // Northeast coordinates
		];
		this.type = null;
		
		this.map = L.map( this.$canvas.get( 0 ), {
			'attributionControl': false,
			'zoomControl': false,
			'doubleClickZoom': !this.settings.crowdmap_enabled, // Set to enable if the crowdmaps for this map is disabled.
			'scrollWheelZoom': this.settings.zoom.enabled,
			'attribution': '',
			'maxBoundsViscosity': 0.9,
			'maxZoom': 18,
		} );

		this.map.on( 'dblclick', e => $( this ).trigger( 'dblclick', e.latlng ) );

		this.map.addLayer( this.cluster );

		this.markersContainer = this.settings.cluster.enabled ? this.cluster : this.map;

		this.StealthLocateControl = L.Control.Locate.extend( {
			onAdd: function ( m ) {
				// do not render a button
				this._layer = this.options.layer || L.layerGroup();
				this._layer.addTo( m );
				this._event = undefined;
				this._prevBounds = null;

				let container = L.DomUtil.create('div');
				let linkAndIcon = this.options.createButtonCallback( container, this.options );
				this._link = linkAndIcon.link;
				this._icon = linkAndIcon.icon;

				this._resetVariables();

				this._map.on('unload', this._unload, this);

				return container;
			},
		} );

		this.attributionControl = L.control.attribution( {
			prefix: false,
			position: 'bottomleft',
		} );
		this.map.addControl(this.attributionControl);

		this.locateControl = new this.StealthLocateControl( {
			icon: 'icon-null-class',
			iconLoading: 'icon-loading-null-class',
		} );
		this.locateControl.addTo( this.map );

		this.$canvas.css( {
			'background': this.settings.map.background
		} );
	}

	setType( type ) {
		for ( var layerType in this.layers ) {
			var layerGroup = this.layers[ layerType ];
			this.map.removeLayer( layerGroup );
		}

		var newLayer = this.layers[ type ];
		newLayer.addTo( this.map );

		this.type = type;
		setTimeout( () => {
			if ( type === 'image' ) {
				this.map.setMaxBounds( null );				
			} else {
				this.map.setMaxBounds( this.worldBounds );
			}
			
			if (newLayer?.options?.maxZoom) {
				this.map.setMaxZoom( newLayer.options.maxZoom );
			}
		}, 0);
	}

	getCenter() {
		return this.map.getCenter();
	}

	setCenter( center ) {
		var _this = this;
		return new Promise( function( resolve, reject ) {
			try {
				_this.map.once( 'moveend', resolve );
				_this.map.panTo( center, {
					reset: true,
					animate: false,
					noMoveStart: true,
				} );
			} catch ( error ) {
				console.log(error);
				reject(error);
			}			
		} );
	}

	setZoom( zoom ) {
		var _this = this;
		return new Promise( function( resolve, reject ) {
			if ( _this.map.getZoom() === zoom ) {
				resolve();
				return;
			}
			_this.map.once( 'zoomend', resolve );
			_this.map.setZoom( zoom, {
				reset: true,
				animate: false
			} );
		} );
	}

	getZoom() {
		return this.map.getZoom();
	}

	addPinWithIcon( pin, icon ) {
		var _this = this;
		var markerOptions = {
			'draggable': false,
			'riseOnHover': true
		};
		
		if ( icon ) {
			markerOptions.icon = icon;
		}
		
		if ( pin.model.animatePinpoints && ! this.settings.cluster.enabled ) {
			markerOptions.bounceOnAdd = true;
		}
		
		var marker = L.marker( pin.model.latlng, markerOptions );
		marker._Mapify = pin;

		if ( pin.isVisible() ) {
			this.markersContainer.addLayer( marker );
		}

		// For some reason addLayer causes the marker to change it's LatLng
		// so we buffer it and reapply it
		marker.setLatLng( pin.model.latlng );

		this.markers.push( marker );
		this.mapifyPins[ marker._leaflet_id ] = pin;

		// Events
		marker.on( 'mouseover', function( e ) {
			var pin = _this.mapifyPins[ this._leaflet_id ];
			pin.mouseover = true;
			setTimeout( () => {
				pin.mouseover = false;
			} );
			_this.showMarkerTooltip( this );
		} );

		marker.on( 'mouseout', function( e ) {
			var pin = _this.mapifyPins[ this._leaflet_id ];
			var tooltip = pin.tooltip;
			if ( ! tooltip ) {
				return;
			}
			tooltip.node().trigger( {
				'type': 'tooltip_mouseout'
			} );
		} );

		marker.on( 'click', function( e ) {
			var marker = this;
			var pin = _this.mapifyPins[ this._leaflet_id ];
			var tooltip = pin.tooltip;

			var hover = function() {
				_this.showMarkerTooltip( marker );
			}

			var click = function() {
				$document.trigger( 'mapify.action.openPopup', {
					value: pin.model.id
				} );
			}

			if ( tooltip && ! tooltip.node().is( ':visible' ) ) {
				hover();
				return;
			}
			click();
		} );

		if ( pin.tooltip ) {
			this.map.on( 'movestart', function(){
				pin.tooltip.hide();
			});
			this.map.on( 'moveend', function(){
				pin.tooltip.hide();
			});
			this.map.on( 'zoomstart', function(){
				pin.tooltip.hide();
			});
			this.map.on( 'zoomend', function(){
				pin.tooltip.hide();
			});
			this.map.on( 'viewreset', function(){
				pin.tooltip.hide();
			});
		}
	}

	addPin( pin ) {
		var _this = this;

		return new Promise( function( resolve, reject ) {
			var onImageLoaded = function() {
				var width = pin.model.image.size ? pin.model.image.size[0] : 0;
				var height = pin.model.image.size ? pin.model.image.size[1] : 0;
				if ( ! width ) {
					width = pin.image.width;
					height = pin.image.height;
				}

				var icon = L.icon( {
					'iconUrl': pin.model.image.url,
					'iconAnchor': [width / 2, height]
				} );
				_this.addPinWithIcon( pin, icon );
				resolve();
			}

			if ( pin.model.image.url ) {
				if ( pin.image.complete ) {
					onImageLoaded();
				} else {
					pin.image.onload = onImageLoaded;
					pin.image.onLoad = pin.image.onload;
				}
			} else {
				_this.addPinWithIcon( pin, null );
				resolve();
			}
		} );
	}

	addPins( pins ) {
		var promises = [];
		for (var i = 0; i < pins.length; i++) {
			var pin = pins[i];
			promises.push( this.addPin( pin ) );
		}
		return Promise.all( promises );
	}

	showTooltip( tooltip, latLng, anchor ) {
		var $win = $(window);
		if ( tooltip.node().is( ':visible' ) ) {
			return false;
		}

		var containerPoint = this.map.latLngToContainerPoint( latLng );
		var left = this.$canvas.offset().left + containerPoint.x - Math.ceil( tooltip.node().width() / 2 ) + anchor[0];
		var top = this.$canvas.offset().top + containerPoint.y - tooltip.node().height() + anchor[1];

		if ( $win.width() >= 767 && tooltip.node().hasClass( 'mpfy-tooltip-image-orientation-left' )  && tooltip.node().hasClass( 'mpfy-tooltip-with-thumbnail' )) {
			var left = this.$canvas.offset().left + containerPoint.x - Math.ceil( tooltip.node().width() / 2 - 61 ) + anchor[0];
		};

		setTimeout( function() {
			tooltip.node().trigger( {
				'type': 'tooltip_mouseover',
				'settings': {
					'left': left,
					'top': top
				}
			} );
		}, 1 );
	}

	showMarkerTooltip( marker ) {
		var _this = this;
		var latLng = marker.getLatLng();
		var pin = _this.mapifyPins[ marker._leaflet_id ];
		var tooltip = pin.tooltip;

		if ( ! tooltip ) {
			return;
		}

		var anchor = [0, -10];
		if ( pin.model.image.url ) {
			if ( pin.model.image.anchor[1] > 0 ) {
				anchor[1] -= pin.model.image.anchor[1];
			} else {
				anchor[1] -= pin.image.height;
			}
		} else {
			anchor[1] = -50; // default pin height
		}

		if ( utils.isPhone() ) {
			// offset the position slightly so that the map is centered lower so there is more vertical space for the tooltip on mobile

			var targetPoint = _this.map.project( latLng, _this.getZoom() );
			var targetLatLng = _this.map.unproject( targetPoint.add( L.point( [0, -128] ) ) );

			_this.setCenter( targetLatLng )
				.then( function() {
					_this.showTooltip( tooltip, latLng, anchor );
				} );
		} else {
			_this.showTooltip( tooltip, latLng, anchor );
		}
	}

	geocode( query, country = '' ) {
		return this.geocoder.search( { query, country } )
			.then(results => {
				if (results.length) {
					return results;
				}

				return this.geocoder.search({ query: '', postalcode: query, country });
			}).then(results => {
				if (results.length) {
					return results;
				}

				return this.geocoder.search({ query, postalcode: query, country });
			}).then(results => {
				let filteredResults = results.map(result => {
					return new GeocodingResult( result.label, result.y, result.x );
				});

				return filteredResults;
			});
	}

	getPinsWithinRange( lat, lng, rangeInMeters ) {
		var _this = this;
		var target = [lat, lng];
		
		return this.markers.filter( function( marker ) {
			var distance = marker.getLatLng().distanceTo( target );
			return distance <= rangeInMeters;
		} ).map( function( marker ) {
			var pin = _this.mapifyPins[ marker._leaflet_id ];
			return pin;
		} );
	}

	getPinClosestTo( lat, lng ) {
		var _this = this;
		var lowestDistance = -1;
		var closestPin = null;

		for (var i = 0; i < this.markers.length; i++) {
			var marker = this.markers[i];
			var distance = marker.getLatLng().distanceTo( [lat, lng] );
			if ( lowestDistance < 0 || distance < lowestDistance ) {
				var pin = _this.mapifyPins[ marker._leaflet_id ];
				lowestDistance = distance;
				closestPin = pin;
			}
		}
		return closestPin;
	}

	getPinDistance( lat, lng, pin ) {
		var marker = this.getPinMarker( pin );
		var distance = marker.getLatLng().distanceTo( [lat, lng] );
		return distance;
	}

	highlightPin( pin ) {
		var _this = this;
		var marker = this.getPinMarker( pin );
		
		if ( ! marker ) {
			return Promise.resolve();
		}
		
		return Promise.resolve()
			.then( () => {
				const promises = this.markers.map(marker => {					
					const currentPin = _this.mapifyPins[ marker._leaflet_id ];
					currentPin.setVisibility('base', true);
					return _this.updatePinVisibility(currentPin);
				});

				return Promise.all(promises);
			} )
			.then( function() {
				if ( ! _this.settings.cluster.enabled ) {
					return Promise.resolve();
				}
				var zoomToLayer = Promise.promisify( _this.cluster.zoomToShowLayer, { context: _this.cluster } );
				return zoomToLayer( marker );
			} )
			.then( function () {
				return _this.setCenter(marker.getLatLng());
			} )
			.then( function () {
				return _this.showMarkerTooltip(marker);
			} );
	}

	getPinMarker( pin ) {
		var _this = this;

		for ( var i = 0; i < this.markers.length; i++ ) {
			var marker = this.markers[i];
			var mapifyPin = _this.mapifyPins[ marker._leaflet_id ];

			if ( mapifyPin === pin ) {
				return marker;
			}
		}

		return null;
	}

	fitPins( pins ) {
		var _this = this;
		if ( pins.length === 0 ) {
			return;
		}
		var latLngs = pins.map( function( pin ) {
			return pin.model.latlng;
		} );
		var bounds = L.latLngBounds( latLngs );

		return new Promise( function( resolve, reject ) {
			_this.map.once( 'viewreset', resolve );
			_this.map.fitBounds( bounds, {
				reset: true,
				animate: false,
				noMoveStart: true,
				maxZoom: _this.map.getZoom(),
				padding: [50, 50],
			} );
		} );
	}

	updatePinVisibility( pin ) {
		var _this = this;
		var marker = _this.getPinMarker( pin );
		if ( ! marker ) {
			return;
		}

		return new Promise( ( resolve, reject ) => {
			if ( pin.isVisible() ) {
				if ( ! _this.markersContainer.hasLayer( marker ) ) {
					// For some reason addLayer causes the marker to change it's LatLng
					// so we buffer it and reapply it
					var markerLatLng = marker.getLatLng();

					_this.markersContainer.on( 'layeradd', resolve );
					_this.markersContainer.addLayer( marker );
					marker.setLatLng( markerLatLng );
					return;
				}
			} else {
				if ( _this.markersContainer.hasLayer( marker ) ) {
					_this.markersContainer.on( 'layerremove', resolve );
					_this.markersContainer.removeLayer( marker );
					return;
				}
			}
			resolve();
		} );
	}

	locate() {
		return new Promise( ( resolve, reject ) => {
			this.map.once( 'locationfound', resolve );
			this.map.once( 'locationerror', reject );
			this.locateControl._onClick();
		} );
	}

	locateWithoutFocus() {		
		return new Promise( ( resolve ) => {
			this.map.once( 'locationfound', resolve );
			this.map.once( 'locationerror', resolve );
			this.map.locate();
		} );
	}

	redraw() {
		this.map._onResize();
	}
};

module.exports = LeafletOSMMapService;
