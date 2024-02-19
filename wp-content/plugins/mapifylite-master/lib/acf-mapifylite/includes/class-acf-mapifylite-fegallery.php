<?php

/**
 * Responsible for the ACF library field's uploader
 * This script based on Fegallery plugin by Haris
 * 
 * @since    1.0.0
 */

/**
 * Class ACF MapifyLite Fegallery
 * 
 * @since    1.0.0
 */
class Acf_Mapifylite_Fegallery {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct($args = array()) {
		$default_args = array(
			'name' => 'wp-media',
			'button' => '#wp-media-add-slide',
			'container' => '#wp-media',
			'type' => 'image',
			'frame' => 'select',
			'multiple' => 'true',
			'empty_notice' => 'No images yet.'
		);

		$this->args = array_replace($default_args, $args);
		$this->acf_field_name = $args['acf_field_name'];

		// print
		$this->javascript();
	}

	/**
	 * Load scripts that required for this library
	 *
	 * @since    1.0.0
	 */
	private function javascript() {
		// prevent the same name of media modal to double identified
		$varname = $this->args['name'];
		$varname = str_replace('-', '_', $varname);
		$varname = str_replace(' ', '_', $varname);

		?>
		
		<script type='text/javascript'>
		
		if(typeof(fegallery_media_<?php echo $varname ?>) === 'undefined') { 
			var fegallery_media_<?php echo $varname ?> = false;
		}

		if(! fegallery_media_<?php echo $varname ?>){ fegallery_media_<?php echo $varname ?> = true;

		jQuery(document).ready(function($) {
			$('<?php echo $this->args['button'] ?>').on('click',function(e) {
		        var custom_uploader
		        var media_button = $(this);

		        e.preventDefault();

		        // If the uploader object has already been created, reopen the dialog
		        if (custom_uploader) {
		            custom_uploader.open();
		            return;
		        }

		        // Extend the wp.media object
		        custom_uploader = wp.media.frames.file_frame = wp.media({
		            frame: '<?php echo $this->args['frame'] ?>',
		            title: 'Choose <?php echo $this->args['type'] ?>',
		            library : { type : '<?php echo $this->args['type'] ?>'},
		            button: {
		                text: 'Choose <?php echo $this->args['type'] ?>'
		            },
		            multiple: <?php echo $this->args['multiple'] ?>
		        });
		 
		        // When a file is selected, grab the URL and set it as the text field's value
		        custom_uploader.on('select', function() {
		        	var container = $('<?php echo $this->args['container'] ?>');
		            var attachments = custom_uploader.state().get('selection').toJSON();

		            if(attachments.length > 0 && container.children('span').length > 0) {
		            	container.children('span').remove();
		            }

					$.each(attachments, function(index, value){
						var attachment_id = value.id;
						var original_url  = value.url;

						// get thumbnail url
						if ( typeof value.sizes.thumbnail === 'undefined' ) {
							var thumbnail_url = original_url;
						}
						else {
							var thumbnail_url = value.sizes.thumbnail.url;
						}

						// get medium url
						if ( typeof value.sizes.medium === 'undefined' ) {
							var medium_url = original_url;
						}
						else {
							var medium_url = value.sizes.medium.url;
						}

						var string = '<div class="fegallery-image-item">';
							string+= '<img src="' + medium_url + '" >';
							string+= '<a href="javascript:;" class="fegallery-edit-item"><span class="dashicons dashicons-edit"></span></a>';
							string+= '<a href="javascript:;" class="fegallery-close-item"><span class="dashicons dashicons-no"></span></a>';
							string+= '<input type="hidden" name="<?php echo esc_attr( $this->acf_field_name ) . '[image_id][]' ?>" class="image-id-field" value="' + attachment_id + '" >';
							string+= '<input type="hidden" name="<?php echo esc_attr( $this->acf_field_name ) . '[image_url][]' ?>" class="image-url-field" value="' + original_url + '" >';
							string+= '<input type="hidden" name="<?php echo esc_attr( $this->acf_field_name ) . '[image_thumb][]' ?>" value="' + thumbnail_url + '" >';
							string+= '</div>';

						container.append(string);
					});
		        });
		 
		        // Open the uploader dialog
		        custom_uploader.open();	 
		    });

		    // remove item
		    $('<?php echo $this->args['container'] ?>').on('click', '.fegallery-close-item', function(){
		    	var container = $('<?php echo $this->args['container'] ?>');
		    	
		    	$(this).parent('.fegallery-image-item').remove();		    	

		    	if(container.children('.fegallery-image-item').length <= 0) {
		    		container.html("<?php echo $this->args['empty_notice'] ?>");
		    	}
		    });

		    // sortable
		    $('<?php echo $this->args['container'] ?>').sortable({
		      	placeholder: "fegallery-sortable-placeholder",
		      	cancel: ".fegallery-close-item"
		    });

		    $('<?php echo $this->args['container'] ?>').disableSelection();
		});		

		} // if fegallery_media_$varname";

		</script>

		<?php
	}
}	