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

if (!function_exists('essb5_generate_booster')) {
	function essb5_generate_booster($share_buttons = '') {
		$output = '';
		
		$booster_trigger = essb_option_value('booster_trigger');
		$booster_time = essb_option_value('booster_time');
		$booster_scroll = essb_option_value('booster_scroll');
		$booster_bg = essb_option_value('booster_bg');
		
		$booster_donotshow = essb_option_value('booster_donotshow');
		$booster_donotshow_on = essb_option_value('booster_donotshow_on');
		$booster_autoclose = essb_option_value('booster_autoclose');
		$booster_manualclose = essb_option_bool_value('booster_manualclose');
		$booster_manualclose_text = essb_option_value('booster_manualclose_text');
		
		$booster_window_bg = essb_option_value('booster_window_bg');
		$booster_window_color = essb_option_value('booster_window_color');
		$booster_title = essb_option_value('booster_title');
		$booster_message = essb_option_value('booster_message');
		$booster_bg_image = essb_option_value('booster_bg_image');
		
		if ($booster_message != '') {
			$booster_message = stripslashes($booster_message);
			$booster_message = do_shortcode($booster_message);
		}
		
		if ($booster_manualclose_text != '') {
			$booster_manualclose_text = stripslashes($booster_manualclose_text);
		}
		
		if ($booster_manualclose_text == '') {
			$booster_manualclose_text = esc_html__('I am not interested. Take me back to content', 'essb');
		}
		
		$custom_styles = "";
		
		if ($booster_window_bg != '') {
			$custom_styles .= 'background-color:'.esc_attr($booster_window_bg).';';
		}
		
		if ($booster_window_color != '') {
			$custom_styles .= 'color:'.esc_attr($booster_window_color).';';
		}
		if ($booster_bg_image != '') {
			$custom_styles .= 'background-image: url('.esc_url($booster_bg_image).');background-size: cover; background-position: center;';
		}
		
		if ($custom_styles != '') {
			$custom_styles = ' style="'.$custom_styles.'"';
		}
		
		$responsive_class = '';
		
		if (essb_option_bool_value('booster_mobile_deactivate')) {
			$responsive_class .= ' essb_mobile_hidden';
		}
		if (essb_option_bool_value('booster_tablet_deactivate')) {
			$responsive_class .= ' essb_tablet_hidden';
		}
		if (essb_option_bool_value('booster_desktop_deactivate')) {
			$responsive_class .= ' essb_desktop_hidden';
		}
		
		$output .= '<div class="essb-sharebooster'.esc_attr($responsive_class).'" data-trigger="'.esc_attr($booster_trigger).'" data-trigger-time="'.esc_attr($booster_time).
			'" data-trigger-scroll="'.esc_attr($booster_scroll).'" data-donotshow="'.esc_attr($booster_donotshow).'" data-donotshowon="'.esc_attr($booster_donotshow_on).
			'" data-autoclose="'.esc_attr($booster_autoclose).'"'.$custom_styles.'>';
		
		if ($booster_title != '') {
			$output .= '<h3 class="essb-sharebooster-title">'.$booster_title.'</h3>';
		}
		
		if ($booster_message != '') {
			$output .= '<div class="essb-sharebooster-message">'.$booster_message.'</div>';
		}
		
		$output .= '<div class="essb-sharebooster-buttons">'.$share_buttons.'</div>';
		
		if ($booster_manualclose) {
			$output .= '<div class="essb-sharebooster-close">'.$booster_manualclose_text.'</div>';
		}
		
		if ($booster_autoclose != '') {
			$output .= '<div class="essb-sharebooster-autoclose">'.esc_html__('This window will automatically close in ', 'essb').$booster_autoclose.esc_html__(' seconds', 'essb').'</div>';
		}
		
		$output .= '</div>';
		
		$output .= '<div class="essb-sharebooster-overlay" '.($booster_bg != '' ? 'style="background-color: '.esc_attr($booster_bg).';"' : '').'></div>';
		
		return $output;
	}
	
	
}
