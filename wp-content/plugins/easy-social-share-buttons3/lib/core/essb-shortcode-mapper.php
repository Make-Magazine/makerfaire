<?php
/**
 * Shortcode Mapper
 * 
 * @author appsreo
 * @package EasySocialShareButtons
 * @since 4.0
 * 
 */

add_shortcode ('easy-profiles', 'essb_shortcode_profiles');
add_shortcode ('easy-social-like', 'essb_shortcode_native');
add_shortcode ('easy-subscribe',  'essb_shortcode_subscribe');
add_shortcode ('easy-popular-posts', 'essb_shortcode_popular_posts');

add_shortcode ( 'easy-social-share-popup', 'essb_shortcode_share_popup');
add_shortcode ( 'easy-social-share-flyin', 'essb_shortcode_share_flyin');
add_shortcode ( 'easy-total-shares', 'essb_shortcode_total_shares');
add_shortcode ( 'easy-social-share-cta', 'essb_shortcode_share_cta');
add_shortcode ( 'share-action-button', 'essb_shortcode_share_cta');

add_shortcode ( 'essb', 'essb_shortcode_share');
add_shortcode ( 'easy-share', 'essb_shortcode_share');
add_shortcode ( 'easy-social-share-buttons', 'essb_shortcode_share');
add_shortcode ( 'social-share', 'essb_shortcode_share_short');

add_shortcode ( 'easy-social-share', 'essb_shortcode_share_vk');

add_shortcode ( 'essb-click2chat' , 'essb_shortcode_click2chat' );
add_shortcode ( 'easy-click2chat' , 'essb_shortcode_click2chat' );

add_shortcode ( 'pinterest-image', 'essb_pinterest_image');
add_shortcode ( 'pinterest-gallery', 'essb_pinterest_gallery');

add_shortcode ( 'profile-bar', 'essb_inline_profile_bar');

function essb_inline_profile_bar() {
	// checking if the module is in use. Otherwise we need to load all that is required for work
	if (!defined('ESSB3_SOCIALPROFILES_ACTIVE')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles.php');
		include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles-helper.php');
		define('ESSB3_SOCIALPROFILES_ACTIVE', 'true');
		$template_url = ESSBSocialProfilesHelper::get_stylesheet_url();
		essb_resource_builder()->add_static_footer_css($template_url, 'essb-social-followers-counter');
	}
	
	return ESSBSocialProfiles::draw_social_profiles_bar();
}

function essb_pinterest_gallery($attrs = array()) {
	$defaults = array('columns' => '', 'images' => '', 'message' => '', 'classes' => '', 'spacing' => '', 'adjust' => '');
	$attrs = shortcode_atts( $defaults , $attrs );

	essb_depend_load_function('essb5_generate_pinterest_gallery', 'lib/modules/pinterest-pro/pinterest-pro-shortcodes.php');

	return essb5_generate_pinterest_gallery($attrs);
}


function essb_pinterest_image($attrs = array()) {
	$defaults = array('type' => '', 'image' => '', 'message' => '', 'custom_image' => '', 'align' => '');
	$attrs = shortcode_atts( $defaults , $attrs );
	
	essb_depend_load_function('essb5_generate_pinterest_image', 'lib/modules/pinterest-pro/pinterest-pro-shortcodes.php');
	
	return essb5_generate_pinterest_image($attrs);
}

function essb_shortcode_share_cta($attrs = array()) {
	$defaults = array('text' => '', 'icon' => '', 'style' => '', 'background' => '', 'color' => '', 'stretched' => '', 'shortcode' => 'true', 'total' => '');
	$attrs = shortcode_atts( $defaults , $attrs );
	
	$share_buttons = essb_core()->generate_share_buttons('sharebutton');
	
	essb_depend_load_function('essb5_generate_share_button', 'lib/core/display-methods/essb-display-method-button.php');
	
	$output = essb5_generate_share_button($share_buttons, $attrs);
	
	return $output;
}

function essb_shortcode_click2chat($attrs = array()) {
	$defaults = array('text' => '', 'background' => '', 'color' => '', 'icon' => '');

	$attrs = shortcode_atts( $defaults , $attrs );
	$attrs['shortcode'] = 'true';

	essb_depend_load_function('essb_click2chat_draw', 'lib/modules/social-chat/essb-click2chat.php');
	
	ob_start();
	essb_click2chat_draw($attrs);
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}

