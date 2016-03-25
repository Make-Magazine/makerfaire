(function(angular) {
  'use strict';
  angular.module('faireMapsApp').component('fairesGoogleMap', {
    bindings: {
      mapId: '@id',
      mapData: '='
    },
    controller: ['$rootScope', 'GMapsInitializer', 'FaireMapsSharedData', '$filter',
      function($rootScope, GMapsInitializer, FaireMapsSharedData, $filter) {
        var ctrl = this;
        var gMap;

        function initMap(mapId) {
          var customMapType = new google.maps.StyledMapType([{
            stylers: [{
              hue: '#FFFFFF'
            }, {
              visibility: 'simplified'
            }, {
              gamma: 2
            }, {
              weight: 0.5
            }]
          }, {
            elementType: 'labels',
            stylers: [{
              visibility: 'off'
            }]
          }, {
            featureType: 'landscape',
            stylers: [{
              color: '#FFFFFF'
            }]
          }, {
            featureType: 'water',
            stylers: [{
              color: '#EDF2F5'
            }]
          }], {
            name: 'Custom Style'
          });
          var customMapTypeId = 'custom_style';
          gMap = new google.maps.Map(document.getElementById(mapId), {
            center: {
              lat: 32,
              lng: -70
            },
            disableDefaultUI: true,
            scrollwheel: false,
            zoomControl: true,
            minZoom: 2,
            zoom: 3
          });
          gMap.mapTypes.set(customMapTypeId, customMapType);
          gMap.setMapTypeId(customMapTypeId);
          FaireMapsSharedData.infowindow = new google.maps.InfoWindow({
            content: undefined
          });

          function setMarkers(data) {
            var row;
            var gMarker;
            var gMarkerIcon;
            var gMarkerZIndex;
            for (var i = 0; i < data.length; i++) {
              row = data[i];
              gMarkerIcon = {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 5,
                fillOpacity: 0.5,
                strokeOpacity: 0
              };
              gMarkerZIndex = 1;
              switch (row.category) {
                case 'Flagship':
                  gMarkerIcon.fillColor = '#1DAFEC';
                  gMarkerIcon.scale = 9;
                  gMarkerZIndex = 2;
                  break;
                case 'Featured':
                  gMarkerIcon.fillColor = '#E75662';
                  break;
                case 'School':
                  gMarkerIcon.fillColor = '#00BA4B';
                  break;
                default:
                  gMarkerIcon.fillColor = '#F3BF70';
              }
              gMarker = new google.maps.Marker({
                position: {
                  lat: parseFloat(row.lat),
                  lng: parseFloat(row.lng)
                },
                icon: gMarkerIcon,
                map: gMap,
                animation: google.maps.Animation.DROP,
                title: row.name,
                description: row.description,
                category: row.category,
                zIndex: gMarkerZIndex,
                dataRowSrc: row
              });
              google.maps.event.addListener(gMarker, 'mouseover', displayMarkerInfo);
              gMarker.dataRowSrc.event_end_dt = new Date(gMarker.dataRowSrc.event_end_dt);
              gMarker.dataRowSrc.event_start_dt = new Date(gMarker.dataRowSrc.event_start_dt);
              FaireMapsSharedData.gmarkers1.push(gMarker);
            }
            FaireMapsSharedData.setMapDone();
          }

          function displayMarkerInfo() {
            var marker_map = this.getMap();
            FaireMapsSharedData.infowindow.setContent('<div id="content"><h3 class="firstHeading">' +
              this.title + '</h3>' +
              '<div id="bodyContent"><p>' +
              (this.dataRowSrc.venue_address_city || '') +
              (this.dataRowSrc.venue_address_state && ', ' + this.dataRowSrc.venue_address_state || '') +
              (this.dataRowSrc.venue_address_country && ', ' + this.dataRowSrc.venue_address_country + ' ' || '') +
              (this.dataRowSrc.event_start_dt && $filter('date')(this.dataRowSrc.event_start_dt, 'mediumDate') || '') +
              '</p><p>' +
              (this.dataRowSrc.faire_url &&
                '<a href="' + this.dataRowSrc.faire_url + '" target="blank">' + this.dataRowSrc.faire_url + '</a>' || '') +
              '</p></div>' +
              '</div>'
            );
            FaireMapsSharedData.infowindow.open(marker_map, this);
          }
          setMarkers(ctrl.mapData);
        }
        GMapsInitializer.then(function() {
          initMap(ctrl.mapId);
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
              gMap.setCenter(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
            });
          }
        });
      }
    ]
  });
})(window.angular);
