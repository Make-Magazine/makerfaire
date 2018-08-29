var app = angular.module('mtm', []);

app.controller('mtmMakers', function ($scope, $http) {
   //infinite scroll
   $scope.limit = 20;
   var counter = 0;
   $scope.loadMore = function() {
      $scope.limit += 5;
   };
     
   $scope.layout = 'grid';
   $scope.category = '';
   $scope.location = '';
   $scope.flag = '';
   $scope.tags = [];
   $scope.locations = [];
   $scope.letter = '';
   $scope.makerSearch = [];
   $scope.makerSearch.flag = '';
   $scope.makerSearch.categories = '';
   $scope.makerSearch.location = '';
   $scope.alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
   catJson = [];
   var noMakerText = jQuery('#noMakerText').val();
   var formIDs = jQuery('#forms2use').val();
   var faireID = jQuery('#mtm-faire').val();
   formIDs = replaceAll(formIDs, ",", "-");
      
   //call to MF custom rest API
   $http.get('/wp-json/makerfaire/v2/fairedata/mtm/' + formIDs+'/'+faireID)
   .then(function successCallback(response) {
      if (response.data.entity.length <= 0) {
         jQuery('.mtm .loading').html(noMakerText);
      }
      $scope.makers = response.data.entity;
           
      var catList = [];
      var locList = [];            
      angular.forEach($scope.makers, function (maker) {
         var location = maker.location;
         if(location != null){
            var locArray = location.split(",");
            angular.forEach(locArray, function(loc){
               if (locList.indexOf(loc) === -1 && loc!=='')
                  locList.push(loc);
            });
         }
         var categories = maker.categories;
         if(categories != null){
            var catArray = categories.split(",");
            angular.forEach(catArray, function(cat){
               if (catList.indexOf(cat) == -1)
                  catList.push(cat);
            });
         }
      });
      
      $scope.tags = catList;
      if(locList.length > 0)
         $scope.locations = locList;
   }, 
      function errorCallback(error) {
         console.log(error);
         jQuery('.mtm .loading').html(noMakerText);
      })
   .finally(function () {

   });

   $scope.setLocFilter = function (location) {
      $scope.makerSearch.location = location; 
   };   
   
   $scope.setFlagFilter = function (flag) {
      $scope.flag = flag;      
   };
   
   $scope.setTagFilter = function (tag) {
      $scope.category = tag;
   };
   
   $scope.setLetter = function (startsWith) {
      $scope.letter = startsWith;
   };
   
   // Clear category filter on All button click
   $scope.clearFilter = function () {
      $scope.category = '';
   };
});

app.filter('byCategory', function () {
   return function (items, maker) {
      var filtered = [];

      if (!maker || !items.length) {
         return items;
      }
      
      items.forEach(function (itemElement, itemIndex) {
         itemElement.category_id_refs.forEach(function (categoryElement, categoryIndex) {
            if (categoryElement === maker) {
               filtered.push(itemElement);
               return false;
            }
         });
      });
      return filtered;
   };
});

app.filter('startsWithLetter', function () {
   return function (items, letter) {
      var filtered = [];
      var letterMatch = new RegExp(letter, 'i');
      if(jQuery(items).length){
         for (var i = 0; i < items.length; i++) {
            var item = items[i];
            if (letterMatch.test(item.name.substring(0, 1))) {
               filtered.push(item);
            }
         }
      }
      return filtered;
   };       
});

app.directive('mtmScroll', ['$window', mtmScroll]);  
function mtmScroll($window) {
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
					 scope.$apply(attrs.mtmScroll);
				 }
			 }
        };
        $window.on('scroll', handler);
      }
    };

};


function replaceAll(str, find, replace) {
   return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}
function escapeRegExp(str) {
   return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}