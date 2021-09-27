(function ($) {
    var elementSettings360 = {};

    var WidgetElements_ThreeSixtySliderHandler = function ($scope, $) {
        elementSettings360 = get_Dyncontel_ElementSettings($scope);
        var threesixty = $scope.find('.dce-threesixty');
        if (threesixty) {
            var frames = Number(threesixty.attr('data-total_frame'));
            car = $scope.find('.dce-threesixty').ThreeSixty({
                totalFrames: frames, // Total no. of image you have for 360 slider
                endFrame: frames, // end frame for the auto spin animation
                imgList: '.threesixty_images', // selector for image list
                progress: '.spinner', // selector to show the loading progress
                imagePath: threesixty.attr('data-pathimages'), // path of the image assets
                filePrefix: '', // file prefix if any
                ext: '.' + threesixty.attr('data-format_file'), // extention for the assets
                height: 'auto', //elementSettings360.height.size,
                width: 'auto', //elementSettings360.width.size,
                navigation: Boolean( elementSettings360.navigation ), // false
                responsive: Boolean( elementSettings360.responsive ), // true
				disableSpin: Boolean( elementSettings360.disable_spin ), // false
				playSpeed: Number( elementSettings360.play_speed.size ), // 100	
            });
        }

    };

	

    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-threesixtyslider.default', WidgetElements_ThreeSixtySliderHandler);
    });
})(jQuery);
