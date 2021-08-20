<?php

/**
 * Advanced and adaptive fake share counter parser. The option makes possible to switch the 
 * share value to internal and multiply the existing shares. But it will work as adaptive and show @author Velimir
 * real progress (without spotting a difference with the real values).
 *
 * @since 6.3
 * @package EasySocialShareButtons
 * @author appscreo
 *
 */

if (!function_exists('essb_apply_fake_sharecounter_value')) {
	
	/**
	 * Set fake share counter value based on the settings
	 * 
	 * @param unknown_type $cached_counters
	 * @return unknown
	 */
	function essb_apply_fake_sharecounter_value($cached_counters = array()) {
		global $post;
		
		if (!isset($post)) {
			return $cached_counters;
		}
		
		$fake_counter_correction = essb_sanitize_option_value('fake_counter_correction');
		$activate_fake_counters_internal = essb_option_bool_value('activate_fake_counters_internal');
		
		$post_id = $post->ID;
		$cumulative_total = 0;
		foreach ($cached_counters as $network => $shares) {
				
			if ($network == 'total') {
				continue;
			}
				
			if ($activate_fake_counters_internal) {
				$minimal_fake_shares = get_post_meta($post_id, 'essb_pc_'.$network, true);
				
				if (intval($shares) < intval($minimal_fake_shares)) {
					$shares = $minimal_fake_shares;
				}
			}
			
			/**
			 * @since 7.7 Allow usage of non-integer values in the fake counter correction
			 */
			if (floatval($fake_counter_correction) != 0) {
			    $shares = intval($shares) * floatval($fake_counter_correction);
			    $shares = intval($shares); // round values
			}
			
			$cached_counters[$network] = $shares;
			$cumulative_total += intval($shares);
		}
		
		$cached_counters['total'] = $cumulative_total;
		
		
		return $cached_counters;
	}
	
	add_filter('essb4_get_cached_counters', 'essb_apply_fake_sharecounter_value');
}