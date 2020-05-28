<?php
/**
 * EasySocialShareButtons Display Method: Follow Me Bar
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2017 AppsCreo
 * @since 5.0
 *
 */

if (!function_exists('essb5_generate_followme_bar')) {
	function essb5_generate_followme_bar($share_buttons = '') {
		$output = '';
		
		$followme_pos = essb_option_value('followme_pos');
		$followme_full = essb_option_bool_value('followme_full');
		$followme_top = essb_option_value('followme_top');
		$followme_bg = essb_option_value('followme_bg');
		$followme_nomargin = essb_options_bool_value('followme_nomargin');
		$followme_noleftmargin = essb_option_bool_value('followme_noleftmargin');
		$followme_hide = essb_option_bool_value('followme_hide');
		$followme_top_offset = essb_sanitize_option_value('followme_top_offset');
		
		if ($followme_pos == '') {
			$followme_pos = 'bottom';
		}
		if ($followme_pos == 'left') {
			$followme_full = false;
			$followme_top = '';
			$followme_nomargin = true;
		}
		
		$followme_pos_class = 'essb-followme-'.$followme_pos;
		
		$output .= '<div class="essb-followme '.esc_attr($followme_pos_class).($followme_full ? ' essb-followme-full' : '').($followme_nomargin ? ' essb-followme-nospace' : '').'" data-full="'.esc_attr($followme_full).'" data-top="'.esc_attr($followme_top).'" data-background="'.esc_attr($followme_bg).'" data-position="'.esc_attr($followme_pos).'" data-avoid-left="'.($followme_noleftmargin ? 'true': 'false').'" data-hide="'.esc_attr($followme_hide).'" data-showafter="'.esc_attr($followme_top_offset).'">';
		$output .= $share_buttons;
		$output .= '</div>';
		
		return $output;
	}
}
