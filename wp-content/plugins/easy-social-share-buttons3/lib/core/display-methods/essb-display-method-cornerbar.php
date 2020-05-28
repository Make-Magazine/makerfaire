<?php
/**
 * EasySocialShareButtons Display Method: Corner Bar
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2017 AppsCreo
 * @since 5.1
 *
 */

if (!function_exists('essb5_generate_corner_bar')) {
	function essb5_generate_corner_bar($share_buttons = '') {
		$output = '';
		
		$cornerbar_pos = essb_option_value('cornerbar_pos');
		$cornerbar_style = essb_option_value('cornerbar_style');
		$cornerbar_bg = essb_option_value('cornerbar_bg');
		$cornerbar_width = essb_option_value('cornerbar_width');
		$cornerbar_show = essb_option_value('cornerbar_show');
		$cornerbar_hide = essb_option_bool_value('cornerbar_hide');


		$custom_styles = '';
		if ($cornerbar_bg != '') {
			$custom_styles .= 'background-color:'.esc_attr($cornerbar_bg).';';
		}
		if ($cornerbar_width != '') {
			$custom_styles .= 'max-width:'.esc_attr($cornerbar_width).';';
		}
		
		if ($cornerbar_pos == '') {
			$cornerbar_pos = 'bottom-right';
		}
		
		if ($cornerbar_style == '') {
			$cornerbar_style = 'transparent';
		}
		
		$responsive_class = '';
		
		if (essb_option_bool_value('cornerbar_mobile_deactivate')) {
			$responsive_class .= ' essb_mobile_hidden';
		}
		if (essb_option_bool_value('cornerbar_tablet_deactivate')) {
			$responsive_class .= ' essb_tablet_hidden';
		}
		if (essb_option_bool_value('cornerbar_desktop_deactivate')) {
			$responsive_class .= ' essb_desktop_hidden';
		}
		
		$output .= '<div class="essb-cornerbar essb-cornerbar-'.esc_attr($cornerbar_pos).' essb-cornerbar-'.esc_attr($cornerbar_style).($cornerbar_show != '' ? ' essb-cornerbar-hidden' : '').esc_attr($responsive_class).'" data-show="'.esc_attr($cornerbar_show).'" data-hide="'.esc_attr($cornerbar_hide).'" '.($custom_styles != '' ? 'style="'.$custom_styles.'"': '').'>';
		$output .= $share_buttons;
		$output .= '</div>';
		
		return $output;
	}
	
	add_filter('essb4_draw_style_details', 'essb5_cornerbar_customtext');	
	function essb5_cornerbar_customtext($styles) {
		
		$cornerbar_text = essb_option_value('cornerbar_text');
		$called_position = isset($styles['called_position']) ? $styles['called_position'] : '';
		
		if ($called_position == 'cornerbar' && $cornerbar_text != '') {
			if (essb_option_bool_value('cornerbar_small')) {
				$cornerbar_text = '<span style="font-size:12px;">'.$cornerbar_text.'</span>';
			}
			
			if (essb_option_bool_value('cornerbar_arrow')) {
				$cornerbar_text .= ' &rarr;';
			}
			
			$styles['message_share_before_buttons'] = $cornerbar_text;
		}
		
		return $styles;
	}
}
