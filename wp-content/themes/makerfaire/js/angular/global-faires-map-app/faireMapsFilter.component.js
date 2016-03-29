(function(angular) {
  'use strict';
  angular.module('faireMapsApp').component('fairesMapFilter', {
    template: '<div class="checkbox">\
        <label><input type="checkbox" class="checkbox-fa-icon" ng-model="$ctrl.defaultState" ng-click="$ctrl.toggleFilter()">\
          <i class="fa fa-square-o"></i>\
          <i class="fa fa-check-square-o"></i>\
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
