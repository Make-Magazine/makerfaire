(function ($) {

    var DyncontEl_GoogleMapsHandler = function ($scope, $) {
        var $map = $scope.find('.map');
		var map = $map[0];
        var bounds;

		// Positions
		let positions = $map.data('positions') || [];

		// Exit if the maps doesn't have positions
		if( ! positions.length ) {
			return;
		}

		var latitude = parseFloat( positions[0].lat ) || 0;
		var longitude = parseFloat( positions[0].lng ) || 0;
        var lastWindow = null;
		var elementSettingsMap = dceGetElementSettings($scope);
        var markerWidth = elementSettingsMap.marker_width || 0;
        var markerHeight = elementSettingsMap.marker_height || 0;
		var zoom = $map.data('zoom') || 10;
		var imageMarker = positions[0].marker || '';

        if (markerWidth && markerHeight && imageMarker) {
			imageMarker = {
				url: imageMarker,
				scaledSize: new google.maps.Size(markerWidth, markerHeight),
			};
        }

		// Map Parameters
        var mapParams = {
			zoom: zoom,
			scrollwheel: Boolean( elementSettingsMap.prevent_scroll ),
			mapTypeControl: Boolean( elementSettingsMap.maptypecontrol ),
			panControl: Boolean( elementSettingsMap.pancontrol ),
			rotateControl: Boolean( elementSettingsMap.rotaterontrol ),
			scaleControl: Boolean( elementSettingsMap.scalecontrol ),
			streetViewControl: Boolean( elementSettingsMap.streetviewcontrol ),
			zoomControl: Boolean( elementSettingsMap.zoomcontrol ),
			fullscreenControl: Boolean( elementSettingsMap.fullscreenControl ),
			center: {
				lat: latitude,
				lng: longitude,
			},
        };

		// Map Type (Roadmap, satellite, etc.)
        if (elementSettingsMap.map_type && elementSettingsMap.map_type !== "acfmap") {
          	mapParams['mapTypeId'] = elementSettingsMap.map_type;
        }

		// Zoom Minimum and Maximum
		if (elementSettingsMap.zoom_custom ) {
			minZoom = elementSettingsMap.zoom_minimum.size || 0;
			maxZoom = elementSettingsMap.zoom_maximum.size || 20;
			if( minZoom > maxZoom ) {
				minZoom = maxZoom;
			}
			mapParams['minZoom'] = minZoom;
			mapParams['maxZoom'] = maxZoom;
		}

        if (elementSettingsMap.style_select === 'custom') {
			mapParams['styles'] = eval(elementSettingsMap.style_map);
			initMap(map, mapParams);
        } else if (elementSettingsMap.style_select === 'prestyle') {
			var fileStyle = elementSettingsMap.snazzy_select;
			$.getJSON(fileStyle + ".json", function (json) {
				mapParams['styles'] = json;
				initMap(map, mapParams);
			});
        } else {
          	initMap(map, mapParams);
        }

        function initMap(mapElement, mapParameters) {
            map = new google.maps.Map(mapElement, mapParameters);
            var markers = [];
            var mapDataType = elementSettingsMap.map_data_type;

            // Geolocation
            if(elementSettingsMap.geolocation == 'yes') {
              const locationButton = document.createElement("button");
              locationButton.textContent = elementSettingsMap.geolocation_button_text;
              locationButton.classList.add("custom-map-control-button");
              map.controls[google.maps.ControlPosition.TOP_CENTER].push(locationButton);
              locationButton.addEventListener("click", () => {
                // Try HTML5 geolocation.
                if (navigator.geolocation) {
                  navigator.geolocation.getCurrentPosition(
                    (position) => {
                      const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                      };
                      map.setCenter(pos);
                    },
                    () => {
                      handleLocationError(true, new google.maps.InfoWindow(), map.getCenter());
                    }
                  );
                } else {
                  // Browser doesn't support Geolocation
                  handleLocationError(false, new google.maps.InfoWindow(), map.getCenter());
                }
              });
            }

            if (elementSettingsMap.use_query) {
                bounds = new google.maps.LatLngBounds();
                for (let i = 0; i < positions.length; i++) {

                    if (mapDataType == 'address') {
						addressToLocation(
							positions[i]['address'],
							positions[i]['marker'],
							positions[i]['infowindow'],
							positions[i]['link'],
							changeMapLocation,
							markerWidth,
							markerHeight
						);
                    } else if ( mapDataType == 'latlng' || mapDataType == 'acfmap' ) {

                        var latLng = new google.maps.LatLng(positions[i]['lat'], positions[i]['lng']); //Makes a latlng
                        map.panTo(latLng); //Make map global

                        var imageMarkerList = positions[i]['marker'];
                        var markerWidth = elementSettingsMap.marker_width;
                        var markerHeight = elementSettingsMap.marker_height;
                        if (markerWidth && markerHeight && imageMarkerList) {
							imageMarkerList = {
								url: positions[i]['marker'],
								scaledSize: new google.maps.Size(markerWidth, markerHeight)
							};
                        }

                        new google.maps.LatLng('0', '0');

                        var marker = new google.maps.Marker({
							position: latLng,
							map: map,
							icon: imageMarkerList,
							animation: google.maps.Animation.DROP,
                        });

                        markers.push(marker);
                        bounds.extend(marker.position);

                        if (elementSettingsMap.enable_infoWindow) {
                          google.maps.event.addListener(marker, 'click', (function (marker, k) {
                            return function () {
								if (elementSettingsMap.infoWindow_click_to_post) {
									window.location = positions[k]['link'];
								} else {
									var iwOptions = {
										content: positions[k]['infowindow'],
									}
									if(elementSettingsMap.infoWindow_panel_maxwidth.size){
										iwOptions['maxWidth'] = elementSettingsMap.infoWindow_panel_maxwidth.size;
									}
									var infoWindowMap = new google.maps.InfoWindow(iwOptions);

									if (lastWindow) lastWindow.close();
									infoWindowMap.open(map, marker);
									lastWindow = infoWindowMap;
								}
                            }
                          })(marker, i));
                        }
                    }
                    // Center the map
                    map.fitBounds(bounds);
					if ( ! elementSettingsMap.auto_zoom ) {
						var listener = google.maps.event.addListenerOnce(map, "idle", function () {
							// Set Zoom after centered the map
							map.setZoom(zoom);
						});
					}
                }
                if( elementSettingsMap.markerclustererControl ){
                	// Add a marker clusterer to manage the markers.
                	new markerClusterer.MarkerClusterer({
						map,
						markers,
						imagePath: '/wp-content/plugins/dynamic-content-for-elementor/assets/lib/gmap/markerclusterer/img/m'
					});
                }
            } else {
				var marker;
                if (mapDataType == 'address') {
					let address = positions[0].address || '';
					let geocoder = new google.maps.Geocoder();
					geocoder.geocode( { 'address': address}, function(results, status) {
						if (status == 'OK') {
							map.setCenter(results[0].geometry.location);
							marker = new google.maps.Marker({
									map: map,
									position: results[0].geometry.location,
									icon: imageMarker,
									animation: google.maps.Animation.DROP,
							});
							infoWindow(marker);
						}
					});
                } else if (mapDataType == 'latlng' || mapDataType == 'acfmap') {
                    var latLng = new google.maps.LatLng(latitude, longitude); // Makes a latlng
                    map.panTo(latLng); // Make map global

                    marker = new google.maps.Marker({
						map: map,
                        position: latLng,
                        icon: imageMarker,
                        animation: google.maps.Animation.DROP,
                    });
					infoWindow(marker);
                }


            }
        }

		function infoWindow(marker) {
			if (elementSettingsMap.enable_infoWindow ) {
				if( elementSettingsMap.infoWindow_click_to_url && elementSettingsMap.infoWindow_url ) {
					marker.addListener('click', function () {
						window.location = elementSettingsMap.infoWindow_url.url;
					});
				} else if( positions[0].infowindow ) {
					var iwOptions = {
						content: positions[0].infowindow,
					}
					if(elementSettingsMap.infoWindow_panel_maxwidth.size){
						iwOptions['maxWidth'] = elementSettingsMap.infoWindow_panel_maxwidth.size;
					}
					var infoWindowMap = new google.maps.InfoWindow(iwOptions);

					marker.addListener('click', function () {
						infoWindowMap.open(map, marker);
					});
				}
			}
		}

        function changeMapLocation(locations) {
            if (locations && locations.length >= 1) {

				// Image Marker
				if (locations[0].marker != "") {
					let imageMarker;
					if( locations[0].markerWidth && locations[0].markerHeight && locations[0].marker ) {
						imageMarker = {
							url: locations[0].marker,
							scaledSize: new google.maps.Size(markerWidth, markerHeight),
						};
					} else {
						imageMarker = {
							url: locations[0].marker,
						};
					}
				}

				// New marker
                var marker = new google.maps.Marker({
                    map: map,
                    position: locations[0].location,
					icon: imageMarker,
                });

				map.panTo(locations[0].location);

				// Infowindow
                if (elementSettingsMap.enable_infoWindow && locations[0].infoWindow) {
					var iwOptions = {
						content: locations[0].infoWindow,
					}
					if(elementSettingsMap.infoWindow_panel_maxwidth.size){
						iwOptions['maxWidth'] = elementSettingsMap.infoWindow_panel_maxwidth.size;
					}
					var infoWindowMap = new google.maps.InfoWindow(iwOptions);
                    marker.addListener('click', function () {
                        if (elementSettingsMap.infoWindow_click_to_post) {
                            window.location = locations[0].postLink;
                        } else {
                            infoWindowMap.open(map, marker);
                        }
                    });
                }
                if (elementSettingsMap.use_query) {
                    bounds.extend(marker.position);
                    map.fitBounds(bounds);
                }
            }
        }
    };

    function addressToLocation(address, markerimg, iw, pl, callback, markerWidth, markerHeight) {

        // Geocoder converts addresses to latitude-longitude positions
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode(
			{
				address: address
			},
			function (results, status) {

              var resultLocations = [];

              if (status == google.maps.GeocoderStatus.OK) {
                if (results) {
                  var numOfResults = results.length;
                  for (var i = 0; i < numOfResults; i++) {
                    var result = results[i];
                    resultLocations.push(
                      	{
							text: result.formatted_address,
							addressStr: result.formatted_address,
							location: result.geometry.location,
							marker: markerimg,
							postLink: pl,
							infoWindow: iw,
							markerWidth: markerWidth,
							markerHeight: markerHeight,
                      	}
                    );
                  }
                }
              }

              if (resultLocations.length > 0) {
                callback(resultLocations);
              } else {
                callback(null);
              }
          }
        );

    }

    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
      infoWindow.setPosition(pos);
      infoWindow.setContent(
        browserHasGeolocation
          ? "Error: The Geolocation service failed."
          : "Error: Your browser doesn't support geolocation."
      );
      infoWindow.open(map);
    }

    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-acf-google-maps.default', DyncontEl_GoogleMapsHandler);
    });

})(jQuery);

// Re init layout after ajax request on Search&Filter Pro
(function ( $ ) {
	"use strict";
	$(function () {
		$(document).on("sf:ajaxfinish", ".searchandfilter", function( e, data ) {
			if ( elementorFrontend) {
				if ( elementorFrontend.elementsHandler.runReadyTrigger && SF_LDATA.extensions.indexOf('search-filter-elementor') < 0 ) {
					elementorFrontend.elementsHandler.runReadyTrigger(data.targetSelector);
				}
			}
		});
	});
}(jQuery));
