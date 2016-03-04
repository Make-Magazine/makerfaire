var resourceApp = angular.module('resourceApp', ['ngRoute', 'rmgControllers', 'ngSanitize', 'ui.bootstrap',
                                                 'ui.grid', 'ui.grid.edit', 'ui.grid.rowEdit','ui.grid.cellNav','ui.grid.exporter']);

resourceApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/reports', {
        templateUrl: 'partials/reports.html',
        controller: 'reportsCtrl'
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