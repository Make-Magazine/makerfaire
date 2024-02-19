'use strict';

const $ = jQuery;
const $document = $( document );
const utils = require( './utils.js' );
const LeafletOSMMapService = require( './map-services/leaflet-osm-map-service.js' );
const Pin = require( './pin.js' );
const LocationList = require( './location-list.js' );

class Map {
	constructor( $container, settings ) {
		var defaults = {
			strings: window.mapify_script_settings.strings,
			map: {
				id: 0,
				type: 'road',
				mode: 'map',
				center: [0, 0],
				tileset: '',

				background: ''
			},
			zoom: {
				enabled: true,
				zoom: 3
			},
			pins: {
				defaultImage: '',
				pins: []
			},
			cluster: {
				enabled: false
			},
			tooltip: {
				background: [255, 255, 255, 1]
			},
			search: {
				centerOnSearch: true,
				radiusUnitName: 'Miles',
				radiusUnit: 'mi',
				radius: 5,
				radiusInMeters: 0,
				regionBias: ''
			},
			filters: {
				centerOnFilter: true
			},
			routes: []
		};
		this.settings = $.extend( true, {}, defaults, settings );
		this.calculateSearchRadiusInMeters();

		this.$container = $container.first();
		if ( ! this.$container ) {
			console.log( 'Could not find map container' );
			return;
		}

		this.searchIsSearching = false;
		this.searchLastResult = null;
		this.searchRadiusInMeters = 0;
		this.searchLastClosestPin = null;

		window.Mapify.instances.push( this );
		this.$container.data( 'mapify', this );
		this.$canvas = this.$container.find( '.mpfy-map-canvas:first' );
		this.$zoomInButton = this.$container.find( '.mpfy-zoom-in:first' );
		this.$zoomOutButton = this.$container.find( '.mpfy-zoom-out:first' );
		this.$tagSelect = this.$container.find( '.mpfy-tag-select:first' );
		this.$searchForm = this.$container.find( '.mpfy-search-form:first' );
		this.$searchFormInput = this.$searchForm.find( '.mpfy-search-input:first' );
		this.$searchFormButton = this.$searchForm.find( '.mpfy-search-button:first' );
		this.$searchFormClearButton = this.$searchForm.find( '.mpfy-search-clear:first' );
		this.$searchRadiusSelect = this.$container.find( 'select[name="mpfy_search_radius"]:first' );
		this.$locationList = this.$container.find( '.mpfy-mll:first' );
		this.$geolocateButton = this.$container.find( '.mpfy-geolocate:first' );

		this.proprietaryData = JSON.parse( this.$container.attr( 'data-proprietary' ) );
		this.pins = [];

		this.mapService = new LeafletOSMMapService( this.$canvas, {
			map: {
				background: this.settings.map.background,
				tileset: this.settings.map.tileset
			},
			zoom: {
				enabled: this.settings.zoom.enabled,
				zoom: this.settings.zoom.zoom
			},
			cluster: {
				enabled: this.settings.cluster.enabled
			}
		} );

		if ( this.settings.map.mode === 'image' ) {
			this.mapService.setType( 'image' );
		} else {
			this.mapService.setType( this.settings.map.type );
		}

		this.mapService.setCenter( this.settings.map.center );
		this.mapService.setZoom( this.settings.zoom.zoom );

		this.locationList = ( this.$locationList.length > 0 ) ? new LocationList( this.$locationList, this ) : null;

		this.addEvents();
		this.addPins()
			.then( () => {
				this.$container.trigger( 'mapify.map.loaded', this );
				return Promise.resolve();
			} );
	}

	calculateSearchRadiusInMeters() {
		this.settings.search.radiusInMeters = utils.unit2meters( this.settings.search.radius, this.settings.search.radiusUnit );
	}

	getProprietaryData( key ) {
		if ( typeof this.proprietaryData[ key ] == 'undefined') {
			return null;
		}
		return this.proprietaryData[ key ];
	}

