<?php

/**
 * Button design time functions
 *
 * @author appscreo
 * @package EasySocialShareButtons
 * @version 1.0
 * @since 3.0
 */


class ESSBButtonHelper {
	public static function draw_share_buttons($share = array(), $style = array(), $networks = array(),
			$networks_order = array(), $network_names = array(),
			$position = '', $salt = '', $like_or_share = 'share', $native_buttons = '') {

		return essb_draw_share_buttons($share, $style, $networks, $networks_order, $network_names,
				$position, $salt, $like_or_share, $native_buttons);
	}
}


function essb_draw_share_buttons($share = array(), $style = array(), $networks = array(),
			$networks_order = array(), $network_names = array(),
			$position = '', $salt = '', $like_or_share = 'share', $native_buttons = '') {

	$content = '';
	$share['salt'] = $salt;
	$style['called_position'] = $position;
		
	/**
	 * Adaptive stretched width
	 */
	if ($style['button_align'] == 'stretched') {		
		$style = essb_draw_adaptive_stretch_styles($style);
	}
		
	// @since 4.2
	// Developer filters added to allow in draw customization of share buttons and social networks
	// Example usage: live customizer
	if (has_filter('essb4_draw_share_details')) {
		$share = apply_filters('essb4_draw_share_details', $share);
	}

	if (has_filter('essb4_draw_style_details')) {
		$style = apply_filters('essb4_draw_style_details', $style);
	}

	if (has_filter('essb4_draw_customize_networks')) {
		$networks = apply_filters('essb4_draw_customize_networks', $networks);
	}

	if (has_filter('essb4_draw_customize_networks_order')) {
		$networks_order = apply_filters('essb4_draw_customize_networks_order', $networks_order);
	}

		
	$mobile_avoid_newwindow = false;
	$is_mobile = false;
	if (essb_options_bool_value('mobile_avoid_newwindow') && essb_is_mobile()) {
		$mobile_avoid_newwindow = essb_options_bool_value('mobile_avoid_newwindow');
		$is_mobile = $mobile_avoid_newwindow ? true : false;
	}

	$sigle_counter_hidden_till = ESSBGlobalSettings::$button_counter_hidden_till;
	$more_button_icon = isset($style['more_button_icon']) ? $style['more_button_icon'] : ESSBGlobalSettings::$more_button_icon;
	$comments_address = ESSBGlobalSettings::$comments_address;

	$button_follow_state = ESSBGlobalSettings::$use_rel_me ? 'me' : (ESSBGlobalSettings::$use_rel_noopener ? 'noreferrer noopener nofollow' : 'nofollow');
	if (has_filter('essb_share_link_rel')) {
		$button_follow_state = apply_filters('essb_share_link_rel', $button_follow_state);
	}

	// share button code
	$share_button_icon = isset($style['share_button_icon']) ? $style['share_button_icon'] : '';
	if ($share_button_icon == '') { $share_button_icon = 'share'; }
	$share_button_style = isset($style['share_button_style']) ? $style['share_button_style'] : '';

	// since @3.2.4
	// encoding of url parts
	$share['essb_encode_text'] = ESSBGlobalSettings::$essb_encode_text;
	$share['essb_encode_url'] = ESSBGlobalSettings::$essb_encode_url;
	$share['essb_encode_text_plus'] = ESSBGlobalSettings::$essb_encode_text_plus;

	$cached_counters = array();
	$cached_counters_active = false;

	if (isset($style['demo_counter'])) {
		if ($style['demo_counter'] && !defined('ESSB3_CACHED_COUNTERS')) {
			define('ESSB3_CACHED_COUNTERS', true);
			if (!class_exists('ESSBCachedCounters')) {
				include_once(ESSB3_PLUGIN_ROOT . 'lib/core/share-counters/essb-cached-counters.php');
			}
		}
	}
	else {
		$style['demo_counter'] = false;
	}

	if (defined('ESSB3_CACHED_COUNTERS') && $style ['show_counter']) {
		$cached_counter_networks = ESSBCachedCounters::prepare_list_of_networks_with_counter($networks_order, $networks);
		$cached_counters_active = true;

		if ($style['demo_counter']) {
			foreach ($cached_counter_networks as $n) {
				$cached_counters[$n] = rand(100, 2000);
			}

			$cached_counters['total'] = rand(500, 10000);
		}
		else {
			$cached_counters = ESSBCachedCounters::get_counters($share['post_id'], $share, $cached_counter_networks);

			// @since 4.0 we made order possible by number of shares
			if (ESSBGlobalSettings::$user_sort == 'shares') {
				$networks_order = essb_prepare_networks_order_by_shares($cached_counters);
			}
		}
	}



	// beginning of share buttons snippet
	$content .= essb_draw_buttons_start ( $style, $position, $salt, $like_or_share, $share, $cached_counters, $networks );
	$is_active_more_button = false;
	$is_active_share_button = false;

	// @since 3.3 - option to change more button style on each display position
	if (isset($style['location_more_button_func'])) {
		if (!empty($style['location_more_button_func'])) {
			$style['more_button_func'] = $style['location_more_button_func'];
		}
	}

	if (!isset($style['show_counter'])) {
		$style['show_counter'] = false;
	}
	
	// 7.1
	// Adding translation of the Share on words only
	$share_on_prefix = essb_option_value('translate_share_on_prefix');
	if ($share_on_prefix == '') { $share_on_prefix = esc_html__('Share on', 'essb'); }

	$required_fullwidth_correction = essb_button_require_fullwidth_correction($style);
	$count_displayed = 0;
	foreach ( $networks_order as $single ) {
		// generate single network button
		if (in_array ( $single, $networks )) {

			if ($single == 'no') { continue; }
			
			// since version 7 Google+ and StumbleUpon won't show even choosed
			/**
			 * @since 7.3 - remove also ManageWP (network closed)
			 */
			if ($single == 'google' || $single == 'stumbleupon' || $single == 'mwp') { continue; }

			$count_displayed++;

			$url = '';
			$api_command = '';
			$link_target = '_blank';

			$single_share_address = $single;
			$icon = $single;
			$additional_icon = $single;

			$cached_code_left = '';
			$cached_code_right = '';
			$cached_code_insidebefore = '';
			$cached_code_insideafter = '';
			$cached_code_before = '';
			$cached_code_after = '';
			$counter_pos = $style['counter_pos'];
			
			/**
			 * Support for the mini inside share counters, available since 6.5
			 */
			if ($counter_pos == 'insidem') {
				$counter_pos = 'inside';
			}
			if ($counter_pos == 'insidenamem') {
				$counter_pos = 'insidename';
			}
			if ($counter_pos == 'insidebeforenamem') {
				$counter_pos = 'insidebeforename';
			}
			
			if ($cached_counters_active) {
				$cached_counter_key = ($single != 'share') ? $single : 'total';
				
				if ($single == 'share') {
					$counter_pos = isset($style['share_button_counter']) ? $style['share_button_counter'] : '';
					if ($counter_pos == '') {
						$counter_pos = essb_option_value('share_button_counter');
					}
					if ($counter_pos == '') { $counter_pos == 'hidden'; }
				}

				if (isset($cached_counters[$cached_counter_key])) {

					essb_depend_load_function('essb_draw_share_counter_code', 'lib/core/share-counters/essb-sharecounter-draw.php');

					$single_counter = isset($cached_counters[$cached_counter_key]) ? $cached_counters[$cached_counter_key] : '0';

					if ($counter_pos == 'left' || $counter_pos == '' || $counter_pos == 'leftm' || $counter_pos == 'topm' || $counter_pos == 'top') {
						$cached_code_left = essb_draw_share_counter_code('', $single_counter, $sigle_counter_hidden_till);
					}

					if ($counter_pos == 'right' || $counter_pos == 'rightm') {
						$cached_code_right = essb_draw_share_counter_code('_right', $single_counter, $sigle_counter_hidden_till);
					}
					if ($counter_pos == 'insidename' || $counter_pos == 'inside' || $counter_pos == 'bottom') {
						$cached_code_insideafter = essb_draw_share_counter_code('_'.$counter_pos, $single_counter, $sigle_counter_hidden_till);
					}
					if ($counter_pos == 'insidebeforename') {
						$cached_code_insidebefore = essb_draw_share_counter_code('_'.$counter_pos, $single_counter, $sigle_counter_hidden_till);
					}
					if ($counter_pos == 'hidden') {
						$cached_code_right = essb_draw_share_counter_code('_'.$counter_pos, $single_counter, $sigle_counter_hidden_till);
					}
					if ($counter_pos == 'topn') {
						$cached_code_before = essb_draw_share_counter_code('_'.$counter_pos, $single_counter, $sigle_counter_hidden_till);
					}
					if ($counter_pos == 'insidehover') {
						$cached_code_after = essb_draw_share_counter_code('_'.$counter_pos, $single_counter, $sigle_counter_hidden_till);
					}
				}
			}

			// specail network code
			if ($single == 'print' && ESSBGlobalSettings::$print_use_printfriendly) {
				$single_share_address = 'print_friendly';
			}

			if ($single == 'mail' && $style['mail_function'] == 'form') {
				$single_share_address = 'mail_form';
			}

			if ($single == 'pinterest' && !ESSBGlobalSettings::$pinterest_sniff_disable) {
				$single_share_address = 'pinterest_picker';
			}

			if ($single == 'pinterest' && $single_share_address == 'pinterest_picker') {
				if ($style['amp']) $single_share_address = 'pinterest';
			}

			if (!$style['is_mobile'] && $single == 'facebook' && ESSBGlobalSettings::$facebookadvanced && ESSBGlobalSettings::$facebookadvancedappid != '') {
				$single_share_address = 'facebook_advanced';
			}

			if ($single == 'more' && $style['more_button_func'] != '1') {
				$single_share_address = 'more_popup';
			}

			if ($single == 'share' && $style['share_button_func'] != '1') {
				$single_share_address = 'more_popup';
			}
			
			// Facebook Like
			if ($single == 'facebook_like') {
				$single_share_address = 'facebook_like';
			}

			if ($single == 'more' && $more_button_icon == 'dots') {
				$icon = 'more_dots';
				$additional_icon = 'more_dots';
			}

			if ($single == 'share' && $share_button_icon != '') {
				$icon = 'sharebtn';
				$additional_icon = $share_button_icon;
			}

			if ($required_fullwidth_correction && $count_displayed == 1) {
				$icon .= ' essb_item_fw_first';
			}

			if ($required_fullwidth_correction && $count_displayed == 2) {
				$icon .= ' essb_item_fw_second';
			}

			// get single social network commands
			$share_details = essb_get_share_address($single_share_address, $share, $salt, $style['amp'], $is_mobile);

			$url = $share_details['url'];
			$api_command = $share_details['api_command'];

			if ($single == 'sidebar-close') {
				$api_command = '';
			}

			if ($single == 'comments') {
				$api_command = "essb.tracking_only('', 'comments', '".$salt."', true);";
				$url = $comments_address;
				$link_target = '_self';
			}

			if ($single_share_address == 'mail') {
				$link_target = '_self';
			}

			// @since 4.1 avoid new window of mobile
			if ($mobile_avoid_newwindow && $single != 'more') {
				if ($single == 'whatsapp' || $single == 'mail' || $single == 'viber' || $single == 'telegram' || $single == 'messenger' || $single == 'sms') {
					$link_target = '_self';
					$api_command = "essb.tracking_only('', '".$single."', '".$salt."', true);";
				}
			}
			
			/**
			 * @since 7.3 
			 * 
			 * Allow external change of any network target
			 */
			if (has_filter("essb_network_target_{$single}")) {
			    $link_target = apply_filters("essb_network_target_{$single}", $link_target);
			}

			$name = isset($network_names[$single]) ? $network_names[$single] : '';
			$noname_class = '';
			
			if ($single == 'facebook_like') {
				$single = 'facebook';
				$icon = 'facebook';
				$additional_icon = 'facebook-official';
			}

			$use_button_style = $style['button_style'];
			if ($single == 'share' && $share_button_style != '') {
				$use_button_style = $share_button_style;
			}

			if ($use_button_style == 'icon' || $name == '-' || $single == 'more') {
				$noname_class = ' essb_noname';
			}
			if ($counter_pos == 'insidehover' && $style['show_counter']) {
				$noname_class .= ' essb_hideonhover';
			}
			
			// Additional check for the localization setup
			$hover_text = essb_sanitize_option_value('hovertext_'.$single);
			if ($hover_text == '' || essb_option_bool_value('deactivate_module_translate')) {
			    $hover_text = $share_on_prefix.' '.$name;
			}
			
			if (essb_option_bool_value('user_link_notitle')) {
				$hover_text = '';
			}
				
			// clean network names when depends on button style or seleceted network
			if ($use_button_style == 'icon' || (($counter_pos == 'inside' || $counter_pos == 'bottom') && $style['show_counter'] ) || $name == '-' || $single == 'more') {
				$name = '';
			}

			$mycred_token = '';
			if (defined ('ESSB3_MYCRED_ACTIVE')) {
				$mycred_token = ESSBMyCredIntegration::generate_mycred_datatoken();
			}

			$more_after_class = ($is_active_more_button || $is_active_share_button) ? ' essb_after_more' : '';

			if ($single == 'sidebar-close') {
				$more_after_class = '';
			}
			
			/**
			 * 6.3 Reponsive networks controls
			 */
			if (essb_option_bool_value('activate_networks_responsive')) {
				if (essb_option_bool_value('responsive_'.$single.'_desktop')) {
					$more_after_class .= ' essb_desktop_hidden';
				}
				
				if (essb_option_bool_value('responsive_'.$single.'_mobile')) {
					$more_after_class .= ' essb_tablet_hidden essb_mobile_hidden';
				}
			}
			
			$custom_svg_icon = '';
			
			if (has_filter("essb_network_svg_icon_{$single}")) {
				$custom_svg_icon = apply_filters("essb_network_svg_icon_{$single}", $custom_svg_icon);
			}
			
			
			// Attach custom URL options if present to the base link
			if (has_filter("essb_network_custom_url_options_{$single}")) {
			    $mycred_token .= apply_filters("essb_network_custom_url_options_{$single}", $share);
			}

			$content .= sprintf('<li class="essb_item essb_link_%8$s nolightbox%2$s">
					%12$s<a href="%3$s" title="%4$s" onclick="%5$s" target="%10$s" rel="%11$s" class="nolightbox" %7$s>%13$s<span class="essb_icon essb_icon_%17$s">%19$s</span><span class="essb_network_name%9$s">%14$s%6$s%15$s</span>%18$s</a>%16$s</li>',
					$single, $more_after_class, $url, $hover_text, $api_command, $name, $mycred_token, $icon, $noname_class, $link_target, $button_follow_state,
					$cached_code_left, $cached_code_before, $cached_code_insidebefore, $cached_code_insideafter,
					$cached_code_right, $additional_icon, $cached_code_after, $custom_svg_icon);

			// at the end toggle more button state
			if ($single == 'more') {
				$is_active_more_button = true;
			}
			if ($single == 'share') {
				$is_active_share_button = true;
			}
		}
	}

	// adding less button when + function of more button is active
	if (($is_active_more_button && $style['more_button_func'] == '1')) {
		$share_details = essb_get_share_address('less', $share, $salt);
		$url = $share_details['url'];
		$api_command = $share_details['api_command'];

		$content .= sprintf('<li class="essb_item essb_link_%1$s nolightbox%2$s">
				<a href="%3$s" title="%4$s" onclick="%5$s" target="_blank" rel="nofollow"><span class="essb_icon essb_icon_%1$s"></span><span class="essb_network_name">%6$s</span></a></li>',
				"less", $more_after_class, esc_url($url), "", $api_command, "");

	}

	if (is_array($native_buttons) && $native_buttons['active'] && $native_buttons['sameline']) {
  		$content .= ESSBNativeButtonsHelper::draw_native_buttons($native_buttons, $native_buttons['order'], $native_buttons['counters'],
			$native_buttons['sameline'], $native_buttons['skinned']);
	}

	// end of share buttons snippet
	
	$content .= essb_draw_buttons_end ( $style, $position, $salt, $like_or_share, $native_buttons, $cached_counters, $share );

	return $content;
}



