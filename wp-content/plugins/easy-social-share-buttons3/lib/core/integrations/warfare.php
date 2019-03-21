<?php

if (!function_exists('essb_sw_custom_data')) {
	function essb_sw_custom_data() {
		
		global $post;
		
		$result = array('og_title' => '', 'og_description' => '', 'og_image' => '', 'custom_tweet' => '', 'pin_image' => '' );
		
		if (isset($post)) {
			$swp_custom_tweet = get_post_meta($post->ID, 'swp_custom_tweet', true);
			if ($swp_custom_tweet != '') {
				$result['custom_tweet'] = $swp_custom_tweet;
			}
			
			$swp_og_description = get_post_meta($post->ID, 'swp_og_description', true);
			if ($swp_og_description != '') {
				$result['og_description'] = $swp_og_description;
			}
					
			$swp_og_image = get_post_meta($post->ID, 'swp_og_image', true);
			if ($swp_og_image != '') {
				$result['og_image'] = wp_get_attachment_url($swp_og_image);
			}
			
			$swp_og_title = get_post_meta($post->ID, 'swp_og_title', true);
			if ($swp_og_title != '') {
				$result['og_title'] = $swp_og_title;
			}
			
			$swp_pinterest_image = get_post_meta($post->ID, 'swp_pinterest_image', true);
			if ($swp_pinterest_image != '') {
				$result['pin_image'] = wp_get_attachment_url($swp_pinterest_image);	
			}
		
		}
		
		return $result;
	}
	
	function essb_sw_counters_parse($cached_counters = array()) {
		global $post;
	
		if (!isset($post)) return $cached_counters;
	
		$post_id = $post->ID;
	
		$cumulative_total = 0;
		foreach ($cached_counters as $network => $shares) {
				
			if ($network == 'total') continue;
			
			$sw_shares = get_post_meta($post_id, '_'.$network.'_shares', true);				
			$shares = get_post_meta($post_id, 'essb_c_'.$network, true);
				
			if (intval($sw_shares) > intval($shares)) {
				$shares = $sw_shares;
				$cached_counters[$network] = $shares;
			}
			$cumulative_total += intval($shares);
		}
	
		$cached_counters['total'] = $cumulative_total;
	
		return $cached_counters;
	}
	
	add_filter('essb4_get_cached_counters', 'essb_sw_counters_parse');
	
	add_shortcode('click_to_tweet', 'essb_sw_click_to_tweet');
	
	function essb_sw_click_to_tweet($atts) {
		if (isset($atts['quote']) && !isset($atts['tweet'])) {
			$atts['tweet'] = $atts['quote'];
		}
		
		$atts['user'] = get_post_meta(get_the_ID(), 'essb_post_twitter_username', true);
		$atts['hashtags'] = get_post_meta(get_the_ID(), 'essb_post_twitter_hashtags', true);
		
		if (function_exists('essb_ctt_shortcode')) {
			return essb_ctt_shortcode($atts);
		}
		else {
			return '';
		}
	}
}