	addPins() {
		for ( var i = 0; i < this.settings.pins.pins.length; i++) {
			var pinModel = this.settings.pins.pins[i];

			var tooltipClasses = [ 'mpfy-tooltip-map-' + this.settings.map.id, 'mpfy-tooltip-image-orientation-' + this.settings.tooltip.imageOrientation ];
			if ( pinModel.thumbnail ) {
				tooltipClasses.push( 'mpfy-tooltip-with-thumbnail' );
			}
			var pin = new Pin( pinModel, {
				classes: tooltipClasses.join( ' ' ),
				background: this.settings.tooltip.background,
				content: pinModel.tooltipContent,
				closeBehavior: pinModel.tooltipCloseBehavior,
				animate: pinModel.animateTooltips
			} );
			this.pins.push( pin );
		}
		return this.mapService.addPins( this.pins );
	}

	addEvents() {
		var _this = this;

		_this.tagSelectEvent = function() {
			var tagId = $( this ).val();
			_this.filterByTag( tagId );
		};

		$document.one( 'ready', function() {
			$( 'body' ).trigger( 'mapify.map.created', _this );

			if ( _this.$tagSelect.length > 0 ) {
				_this.$tagSelect.on( 'change', _this.tagSelectEvent );
			}

			if ( _this.$searchRadiusSelect.length > 0 ) {
				_this.$searchRadiusSelect.on( 'change', function(){
					_this.settings.search.radius = parseInt( $( this ).val() );
					_this.calculateSearchRadiusInMeters();
				} ).trigger( 'change' );
			}
		} );

		$( window ).on( 'load resize orientationchange mapify.redraw', function() {
			var controls = _this.$container.find( '.mpfy-controls:first' );
			var breakingWidth = 650;
			var controlsWidth = controls.width();
			controls.toggleClass( 'mpfy-controls-mobile', controlsWidth <= breakingWidth );
			_this.$container.toggleClass( 'mpfy-layout-mobile', controlsWidth <= breakingWidth );
		} );

		if ( "ontouchstart" in window || navigator.msMaxTouchPoints ) {
			this.$container.addClass( 'mpfy-touch-device' );
		}

		this.$searchFormClearButton.click( function( e ) {
			e.preventDefault();
			_this.clearSearch();
		} );

		this.$searchFormInput.bind( 'keyup change', function( e ) {
			_this.$searchFormClearButton.toggle( $(this).val().length > 0 );
		} );

		this.$zoomInButton.click( function( e ) {
			e.preventDefault();
			_this.mapService.setZoom( _this.mapService.getZoom() + 1 );
		} );

		this.$zoomOutButton.click( function( e ) {
			e.preventDefault();
			_this.mapService.setZoom( Math.max( 0, _this.mapService.getZoom() - 1 ) );
		} );

		this.$searchForm.submit( function( e ) {
			e.preventDefault();
			var recenterOnBlank = (typeof e.mapify == 'undefined' || typeof e.mapify.recenterOnBlank == 'undefined') ? true : e.mapify.recenterOnBlank;

			var query = _this.$searchFormInput.val().toLowerCase();
			_this.search( query, recenterOnBlank );
		} );

		this.$geolocateButton.on( 'click', e => {
			e.preventDefault();
			this.mapService.locate()
				.then( () => {
					_this.$container.trigger( 'mapify.useMyLocation.ended' );
				} );
		} );
	}

	getPinById( pinId ) {
		for (var i = 0; i < this.pins.length; i++) {
			var pin = this.pins[i];
			if ( pin.model.id == pinId ) {
				return pin;
			}
		}
		return null;
	}

	getVisiblePins() {
		return this.pins.filter( function( pin ) {
			return pin.isVisible();
		} );
	}

	filter( visibilityCondition, filterCallback ) {
		var _this = this;
		var promises = [];
		for (var i = this.pins.length - 1; i >= 0; i--) {
			var pin = this.pins[i];
			pin.setVisibility( visibilityCondition, filterCallback( pin ) );
			promises.push( this.mapService.updatePinVisibility( pin ) );
		}
		return Promise.all( promises )
			.then( () => {
				_this.$container.trigger( 'mapify.pins.visibilityUpdated', visibilityCondition );
			} );
	}

