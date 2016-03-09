(function(angular) {
  'use strict';
  var faireMapsApp = angular.module('faireMapsApp', ['angularUtils.directives.dirPagination', 'ordinal']);
  // todo: these state vars would be better as injectable factory dependency:
  var gmarkers1 = [];
  var infowindow;

  faireMapsApp.controller('MapCtrl', ['$http', '$rootScope', '$filter',
    function($http, $rootScope, $filter) {
      var ctrl = this;
      var faireFilters = {
        filters: ['Flagship', 'Featured', 'Mini'],
        search: '',
        pastEvents: false
      }
      ctrl.pastEvents = false;
      $rootScope.$on('toggleMapFilter', function(event, args) {
        var index = faireFilters.filters.indexOf(args.filter);
        if (args.state && index < 0) {
          faireFilters.filters.push(args.filter);
        } else if (!args.state && index > -1) {
          faireFilters.filters.splice(index, 1);
        }
        ctrl.applyMapFilters();
      });
      ctrl.applyMapFilters = function() {
        infowindow.close();
        ctrl.pastPresent = {
          past: 0,
          present: 0
        }
        faireFilters.search = ctrl.filterText;
        faireFilters.pastEvents = ctrl.pastEvents;
        var newModel = [];
        var todaysDate = new Date();
        // check if "sorting.search" string exists in marker object:
        function containsString(marker) {
          if(!faireFilters.search) {
            return true;
          }
          function checkForValue(json, value) {
            for (var key in json) {
              if (typeof(json[key]) === 'object') {
                return checkForValue(json[key], value);
              } else if (typeof(json[key]) === 'string' && json[key].toLowerCase().match(value)) {
                return true;
              }
            }
            return false;
          }
          return checkForValue(marker, faireFilters.search.toLowerCase());
        }
        // check if type matches ok:
        function isTypeToggled(marker) {
          return (faireFilters.filters.indexOf(marker.category) > -1);
        }
        // check if date is ok:
        function isDateOk(marker) {
          if(!marker.event_end_dt || marker.event_end_dt == '0000-00-00 00:00:00') {
            ctrl.pastPresent.present++;
            return true;
          }
          var isInPast = new Date(marker.event_end_dt).getTime() < todaysDate.getTime();
          if(isInPast) {
            ctrl.pastPresent.past++;
          } else {
            ctrl.pastPresent.present++;
          }
          return (faireFilters.pastEvents == isInPast);
        }
        gmarkers1.map(function(marker) {
          var rowData = marker.dataRowSrc;
          if (containsString(rowData) && isTypeToggled(rowData) && isDateOk(rowData)) {
            newModel.push(rowData);
            
            marker.setVisible(true);
          } else {
            ctrl.pastPresent.past++;
            marker.setVisible(false);
          }
        });
        ctrl.faireMarkers = newModel;
      }

      $http.get('/query/?type=map')
        .then(function successCallback(response) {
          ctrl.faireMarkers = response && response.data && response.data.Locations;
        }, function errorCallback() {
          // error
        });
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
                gMarkerIcon.fillColor = '#F2BF70';
                break;
              default:
                gMarkerIcon.fillColor = '#666666';
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
            google.maps.event.addListener(gMarker, 'click', displayMarkerInfo);
            gMarker.dataRowSrc.event_end_dt = new Date(gMarker.dataRowSrc.event_end_dt);
            gMarker.dataRowSrc.event_start_dt = new Date(gMarker.dataRowSrc.event_start_dt);
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
        setMarkers(ctrl.mapData);
      }
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
    }
  });

})(window.angular);
