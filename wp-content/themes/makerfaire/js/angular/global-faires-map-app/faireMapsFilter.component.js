(function(angular) {
  'use strict';
  angular.module('faireMapsApp').component('fairesMapFilter', {
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
