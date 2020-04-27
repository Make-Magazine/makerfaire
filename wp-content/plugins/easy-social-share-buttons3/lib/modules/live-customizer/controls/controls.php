<?php
class ESSBLiveCustomizerControls {
	
	public static function draw_textbox_field($update_param, $update_at, $value, $placeholder = '') {
		echo '<input type="text" name="'.esc_attr($update_param).'" id="'.esc_attr($update_param).'" class="section-save" data-update="'.esc_attr($update_at).'" data-field="'.esc_attr($update_param).'" value="'.esc_attr($value).'" placeholder="'.esc_attr($placeholder).'" />';
	}
	
	public static function draw_switch_field2($field, $value = '', $update_at = '', $update_param = '') {
		
		$state = ($value == 'true') ? true : false;
		$is_checked = ($value == 'true') ? ' checked="checked"' : '';
		
		printf('<label class="switch ' . ($state ? ' checked' : '') . ' %1$s">
		<i class="icon-ok ti-check"></i>
		<i class="icon-remove ti-close"></i>
		<input id="essb_options_%1$s" type="checkbox" name="%1$s" data-update="%2$s" data-field="%8$s" value="true" class="checkbox section-save" %3$s />
		</label>', $field, $update_at, $is_checked, $on_text, $off_text, $on_switch, $off_switch, $update_param);
		
	}
	
	public static function draw_switch_field($field, $value = '', $update_at = '', $update_param = '') {

		$on_text = esc_html__('Yes', 'essb');
		$off_text = esc_html__('No', 'essb');
	
		$on_switch = '';
		$off_switch = '';
		
		if ($value == "true") {
			$on_switch = " selected";
			$off_switch = "";
		}
		else {
			$off_switch = " selected";
			$on_switch = "";
		}
		$is_checked = ($value == 'true') ? ' checked="checked"' : '';
		
		
		printf('<div class="essb-switch %1$s">
				<label class="cb-enable%6$s"><span>%4$s</span></label>
				<label class="cb-disable%7$s"><span>%5$s</span></label>
				<input id="essb_options_%1$s" type="checkbox" name="%1$s" data-update="%2$s" data-field="%8$s" value="true" class="checkbox section-save" %3$s />
				</div>', $field, $update_at, $is_checked, $on_text, $off_text, $on_switch, $off_switch, $update_param);
	}
	
	public static function draw_select_field($update_param, $update_at, $value, $values, $user_update_param = '') {
		$user_update_param = trim($user_update_param);
		if ($user_update_param == '') {
			$user_update_param = $update_param;
		}
		echo '<select id="'.esc_attr($update_param).'" class="section-save" data-update="'.esc_attr($update_at).'" data-field="'.esc_attr($user_update_param).'">';
		
		foreach ($values as $key => $single) {
			echo '<option value="'.esc_attr($key).'"'.($key == $value ? ' selected="selected"' : '').'>'.$single.'</option>';
		}
		
		echo '</select>';
	}
	
	public static function draw_image_select($preview_field, $update_param, $update_at, $value) {
		?>
		
		<div class="default-preview-image <?php echo esc_attr($preview_field) ?>">
			<img src="<?php echo esc_url($value); ?>" class="<?php echo esc_attr($preview_field); ?>-placeholder"/>
					
				<a href="#" class="essb-composer-button essb-composer-blue image-picker-button" id="<?php echo esc_attr($preview_field);?>-button"><i class="fa fa-upload"></i></a>
		</div>

		<script type="text/javascript">

		jQuery(document).ready(function($){
			 
			 
		    var custom_uploader;
		 
			function essb_og_image_upload() {
				 //If the uploader object has already been created, reopen the dialog
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
		            $('#<?php echo $update_param; ?>').val(attachment.url);

		            if ($('.<?php echo $preview_field; ?>-placeholder').length) {
			            $('.<?php echo $preview_field; ?>-placeholder').attr('src', attachment.url);
		            }
		        });
		 
		        //Open the uploader dialog
		        custom_uploader.open();
		    }


		    $('#<?php echo $preview_field;?>-button').click(function(e) {
				 
		        e.preventDefault();
		 
		        essb_og_image_upload();
		 
		    });
		});
		
		</script>
		<input type="text" name="<?php echo $update_param; ?>" id="<?php echo $update_param; ?>" class="section-save" data-update="<?php echo $update_at; ?>" data-field="<?php echo $update_param; ?>" value="<?php echo $value; ?>" style="display: none; " />
		
		<?php 
	}
	
	public static function draw_image_radio_field($field, $update_at = 'options', $value = '', $listOfValues) {
		$exist_user_value = true;
	
	
		echo '<div class="essb_image_radio_container essb_image_radio_container_'.$field.'">';
		$position = 1;
		foreach ( $listOfValues as $singleValueCode => $singleValue ) {
			$label = isset($singleValue['label']) ? $singleValue['label'] : '';
	
			$active_state = "";
			$active_element = "";
	
			if ($exist_user_value) {
				if ($value == $singleValueCode) {
					$active_state = " active";
					$active_element = ' checked="checked"';
				}
			}
				
			if ($label != '') {
				if ($html_values == 'true') {
					$label = sprintf('<div class="essb_radio_label_html">%1$s</div>', $label);
				}
				else {
					$label = sprintf('<div class="essb_radio_label">%1$s</div>', $label);
				}
			}
	
	
			$pathToImages = ESSB3_PLUGIN_URL.'/';
			if (strpos($singleValue['image'], 'http://') !== false || strpos($singleValue['image'], 'https://') != false) {
				$pathToImages = '';
			}
	
			echo '<div class="essb_radio">';
			echo '<div class="essb_image_radio'.$active_state.' essb_image_radio_'.$position.'" data-field="'.esc_attr($field.$position).'">';
			echo '<span class="checkbox-image"><img src="'.esc_url($pathToImages.$singleValue['image']).'"/></span>';
			echo '<span class="checkbox-state"><i class="fa fa-lg fa-check-circle"></i></span>';
			echo '<input type="radio" id="essb_options_'.esc_attr($field.$position).'" name="'.esc_attr($field).'" class="section-save" value="'.$singleValueCode.'"'.$active_element.' data-update="'.esc_attr($update_at).'" data-field="'.esc_attr($field).'" data-format="array"/>';
			echo $label;
			echo '</div></div>';
			
			$position++;
		}
		echo '</div>';
	}
}