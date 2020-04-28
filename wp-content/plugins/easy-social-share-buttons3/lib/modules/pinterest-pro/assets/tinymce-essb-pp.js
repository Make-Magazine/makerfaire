( function() {
	tinymce.PluginManager.add( 'essb_pp', function( editor, url ) {

		// Add a button that opens a window
		editor.addButton( 'essb_pp', {

			text: '',
			tooltip: 'Pinterest Pro Image by Easy Social Share Buttons',
			icon: 'essb-pp-image',
			onclick: function() {
				// Open window
				editor.windowManager.open( {
					title: 'Pinterest Pro Image by Easy Social Share Buttons',
					body: [
						{
							type: 'textbox',
							name: 'message',
							label: 'Custom Pin Description',
							multiline : true,
							minHeight : 60
						},
						{
							type: 'listbox',
							name: 'type',
							label: 'Type',
							tooltip: 'If you select post type mode the plugin will generate a Pin with the customized data from your post details',
							values: [
							   { text: 'Pin Custom Image', value: ''},
							   { text: 'Pin Post Custom Pinterest Data', value: 'post'}
							]
						},
						{
							type: 'listbox',
							name: 'align',
							label: 'Align',
							values: [
							   { text: 'Default', value: ''},
							   { text: 'Center', value: 'center'},
							   { text: 'Left', value: 'left'},
							   { text: 'Right', value: 'right'}
							]
						},
						{
							type: 'textbox',
							name: 'image',
							label: 'Image URL',
							classes: 'essb-pp-main-image',
							tooltip: 'Fill the image URL that will be used. If you do not specify custom Pin image URL than this will also be used as Pin image'
						},
						{
							type: 'container',
							html: '<button class="button button-primary" onclick="essbOpenCustomImageSelector(\'essb-pp-main-image\');">Choose Image ...</button>',
							label: ' ',
						},
						{
							type: 'textbox',
							name: 'custom_image',
							label: 'Custom Pin Image URL',
							classes: 'essb-pp-custom-image',
							tooltip: 'Fill a custom image URL for Pinning. This image will not appear visible inside content'
						},
						{
							type: 'container',
							html: '<button class="button button-primary" onclick="essbOpenCustomImageSelector(\'essb-pp-custom-image\');">Choose Image ...</button>',
							label: ' ',
						},
						{
							type: 'textbox',
							name: 'custom_classes',
							label: 'Custom Container Classes',
							tooltip: 'Add in case of need a custom class definitation. This custom class will be added to the main Pinable image holding element'
						}
					],
					width: 620,
					height: 360,
					onsubmit: function( e ) {

						// bail without tweet text
						if ( e.data.message === '' && e.data.image === '' && e.data.custom_image === '' && e.data.type === '' ) {
							return;
						}
						
						// build my content
						var shortcodeBuilder = '';

						// set initial
						shortcodeBuilder  += '[pinterest-image';
						if (e.data.message != '') shortcodeBuilder += ' message="' + e.data.message + '"';
						if (e.data.type != '') shortcodeBuilder += ' type="' + e.data.type + '"';
						if (e.data.align != '') shortcodeBuilder += ' align="' + e.data.align + '"';
						if (e.data.image != '') shortcodeBuilder += ' image="' + e.data.image + '"';
						if (e.data.custom_image != '') shortcodeBuilder += ' custom_image="' + e.data.custom_image + '"';
						if (e.data.custom_classes != '') shortcodeBuilder += ' class="' + e.data.custom_classes + '"';
						
						// close it up
						shortcodeBuilder  += ']';

						// Insert content when the window form is submitted
						editor.insertContent( shortcodeBuilder );
					}
				});
			}
		});
	});
	
	//-- assigning a media message selection
	
	var essb_custom_image_selector, essb_pastSender;
	
	var essbOpenCustomImageSelector = window.essbOpenCustomImageSelector = function(sender) {
		essb_pastSender = sender;
		
		if (essb_custom_image_selector) {
			essb_custom_image_selector.open();
            return;
        }
 
        //Extend the wp.media object
		essb_custom_image_selector = wp.media.frames.file_frame = wp.media({
            title: 'Select File',
            button: {
                text: 'Select File'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
		essb_custom_image_selector.on('select', function() {
            attachment = essb_custom_image_selector.state().get('selection').first().toJSON();
            if (jQuery('.mce-'+essb_pastSender).length) {
            	jQuery('.mce-'+essb_pastSender).val(attachment.url);
            }
            //$('#essb_options_<?php echo $field_id; ?>').val(attachment.url);
        });
 
        //Open the uploader dialog
		essb_custom_image_selector.open();
	}
})();