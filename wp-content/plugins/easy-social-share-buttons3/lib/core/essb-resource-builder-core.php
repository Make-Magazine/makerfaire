<?php

// building custom filters for handling everytime loaded resources;
define('ESSB_RESOURCE_BUILDER_FOLDER', ESSB3_PLUGIN_ROOT . 'lib/core/resource-snippets/');

add_filter('essb_js_buffer_head', 'essb_js_build_admin_ajax_access_code');


/**
 * Add customized generated styles to the output, based on the user changes inside settings
 */
add_filter('essb_css_buffer_footer', 'essb_css_build_footer_css');
add_filter('essb_css_buffer_head', 'essb_css_build_customizer');
add_filter('essb_css_buffer_head', 'essb_css_build_compile_display_locations_code');

/**
 * Generate the customized CSS code for various locations, based on user input
 * @param unknown_type $buffer
 */
function essb_css_build_compile_display_locations_code($buffer) {
	$snippet = '';
	/**
	 * Sidebar
	 */
	if (essb_should_add_position_styles('sidebar')) {
		essb_depend_load_function('essb_rs_css_sidebar', 'lib/core/resource-snippets/essb_rs_css_sidebar.php');
		$snippet .= essb_rs_css_sidebar();
	}
	
	/**
	 * Top Bar
	 */
	if (essb_should_add_position_styles('topbar')) {
		essb_depend_load_function('essb_rs_css_topbar', 'lib/core/resource-snippets/essb_rs_css_topbar.php');
		$snippet .= essb_rs_css_topbar();
	}
	
	/**
	 * Bottom Bar
	 */
	if (essb_should_add_position_styles('bottombar')) {
		essb_depend_load_function('essb_rs_css_bottombar', 'lib/core/resource-snippets/essb_rs_css_bottombar.php');
		$snippet .= essb_rs_css_bottombar();
	}
	
	/**
	 * Float From Top
	 */	
	if (essb_should_add_position_styles('content_float')) {
		essb_depend_load_function('essb_rs_css_float', 'lib/core/resource-snippets/essb_rs_css_float.php');
		$snippet .= essb_rs_css_float();
	}
		
	/**
	 * Post Vertical Float
	 */
	if (essb_should_add_position_styles('postfloat')) {
		essb_depend_load_function('essb_rs_css_postfloat', 'lib/core/resource-snippets/essb_rs_css_postfloat.php');
		$snippet .= essb_rs_css_postfloat();
	}
	
	return $buffer.$snippet;
}

/**
 * Generate the CSS code for the customizer menu if active
 * @param unknown_type $buffer
 */
function essb_css_build_customizer($buffer) {
		
	$is_active = essb_option_bool_value('customizer_is_active');
	
	$snippet = '';
	if ($is_active) {
		if (!function_exists('essb_rs_css_build_customizer')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_css_build_customizer.php');
		}
			
		$snippet .= essb_rs_css_build_customizer();
	}
	
	$is_active_subscribe = essb_option_bool_value('activate_mailchimp_customizer');
	$is_active_subscribe2 = essb_option_bool_value( 'activate_mailchimp_customizer2');
	$is_active_subscribe3 = essb_option_bool_value('activate_mailchimp_customizer3');
	$is_active_subscribe4 = essb_option_bool_value('activate_mailchimp_customizer4');
	$is_active_subscribe5 = essb_option_bool_value('activate_mailchimp_customizer5');
	$is_active_subscribe6 = essb_option_bool_value('activate_mailchimp_customizer6');
	$is_active_subscribe7 = essb_option_bool_value('activate_mailchimp_customizer7');
	$is_active_subscribe8 = essb_option_bool_value('activate_mailchimp_customizer8');
	$is_active_subscribe9 = essb_option_bool_value('activate_mailchimp_customizer9');
	
	if ($is_active_subscribe || $is_active_subscribe2 || $is_active_subscribe3 || $is_active_subscribe4 ||
			$is_active_subscribe5 || $is_active_subscribe6 || $is_active_subscribe7 || $is_active_subscribe8 || 
			$is_active_subscribe9) {
		
		if (!function_exists('essb_rs_css_build_customizer_mailchimp')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_css_build_customizer_mailchimp.php');
		}
	
		
		$snippet .= (essb_rs_css_build_customizer_mailchimp(array('design1' => $is_active_subscribe,
				'design2' => $is_active_subscribe2,
				'design3' => $is_active_subscribe3,
				'design4' => $is_active_subscribe4,
				'design5' => $is_active_subscribe5,
				'design6' => $is_active_subscribe6,
				'design7' => $is_active_subscribe7,
				'design8' => $is_active_subscribe8,
				'design9' => $is_active_subscribe9)));
	}
	
	
	$global_user_defined_css = essb_option_value('customizer_css');
	
	
	if ($global_user_defined_css != '') {
		$global_user_defined_css = stripslashes ( $global_user_defined_css );
		$snippet .= $global_user_defined_css;
	}
	
	if (essb_option_bool_value('activate_cct_customizer')) {
		if (!function_exists('essb_rs_css_build_cct_customizer')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_css_build_cct_customizer.php');
		}
		
		$snippet .= essb_rs_css_build_cct_customizer();
	}
	
	
	return $buffer.$snippet;
}