function essb_get_share_address($network, $share = array(), $salt = '', $amp_endpoint = false, $is_mobile = false) {
	
	if (essb_options_bool_value( 'advanced_custom_share')) {
		essb_depend_load_function('essb_apply_advanced_custom_share', 'lib/core/extenders/essb-buttonhelper-extender-advancedshare.php');
		$share = essb_apply_advanced_custom_share($share, $network);
	}

	// @since version 3.0.3 - fixes the GA Campaign tracking fields
	$ga_tracking_code = ESSBGlobalSettings::$activate_ga_campaign_tracking;
	if ($ga_tracking_code != '' || isset($share['ga_mode'])) {
		essb_depend_load_function('essb_correct_url_on_tracking_code', 'lib/core/extenders/essb-buttonhelper-extender-encode.php');
		$share = essb_correct_url_on_tracking_code($share, $network);
	}	
	
	if (!isset($share['query'])) {
		if (isset($share['essb_encode_url'])) {
			if ($share['essb_encode_url']) {
				essb_depend_load_function('essb_buttonhelper_encode_url_sharing', 'lib/core/extenders/essb-buttonhelper-extender-encode.php');
				$share = essb_buttonhelper_encode_url_sharing($share);
			}
		}

		if (isset($share['essb_encode_text'])) {
			if ($share['essb_encode_text']) {
				essb_depend_load_function('essb_buttonhelper_encode_text', 'lib/core/extenders/essb-buttonhelper-extender-encode.php');
				$share = essb_buttonhelper_encode_text($share);
			}
		}

		if (isset($share['essb_encode_text_plus'])) {
			if ($share['essb_encode_text_plus']) {
				$share['twitter_tweet'] = str_replace(' ', '%20', $share['twitter_tweet']);
				$share['twitter_tweet'] = str_replace('+', '%20', $share['twitter_tweet']);
			}
		}
	}

	$share['url'] = rawurlencode(esc_url($share['url']));		
	$share['short_url'] = rawurlencode(esc_url($share['short_url']));
	$share['short_url_twitter'] = rawurlencode(esc_url($share['short_url_twitter']));
	$share['full_url'] = rawurlencode(esc_url($share['full_url']));
	$share['title'] = urlencode(esc_attr($share['title']));
	$share['image'] = esc_attr($share['image']);
	$share['description'] = urlencode(esc_attr($share['description']));		

	/**
	 * 7.2.2 Fix the wrong encoded URLs in UTM options
	 */
	if (strpos($share['url'], '%26%23038%3B') !== false) {
	    $share['url'] = str_replace('%26%23038%3B', '%26', $share['url']);
	    $share['full_url'] = str_replace('%26%23038%3B', '%26', $share['full_url']);
	    $share['short_url'] = str_replace('%26%23038%3B', '%26', $share['short_url']);
	    $share['short_url_twitter'] = str_replace('%26%23038%3B', '%26', $share['short_url_twitter']);	  
	}
	
	/**
	 * @since 7.7 Title get & symbol issue after encoding to %26amp%3B (got as &amp; instead of &)
	 */
	if (strpos($share['title'], '%26amp%3B') !== false) {
	    $share['title'] = str_replace('%26amp%3B', '%26', $share['title']);
	}

	// adding additional support for hashtags everywhere
	$share['title'] = str_replace('#', '%23', $share['title']);
	$share['description'] = str_replace('#', '%23', $share['description']);

	if (isset($share['mail_subject'])) {
		$share['mail_subject'] = esc_attr(stripslashes($share['mail_subject']));
	}
	if (isset($share['mail_body'])) {
		$share['mail_body'] = esc_attr(stripslashes($share['mail_body']));		
	}

	$pinterest_description = $share['title'];
	$custom_pinterest_desc = isset($share['pinterest_desc']) ? $share['pinterest_desc'] : '';
	if (!empty($custom_pinterest_desc)) {
		$pinterest_description = $custom_pinterest_desc;
	}
	
	$pinterest_description = essb_core_helper_textencode($pinterest_description);

	// @since version 3.0.4 - fix for shorturl
	$shorturl_activate = essb_options_bool_value( 'shorturl_activate');
	if ($shorturl_activate && !empty($share['short_url'])) {
		$share['url'] = $share['short_url'];
	}

	$url = '';
	$api_command = '';

	$network_type = 'buildin';

	if (isset($share['query'])) {
		if ($share['query']) {
			$share['short_url_twitter'] = urlencode($share['short_url_twitter']);
			$share['full_url'] = urlencode($share['full_url']);
			$share ['url'] = urlencode($share['url']);
		}
	}


	switch ($network) {
		case 'facebook':
			$url = sprintf ( 'https://www.facebook.com/sharer/sharer.php?u=%1$s&t=%2$s', $share ['url'], $share ['title'] );			
			break;
		case 'facebook_like':
			$url = sprintf ( 'https://www.facebook.com/plugins/like.php?href=%1$s', $share ['url'] );
			break;
		case 'facebook_advanced':
			$fbappid = essb_options_value( 'facebookadvancedappid');
			$url = 'https://www.facebook.com/dialog/feed?app_id='.$fbappid.'&amp;display=popup&amp;name='.$share['title'].'&amp;link='.$share['url'].'';

			if ($share['image'] != '') {
				$url .= '&picture='.$share['image'];
			}
			if ($share['description'] != '') {
				$url .= '&description='.$share['description'];
			}
			break;
		case 'twitter' :
			if ($share['short_url_twitter'] == '') {
				$share['short_url_twitter'] = $share['url'];
			}

			if (isset($share['clear_twitter_url'])) {
				if ($share['clear_twitter_url']) { $share['short_url_twitter'] = ''; }
			}

			/**
			 * Changing econding of options to happen via the included inside plugin function instead of 
			 * replacing all one
			 */			
			$use_tweet = $share ['twitter_tweet'];
			$use_tweet = essb_core_helper_prevent_percent_break_tweet($use_tweet);
			$use_tweet = essb_core_helper_textencode( $use_tweet );

			// @since 3.1 Twitter message optimization
			$twitter_message_optimize = ESSBGlobalSettings::$twitter_message_optimize;
			if ($twitter_message_optimize) {
				$twitter_message_optimize_method = essb_options_value('twitter_message_optimize_method');

				if (!class_exists('ESSBCoreExtenderTweetOptimization')) {
					include_once (ESSB3_PLUGIN_ROOT . 'lib/core/extenders/essb-core-extender-tweet-optimization.php');

				}
				$optmized_tweet = ESSBCoreExtenderTweetOptimization::twitter_message_optimization($use_tweet, $share['short_url_twitter'], $share['twitter_user'], $share ['twitter_hashtags'], $twitter_message_optimize_method);
				$use_tweet = $optmized_tweet['tweet'];
				$share['twitter_user'] = $optmized_tweet['user'];
				$share['twitter_hashtags'] = $optmized_tweet['hashtags'];
			}

			$twitter_pass_user = ($share['twitter_user'] != '') ? sprintf('&amp;related=%1$s&amp;via=%1$s', $share['twitter_user']) : '';
			$twitter_pass_hastags = ($share ['twitter_hashtags'] != '') ? sprintf('&amp;hashtags=%1$s', $share ['twitter_hashtags']) : '';

			$url = sprintf ( 'https://twitter.com/intent/tweet?text=%1$s&amp;url=%2$s&amp;counturl=%3$s%4$s%5$s', $use_tweet, $share ['short_url_twitter'], $share ['full_url'], $twitter_pass_user, $twitter_pass_hastags );
			break;
		case 'google' :
			$url = sprintf ( 'https://plus.google.com/share?url=%1$s', $share ['url'] );
			break;
		case 'pinterest' :
			$pin_image = $share['image'];
			$custom_pin_image = isset($share['pinterest_image']) ? $share['pinterest_image'] : '';
			if (!empty($custom_pin_image)) {
				$pin_image = $custom_pin_image;
			}
			
			// @ updating from bookmarklet to button
			if (essb_option_bool_value('pinterest_using_api')) {
                $url = sprintf ( 'https://pinterest.com/pin/create/bookmarklet/?media=%1$s&url=%2$s&title=%3$s&description=%4$s', $pin_image, $share ['url'], $share ['title'], $pinterest_description );
			}
			else {
			    $url = sprintf ( 'https://pinterest.com/pin/create/button/?media=%1$s&url=%2$s&description=%3$s', $pin_image, $share ['url'], $pinterest_description );
			}
			
			// Repin
			if (isset($share['pinterest_id']) && !empty($share['pinterest_id'])) {
			    $url = sprintf('https://www.pinterest.com/pin/%s/repin/x/', esc_attr($share['pinterest_id']));
			}
			break;
		case 'pinterest_picker' :
			$url = '#';
			$api_command = "essb.pinterest_picker(&#39;" . $salt . "&#39;); return false;";
			break;
		case 'linkedin' :
			$use_message = essb_core_helper_textencode( $share ['title'] );

			$url = sprintf ( 'https://www.linkedin.com/shareArticle?mini=true&amp;ro=true&amp;trk=EasySocialShareButtons&amp;title=%1$s&amp;url=%2$s', $use_message, $share ['url'] );
			break;
		case 'digg' :
			$url = sprintf ( 'http://digg.com/submit?phase=2%20&amp;url=%1$s&amp;title=%2$s', $share ['url'], essb_core_helper_textencode($share ['title']) );
			break;
		case 'reddit' :
			$url = sprintf ( 'http://reddit.com/submit?url=%1$s&amp;title=%2$s', $share ['url'], $share ['title'] );
			break;
		case 'del' :
			$url = sprintf ( 'https://del.icio.us/login?log=out&provider=essb&title=%2$s&url=%1$s&v=5', $share ['url'], $share ['title'] );
			break;
		case 'buffer' :
			$url = sprintf ( 'https://bufferapp.com/add?url=%1$s&text=%2$s&via=%3$s&picture=&count=horizontal&source=button', $share ['url'], essb_core_helper_textencode($share ['title']), $share ['twitter_user'] );
			break;
		case 'love' :
			$url = '#';
			$api_command = "essb.loveThis(&#39;" . $salt . "&#39;); return false;";
			break;
		case 'stumbleupon' :
			$url = sprintf ( 'http://www.stumbleupon.com/badge/?url=%1$s', $share ['full_url'] );
			break;
		case 'tumblr' :
			$url = sprintf ( 'https://www.tumblr.com/widgets/share/tool?canonicalUrl=%2$s&amp;title=%1$s&amp;posttype=link', essb_core_helper_textencode($share ['title']), ( $share ['url'] ) );
			break;
		case 'vk' :
		    $url = sprintf ( 'http://vk.com/share.php?url=%1$s&image=%2$s&description=%3$s&title=%4$s', $share ['url'], $share['image'], essb_core_helper_nonlatin_textencode($share['description']), essb_core_helper_nonlatin_textencode($share['title']) );
			break;
		case 'ok' :
		    $url = sprintf('https://connect.ok.ru/offer?url=%1$s&title=%2$s&imageUrl=%3$s', $share ['url'], essb_core_helper_nonlatin_textencode($share['title']), $share['image']);
			break;
		case 'weibo' :
			$url = sprintf ( 'http://service.weibo.com/share/share.php?url=%1$s&title=%2$s&pic=%3$s', $share ['url'], essb_core_helper_textencode($share['title']), $share['image'] );
			break;
		case 'xing' :
			$url = sprintf ( 'https://www.xing.com/social_plugins/share?h=1;url=%1$s', $share ['url'] );
			break;
		case 'pocket' :
			$url = sprintf ( 'https://getpocket.com/save?title=%1$s&url=%2$s', essb_core_helper_textencode($share ['title']), ( $share ['url'] ) );
			break;
		case 'mwp' :
			$url = sprintf ( 'http://managewp.org/share/form?url=%1$s', ( $share ['url'] ) );
			break;
		case 'whatsapp' :
			if ($share['short_url_whatsapp'] == '') {
				$share['short_url_whatsapp'] = $share['url'];
			}
			$url = sprintf ( 'whatsapp://send?text=%1$s%3$s%2$s', essb_core_helper_textencode ( $share ['title_plain'] ),  ( $share ['short_url_whatsapp'] ), '%20' );
			$api_command = "essb.tracking_only('', 'whatsapp', '".$salt."', true);";

			if (essb_option_bool_value('whatsapp_api')) {
				$api_command = "essb.whatsapp('".sprintf('%1$s%3$s%2$s', essb_core_helper_textencode ( $share ['title_plain'] ),  ( $share ['short_url_whatsapp'] ), '%20')."', '".$salt."'); return false;";
			}

			break;
		case 'meneame' :
			$url = sprintf ( 'http://www.meneame.net/submit.php?url=%1$s', $share ['url'] );
			break;
		case 'print_friendly' :
			$url = sprintf ( 'http://www.printfriendly.com/print/?url=%1$s', $share ['url'] );
			break;
		case 'print' :
			$url = "#";
			$api_command = "essb.print(&#39;" . $salt . "&#39;); return false;";
			break;
		case 'mail' :
			if (!isset($share['mail_subject'])) {
				$share['mail_subject'] = '';
			}
			if (!isset($share['mail_body'])) {
				$share['mail_body'] = '';
			}

			$url = sprintf('mailto:?subject=%1$s&amp;body=%2$s', rawurlencode($share['mail_subject']), rawurlencode($share['mail_body']));
			
			$url = str_replace('"', '%22', $url);
			$url = str_replace("'", '%27', $url);
			$url = str_replace('&nbsp;', ' ', $url);
			$url = str_replace('&quot;', '%22', $url);
			$url = str_replace('%26quot;', '%22', $url);
			$url = str_replace('%26#039;', '%27', $url);
			$url = str_replace('\n', '%0A', $url);
			$url = str_replace('\r', '%0D', $url);
			$url = str_replace('%26%23039%3B', '%27', $url); // unicode quote
			
			/**
			 * Fix for the AMP in the subject and mail
			 */
			$url = str_replace('%26amp;', '%26', $url);
			$url = str_replace('%26amp%3B', '%26', $url);
			
			$api_command = "essb.tracking_only('', 'mail', '".$salt."', true);";
			break;
		case 'mail_form' :
			$url = "#";
			$api_command = "essb_open_mailform(&#39;" . $salt . "&#39;); return false;";
			break;
		case 'share':
		case 'more' :
			$url = "#";
			$api_command = "essb.toggle_more(&#39;" . $salt . "&#39;); return false;";
			break;
		case 'less' :
			$url = "#";
			$api_command = "essb.toggle_less(&#39;" . $salt . "&#39;); return false;";
			break;
		case 'more_popup' :
			$url = "#";
			$api_command = "essb.toggle_more_popup(&#39;" . $salt . "&#39;); return false;";
			break;
		case 'flattr' :
			$url = sprintf('https://flattr.com/sharing?url=%1$s', $share['url']);
			break;
			// @since 3.0
		case 'blogger' :
			$url = sprintf ( 'https://www.blogger.com/blog_this.pyra?t&u=%1$s&n=%2$s', $share ['url'], $share ['title'] );
			break;
		case 'amazon' :
			$url = sprintf ( 'http://www.amazon.com/gp/wishlist/static-add?u=%1$s&t=%2$s', $share ['url'], essb_core_helper_textencode($share ['title']) );
			break;
		case 'yahoomail' :
			$url = sprintf ( 'http://compose.mail.yahoo.com/?body=%1$s', $share ['url'] );
			break;

		case 'gmail' :
			$url = sprintf ( 'https://mail.google.com/mail/u/0/?view=cm&fs=1&su=%2$s&body=%1$s&ui=2&tf=1', $share ['url'], essb_core_helper_textencode($share ['title']) );
			break;

		case 'aol' :
			$url = sprintf ( 'http://webmail.aol.com/Mail/ComposeMessage.aspx?subject=%2$s&body=%1$s', $share ['url'], $share ['title'] );
			break;

		case 'newsvine' :
			$url = sprintf ( 'http://www.newsvine.com/_tools/seed&save?u=%1$s&h=%2$s', $share ['url'], $share ['title'] );
			break;

		case 'hackernews' :
			$url = sprintf ( 'https://news.ycombinator.com/submitlink?u=%1$s&t=%2$s', $share ['url'], $share ['title'] );
			break;

		case 'evernote' :
			$url = sprintf ( 'http://www.evernote.com/clip.action?url=%1$s&title=%2$s', $share ['url'], $share ['title'] );
			break;
		case 'myspace':
			$url = sprintf( 'https://myspace.com/post?u=%1$s', esc_attr( $share ['url'] ));
			break;
		case 'mailru':
			$url = sprintf('http://connect.mail.ru/share?url=%1$s&title=%2$s&description=%3$s', $share['url'], essb_core_helper_textencode($share['title']), essb_core_helper_textencode($share['description']));
			break;
		case 'viadeo':
			$url = sprintf('https://www.viadeo.com/?url=%1$s&amp;title=%2$s', $share['url'], $share['title']);
			break;
		case 'line':
			$url = 'https://social-plugins.line.me/lineit/share?url='.($share['url']);
			$api_command = "essb.tracking_only('', 'line', '".$salt."', true);";

			break;
		case 'flipboard':
			$url = sprintf('https://share.flipboard.com/bookmarklet/popout?url=%1$s&title=%2$s', $share['url'], $share['title']);
			break;
		case 'yummly' :
			$url = sprintf ( 'http://www.yummly.com/urb/verify?url=%2$s&title=%3$s&image=%1$s&yumtype=button', $share ['image'], $share ['url'], essb_core_helper_textencode($share ['title']), essb_core_helper_textencode($share ['description']) );
			break;
		case 'sms' :
			if ($share ['short_url_whatsapp'] == '') {
				$share ['short_url_whatsapp'] = $share ['url'];
			}

			$url = sprintf ( 'sms:%4$sbody=%1$s%3$s%2$s', essb_core_helper_textencode ( $share ['title_plain'] ),  ( $share ['short_url_whatsapp'] ), '%20', '&' );
			$api_command = "essb.sms('".sprintf('%1$s%3$s%2$s', essb_core_helper_textencode ( $share ['title_plain'] ),  ( $share ['short_url_whatsapp'] ), '%20')."', '".$salt."'); return false;";
				
			break;
		case 'viber' :
			if ($share ['short_url_whatsapp'] == '') {
				$share ['short_url_whatsapp'] = $share ['url'];
			}

			$url = sprintf ( 'viber://forward?text=%1$s%3$s%2$s', essb_core_helper_textencode ( $share ['title_plain'] ),  ( $share ['short_url_whatsapp'] ), '%20' );
			$api_command = "essb.tracking_only('', 'viber', '".$salt."', true);";

			break;

		case 'telegram' :
			if ($share ['short_url_whatsapp'] == '') {
				$share ['short_url_whatsapp'] = $share ['url'];
			}

			// @since 3.4.2 - we include telegram alternative share method
			if (ESSBGlobalSettings::$telegram_alternative) {
				$url = sprintf ( 'https://telegram.me/share/url?url=%2$s&text=%1$s', essb_core_helper_textencode ( $share ['title_plain'] ),  ( $share ['short_url_whatsapp'] ) );
			}
			else {
				$url = sprintf ( 'tg://msg?text=%1$s%3$s%2$s', essb_core_helper_textencode ( $share ['title_plain'] ),  ( $share ['short_url_whatsapp'] ), '%20' );
			}
			$api_command = "essb.tracking_only('', 'telegram', '".$salt."', true);";

			break;
		case 'subscribe':

			if (ESSBGlobalSettings::$subscribe_function == 'link') {
				$url = ESSBGlobalSettings::$subscribe_link;
				$api_command = "essb.tracking_only('', 'subscribe', '".$salt."', true);";
			}
			else {
				$url = "#";
				$api_command = "essb.toggle_subscribe('".$salt."'); return false;";
			}

			break;
		case 'kakaotalk':
			$url = sprintf ( 'https://story.kakao.com/share?url=%1$s', $share ['url']);
			break;
		case 'skype':
			$url = sprintf ( 'https://web.skype.com/share?url=%1$s', $share ['url']);
			break;
		case 'messenger':			
			$url = sprintf('fb-messenger://share/?link=%1$s', $share ['url']);
			$api_command = "essb.fbmessenger('".ESSBGlobalSettings::$fbmessengerapp."', '".$share['url']."', '".$salt."'); return false;";
			break;
		case 'livejournal':
			$url = sprintf('http://www.livejournal.com/update.bml?subject=%1$s&event=%2$s', $share['title_plain'], urlencode('<a href="'.$share['url'].'">'.$share['title_plain'].'</a>'));
			break;
		case 'yammer':
			$url = sprintf('https://www.yammer.com/messages/new?login=true&amp;trk_event=yammer_share&amp;status=%1$s %2$s', essb_core_helper_textencode ( $share ['title_plain'] ), ( $share['url']));
			break;
		case 'meetedgar':
			$url = htmlentities('javascript:(function()%7B!function()%7Bvar e%3Ddocument.getElementsByTagName("head")%5B0%5D,t%3Ddocument.createElement("script")%3Bt.type%3D"text/javascript",t.src%3D"//app.meetedgar.com/share.js%3F"%2BMath.floor(99999*Math.random()),e.appendChild(t)%7D()%7D)()%3B');
			$api_command = "essb.tracking_only('', 'meetedgar', '".$salt."', true);";
			break;
		case 'fintel':
			$url = sprintf('https://fintel.io/submit?url=%1$s', $share ['url']);
			break;
		case 'instapaper':
			$url = sprintf('https://www.instapaper.com/hello2?url=%1$s&title=%2$s&description=%3$s', $share ['url'], $share['title'], $share['description']);
			break;
		case 'copy':
			$url = '#';
			$copy_url = isset($share['short_url_twitter']) && $share['short_url_twitter'] != '' ? $share['short_url_twitter'] : $share['url'];
			
			$api_command = 'essb.copy_link(&#39;' . $salt . '&#39;,&#39;'.urldecode($copy_url).'&#39;); return false;'; 
			break;
		case 'mix':
			// 2019-10-10 mix.com has updated the share command from. The old command is still working
			// which is why it stays here for a referrence.
			// $url = sprintf('https://mix.com/extension/add?source=%1$s', $share ['url']);
			$url = sprintf('https://mix.com/add?url=%1$s', $share['url']);
			break;
		default:
			// @since 3.0 - module parsing social buttons or custom social buttons
			if (has_filter("essb4_shareapi_api_command_{$network}")) {
				$api_command = apply_filters("essb4_shareapi_api_command_{$network}", $share);
			}
			if (has_filter("essb4_shareapi_url_{$network}")) {
				$url = apply_filters("essb4_shareapi_url_{$network}", $share);
			}
			break;
	}
	
	/**
	 * Support of Twitter button for AMP
	 */
	if ($network == 'twitter' && $amp_endpoint) {
	    $network = 'twitter_amp';
	}
	
	if ($network == 'twitter') {
		$api_command = sprintf('essb.window(&#39;%1$s&#39;,&#39;%2$s&#39;,&#39;%3$s&#39;); return false;', $url, $network, $salt);
		$url = '#';
	}

	if (has_filter("essb_shareapi_command_{$network}")) {
		$api_command = apply_filters("essb_shareapi_command_{$network}", $share);
	}

	if (has_filter("essb_shareapi_url_{$network}")) {
		$url = apply_filters("essb_shareapi_url_{$network}", $share);
	}

	if ($api_command == '') {
		$api_command = sprintf('essb.window(&#39;%1$s&#39;,&#39;%2$s&#39;,&#39;%3$s&#39;); return false;', $url, $network, $salt);
	}

	/**
	 * Compatibility with Social Proof Notifications
	 */
	if (isset($share['essb_encode_url']) && $share['essb_encode_url']) {
		$url = str_replace ('&', '&amp;', $url);
	}

	$share_command = array('url' => $url, 'api_command' => $api_command);

	// @since 5.5 - adding a global share generation filter that will make possible to change the execution
	// of all build in social networks and modify the custom generated share URL
	if (has_filter("essb_share_command_{$network}")) {
		$share_command = apply_filters("essb_share_command_{$network}", $share_command);
	}
	
	return $share_command;
}


