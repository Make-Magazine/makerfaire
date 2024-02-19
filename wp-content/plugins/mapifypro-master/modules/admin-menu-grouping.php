<?php

/**
 * Group admin menu items under one parent
 */
function mpfy_amg_update_menu() {
    add_menu_page( 'MapifyPro', 'MapifyPro', 'edit_map_locations', 'mapify.php', '', 'dashicons-location-alt', '33.333' );
    add_submenu_page( 'mapify.php', 'Location Tags', mpfy_get_icon( 'location-tags' ) . 'Location Tags', 'manage_categories', 'edit-tags.php?taxonomy=location-tag' );    
}
add_action( 'admin_menu', 'mpfy_amg_update_menu', 10 );

/**
 * Highlight proper admin menu
 */
function mpfy_amg_menu_parent_file( $parent_file ) {
    global $current_screen;
	
    if ( isset( $current_screen->taxonomy ) && 'location-tag' === $current_screen->taxonomy ) {
        $parent_file = 'mapify.php';
    }

    return $parent_file;
}
add_filter( 'parent_file', 'mpfy_amg_menu_parent_file' );

/**
 * Highlight proper admin submenu
 */
function mpfy_amg_menu_submenu_file( $submenu_file, $parent_file ) {
    global $current_screen;

	if ( ! $current_screen || ! isset( $current_screen->id ) ) {
		return $submenu_file;
	}

	switch ( $current_screen->id ) {
		case 'map':
			$submenu_file = 'edit.php?post_type=map';
			break;

		case 'map-location':
			$submenu_file = 'edit.php?post_type=map-location';
			break;

		case 'map-drawer':
			$submenu_file = 'edit.php?post_type=map-drawer';
			break;
		
		default:
			// silent here
			break;
	}

    return $submenu_file;
}
add_filter( 'submenu_file', 'mpfy_amg_menu_submenu_file', 10, 2 );
