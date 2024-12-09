var mtm = angular.module('mtm', ['angular.filter', 'ngSanitize']);

//filter by Location with URL
var initialLocation = "";
if (getUrlParam("location")) {
    initialLocation = getUrlParam("location");
}

//filter by Category with URL
var initialCategory = "";
if (getUrlParam("category")) {
    initialCategory = getUrlParam("category");
}

//filter by Entry Type with URL
var initialType = "";
if (getUrlParam("type")) {
    initialType = getUrlParam("type");
}

//Toggle Hands on filter with URL
var handsOn = "";
if (getUrlParam("handson")) {
    handsOn = getUrlParam("handson");
}

//Toggle Featured filter with URL
var featured = "";
if (getUrlParam("featured")) {
    featured = getUrlParam("featured");
}

// filter by layout with url
var layout = "grid";
if (getUrlParam("layout")) {
    layout = getUrlParam("layout");
}

mtm.controller('mtmMakers', ['$scope', '$sce', '$filter', '$http', function ($scope, $sce, $filter, $http) {
    $scope.trust = $sce.trustAsHtml; // for rendering html
    //infinite scroll
    $scope.limit = 12;
    var counter = 0;
    $scope.loadMore = function () {
        $scope.limit += 12;
    };

    $scope.location = '';
    $scope.weekend = '';
    $scope.flag = '';
    $scope.showHandsOn = '';
    $scope.handson = '';
    $scope.locations = [];
    $scope.types = [];
    $scope.weekends = [];
    $scope.letter = '';
    $scope.makerSearch = {};
    $scope.makerSearch.flag = '';
    $scope.makerSearch.handson = '';
    $scope.makerSearch.categories = '';
    $scope.makerSearch.types = '';
    $scope.makerSearch.location = '';
    $scope.makerSearch.weekend = '';
    $scope.alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    $scope.layout = layout;
    $scope.category = '';
    $scope.type = '';
    $scope.tags = [];
    $scope.makers = [];
    catJson = [];
    
    var noMakerText = jQuery('#noMakerText').val();

    var formIDs = jQuery('#forms2use').val();
    var faireID = jQuery('#mtm-faire').val();
    formIDs = replaceAll(formIDs, ",", "-");

    if (initialLocation) {
        $scope.makerSearch.location = initialLocation;
    }

    if (initialCategory) {
        $scope.makerSearch.categories = initialCategory;
    }

    if (initialType) {
        $scope.makerSearch.types = initialType;
    }

    if (handsOn == "true") {
        $scope.makerSearch.handson = "Featured HandsOn";
    }
    if (featured == "true") {
        $scope.makerSearch.flag = "Featured Maker";
    }

    $scope.changeView = function (view) {
        jQuery('body').removeClass ("listview gridview makerview");
        jQuery('body').addClass(view + "view");
        $scope.layout = view;
    };

    //call to MF custom rest API

    //console.log('pulling makerfaire data');
    $http.get('/wp-json/makerfaire/v2/fairedata/mtm/' + formIDs + '/' + faireID)
        .then(function successCallback(response) {
            if (response.data.entity.length <= 0) {
                jQuery('.mtm .loading').html(noMakerText);
            }

            jQuery.merge($scope.makers, response.data.entity);
            //console.log($scope.makers);
            shuffle($scope.makers);

        }, function errorCallback(error) {
            console.log(error);
            jQuery('.mtm .loading').html(noMakerText);
        })
        .finally(function () {
            //console.log($scope.makers);
        });
    
    $scope.trustedHTML = function(html_code) {
        return $sce.trustAsHtml(html_code);
    };

    $scope.setLocFilter = function (location) {
        $scope.makerSearch.location = location;
    };

    $scope.setWkndFilter = function (weekend) {
        $scope.makerSearch.weekend = weekend;
    };

    $scope.setFlagFilter = function (flag) {
        $scope.flag = flag;
    };

    $scope.setHandsonFilter = function (handson) {
        $scope.handson = handson;
    };

    $scope.setTypeFilter = function (type) {
        $scope.type = type;
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
        $scope.type = '';
    };

    //watch the maker variable, if it changes update the location, category and weekend drop downs
    $scope.$watch("makers", function (newValue, oldValue) {
        var catList = [];
        var locList = [];
        var wkndList = [];
        var typeList = [];
        angular.forEach($scope.makers, function (maker) {
            //weekends
            var weekend = maker.weekend;
            if (weekend != null) {
                angular.forEach(weekend, function (wknd) {
                    if (wkndList.indexOf(wknd) === -1 && wknd !== '') {
                        wkndList.push(wknd);
                    }
                });
            }

            //locations
            var location = maker.location;
            if (location != null) {
                angular.forEach(location, function (loc) {
                    if (locList.indexOf(loc) === -1 && loc !== '') {
                        locList.push(loc);
                    }
                });
            }

            var categories = maker.categories;
            //reset the category ids to the category names
            maker.category_id_refs = categories;
            // sometimes we get arrays for some reason, and that causes unsightly ng errors
            if (maker.makerList && Array.isArray(maker.makerList)) {
                maker.makerList = " ";
            }

            if (categories != null) {
                angular.forEach(categories, function (cat) {
                    if (catList.indexOf(cat) == -1)
                        catList.push(cat);
                });
            }

            var types = maker.types;
            if (types != null) {
                angular.forEach(types, function (type) {
                    if (typeList.indexOf(type) === -1 && type !== '') {
                        typeList.push(type);
                    }
                });
            }
            maker.typeString = types.join(", ");
        });

        //categories
        $scope.tags = catList;
        //weekends
        $scope.weekends = [];
        if (wkndList.length > 0) {
            var collator = new Intl.Collator(undefined, { numeric: true, sensitivity: 'base' });
            $scope.weekends = wkndList.sort(collator.compare);
        }
        //locations
        $scope.locations = [];
        if (locList.length > 0) {
            var collator = new Intl.Collator(undefined, { numeric: true, sensitivity: 'base' });
            $scope.locations = locList.sort(collator.compare);
        }
        //exhibit types
        $scope.types = [];
        if (typeList.length > 0) {
            var collator = new Intl.Collator(undefined, { numeric: true, sensitivity: 'base' });
            $scope.types = typeList.sort(collator.compare);
        }
    }, true);
}]);


mtm.filter('startsWithLetter', function () {
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

mtm.directive('mtmScroll', ['$window', mtmScroll]);
function mtmScroll($window) {
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
                        scope.$apply(attrs.mtmScroll);
                    }
                }
            };
            $window.on('scroll', handler);
        }
    };

}

mtm.directive('onError', function() {
    return {
      restrict:'A',
      link: function(scope, element, attr) {
        element.on('error', function() {
          element.attr('src', attr.onError);
        })
      }
    }
  })
  


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
