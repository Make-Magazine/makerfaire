<?php

function mpfy_mm_map_limit($limit) {
	return -1;
}
add_filter('mpfy_map_limit', 'mpfy_mm_map_limit');

// Remove default menu items & behavior
remove_action('admin_menu', 'mpfy_amg_map_settings_menu', 9);
remove_action('admin_enqueue_scripts', 'mpfy_ms_admin_behaviors');
remove_action('admin_menu', 'mpfy_ms_map_settings_limit', 9);
remove_action('init', 'mpfy_ms_guarantee_map_post');