/*** shortcode functions ***/
function essb_shortcode_profiles($atts) {
	global $essb_options;
	essb_depend_load_class('ESSBCoreExtenderShortcodeProfiles', 'lib/core/extenders/essb-core-extender-shortcode-profiles.php');
	
	$exist_ukey = isset($atts['ukey']) ? $atts['ukey'] : '';
	if ($exist_ukey != '') {
		essb_depend_load_function('essb_shortcode_ukey_options', 'lib/core/extenders/essb-shortcode-ukey.php');
		$atts = essb_shortcode_ukey_options('easy-profiles', $exist_ukey);
	}
	
	return ESSBCoreExtenderShortcodeProfiles::parse_shortcode($atts, $essb_options);
}


function essb_shortcode_native($atts) {
	global $essb_options;
	essb_depend_load_class('ESSBCoreExtenderShortcodeNative', 'lib/core/extenders/essb-core-extender-shortcode-native.php');
	
	$exist_ukey = isset($atts['ukey']) ? $atts['ukey'] : '';
	if ($exist_ukey != '') {
		essb_depend_load_function('essb_shortcode_ukey_options', 'lib/core/extenders/essb-shortcode-ukey.php');
		$atts = essb_shortcode_ukey_options('easy-social-like', $exist_ukey);
	}
	
	return ESSBCoreExtenderShortcodeNative::parse_shortcode($atts, $essb_options);
}

function essb_shortcode_subscribe($atts, $content = '') {
	
	$exist_ukey = isset($atts['ukey']) ? $atts['ukey'] : '';
	if ($exist_ukey != '') {
		essb_depend_load_function('essb_shortcode_ukey_options', 'lib/core/extenders/essb-shortcode-ukey.php');
		$atts = essb_shortcode_ukey_options('easy-subscribe', $exist_ukey);
	}
	
	$mode = 'mailchimp';
	$design = '';
	$twostep = 'false';
	$twostep_inline = 'false';
	$conversion_key = 'shortcode';
	$hide_mobile = false;

	if (is_array($atts)) {
		$mode = essb_object_value($atts, 'mode');
		$design = essb_object_value($atts, 'design');
		$twostep = essb_object_value($atts, 'twostep');
		$twostep_inline = essb_object_value($atts, 'twostep_inline');
		$twostep_text = essb_object_value($atts, 'twostep_text');
		$conversion = essb_object_value($atts, 'conversion');
		
		$mobile_deactivate = essb_object_value($atts, 'mobile_deactivate');
		
		if ($conversion != '') {
			$conversion_key = $conversion;
		}
			
		if ($content == '' && $twostep_text != '') {
			$content = $twostep_text;
		}
		
		if ($mode == '') {
			$mode = 'mailchimp';
		}
		
		if ($mobile_deactivate == 'true') {
		    $hide_mobile = true;
		}
	}

	if (!class_exists('ESSBNetworks_Subscribe')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe.php');
	}
		
	if ($twostep == 'true') {
		return ESSBNetworks_Subscribe::draw_inline_subscribe_form_twostep($mode, $design, $content, $twostep_inline);
	}
	else {
	    return ESSBNetworks_Subscribe::draw_inline_subscribe_form($mode, $design, false, $conversion_key, $hide_mobile);
	}
}

function essb_shortcode_popular_posts($atts) {	
	essb_depend_load_function('essb_popular_posts', 'lib/core/widgets/essb-popular-posts-widget-shortcode.php');
	
	$exist_ukey = isset($atts['ukey']) ? $atts['ukey'] : '';
	if ($exist_ukey != '') {
		essb_depend_load_function('essb_shortcode_ukey_options', 'lib/core/extenders/essb-shortcode-ukey.php');
		$atts = essb_shortcode_ukey_options('easy-popular-posts', $exist_ukey);
	}
	
	return essb_popular_posts_code($atts, false);
}

function essb_shortcode_share_popup($atts) {
	essb_depend_load_function('essb_shortcode_share_popup_prepare', 'lib/core/extenders/essb-shortcode-share-popup.php');
	
	$exist_ukey = isset($atts['ukey']) ? $atts['ukey'] : '';
	if ($exist_ukey != '') {
		essb_depend_load_function('essb_shortcode_ukey_options', 'lib/core/extenders/essb-shortcode-ukey.php');
		$atts = essb_shortcode_ukey_options('easy-social-share-popup', $exist_ukey);
	}
	
	$mobile_app = essb_object_value($atts, 'mobile_app');
	if ($mobile_app == 'mobile') {
		$atts['only_mobile'] = 'yes';
		$atts['hide_mobile'] = '';
	}
	else if ($mobile_app == 'desktop') {
		$atts['hide_mobile'] = 'yes';
		$atts['only_mobile'] = '';
	}
	
	$shortcode_options = essb_shortcode_share_popup_prepare($atts);
	
	return essb_shortcode_share($shortcode_options);
}

