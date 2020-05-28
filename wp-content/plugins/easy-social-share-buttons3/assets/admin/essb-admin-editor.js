( function() {
	"use strict";
	tinymce.PluginManager.add( 'essb_media_shortcodes', function( editor, url ) {

		// Add a button that opens a window
		editor.addButton( 'essb_media_shortcodes', {

			text: '',
			tooltip: 'Easy Social Share Buttons Shortcode Generator',
			icon: 'essb-sc-generator',
			
		});
	});	
})();