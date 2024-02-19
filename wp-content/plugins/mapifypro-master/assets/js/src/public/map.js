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
			routes: [],
			crowdmap_enabled: false,
		};

		this.settings = $.extend( true, {}, defaults, settings );
		this.$container = $container.first();
		this.calculateSearchRadiusInMeters();

		if ( ! this.$container || ! this.$container.length ) {
			console.log( 'Could not find map container' );
			return;
		}

		this.searchIsSearching = false;
		this.searchLastResult = null;
		this.searchRadiusInMeters = 0;
		this.searchLastClosestPin = null;
		this.searchDelayTimeout = null;
		this.circleRadius = null;
		this.geolocationCircleRadius = null;

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
		this.$searchResetButton = this.$searchForm.find( '.mpfy-reset-search-button:first' );
		this.$searchQueryFields = this.$searchForm.find( '.mpfy-search-field-queries:first' );
		this.$searchOriginQueryField = this.$searchForm.find( '.mpfy-search-field-item:first' );
		this.$searchAddQueryFieldButton = this.$searchOriginQueryField.children( '.mpfy-search-field-add-item' );
		this.$searchFieldsDropdownButton = this.$searchQueryFields.children( '.mpfy-search-field-dropdown-toggle' );
		this.$searchRadiusSelect = this.$container.find( 'select[name="mpfy_search_radius"]:first' );
		this.$searchRadiusSelectType = this.$container.find( 'select[name="mpfy_search_radius_type"]:first' );
		this.$locationList = this.$container.find( '.mpfy-mll:first' );
		this.$geolocateButton = this.$container.find( '.mpfy-geolocate:first' );

		this.proprietaryData = JSON.parse( this.$container.attr( 'data-proprietary' ) );
		this.attrsData = JSON.parse( this.$container.attr( 'data-attrs' ) );
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
			},
			crowdmap_enabled: this.settings.crowdmap_enabled,
		} );

		if ( this.settings.map.mode === 'image' ) {
			this.mapService.setType( 'image' );
		} else {
			this.mapService.setType( this.settings.map.type );
		}

		this.mapService.setZoom( this.settings.zoom.zoom );
		this.mapService.setCenter( this.settings.map.center );

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

	getAttrsData( key ) {
		if ( typeof this.attrsData[ key ] == 'undefined') {
			return null;
		}
		return this.attrsData[ key ];
	}

	addPins() {
		for ( var i = 0; i < this.settings.pins.pins.length; i++) {
			var pinModel = this.settings.pins.pins[i];
			var tooltipClasses = [ 'mpfy-tooltip-map-' + this.settings.map.id, 'mpfy-tooltip-image-orientation-' + this.settings.tooltip.imageOrientation ];

			if ( pinModel.popupEnabled ) {
				tooltipClasses.push( 'mpfy-tooltip-has-popup' );
			}

			if ( pinModel.thumbnail ) {
				tooltipClasses.push( 'mpfy-tooltip-with-thumbnail' );
			}

			var pin = new Pin(
				pinModel,
				{
					classes: tooltipClasses.join( ' ' ),
					background: this.settings.tooltip.background,
					content: pinModel.tooltipContent,
					closeBehavior: pinModel.tooltipCloseBehavior,
					animate: pinModel.animateTooltips,
					pinId: pinModel.id,
				},
				{
					hideInitially: !!this.settings.pins.hideInitially,
				}
			);

			this.pins.push( pin );
		}
		return this.mapService.addPins( this.pins );
	}

	addEvents() {
		var _this = this;

		_this.tagSelectEvent = function() {
			var tagId = $( this ).val();

			$document.trigger( 'mapify.action.setMapTag', {
				mapId: _this.settings.map.id,
				value: tagId,
				element: $( this ),
				force: true
			} );
		};

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

		this.$searchQueryFields.on( 'keyup', '.mpfy-search-input', function( e ) {
			clearTimeout( _this.searchDelayTimeout );
			
			const minChars = 2;

			if ( minChars <= $(this).val().length ) {
				_this.searchDelayTimeout = setTimeout(function(){					
					_this.doAutoSearch(); // trigger the search
				}, 750);
			}
		} );

		this.$searchForm.on( 'change', '.mpfy-search-radius select', function( e ) {
			clearTimeout( _this.searchDelayTimeout );
			
			_this.searchDelayTimeout = setTimeout(function(){
				_this.doAutoSearch(); // trigger the search
			}, 100);
		} );

		this.$zoomInButton.click( function( e ) {
			e.preventDefault();
			_this.mapService.setZoom( _this.mapService.getZoom() + 1 );
		} );

		this.$zoomOutButton.click( function( e ) {
			e.preventDefault();
			_this.mapService.setZoom( Math.max( 0, _this.mapService.getZoom() - 1 ) );
		} );
		
		var selects_input = this.$container.find( '.mpfy-selecter-wrap select' );
		this.selecters    = null;

		if ( selects_input.length ) {
			this.selecters = $(selects_input).selecter();
		}

		this.$searchForm.submit( function( e ) {
			e.preventDefault();
			var recenterOnBlank = (typeof e.mapify == 'undefined' || typeof e.mapify.recenterOnBlank == 'undefined') ? true : e.mapify.recenterOnBlank;
			var queries = _this.getSearchQueries();
			
			_this.search( queries, recenterOnBlank );
		} );

		this.$geolocateButton.on( 'click', e => {
			e.preventDefault();
			this.mapService.locate()
				.then( () => {
					_this.$container.trigger( 'mapify.useMyLocation.ended' );
				} );
		} );

		this.$searchResetButton.on( 'click', e => {
			const _this = this;

			this.search( '', false )
				.then( function() {
					// reset query fields
					_this.$searchForm.find( '.mpfy-search-field-item' ).slice(1).remove();
					_this.$searchForm.removeClass( 'has-multiple-queries' );
					_this.$searchOriginQueryField.children('.mpfy-search-input').val('');

					// close the 'search not found' modal
					if ( 'undefined' !== typeof _this.searchTooltip ) {
						_this.searchTooltip.hide();
					}

					// clear all circle search radius
					_this.clearCircleRadius();
					_this.clearGeolocationCircleRadius();

					// remove search results status
					_this.$searchForm.removeClass('mpfy-search-has-results');
				} );
		} );

		(() => {
			if (!ResizeObserver) {
				return;
			}

			const resizeObserver = new ResizeObserver(entries => {
				this.mapService.redraw();
			});

			resizeObserver.observe(this.$container[0]);
		})();

		this.$searchAddQueryFieldButton.on( 'click', function( e ) {
			e.preventDefault();
			var $clone           = _this.$searchOriginQueryField.clone();
			var $clonedAddButton = $clone.children( '.mpfy-search-field-add-item' );

			// replace original add button to remove button
			$clonedAddButton.attr( 'class', 'mpfy-search-field-remove-item' );
			$clonedAddButton.html( '-' );

			// set the cloned input field to empty
			$clone.children('.mpfy-search-input').val( '' );
							
			// append to the form
			$clone.appendTo( _this.$searchQueryFields );

			// add a specific class-name if there are multiple fields
			_this.multipeQueriesStatus();

			// set placeholder
			_this.setMultiSearchInputPlaceholder();
		} );

		this.$searchQueryFields.on( 'click', '.mpfy-search-field-remove-item', function( e ) {
			e.preventDefault();
			$( this ).parent( '.mpfy-search-field-item' ).remove();
			
			// trigger the search
			_this.doAutoSearch();

			// add a specific class-name if there are multiple fields
			_this.multipeQueriesStatus();

			// set placeholder
			_this.setMultiSearchInputPlaceholder();
		} );
		
		$( document ).on( 'click', function( e ) {
			if ( 
				$( e.target ).closest( _this.$searchQueryFields ).length === 0 &&
				! $( e.target ).hasClass( 'mpfy-search-field-remove-item' ) &&
				! $( e.target ).hasClass( 'mpfy-search-field-add-item' )
			) {
				_this.hideMultiSearchQueryFields();
			} else {
				_this.showMultiSearchQueryFields()
			}			
		});

		this.$searchFieldsDropdownButton.on( 'click', function( e ) {
			_this.$searchQueryFields.addClass( 'show-query-fields' );
		});

		this.$container.find( '.mpfy-selecter-wrap select' ).on('change', function( e ) {
			_this.hideMultiSearchQueryFields();
		} );

		this.$container.find( '.mpfy-selecter-wrap .selecter .selecter-selected' ).on( 'click', function( e ) { 
			_this.hideMultiSearchQueryFields();
		} );

		this.$searchQueryFields.on( 'keydown', 'input', function( e ) {
			if( 13 === e.which ) {
				e.stopPropagation();
			}
		} );

		$( document ).on( 'click', '.mpfy-tooltip', function( e ) {
			var pinId = parseInt( $(this).data('pinId') );

			if ( pinId > 0 && ! $(e.target).hasClass( 'mpfy-close-tooltip' ) ) {
				$document.trigger( 'mapify.action.openPopup', {
					value: pinId
				} );
			}
		} );
	}

	doAutoSearch() {
		this.$searchForm.trigger( 'submit' );
	}

	showMultiSearchQueryFields() {
		this.$searchQueryFields.addClass( 'show-query-fields' );
	}

	setMultiSearchInputPlaceholder() {
		var fieldItems = this.$searchForm.find( '.mpfy-search-field-item' );
		
		// set placeholder for the second field
		fieldItems.eq( 1 ).children('.mpfy-search-input').attr( 'placeholder', this.settings.search.labelSearchSecond );

		// remove placeholder on 3rd field and so on
		fieldItems.slice( 2 ).children('.mpfy-search-input').attr( 'placeholder', '' );
	}

	hideMultiSearchQueryFields() {
		this.$searchQueryFields.removeClass( 'show-query-fields' );
	}

	multipeQueriesStatus() {
		if ( 1 < this.$searchForm.find( '.mpfy-search-field-item' ).length ) {
			this.$searchForm.addClass( 'has-multiple-queries' );
		} else {
			this.$searchForm.removeClass( 'has-multiple-queries' );
		}
	}

	getSearchQueries() {
		var input_fields = this.$searchQueryFields.find('.mpfy-search-input');
		var queries = [];

		for (let index = 0; index < input_fields.length; index++) {
			var new_query = $(input_fields[index]).val().trim().toLowerCase();

			if ( '' != new_query ) {
				queries.push( new_query );
			}			
		}

		return queries;
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

	filter( visibilityCondition, filterCallback, baseVisibility = true ) {
		var _this = this;
		var promises = [];
		
		for (var i = this.pins.length - 1; i >= 0; i--) {
			var pin = this.pins[i];
			pin.setVisibility( visibilityCondition, filterCallback( pin ) );
			pin.setVisibility('base', baseVisibility);
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
					return _this.filter( 'tag', pin => true, !_this.settings.pins.hideInitially )
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

				// remove the "multiple" option
				_this.toggleTagSelectMultipleOption( false );
			} );
	}

	filterByTags( tagIds ) {
		var _this = this;
		
		if ( tagIds.length <= 1 ) {
			var tagId = typeof tagIds[0] === 'undefined' ? 0 : tagIds[0];
			_this.filterByTag( tagId );
			return Promise.resolve();
		} else {
			return Promise.resolve()
			.then( () => {
				return _this.filter( 'tag', function( pin ) {
						var match = false;

						tagIds.forEach(tagId => {
							if ( typeof pin.model.tags[ tagId.toString() ] !== 'undefined' ) {
								match = true;
							}
						});
					
						return match;
					} )
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
					tagId: tagIds[0],
					tagIds: tagIds,
				} );

				// add then select the "multiple" option
				_this.toggleTagSelectMultipleOption( true );
				_this.$tagSelect.val( 'multiple' );
				_this.$tagSelect.trigger( 'change' );
			} );
		}
	}

	toggleTagSelectMultipleOption( isAdd ) {
		const multiple_label = this.settings.strings.multiple;

		if ( ! this.$tagSelect.length ) {
			return;
		} else if ( isAdd && ! this.$tagSelect.children( '[value = multiple]' ).length ) {
			this.$tagSelect.append( $( '<option>', {
				value: 'multiple',
				text: multiple_label
			} ) );
		} else if ( ! isAdd && this.$tagSelect.children( '[value = multiple]' ).length ) {
			this.$tagSelect.children( '[value = multiple]' ).remove();
		}

		this.$tagSelect.selecter( 'update' );
	}

	requestSearchResults( queries, recenterOnBlank ) {
		const _this = this;
		
		if ( queries.length === 0 ) {
			return _this.filter( 'search', pin => true )
				.then( function() {
					if ( recenterOnBlank ) {
						return _this.mapService.setCenter( _this.settings.map.center );
					}
					return Promise.resolve();
				} );
		}

		// close the 'search not found' modal
		if ( 'undefined' !== typeof _this.searchTooltip ) {
			_this.searchTooltip.hide();
		}

		_this.searchLastResult = null;		

		if ( _this.settings.map.mode === 'image' ) {			
			return _this.requestSearchResultsImageMode(queries);
		}
		
		return _this.requestSearchResultsMapMode(queries);
	}

	// search results for Mapify map image mode
	requestSearchResultsImageMode( queries ) {
		const _this     = this;
		let pinsResults = [];
		let promises    = [];

		for ( let index = 0; index < queries.length; index++ ) {
			const query = queries[index];
			
			// add results from the title search
			promises.push(
				_this.searchPinsByVariable( query, 'title' )
					.then( function( results ) {				
						if ( results.length ) {
							pinsResults.push( results );
						}
					} )
			);

			// add results from the city search
			promises.push(
				_this.searchPinsByVariable( query, 'city' )
					.then( function( results ) {				
						if ( results.length ) {
							pinsResults.push( results );
						}
					} )
			);

			// add results from the zip search
			promises.push(
				_this.searchPinsByVariable( query, 'zip' )
					.then( function( results ) {				
						if ( results.length ) {
							pinsResults.push( results );
						}
					} )
			);
		}

		return Promise.all( promises )
			.then(() => {
				_this.filter( 'search', pin => ( pinsResults.indexOf( pin ) !== -1 ) )
					.then( function() {
						if ( _this.getVisiblePins().length === 0 ) {
							utils.showSearchPopup( _this, _this.getAttrsData('no_search_results') );
						}
					} )
					.catch( function( error ) {
						console.log( error );
					} )
					.then( function() {
						_this.$searchForm.addClass('mpfy-search-has-results');
						return Promise.resolve();
					} );
			})
			.catch((e) => {
				return Promise.reject( 'unexpected error' );
			});
	}

	// search results for the Mapify map default mode
	requestSearchResultsMapMode( queries ) {
		const _this             = this;
		let pinsResults         = [];
		let variablePinsResults = [];
		let promises            = [];

		this.clearCircleRadius();

		for ( let index = 0; index < queries.length; index++ ) {
			const query = queries[index];

			// add results from the title search
			promises.push(
				_this.searchPinsByVariable( query, 'title' )
					.then( function( results ) {
						if ( results.length ) {
							variablePinsResults.push( results );
						}
					} )
			);

			// add results from the address / post-code search
			promises.push(
				_this.mapService.geocode( query, _this.settings.search.regionBias )
					.then( function( results ) {
						if ( results.length === 0 ) {					
							return;
						}

						var result = results[0];
						var filteredPins = _this.mapService.getPinsWithinRange( result.lat, result.lng, _this.settings.search.radiusInMeters );
						
						pinsResults.push( filteredPins );
						
						_this.searchLastResult = result;
						_this.setCircleRadius( [ result.lat, result.lng ] );
					} )
			);
		}

		return Promise.all( promises )
			.then(() => {
				// no results found
				if ( ! pinsResults.length ) {
					utils.showSearchPopup( _this, _this.getAttrsData('search_geolocation_failure') );
					return Promise.resolve();
				}

				let combinedPinsResults = [];

				// whether the results using union (any_within) or intersection (all_within) method
				if ( 'all_within' === this.$searchRadiusSelectType.val() && 1 < queries.length ) {
					combinedPinsResults = pinsResults.reduce((a, c) => a.filter(i => c.includes(i)));
				} else {
					pinsResults.forEach(pinsResult => {
						combinedPinsResults = combinedPinsResults.concat( pinsResult );
					});

					// combine with the variable results
					variablePinsResults.forEach(variablePinsResult => {
						combinedPinsResults = combinedPinsResults.concat( variablePinsResult );
					});	
				}				

				return _this.filter( 'search', pin => ( combinedPinsResults.indexOf( pin ) !== -1 ) )
					.then( function() {
						if ( ! _this.getVisiblePins().length ) {
							_this.searchLastClosestPin = _this.mapService.getPinClosestTo( _this.searchLastResult.lat, _this.searchLastResult.lng );
							utils.showSearchPopup( _this, _this.getAttrsData('no_search_results_with_closest') );
						}
					} )
					.catch( function( error ) {
						console.log( error );
					} )
					.then( function() {
						_this.$searchForm.addClass('mpfy-search-has-results');						
						return Promise.resolve();
					} );
			})
			.catch((e) => {
				return Promise.reject( 'unexpected error' );
			});
	}

	search( queries, recenterOnBlank ) {
		const _this = this;

		if ( _this.searchIsSearching ) {
			return Promise.resolve();
		}
		
		// set loading
		_this.$searchForm.addClass('mpfy-search-loading');
		_this.searchIsSearching = true;

		return _this.requestSearchResults( queries, recenterOnBlank )
			.then( function() {
				var visiblePins = _this.getVisiblePins();
				if ( visiblePins.length > 0 && _this.settings.search.centerOnSearch ) {
					return _this.mapService.fitPins( visiblePins );
				}
				return Promise.resolve();
			} )
			.then( function() {
				_this.$container.trigger( 'mapify.search.ended', {
					'queries': queries
				} );
				return Promise.resolve();
			} )
			.catch( function( error ) {
				if ( error !== 'busy' ) {
					return Promise.reject( error );
				}
			} )
			.then( function() {				
				// unset loading
				_this.$searchForm.removeClass('mpfy-search-loading');
				_this.searchIsSearching = false;
			} );
	}

	searchPinsByVariable( query, varName, useGeoLocation = false ) {
		const _this   = this;
		const radius  = this.settings.search.radiusInMeters;
		var varName   = ( typeof varName == 'undefined') ? 'title' : varName;
		var query     = query.replace( /[^0-9A-Z]+/gi, "" ).toLowerCase();
		var pins      = this.pins;
		var foundPins = [];

		// filter pins by location or not
		return new Promise( ( resolve ) => {
			var result = {
				type : 'nogeolocation'
			};
			
			if ( useGeoLocation ) {
				result = this.mapService.locateWithoutFocus();
			}

			resolve( result );
		})
		.then( function( result ) {			
			if ( 'locationfound' === result.type ) {
				pins = _this.mapService.getPinsWithinRange( result.latitude, result.longitude, radius );

				// show user's location
				_this.setGeolocationCircleRadius( result.latlng );
			}
		} )
		.catch( function( error ) {
			console.log( error );
		} )
		.then( function() {
			// search pins by variable
			pins.forEach( pin => {
				let variable = pin.model[ varName ];
					variable = variable.replace( /[^0-9A-Z]+/gi, "" ).toLowerCase();
					
				if ( -1 !== variable.indexOf( query ) ) {
					foundPins.push( pin );
				}
			} );

			return foundPins;
		} )
	}

	clearCircleRadius() {
		if ( Array.isArray( this.circleRadius ) ) {
			this.circleRadius.forEach( each => {
				this.mapService.map.removeLayer( each.inner );
				this.mapService.map.removeLayer( each.outer );
			} );

			this.circleRadius = null;
		}
	}

	setCircleRadius( latLng ) {
		const _this     = this;
		const radius    = this.settings.search.radiusInMeters;
		const newCircle = {
			inner: L.circleMarker( latLng, { radius: 5, color: this.settings.search.locationCircleColor } ).addTo( _this.mapService.map ),
			outer: L.circle( latLng, { radius: radius, color: this.settings.search.locationCircleColor } ).addTo( _this.mapService.map ),
		};
		
		if ( Array.isArray( this.circleRadius ) ) {
			this.circleRadius.push( newCircle )
		} else {
			this.circleRadius = [ newCircle ];
		}
	
	}

	clearGeolocationCircleRadius() {
		if ( this.geolocationCircleRadius ) {
			this.mapService.map.removeLayer( this.geolocationCircleRadius.inner );
			this.mapService.map.removeLayer( this.geolocationCircleRadius.outer );
		}
	}

	setGeolocationCircleRadius( latLng ) {
		const _this     = this;
		const radius    = this.settings.search.radiusInMeters;

		this.clearGeolocationCircleRadius();

		this.geolocationCircleRadius = {
			inner: L.circleMarker( latLng, { radius: 5, color: this.settings.search.locationCircleColor } ).addTo( _this.mapService.map ),
			outer: L.circle( latLng, { radius: radius, color: this.settings.search.locationCircleColor } ).addTo( _this.mapService.map ),
		};
	}

	clearSearch( recenterOnBlank ) {		
		recenterOnBlank = ( typeof recenterOnBlank == 'undefined') ? true : recenterOnBlank;
		this.$searchFormInput.val( '' ).trigger( 'change' );
		return this.search( '', recenterOnBlank );
	}
}

module.exports = Map;