function essb_draw_buttons_start($style = array(), $position = '', $salt = '', $like_or_share = 'share', $share = array(), $cached_counters = array(), $networks = array()) {

	$network_hidden_name_class = essb_button_get_network_hidden_class($style);
	
	$template = essb_template_folder( $style ['template'] );
	$instance_template = $template;
	if ($template != '') {
		$template = ' essb_template_' . $template;
	}

	$css_class_width = '';
	if ($style['button_width'] == 'fixed') {
		$style = essb_button_generate_fixed_width($style);
	}

	if ($style['button_width'] == 'full') {
		$style = essb_button_generate_full_width($style);
	}

	if ($style['button_width'] == 'column') {
		$style = essb_button_generate_column_width($style);
	}

	/**
	 * Applying new flexible width options added in version 5.9
	 */
	if ($style['button_width'] == 'flex') {
		$style = essb_button_generate_flex_width($style);
	}
	
	$css_class_width = isset($style['class_width']) ? $style['class_width'] : '';

	if (isset($style['button_size'])) {
		if ($style['button_size'] != '') {
			$css_class_width .= ' essb_size_'.$style['button_size'];
		}
	}

	$css_class_align = '';
	if ($style['button_align'] == 'right') {
		$css_class_align = ' essb_links_right';
	}
	if ($style['button_align'] == 'center') {
		$css_class_align = ' essb_links_center';
	}
	
	/**
	 * Adaptive stretched width
	 */
	if ($style['button_align'] == 'stretched') {
		$css_class_align = ' essb_width_flex essb_network_align_center essb_responsive_adaptive';	
	}

	// modern counter style
	$essb_css_modern_counter_class = '';
	if ($style ['counter_pos'] == 'leftm') {
		$style ['counter_pos'] = 'left';
		$essb_css_modern_counter_class = ' essb_counter_modern_left';
	}

	if ($style ['counter_pos'] == 'rightm') {
		$style ['counter_pos'] = 'right';
		$essb_css_modern_counter_class = ' essb_counter_modern_right';
	}

	if ($style ['counter_pos'] == 'top') {
		$style ['counter_pos'] = 'left';
		$essb_css_modern_counter_class = ' essb_counter_modern_top';
	}

	if ($style ['counter_pos'] == 'topm') {
		$style ['counter_pos'] = 'left';
		$essb_css_modern_counter_class = ' essb_counter_modern_top_mini';
	}
	if ($style ['counter_pos'] == 'bottom') {
		$essb_css_modern_counter_class = ' essb_counter_modern_bottom';
	}
	if ($style ['counter_pos'] == 'topn') {
		$essb_css_modern_counter_class = ' essb_counter_modern_topn';
	}
	
	if ($style['counter_pos'] == 'insidem' || $style['counter_pos'] == 'insidenamem' || $style['counter_pos'] == 'insidebeforenamem') {
		$essb_css_modern_counter_class = ' essb_counter_inside_mini';
	}

	if (isset($style['button_animation'])) {
		if ($style['button_animation'] != '' && $style['button_animation'] != 'no') {
			$css_class_align .= ' '.$style['button_animation'];
		}
	}

	// share button class patch
	if (in_array('share', $networks)) {
		if (isset($style['share_button_style'])) {
			if ($style['share_button_style'] != '') {
				$css_class_align .= ' essb_sharebtn_'.$style['share_button_style'];
			}

			$share_counter_pos = isset($style['share_button_counter']) ? $style['share_button_counter'] : '';
			if ($share_counter_pos == '') {
				$share_counter_pos = essb_option_value('share_button_counter');
			}
			if ($share_counter_pos != '') {
				$css_class_align .= ' essb_sharebtn_counter_'.$share_counter_pos;
			}
		}
	}

	$counter_class = ($style ['show_counter']) ? ' essb_counters' : '';

	$total_text = ESSBGlobalSettings::$counter_total_text;

	$activate_total_counter_text = ESSBGlobalSettings::$counter_total_text_shares;
	$activate_total_counter_text_singular = ESSBGlobalSettings::$counter_total_text_share;
	$activate_total_counter_icon = ESSBGlobalSettings::$counter_total_icon;

	$css_hide_total_counter = '';
	if ($style ['total_counter_hidden_till'] != '') {
		if ($style ['total_counter_pos'] == 'hidden') {
			$css_hide_total_counter = ' style="display: none !important;"';
		}
		else {
			$css_hide_total_counter = ' style="display: none !important;" data-essb-hide-till="' . $style ['total_counter_hidden_till'] . '"';
		}
	}

	$cached_total_counter = '';
	$original_cached_total_counter = '';
	$cached_counters_active = false;
	if (defined('ESSB3_CACHED_COUNTERS')) {
		$cached_total_counter = isset($cached_counters['total']) ? $cached_counters['total'] : '0';
		$original_cached_total_counter = $cached_total_counter;

		if ($style ['total_counter_hidden_till'] != '') {
			if (intval($cached_total_counter) > intval($style ['total_counter_hidden_till'])) {
				$css_hide_total_counter = '';
			}
		}

		$cached_total_counter = essb_kilomega_format($cached_total_counter, 'total');
		$cached_counters_active = true;

		// display veiws instead of shares - @since 5.0
		if ($position == 'sharebottom' && essb_option_value('mobile_sharebuttonsbar_total_source') == 'views') {
			$cached_total_counter = essb_bridge_get_views($share['post_id']);
			$activate_total_counter_text = essb_bridge_get_views_text();
		}
		
		// since 6.2.5 added support for singular/plural values
		if ($original_cached_total_counter <= 1) {
			$activate_total_counter_text = $activate_total_counter_text_singular;
		}

	}
	else {
		// display veiws instead of shares - @since 5.0
		if ($position == 'sharebottom' && essb_option_value('mobile_sharebuttonsbar_total_source') == 'views') {
			$cached_total_counter = essb_bridge_get_views($share['post_id']);
			$activate_total_counter_text = essb_bridge_get_views_text();
		}
	}



	$css_class_nospace = ($style['nospace']) ? ' essb_nospace' : '';

	$css_class_nostats = '';
	if (isset($style['nostats'])) {
		$css_class_nostats = ($style['nostats']) ? ' essb_nostats' : '';
	}

	// applying the hidding features of the display method

	if (essb_is_hidden_on_device('mobile', $position, $style)) {
		$css_class_nostats .= ' essb_mobile_hidden';
	}
	if (essb_is_hidden_on_device('tablet', $position, $style)) {
		$css_class_nostats .= ' essb_tablet_hidden';
	}
	if (essb_is_hidden_on_device('desktop', $position, $style)) {
		$css_class_nostats .= ' essb_desktop_hidden';
	}

	$like_share_position = ($like_or_share == 'share') ? ' essb_share' : ' essb_native';
	$links_start = '';

	$key_position = $position;
	if ($position == 'sidebar') {
		$current_sidebar_pos = essb_options_value('sidebar_pos');

		if (!empty($style['sidebar_pos'])) {
			$current_sidebar_pos = $style['sidebar_pos'];
		}
		if ($current_sidebar_pos != '' && $current_sidebar_pos != 'left') {
			$key_position .= ' essb_displayed_sidebar_'.$current_sidebar_pos;
		}

		$sidebar_entry_ani = essb_option_value('sidebar_entry_ani');
		if ($sidebar_entry_ani != '') {
			// change in 4.2 to assing at $like_share_position
			$like_share_position .= ' essb_sidebar_transition essb_sidebar_transition_'.$sidebar_entry_ani;
		}
		
		$sidebar_location = essb_sanitize_option_value('sidebar_location');
		$like_share_position .= ' essb_sidebar_location_'.($sidebar_location != '' ? $sidebar_location : 'middle');
		
		$sidebar_icon_space = essb_sanitize_option_value('sidebar_icon_space');
		if ($sidebar_icon_space != '') {
			$like_share_position .= ' essb_sidebar_icon_'.$sidebar_icon_space;
		}
	}
	
	/**
	 * Apply Responsive Content Style
	 */
	if (essb_option_bool_value('activate_automatic_mobile_content') && ($position == 'top' || $position == 'bottom' || $position == 'float' || $position == 'bimber_top' || $position == 'bimber_bottom')) {
		$like_share_position .= ' essb_content_responsive';
	}


	$counter_url = $share['url'];
	$counter_full_url = $share['full_url'];

	if (!defined('ESSB3_LIGHTMODE')) {
		$ga_tracking_code = ESSBGlobalSettings::$activate_ga_campaign_tracking;
		if ($ga_tracking_code != '' || strpos($counter_url, '{network}') !== false) {

			$post_ga_campaign_tracking = get_post_meta(get_the_ID(), 'essb_activate_ga_campaign_tracking', true);
			if ($post_ga_campaign_tracking != '') {
				$ga_tracking_code = $post_ga_campaign_tracking;
			}
			$ga_tracking_code = str_replace('&', '%26', $ga_tracking_code);
			$counter_url = str_replace($ga_tracking_code, '', $counter_url);
			$counter_full_url = str_replace('{network}', 'twitter', $counter_full_url);
			$counter_full_url = str_replace('{title}', '', $counter_full_url);
		}
	}

	$extra_start_options = '';

	$buttons_id = 'essb_displayed_'.$position.'_'.$salt;

	if (has_filter('essb_sharebuttons_draw_id')) {
		$buttons_id = apply_filters('essb_sharebuttons_draw_id', $buttons_id, $style);
	}
	
	/**
	 * Filter added in version 6.2.4 to allow generation of custom options to the start of share button
	 */
	if (has_filter('essb_sharebuttons_open_element')) {
		$extra_start_options = apply_filters('essb_sharebuttons_open_element', $extra_start_options, $position, $style);
	}

	$links_start = '';

	/**
	 * Apply the custom code that is set inside settings for the position
	 * @var unknown_type
	 */
	$position_code_before = essb_option_value($position.'_code_before');
	if (essb_option_bool_value($position.'_code') && $position_code_before != '') {
		$position_code_before = stripslashes($position_code_before);
		$position_code_before = str_replace('{instance_class}', '.essb_links.essb_'.$salt, $position_code_before);
		$position_code_before = do_shortcode($position_code_before);

		$links_start .= $position_code_before;
	}

	$links_start .= sprintf ( '<div class="essb_links%1$s%2$s essb_displayed_%3$s%4$s%5$s essb_%6$s%13$s%14$s%15$s%16$s print-no"
			id="'.$buttons_id.'" data-essb-postid="%7$s" data-essb-position="%3$s" data-essb-button-style="%8$s"
			data-essb-template="%9$s" data-essb-counter-pos="%10$s" data-essb-url="%11$s" data-essb-fullurl="%12$s" data-essb-instance="%6$s"%17$s>',
			$counter_class, $essb_css_modern_counter_class, $key_position, $like_share_position, $template, $salt,
			$share['post_id'], $style['button_style'], $instance_template, $style['counter_pos'], $counter_url, $counter_full_url, $css_class_width,
			$css_class_align, $css_class_nospace, $css_class_nostats, $extra_start_options );

	$deactivate_message_for_location = essb_options_bool_value( $position.'_text_deactivate');
	//@since 3.4.2 - deactivate message before share buttons on mobile display methods
	if ($position == 'sharebottom' || $position == 'sharebar' || $position == 'sharepoint' || $position == 'postbar' || $position == 'point') {
		$deactivate_message_for_location = true;
	}

	if ($deactivate_message_for_location) {
		$style ['message_share_before_buttons'] = '';
		$style ['message_share_buttons'] = '';
	}


	if ($like_or_share == 'share' && $style ['message_share_buttons'] != '') {
		$links_start .= essb_format_message_buttons($style ['message_share_buttons'], 'above', $share);
	}

	if (has_filter('essb4_before_draw_buttons')) {
		$links_start .= apply_filters('essb4_before_draw_buttons', $share, $style);
	}

	$links_start .= sprintf ( '<ul class="essb_links_list%1$s">', $network_hidden_name_class );

	// generating share buttons template start
	if ($like_or_share == 'share' && $style ['message_share_before_buttons'] != '') {
		$links_start .= essb_format_message_buttons($style ['message_share_before_buttons'], 'before', $share);		
	}
	
	if ($like_or_share == 'share' && $style ['show_counter']) {
		if ($style ['total_counter_pos'] == 'left' || $style ['total_counter_pos'] == 'leftbig' ||
				$style ['total_counter_pos'] == 'before' || $style ['total_counter_pos'] == 'hidden' || $style ['total_counter_pos'] == 'leftbigicon') {

				    // Removing the !essb_is_mobile() check. This we made to provide animation work on any device
			if (essb_option_bool_value('animate_total_counter')) {
				$cached_total_counter = '<span class="essb_animated" data-cnt="'.$original_cached_total_counter.'" data-cnt-short="'.$cached_total_counter.'">&nbsp;</span>';
			}

			// left
			if ($style ['total_counter_pos'] == 'left' || $style ['total_counter_pos'] == 'hidden') {
				$display_total_text = '';
				if ($cached_counters_active) {
					$display_total_text = '<span class="essb_total_text">'.$total_text.'</span>';
				}
				$links_start .= '<li class="essb_item essb_totalcount_item" ' . ($style ['total_counter_pos'] == "hidden" ? 'style="display: none !important;"' : '') . $css_hide_total_counter . ' data-counter-pos="' . $style ['counter_pos'] . '"><span class="essb_totalcount essb_t_l"  title="' . $total_text . '">'.$display_total_text.'<span class="essb_t_nb">'.$cached_total_counter.'</span></span></li>';
			}
			if ($style ['total_counter_pos'] == 'leftbig' || $style ['total_counter_pos'] == 'leftbigicon') {
				$links_start .= '<li class="essb_item essb_totalcount_item" ' . ($style ['total_counter_pos'] == "hidden" ? 'style="display: none !important;"' : '') . $css_hide_total_counter . ' data-counter-pos="' . $style ['counter_pos'] . '"><span class="essb_totalcount essb_t_l_big '.($style['total_counter_pos'] == 'leftbigicon' ? 'essb_total_icon essb_icon_'.$activate_total_counter_icon : '').'" title="" data-shares-text="'.$activate_total_counter_text.'"><span class="essb_t_nb">'.$cached_total_counter.'<span class="essb_t_nb_after">'.$activate_total_counter_text.'</span></span></span></li>';
			}
			if ($style ['total_counter_pos'] == 'before') {
				$userbased_text = $style ['total_counter_afterbefore_text'];
				if ($userbased_text == '') {
					$userbased_text = '{TOTAL} shares';
				}

				$userbased_text = str_replace ( '{TOTAL}', '<span class="essb_t_nb">'.$cached_total_counter.'</span>', $userbased_text );
				$links_start .= '<li class="essb_item essb_totalcount_item essb_totalcount_item_before" ' . ($style ['total_counter_pos'] == "hidden" ? 'style="display: none !important;"' : '') . $css_hide_total_counter . ' data-counter-pos="' . $style ['counter_pos'] . '"><span class="essb_totalcount essb_t_before" title="">' . $userbased_text . '</span></li>';
			}
		}
	}

	// ESSB Post Views add-on bridge
	// @since 3.5
	if (defined('ESSB3_POSTVIEWS_ACTIVE')) {
		$postviews_position = essb_postview_position($position, $style['total_counter_pos']);


		if (!empty($style['postviews'])) {

			$postviews_position = essb_postviews_position_from_shortcode($style['postviews'], $style['total_counter_pos']);
		}

		if ($postviews_position == 'left') {
			$links_start .= essb_generate_views_code($share['post_id'], $postviews_position);
		}
	}

	return $links_start;
}


