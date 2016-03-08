(function(angular) {
  'use strict';
  var faireMapsApp = angular.module('faireMapsApp', ['angularUtils.directives.dirPagination', 'ordinal']);
  // todo: these state vars would be better as injectable factory dependency:
  var gmarkers1 = [];
  var infowindow;

  faireMapsApp.controller('MapCtrl', ['$http', '$rootScope', '$filter',
    function($http, $rootScope, $filter) {
      var ctrl = this;
      var markersData;
      function setMarkers(markers) {
        ctrl.faireMarkers = {};
        ctrl.faireMarkers.header = [{
          'name': 'Name'
        }, {
          'category': 'Category'
        }, {
          'description': 'Description'
        }];
        ctrl.faireMarkers.sortBy = 'name';
        ctrl.faireMarkers.sortOrder = 'asc';
        ctrl.faireMarkers.rows = markers;
      }
      $http.get('/query/?type=map')
        .then(function successCallback(response) {
          setMarkers(response && response.data && response.data.Locations);
          markersData = response && response.data && response.data.Locations;
        }, function errorCallback() {
          // error
        });
      ctrl.toggleMapSearch = function() {
        setMarkers($filter('filter')(markersData, ctrl.filterText));
        $rootScope.$emit('toggleMapSearch', ctrl.filterText);
      };
      $rootScope.$on('toggleMapFilter', function(event, args) {
        ctrl.filterText = undefined;
        applyMapFilters(event, args);
      });
      var faireTypesFilter = [];
      function applyMapFilters (event, args) {
        ctrl.searchText = undefined;
        var index = faireTypesFilter.indexOf(args.filter);
        if(args.state && index < 0) {
          faireTypesFilter.push(args.filter);
        } else if (!args.state && index > -1) {
          faireTypesFilter.splice(index, 1);
        }
        if (!markersData) {
          return;
        }
        function isEnabled(marker) {
          return (faireTypesFilter.indexOf(marker.category) > -1);
        }
        // filter angular table model (Array.prototype.filter)
        setMarkers(markersData.filter(isEnabled));
        // set pin visibility through Google Maps JS API (Array.prototype.map)
        gmarkers1.map(function(marker) {
          marker.setVisible(isEnabled(marker));
        });
      }
    }
  ]);

  faireMapsApp.factory('GMapsInitializer', ['$window', '$q',
    function($window, $q) {
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

  faireMapsApp.component('fairesGoogleMap', {
    bindings: {
      mapId: '@id',
      mapData: '='
    },
    controller: function($rootScope, GMapsInitializer) {
      var ctrl = this;
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
          scrollwheel: false,
          zoomControl: true,
          minZoom: 2,
          zoom: 3
        });
        gMap.mapTypes.set(customMapTypeId, customMapType);
        gMap.setMapTypeId(customMapTypeId);
        infowindow = new google.maps.InfoWindow({
          content: undefined
        });

        function setMarkers(data) {
          var gMarker;
          var gMarkerIcon;
          var gMarkerZIndex;
          for (var i = 0; i < data.length; i++) {
            gMarker = data[i];
            gMarkerIcon = {
              path: google.maps.SymbolPath.CIRCLE,
              scale: 5,
              fillOpacity: 0.5,
              strokeOpacity: 0
            };
            gMarkerZIndex = 1;
            switch (gMarker.category) {
              case 'Flagship':
                gMarkerIcon.fillColor = '#1DAFEC';
                gMarkerIcon.scale = 9;
                gMarkerZIndex = 2;
                break;
              case 'Featured':
                gMarkerIcon.fillColor = '#F2BF70';
                break;
              default:
                gMarkerIcon.fillColor = '#666666';
            }
            gMarker = new google.maps.Marker({
              position: {
                lat: parseFloat(gMarker.lat),
                lng: parseFloat(gMarker.lng)
              },
              icon: gMarkerIcon,
              map: gMap,
              animation: google.maps.Animation.DROP,
              title: gMarker.name,
              description: gMarker.description,
              category: gMarker.category,
              zIndex: gMarkerZIndex
            });
            google.maps.event.addListener(gMarker, 'click', displayMarkerInfo);
            gmarkers1.push(gMarker);
          }
        }
        function displayMarkerInfo() {
          var marker_map = this.getMap();
          infowindow.setContent('<div id="content"><h3 class="firstHeading">' +
            this.title + '</h3>' +
            '<div id="bodyContent"><p>' +
            (this.description || '') +
            '</p></div>' +
            '</div>'
          );
          infowindow.open(marker_map, this);
        }
        setMarkers(ctrl.mapData.rows);
      }

      function searchMarkers(text) {
        text = text.toUpperCase();
        infowindow.close();
        gmarkers1.map(function(obj) {
          if (obj.title && obj.title.toUpperCase().match(text) ||
            obj.category && obj.category.toUpperCase().match(text) ||
            obj.description && obj.description.toUpperCase().match(text)) {
            obj.setVisible(true);
          } else {
            obj.setVisible(false);
          }
        });
      }
      $rootScope.$on('toggleMapSearch', function(event, args) {
        searchMarkers(args);
      });
      GMapsInitializer.then(function() {
        initMap(ctrl.mapId);
      });
    }
  });
  
  faireMapsApp.component('fairesMapFilter', {
    template: '<div class="checkbox">' +
      '<label><input type="checkbox" ng-model="$ctrl.defaultState" ng-click="$ctrl.toggleFilter()">' +
      '<ng-transclude></ng-transclude>' +
      '</label>' +
      '</div>',
    transclude: true,
    // bindings: { toggleMapFilter: '<' },
    bindings: {
      filter: '@',
      defaultState: '='
    },
    replace: true,
    controller: function($rootScope) {
      var ctrl = this;
      $rootScope.$on('toggleMapSearch', function() {
        ctrl.defaultState = true;
      });
      ctrl.toggleFilter = function() {
        var toggleState = {
          filter: ctrl.filter,
          state: ctrl.defaultState
        };
        $rootScope.$emit('toggleMapFilter', toggleState);
      };
      ctrl.toggleFilter();
    }
  });

})(window.angular);
