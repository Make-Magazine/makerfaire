var app = angular.module('mtm', ['ngAnimate', 'ui.bootstrap', 'angular.filter', 'ngSanitize']);

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

app.controller('mtmMakers', ['$scope', '$sce', '$filter', '$http', function ($scope, $sce, $filter, $http) {
        $scope.trust = $sce.trustAsHtml; // for rendering html
        //infinite scroll
        $scope.limit = 20;
        var counter = 0;
        $scope.loadMore = function () {
            $scope.limit += 5;
        };

        $scope.layout = 'grid';
        $scope.category = '';
        $scope.location = '';
        $scope.flag = '';
        $scope.handson = '';
        $scope.tags = [];
        $scope.locations = [];
        $scope.letter = '';
        $scope.makerSearch = [];
        $scope.makerSearch.flag = '';
        $scope.makerSearch.handson = '';
        $scope.makerSearch.categories = '';
        $scope.makerSearch.location = '';
        $scope.alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        catJson = [];
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

        var mp_array = [];
        $scope.makers = [];
        var showMakeProjects = jQuery('#showMakeProjects').val();
        var MPCategory = jQuery('#MPCategory').val();
        if (showMakeProjects !== 'mfonly') {
            //call to make projects
            $http.get('https://makeprojects.com/api/projects/category/' + MPCategory + '?limit=200&offset=0&sort=recent_activity&platform=projects')
                    .then(function successCallback(response) {
                        if (response.data.code == 200) {
                            //alert(response.data.result.total + ' projects found');
                            var mp_projects = response.data.result.projects;
                            //build $scope.makers;                        
                            angular.forEach(mp_projects, function (projects) {
                                //set maker name
                                var makerName = projects.user.fullName;
                                if (makerName == '') {
                                    makerName = projects.user.userName;
                                }

                                //categories
                                var categories = projects.categories;
                                var catName = [];
                                jQuery.each(categories, function (key, category) {
                                    catName.push(category.name);
                                });

                                //set data
                                mp_array.push({'category_id_refs': catName,
                                    'description': projects.description,
                                    'featured_img': projects.image,
                                    'flag': '',
                                    'link': 'https://makeprojects.com/project/' + projects.id,
                                    'large_img_url': projects.image,
                                    'makerList': makerName,
                                    'name': projects.title});
                            });

                            if (showMakeProjects === 'mfandmp') {
                                jQuery.merge($scope.makers, mp_array);
                            } else if (showMakeProjects === 'mponly') {
                                $scope.makers = mp_array;
                            }
                            if (response.data.result.hasMore) {
                                alert('There are more projects to pull');
                            }
                        }
                    }, function errorCallback(error) {
                        alert('Error occured in call to Make: Projects. ' + error.data.code + ' - ' + error.data.messages[0]);
                        console.log(error);
                        jQuery('.mtm .loading').html(noMakerText);
                    }).finally(function () {

            });
        }
        
        //call to MF custom rest API
        $http.get('/wp-json/makerfaire/v2/fairedata/mtm/' + formIDs + '/' + faireID)
                .then(function successCallback(response) {
                    if (response.data.entity.length <= 0) {
                        jQuery('.mtm .loading').html(noMakerText);
                    }

                    jQuery.merge($scope.makers, response.data.entity);

                    var catList = [];
                    var locList = [];
                    angular.forEach($scope.makers, function (maker) {
                        var location = maker.location;
                        if (location != null) {
                            angular.forEach(location, function (loc) {
                                if (locList.indexOf(loc) === -1 && loc !== '') {
                                    locList.push(loc);
                                }
                            });
                        }

                        var categories = maker.categories;
                        if (categories != null) {
                            angular.forEach(categories, function (cat) {
                                if (catList.indexOf(cat) == -1)
                                    catList.push(cat);
                            });
                        }
                    });

                    $scope.tags = catList;
                    $scope.locations = [];
                    if (locList.length > 0) {
                        var collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
                        $scope.locations = locList.sort(collator.compare);
                    }
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
    }]);

app.filter('byCategory', function () {
    // leaving the param in the url would look awkward once users start selecting different filters so let's get rid of it
    if (getUrlParam("category")) {
        window.history.replaceState({}, document.title, window.location.href.split('?')[0]);
    }
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

app.directive('mtmScroll', ['$window', mtmScroll]);
function mtmScroll($window) {
    return {
        link: function (scope, element, attrs) {
            var handler;
            var raw = element[0];
            console.log(raw);
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