function essb_draw_buttons_end($style = array(), $position = '', $salt = '', $like_or_share = 'share', $native_buttons = array(), $cached_counters = array(), $share = array()) {


	$css_hide_total_counter = '';
	if ($style ['total_counter_hidden_till'] != '') {
		$css_hide_total_counter = ' style="display: none !important;" data-essb-hide-till="' . $style ['total_counter_hidden_till'] . '"';
	}
	
	$activate_total_counter_text = ESSBGlobalSettings::$counter_total_text_shares;
	$activate_total_counter_text_singular = ESSBGlobalSettings::$counter_total_text_share;
	
	$cached_total_counter = '';
	$original_cached_total_counter = '';
	$cached_counters_active = false;
	if (defined('ESSB3_CACHED_COUNTERS')) {
		$cached_total_counter = isset($cached_counters['total']) ? $cached_counters['total'] : '0';
		$original_cached_total_counter = $cached_total_counter;

		if ($style ['total_counter_hidden_till'] != '') {
			if (intval($cached_total_counter) > intval($style ['total_counter_hidden_till'])) {
				$css_hide_total_counter = '';
			}
		}

		$cached_total_counter = essb_kilomega_format($cached_total_counter, 'total');
		$cached_counters_active = true;
		
		
		// since 6.2.5 added support for singular/plural values
		if ($original_cached_total_counter <= 1) {
			$activate_total_counter_text = $activate_total_counter_text_singular;
		}
	}

	$links_start = '';
	$total_text = ESSBGlobalSettings::$counter_total_text;
	$activate_total_counter_icon = ESSBGlobalSettings::$counter_total_icon;

	// generating share buttons template start
	if ($like_or_share == 'share') {

		// ESSB Post Views add-on bridge
		// @since 3.5
		if (defined('ESSB3_POSTVIEWS_ACTIVE')) {
			$postviews_position = essb_postview_position($position, $style['total_counter_pos']);

			if (!empty($style['postviews'])) {
				$postviews_position = essb_postviews_position_from_shortcode($style['postviews'], $style['total_counter_pos']);
			}


			if ($postviews_position == 'right') {

				$links_start .= essb_generate_views_code($share['post_id'], $postviews_position);
			}
		}

		if ($style ['show_counter']) {
			if ($style ['total_counter_pos'] == 'right' || $style ['total_counter_pos'] == 'rightbig'
					|| $style ['total_counter_pos'] == 'after' || $style['total_counter_pos'] == 'rightbigicon') {

			    // Removing the !essb_is_mobile() check. This we made to provide animation work on any device
				if (essb_option_bool_value('animate_total_counter')) {
					$cached_total_counter = '<span class="essb_animated" data-cnt="'.$original_cached_total_counter.'" data-cnt-short="'.$cached_total_counter.'">&nbsp;</span>';
				}


				// left
				if ($style ['total_counter_pos'] == 'right' || $style ['total_counter_pos'] == 'hidden') {
					$display_total_text = '';
					if ($cached_counters_active) {
						$display_total_text = '<span class="essb_total_text">'.$total_text.'</span>';
					}

					$links_start .= '<li class="essb_item essb_totalcount_item" ' . ($style ['total_counter_pos'] == "hidden" ? 'style="display: none !important;"' : '') . $css_hide_total_counter . ' data-counter-pos="' . $style ['counter_pos'] . '"><span class="essb_totalcount essb_t_r"  title="' . $total_text . '">'.$display_total_text.'<span class="essb_t_nb">'.$cached_total_counter.'</span></span></li>';
				}
				if ($style ['total_counter_pos'] == 'rightbig' || $style['total_counter_pos'] == 'rightbigicon') {
					$links_start .= '<li class="essb_item essb_totalcount_item" ' . ($style ['total_counter_pos'] == "hidden" ? 'style="display: none !important;"' : '') . $css_hide_total_counter . ' data-counter-pos="' . $style ['counter_pos'] . '"><span class="essb_totalcount essb_t_r_big '.($style['total_counter_pos'] == 'rightbigicon' ? 'essb_total_icon essb_icon_'.$activate_total_counter_icon : '').'" title="" data-shares-text="'.$activate_total_counter_text.'"><span class="essb_t_nb">'.$cached_total_counter.'<span class="essb_t_nb_after">'.$activate_total_counter_text.'</span></span></span></li>';

				}
				if ($style ['total_counter_pos'] == 'after') {
					$userbased_text = $style ['total_counter_afterbefore_text'];
					if ($userbased_text == '') {
						$userbased_text = '{TOTAL} shares';
					}

					$userbased_text = str_replace ( '{TOTAL}', '<span class="essb_t_nb">'.$cached_total_counter.'</span>', $userbased_text );
					$links_start .= '<li class="essb_item essb_totalcount_item essb_totalcount_item_before" ' . ($style ['total_counter_pos'] == "hidden" ? 'style="display: none !important;"' : '') . $css_hide_total_counter . ' data-counter-pos="' . $style ['counter_pos'] . '"><span class="essb_totalcount essb_t_before" title="">' . $userbased_text . '</span></li>';
				}
			}
		}
	}

	$links_start .= '</ul>';

	if (isset($native_buttons['active'])) {
		if ($native_buttons['active']) {
			if (!$native_buttons['sameline']) {
				$links_start .= '<!--native-->';
			}
		}
	}

	if (has_filter('essb4_after_draw_buttons')) {
		$links_start .= apply_filters('essb4_after_draw_buttons', $share, $style);
	}

	$links_start .= '</div>';

	/**
	 * Apply the custom code that is set inside settings for the position
	 * @var unknown_type
	 */
	$position_code_after = essb_option_value($position.'_code_after');
	if (essb_option_bool_value($position.'_code') && $position_code_after != '') {
		$position_code_after = stripslashes($position_code_after);
		$position_code_after = str_replace('{instance_class}', '.essb_links.essb_'.$salt, $position_code_after);
		$position_code_after = do_shortcode($position_code_after);

		$links_start .= $position_code_after;
	}

	return $links_start;
}



