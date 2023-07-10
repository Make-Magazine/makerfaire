<?php
/**
 * SliceWP integration functions
 * 
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2023 AppsCreo
 * @since 9.1
 */

if (! function_exists ( 'essb_generate_slicewp_referral_link' )) {
    function essb_generate_slicewp_referral_link($permalink) {
		global $essb_options;
		
		if (!function_exists('slicewp_is_user_affiliate')) {
			return $permalink;
		}
		
		if (! (is_user_logged_in () && slicewp_is_user_affiliate ())) {
			return $permalink;
		}
		
		$affiliate_id = slicewp_get_current_affiliate_id();
		
		$permalink = slicewp_get_affiliate_url($affiliate_id, $permalink);
		return $permalink;
	}	
}