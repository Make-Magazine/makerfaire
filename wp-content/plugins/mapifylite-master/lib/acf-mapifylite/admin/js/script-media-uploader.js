var media_container_element_name = '.osm-media-uploader';

jQuery(function($){
	$( media_container_element_name ).each(function(i, obj) {
		
		var frame,
			addImgLink   = $(this).find( '.maptiles-select-image' ),
			delImgLink   = $(this).find( '.maptiles-delete-img' ),
			imgContainer = $(this).find( '.maptiles-img-container' ),
			imgIdInput   = $(this).find( '.maptiles-image-id' ),
			imgURLInput  = $(this).find( '.maptiles-image-url' );

		// Initially hide the image preview if empty
		if ( ! imgContainer.find( 'img' ).length ) {
			imgContainer.hide();
		}

		// Add image link
		addImgLink.on( 'click', function( event ){
			event.preventDefault();
		
			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}

			// Create a new media frame
			frame = wp.media({
				title: 'Select or upload an image',
				button: {
					text: 'Use this image'
				},
				type: 'image',
				multiple: false  // Set to true to allow multiple files to be selected
			});

			// When an image is selected in the media frame...
			frame.on( 'select', function() {      
				// Get media attachment details from the frame state
				var attachment = frame.state().get('selection').first().toJSON();
				var attachment_id = attachment.id;
				var original_url = attachment.url;
				
				// get medium url
				if ( typeof attachment.sizes.medium === 'undefined' ) {
					var medium_url = original_url;
				}
				else {
					var medium_url = attachment.sizes.medium.url;
				}

				// Remove previously image
				imgContainer.find( 'img' ).remove();

				// Send the attachment URL to our custom image input field, then show the image
				imgContainer
					.prepend( '<img src="' + medium_url + '" class="img-fluid img-thumbnail mb-2"/>' )
					.show();			

				// Send the attachment id to our hidden input
				imgIdInput.val( attachment_id );

				// Send the attachment url to our URL input
				imgURLInput.val( original_url );
			});

			frame.open();
		});

		// Delete image link
		delImgLink.on( 'click', function( event ){
			event.preventDefault();

			// Clear out the preview image
			imgContainer.find( 'img' ).remove();
			imgContainer.hide();
			
			// Delete the image id from the hidden input
			imgIdInput.val( '' );
			
			// Clear out the input url
			imgURLInput.val( '' );
		});

	});
});