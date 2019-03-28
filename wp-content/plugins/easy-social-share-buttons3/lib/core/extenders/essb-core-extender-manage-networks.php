<?php

/**
 * Manage installed social networks inside plugin. This makes easy to customize the styles and networks use
 *
 * @since 5.8
 * @author appscreo
 * @package EasySocialShareButtons
 *
 */


add_filter('essb_manage_networks', 'essb_manage_active_social_networks');

function essb_manage_active_social_networks($networks) {
	
	$user_selection = essb_option_value('functions_networks');
	if (!is_array($user_selection)) {
		$user_selection = array();
	}
	
	$my_networks = array();
	
	if (count($user_selection) > 0) {
		$my_networks = array();
		
		foreach ($networks as $key => $data) {
			if (in_array($key, $user_selection)) {
				$my_networks[$key] = $data;
			}
		}
	}
	else {
		$my_networks = $networks;
	}
	
	return $my_networks;
}