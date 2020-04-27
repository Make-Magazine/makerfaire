<?php
function essb_actions_get_share_counts() {
	$networks = isset($_REQUEST['nw']) ? $_REQUEST['nw'] : '';
	$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
	$instance = isset($_REQUEST['instance']) ? $_REQUEST['instance'] : '';
	$post = isset($_REQUEST['post']) ? $_REQUEST['post'] : '';

	$networks = sanitize_text_field($networks);

	header('content-type: application/json');

	// check if cache is present

	$is_active_cache =  essb_option_bool_value('admin_ajax_cache');
	$cache_ttl = intval(essb_option_value('admin_ajax_cache_time'));
	if ($cache_ttl == 0) {
		$cache_ttl = 600;
	}

	$list = explode(',', $networks);
	$output = array();
	$output['url'] = sanitize_text_field($url);
	$output['instance'] = sanitize_text_field($instance);
	$output['post'] = sanitize_text_field($post);
	$output['network'] = $networks;

	if (!class_exists('ESSBCachedCounters')) {
		define('ESSB3_CACHED_COUNTERS', true);
		include_once(ESSB3_PLUGIN_ROOT . 'lib/core/share-counters/essb-cached-counters.php');
	}

	$share_details = essb_get_post_share_details('');
	$share_details['url'] = $url;
	$share_details['full_url'] = $url;
	$networks = essb_option_value('networks');
	$result = ESSBCachedCounters::get_counters($post, $share_details, ESSBCachedCounters::all_socaial_networks());


	foreach ($list as $nw) {
		$transient_key = 'essb_'.$nw.'_'.$url;
		$exist_in_cache = false;
		if ($is_active_cache) {
			$cached_value = get_transient($transient_key);
			if ($cached_value) {
				$output[$nw] = $cached_value;
				$exist_in_cache = true;
			}
		}
			
		if (!$exist_in_cache) {
			$count = isset($result[$nw]) ? $result[$nw] : 0;
			$output[$nw] = $count;
			if ($is_active_cache) {
				delete_transient($transient_key);
				set_transient( $transient_key, $count, $cache_ttl );
			}
		}
	}
	echo str_replace('\\/','/',json_encode($output));

	die();
}