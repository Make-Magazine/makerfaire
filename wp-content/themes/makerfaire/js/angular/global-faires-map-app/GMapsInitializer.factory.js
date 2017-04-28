(function(angular) {
  'use strict';
  angular.module('faireMapsApp').factory('GMapsInitializer', ['$window', '$q',
    function($window, $q) {
      // &key=AIzaSyBITa21JMkxsELmGoDKQ3owasOW48113w4
      var asyncUrl = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBITa21JMkxsELmGoDKQ3owasOW48113w4&v=3.exp&callback=googleMapsInitialized',
        mapsDefer = $q.defer();
      //Callback function - resolving promise after maps successfully loaded
      $window.googleMapsInitialized = mapsDefer.resolve;
      //Async loader
      var asyncLoad = function(asyncUrl) {
        var script = document.createElement('script');
        script.src = asyncUrl;
        document.body.appendChild(script);
      };
      //Start loading google maps
      asyncLoad(asyncUrl);
      //Usage: GMapsInitializer.then(callback)
      return mapsDefer.promise;
    }
  ]);
})(window.angular);
