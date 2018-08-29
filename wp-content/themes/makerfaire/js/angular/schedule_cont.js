var scheduleApp = angular.module('scheduleApp', ['ngAnimate', 'ui.bootstrap', 'angular.filter', 'ngSanitize']);

var dayParam = getUrlParam("day");
var stageParam = getUrlParam("stage");
var typeParam = getUrlParam("type");

scheduleApp.controller('scheduleCtrl', ['$scope', '$filter', '$http', function ($scope, $filter, $http) {   
   //infinite scroll
   $scope.limit = 5;
   var counter = 0;
   $scope.loadMore = function() {
      $scope.limit += 5;
   };
   
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
   
   //if day of the week URL parameter is passed, default the day to this
   $scope.filterdow = "";
   filterdow = "";
   if (dayParam != undefined && dayParam != "") {
      $scope.filterdow = dayParam;
      filterdow  = dayParam;
   }   
   
   var formIDs = jQuery('#forms2use').val();
   var defType = jQuery('#schedType').val();
   var defDOW  = jQuery('#schedDOW').val();

   if (formIDs == '')
      alert('error!  Please set the form to pull from on the admin page.')
   $http.get('/wp-json/makerfaire/v2/fairedata/schedule/' + formIDs)
      .then(function successCallback(response) {                         
         $scope.schedules = response.data.schedule;   
         var dateList = [];            
         angular.forEach($scope.schedules, function (schedule) {
            defDOW = $filter('date')(schedule.time_start, "EEEE");

            if (dateList.indexOf(defDOW) == -1)
               dateList.push(defDOW);
         });
         $scope.dates = dateList.sort();
      }, function errorCallback(error) {
         console.log(error);
      }).finally(function () {
         $scope.showSchedules = true;
      });  
   
   $scope.setDateFilter = function (date) {
      $scope.filterdow = $filter('date')(date, "EEEE");
      filterdow = $filter('date')(date, "EEEE");
   };   
}]);

scheduleApp.filter('dateFilter', function($filter) {
   // Create the return function and set the required parameter name to **input**
   return function(schedules,dayOfWeek) {      
      if(filterdow!=''){
         var out = [];
         // Loop thru the schedule and return only items that meet the selected date         
         angular.forEach(schedules, function(schedule) {            
            if(filterdow===$filter('date')(schedule.time_start, "EEEE")){
               out.push(schedule);
            }
         });
      }else{
         var out = schedules;
      }
      return out;
   }
});

scheduleApp.directive('schedScroll', ['$window', schedScroll]);  
function schedScroll($window) {
    return {
      link: function (scope, element, attrs) {
        var handler;
		  var raw = element[0]; 
		  console.log(raw);
        $window = angular.element($window);
        handler = function() {
			 if(jQuery(".loading").hasClass("ng-hide")){ // don't start adding to the limit until the loading is done
				 var top_of_element = jQuery(".magazine-footer").offset().top;
				 if(jQuery(window).width() < 768) {
					top_of_element = jQuery(".newsletter-footer").offset().top;
				 }
				 var bottom_of_screen = jQuery(window).scrollTop() + window.innerHeight;
				 if (bottom_of_screen > top_of_element) {
					 scope.$apply(attrs.schedScroll);
				 }
			 }
        };
        $window.on('scroll', handler);
      }
    };

};
     