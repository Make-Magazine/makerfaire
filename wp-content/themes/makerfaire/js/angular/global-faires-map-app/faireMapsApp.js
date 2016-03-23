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
