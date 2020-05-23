var scheduleApp = angular.module('scheduleApp', ['ngAnimate', 'ui.bootstrap', 'angular.filter', 'ngSanitize']);

function ucwords(str) {
    if (str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
    }
}
var dayParam = ucwords(getUrlParam("day"));
var stageParam = ucwords(getUrlParam("stage"));
var typeParam = ucwords(getUrlParam("type"));
var topicParam = ucwords(getUrlParam("topic"));
var weekday = new Array(7);
weekday[0] = "Sunday";
weekday[1] = "Monday";
weekday[2] = "Tuesday";
weekday[3] = "Wednesday";
weekday[4] = "Thursday";
weekday[5] = "Friday";
weekday[6] = "Saturday";

var featured = "";
scheduleApp.controller('scheduleCtrl', ['$scope', '$sce', '$filter', '$http', function ($scope, $sce, $filter, $http) {
        $scope.trust = $sce.trustAsHtml; // for rendering html
        $scope.inFaire = false; //are we during the faire?
        inFaire = false;
        //infinite scroll
        $scope.limit = 5;
        var counter = 0;
        $scope.loadMore = function () {
            $scope.limit += 5;
        };
        $scope.category = '';
        $scope.showSchedules = false;
        $scope.schedSearch = [];
        $scope.schedSearch.nicename = '';
        $scope.schedSearch.category = '';
        $scope.schedSearch.featured = '';

        if (topicParam != undefined) {
            $scope.schedSearch.category = topicParam;
        }
        $scope.schedSearch.type = '';
        //if stage URL parameter is passed, default the stage to this
        if (stageParam != undefined) {
            $scope.schedSearch.nicename = stageParam;
        }
        
        if (featured == "true") {
            $scope.schedSearch.featured = "Featured";
        }
        
        $scope.schedSearch.type = '';
        //if type URL parameter is passed, default the type to this
        if (typeParam != undefined) {
            $scope.schedSearch.type = typeParam;
        }

        /* check faire start and end date 
         * if we are during the faire, default filterdow to current dow */
        var faire_start = new Date(jQuery('#faire_st').val());
        var faire_end = new Date(jQuery('#faire_end').val());
        var todaysDate = new Date();
        $scope.todaysDate = todaysDate;

        $scope.filterdow = "";
        filterdow = "";

        if (todaysDate.getTime() > faire_start.getTime() &&
                todaysDate.getTime() <= faire_end.getTime()) {
            $scope.inFaire = true;
            inFaire = true;
            //todayDOW = weekday[todaysDate.getDay()];
            //$scope.filterdow = todayDOW;
            //filterdow = todayDOW;
        }
        $scope.inFaire = true;
        inFaire = true;
        //if day of the week URL parameter is passed, default the day to this
        if (dayParam != undefined && dayParam != "") {
            $scope.filterdow = dayParam;
            filterdow = dayParam;
        }

        var formIDs = jQuery('#forms2use').val();
        var defType = jQuery('#schedType').val();
        var defDOW = jQuery('#schedDOW').val();
        var faire = jQuery('#faire').val();

        if (formIDs == '')
            alert('error!  Please set the form to pull from on the admin page.');
        // alert('before the call');
        $http.get('/wp-json/makerfaire/v2/fairedata/schedule/' + formIDs + '/' + faire)
                .then(function successCallback(response) {
                    //alert('success');
                    $scope.schedules = response.data.schedule;
                    var dateList = [];
                    var catList = [];
                    angular.forEach($scope.schedules, function (schedule) {
                        defDOW = $filter('date')(schedule.time_start, "EEEE");

                        if (dateList.indexOf(defDOW) == -1)
                            dateList.push(defDOW);

                        var categories = schedule.category;
                        if (categories != null) {
                            var catArray = categories.split(",");
                            angular.forEach(catArray, function (cat) {
                                if (catList.indexOf(cat) == -1)
                                    catList.push(cat);
                            });
                        }
                    });
                    $scope.tags = catList;
                    $scope.dates = dateList.sort();
                }, function errorCallback(error) {
                    //alert('error');
                    //alert(error);
                    console.log(error);
                }).finally(function () {
            //alert('finally');
            $scope.showSchedules = true;
        });


        $scope.setFeaturedFilter = function (featured) {
            $scope.featured = featured;
        };
        $scope.setDateFilter = function (date) {
            $scope.filterdow = $filter('date')(date, "EEEE");
            filterdow = $filter('date')(date, "EEEE");
            buildPrintSchedURL();//add day variable to 
        };
        // console.log("Scope is ", + $scope);
        function buildPrintSchedURL() {
            var faire = jQuery('#faire').val();
            var builtURL = '/stage-schedule/?faire=' + faire + '&orderBy=time&qr=true';

            var schedDOW = $scope.filterdow;
            if (schedDOW !== '') {
                builtURL = builtURL + '&day=' + schedDOW;
            }

            var type = $scope.schedSearch.type;
            if (type !== '') {
                builtURL = builtURL + '&type=' + type;
            }

            var topic = $scope.schedSearch.category;
            if (topic !== '') {
                builtURL = builtURL + '&topic=' + topic;
            }

            var stage = $scope.schedSearch.nicename;
            if (stage !== '') {
                builtURL = builtURL + '&stage=' + stage;
            }

            var textsearch = jQuery('#mtm-search-input').val();
            if (textsearch !== '') {
                builtURL = builtURL + '&text=' + textsearch;
            }

            jQuery("#printSchedule").attr("src", encodeURI(builtURL));
        }
        $scope.$watch('schedSearch.type', function (newV) {
            buildPrintSchedURL();
        })
        $scope.$watch('schedSearch.category', function (newV) {
            buildPrintSchedURL();
        })
        $scope.$watch('schedSearch.nicename', function (newV) {
            buildPrintSchedURL();
        })
        jQuery('#mtm-search-input').change(function () {
            buildPrintSchedURL();
        });
    }]);


