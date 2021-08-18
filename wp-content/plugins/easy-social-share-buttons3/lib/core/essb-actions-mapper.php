<?php
/**
 * Map extra ajax plugin actions for usage
 * 
 * @author appsreo
 * @package EasySocialShareButtons
 * @since 4.0
 * 
 */

if (!essb_option_bool_value('deactivate_postcount')) {
	add_action ( 'wp_ajax_nopriv_essb_self_postcount', 'essb_actions_update_post_count' );
	add_action ( 'wp_ajax_essb_self_postcount', 'essb_actions_update_post_count' );
}

/**
 * Additional ajax update requests that can run over the site
 */
add_action ( 'template_redirect', 'essb_process_additional_ajax_requests', 1 );

/**
 * Love this button code (only when the button is active on site)
 */
if (essb_is_active_social_network('love')) {
	add_action ( 'wp_ajax_nopriv_essb_love_action', 'essb_love_logclick');
	add_action ( 'wp_ajax_essb_love_action', 'essb_love_logclick');
}

/**
 * Adding mail sending action only when the form is configured to work
 */
if (essb_option_value('mail_function') == 'form' && essb_is_active_social_network('mail')) {
	essb_depend_load_function('essb_actions_sendmail', 'lib/core/extenders/essb-actions-sendmail.php');	
	add_action ( 'wp_ajax_nopriv_essb_mail_action', 'essb_actions_sendmail' );
	add_action ( 'wp_ajax_essb_mail_action', 'essb_actions_sendmail' );
}


// --------------------------------------------------
// Actions
// --------------------------------------------------

if (!function_exists('essb_love_logclick')) {
	function essb_love_logclick() {
		essb_depend_load_function('essb_actions_extender_lovethis', 'lib/core/extenders/essb-actions-lovethis.php');
		essb_actions_extender_lovethis();
	}
}

function essb_process_additional_ajax_requests() {

	$subscribe_action = isset($_REQUEST['essb-malchimp-signup']) ? $_REQUEST['essb-malchimp-signup']: '';

	if ($subscribe_action == '1') {
		if (!class_exists('ESSBNetworks_SubscribeActions')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-actions.php');
		}
			
		ESSBNetworks_SubscribeActions::process_subscribe();
			
		die();
	}

	if (defined('ESSB3_CACHED_COUNTERS')) {
		if (ESSBGlobalSettings::$cached_counters_cache_mode) {
			if (isset($_REQUEST['essb_counter_cache']) && $_REQUEST['essb_counter_cache'] == 'rebuild') {
				$share_details = essb_get_post_share_details('');
				$share_details['full_url'] = $share_details['url'];
				$networks = essb_option_value('networks');
				$result = ESSBCachedCounters::get_counters(get_the_ID(), $share_details, $networks);
				echo json_encode($result);
				die();
			}
		}
		
		$full_counter_update = isset($_REQUEST['essb_clear_cached_counters']) ? $_REQUEST['essb_clear_cached_counters'] : '';
		if ($full_counter_update == 'true') {
			delete_post_meta_by_key('essb_cache_expire');
		}
		
		$full_history_clear = isset($_REQUEST['essb_clear_counters_history']) ? $_REQUEST['essb_clear_counters_history'] : '';
		if ($full_history_clear == 'true') {
			delete_post_meta(get_the_ID(), 'essb_cache_expire');
			$networks = essb_available_social_networks();
			
			foreach ($networks as $key => $data) {
				delete_post_meta(get_the_ID(), 'essb_c_'.$key);
			} 
			
			delete_post_meta(get_the_ID(), 'essb_c_total');
		}
	}

	$current_action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

	if ($current_action == "essb_counts") {
		define('DOING_AJAX', true);

		send_nosniff_header();
		header('content-type: application/json');
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');

		if(is_user_logged_in())
			do_action('wp_ajax_essb_counts');
		else
			do_action('wp_ajax_nopriv_essb_counts');

		exit;
	}
	
	
	$design_preview = isset($_REQUEST['subscribe-preview']) ? $_REQUEST['subscribe-preview'] : '';
	if ($design_preview == 'true') {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-preview.php');
		die();
	}
}

function essb_actions_update_post_count() {
	essb_depend_load_function('essb_actions_extender_postcount', 'lib/core/extenders/essb-actions-postcount.php');
	$r = essb_actions_extender_postcount();

	die(json_encode($r));
}


/**
 * Control the networks with internal share counters
 * 
 * @param unknown $network
 * @return boolean
 */
function essb_is_internal_counted($network) {
	$api_counters = array();
	$api_counters[] = 'facebook';
	
	if (essb_option_value('twitter_counters') != 'self') {
		$api_counters[] = 'twitter';
	}
	
	$api_counters[] = 'pinterest';
	$api_counters[] = 'vk';
	$api_counters[] = 'reddit';
	$api_counters[] = 'buffer';
	$api_counters[] = 'ok';	
	$api_counters[] = 'xing';
	$api_counters[] = 'yummly';
	$api_counters[] = 'comments'; // added 7.0.4
	
	if (in_array($network, $api_counters)) {
		return false;
	}
	else {
		return true;
	}
}