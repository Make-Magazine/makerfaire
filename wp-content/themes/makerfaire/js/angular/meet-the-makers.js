var mtm = angular.module('mtm', ['angular.filter', 'ngSanitize']);

var initialCategory = "";
if (getUrlParam("category")) {
    initialCategory = getUrlParam("category");
}
var handsOn = "";
if (getUrlParam("handson")) {
    handsOn = getUrlParam("handson");
}
var featured = "";
if (getUrlParam("featured")) {
    featured = getUrlParam("featured");
}

mtm.controller('mtmMakers', ['$scope', '$sce', '$filter', '$http', function ($scope, $sce, $filter, $http) {
        $scope.trust = $sce.trustAsHtml; // for rendering html
        //infinite scroll
        $scope.limit = 10;
        var counter = 0;
        $scope.loadMore = function () {
            $scope.limit += 5;
        };

        $scope.location = '';
        $scope.weekend = '';
        $scope.flag = '';
        $scope.showHandsOn = '';
        $scope.handson = '';
        $scope.locations = [];
        $scope.weekends = [];
        $scope.letter = '';
        $scope.makerSearch = {};
        $scope.makerSearch.flag = '';
        $scope.makerSearch.handson = '';
        $scope.makerSearch.categories = '';
        $scope.makerSearch.location = '';
        $scope.makerSearch.weekend = '';
        $scope.alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $scope.layout = 'grid';
        $scope.category = '';
        $scope.tags = [];
        $scope.makers = [];
        catJson = [];
        var mp_array = [];
        var catName = [];
        var noMakerText = jQuery('#noMakerText').val();

        var formIDs = jQuery('#forms2use').val();
        var faireID = jQuery('#mtm-faire').val();
        formIDs = replaceAll(formIDs, ",", "-");

        if (initialCategory) {
            $scope.makerSearch.categories = initialCategory;
        }

        if (handsOn == "true") {
            $scope.makerSearch.handson = "Featured HandsOn";
        }
        if (featured == "true") {
            $scope.makerSearch.flag = "Featured Maker";
        }

        var showMakeProjects = jQuery('#showMakeProjects').val();
        var MPCategory = jQuery('#MPCategory').val();

        if (showMakeProjects !== 'mfonly' && showMakeProjects != '') {
            //console.log('pulling makeprojects data');
            //call to make projects
            $http.get('https://makeprojects.com/api/projects/category/' + MPCategory + '?limit=200&offset=0&sort=recent_activity&platform=projects', {headers: {'X-Partner': 'make'}})
                    .then(function successCallback(response) {
                        if (response.data.code == 200) {
                            if (response.data.result.projects <= 0) {
                                jQuery('.mtm .loading').html(noMakerText);
                            }

                            var mp_projects = response.data.result.projects;

                            //build $scope.makers;                        
                            angular.forEach(mp_projects, function (projects) {
                                //set maker name
                                var makerName = projects.user.fullName;
                                if (makerName == '') {
                                    makerName = projects.user.userName;
                                }

                                //categories
                                var MPcategories = projects.categories;
                                var catName = [];
                                angular.forEach(MPcategories, function (category) {
                                    catName.push(category.name);
                                });
                                
                                //set data
                                mp_array.push({'id': projects.id,
                                    'categories': catName,
                                    'category_id_refs': catName,
                                    'description': projects.description,
                                    'featured_img': projects.image,
                                    'flag': '',
                                    'handson': '',
                                    'location': ['Make: Projects'],
                                    'link': 'https://makeprojects.com/project/' + projects.id,
                                    'large_img_url': projects.image,
                                    'makerList': makerName,
                                    'name': projects.title});
                            });

                            if (response.data.result.hasMore) {
                                alert('There are more projects to pull');
                            }
                        }
                    }, function errorCallback(error) {
                        alert('Error occured in call to Make: Projects. ' + error.data.code + ' - ' + error.data.messages[0]);
                        console.log(error);
                        jQuery('.mtm .loading').html(noMakerText);
                    })
                    .finally(function () {
                        if (showMakeProjects === 'mfandmp') {
                            jQuery.merge($scope.makers, mp_array);
                        } else if (showMakeProjects === 'mponly') {
                            $scope.makers = mp_array;
                        }      
                        shuffle($scope.makers);
                    });
        }

        //call to MF custom rest API
        if (showMakeProjects !== 'mponly') { //don't pull mf data if admin selected to display projects from makeprojects only
            //console.log('pulling makerfaire data');
            $http.get('/wp-json/makerfaire/v2/fairedata/mtm/' + formIDs + '/' + faireID)
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
                        //console.log($scope.makers);
                    });
        }

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

        //watch the maker variable, if it changes update the location, category and weekend drop downs
        $scope.$watch("makers", function (newValue, oldValue) {
            var catList = [];
            var locList = [];
            var wkndList = [];
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
            //weekends
            $scope.weekends = [];
            if (wkndList.length > 0) {
                var collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
                $scope.weekends = wkndList.sort(collator.compare);
            }   
            //locations
            $scope.locations = [];
            if (locList.length > 0) {
                var collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
                $scope.locations = locList.sort(collator.compare);
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
;


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
