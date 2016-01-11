// Code goes here
var ribbonApp = angular.module('ribbonApp', ['ngRoute', 'angularUtils.directives.dirPagination']);

ribbonApp.directive('fallbackSrc', function() {
  var fallbackSrc = {
    link: function postLink(scope, iElement, iAttrs) {
      iElement.bind('error', function() {
        angular.element(this).attr("src", iAttrs.fallbackSrc);
      });
    }
  };
  return fallbackSrc;
});

ribbonApp.controller('ribbonController', function($scope, $http) {
  $scope.vm = {}; // viewmodel

  $scope.vm.layout = 'grid';
  $scope.vm.currentPage = 1;
  $scope.vm.faires = [];
  $scope.vm.years = yearJson;
  
  $scope.vm.loadData = function(faireYear) {
    var postData = {
      'action': 'getRibbonData',
      'year': faireYear
    };
    $http.get('/wp-content/themes/makerfaire/partials/data/' + faireYear + 'ribbonData.json').success(function(data) {
      $scope.vm.ribbons = data.json;
      //for random order
      shuffle($scope.vm.ribbons);
      $scope.vm.blueList = data.blueList;
      $scope.vm.redList = data.redList;

      angular.forEach($scope.vm.ribbons, function(row, key) {
        /* create faires data */
        angular.forEach(row.faireData, function(value, faire) {
          if ($scope.vm.faires.indexOf(value.faire) == -1) {
            $scope.vm.faires.push(value.faire);
          }
        });
        $scope.vm.faires.sort();
      });
    }).
    error(function(data, status, headers, config) {
      // log error
      alert('error');
    });
  };

  //initial load   
  var faireYear = $scope.vm.years[0].name;
  $scope.vm.loadData(faireYear);

  $scope.vm.pageChangeHandler = function(num) {
    /*console.log('meals page changed to ' + num);*/
  };
  
  function shuffle(array) {
    var currentIndex = array.length,temporaryValue, randomIndex;

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

});

