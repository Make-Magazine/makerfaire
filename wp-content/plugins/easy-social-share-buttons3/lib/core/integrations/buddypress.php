<?php
/**
 * iThemes integration functions
 * 
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2016 AppsCreo
 * @since 4.2
 *
 */

if (!function_exists('essb_buddypress_integration_group')) {
	function essb_buddypress_integration_group() {
		global $essb_options;
		
		essb_depend_load_function('essb_check_applicability_module', 'lib/core/extenders/essb-core-extender-check-applicability-module.php');
		
		if (essb_check_applicability_module('buddypress', $essb_options, essb_option_value('display_exclude_from'))) {
			$activity_link = bp_get_group_permalink();
			$activity_title =  bp_get_group_name();
			
			$affwp_active = essb_option_bool_value('affwp_active');
			if ($affwp_active) {
				essb_depend_load_function('essb_generate_affiliatewp_referral_link', 'lib/core/integrations/affiliatewp.php');
				$activity_link = essb_generate_affiliatewp_referral_link($activity_link);
			}
				
			$affs_active = essb_option_bool_value('affs_active');
			if ($affs_active) {
				$activity_link = do_shortcode('[affiliates_url]'.$activity_link.'[/affiliates_url]');
			}
			
			printf('%1$s<div class="essb_clear"></div>', essb_core()->generate_share_buttons('buddypress', 'share', array('only_share' => false, 'post_type' => 'buddypress', 'url' => $activity_link, 'title' => $activity_title)));
		}
	}	
}

if (!function_exists('essb_buddypress_integration_activity')) {
	function essb_buddypress_integration_activity() {
		global $essb_options;

		essb_depend_load_function('essb_check_applicability_module', 'lib/core/extenders/essb-core-extender-check-applicability-module.php');

		if (essb_check_applicability_module('buddypress', $essb_options, essb_option_value('display_exclude_from'))) {
			$activity_type = bp_get_activity_type();
			$activity_link = bp_get_activity_thread_permalink();
			$activity_title = bp_get_activity_feed_item_title();
			
			$affwp_active = essb_option_bool_value('affwp_active');
			if ($affwp_active) {
				essb_depend_load_function('essb_generate_affiliatewp_referral_link', 'lib/core/integrations/affiliatewp.php');
				$activity_link = essb_generate_affiliatewp_referral_link($activity_link);
			}
			
			$affs_active = essb_option_bool_value('affs_active');
			if ($affs_active) {
				$activity_link = do_shortcode('[affiliates_url]'.$activity_link.'[/affiliates_url]');
			}
			
			printf('%1$s<div class="essb_clear"></div>', essb_core()->generate_share_buttons('buddypress', 'share', array('only_share' => false, 'post_type' => 'buddypress', 'url' => $activity_link, 'title' => $activity_title)));
		}
	}
}


if (!function_exists('essb_buddypress_activate')) {
	function essb_buddypress_activate() {
		if (essb_option_bool_value('buddypress_group')) {
			add_action ( 'bp_before_group_home_content', 'essb_buddypress_integration_group' );
		}
		if (essb_option_bool_value('buddypress_activity')) {
			add_action ( 'bp_activity_entry_meta', 'essb_buddypress_integration_activity' );
		}
	}
}