<?php
/**
 * AffiliateWP integration functions
 * 
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2016 AppsCreo
 * @since 4.2
 * @modified 6.2
 *
 */

if (! function_exists ( 'essb_generate_affiliatewp_referral_link' )) {
	function essb_generate_affiliatewp_referral_link($permalink) {
		global $essb_options;
		
		if (!function_exists('affwp_is_affiliate')) {
			return $permalink;
		}
		
		if (! (is_user_logged_in () && affwp_is_affiliate ())) {
			return $permalink;
		}
		
		$affwp_active_mode = essb_options_value ( 'affwp_active_mode' );
		$affwp_active_pretty = essb_options_bool_value ( 'affwp_active_pretty' );
		
		/**
		 * Doing and additional check to prevent caching of the URL with the affiliate
		 * when a deep memory cache plugin is used. If so happens we will just split and remove
		 * the affiliate ID. This happens just on pretty affilite ID generation. The regular
		 * affiliate ID added with query option is not cached by default
		 */
		if ($affwp_active_pretty) {
			$has_affiliate = strpos($permalink, '/' . affiliate_wp ()->tracking->get_referral_var () . '/');
			if ($has_affiliate !== false) {
				$permalink = substr($permalink, 0, $has_affiliate + 1);
			}
		}
		
		// append referral variable and affiliate ID to sharing links in ESSB
		if ($affwp_active_mode == 'name') {
			if ($affwp_active_pretty) {
				//$aff_append_data = affiliate_wp ()->tracking->get_referral_var () . '/' . affwp_get_affiliate_username ();
				$aff_append_data = '/' . affiliate_wp ()->tracking->get_referral_var () . '/';
				if (strpos($permalink, $aff_append_data) === false) {
					$permalink .= affiliate_wp ()->tracking->get_referral_var () . '/' . affwp_get_affiliate_username ();
				}
			} else {
				$permalink = add_query_arg ( affiliate_wp ()->tracking->get_referral_var (), affwp_get_affiliate_username (), $permalink );
			}
		} else {
			if ($affwp_active_pretty) {
				//$aff_append_data = affiliate_wp ()->tracking->get_referral_var () . '/' . affwp_get_affiliate_id ();
				$aff_append_data = '/' . affiliate_wp ()->tracking->get_referral_var () . '/';
				if (strpos($permalink, $aff_append_data) === false) {
					$permalink .= affiliate_wp ()->tracking->get_referral_var () . '/' . affwp_get_affiliate_id ();
				}
			} else {
				$permalink = add_query_arg ( affiliate_wp ()->tracking->get_referral_var (), affwp_get_affiliate_id (), $permalink );
			}
		}
		return $permalink;
	}
	
}