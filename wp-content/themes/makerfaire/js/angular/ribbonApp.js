(function(angular) {
  'use strict';
  var ribbonApp = angular.module('ribbonApp', ['ngRoute', 'angular.filter', 'angularUtils.directives.dirPagination']);

  ribbonApp.directive('fallbackSrc', function() {
    var fallbackSrc = {
      link: function postLink(scope, iElement, iAttrs) {
        iElement.bind('error', function() {
          angular.element(this).attr('src', iAttrs.fallbackSrc);
        });
      }
    };
    return fallbackSrc;
  });

  ribbonApp.controller('ribbonController', ['$scope', '$filter', '$http', function($scope, $filter, $http) {
    $scope.layout      = 'grid';
    $scope.currentPage = 1;
    $scope.pageSize    = 40;
    $scope.faires      = [];
    
    $scope.query = {};
    $scope.query.faireYear = '';
    $scope.query.location = '';
    $scope.query.ribbonType = '';

    $scope.hasBlue = false;
    $scope.hasRed  = false;

    $scope.loadData = function(year, years) {
      var faireYear = year;
      $scope.years = years || $scope.years;
      $scope.ribbons = [];
      $http.get('/wp-json/makerfaire/v2/mfRibbons/'+faireYear)
        .then(function successCallback(response) {
          var data = response.data;
          $scope.ribbons      = data.ribbons;          
          $scope.blueRibbons  = data.ribbons;
          $scope.redRibbons   = $scope.ribbons;

          //for random order
          shuffle($scope.ribbons);
          
          
          var faires = [];
          angular.forEach(data.ribbons, function(row) {
            //get a list of faires for this particular year
            if (faires.indexOf(row.location) === -1) {
                faires.push(row.location);
            }
            if(row.blueCount > 0){
              $scope.hasBlue = true;
            }
            if(row.redCount > 0){
              $scope.hasRed = true;
            }
          });
          faires.sort();
          $scope.faires = faires;

          $scope.changeView = function (view) {
            jQuery('body').removeClass ("listview gridview");
              jQuery('body').addClass(view + "view");
              $scope.layout = view;
          };
          
          $scope.blueCount = function(arr) {
            return $filter('blueCount')
              ($filter('blueCount')(arr, 'blueCount'));
          };
          $scope.redCount = function(arr) {
            return $filter('redCount')
              ($filter('redCount')(arr, 'redCount'));
          };
        }, function errorCallback() {
          // log error
          alert('I am sorry. There has been an error in retrieving the Maker Faire ribbon data');
        });
    };

  }]);

  

  function shuffle(array) {
    var currentIndex = array.length,
      temporaryValue, randomIndex;

    // While there remain elements to shuffle...
    while (0 !== currentIndex) {

      // Pick a remaining element...
      randomIndex = Math.floor(Math.random() * currentIndex);
      currentIndex -= 1;

      // And swap it with the current element.
      temporaryValue = array[currentIndex];
      array[currentIndex] = array[randomIndex];
      array[randomIndex] = temporaryValue;
    }

    return array;
  }
})(window.angular);

