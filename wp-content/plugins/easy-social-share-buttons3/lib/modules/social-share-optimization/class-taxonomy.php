<?php

/**
 * @package EasySocialShareButtons\SocialShareOptimization
 * @author appscreo
 * @since 5.9
 *
 * Generate and manage optimization fields for taxonomies
 */

class ESSB_TaxonomyOptimizations {
	
	private $fields = array('sso_title' => array('text' => 'Social Media Title', 'type' => 'text'), 'sso_desc' => array('text' => 'Social Media Description', 'type' => 'textarea'), 'sso_image' => array('text' => 'Social Media Image', 'type' => 'image'));
	
	public function __construct() {		
		add_action ( 'admin_init', array($this, 'init') );
	}
	
	public function init() {
		add_action( 'created_term', array($this, 'save_meta_tags') );
		add_action( 'edit_term', array($this, 'save_meta_tags') );
		add_action( 'delete_term', array($this, 'delete_meta_tags') );
		
		$wptm_taxonomies = get_taxonomies('','names');
		
		if (is_array($wptm_taxonomies) ) {
		
			foreach ($wptm_taxonomies as $wptm_taxonomy ) {		
				add_action( $wptm_taxonomy . '_edit_form', array($this, 'add_meta_textinput') );
		
			}
		
		}
	}
	
	public function save_meta_tags($id) {
		
		$wptm_edit = $_POST["wptm_sso_edit"];
		if (isset($wptm_edit) && !empty($wptm_edit)) {
			foreach ($this->fields as $field_id => $field_data) {
				$inputValue = $_POST['essb_wptm_'.$field_id];
				delete_term_meta( $id, $field_id );
				
				if (isset($inputValue) && !empty($inputValue)) {				
					add_term_meta($id, $field_id, $inputValue);
				}
			}
		}
	}
	
	public function delete_meta_tags($id) {
		foreach ($this->fields as $field_id => $field_data) {
			delete_term_meta( $id, $field_id );
		}
	}
	
