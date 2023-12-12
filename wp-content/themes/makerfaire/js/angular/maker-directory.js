var makerdir = angular.module('makerdir', ['angular.filter', 'ngSanitize']);

var initialCategory = "";
if (getUrlParam("category")) {
    initialCategory = getUrlParam("category");
}

makerdir.controller('mdirMakers', ['$scope', '$sce', '$filter', '$http', function ($scope, $sce, $filter, $http) {    
        $scope.trust = $sce.trustAsHtml; // for rendering html
        //infinite scroll
        $scope.limit = 10;
        var counter = 0;
        $scope.loadMore = function () {
            $scope.limit += 5;
        };
        
        $scope.flag = '';
        $scope.letter = '';

        $scope.makerSearch = {};
        $scope.makerSearch.flag = '';
        $scope.makerSearch.categories = '';
        $scope.makerSearch.faire = '';

        $scope.alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $scope.layout = 'grid';
        $scope.category = '';
        
        //faire search
        $scope.faire = '';
        $scope.faires = [];        

        $scope.tags = [];
        $scope.makers = [];
        catJson = [];
                
        var noMakerText = jQuery('#noMakerText').val();

        var years = jQuery('#years2use').val();
        years = replaceAll(years, ",", "-");                
        
        if (initialCategory) {
            $scope.makerSearch.categories = initialCategory;
        }

        //call to MF custom rest API                
        $http.get('/wp-json/makerfaire/v2/fairedata/makerDir/' + years)
            .then(function successCallback(response) {
                if (response.data.entity.length <= 0) {
                    jQuery('.mtm .loading').html(noMakerText);
                }

                jQuery.merge($scope.makers, response.data.entity);
                shuffle($scope.makers);

            }, function errorCallback(error) {
                console.log(error);
                jQuery('.mtm .loading').html(noMakerText);
            })
            .finally(function () {
                console.log($scope.makerSearch);
            })  ;
        
        
        $scope.setFlagFilter = function (flag) {
            $scope.flag = flag;
        };

        $scope.setTagFilter = function (tag) {
            $scope.category = tag;
        };

        $scope.setFaireFilter = function (faire) {
            $scope.makerSearch.faire = faire;
        };

        $scope.setLetter = function (startsWith) {
            $scope.letter = startsWith;
        };

        // Clear category filter on All button click
        $scope.clearFilter = function () {
            $scope.category = '';
        };

        //watch the maker variable, if it changes update the category drop downs
        $scope.$watch("makers", function (newValue, oldValue) {
            var catList = [];
            var faireList = [];

            angular.forEach($scope.makers, function (maker) {    
                //faires
                var faireName = maker.faire_name;
                if (faireName != null) {                    
                    if (faireList.indexOf(faireName) === -1 && faireName !== '') {
                        faireList.push(faireName);
                    }                    
                }

                var categories = maker.categories;
                //reset the category ids to the category names
                maker.category_id_refs = categories;
				// sometimes we get arrays for some reason, and that causes unsightly ng errors
				if(maker.makerList && Array.isArray(maker.makerList)) {
					maker.makerList = " ";
				}

                if (categories != null) {
                    angular.forEach(categories, function (cat) {
                        if (catList.indexOf(cat) == -1)
                            catList.push(cat);
                    });
                }
            });

            //categories
            $scope.tags = catList;                      

            //faires
            $scope.faires = [];
            if (faireList.length > 0) {
                var collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
                $scope.faires = faireList.sort(collator.compare);
            }  
        }, true);
    }]);


    makerdir.filter('startsWithLetter', function () {
    return function (items, letter) {
        var filtered = [];
        var letterMatch = new RegExp(letter, 'i');
        if (jQuery(items).length) {
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

makerdir.directive('makerdirScroll', ['$window', makerdirScroll]);
function makerdirScroll($window) {
    return {
        link: function (scope, element, attrs) {
            var handler;
            var raw = element[0];
            //console.log(raw);
            $window = angular.element($window);
            handler = function () {
                if (jQuery(".loading").hasClass("ng-hide")) { // don't start adding to the limit until the loading is done
                    var top_of_element = jQuery(".load-trigger").offset().top;
                    var bottom_of_screen = jQuery(window).scrollTop() + window.innerHeight;
                    if (bottom_of_screen > top_of_element) {
                        scope.$apply(attrs.makerdirScroll);
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


function shuffle(array) {
  var currentIndex = array.length, temporaryValue, randomIndex;

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
