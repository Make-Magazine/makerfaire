<?php
/**
 * Hide share counter based on settings from specific components (homepage, archives, etc.)
 * 
 * @since 7.0
 * @author appscreo
 * @package EasySocialShareButtons
 */

if (!function_exists('essb_hide_counters_on_archives')) {
	
	function essb_hide_counters_on_archives($style = array()) {
		
		if (essb_option_bool_value('hide_counter_homepage')) {
			if (is_home() || is_front_page()) {
				$style['show_counter'] = false;
			}
		}
		
		if (essb_option_bool_value('hide_counter_archive')) {
			if (is_archive() || is_search() || is_tag() || is_post_type_archive()) {
				$style['show_counter'] = false;
			}
		}
		
		return $style;
	}
	
	add_filter('essb4_draw_style_details', 'essb_hide_counters_on_archives');
}