/**
 * Shorten a numeric share value
 * 
 * @param number $val
 * @param string $type
 * 
 * @return string 
 */

function essb_kilomega_format ( $val, $type) {
	$value_format_type = ($type == 'total') ? essb_option_value('total_counter_format') : essb_option_value('counter_format');

	if ($value_format_type == 'full' || $value_format_type == 'fulldot' || $value_format_type == 'fullcomma' || $value_format_type == 'fullspace') {
		if ($val) {
			if ($value_format_type == 'full') {
				return number_format(intval($val));
			}
			else if ($value_format_type == 'fulldot') {
				return number_format(intval($val), 0, '', '.');
			}
			else if ($value_format_type == 'fullcomma') {
				return number_format(intval($val), 0, '', ',');
			}
			else if ($value_format_type == 'fullspace') {
				return number_format(intval($val), 0, '', ' ');
			}
			else if ($value_format_type == 'no') {
				return intval($val);
			}
			else {
				return number_format(intval($val));
			}
		}
		else {
			return 0;
		}
	}
	else {
		return essb_kilomega($val);
	}
}

/**
 * Shorten number values
 * 
 * @param unknown $val
 * @return string|number
 */
function essb_kilomega( $val ) {

	if ($val) {
		$val = intval($val);
		if ($val < 1000) {
			return number_format ( $val );
		} else {
			if ($val < 1200) {
				$val = intval ( $val ) / 1000;
				return number_format ( $val, 1 ) .'K';
			} else {
				if ($val < 1000000) {
					$val = intval ( $val ) / 1000;
					return number_format ( $val, 1 ).'K';
				}
				else {
					$val = intval ( $val ) / 1000000;
					return number_format ( $val, 1 ).'M';
				}
			}
		}
	} else {
		return 0;
	}
}

