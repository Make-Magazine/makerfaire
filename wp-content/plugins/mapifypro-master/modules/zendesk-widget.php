<?php

/**
 * Add a Zendesk widget to MapifyPro admin pages
 */
function mpfy_zendesk_widget( $hook ) {
	$screen      = get_current_screen();
	$screen_id   = isset( $screen->id ) ? $screen->id : false;
	$allowed_ids = array( 
		'map', 
		'map-location', 
		'map-drawer', 
		'route', 
		'route_map', 
		'edit-map', 
		'edit-map-location', 
		'edit-map-drawer', 
		'edit-location-tag', 
		'edit-route', 
		'edit-route_map', 
		'mapifypro_page_crowd-inbox', 
		'mapifypro_page_mpfy-import', 
		'mapifypro_page_mapifypro-multi-map', 
		'mapifypro_page_mapifypro-settings', 
	);

	if ( ! in_array( $screen_id, $allowed_ids ) ) {
		return;
	}

	wp_enqueue_script( 'ze-snippet', "https://static.zdassets.com/ekr/snippet.js?key=a3d89db5-c78a-4699-b055-f20d4180c88c", array(), null, false );
}
add_action( 'admin_enqueue_scripts', 'mpfy_zendesk_widget' );


/**
 * Make sure the script ID is exactly `ze-snippet` instead of `ze-snippet-js`
 */
function mpfy_zendesk_widget_script_loader_tag( $tag, $handle, $src ) {
    if ( 'ze-snippet' === $handle ) {
        $tag = '<script id="ze-snippet" src="' . esc_url( $src ) . '"></script>';
    }

    return $tag;
}
add_filter( 'script_loader_tag', 'mpfy_zendesk_widget_script_loader_tag', 10, 3 );