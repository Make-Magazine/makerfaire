(function(angular) {
  'use strict';
  var ribbonApp = angular.module('ribbonApp', ['ngRoute', 'angularUtils.directives.dirPagination']);

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

  ribbonApp.controller('ribbonController', function($scope, $http) {
    $scope.layout      = 'grid';
    $scope.currentPage = 1;
    $scope.pageSize    = 40;
    $scope.faires      = [];

    $scope.loadData = function(year, years) {
      var faireYear = year;
      $scope.years = years || $scope.years;

      $http.get('/wp-content/themes/makerfaire/partials/data/' + faireYear + 'ribbonData.json')
        .then(function successCallback(response) {
          var data = response.data;
          $scope.ribbons = data.json;
          //for random order
          shuffle($scope.ribbons);
          $scope.blueList = data.blueList;
          $scope.redList = data.redList;

          angular.forEach($scope.ribbons, function(row) {
            /* create faires data */
            angular.forEach(row.faireData, function(value) {
              if ($scope.faires.indexOf(value.faire) == -1) {
                $scope.faires.push(value.faire);
              }
            });
            $scope.faires.sort();
          });
        }, function errorCallback() {
          // log error
          alert('error');
        });
    };

  });

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