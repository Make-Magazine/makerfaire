(function($, $window, $document){

var image_status = mpfy_image_mode.image_status;
var image_source = mpfy_image_mode.image_source;

$('body').on('mpfy_image_mode_status_changed', function(e) {
	image_status = e._response.status;
	$('body').trigger($.Event('mpfy_trigger_map_reload'));
});

$('body').on('mpfy_admin_map_updated', function(e) {
	if (!e.mpfy.settings.efo) {
		return false;
	}

	if ( e.mpfy.settings.mode == 'image' && image_status == 'ready' ) {
		e.mpfy.settings.efo.enableImageMode( image_source + '/z{z}-tile_{y}_{x}.png' );
	}
});

$('body').on('mpfy_admin_map_location_map_updated', function(e) {
	var settings = e.mpfy.settings;

	var image_status = settings.response.image_status;
	var image_source = settings.response.image_source;

	if ( settings.mode == 'image' && image_status == 'ready' ) {
		settings.efo.enableImageMode( image_source + '/z{z}-tile_{y}_{x}.png' );
	}
});

})(jQuery, jQuery(window), jQuery(document));