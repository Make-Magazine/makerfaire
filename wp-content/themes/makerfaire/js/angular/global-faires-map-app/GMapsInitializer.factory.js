(function(angular) {
  'use strict';
  angular.module('faireMapsApp').factory('GMapsInitializer', ['$window', '$q',
    function($window, $q) {
      // &key=AIzaSyCJHKq4cnlAd4WkaJKYal-8n6V1Y8MCPxw
      var asyncUrl = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCJHKq4cnlAd4WkaJKYal-8n6V1Y8MCPxw&v=3.exp&callback=googleMapsInitialized',
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