/**
 * Check if the display can run on specific device
 * 
 * @param unknown_type $device
 * @param unknown_type $position
 * @param unknown_type $style
 * @return boolean
 */
function essb_is_hidden_on_device($device = '', $position = '', $style = array()) {
	$r = false;
	
	if ($device == 'mobile') {
		if (essb_option_bool_value($position.'_mobile_deactivate')) {
			$r = true;
		}
		if ($position == 'shortcode') {
			if (isset($style['responsive_hide_mobile']) && $style['responsive_hide_mobile'] == 'true') {
				$r = true;
			}
		}
	}
	if ($device == 'tablet') {
		if (essb_option_bool_value($position.'_tablet_deactivate')) {
			$r = true;
		}
		
		if ($position == 'shortcode') {
			if (isset($style['responsive_only_mobile']) && $style['responsive_only_mobile'] == 'true') {
				$r = true;
			}
		}
	}
	if ($device == 'desktop') {
		if (essb_option_bool_value($position.'_desktop_deactivate')) {
			$r = true;
		}
		
		if ($position == 'shortcode') {
			if (isset($style['responsive_only_mobile']) && $style['responsive_only_mobile'] == 'true') {
				$r = true;
			}
		}
	}
	
	return $r;
}

function essb_prepare_networks_order_by_shares($share_array = array()) {
	arsort($share_array);

	$ordered = array();
	foreach ($share_array as $key => $shares) {
		$ordered[] = $key;
	}

	return $ordered;
}

