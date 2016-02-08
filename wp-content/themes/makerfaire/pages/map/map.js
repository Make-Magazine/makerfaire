var faireMapsApp = angular.module('faireMapsApp', []);

faireMapsApp.controller('MapCtrl', function($scope, GMapsInitializer) {
  'use strict';
  $scope.name = 'World';
});

faireMapsApp.factory('GMapsInitializer', function($window, $q) {
  'use strict';
  // &key=AIzaSyBITa21JMkxsELmGoDKQ3owasOW48113w4
  var asyncUrl = 'https://maps.googleapis.com/maps/api/js??v=3.exp&callback=googleMapsInitialized',
    mapsDefer = $q.defer();
  //Callback function - resolving promise after maps successfully loaded
  $window.googleMapsInitialized = mapsDefer.resolve;
  //Async loader
  var asyncLoad = function(asyncUrl) {
    var script = document.createElement('script');
    script.src = asyncUrl;
    document.body.appendChild(script);
  };
  //Start loading google maps
  asyncLoad(asyncUrl);
  //Usage: GMapsInitializer.then(callback)
  return mapsDefer.promise;
});

faireMapsApp.directive('fairesGoogleMap', ['$rootScope', 'GMapsInitializer', '$timeout',
  function($rootScope, GMapsInitializer, $timeout) {
    'use strict';
    return {
      scope: {
        mapId: '@id'
      },
      controller: function($scope) {
        var faireMarkers;
        var gmarkers1 = [];

        function initMap(mapId) {
          var map;
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
          map = new google.maps.Map(document.getElementById(mapId), {
            center: {
              lat: 32,
              lng: -70
            },
            disableDefaultUI: true,
            zoom: 3
          });
          map.mapTypes.set(customMapTypeId, customMapType);
          map.setMapTypeId(customMapTypeId);

          function setMarkers(data, map) {
            var gMarker;
            var gMarkerIcon;
            var gMarkerZIndex;
            for (var i = 0; i < data.length; i++) {
              gMarker = data[i];
              gMarkerIcon = {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 8,
                fillOpacity: 0.5,
                strokeOpacity: 0
              };
              gMarkerZIndex = 1;
              switch (gMarker.category) {
                case 'Featured Faires':
                  gMarkerIcon.fillColor = '#1DAFEC';
                  gMarkerIcon.scale = 9;
                  gMarkerZIndex = 2;
                  break;
                case 'All Maker Faires':
                  gMarkerIcon.fillColor = '#F2BF70';
                  break;
                default:
                  gMarkerIcon.fillColor = '#666666';
              }
              gMarker = new google.maps.Marker({
                position: {
                  lat: gMarker.coordinates[1],
                  lng: gMarker.coordinates[0]
                },
                icon: gMarkerIcon,
                map: map,
                animation: google.maps.Animation.DROP,
                title: gMarker.description,
                category: gMarker.category,
                zIndex: gMarkerZIndex
              });
              gmarkers1.push(gMarker);
            }
          }
          jQuery.get('/wp-admin/admin-ajax.php?action=get_faires_map_data', function(data) {
            faireMarkers = JSON.parse(data);
            setMarkers(faireMarkers, map);
          });
        }

        function filterMarkers(category) {
          var marker;
          for (var i = 0; i < gmarkers1.length; i++) {
            marker = gmarkers1[i];
            // Visible only if category matches
            if (marker.category == category || category.length === 0) {
              marker.setVisible(true);
            }
            else {
              marker.setVisible(false);
            }
          }
        }
        $rootScope.$on('toggleMapFilter', function(event, args) {
          filterMarkers(args)
        });
        GMapsInitializer.then(function() {
          initMap($scope.mapId);
        });
      }
    };
  }
]);

faireMapsApp.directive('fairesMapFilter', ['$rootScope', 'GMapsInitializer', '$timeout',
  function($rootScope, GMapsInitializer, $timeout) {
    'use strict';
    return {
      scope: {
        filter: '@'
      },
      template: '<div ng-click="foo.filter(filter)" class="btn btn-primary">{{filter}}</div>',
      replace: true,
      controller: function($scope) {
        $scope.foo = {
          filter: function(filter) {
            $rootScope.$emit('toggleMapFilter', filter);
          }
        }
      }
    };
  }
]);
