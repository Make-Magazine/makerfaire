var resourceApp = angular.module('resourceApp', ['ngRoute', 'rmgControllers', 'ngSanitize', 'ui.bootstrap','ui.grid.resizeColumns',
                                                 'ui.grid', 'ui.grid.edit', 'ui.grid.rowEdit','ui.grid.cellNav','ui.grid.exporter',
                                                 'ui.grid.selection', 'ui.grid.grouping', 'ui.grid.autoResize']);

resourceApp.config(['$locationProvider', '$routeProvider',
  function config($locationProvider, $routeProvider) {
    $locationProvider.hashPrefix('');
    $routeProvider.
      when('/canned', {
        templateUrl: 'partials/canned.html',
        controller: 'cannedCtrl'
      }).
      when('/canned/:sub', {
        templateUrl: 'partials/canned.html',
        controller: 'cannedCtrl'
      }).
      when('/canned/:sub/:faire', {
        templateUrl: 'partials/canned.html',
        controller: 'cannedCtrl'
      }).
      when('/reports/build', {
        templateUrl: 'partials/build.html',
        controller: 'buildCtrl'
      }).
      when('/reports', {
        templateUrl: 'partials/reports.html',
        controller: 'reportsCtrl'
      }).
      when('/reports/:sub', {
        templateUrl: 'partials/reports.html',
        controller: 'reportsCtrl'
      }).
      when('/reports/:sub/:faire', {
        templateUrl: 'partials/reports.html',
        controller: 'reportsCtrl'
      }).
      when('/ent2resources', {
        templateUrl: 'partials/ent2resources.html',
        controller: 'ent2ResCtrl'
      }).
      when('/ent2resources/:type', {
        templateUrl: 'partials/ent2resources.html',
        controller: 'ent2ResCtrl'
      }).
      when('/ent2resources/:type/:faire', {
        templateUrl: 'partials/ent2resources.html',
        controller: 'ent2ResCtrl'
      }).
      when('/:main/:sub', {
        templateUrl: 'partials/vendors.html',
        controller: 'VendorsCtrl'
      }).
      otherwise({
        templateUrl: "partials/empty.html"
      });
  }
]);

jQuery('.tree-toggle').click(function () {
	jQuery(this).parent().children('ul.tree').toggle(200);
});

jQuery("#menu-toggle").click(
  function(e){
    e.preventDefault();
    jQuery("#wrapper").toggleClass("toggled");
    if(jQuery("#wrapper").hasClass("toggled")){
      jQuery('#menu-toggle span').html('Show Sidebar');
    }else{
      jQuery('#menu-toggle span').html('Collapse Sidebar');
    }
  }
);