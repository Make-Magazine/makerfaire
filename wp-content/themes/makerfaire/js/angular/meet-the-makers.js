var app = angular.module('mtm', []);

app.controller('mtmMakers', function ($scope, $http) {
   $scope.layout = 'grid';
   $scope.category = '';
   $scope.flag = '';
   $scope.tags = [];
   $scope.letter = '';
   $scope.alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
   catJson = [];
   var noMakerText = jQuery('#noMakerText').val();
   var formIDs = jQuery('#forms2use').val();
   formIDs = replaceAll(formIDs, ",", "-");
   //formIDs = formIDs.replace(",","-");
   //to be added - replace commas with - in form ids
   //call to MF custom rest API
   $http.get('/wp-json/makerfaire/v2/fairedata/mtm/' + formIDs)
   .then(function successCallback(response) {
      if (response.data.entity.length <= 0) {
         jQuery('.mtm .loading').html(noMakerText);
      }
      $scope.makers = response.data.entity;
      
      //build array of categories
      angular.forEach(response.data.category, function (catArr) {
         catJson[catArr.id] = catArr.name.trim();
      });
      var catList = [];
      
      //Owl carousel does not like to work with a ng-repeat so the images must be build and loaded
      var carouselImgs = '';
      angular.forEach($scope.makers, function (maker) {
         var categories = [];
         /* input categories are in an array
          This will compare them to the catJson to get the category name,
          and add to the category list if it's not there  */
         angular.forEach(maker.category_id_refs, function (catID) {
            catID = catID.trim();
            if (catID != '') {
               var addCat = catID;
               //look up cat id in the category json file to find the matching category name
               if (catID in catJson) {
                  addCat = catJson[catID];
               }
               categories.push(addCat);
               //create a unique list of category names for a filter drop down
               if (catList.indexOf(addCat) == -1)
                  catList.push(addCat);
            }
         });
         //reset the category ids to the category names
         maker.category_id_refs = categories;
      });
      $scope.tags = catList;

   }, 
      function errorCallback(error) {
         console.log(error);
         jQuery('.mtm .loading').html(noMakerText);
      })
   .finally(function () {

   });
   
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
      for (var i = 0; i < items.length; i++) {
         var item = items[i];
         if (letterMatch.test(item.name.substring(0, 1))) {
            filtered.push(item);
         }
      }
      return filtered;
   };
});

function replaceAll(str, find, replace) {
   return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}
function escapeRegExp(str) {
   return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}