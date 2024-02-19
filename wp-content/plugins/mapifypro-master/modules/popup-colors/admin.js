(function($, $window, $document){

$('body').on('mpfy_admin_map_updated', function(e) {
	var bg_color = $('input[name="_map_background_color"]').val();
	bg_color = (bg_color) ? bg_color : ''; // #e5e3df

	e.mpfy.settings.bg_color = bg_color;
});

})(jQuery, jQuery(window), jQuery(document));