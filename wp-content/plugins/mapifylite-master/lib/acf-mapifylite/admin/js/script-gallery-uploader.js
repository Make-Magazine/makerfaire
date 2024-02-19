jQuery(document).ready(function($) {
	var current_edited_image_url_field;
	var input_image_url = $('#input-image-url');

	$('#fegallery-edit-modal').dialog({
		'width'    : 600,
		'modal'    : true,
		'autoOpen' : false,
		'appendTo' : '#fegallery-modal-container'
	});

	$('#fegallery-image-container').on( 'click', '.fegallery-edit-item', function(){
		current_edited_image_url_field = $(this).siblings('.image-url-field');
		var current_image_url          = current_edited_image_url_field.val();
		
		input_image_url.val(current_image_url);
		$('#fegallery-edit-modal').dialog('open');
	});

	$('#fegallery-update-image-url').click(function(){
		var new_image_url = input_image_url.val();

		/**
		 * Set new custom image url
		 * Also set the thumbnail and the class
		 */
		if ( '' != new_image_url && current_edited_image_url_field.val() != new_image_url ) {
			current_edited_image_url_field.val(new_image_url);
			current_edited_image_url_field.siblings('.image-id-field').val('');
			current_edited_image_url_field.siblings('img').attr('src', new_image_url);
			current_edited_image_url_field.parents('.fegallery-image-item').addClass('use-custom-url');
		}

		// close dialog
		$('#fegallery-edit-modal' ).dialog('close');
	});
});