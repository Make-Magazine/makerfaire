<?php
/**
 * EasySocialShareButtons Display Method: Sidebar
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2019 AppsCreo
 * @since 6.3
 */

if (!function_exists('essb_sidebar_extender')) {
	function essb_sidebar_extender($extra_options = '', $position = '', $style = array()) {
		if ($position == 'sidebar') {
			// sidebar reveal code
			$appear_pos = essb_sanitize_option_value('sidebar_leftright_percent');
			$disappear_pos = essb_sanitize_option_value('sidebar_leftright_percent_hide');
			$appear_unit = essb_sanitize_option_value('sidebar_appear_unit');
			
			if ($appear_pos != '') {
				$extra_options .= ' data-sidebar-appear-pos="'.esc_attr($appear_pos).'"';
				$extra_options .= ' data-sidebar-appear-unit="'.esc_attr($appear_unit).'"';
			}
			if ($disappear_pos != '') {
				$extra_options .= ' data-sidebar-disappear-pos="'.esc_attr($disappear_pos).'"';
			}
			
			if (essb_option_bool_value('sidebar_content_hide')) {
				$extra_options .= ' data-sidebar-contenthidden="yes"';
			}
		}
		
		return $extra_options;
	}
	
	add_filter('essb_sharebuttons_open_element', 'essb_sidebar_extender', 10, 3);
	
	if (essb_option_bool_value('sidebar_adaptive_style')) {
		
		function essb_sidebar_apply_adaptive_styles($style = array()) {
			
			$style['button_width'] = '';
			$style['button_align'] = 'center';
			$style['button_style'] = 'icon';
			$style['nospace'] = 'true';
				
			if ($position == 'sidebar' && $style['button_size'] == '' && essb_sanitize_option_value('sidebar_icon_space') == '') {
				$style['button_width'] = 'fixed';
				$style['button_width_fixed_value'] = '52';
				$style['button_width_fixed_align'] = 'center';
			}
				
			if ($style['show_counter']) {
				if ($style['counter_pos'] != 'hidden') {
					$style['button_style'] = 'button';
				}
			
				if ($position == 'sidebar') {
					$style['counter_pos'] = 'bottom';
					$style['total_counter_post'] = 'leftbig';
				}
				if ($position == 'postfloat') {
					$style['counter_pos'] = 'inside';
					$style['button_align'] = 'left';
					$style['button_width'] = 'fixed';
					$style['button_width_fixed_value'] = '80';
					$style['button_width_fixed_align'] = 'left';
					$style['total_counter_pos'] = 'hidden';
				}
			}
			else {
				$style['button_style'] = 'icon_hover';
				$style['button_align'] = 'left';
			}
			
			return $style;
		}
		
		add_filter('essb4_position_style_sidebar', 'essb_sidebar_apply_adaptive_styles');
	}
}