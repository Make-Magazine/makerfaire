(function(angular) {
  'use strict';
  angular.module('faireMapsApp', ['angularUtils.directives.dirPagination', 'ordinal']);

  angular.module('faireMapsApp').factory('FaireMapsSharedData', function() {
    var FaireMapsSharedData = {
      gmarkers1: [],
      infowindow: undefined
    };
    return FaireMapsSharedData;
  });
})(window.angular);
