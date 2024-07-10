/**
 * @namespace WPGMZA
 * @module MarkerSeparatorSettingsPanel
 */
jQuery(function($) {
	
	$(document).ready(function(event) {
		
		var el = $("#marker-separator-placeholder-icon-picker-container > .wpgmza-marker-icon-picker");
		
		if(!el.length)
			return;
		
		new WPGMZA.MarkerIconPicker(el);
		
	});
	
});