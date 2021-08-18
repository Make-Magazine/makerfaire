<?php

/**
 * EasySocialShareButtons Display Method: Post Vertical Float
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2019 AppsCreo
 * @since 6.3
 *
 */

if (!function_exists('essb_postfloat_extender')) {
	
	function essb_postfloat_extender($extra_options = '', $position = '', $style = array()) {
		if ($position == 'postfloat') {
			$postfloat_bottom_offset = essb_option_value('postfloat_bottom_offset');
			if ($postfloat_bottom_offset != '' && intval($postfloat_bottom_offset) > 0) {
				$extra_options .= ' data-postfloat-bottom="'.esc_attr($postfloat_bottom_offset).'"';
			}
			if (essb_option_bool_value('postfloat_always_visible')) {
				$extra_options .= ' data-postfloat-stay="true"';
			}
						
			$postfloat_top = essb_sanitize_option_value('postfloat_top');
			if (!empty($postfloat_top)) {
				$extra_options .= ' data-postfloat-top="'.esc_attr($postfloat_top).'"';
			}
			
			$postfloat_percent = essb_sanitize_option_value('postfloat_percent');
			if ($postfloat_percent != '') {
				$measuring_unit = '';
				if (strpos($postfloat_percent, '%') !== false) {
					$measuring_unit = '%';
				}
				
				if (strpos($postfloat_percent, 'px') !== false) {
					$measuring_unit = 'px';
				}
				
				$postfloat_percent = str_replace('px', '', $postfloat_percent);
				$postfloat_percent = str_replace('%', '', $postfloat_percent);
				$postfloat_percent = trim($postfloat_percent);
				
				$extra_options .= ' data-postfloat-percent="'.esc_attr($postfloat_percent).'" data-postfloat-percent-m="'.esc_attr($measuring_unit).'"';
			}
			
			$postfloat_selectors = essb_sanitize_option_value('postfloat_selectors');
			if ($postfloat_selectors != '') {
				$extra_options .= ' data-postfloat-selectors="'.esc_attr($postfloat_selectors).'"';
			}
			
			if (essb_option_bool_value('postfloat_fix_bottom') && essb_option_bool_value('postfloat_always_visible')) {
			    $extra_options .= ' data-postfloat-fixbottom="true"';
			}
		}
		
		return $extra_options;
	}
	
	add_filter('essb_sharebuttons_open_element', 'essb_postfloat_extender', 10, 3);
}