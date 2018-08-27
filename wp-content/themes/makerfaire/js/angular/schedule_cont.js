var scheduleApp = angular.module('scheduleApp', ['ngAnimate', 'ui.bootstrap', 'angular.filter', 'ngSanitize']);
var weekday = new Array(7);
weekday[1] = "Sunday";
weekday[2] = "Monday";
weekday[3] = "Tuesday";
weekday[4] = "Wednesday";
weekday[5] = "Thursday";
weekday[6] = "Friday";
weekday[7] = "Saturday";
var filterdow = "All Days";
var dayParam = getUrlParam("day");
var stageParam = getUrlParam("stage");
var typeParam = getUrlParam("type");
if (dayParam != undefined && dayParam != "") {
   filterdow = dayParam;
}
scheduleApp.controller('scheduleCtrl', ['$scope', '$filter', '$http', function ($scope, $filter, $http) {   
   $scope.showSchedules = false;
   $scope.schedSearch = [];
   $scope.schedSearch.nicename = '';
   //if stage URL parameter is passed, default the stage to this
   if(stageParam != undefined){
      $scope.schedSearch.nicename = stageParam;
   }

   $scope.schedSearch.type = '';
   //if type URL parameter is passed, default the type to this
   if(typeParam != undefined){
      $scope.schedSearch.type = typeParam;
   }
   
   $scope.propertyName = 'time_start';
   var formIDs = jQuery('#forms2use').val();
   var defType = jQuery('#schedType').val();
   var defDOW  = jQuery('#schedDOW').val();

   if (formIDs == '')
      alert('error!  Please set the form to pull from on the admin page.')
   $http.get('/wp-json/makerfaire/v2/fairedata/schedule/' + formIDs)
      .then(function successCallback(response) {                         
         $scope.schedules = response.data.schedule;        
      }, function errorCallback(error) {
         console.log(error);
      }).finally(function () {
         $scope.showSchedules = true;
      });     
}]);
     