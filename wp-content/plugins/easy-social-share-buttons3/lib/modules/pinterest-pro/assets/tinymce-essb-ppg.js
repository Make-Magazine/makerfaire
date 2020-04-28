( function() {
	tinymce.PluginManager.add( 'essb_ppg', function( editor, url ) {

		// Add a button that opens a window
		editor.addButton( 'essb_ppg', {

			text: '',
			tooltip: 'Pinterest Pro Gallery by Easy Social Share Buttons',
			icon: 'essb-ppg-image',
			onclick: function() {
				// Open window
				editor.windowManager.open( {
					title: 'Pinterest Pro Gallery by Easy Social Share Buttons',
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
							name: 'columns',
							label: 'Columns',
							values: [
							   { text: '1 Column', value: '1'},
							   { text: '2 Columns', value: '2'},
							   { text: '3 Columns', value: '3'},
							   { text: '4 Columns', value: '4'}
							]
						},
						{
							type: 'textbox',
							name: 'images',
							label: 'Image URL',
							classes: 'essb-ppg-image',
							tooltip: 'Fill the image URL that will be used. If you do not specify custom Pin image URL than this will also be used as Pin image'
						},
						{
							type: 'container',
							html: '<button class="button button-primary" onclick="essbOpenCustomImagesSelector(\'essb-ppg-image\');">Choose Image ...</button>',
							label: ' ',
						},
						{
							type: 'textbox',
							name: 'custom_classes',
							label: 'Custom Container Classes',
							tooltip: 'Add in case of need a custom class definitation. This custom class will be added to the main Pinable image holding element'
						},
						{
							type: 'checkbox',
							checked: true,
							name: 'adjust',
							value: true,
							text: 'Yes, adjust images height',
							label: 'Equal Images Height?',
						},
						{
							type: 'textbox',
							name: 'spacing',
							label: 'Add Space Between Images',
							tooltip: 'Fill a numeric value in case you need to add spacing between images'
						},
					],
					width: 620,
					height: 360,
					onsubmit: function( e ) {

						// bail without tweet text
						if ( e.data.images === '' ) {
							return;
						}
						
						// build my content
						var shortcodeBuilder = '';

						// set initial
						shortcodeBuilder  += '[pinterest-gallery';
						if (e.data.message != '') shortcodeBuilder += ' message="' + e.data.message + '"';
						if (e.data.columns != '') shortcodeBuilder += ' columns="' + e.data.columns + '"';
						if (e.data.images != '') shortcodeBuilder += ' images="' + e.data.images + '"';
						if (e.data.custom_classes != '') shortcodeBuilder += ' class="' + e.data.custom_classes + '"';
						if (e.data.spacing != '') shortcodeBuilder += ' spacing="' + e.data.spacing + '"';
						if (e.data.adjust != '') shortcodeBuilder += ' adjust="yes"';
						
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
	
	var essb_gcustom_image_selector, essb_pastgSender;
	
	var essbOpenCustomImagesSelector = window.essbOpenCustomImagesSelector = function(sender) {
		essb_pastgSender = sender;
		
		if (essb_gcustom_image_selector) {
			essb_gcustom_image_selector.open();
            return;
        }
 
        //Extend the wp.media object
		essb_gcustom_image_selector = wp.media.frames.file_frame = wp.media({
            title: 'Select File',
            button: {
                text: 'Select File'
            },
            multiple: true
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
		essb_gcustom_image_selector.on('select', function() {
			var attachments = essb_gcustom_image_selector.state().get('selection').toJSON(), ids = '';
			for (var i=0;i<attachments.length;i++) {
				ids += (ids != '' ? ',' : '') + attachments[i].id;
			}

            if (jQuery('.mce-'+essb_pastgSender).length) {
            	jQuery('.mce-'+essb_pastgSender).val(ids);
            }
            //$('#essb_options_<?php echo $field_id; ?>').val(attachment.url);
        });
 
        //Open the uploader dialog
		essb_gcustom_image_selector.open();
	}
})();