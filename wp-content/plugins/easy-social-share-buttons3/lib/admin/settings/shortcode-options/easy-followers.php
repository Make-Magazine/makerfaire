<?php

if (!function_exists('essb_get_shortcode_options_easy_followers')) {
	function essb_get_shortcode_options_easy_followers($hide_advanced = false) {
		$r = array();
		
		if (!class_exists('ESSBSocialFollowersCounterHelper')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-helper.php');
		}
		
		$default_shortcode_setup = ESSBSocialFollowersCounterHelper::default_instance_settings();
		$shortcode_settings = ESSBSocialFollowersCounterHelper::default_options_structure(true, $default_shortcode_setup);
		
		foreach ($shortcode_settings as $field => $options) {
				
			if ($hide_advanced) {
				$field_hide_advanced = isset($options['hide_advanced']) ? $options['hide_advanced'] : '';
				if ($field_hide_advanced == 'true') {
					continue;
				}
			}
				
			$description = isset($options['description']) ? $options['description'] : '';
			$title = isset($options['title']) ? $options['title'] : '';
			$type = isset($options['type']) ? $options['type'] : '';				
			$values = isset($options['values']) ? $options['values'] : array();
			$default_value = isset($options['default_value']) ? $options['default_value'] : '';
				
			$paramOption = array();
			$paramOption['type'] = 'text';
			if ($type == 'textbox') { $paramOption['type'] = 'text'; }
			if ($type == 'select') {
				$paramOption['type'] = 'select';
				$paramOption['options'] = $values;
			} 
			
			if ($type == 'separator') {
				$paramOption['type'] = 'separator';
			}
			
			if ($type == 'checkbox') {
				$paramOption['type'] = 'checkbox';
			}
			
			$paramOption['title'] = $title;
			$paramOption['description'] = $description;
			
			if ($default_value && $default_value != '') {
				if ($type == 'checkbox' && $default_value == '1') { 
					$default_value = 'yes';
				}
				$paramOption['default_value'] = $default_value;
			}
			
			$r[$field] = $paramOption;
		}
		
		
		return $r;
	}
}
