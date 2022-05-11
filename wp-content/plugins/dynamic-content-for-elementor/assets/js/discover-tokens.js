(function ($) {
    var widgetElementsDiscoverTokens = function ($scope, $) {
		discoverTokens = $scope[0];
		let copyInstances = discoverTokens.querySelectorAll("span.copy");

		copyInstances.forEach(function(copy) {
			tippy( copy, {
				content: 'Copied!',
				arrow: true,
				trigger:'click',
			});
			new ClipboardJS(copy);
		});
    };

    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dce-discover-tokens.default', widgetElementsDiscoverTokens);
    });
})(jQuery);
