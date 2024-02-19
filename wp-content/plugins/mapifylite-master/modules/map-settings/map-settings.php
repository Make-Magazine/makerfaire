<?php
add_action('admin_enqueue_scripts', 'mpfy_ms_admin_behaviors');
function mpfy_ms_admin_behaviors($hook) {
    if ($hook !== 'post.php' || !isset($_GET['post'])) {
        return;
    }

    $map_id = intval($_GET['post']);
    $post = get_post($map_id);
    if ($post->post_type != 'map') {
    	return;
    }

    wp_enqueue_script('mpfy-map-settings-admin', plugins_url('modules/map-settings/admin.js', MAPIFY_PLUGIN_FILE), array('jquery'), '1.0.0', false);
}

add_action('admin_menu', 'mpfy_ms_map_settings_limit', 9);
function mpfy_ms_map_settings_limit() {
	global $pagenow;

	if (
		($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'map')
		||
		($pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'map')
		) {
	    $map_id = Mpfy_Map::get_first_map_id();
	    $map_settings = 'post.php?post=' . $map_id . '&action=edit';
    	wp_redirect($map_settings);
    	exit;
    }
}

add_action('init', 'mpfy_ms_guarantee_map_post');
function mpfy_ms_guarantee_map_post($custom_fields) {
	$maps = Mpfy_Map::get_all_maps();
	if (!empty($maps)) {
		return;
	}

	$admins = get_users(array('role'=>'administrator'));

	$new_post = array(
		'post_type'=>'map',
		'post_status'=>'publish',
		'post_title'=>MAPIFY_PLUGIN_NAME . ' Map',
		'post_author'=>$admins[0]->ID,
	);
	wp_insert_post($new_post);
}
