var resourceApp = angular.module('resourceApp', ['ngRoute', 'rmgControllers', 'ngSanitize', 'ngCsv', 'ui.bootstrap',
                                                 'ngTouch', 'ui.grid', 'ui.grid.edit', 'ui.grid.rowEdit','ui.grid.cellNav']);

resourceApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/entry', {
        templateUrl: 'partials/entry.html',
        controller: 'entryCtrl'
      }).
      when('/:main/:sub', {
        templateUrl: 'partials/vendors.html',
        controller: 'VendorsCtrl'
      }).
      otherwise({
        redirectTo: '/'
      });
  }]);

jQuery('.tree-toggle').click(function () {
	jQuery(this).parent().children('ul.tree').toggle(200);
});