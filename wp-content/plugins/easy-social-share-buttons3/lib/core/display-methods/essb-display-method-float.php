<?php
/**
 * EasySocialShareButtons Display Method: Float from content top
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2019 AppsCreo
 * @since 6.3
 */

if (!function_exists('essb_float_extender')) {
	function essb_float_extender($extra_options = '', $position = '', $style = array()) {
		if ($position == 'float') {
			$hide_float_from_top = essb_sanitize_option_value('float_top_disappear');
			if (!empty($hide_float_from_top)) {
				$extra_options .= ' data-float-hide="'.esc_attr($hide_float_from_top).'"';
			}
			
			$top_pos = essb_sanitize_option_value('float_top');
			$float_top_loggedin = essb_sanitize_option_value('float_top_loggedin');
			if (is_user_logged_in() && $float_top_loggedin != '') {
				$top_pos = $float_top_loggedin;
			}
			if (!empty($top_pos)) {
				$extra_options .= ' data-float-top="'.esc_attr($top_pos).'"';
			}
		}
		
		return $extra_options;
	}
	
	add_filter('essb_sharebuttons_open_element', 'essb_float_extender', 10, 3);
}