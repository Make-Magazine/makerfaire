var isAdminBar = false,
isEditMode = false;
(function ($) {

    var DyncontEl_GoogleMapsHandler = function ($scope, $) {
        var map;
        var bounds;

        var elementSettingsMap = get_Dyncontel_ElementSettings($scope);

        var id_scope = $scope.attr('data-id');

        var map = $scope.find('#el-wgt-map-' + id_scope)[0];
        if (!map) {
          return;
        }

        var indirizzo = jQuery(map).data('address');
        var lati = jQuery(map).data('lat') || 0;
        var long = jQuery(map).data('lng') || 0;

        var infoWindow = jQuery(map).data('infowindow') || 'Empty Info Window';
        var lastWindow = null;

        var imageMarker = jQuery(map).data('imgmarker') || '';

        var w = elementSettingsMap.marker_width;
        var h = elementSettingsMap.marker_height;
        if (w && h && imageMarker) {
          imageMarker = {
            url: jQuery(map).data('imgmarker') || '',
            scaledSize: new google.maps.Size(w, h)
          };
        }
        var zoom = jQuery(map).data('zoom');

        if (elementSettingsMap.zoom && elementSettingsMap.zoom.size) {
            zoom = elementSettingsMap.zoom.size;
        }

        var centroMappa = {lat: lati, lng: long};

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
          center: centroMappa
        };

        if (elementSettingsMap.map_type && elementSettingsMap.map_type !== "acfmap") {
          mapParams['mapTypeId'] = elementSettingsMap.map_type;
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

        function initMap(elements_map, mapParams_map) {
            map = new google.maps.Map(elements_map, mapParams_map);
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

            // Forzo l'uso della latitudine-longitudine se sto usando acf
            if(elementSettingsMap.acf_mapfield) {
              mapDataType = 'latlng';
            }

            if (elementSettingsMap.use_query) {
                bounds = new google.maps.LatLngBounds();
                eval('var address_list = address_list_'+id_scope);

                for (i = 0; i < address_list.length; i++) {

                    if (mapDataType == 'address') {
                      addressToLocation(
                        address_list[i]['address'],
                        address_list[i]['marker'],
                        address_list[i]['infoWindow'],
                        address_list[i]['postLink'],
                        changeMapLocation);

                    } else if (mapDataType == 'latlng') {

                        var latLng = new google.maps.LatLng(address_list[i]['lat'], address_list[i]['lng']); //Makes a latlng
                        map.panTo(latLng); //Make map global

                        var imageMarkerList = address_list[i]['marker'];

                        var w = elementSettingsMap.marker_width;
                        var h = elementSettingsMap.marker_height;
                        if (w && h && imageMarkerList) {
                          imageMarkerList = {
                            url: address_list[i]['marker'],
                            scaledSize: new google.maps.Size(w, h)
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
                                  if (isEditMode) {
                                      alert('You have clicked: ' + address_list[k]['postLink']);
                                      return false;
                                  } else {
                                      window.location = address_list[k]['postLink'];
                                  }
                              } else {
                                  var iwOptions = {
                                      content: address_list[k]['infoWindow'],
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
						var listener = google.maps.event.addListener(map, "idle", function () {
							// Set Zoom after centered the map
							map.setZoom(zoom);
							google.maps.event.removeListener(listener);
						});
					}
                }
                if( elementSettingsMap.markerclustererControl ){
                	// Add a marker clusterer to manage the markers.
                	new MarkerClusterer(map, markers,
                    {imagePath: '/wp-content/plugins/dynamic-content-for-elementor/assets/lib/gmap/markerclusterer/img/m'});
                }

            } else {

                if (mapDataType == 'address') {
                    addressToLocation(indirizzo, imageMarker, infoWindow, null, changeMapLocation);
                } else if (mapDataType == 'latlng') {
                    var latLng = new google.maps.LatLng(lati, long); // Makes a latlng
                    map.panTo(latLng); // Make map global

                    var infoWindowMap = new google.maps.InfoWindow({
                        content: infoWindow
                    });

                    var marker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                        icon: imageMarker,
                        animation: google.maps.Animation.DROP,
                    });

                    if (elementSettingsMap.enable_infoWindow) {
                        marker.addListener('click', function () {
                            infoWindowMap.open(map, this);
                        });
                    }
                }
            }
        }

        function changeMapLocation(locations) {
            if (locations && locations.length >= 1) {

                var image = {
                    url: locations[0].marker,
                };
                var objMarker = {
                    map: map,
                    position: locations[0].location,
                };
                if (locations[0].marker != "") {
                    objMarker['icon'] = image;
                }
                var marker = new google.maps.Marker(objMarker);

                var infoWindowMap = new google.maps.InfoWindow({
                    content: locations[0].infoWindow,
                });
                map.panTo(locations[0].location);

                if (elementSettingsMap.enable_infoWindow) {
                    marker.addListener('click', function () {

                        if (elementSettingsMap.infoWindow_click_to_post) {
                            if (isEditMode) {
                                alert('You clicked: ' + locations[0].postLink);
                                return false;
                            } else {
                                window.location = locations[0].postLink;
                            }
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

    function addressToLocation(address, markerimg, iw, pl, callback) {

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
                          infoWindow: iw
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
        if (elementorFrontend.isEditMode()) {
            isEditMode = true;
        }

        if ($('body').is('.admin-bar')) {
            isAdminBar = true;
        }
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
