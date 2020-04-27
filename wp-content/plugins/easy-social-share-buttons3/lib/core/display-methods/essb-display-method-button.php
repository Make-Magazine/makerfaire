<?php
/**
 * EasySocialShareButtons Display Method: Share Button
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2018 AppsCreo
 * @since 5.7
 *
 */


if (!function_exists('essb5_generate_share_button')) {
	function essb5_generate_share_button($share_buttons = '', $shortcode = array()) {
		$output = '';

		$button_text = essb_option_value('sharebutton_text');
		$button_bg = essb_option_value('sharebutton_bg');
		$button_color = essb_option_value('sharebutton_color');
		$button_style = essb_option_value('sharebutton_style');
		$button_position = essb_option_value('sharebutton_position');
		$button_total = essb_option_bool_value('sharebutton_total');
		$button_icon = essb_option_value('sharebutton_icon');
		
		if ($button_icon == '') {
			$button_icon = 'share';
		}
		
		if ($button_position == '') {
			$button_position = 'bottomright';
		}
		
		if ($button_style == '') {
			$button_style = 'button';
		}
		
		if ($button_text == '') {
			$button_text = esc_html__('Share', 'essb');
		}
		
		// -- passing shortcode options
		if (isset($shortcode['text'])) {
			if ($shortcode['text'] != '') {
				$button_text = $shortcode['text'];
			}
		}
		if (isset($shortcode['icon'])) {
			if ($shortcode['icon'] != '') {
				$button_icon = $shortcode['icon'];
			}
		}
		
		if (isset($shortcode['style'])) {
			if ($shortcode['style'] != '') {
				$button_style = $shortcode['style'];
			}
		}
		if (isset($shortcode['background'])) {
			if ($shortcode['background'] != '') {
				$button_bg = $shortcode['background'];
			}
		}
		if (isset($shortcode['color'])) {
			if ($shortcode['color'] != '') {
				$button_color = $shortcode['color'];
			}
		}
		
		if (isset($shortcode['shortcode'])) {
			$button_position = 'inline';
			if (isset($shortcode['stretched']) && $shortcode['stretched'] == 'true') {
				$button_position = 'inline-full';
			}
		}
		
		if (isset($shortcode['total'])) {
			if ($shortcode['total'] == 'true') {
				$button_total = true;
			}
			else {
				$button_total = false;
			}
		}
		
		$total_counter_code = '';
		
		if ($button_total && class_exists('ESSBCachedCounters')) {
			$share_details = essb_get_post_share_details('');
			$share_details['full_url'] = $share_details['url'];
			$networks = essb_option_value('networks');
			$result = ESSBCachedCounters::get_counters(get_the_ID(), $share_details, $networks);
			
			$total_shares = isset($result['total']) ? $result['total'] : '';
			
			if ($total_shares != '') {
				$total_counter_code = '<div class="essb-total">'.essb_kilomega($total_shares).'</div>';
			}
		}
		
		$button_text = stripslashes($button_text);
		
		$button_window_title = essb_option_value('sharebutton_window_title');
		$button_user_message = essb_option_value('sharebutton_user_message');

		$salt = mt_rand();
		// Generating the share button that will appear on screen
		if ($button_position != 'manual') {
			
			if ($button_bg != '' || $button_color != '') {
				$output .= '<style type="text/css">';
				
				if ($button_style == 'button' || $button_style == 'modern') {
					if ($button_bg != '') {
						$output .= ' .essb-share-button.essb-bs-'.esc_attr($button_style).' { background-color: '.esc_attr($button_bg).'; }';
					}
					if ($button_color != '') {
						$output .= ' .essb-share-button.essb-bs-'.esc_attr($button_style).' { color: '.esc_attr($button_color).'; }';
					}
				}
				
				if ($button_style == 'outline') {
					if ($button_color != '') {
						$output .= ' .essb-share-button.essb-bs-'.esc_attr($button_style).' { color: '.esc_attr($button_color).'; }';
					}
				}
				
				$output .= '</style>';
			}
			
			if ($button_position == 'inline' || $button_position == 'inline-full') {
				$output .= '<div class="essb-share-button-inline">';
			}
			
			$output .= '<div class="essb-share-button essb-bl-'.esc_attr($button_position).' essb-bs-'.esc_attr($button_style).' essb-cs-'.esc_attr($salt).'" onclick="essb.sharebutton(\''.$salt.'\');"><i class="essb_icon_'.$button_icon.'"></i><span>'.$button_text.'</span>'.$total_counter_code.'</div>';

			if ($button_position == 'inline' || $button_position == 'inline-full') {
				$output .= '</div>';
			}
		}
		
				
		$output .= '<div class="essb-share-button-window essb-windowcs-'.$salt.' essb-bl-'.esc_attr($button_position).'">';
		$output .= '<a href="#" class="essb-share-button-close" onclick="essb.sharebutton_close(\''.$salt.'\'); return false;"></a>';
		$output .= '<div class="inner-content">';
		
		$button_window_title = stripslashes($button_window_title);
		$button_window_title = do_shortcode($button_window_title);
		
		if ($button_window_title != '') {
			$button_window_title = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $button_window_title);
		
			$button_window_title = essb_post_details_to_content($button_window_title);
			$output .= '<h3>'.$button_window_title.'</h3>';
		}
		
		$button_user_message = stripslashes($button_user_message);
		$button_user_message = do_shortcode($button_user_message);
		
		if ($button_user_message != '') {
			$button_user_message = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $button_user_message);
		
			$button_user_message = essb_post_details_to_content($button_user_message);
			$output .= '<p>'.$button_user_message.'</p>';
		}
		$output .= $share_buttons;
		$output .= '</div>';
		$output .= '</div>';
		
		return $output;
	}
}