function essb_shortcode_share_flyin($atts) {
	essb_depend_load_function('essb_shortcode_share_flyin_prepare', 'lib/core/extenders/essb-shortcode-share-flyin.php');

	$exist_ukey = isset($atts['ukey']) ? $atts['ukey'] : '';
	if ($exist_ukey != '') {
		essb_depend_load_function('essb_shortcode_ukey_options', 'lib/core/extenders/essb-shortcode-ukey.php');
		$atts = essb_shortcode_ukey_options('easy-social-share-flyin', $exist_ukey);
	}
	
	$mobile_app = essb_object_value($atts, 'mobile_app');
	if ($mobile_app == 'mobile') {
		$atts['only_mobile'] = 'yes';
		$atts['hide_mobile'] = '';
	}
	else if ($mobile_app == 'desktop') {
		$atts['hide_mobile'] = 'yes';
		$atts['only_mobile'] = '';
	}
	
	$shortcode_options = essb_shortcode_share_flyin_prepare($atts);

	return essb_shortcode_share($shortcode_options);
}

function essb_shortcode_total_shares($atts) {
	global $essb_options;
	
	$exist_ukey = isset($atts['ukey']) ? $atts['ukey'] : '';
	if ($exist_ukey != '') {
		essb_depend_load_function('essb_shortcode_ukey_options', 'lib/core/extenders/essb-shortcode-ukey.php');
		$atts = essb_shortcode_ukey_options('easy-total-shares', $exist_ukey);
	}
	
	
	$network_list = essb_option_value('networks');
	if (!is_array($network_list)) { $network_list = array(); }
	essb_depend_load_class('ESSBCoreExtenderShortcodeTotalShares', 'lib/core/extenders/essb-core-extender-shortcode-totalshares.php');

	return ESSBCoreExtenderShortcodeTotalShares::parse_shortcode($atts, $essb_options, $network_list);
}

function essb_shortcode_share_vk($atts) {
	$exist_ukey = isset($atts['ukey']) ? $atts['ukey'] : '';
	if ($exist_ukey != '') {
		essb_depend_load_function('essb_shortcode_ukey_options', 'lib/core/extenders/essb-shortcode-ukey.php');
		$atts = essb_shortcode_ukey_options('easy-social-share', $exist_ukey);
	}
	
	$total_counter_pos = essb_object_value($atts, 'total_counter_pos');
	if ($total_counter_pos == "none") {
		$atts['hide_total'] = "yes";
	}

	$counter_pos = essb_object_value($atts, 'counter_pos');
	if ($counter_pos == "none") {
		$atts['counter_pos'] = "hidden";
	}

	return essb_shortcode_share($atts);
}

function essb_shortcode_share_short($atts) {
	$exist_ukey = isset($atts['ukey']) ? $atts['ukey'] : '';
	if ($exist_ukey != '') {
		essb_depend_load_function('essb_shortcode_ukey_options', 'lib/core/extenders/essb-shortcode-ukey.php');
		$atts = essb_shortcode_ukey_options('social-share', $exist_ukey);
	}

	$total_counter_pos = essb_object_value($atts, 'total_counter_pos');
	if ($total_counter_pos == "none") {
		$atts['hide_total'] = "yes";
	}

	$counter_pos = essb_object_value($atts, 'counter_pos');
	if ($counter_pos == "none") {
		$atts['counter_pos'] = "hidden";
	}
	
	$counters = essb_object_value($atts, 'counters');
	if ($counters == 'no') {
		$atts['counters'] = '0';
	}
	else if ($counters == 'yes') {
		$atts['counters'] = '1';
	}
	
	$mobile_app = essb_object_value($atts, 'mobile_app');
	if ($mobile_app == 'mobile') {
		$atts['only_mobile'] = 'yes';
		$atts['hide_mobile'] = '';
	}
	else if ($mobile_app == 'desktop') {
		$atts['hide_mobile'] = 'yes';
		$atts['only_mobile'] = '';
	}

	return essb_shortcode_share($atts);
}

function essb_shortcode_share($atts) {
	
	$exist_ukey = isset($atts['ukey']) ? $atts['ukey'] : '';
	if ($exist_ukey != '') {
		essb_depend_load_function('essb_shortcode_ukey_options', 'lib/core/extenders/essb-shortcode-ukey.php');
		$atts = essb_shortcode_ukey_options('easy-social-share', $exist_ukey);
	}
	
	$mobile_app = essb_object_value($atts, 'mobile_app');
	if ($mobile_app == 'mobile') {
		$atts['only_mobile'] = 'yes';
		$atts['hide_mobile'] = '';
	}
	else if ($mobile_app == 'desktop') {
		$atts['hide_mobile'] = 'yes';
		$atts['only_mobile'] = '';
	}
	
	essb_depend_load_function('essb_shortcode_share_prepare', 'lib/core/extenders/essb-shortcode-share-code.php');
	return essb_shortcode_share_prepare($atts);
}