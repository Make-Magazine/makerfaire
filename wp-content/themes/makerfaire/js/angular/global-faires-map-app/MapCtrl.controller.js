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
          if (Object.prototype.toString.call(marker.event_end_dt) !== "[object Date]" ||
            marker.event_end_dt == '0000-00-00 00:00:00') {
            ctrl.pastPresent.present++;
            return true;
          }
          var isInPast = new Date(marker.event_end_dt).getTime() < todaysDate.getTime();
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
          })
        }, function errorCallback() {
          // error
        });
    }
  ]);
})(window.angular);