	filterByTag( tagId ) {
		var _this = this;
		tagId = parseInt( tagId );
		_this.$tagSelect.off( 'change', _this.tagSelectEvent );
		this.$tagSelect.val( tagId ).trigger( 'change' );
		_this.$tagSelect.on( 'change', _this.tagSelectEvent );

		return Promise.resolve()
			.then( () => {
				if ( tagId <= 0 ) {
					return _this.filter( 'tag', pin => true )
						.then( () => _this.mapService.setCenter( _this.settings.map.center ) ) // always center first
						.then( () => _this.mapService.setZoom( _this.settings.zoom.zoom ) )
						.then( () => _this.mapService.setCenter( _this.settings.map.center ) ); // center again after zoom to avoid rounding inaccuracies
				}

				return _this.filter( 'tag', pin => ( typeof pin.model.tags[ tagId.toString() ] !== 'undefined' ) )
					.then( () => {
						if ( _this.settings.filters.centerOnFilter ) {
							var visiblePins = _this.getVisiblePins();
							return _this.mapService.fitPins( visiblePins );
						}
						return Promise.resolve();
					} );
			} )
			.then( () => {
				_this.$container.trigger( 'mapify.filter.ended', {
					tagId: tagId
				} );
			} );
	}

	requestSearchResults( query, recenterOnBlank ) {
		var _this = this;
		if ( _this.searchIsSearching ) {
			return Promise.reject( 'busy' );
		}

		_this.searchLastResult = null;
		if ( query.length === 0 ) {
			return _this.filter( 'search', pin => true )
				.then( () => {
					if ( recenterOnBlank ) {
						return _this.mapService.setCenter( _this.settings.map.center );
					}
					return Promise.resolve();
				} );
		}

		if ( _this.settings.map.mode === 'image' ) {
			return _this.filter( 'search', pin => ( pin.model.city.indexOf( query ) !== -1 || pin.model.city.indexOf( query ) !== -1 ) )
				.then( () => {
					if ( _this.getVisiblePins().length === 0 ) {
						utils.showSearchPopup( _this, _this.settings.strings.no_search_results);
					}
					return Promise.resolve();
				} );
		}

		_this.searchIsSearching = true;
		return _this.mapService.geocode( query + ', ' + _this.settings.search.regionBias )
			.then( function( results ) {
				if ( results.length === 0 ) {
					utils.showSearchPopup( _this, _this.settings.strings.search_geolocation_failure );
					return Promise.resolve();
				}

				var result = results[0];
				_this.searchLastResult = result;

				var filteredPins = _this.mapService.getPinsWithinRange( result.lat, result.lng, _this.settings.search.radiusInMeters );

				return _this.filter( 'search', pin => ( filteredPins.indexOf( pin ) !== -1 ) )
					.then( () => {
						if ( _this.getVisiblePins().length === 0 ) {
							_this.searchLastClosestPin = _this.mapService.getPinClosestTo( result.lat, result.lng );
							utils.showSearchPopup( _this, _this.settings.strings.no_search_results_with_closest );
						}
						return Promise.resolve();
					} );
			} )
			.catch( function( error ) {
				console.log( error );
			} )
			.then( function() {
				_this.searchIsSearching = false;
				return Promise.resolve();
			} );
	}

	search( query, recenterOnBlank ) {
		var _this = this;
		return _this.requestSearchResults( query, recenterOnBlank )
			.then( function() {
				var visiblePins = _this.getVisiblePins();
				if ( visiblePins.length > 0 && _this.settings.search.centerOnSearch ) {
					return _this.mapService.fitPins( visiblePins );
				}
				return Promise.resolve();
			} )
			.then( function() {
				_this.$container.trigger( 'mapify.search.ended', {
					'query': query
				} );
				return Promise.resolve();
			} )
			.catch( function( error ) {
				if ( error !== 'busy' ) {
					return Promise.reject( error );
				}
			} );
	}

	clearSearch( recenterOnBlank ) {
		recenterOnBlank = ( typeof recenterOnBlank == 'undefined') ? true : recenterOnBlank;
		this.$searchFormInput.val( '' ).trigger( 'change' );
		return this.search( '', recenterOnBlank );
	}
}

module.exports = Map;
