const $ = jQuery;
const $document = $( document );
import { OpenStreetMapProvider } from 'leaflet-geosearch';
const MapService = require( './map-service.js' );
const GeocodingResult = require( '../geocoding-result.js' );
const utils = require( '../utils.js' );

class LeafletOSMMapService extends MapService {
	constructor( $canvas, settings ) {
		super( $canvas, settings );
		this.layers = {
			'road': [
				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					minZoom: 2,
					attribution: 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
				}),
			],
			'terrain': [
				L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
					minZoom: 2,
					attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012'
				}),
			],
			'pastel': [
				L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
					minZoom: 2,
					maxZoom: 19,
					attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>'
				}),
			],
			'image': [
				L.tileLayer( settings.map.tileset + 'z{z}-tile_{y}_{x}.png', {
					minZoom: 0,
					maxZoom: 4,
					noWrap: true
				} ),
			],
		};

		this.settings = settings;
		this.markers = [];
		this.cluster = L.markerClusterGroup();
		this.geocoder = new OpenStreetMapProvider();
		this.$canvas = $canvas;

		this.worldBounds = [
			[-85, -180], // Southwest coordinates
			[85, 180]  // Northeast coordinates
		];
		
		this.type = null;

		this.map = L.map( this.$canvas.get( 0 ), {
			'attributionControl': false,
			'zoomControl': false,
			'doubleClickZoom': false,
			'scrollWheelZoom': this.settings.zoom.enabled,
			'attribution': '',
			'maxBoundsViscosity': 0.9
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
		} );

		this.locateControl = new this.StealthLocateControl( {
			icon: '',
			iconLoading: '',
		} );
		this.locateControl.addTo( this.map );

		this.$canvas.css( {
			'background': this.settings.map.background
		} );
	}

	setType( type ) {
		for ( var layerType in this.layers ) {
			var layerGroup = this.layers[ layerType ];
			for (var i = 0; i < layerGroup.length; i++) {
				this.map.removeLayer( layerGroup[ i ] );
			}
		}

		for (var i = 0; i < this.layers[ type ].length; i++) {
			this.map.addLayer( this.layers[ type ][ i ] );
		}

		this.type = type;
		setTimeout( () => {
			if ( type === 'image' ) {
				this.map.setMaxBounds( null );
			} else {
				this.map.setMaxBounds( this.worldBounds );
			}
		}, 0);
	}

	getCenter() {
		return this.map.getCenter();
	}

	setCenter( center ) {
		var _this = this;
		return new Promise( function( resolve, reject ) {
			_this.map.once( 'moveend', resolve );
			_this.map.panTo( center, {
				reset: true,
				animate: false,
				noMoveStart: true,
			} );
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
		this.markersContainer.addLayer( marker );
		this.markers.push( marker );

		// Events
		marker.on( 'mouseover', function( e ) {
			var pin = this._Mapify;
			pin.mouseover = true;
			setTimeout( () => {
				pin.mouseover = false;
			} );
			_this.showMarkerTooltip( this );
		} );

		marker.on( 'mouseout', function( e ) {
			var pin = this._Mapify;
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
			var pin = marker._Mapify;
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
		var pin = marker._Mapify;
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

	geocode( query ) {
		return this.geocoder.search( { query: query } )
			.then( function( results ) {
				var filteredResults = [];
				for (var i = 0; i < results.length; i++) {
					var result = results[i]
					filteredResults.push( new GeocodingResult( result.label, result.y, result.x ) );
				}
				return Promise.resolve( filteredResults );
			} );
	}

	getPinsWithinRange( lat, lng, rangeInMeters ) {
		var target = [lat, lng];
		return this.markers.filter( function( marker ) {
			var distance = marker.getLatLng().distanceTo( target );
			return distance <= rangeInMeters;
		} ).map( function( marker ) {
			return marker._Mapify;
		} );
	}

	getPinClosestTo( lat, lng ) {
		var lowestDistance = -1;
		var closestPin = null;

		for (var i = 0; i < this.markers.length; i++) {
			var marker = this.markers[i];
			var distance = marker.getLatLng().distanceTo( [lat, lng] );
			if ( lowestDistance < 0 || distance < lowestDistance ) {
				lowestDistance = distance;
				closestPin = marker._Mapify;
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
			.then( function() {
				if ( ! _this.settings.cluster.enabled ) {
					return Promise.resolve();
				}
				var zoomToLayer = Promise.promisify( _this.cluster.zoomToShowLayer, { context: _this.cluster } );
				return zoomToLayer( marker );
			} )
			.then( () => _this.setCenter( marker.getLatLng() ) )
			.then( () => _this.showMarkerTooltip( marker ) );
	}

	getPinMarker( pin ) {
		for (var i = 0; i < this.markers.length; i++) {
			var marker = this.markers[i];
			if ( marker._Mapify === pin ) {
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

	redraw() {
		this.map._onResize();
	}
};

module.exports = LeafletOSMMapService;