scheduleApp.filter('dateFilter', function ($filter) {
    // Create the return function and set the required parameter name to **input**
    return function (schedules, dayOfWeek) {
        if (filterdow != '') {
            var out = [];
            // Loop thru the schedule and return only items that meet the selected date         
            angular.forEach(schedules, function (schedule) {
                if (filterdow === $filter('date')(schedule.time_start, "EEEE")) {
                    out.push(schedule);
                }
            });
        } else {
            var out = schedules;
        }
        return out;
    }
});

scheduleApp.filter('inFaireFilter', function ($filter) {
    // Check if we're in the faire and if the day is filtered to be today, show only the upcoming events for the day
    return function (schedules, todaysDate) {

        if (inFaire == true) {
            var out = [];
            // Loop thru the schedule and return only items that meet the selected date         
            angular.forEach(schedules, function (schedule) {
                var scheduledTime = new Date(schedule.time_end);
                //console.log("Today = " + todaysDate);
                //console.log("Scheduled Time = " + scheduledTime);
                if (todaysDate < scheduledTime) {
                    out.push(schedule);
                }
            });
        } else {
            var out = schedules;
        }
        return out;
    }
});

scheduleApp.directive('schedScroll', ['$window', schedScroll]);
function schedScroll($window) {
    return {
        link: function (scope, element, attrs) {
            var handler;
            var raw = element[0];
            $window = angular.element($window);
            handler = function () {
                if (jQuery(".loading").hasClass("ng-hide")) { // don't start adding to the limit until the loading is done

                    // this convoluted logic down here is basically all about identifying the div directly under the schedule scroll
                    var top_of_element = jQuery(".load-trigger").offset().top;
                    var bottom_of_screen = jQuery(window).scrollTop() + window.innerHeight;
                    if (bottom_of_screen > top_of_element) {
                        scope.$apply(attrs.schedScroll);
                        changeTimeZone(jQuery(".timeZoneSelect").val());
                    }

                }
            };
            $window.on('scroll', handler);
        }
    };
}

function changeTimeZone(tz) {
    jQuery('.sched-col-3').each(function () {
        //start time
        var s = spacetime(jQuery(this).find(".start_dt").text(), 'America/Los_Angeles');
        s = s.goto(tz);
        var dispStartTime = s.format('time');
        jQuery(this).find(".dispStartTime").text(dispStartTime);

        //end time        
        var e = spacetime(jQuery(this).find(".end_dt").text(), 'America/Los_Angeles');
        e = e.goto(tz);
        dispEndTime = e.format('time');
        jQuery(this).find(".dispEndTime").text(dispEndTime);

        // the day
        var day = s.dayName();
        jQuery(this).find(".dispDay").text(day);

    });
}

jQuery(document).ready(function () {
    jQuery(".timeZoneSelect").on("change", function () {
        changeTimeZone(this.value);
    });
});