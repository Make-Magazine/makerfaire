var essbShowFreeAddonInstallation = function() {
	swal('Thank you for downloading a free extension!', 'When the file download is completed go to your WordPress plugin menu and install the file like other plugins. All settings of the extension will appear integrated inside plugin or under plugin menu Easy Social Share Buttons for WordPress.', 'success');
};

jQuery(document).ready(function($){

	"use strict";

	var essbRemoveFilterActiveState = function() {
		$('.essb-wp-filters').find('.essb-filter-addon').each(function() {
			if ($(this).hasClass('current'))
				$(this).removeClass('current');
		});
	}

	if ($('.essb-wp-filters').length) {
		$('.essb-wp-filters').find('.essb-filter-addon').each(function() {
			$(this).on('click', function(e) {
				e.preventDefault();

				if ($(this).hasClass('current')) return;

				essbRemoveFilterActiveState();
				$(this).addClass('current');

				var filterFor = $(this).attr("data-filter") || "";
				var filterClass = 'essb-addon-filter-'+filterFor;

				$('.extensions-list').hide();

				var count = 0;
				$('.extensions-list').find('.extension').each(function() {
					if ($(this).hasClass(filterClass)) {
						$(this).show();
						count++;
					}
					else
						$(this).hide();
				});
				$('.essb-active-count').text(count);
				$('.extensions-list').show();
			});
		});
	}
});