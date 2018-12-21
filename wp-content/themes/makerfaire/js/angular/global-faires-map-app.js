// Compiled file - any changes will be overwritten by grunt task
//!!
//!! js/angular/global-faires-map-app/faireMapsApp.js
(function(angular) {
  'use strict';
  angular.module('faireMapsApp', ['angularUtils.directives.dirPagination', 'ordinal']);

  angular.module('faireMapsApp').factory('FaireMapsSharedData', ['$q', function($q) {
      var defer = $q.defer();
      var FaireMapsSharedData = {
        gmarkers1: [],
        infowindow: undefined,
        mapDone: function() {
          return defer.promise;
        },
        setMapDone: function() {
          defer.notify(true);
        }
      };
      return FaireMapsSharedData;
    }]);
})(window.angular);
;//!!
//!! js/angular/global-faires-map-app/faireMapsFilter.component.js
(function(angular) {
  'use strict';
  angular.module('faireMapsApp').component('fairesMapFilter', {
    template: '<div class="checkbox">\
        <label>\
          <input type="checkbox" class="checkbox-fa-icon" ng-model="$ctrl.defaultState" ng-click="$ctrl.toggleFilter()">\
          <i class="fa fa-circle-o"></i>\
          <i class="fa fa-circle"></i>\
          <ng-transclude></ng-transclude>\
        </label>\
      </div>',
    transclude: true,
    bindings: {
      filter: '@',
      defaultState: '='
    },
    replace: true,
    controller: ['$rootScope', function($rootScope) {
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
    }]
  });
})(window.angular);
;//!!
//!! js/angular/global-faires-map-app/fairesGoogleMap.component.js
(function (angular) {
  'use strict';
  angular.module('faireMapsApp').component('fairesGoogleMap', {
    bindings: {
      mapId: '@id',
      mapData: '='
    },
    controller: ['$rootScope', 'GMapsInitializer', 'FaireMapsSharedData', '$filter',
      function ($rootScope, GMapsInitializer, FaireMapsSharedData, $filter) {
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
                  visibility: 'on'
                }]
            }, {
              featureType: 'landscape',
              stylers: [{
                  color: '#FFFFFF'
                }]
            },
            {
              featureType: 'all',
              elementType: 'geometry',
              stylers: [{
                  color: '#FFFFFF'
                }, {
                  visibility: 'on'
                }]
            }, {
              featureType: 'water',
              stylers: [{
                  color: '#d0eafc'
                }]
            },
            {
              featureType: 'administrative.country',
              elementType: 'labels',
              stylers: [
                {
                  visibility: 'on'
                }
              ]
            },
            {
              featureType: 'all',
              elementType: 'labels.icon',
              stylers: [{
                  visibility: 'off'
                }]
            },
            {
              featureType: 'poi',
              elementType: 'all',
              stylers: [
                {
                  visibility: 'off'
                }
              ]
            },
            {
              featureType: 'administrative.province',
              elementType: 'labels',
              stylers: [
                {
                  visibility: 'off'
                }
              ]
            },
            {
              featureType: 'administrative.locality',
              elementType: 'all',
              stylers: [
                {
                  visibility: 'on'
                }
              ]
            },
            {
              featureType: 'road',
              elementType: 'all',
              stylers: [
                {
                  visibility: 'off'
                }
              ]
            }

          ], {
            name: 'Custom Style'
          });
          var customMapTypeId = 'custom_style';
          gMap = new google.maps.Map(document.getElementById(mapId), {
            center: {
              lat: 23.9758543, lng: 1.4487502
            },
            disableDefaultUI: true,
            scrollwheel: false,
            zoomControl: true,
            minZoom: 1,
            zoom: 2
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
                fillOpacity: 1,
                strokeOpacity: 0
              };
              gMarkerZIndex = 1;
              switch (row.category) {
                case 'Flagship':
                  gMarkerIcon.fillColor = '#D42410';
                  gMarkerIcon.scale = 11;
                  gMarkerZIndex = 2;
                  break;
                case 'Featured':
                  gMarkerIcon.fillColor = '#085a7e';
                  gMarkerIcon.scale = 8;
                  break;
                case 'School':
                  gMarkerIcon.fillColor = '#7ED321';
                  break;
                default:
                  gMarkerIcon.fillColor = '#67D0F7';
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
              (this.dataRowSrc.event_dt || '') +
              '</p><p>' +
              (this.dataRowSrc.faire_url &&
              '<a href="' + this.dataRowSrc.faire_url + '" target="_blank">' + this.dataRowSrc.faire_url + '</a>' || '') +
              '</p></div>' +
              '</div>'
              );
            FaireMapsSharedData.infowindow.open(marker_map, this);
          }
          setMarkers(ctrl.mapData);
        }
        GMapsInitializer.then(function () {
          initMap(ctrl.mapId);
          /*
           if (navigator.geolocation) {
           navigator.geolocation.getCurrentPosition(function(position) {
           gMap.setCenter(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
           });
           }*/
        });
      }
    ]
  });
})(window.angular);
;//!!
//!! js/angular/global-faires-map-app/GMapsInitializer.factory.js
(function(angular) {
  'use strict';
  angular.module('faireMapsApp').factory('GMapsInitializer', ['$window', '$q',
    function($window, $q) {
      // &key=AIzaSyCJHKq4cnlAd4WkaJKYal-8n6V1Y8MCPxw
      var asyncUrl = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDtWsCdftU2vI9bkZcwLxGQwlYmNRnT2VM&v=3.exp&callback=googleMapsInitialized',
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
})(window.angular);
;//!!
//!! js/angular/global-faires-map-app/MapCtrl.controller.js
(function(angular) {
  'use strict';
  angular.module('faireMapsApp').controller('MapCtrl', ['$http', '$rootScope', '$filter', 'FaireMapsSharedData',
    function($http, $rootScope, $filter, FaireMapsSharedData) {
      var ctrl = this;
      var faireFilters = {
        filters: ['Flagship', 'Featured', 'Mini'],
        search: '',
        pastEvents: false
      };
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
      ctrl.toggleBox = function(type) {
        jQuery('.filters faires-map-filter').each(function(index,obj){
          var filter = jQuery(obj).attr( "filter");
          var index = faireFilters.filters.indexOf(filter);
          //if the filter is not the same as the selected type
          if(filter!==type){
            //uncheck and remove from the faireFilters Object
            jQuery(obj).find('input').prop( "checked", false );
            if (index > -1) {
              faireFilters.filters.splice(index, 1);
            }
          }else{
            //make sure it is checked and add to the faireFilters Object if it's not there
            jQuery(obj).find('input').prop( "checked", true );
            if (index < 0) {
              faireFilters.filters.push(filter);
            }
          }
        });

        ctrl.applyMapFilters();
      }
      ctrl.applyMapFilters = function() {
        FaireMapsSharedData.infowindow.close();
        ctrl.pastPresent = {
          past: 0,
          present: 0
        };
        faireFilters.search = ctrl.filterText;
        faireFilters.pastEvents = ctrl.pastEvents;
        var newModel = [];
        var todaysDate = new Date();
        // check if "sorting.search" string exists in marker object:
        function containsString(marker) {
          if (!faireFilters.search) {
            return true;
          }
          function checkForValue(json, value) {
            for (var key in json) {
              if (typeof(json[key]) === 'object') {
                checkForValue(json[key], value);
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
          var eventDate;

          if (Object.prototype.toString.call(marker.event_end_dt) === "[object Date]" &&
              !isNaN(marker.event_end_dt.getTime()) ) { // is end date valid and not set to 0's
            eventDate = marker.event_end_dt; // use end date
          } else if (Object.prototype.toString.call(marker.event_start_dt) === "[object Date]" &&
              !isNaN(marker.event_start_dt.getTime()) ) { // is start date valid and not set to 0's
            eventDate = marker.event_start_dt; // use start date
          } else {
            ctrl.pastPresent.present++;
            return true;
          }

          //set event_date to midnight on the following day so we don't display a faire as being passed until the following day
          var event_date = new Date(eventDate);
          event_date.setDate(event_date.getDate() + 1); //END DATE + 1 day
          event_date.setHours(0,0,0,0);  //event ends at midnight
          var isInPast = event_date.getTime() < todaysDate.getTime();

          if (isInPast) {
            ctrl.pastPresent.past++;
          } else {
            ctrl.pastPresent.present++;
          }
          return (faireFilters.pastEvents == isInPast);
        }
        FaireMapsSharedData.gmarkers1.map(function(marker) {
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
      };

      $http.get('/query/?type=map')
        .then(function successCallback(response) {
          ctrl.faireMarkers = response && response.data && response.data.Locations;
          FaireMapsSharedData.mapDone().then(null, null, function() {
            ctrl.applyMapFilters();
          });
        }, function errorCallback() {
          // error
        });
    }
  ]);
})(window.angular);