	public function add_meta_textinput($tag) {
		global $category, $wp_version, $taxonomy;
		
		$category_id = '';
		
		if ($wp_version >= '3.0') {
			$category_id = (is_object($tag))?$tag->term_id:null;
		
		} else {
			$category_id = $category;		
		}
		
		?>
		<h3 class='hndle'><span><?php _e('Social Sharing Optimization', 'essb');?></span></h3>
        
        <div class="inside">
            
            <input value="wptm_sso_edit" type="hidden" name="wptm_sso_edit" /> 
            <input type="hidden" name="image_field" id="image_field" value="" />
            <table class="form-table">		
		<?php 
		
		foreach ($this->fields as $field_id => $field_data) {
			$field_title = $field_data['text'];
			$field_type = $field_data['type'];
			
			$inputValue = htmlspecialchars(stripcslashes(get_term_meta($category_id, $field_id, true)));
			
			if ($field_type == 'text') { ?>
			                        
			                    	<tr class="form-field">
			                    		<th scope="row" valign="top">
			                                <label for="category_nicename"><?php echo $field_title;?></label>
			                            </th>
			                    		<td>
			                                <input value="<?php echo $inputValue ?>" type="text" size="40" name="<?php echo 'essb_wptm_'.$field_id;?>" /><br />
			                            </td>
			                    	</tr>
			                        
			                	<?php } elseif ($field_type == 'textarea') { ?>
			                        
			                    	<tr class="form-field">
			                    		<th scope="row" valign="top">
			                                <label for="category_nicename"><?php echo $field_title;?></label>
			                            </th>
			                    		<td>
			                                <textarea name="<?php echo "essb_wptm_".$field_id?>" rows="5" cols="50" class="large-text"><?php echo $inputValue ?></textarea><br />
			                            </td>
			                    	</tr>
			                    			                    
			                	<?php } elseif ($field_type == 'image') { ?>
			                        
			                        <?php $current_image_url = get_term_meta($category_id, $field_id, true); ?>
			                        
			                    	<tr class="form-field">
			                    		<th scope="row" valign="top">
			                                <label for="<?php echo "essb_wptm_".$field_id;?>" class="wptm_meta_name_label"><?php echo $field_title;?></label>
			                            </th>
			                    		<td>
			                                <div id="<?php echo "essb_wptm_".$field_id;?>_selected_image" class="wptm_selected_image">
			                                    <?php if ($current_image_url != '') echo '<img src="'.$current_image_url.'" style="max-width:100%;"/>';?>
			                                </div>
			                                <input type="text" name="<?php echo "essb_wptm_".$field_id;?>" id="<?php echo "essb_wptm_".$field_id;?>" value="<?php echo $current_image_url;?>" /><br />
			                                <br />
			                                <a href="" onclick="essb_select_tax_image('<?php echo "essb_wptm_".$field_id;?>'); return false;" title="Add an Image"> 
			                                    <strong>
			                                        <?php echo _e('Select Image', 'essb');?>
			                                    </strong>
			                        		</a>  |  
			                        		<a href="#" onclick="remove_image_url('<?php echo "essb_wptm_".$field_id;?>','<?php _e('No image selected', 'essb');?>');return false;">
			                                    <strong>
			                                        <?php _e('Clear Selected Image', 'essb');?>
			                                    </strong>
			                        		</a><br />
			                            </td>
			                        </tr>
			                    			                        
			                	<?php } // end ELSEIF
		}
		
		?>
		</table>
		</div>
        <textarea id="content_temp" name="content_temp" rows="100" cols="10" tabindex="2" onfocus="image_url_add()" style="width: 1px; height: 1px; padding: 0px; border: none;display :   none;"></textarea>
        <script type="text/javascript">edCanvas_temp = document.getElementById('content_temp');enable=false;</script>
        <script type="text/javascript">
		var custom_uploader;

		function essb_select_tax_image(id) {
			if (custom_uploader) {
	            custom_uploader.open();
	            return;
	        }
	 
	        //Extend the wp.media object
	        custom_uploader = wp.media.frames.file_frame = wp.media({
	            title: 'Select File',
	            button: {
	                text: 'Select File'
	            },
	            multiple: false
	        });
	 
	        //When a file is selected, grab the URL and set it as the text field's value
	        custom_uploader.on('select', function() {
	            attachment = custom_uploader.state().get('selection').first().toJSON();
	            $('#'+id).val(attachment.url);
	            var image_display_id = '#' + id + '_selected_image',
	            	view_image_url = "<img src=\"" + attachment.url + "\"  style=\"max-width:100%;\"/>";
	            jQuery(image_display_id).html(view_image_url);
	        });
	 
	        //Open the uploader dialog
	        custom_uploader.open();
		}
        
        function image_url_sync(){
        	add_image_url = '';
            add_image_url = image_url_collection;
            view_image_url = "<img src=\"" + add_image_url + "\"  style=\"max-width:100%;\"/>";
               
            if (add_image_url == '') add_image_url = 'No images selected';
            field = '';
            field = jQuery("#image_field").val();
            
            url_display_id = '#' + field + '_url_display';
            image_display_id = '#' + field + '_selected_image';
            
            jQuery(url_display_id).html(add_image_url);
        	jQuery('#' + field).val(add_image_url);
        	jQuery(image_display_id).html(view_image_url);
        	jQuery("#image_field").val('');
            
        }

        function image_url_add(){
            enable = true;
        	image_url = edCanvas_temp.value.match(/img src=\"(.*?)\"/g)[0].split(/img src=\"(.*?)\"/g)[1];
            image_url = image_url.replace(/-[0-9][0-9][0-9]x[0-9][0-9][0-9]\./i,'.');
            image_url_collection = image_url;
            edCanvas_temp.value = '';
            image_url_sync();
        }

        function image_photo_url_add($field){
        	jQuery("#image_field").val($field);
        }

        function remove_image_url($field, $message){
        	url_display_id = '#' + $field + '_url_display';
            image_display_id = '#' + $field + '_selected_image';
            
            jQuery(url_display_id).html($message);
        	jQuery('#' + $field).val('');
        	jQuery(image_display_id).html('');
            return false;
        }

        jQuery(document).ready(function($) {
            
            if (enable) {
                var original_send_to_editor = window.send_to_editor;
            }
        	window.send_to_editor = function (html) {
        		tb_remove();
        		edCanvas_temp.value = html;
        		image_url_add();
                if (enable) {
                    window.send_to_editor = original_send_to_editor;
                }
                enable = false;		
        	}
        });
        </script>
		<?php 
	}
}