function essb_bridge_get_views($post_id) {
	$post_views = intval ( get_post_meta ( $post_id, 'essb_views', true ) );

	return essb_kilomega($post_views);
}

function essb_bridge_get_views_text() {
	global $essb3pv_options;

	if (!isset($essb3pv_options)) {
		$essb3pv_options = get_option('essb3-pv');
	}

	return isset($essb3pv_options['postviews_text']) ? $essb3pv_options['postviews_text'] : 'views';
}

function essb_draw_adaptive_stretch_styles($style = array()) {
	if ($style ['counter_pos'] == 'leftm' || $style ['counter_pos'] == 'left') {
		$style ['counter_pos'] = 'insidebeforename';
	}
	
	if ($style ['counter_pos'] == 'right' || $style ['counter_pos'] == 'rightm') {
		$style ['counter_pos'] = 'insidename';
	}
	
	if ($style ['counter_pos'] == 'topm' || $style ['counter_pos'] == 'top') {
		$style ['counter_pos'] = 'topn';
	}
	
	return $style;
}

/**
 * Check the existing style options for a parameter value
 * 
 * @param array $style
 * @param string $param
 * @param mixed $value
 * @return boolean
 */
function essb_style_variable_is($style = '', $param = '', $value = '') {
	$current_value = isset($style[$param]) ? $style[$param] : '';
	
	return $current_value == $value;
}

