<?php
/**
 * EasySocialShareButtons DisplayMethod: HeroShare
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2016 AppsCreo
 * @since 3.6
 *
 */

class ESSBDisplayMethodHeroShare {
	
	public static function generate_heroshare_code($options, $share_buttons, $is_shortcode, $shortcode_options = array(), $post_details = array()) {
		
		// loading popup display settings
		$popup_window_title = essb_object_value($options, 'heroshare_window_title');
		$popup_user_message = essb_object_value($options, 'heroshare_user_message');
		
		$popup_second_title = essb_object_value($options, 'heroshare_second_title');
		$popup_second_type = essb_object_value($options, 'heroshare_second_type');
		$popup_second_fans = essb_object_value($options, 'heroshare_second_fans');
		$popup_second_html = essb_object_value($options, 'heroshare_second_message');
			
		if (ESSB3_DEMO_MODE) {
			$is_active_option = isset($_REQUEST['heroshare']) ? $_REQUEST['heroshare'] : '';
			if (!empty($is_active_option)) {
				$popup_second_type = $is_active_option;
			}
		
			$is_active_option = isset($_REQUEST['heroshare_title']) ? $_REQUEST['heroshare_title'] : '';
			if (!empty($is_active_option)) {
				$popup_second_title = $is_active_option;
			}
		}
			
		// display settings
		$popup_user_width = essb_object_value($options, 'heroshare_user_width');
		$popup_window_popafter = essb_object_value($options, 'heroshare_window_popafter');
		$popup_user_percent = essb_object_value($options, 'heroshare_user_percent');
		$popup_display_end = essb_object_bool_value($options, 'heroshare_display_end');
		$popup_user_manual_show = essb_object_bool_value($options, 'heroshare_user_manual_show');
		$popup_user_notshow_onclose = essb_object_bool_value($options, 'heroshare_user_notshow_onclose');
		$popup_user_notshow_onclose_all = essb_object_bool_value($options, 'heroshare_user_notshow_onclose_all');
		
		// new @3.3
		$popup_display_exit = essb_object_bool_value($options, 'heroshare_display_exit');
		
		if ($is_shortcode) {
		
			$shortcode_window_title = isset($shortcode_options['heroshare_title']) ? $shortcode_options['heroshare_title'] : '';
			$shortcode_window_message = isset($shortcode_options['heroshare_message']) ? $shortcode_options['heroshare_message'] : '';
			$shortcode_pop_on_percent = isset($shortcode_options['heroshare_percent']) ? $shortcode_options['heroshare_percent'] : '';
			$shortcode_pop_end = isset($shortcode_options['heroshare_end']) ? $shortcode_options['heroshare_end'] : '';
		
			if (!empty($shortcode_window_title)) {
				$popup_window_title = $shortcode_window_title;
			}
			if (!empty($shortcode_window_message)) {
				$popup_user_message = $shortcode_window_message;
			}
			if (!empty($shortcode_pop_on_percent)) {
				$popup_user_percent = $shortcode_pop_on_percent;
			}
			if (!empty($shortcode_pop_end)) {
				$popup_display_end = essb_unified_true($shortcode_pop_end);
			}
		}
		
		if (!empty($popup_user_message)) {
			$popup_user_message = essb_post_details_to_content($popup_user_message);
		}
		if (!empty($popup_window_title)) {
			$popup_window_title = essb_post_details_to_content($popup_window_title);
		}
		
		$popup_window_popafter = trim($popup_window_popafter);
		
		$popup_trigger_oncomment = "";
		$output = "";
		$output .= sprintf('<div class="essb-heroshare%8$s" data-width="%1$s" data-load-percent="%2$s" data-load-end="%3$s" data-load-manual="%4$s" data-close-hide="%5$s" data-close-hide-all="%6$s" data-postid="%7$s" data-exit-intent="%9$s" data-load-timer="%10$s">',
				esc_attr($popup_user_width), esc_attr($popup_user_percent), esc_attr($popup_display_end), 
				esc_attr($popup_user_manual_show), esc_attr($popup_user_notshow_onclose),
				esc_attr($popup_user_notshow_onclose_all), esc_attr(get_the_ID()), esc_attr($popup_trigger_oncomment), 
				esc_attr($popup_display_exit), esc_attr($popup_window_popafter));
		$output .= '<a href="#" class="essb-heroshare-close" onclick="essb_heroshare_close(); return false;"></a>';
		$output .= '<div class="essb-heroshare-content">';
		
		if ($popup_window_title != '') {
			$output .= sprintf('<h3>%1$s</h3>', stripslashes($popup_window_title));
		}
		if ($popup_user_message != '') {
			$output .= sprintf('<div class="essb-heroshare-content-message">%1$s</div>', stripslashes($popup_user_message));
		}
		
		$output .= '<div class="essb-heroshare-post">';
		
		$output .= '<div class="essb-heroshare-post-image">';
		$output .= '<img src="'.$post_details['image'].'" height="250" class="essb-heroshare-post-image-src"/>';
		$output .= '</div>';
			
		$output .= '<div class="essb-heroshare-post-content">';
		$output .= '<h2>'.stripslashes($post_details['title_plain']).'</h2>';
		$output .= '<div class="essb-heroshare-post-excerpt">'.stripslashes($post_details['description']).'</div>';
		$output .= '</div>';
		$output .= '</div>';
		
		$output .= $share_buttons;
			
		if (!empty($popup_second_title)) {
			$popup_second_title = stripslashes($popup_second_title);
			$popup_second_title = do_shortcode($popup_second_title);
			$output .= sprintf('<h3 class="essb-heroshare-second-title">%1$s</h3>', $popup_second_title);
		}
			
		if ($popup_second_type == "fans") {
			if (!empty($popup_second_fans)) {
				$popup_second_fans = stripslashes($popup_second_fans);
				$popup_second_fans = do_shortcode($popup_second_fans);
					
				$output .= '<div class="essb-heroshare-second-fans">'.$popup_second_fans.'</div>';
			}
		}
		
		if ($popup_second_type == "html") {
			if (!empty($popup_second_html)) {
				$popup_second_html = stripslashes($popup_second_html);
				$popup_second_html = do_shortcode($popup_second_html);
		
				$output .= '<div class="essb-heroshare-second-html">'.$popup_second_html.'</div>';
			}
		}
			
		if ($popup_second_type == "top") {
			$leading_posts = self::leading_posts_from_analytics_for7days();
		
			$output .= '<div class="essb-heroshare-second-leading">';
			$output .= self::prepare_leadingposts_html($leading_posts);
			$output .= '</div>';
		}
		
		$output .= '</div>';
		
		if ($popup_window_popafter != '') {
			$output .= '<div class="essb-forced-hidden" id="essb_settings_heroafter_counter"></div>';
		}
		
		
		$output .= '</div>';
		$output .= "</div>";
		$output .= '<div class="essb-heroshare-shadow" onclick="essb_heroshare_close(); return false;"></div>';
		
		return $output;
	}
	
	
	public static function leading_posts_from_analytics_for7days() {
		return array();
	}
	
	
	
	public static function prepare_leadingposts_html($posts_data) {
		$output = "";
	
		foreach ($posts_data as $post_object) {
			$output .= '<div class="essb-heroshare-leading-post">';
				
			if (!empty($post_object['image'])) {
				$output .= '<a href="'.esc_url($post_object['url']).'"><img src="'.esc_url($post_object['image']).'" class="essb-heroshare-leading-post-image"/></a>';
			}
				
			$output .= '<a href="'.esc_url($post_object['url']).'"><h4>'.$post_object['title'].'</h4>';
			$output .= $post_object['excerpt'].'</a>';
				
			$output .= '</div>';
		}
	
		return $output;
	}
}