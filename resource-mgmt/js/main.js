var resourceApp = angular.module('resourceApp', ['ngRoute', 'rmgControllers', 'ngSanitize', 'ngCsv',
                                                 'ngTouch', 'ui.grid', 'ui.grid.edit', 'ui.grid.rowEdit']);

resourceApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
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