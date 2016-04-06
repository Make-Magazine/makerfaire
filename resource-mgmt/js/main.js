var resourceApp = angular.module('resourceApp', ['ngRoute', 'rmgControllers', 'ngSanitize', 'ui.bootstrap','ui.grid.resizeColumns',
                                                 'ui.grid', 'ui.grid.edit', 'ui.grid.rowEdit','ui.grid.cellNav','ui.grid.exporter',
                                                 'ui.grid.selection', 'ui.grid.grouping', 'ui.grid.autoResize']);

resourceApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/reports', {
        templateUrl: 'partials/reports.html',
        controller: 'reportsCtrl'
      }).
      when('/reports/:sub', {
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

jQuery("#menu-toggle").click(function(e){e.preventDefault();jQuery("#wrapper").toggleClass("toggled");});