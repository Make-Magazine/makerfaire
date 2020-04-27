<?php

function essb_cached_counters_options($code_options = array()) {
	if (essb_option_bool_value('cache_counter_facebook_async')) {
		$code_options['facebook_client'] = true;
	}
	
	if (essb_option_bool_value('cache_counter_pinterest_async')) {
		$code_options['pinterest_client'] = true;
	}
	
	if (essb_option_bool_value('deactivate_homepage') && (is_home() || is_front_page())) {
		$code_options['facebook_client'] = false;
		$code_options['pinterest_client'] = false;
	}
	
	$code_options['facebook_post_url'] = esc_url(get_permalink(get_the_ID()));
	
	// service change in version 5.6 to capture custom share URL inside options
	if (essb_option_bool_value('customshare')) {
		if (essb_option_value('customshare_url') != '') {
			$code_options['facebook_post_url'] = essb_sanitize_option_value('customshare_url');
		}
	}
	
	if (defined('ESSB3_SHARED_COUNTER_RECOVERY')) {
		$code_options['facebook_post_recovery_url'] = esc_url(essb_recovery_get_alt_permalink(get_permalink(get_the_ID()), get_the_ID()));
			
		if (essb_option_bool_value('customshare')) {
			if (essb_option_value('customshare_url') != '') {
				$code_options['facebook_post_recovery_url'] = esc_url(essb_recovery_get_alt_permalink(essb_option_value('customshare_url'), get_the_ID()));
			}
		}
	}
	
	return $code_options;
}