/**
 * Include additional custom CSS footer below the fold
 * @param unknown_type $buffer
 * @return string
 */
function essb_css_build_footer_css($buffer) {

	$global_user_defined_css = essb_option_value('customizer_css_footer');
	if ($global_user_defined_css != '') {
		$global_user_defined_css = stripslashes ( $global_user_defined_css );
	}
	return $buffer.$global_user_defined_css;
}

/**
 * Core plugin settings for running on site. The filter will load them according to the loaded core
 * script to prevent errors.
 * 
 * @param unknown_type $buffer
 * @return string
 */
function essb_js_build_admin_ajax_access_code($buffer) {
	$code_options = array();
	$code_options['ajax_url'] = esc_url(admin_url ('admin-ajax.php'));
	$code_options['essb3_nonce'] = wp_create_nonce('essb3_ajax_nonce');
	$code_options['essb3_plugin_url'] = esc_url(ESSB3_PLUGIN_URL);
	
	/**
	 * Real-time related share counters only
	 */
	if (essb_option_value('counter_mode') == '') {
		$code_options['essb3_facebook_total'] = essb_option_bool_value('facebooktotal');
		$code_options['essb3_admin_ajax'] = essb_option_bool_value('force_counters_admin');
		$code_options['essb3_internal_counter'] = essb_option_bool_value('active_internal_counters');
		$code_options['essb3_counter_button_min'] = intval(essb_option_value('button_counter_hidden_till'));
		$code_options['essb3_counter_total_min'] = intval(essb_option_value('total_counter_hidden_till'));
		$code_options['essb3_no_counter_mailprint'] = essb_option_bool_value('deactive_internal_counters_mail');
		$code_options['essb3_single_ajax'] = essb_option_bool_value('force_counters_admin_single');
		$code_options['ajax_type'] = essb_sanitize_option_value('force_counters_admin_type');
		$code_options['twitter_counter'] = essb_sanitize_option_value('twitter_counters');
		$code_options['google_counter'] = essb_sanitize_option_value('google_counter_type');
	}
	
	$code_options['essb3_stats'] = essb_option_bool_value('stats_active');
	$code_options['essb3_ga'] = essb_option_bool_value('activate_ga_tracking');	
	/**
	 * Google Analytics Mode
	 */
	if (essb_option_bool_value('activate_ga_tracking')) {
		$code_options['essb3_ga_mode'] = essb_sanitize_option_value('ga_tracking_mode');
	}
	$code_options['blog_url'] = esc_url(get_site_url().'/');
	$code_options['post_id'] = esc_attr(get_the_ID());

	if (essb_option_bool_value('activate_ga_layers')) {
		$code_options['essb3_ga'] = true;
		$code_options['essb3_ga_mode'] = 'layers';
	}
	
	if (essb_option_bool_value('deactivate_postcount')) {
		$code_options['stop_postcount'] = true;
	}	
	
	/**
	 * Dedicated after share networks selection
	 */
	if (essb_option_bool_value('afterclose_active') && !essb_option_bool_value('deactivate_module_aftershare')) {
		$aftershare_networks = essb_option_value('aftershare_networks');
		if (is_array($aftershare_networks)) {
			$code_options['aftershare_networks'] = implode (',', $aftershare_networks);
		}
	}
	
	$subscribe_terms_error = essb_sanitize_option_value('subscribe_terms_error');
	if ($subscribe_terms_error != '') {
		$code_options['subscribe_terms_error'] = stripslashes($subscribe_terms_error);
	}
	
	// since 5.6 - require name field
	if (essb_option_bool_value('subscribe_require_name')) {
		$code_options['subscribe_validate_name'] = true;
		if (essb_option_value('subscribe_require_name_error') != '') {
			$code_options['subscribe_validate_name_error'] = essb_sanitize_option_value('subscribe_require_name_error');
		}
	}
	
	// since 5.2 - client side counter update
	if (essb_option_bool_value('cache_counter_facebook_async') || essb_option_bool_value('cache_counter_pinterest_async')) {
		essb_depend_load_function('essb_cached_counters_options', 'lib/core/share-counters/essb-cached-counters-front.php');
		$code_options = essb_cached_counters_options($code_options);
	}
	
	if (essb_option_bool_value('pinterest_force_description') || essb_option_bool_value('pinterest_description')) {
		$code_options['pin_description'] = esc_attr(get_post_meta( get_the_ID(), 'essb_post_pin_desc', true));
		if (empty($code_options['pin_description'])) {
			$current_share_details = essb_get_post_share_details('');
			$code_options['pin_description'] = esc_attr($current_share_details['title_plain']);
		}
		$code_options['force_pin_description'] = true;
	}
	
	/**
	 * Force the custom image appear on the site images
	 */
	if (essb_option_bool_value('pinterest_set_datamedia')) {
		$post_custom_image = get_post_meta(get_the_ID(), 'essb_post_pin_image', true);
		if ($post_custom_image != '') {
			$code_options['pin_force_image'] = esc_url($post_custom_image);
			$code_options['pin_force_active'] = true;
		}
	}
	
	/**
	 * Foce the data-pin-id option
	 */
	if (essb_option_bool_value('pinterest_images') || essb_option_bool_value('pinterest_sniff_disable')) {
		$post_custom_pinid = get_post_meta(get_the_ID(), 'essb_post_pin_id', true);
		if ($post_custom_pinid != '' && essb_option_bool_value('pinterest_set_pinid_all')) {
			$code_options['pin_pinid'] = esc_attr($post_custom_pinid);
			$code_options['pin_pinid_active'] = true;
		}
	}
	
	if (essb_option_bool_value('pinterest_force_responsive')) {
		$code_options['force_pin_thumbs'] = true;
	}
	
	/**
	 * Copy Link Button Translation
	 */
	if (essb_option_value('translate_copy_message1') != '') {
	    $code_options['translate_copy_message1'] = essb_option_value('translate_copy_message1');
	}
	if (essb_option_value('translate_copy_message2') != '') {
	    $code_options['translate_copy_message2'] = essb_option_value('translate_copy_message2');
	}
	if (essb_option_value('translate_copy_message3') != '') {
	    $code_options['translate_copy_message3'] = essb_option_value('translate_copy_message3');
	}
	
	if (has_filter('essb_extend_ajax_options')) {
		$code_options = apply_filters('essb_extend_ajax_options', $code_options);
	}
	
	$output = 'var essb_settings = '.json_encode($code_options).';';

	if (defined('ESSB3_CACHED_COUNTERS')) {
		if (ESSBGlobalSettings::$cached_counters_cache_mode) {
			$update_url = essb_get_current_page_url();
			
			if (defined('ESSB_FORCE_SSL')) {
				$update_url = str_replace('http://', 'https://', $update_url);
			}
			else {
				// second level of protection against non https connection calls when http is detected instead of https
				$current_page_url = get_permalink();
				if (strpos($current_page_url, 'https://') !== false && strpos($update_url, 'https://') === false) {
					$update_url = str_replace('http://', 'https://', $update_url);
				}
			}
			
			$output .= 'var essb_buttons_exist = !!document.getElementsByClassName("essb_links"); if(essb_buttons_exist == true) { document.addEventListener("DOMContentLoaded", function(event) { var ESSB_CACHE_URL = "'.esc_url($update_url).'"; if(ESSB_CACHE_URL.indexOf("?") > -1) { ESSB_CACHE_URL += "&essb_counter_cache=rebuild"; } else { ESSB_CACHE_URL += "?essb_counter_cache=rebuild"; }; var xhr = new XMLHttpRequest(); xhr.open("GET",ESSB_CACHE_URL,true); xhr.send(); });}';
		}
	}
	
	if (essb_option_bool_value('pinterest_images') && !essb_is_module_deactivated_on('pinpro')) {
		$pin_options = array();
		$pin_options['template'] = essb_template_folder(essb_option_value('pinterest_template'));
		$pin_options['button_style'] = essb_sanitize_option_value('pinterest_button_style');
		$pin_options['button_size'] = essb_sanitize_option_value('pinterest_button_size');
		$pin_options['animation'] = essb_sanitize_option_value('pinterest_css_animations');
		$pin_options['text'] = essb_sanitize_option_value('pinterest_text');
		$pin_options['min_width'] = essb_sanitize_option_value('pinterest_minwidth');
		$pin_options['min_height'] = essb_sanitize_option_value('pinterest_minheight');
		$pin_options['min_width_mobile'] = essb_sanitize_option_value('pinterest_minwidth_mobile');
		$pin_options['min_height_mobile'] = essb_sanitize_option_value('pinterest_minheight_mobile');
		$pin_options['nolinks'] = essb_option_bool_value('pinterest_nolinks');
		$pin_options['lazyload'] = essb_option_bool_value('pinterest_lazyload');
		$pin_options['active'] = true;
		$pin_options['position'] = essb_sanitize_option_value('pinterest_position');
		$pin_options['mobile_position'] = essb_sanitize_option_value('pinterest_mobile_position');
		$pin_options['hideon'] = essb_sanitize_option_value('pinterest_hideon');
		$pin_options['visibility'] = essb_sanitize_option_value('pinterest_visibility');
		$pin_options['reposition'] = essb_option_bool_value('pinterest_reposition');
		$pin_options['selector'] = essb_sanitize_option_value('pinterest_selector');
		
		if (essb_option_bool_value('pinterest_alwayscustom') && essb_option_bool_value('pinterest_images')) {
			$pin_options['custompin'] = esc_attr(get_post_meta( get_the_ID(), 'essb_post_pin_desc', true));
			$pin_options['force_custompin'] = true;
		}
		
		$output .= 'var essbPinImages = '.json_encode($pin_options).';';
	}

	return $buffer.$output;
}

function essb_should_add_position_styles($position = '') {
	$r = true;
	
	if (essb_option_bool_value('load_css_active')) {
		$r = essb_is_position_active($position);
	}
	
	return $r;
}

function essb_hex2rgba($color, $opacity = false) {

	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if(empty($color))
		return $default;

	//Sanitize $color if "#" is provided
	if ($color[0] == '#' ) {
		$color = substr( $color, 1 );
	}

	//Check if color has 6 or 3 characters and get values
	if (strlen($color) == 6) {
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( strlen( $color ) == 3 ) {
		$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	} else {
		return $default;
	}

	//Convert hexadec to rgb
	$rgb =  array_map('hexdec', $hex);

	//Check if opacity is set(rgba or rgb)
	if($opacity){
		if(abs($opacity) > 1)
			$opacity = 1.0;
		$output = 'rgba('.implode(',',$rgb).','.$opacity.')';
	} else {
		$output = 'rgb('.implode(',',$rgb).')';
	}

	//Return rgb(a) color string
	return $output;
}