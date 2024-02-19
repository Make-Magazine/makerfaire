<?php
// Group admin menu items under one parent
function mpfy_amg_update_menu() {
    add_menu_page(MAPIFY_PLUGIN_NAME, MAPIFY_PLUGIN_NAME, 'manage_options', 'mapify.php', '', 'dashicons-location', '33.333');
    if ( taxonomy_exists('location-tag') ) {
        add_submenu_page('mapify.php', 'Location Tags', 'Location Tags', 'manage_options', 'edit-tags.php?taxonomy=location-tag');
    }
}
add_action('admin_menu', 'mpfy_amg_update_menu', 10);

function mpfy_amg_map_settings_menu() {
    add_submenu_page('mapify.php', 'Map Settings', 'Map Settings', 'manage_options', 'edit.php?post_type=map');
}
add_action('admin_menu', 'mpfy_amg_map_settings_menu', 9);

function mpfy_amg_reorder_menu() {
    global $submenu;

    // Reorder
    if ( $submenu['mapify.php'][0][2] == 'edit.php?post_type=map-location' && $submenu['mapify.php'][1][2] == 'edit.php?post_type=map' ) {
    	$b = $submenu['mapify.php'][1];
    	$submenu['mapify.php'][1] = $submenu['mapify.php'][0];
    	$submenu['mapify.php'][0] = $b;
    }

    // Delete the default "Maps" admin link as we already has a "Map settings" one
    if ($submenu['mapify.php'][2][2] == 'edit.php?post_type=map') {
        unset($submenu['mapify.php'][2]);
    }
}
add_action('admin_menu', 'mpfy_amg_reorder_menu', 20);

// Set proper admin menu parent items
function mpfy_amg_menu_parent_file($parent_file) {
    global $current_screen;

    if ($current_screen->taxonomy == 'location-tag') {
        $parent_file = 'mapify.php';
    }

    if ($current_screen->post_type == 'map') {
        $parent_file = 'mapify.php';
    }

    return $parent_file;
}
add_action('parent_file', 'mpfy_amg_menu_parent_file');