function essb_button_require_fullwidth_correction($style = array()) {
	$is_active_fullwidth = false;
	$leading_width_mode_fullwidth = false;
	if ($style['button_width'] == 'full') {
		$is_active_fullwidth = true;
	
		$button_width_full_first = isset($style['button_width_full_first']) ? $style['button_width_full_first'] : '';
		$button_width_full_second = isset($style['button_width_full_second']) ? $style['button_width_full_second'] : '';
		if (!empty($button_width_full_first) || !empty($button_width_full_second)) {
			$leading_width_mode_fullwidth = true;
		}
	}
	
	return $is_active_fullwidth && $leading_width_mode_fullwidth;
}

/**
 * Generate message above/below share buttons
 * 
 * @param unknown $message
 * @param string $location
 * @param array $share
 * @return string
 */
function essb_format_message_buttons($message, $location = 'above', $share = array()) {
	
	$message = str_replace('%%title%%', $share['title'], $message);
	$message = str_replace('%%siteurl%%', get_site_url(), $message);
	$message = str_replace('%%permalink%%', $share['url'], $message);
	$message = str_replace('%%image%%', $share['image'], $message);
	$message = str_replace('%%shorturl%%', $share['short_url'], $message);
	
	$message = do_shortcode(stripslashes($message));
	
	if ($location == 'above') {
		return '<div class="essb_message_above_share">'.stripslashes($message).'</div>'; 
	}
	else {
		return 	'<li class="essb_message_before">'.stripslashes($message).'</li>';
	}
}

/**
 * Generate network name hidden class
 * 
 * @param unknown $style
 * @return string
 */
function essb_button_get_network_hidden_class($style) {
	$network_hidden_name_class = '';
	if ($style ['button_style'] == 'icon_hover') {
		$network_hidden_name_class = ' essb_hide_name';
	}
	if ($style ['button_style'] == 'icon') {
		$network_hidden_name_class = ' essb_force_hide_name essb_force_hide';
	}
	if ($style ['button_style'] == 'button_name') {
		$network_hidden_name_class = ' essb_hide_icon';
	}
	if ($style ['button_style'] == 'vertical') {
		$network_hidden_name_class = ' essb_vertical_name';
	}
	
	return $network_hidden_name_class;
}

/**
 * Generate fixed with styles
 * 
 * @param unknown $style
 * @return unknown
 */
function essb_button_generate_fixed_width($style) {
	
	$fixedwidth_key = $style['button_width_fixed_value'] . '_' . $style['button_width_fixed_align'];
	$css_class_width = ' essb_fixedwidth_'.$fixedwidth_key;
	
	if ($style['button_width_fixed_align'] == 'center') {
		$css_class_width .= ' essb_network_align_center';
	}
	if ($style['button_width_fixed_align'] == 'right') {
		$css_class_width .= ' essb_network_align_right';
		$style['button_align'] = 'left';
	}
	if ($style['button_width_fixed_align'] == 'left') {
		$style['button_align'] = 'left';
	}
	
	$style['class_width'] = $css_class_width;
	return $style;
}

/**
 * Generate full width styles
 * 
 * @param unknown $style
 * @return unknown
 */
function essb_button_generate_full_width($style) {
	$container_width = $style['button_width_full_container'];
	$single_button_width = intval($container_width) / intval($style['included_button_count']);
	$single_button_width = floor($single_button_width);
	
	$css_class_width = ' essb_fullwidth_'.$single_button_width.'_'.$style['button_width_full_button'].'_'.$style['button_width_full_container'];
	
	if ($style['fullwidth_align'] == 'center') {
		$css_class_width .= ' essb_network_align_center';
	}
	if ($style['fullwidth_align'] == 'right') {
		$css_class_width .= ' essb_network_align_right';
		$style['button_align'] = 'left';
	}
	if ($style['fullwidth_align'] == 'left') {
		$style['button_align'] = 'left';
	}
	
	$style['class_width'] = $css_class_width;
	return $style;
}

/**
 * Generate column width styles
 * 
 * @param unknown $style
 * @return unknown
 */
function essb_button_generate_column_width($style) {
	$css_class_width = ' essb_width_columns essb_width_columns_'.$style['button_width_columns'];
	
	if ($style['fullwidth_share_buttons_columns_align'] == 'center') {
		$css_class_width .= ' essb_network_align_center';
	}
	if ($style['fullwidth_share_buttons_columns_align'] == 'right') {
		$css_class_width .= ' essb_network_align_right';
	}
	
	$style['class_width'] = $css_class_width;
	return $style;
}

/**
 * Prepare flexible width styles
 * 
 * @param array $style
 * @return unknown
 */
function essb_button_generate_flex_width($style = array()) {
	$css_class_width = ' essb_width_flex';
	
	if ($style['flex_width_align'] == 'center') {
		$css_class_width .= ' essb_network_align_center';
	}
	if ($style['flex_width_align'] == 'right') {
		$css_class_width .= ' essb_network_align_right';
		$style['button_align'] = 'left';
	}
	if ($style['flex_width_align'] == 'left') {
		$style['button_align'] = 'left';
	}
	
	if ($style['flex_width_value'] != '' || $style['flex_button_value'] != '') {
		$css_class_width .= ' essb_flex_'.$style['flex_width_value'].'_'.$style['flex_button_value'];
	}
	
	$style['class_width'] = $css_class_width;
	return $style;
}