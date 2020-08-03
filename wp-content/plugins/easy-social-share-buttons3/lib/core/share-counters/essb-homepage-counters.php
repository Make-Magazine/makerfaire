<?php
/**
 * Functions related to generation of share counters for entire site 
 * when the option to show a global counter is set.
 * 
 * @since 7.0
 * @author appscreo
 * @package EasySocialShareButtons
 */

if (!function_exists('essb_get_counter_meta_values')) {
	/**
	 * Read all plugin metavalues for the share counters
	 * 
	 * @return unknown
	 */
	function essb_get_counter_meta_values() {
		global $wpdb;
		
		$r = $wpdb->get_results( "
				SELECT pm.meta_value, pm.meta_key FROM {$wpdb->postmeta} pm
				LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key LIKE 'essb_c_%'
				AND p.post_status = 'publish'" );
		
		return $r;
	}
	
	function essb_get_overall_sharecounter($counters = array()) {	
		if (essb_option_bool_value('site_total_allposts')) {
			if (false == ($home_counters = get_transient('essb-homepage-counters'))) {
				$home_counters = essb_update_homepage_sharecounter();
			
				$cache_expiration = essb_sanitize_option_value('homepage_total_cache');
				if (intval($cache_expiration) == 0) {
					$cache_expiration = 30;
				}
			
				set_transient('essb-homepage-counters', $home_counters, MINUTE_IN_SECONDS * $cache_expiration);
			}
				
			$counters = $home_counters;
		}
		
		return $counters;
	}
	
	function essb_get_homepage_sharecounter($counters = array()) {
		if (is_home() || is_front_page()) {
			if (false == ($home_counters = get_transient('essb-homepage-counters'))) {
				$home_counters = essb_update_homepage_sharecounter();
				
				$cache_expiration = essb_sanitize_option_value('homepage_total_cache');
				if (intval($cache_expiration) == 0) {
					$cache_expiration = 30;
				}
				
				set_transient('essb-homepage-counters', $home_counters, MINUTE_IN_SECONDS * $cache_expiration);
			}
			
			$counters = $home_counters;
		}
		
		return $counters;
	}
	
	function essb_clear_homepage_sharecounter() {
		delete_transient('essb-homepage-counters');
	}
	
	function essb_update_homepage_sharecounter() {
		$metavalues = essb_get_counter_meta_values();
		$cached = array();
		$total = 0;
		
		foreach ($metavalues as $row) {
			$key = $row->meta_key;
			$value = $row->meta_value;
			
			if ($key == 'essb_cache_expire' || $key == 'essb_cache_updated' || $key == 'essb_cached_image') {
			    continue;
			}
			
			$key = str_replace('essb_c_', '', $key);
			
			if (!isset($cached[$key])) {
				$cached[$key] = 0;
			}			
			
			$cached[$key] += intval($value);
			$total += intval($value);
		}
		
		$cached['total'] = $total;
		 
		return $cached;
	}
	
	add_filter('essb_homepage_get_cached_counters', 'essb_get_homepage_sharecounter');
	add_filter('essb4_get_cached_counters', 'essb_get_overall_sharecounter');
}