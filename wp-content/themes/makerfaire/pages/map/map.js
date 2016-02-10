var faireMapsApp = angular.module('faireMapsApp', ['ngTasty']);

faireMapsApp.controller('MapCtrl', ['$scope', '$http',
  function($scope, $http) {
    'use strict';
    var vm = this;
    $http.get('/wp-admin/admin-ajax.php?action=get_faires_map_data')
      .then(function successCallback(response) {
        vm.faireMarkers = {};
        vm.faireMarkers.header = [
          { 'name': 'Name' },
          { 'category': 'Category' },
          { 'description': 'Description' }
        ];
        vm.faireMarkers.sortBy = 'name';
        vm.faireMarkers.sortOrder = 'asc';
        vm.faireMarkers.rows = response && response.data;
      }, function errorCallback() {
        // error
      });
  }
]);

faireMapsApp.factory('GMapsInitializer', ['$window', '$q',
  function($window, $q) {
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
  }
]);

faireMapsApp.directive('fairesGoogleMap', ['$rootScope', 'GMapsInitializer',
  function($rootScope, GMapsInitializer) {
    'use strict';
    return {
      scope: {
        mapId: '@id',
        mapData: '='
      },
      controller: function($scope) {
        var gmarkers1 = [];
        function initMap(mapId) {
          var gMap;
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
            zoom: 3
          });
          gMap.mapTypes.set(customMapTypeId, customMapType);
          gMap.setMapTypeId(customMapTypeId);
          function setMarkers(data) {
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
                map: gMap,
                animation: google.maps.Animation.DROP,
                title: gMarker.description,
                category: gMarker.category,
                zIndex: gMarkerZIndex
              });
              gmarkers1.push(gMarker);
            }
          }
          setMarkers($scope.mapData.rows);
        }
        function filterMarkers(category, display) {
          var marker;
          for (var i = 0; i < gmarkers1.length; i++) {
            marker = gmarkers1[i];
            // Visible only if category matches
            if (marker.category == category || category.length === 0) {
              marker.setVisible(display);
            }
            // else {
            //   marker.setVisible(false);
            // }
          }
        }
        $rootScope.$on('toggleMapFilter', function(event, args) {
          filterMarkers(args.filter, args.state);
        });
        GMapsInitializer.then(function() {
          initMap($scope.mapId);
        });
      }
    };
  }
]);

faireMapsApp.directive('fairesMapFilter', ['$rootScope',
  function($rootScope) {
    'use strict';
    return {
      scope: {
        filter: '@',
        defaultState: '='
      },
      transclude: true,
      template: '<div class="checkbox">' +
          '<label><input type="checkbox" ng-model="defaultState" ng-click="toggleFilter()">' +
            '<ng-transclude></ng-transclude>' +
          '</label>' +
        '</div>',
      replace: true,
      controller: function($scope) {
        $scope.toggleFilter = function() {
          var toggleState = {
            filter: $scope.filter,
            state: $scope.defaultState
          };
          $rootScope.$emit('toggleMapFilter', toggleState);
        };
      }
    };
  }
]);
