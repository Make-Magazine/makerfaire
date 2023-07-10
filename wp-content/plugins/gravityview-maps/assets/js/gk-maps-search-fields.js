window.gravitykit = window.gravitykit || {};
window.gravitykit.maps = window.gravitykit.maps || {};
window.gravitykit.maps.searchFields = window.gravitykit.maps.searchFields || {};

( ( $, obj, GravityMaps ) => {
	"use strict";

	// Create a global Reference for this globally.
	GravityMaps.searchFields = obj;

	// Run some magic to allow a better handling of class names for jQuery.hasClass type of methods
	obj.className = ( value ) => {
		// Prevent Non Strings to be included
		if (
			(
				'string' !== typeof value
				&& ! this instanceof String
			)
			|| 'function' !== typeof value.replace
		) {
			return value;
		}

		return value.replace( '.', '' );
	};

	/**
	 * Object holding all the selectors used for this module.
	 *
	 * @since 2.2
	 *
	 * @type {{currentLocationButton: string, currentLocationLat: string, currentLocationAccuracy: string, currentLocationLng: string}}
	 */
	obj.selectors = {
		currentLocationButton: '.gk-maps-search-current-geolocation',
		searchBox: '.gv-search-box',
		searchField: '.gk-maps-search-geolocation-address-autocomplete',
		searchFieldCurrentLocation: '.gk-maps-search-geolocation-address-autocomplete-current-location',
		currentLocationButtonActive: '.gk-maps-search-current-geolocation-active',
		currentLocationLng: '.gk-maps-search-geolocation-lng',
		currentLocationLat: '.gk-maps-search-geolocation-lat',
		currentLocationAccuracy: '.gk-maps-search-current-geolocation-accuracy',
		currentLocationFlag: '.gk-maps-search-current-geolocation-flag',
		currentLocationRadius: '.gk-maps-search-current-geolocation-radius',
		currentLocationUnit: '.gk-maps-search-current-geolocation-unit',
		currentLocationHasSearch: '.gk-maps-search-current-geolocation-has-search',

		viewContainer: '.gv-container',
		viewWrapper: `[id^="gv-view-"]`,
		viewWrapperById: ( id ) => `[id^="gv-view-${id}-"]`,
		viewContainerById: ( id ) => `${obj.selectors.viewContainer}-${id}`,

		mapsEntriesContainer: '.gv-map-entries',
		widgetsHeader: '.gv-widgets-header',
		noResults: '.gv-no-results',
	};

	/**
	 * The jQuery object holding the current button for currentLocation.
	 *
	 * @since 2.2
	 *
	 * @type {null|jQuery}
	 */
	obj.$currentLocationButton = null;

	/**
	 * Internationalization for teh Maps Search fields JS.
	 *
	 * @since 2.2
	 *
	 * @type {object}
	 */
	obj.i18n = window.gk_maps_search_fields;

	/**
	 * Pull the current location and saves on a hidden field in the search form.
	 *
	 * @since 2.2
	 *
	 * @param {Event} event
	 *
	 * @return {void}
	 */
	obj.onCurrentLocationClick = ( event ) => {
		event.preventDefault();

		const $target = $( event.target );
		const options = {
			enableHighAccuracy: true,
			timeout: 5000,
			maximumAge: 0
		};

		if ( $target.is( obj.selectors.currentLocationButton ) ) {
			obj.$currentLocationButton = $target;
		} else {
			obj.$currentLocationButton = $( event.target ).parents( obj.selectors.currentLocationButton ).eq( 0 );
		}

		navigator.geolocation.getCurrentPosition( obj.handleCurrentLocationSuccess, obj.handleCurrentLocationError, options );
	};

	obj.resetCurrentLocation = ( $field ) => {
		const $searchBox = $field.parents( obj.selectors.searchBox ).eq( 0 );

		$searchBox.find( obj.selectors.currentLocationButton )
			.removeClass( obj.className( obj.selectors.currentLocationButtonActive ) )
			.prop( 'title', obj.i18n.currentLocationLabel );

		$searchBox.find( obj.selectors.currentLocationFlag ).val( 0 );
	};

	/**
	 * Handles saving the current location on the fields related to the button that was clicked.
	 *
	 * @since 2.2
	 *
	 * @param {Object} position
	 *
	 * @return {void}
	 */
	obj.handleCurrentLocationSuccess = ( position ) => {
		const coordinates = position.coords;
		const $searchBox = obj.$currentLocationButton.parents( obj.selectors.searchBox );
		const $latField = $searchBox.find( obj.selectors.currentLocationLat );
		const $lngField = $searchBox.find( obj.selectors.currentLocationLng );
		const $accuracyField = $searchBox.find( obj.selectors.currentLocationAccuracy );
		const $currentFlagField = $searchBox.find( obj.selectors.currentLocationFlag );
		const $searchField = $searchBox.find( obj.selectors.searchField );

		if ( ! obj.isValidCoordinates( coordinates ) ) {
			window.GV_MAPS.hooks.doAction( 'gk.maps.invalid_map_coordinates', coordinates );
			return;
		}

		$latField.val( coordinates.latitude );
		$lngField.val( coordinates.longitude );
		$accuracyField.val( coordinates.accuracy );
		$currentFlagField.val( 1 );

		obj.$currentLocationButton
			.addClass( obj.className( obj.selectors.currentLocationButtonActive ) )
			.prop( 'title', obj.i18n.currentLocationLabel );


		if ( obj.$currentLocationButton.data( 'gkCurrentLocationInstantSearch' ) ) {
			obj.$currentLocationButton.parents( 'form' ).eq( 0 ).trigger( 'submit' );
		}

		obj.setupCurrentLocationFieldInteractions( $searchField );
	};

	/**
	 * Displays an error using the default gvMaps error for when invalid Coordinates were passed.
	 *
	 * @since 2.2
	 *
	 * @param {object} coordinates
	 */
	obj.throwInvalidCoordinatesError = ( coordinates ) => {
		gvMapsDisplayErrorNotice( obj.i18n.invalidGeolocationCoordinates );
	};

	/**
	 * Determines if a given object is valid as coordinates.
	 *
	 * @since 2.2
	 *
	 * @param {object} coordinates
	 *
	 * @return {boolean}
	 */
	obj.isValidCoordinates = ( coordinates ) => {
		if ( typeof coordinates !== 'object' ) {
			return false;
		}

		// This will also invalidate 0:0 coordinates on error.
		if ( ! coordinates.latitude && ! coordinates.longitude ) {
			return false;
		}

		return true;
	};

	obj.setupCurrentLocationFieldInteractions = ( $searchField ) => {
		$searchField.each( ( index, element ) => {
			const $field = $( element );
			const $currentButton = $field.siblings( obj.selectors.currentLocationButton );
			if ( ! $currentButton.is( obj.selectors.currentLocationButtonActive ) ) {
				return;
			}

			$field.data( 'currentPlaceholder', $field.attr( 'placeholder' ) );
			$field.attr( 'placeholder', obj.i18n.currentLocationLabel ).addClass( obj.className( obj.selectors.searchFieldCurrentLocation ) );
			$field.val( '' );

			const removeContentsCallback = () => {
				$field.attr( 'placeholder', $field.data( 'currentPlaceholder' ) ).removeClass( obj.className( obj.selectors.searchFieldCurrentLocation ) );
				$field.off( 'focusout.gkMapsSearchField' ).one( 'focusout.gkMapsSearchField', () => {
					$field.attr( 'placeholder', obj.i18n.currentLocationLabel ).addClass( obj.className( obj.selectors.searchFieldCurrentLocation ) );
				} );
				$field.off( 'mousedown.gkMapsSearchField' ).one( 'mousedown.gkMapsSearchField', removeContentsCallback );

			};
			$field.off( 'mousedown.gkMapsSearchField' ).one( 'mousedown.gkMapsSearchField', removeContentsCallback );
		} );
	};

	/**
	 * Returns the options selected currently on the fields for radius search.
	 *
	 * @todo Determine if there a localized way of getting these fields.
	 *
	 * @since 2.2
	 *
	 * @return {{unit: string, accuracy: string, radius: number, long: number, lat: number, hasSearch: boolean}}
	 */
	obj.getCurrentLocationOptions = () => {
		const options = {
			long: parseFloat( $( obj.selectors.currentLocationLng ).val() ),
			lat: parseFloat( $( obj.selectors.currentLocationLat ).val() ),
			radius: parseFloat( $( obj.selectors.currentLocationRadius ).val() ),
			unit: $( obj.selectors.currentLocationUnit ).val(),
			accuracy: $( obj.selectors.currentLocationAccuracy ).val(),
			hasSearch: Boolean( Number( $( obj.selectors.currentLocationHasSearch ).val() ) ),
		};

		return options;
	};

	/**
	 * Throws a error to the user that we couldn't get the coordinates from the browser.
	 *
	 * @todo Error handling need to be visible to the user.
	 *
	 * @since 2.2
	 *
	 * @param {Object} error
	 *
	 * @return {void}
	 */
	obj.handleCurrentLocationError = ( error ) => {

		obj.$currentLocationButton.prop( 'title', obj.i18n.currentLocationTitle );

		confirm( obj.i18n.geolocationCurrentLocationError );

		console.warn( `ERROR(${error.code}): ${error.message}` );
	};

	/**
	 * Cache all the maps used to avoid re-selecting using jQuery.
	 *
	 * @type {{}}
	 */
	obj.$maps = {};

	/**
	 * Gets the jQuery object based on the map object from Google API.
	 *
	 * @since 2.2
	 *
	 * @param map
	 *
	 * @returns {jQuery}
	 */
	obj.getMap = ( map ) => {
		const mapDiv = map.getDiv();
		if ( typeof obj.$maps[ mapDiv.id ] === 'undefined' ) {
			obj.$maps[ mapDiv.id ] = $( mapDiv );
		}

		return obj.$maps[ mapDiv.id ];
	};

	/**
	 * Determine if a given map can have a search included.
	 *
	 * @param {Element} map
	 *
	 * @returns {boolean}
	 */
	obj.shouldSearchOnMove = ( map ) => {
		const $map = obj.getMap( map );

		if ( ! $map.hasClass( 'gk-multi-entry-map' ) ) {
			return false;
		}

		return $map.find( '.gk-maps-search-checkbox' ).is( ':checked' );
	};

	/**
	 * Generates a UUID.
	 *
	 * @since 2.2
	 *
	 * @returns {string}
	 */
	obj.uuid = () => {
		let d = new Date().getTime();
		const uuid = 'xxxxxxxx-xxxx-xxxx-yxxx-xxxxxxxxxxxx'.replace( /[xy]/g, ( c ) => {
			const r = ( d + Math.random() * 16 ) % 16 | 0;
			d = Math.floor( d / 16 );
			return ( c == 'x' ? r : ( r & 0x3 | 0x8 ) ).toString( 16 );
		} );
		return uuid;
	};

	/**
	 * Includes a Search box on Google Maps UI with some actions.
	 *
	 * @param {jQuery} $maps
	 */
	obj.includeSearchOnMoveBox = ( $maps ) => {
		$maps.each( ( index, map ) => {
			const $map = $( map );
			const data = $map.data( 'gkMap' );

			// in case we cant find the search map.
			if ( ! $map.hasClass( 'gk-multi-entry-map' ) ) {
				return;
			}

			let hasBox = false;

			// If we already have the box for search on move drawn.
			data.map.controls[ google.maps.ControlPosition.TOP_RIGHT ].forEach( ( element ) => {
				if ( ! $( element ).hasClass( 'gk-maps-element-search' ) && ! $( element ).hasClass( 'gk-maps-element-redo-search' ) ) {
					return;
				}
				hasBox = true;
			} );

			if ( hasBox ) {
				return;
			}

			const toggleUuid = obj.uuid();
			const $toggleButton = $( `<label class="gk-maps-element gk-maps-element-search" for="gk-maps-search-${toggleUuid}"><input type="checkbox" class="gk-maps-search-checkbox" id="gk-maps-search-${toggleUuid}"> ${obj.i18n.searchOnMoveLabel}</label>` );
			const redoUuid = obj.uuid();
			const $redoSearch = $( `<button class="gk-maps-element gk-maps-element-redo-search" id="gk-maps-redo-search-${redoUuid}">${obj.i18n.redoSearchLabel}</button>` );

			$toggleButton.on( 'click', () => obj.toggleSearchOnMove( data.map ) );
			$redoSearch.on( 'click', () => {
				const onCompleteCallback = () => {
					data.map.controls[ google.maps.ControlPosition.TOP_RIGHT ].clear();
					data.map.controls[ google.maps.ControlPosition.TOP_RIGHT ].push( $toggleButton[ 0 ] );

					// Since the button is the same we need to re-enable once completed.
					$redoSearch.prop( 'disabled', false );
				};

				// Make sure you cannot click on this button while it loads.
				$redoSearch.prop( 'disabled', true );

				obj.triggerSearchOnMove( data.map, onCompleteCallback );
			} );
			data.map.controls[ google.maps.ControlPosition.TOP_RIGHT ].push( $toggleButton[ 0 ] );

			google.maps.event.addListenerOnce( data.map, 'idle', () => {
				google.maps.event.addListener( data.map, 'bounds_changed', () => {
					$map.addClass( 'gk-map-moved' );

					let boundsChanged = $map.data( 'gkBoundsChange' );
					if ( 'undefined' === typeof boundsChanged ) {
						boundsChanged = false;
					}

					if ( boundsChanged === true ) {
						return;
					}

					if ( ! obj.shouldSearchOnMove( data.map ) ) {
						data.map.controls[ google.maps.ControlPosition.TOP_RIGHT ].clear();
						data.map.controls[ google.maps.ControlPosition.TOP_RIGHT ].push( $redoSearch[ 0 ] );

						return;
					}

					$map.data( 'gkBoundsChange', true );

					setTimeout( () => {
						google.maps.event.addListenerOnce( data.map, 'idle', () => {
							$map.data( 'gkBoundsChange', false );
							obj.triggerSearchOnMove( data.map );
						} );
					}, 300 );
				} );
			} );
		} );
	};

	/**
	 * Handles the Search inside a set of bounds.
	 *
	 * @since 2.2
	 *
	 * @param {Element} map
	 * @param {function|null} onCompleteCallback
	 */
	obj.triggerSearchOnMove = ( map, onCompleteCallback = null ) => {
		const $map = obj.getMap( map );
		const mapData = $map.data( 'gkMap' );
		$map.addClass( 'gk-multi-entry-map-avoid-rebound' );

		const bounds = map.getBounds();
		const ne = bounds.getNorthEast();
		const sw = bounds.getSouthWest();

		const data = {
			bounds: {
				max_lat: ne.lat(),
				min_lat: sw.lat(),
				min_lng: ne.lng(),
				max_lng: sw.lng(),
			},
			filter_geolocation: 1,
		};

		$.ajax( {
			url: obj.getRestURL( map ),
			data: data,
			accepts: 'json',
			dataType: 'json',
			method: 'GET',
			'async': true, // async is keyword
			beforeSend: ( jqXHR, settings ) => obj.ajaxBeforeSend( jqXHR, settings, map ),
			complete: ( jqXHR, textStatus ) => {
				obj.ajaxComplete( jqXHR, textStatus, map );

				if ( ! mapData.is_multi_entry_map ) {
					$map.removeClass( 'gk-multi-entry-map-avoid-rebound' );
				}

				if ( onCompleteCallback ) {
					onCompleteCallback();
				}
			},
			success: ( data, textStatus, jqXHR ) => obj.ajaxSuccess( data, textStatus, jqXHR, map ),
			error: ( jqXHR, settings ) => obj.ajaxError( jqXHR, settings, map ),
			context: $map,
		} );
	};

	/**
	 * Triggered on jQuery.ajax() beforeSend action, which we hook into to replace the contents of the modal with a
	 * loading HTML, as well as trigger a before and after hook so third-party developers can always extend all
	 * requests.
	 *
	 * @since 2.2
	 *
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {Object} settings Settings that this request will be made with
	 *
	 * @return {void}
	 */
	obj.ajaxBeforeSend = ( jqXHR, settings, map ) => {
		obj.insertLoader( map );
		var $loader = $( document ).find( '.gk-maps-loader' );

		$( document ).trigger( 'beforeAjaxBeforeSend.Search/GravityMaps/GK', [ jqXHR, settings, map ] );

		if ( $loader.length ) {
			$loader.removeClass( 'gk-maps-loader-hidden' );
		}

		$( document ).trigger( 'afterAjaxBeforeSend.Search/GravityMaps/GK', [ jqXHR, settings, map ] );
	};

	obj.insertLoader = ( map ) => {

	};

	/**
	 * Triggered on jQuery.ajax() complete action, which we hook into to reset appropriate variables and remove the
	 * loading HTML, as well as trigger a before and after hook so third-party developers can always extend all requests
	 *
	 * @since 2.2
	 *
	 * @param  {jqXHR}  jqXHR       Request object
	 * @param  {String} textStatus Status for the request
	 *
	 * @return {void}
	 */
	obj.ajaxComplete = ( jqXHR, textStatus, map ) => {
		var $loader = $( document ).find( '.gk-maps-loader' );

		$( document ).trigger( 'beforeAjaxComplete.Search/GravityMaps/GK', [ jqXHR, textStatus, map ] );

		if ( $loader.length ) {
			$loader.addClass( 'gk-maps-loader-hidden' );
		}

		$( document ).trigger( 'afterAjaxComplete.Search/GravityMaps/GK', [ jqXHR, textStatus, map ] );

		// Reset the current ajax request on the manager object.
		obj.currentAjaxRequest = null;
	};

	/**
	 * Triggered on jQuery.ajax() success action, which we hook into to replace the contents of the modal, as well as
	 * trigger a before and after hook so third-party developers can always extend all requests
	 *
	 * @since 2.2
	 *
	 * @param  {String} data       HTML sent from the AJAX request.
	 * @param  {String} textStatus Status for the request.
	 * @param  {jqXHR}  jqXHR      Request object.
	 *
	 * @return {void}
	 */
	obj.ajaxSuccess = ( html, textStatus, jqXHR, map ) => {
		$( document ).trigger( 'beforeAjaxSuccess.Search/GravityMaps/GK', [ html, textStatus, jqXHR, map ] );

		const $map = obj.getMap( map );
		const originalData = $map.data( 'gkMap' );

		if ( originalData.radiusSearch && originalData.radiusMarker ) {
			originalData.radiusSearch.setMap( null );
			originalData.radiusMarker.setMap( null );
		}

		let $response = $( html );

		const $wrapper = obj.getViewWrapper( $map );
		const $container = obj.getResultsContainer( $map );
		const viewType = obj.getViewType( $container );
		const observer = window.GV_MAPS.getObserver();

		$container.find( '.gv-map-canvas' ).each( ( index, map ) => observer.unobserve( map ) );

		// Prevents existent maps to be redrawn.
		$response = obj.keepGeneratedMaps( viewType, $wrapper, $response );

		$container.replaceWith( obj.getResponseContainer( $response, originalData ) );

		const $dataScript = $response.filter( '[data-js="gk-view-data"]' );
		if ( $dataScript.length ) {
			const data = JSON.parse( $dataScript.text() );
			window.GV_MAPS.MapOptions = data.MapOptions;
		}

		window.GV_MAPS.maps = [];
		window.GV_MAPS.markers_info = [];
		window.GV_MAPS.setup_map_options();
		window.GV_MAPS.initMaps( $wrapper );

		$( document ).trigger( 'afterAjaxSuccess.Search/GravityMaps/GK', [ html, textStatus, jqXHR, map ] );
	};

	obj.getNoResultsContainer = ( $response ) => {
		return $response.find( obj.selectors.noResults );
	};

	obj.hasNoResults = ( $response ) => {
		return !! obj.getNoResultsContainer( $response ).length;
	};

	obj.getResponseContainer = ( $response, originalData ) => {
		const $wrapper = $response.filter( obj.selectors.viewWrapper );
		return $wrapper.find( obj.selectors.viewContainerById( originalData.view_id ) );
	};

	obj.getViewWrapper = ( $map ) => {
		const data = $map.data( 'gkMap' );
		return $map.parents( obj.selectors.viewWrapperById( data.view_id ) ).eq( 0 );
	};

	obj.getResultsContainer = ( $map ) => {
		const data = $map.data( 'gkMap' );
		const $wrapper = obj.getViewWrapper( $map );
		return $wrapper.find( obj.selectors.viewContainerById( data.view_id ) );
	};

	obj.getViewType = ( $container ) => {
		if ( $container.hasClass( 'gv-map-container' ) ) {
			return 'map';
		}

		if ( $container.hasClass( 'gv-list-container' ) ) {
			return 'list'
		}

		if ( $container.hasClass( 'gv-table-container' ) ) {
			return 'table'
		}

		return 'other'
	};

	/**
	 * Look at the given container and make sure any Maps that already exist don't get regenerated.
	 *
	 * @since 2.2
	 *
	 * @param {string} viewType
	 * @param {jQuery} $mapsContainer
	 * @param {jQuery} $response
	 */
	obj.keepGeneratedMaps = ( viewType, $mapsContainer, $response ) => {
		window.GV_MAPS.getMaps( $mapsContainer, true ).each( ( index, mapEl ) => {
			const $map = $( mapEl );
			const mapData = $map.data( 'gkMap' );

			$map.removeClass( 'gk-map-markers-generated' );

			// Hide all markers for now.
			mapData.markers.forEach( marker => {
				marker.set( 'gkVisible', false );
				marker.setVisible( false );
			} );

			const $replacementMap = $response.find( `.gk-map-canvas-${mapData.entry_id}` );

			// New map doesnt exist.
			if ( ! $replacementMap.length ) {
				if ( obj.hasNoResults( $response ) && mapData.is_multi_entry_map ) {
					obj.getNoResultsContainer( $response ).before( $map );
					mapData.markers_data = [];
				}
				return $response;
			}

			const receivedData = $replacementMap.data( 'gkMap' );

			// Overwrite the markers data with the new Markers data.
			mapData.markers_data = receivedData.markers_data;

			if ( ! mapData.is_multi_entry_map || viewType === 'map' ) {
				$replacementMap.replaceWith( $map );
			}

			// Manually process the markers for the visible search maps.
			if ( mapData.is_multi_entry_map ) {
				window.GV_MAPS.processMapMarker( $map );
			}
		} );

		return $response;
	}

	/**
	 * Triggered on jQuery.ajax() error action, which we hook into to close the modal for now, as well as
	 * trigger a before and after hook so third-party developers can always extend all requests
	 *
	 * @since 1.0
	 *
	 * @param  {jqXHR}  jqXHR    Request object.
	 * @param  {Object} settings Settings that this request was made with.
	 *
	 * @return {void}
	 */
	obj.ajaxError = ( jqXHR, settings, map ) => {
		$( document ).trigger( 'beforeAjaxError.Search/GravityMaps/GK', [ jqXHR, settings, map ] );

		/**
		 * @todo  we need to handle errors here
		 */

		$( document ).trigger( 'afterAjaxError.Search/GravityMaps/GK', [ jqXHR, settings, map ] );
	};

	/**
	 * Get the REST API based on the PHP sent variables.
	 *
	 * @since 2.2
	 *
	 * @param {Element} map
	 *
	 * @returns {string}
	 */
	obj.getRestURL = ( map ) => {
		const $map = obj.getMap( map );
		const data = $map.data( 'gkMap' );
		let limit = 0;
		if ( data.page_size ) {
			limit = data.page_size;
		}

		return obj.i18n.searchRestEndpoint.replace( '{view_id}', data.view_id ).replace( '{limit}', limit );
	};

	obj.toggleSearchOnMove = ( map ) => {
		const $map = obj.getMap( map );
		const shouldDisplay = ! $map.find( '.gk-maps-element-search' ).find( '.gk-maps-search-checkbox' ).is( ':checked' );

		if ( ! shouldDisplay ) {
			$map.addClass( 'gk-multi-entry-map-on-move' );
		} else {
			$map.removeClass( 'gk-multi-entry-map-on-move' );
		}

		const radiusSearch = $map.data( 'gkRadiusSearch' );
		const radiusMarker = $map.data( 'gkRadiusMarker' );

		if ( radiusSearch ) {
			radiusSearch.setVisible( shouldDisplay );
		}
		if ( radiusMarker ) {
			radiusMarker.setVisible( shouldDisplay );
		}
	};

	/**
	 * Handles adding the bounds circle and a center marker.
	 *
	 * This method will wait until the map is idle to draw.
	 *
	 * @since 2.2
	 *
	 * @param {jQuery} $map
	 * @param {object} data
	 * @param {object} module
	 *
	 * @return {void}
	 */
	obj.drawRadiusOnMap = ( $map, data, module ) => {
		const options = obj.getCurrentLocationOptions();

		// Only do anything if we have a Geolocation Search.
		if ( ! options.hasSearch ) {
			return;
		}

		if ( ! $map.hasClass( 'gk-multi-entry-map' ) ) {
			return;
		}

		if ( $map.hasClass( 'gk-multi-entry-map-done' ) ) {
			return;
		}

		// When dealing with Null Island don't do anything, we are using == intentionally instead of ===.
		if ( options.lat == 0 && options.long == 0 ) {
			return;
		}

		$map.addClass( 'gk-multi-entry-map-done' );

		obj.includeSearchOnMoveBox( $map );

		google.maps.event.addListenerOnce( data.map, 'idle', () => {
			$( document ).trigger( 'beforeRadiusDraw.GravityKit/Maps', [ data.map, module ] );

			if ( 'mi' === options.unit ) {
				options.radius = obj.mileToKm( options.radius );
			}

			const radiusSearch = new google.maps.Circle( {
				strokeColor: "#FF0000",
				strokeOpacity: 0.6,
				strokeWeight: 1,
				fillColor: "#FF0000",
				fillOpacity: 0.17,
				map: data.map,
				center: {
					lat: options.lat,
					lng: options.long,
				},
				radius: options.radius * 1000,
				// draggable: true,
				// editable: true,
			} );

			data.radiusSearch = radiusSearch;
			const radiusMarker = new google.maps.Marker( {
				position: {
					lat: options.lat,
					lng: options.long,
				},
				// draggable: true,
				icon: {
					url: obj.i18n.geolocationFenceCenterImage,
					size: new google.maps.Size( 7, 7 ),
					anchor: new google.maps.Point( 4, 4 )
				},
				map: data.map
			} );
			data.radiusMarker = radiusMarker;

			radiusSearch.bindTo( 'center', radiusMarker, 'position' );

			$map.data( 'gkMap', data );

			setTimeout( () => {
				data.map.fitBounds( radiusSearch.getBounds(), 10 );
			}, 300 );

			$( document ).trigger( 'afterRadiusDraw.GravityKit/Maps', [ data.map, radiusSearch, module ] );
		} );
	};

	/**
	 * Calculate KM from a miles numeric value.
	 *
	 * @since 2.2
	 *
	 * @param {number} miles
	 *
	 * @return {number}
	 */
	obj.mileToKm = ( miles ) => {
		return miles * 1.609344;
	};

	/**
	 * Triggers when Ready of the document.
	 *
	 * @since 2.2
	 *
	 * @return {void}
	 */
	obj.ready = () => {
		if ( ! window.GV_MAPS.can_use_rest ) {
			return;
		}

		obj.setupCurrentLocationFieldInteractions( $( obj.selectors.searchField ) );

		window.GV_MAPS.hooks.addAction( 'gk.maps.after_process_map_markers', 'gravitykit/maps', obj.drawRadiusOnMap );
		window.GV_MAPS.hooks.addAction( 'gk.maps.after_process_map_markers', 'gravitykit/maps', obj.includeSearchOnMoveBox );
		window.GV_MAPS.hooks.addAction( 'gk.maps.invalid_map_coordinates', 'gravitykit/maps', obj.throwInvalidCoordinatesError )
	};

	$( document ).on( 'click', obj.selectors.currentLocationButton, obj.onCurrentLocationClick );

	$( document ).ready( obj.ready );
} )( jQuery, {}, window.GravityKit.GravityMaps );

