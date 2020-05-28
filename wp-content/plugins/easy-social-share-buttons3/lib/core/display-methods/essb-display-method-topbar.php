<?php
/**
 * EasySocialShareButtons DisplayMethod: TopBar
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2016 AppsCreo
 * @since 3.6
 *
 */

class ESSBDisplayMethodTopBar {
	public static function generate_topbar_code($options, $share_buttons, $is_shortcode, $shortcode_options = array()) {
		$output = '';
		
		$topbar_content_area = essb_object_bool_value($options, 'topbar_contentarea');
		$topbar_content_area_pos = essb_object_value($options, 'topbar_contentarea_pos');
		$topbar_buttons_align = essb_object_value($options, 'topbar_buttons_align', 'left');
		$topbar_usercontent = essb_object_value($options, 'topbar_usercontent');
		
		if ($is_shortcode) {
			if (!empty($shortcode_options['topbar_contentarea'])){
				$topbar_content_area = essb_unified_true($shortcode_options['topbar_contentarea']);
			}
			if (!empty($shortcode_options['topbar_contentarea_pos'])) {
				$topbar_contentarea_pos = $shortcode_options['topbar_contentarea_pos'];
			}
			if (!empty($shortcode_options['topbar_buttons_align'])) {
				$topbar_buttons_align = $shortcode_options['topbar_buttons_align'];
			}
			if (!empty($shortcode_options['topbar_usercontent'])) {
				$topbar_usercontent = $shortcode_options['topbar_usercontent'];
			}
		}
			
		$topbar_usercontent = stripslashes($topbar_usercontent);
		$topbar_usercontent = do_shortcode($topbar_usercontent);
		
		if ($topbar_usercontent != '') {
			$topbar_usercontent = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#'), array(get_the_title(), get_site_url(), get_permalink()), $topbar_usercontent);
		
			$topbar_usercontent = essb_post_details_to_content($topbar_usercontent);
		}
			
		$responsive_class = '';
		
		if (essb_option_bool_value('topbar_mobile_deactivate')) {
			$responsive_class .= ' essb_mobile_hidden';
		}
		if (essb_option_bool_value('topbar_tablet_deactivate')) {
			$responsive_class .= ' essb_tablet_hidden';
		}
		if (essb_option_bool_value('topbar_desktop_deactivate')) {
			$responsive_class .= ' essb_desktop_hidden';
		}
		
		$ssbuttons = $share_buttons;
			
		$output = '';
			
		$output .= '<div class="essb_topbar'.esc_attr($responsive_class).'">';
		$output .= '<div class="essb_topbar_inner">';
			
		if (!$topbar_content_area) {
			$output .= sprintf('<div class="essb_topbar_inner_buttons essb_bar_withoutcontent essb_topbar_align_%1$s">', $topbar_buttons_align);
			$output .= $ssbuttons;
			$output .= '</div>';
		}
		else {
			if ($topbar_content_area_pos == "left") {
				$output .= '<div class="essb_topbar_inner_content">';
				$output .= stripslashes($topbar_usercontent);
				$output .= '</div>';
				$output .= sprintf('<div class="essb_topbar_inner_buttons essb_topbar_align_%1$s">', $topbar_buttons_align);
				$output .= $ssbuttons;
				$output .= '</div>';
			}
			else {
				$output .= sprintf('<div class="essb_topbar_inner_buttons essb_topbar_align_%1$s">', $topbar_buttons_align);
				$output .= $ssbuttons;
				$output .= '</div>';
				$output .= '<div class="essb_topbar_inner_content">';
				$output .= stripslashes($topbar_usercontent);
				$output .= '</div>';
			}
		}
		$output .= '</div>';
		$output .= '</div>';
			
		return $output;
	}
}

if (!function_exists('essb_topbar_extender')) {

	function essb_topbar_extender($extra_options = '', $position = '', $style = array()) {

		if ($position == 'topbar') {
			$topbar_appear_pos = essb_sanitize_option_value('topbar_top_onscroll');
			$topbar_hide = essb_sanitize_option_value('topbar_hide');
	
			if ($topbar_appear_pos != '') {
				$extra_options .= ' data-topbar-appear="'.esc_attr($topbar_appear_pos).'"';
			}
			if ($topbar_hide != '') {
				$extra_options .= ' data-topbar-disappear="'.esc_attr($topbar_hide).'"';
			}
		}

		return $extra_options;
	}

	add_filter('essb_sharebuttons_open_element', 'essb_topbar_extender', 10, 3);
}