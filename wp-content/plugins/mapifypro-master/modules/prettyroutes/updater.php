<?php

/**
 * Notify and then run the updater script, 
 * only if the current plugin version is 2.0.0 or below.
 */
function routes_updater_hook_for_v2() {
	if ( isset( $_GET['routes-update'] ) ) {
		routes_update();
	}

	if ( version_compare( routes_get_version(), '2.0.0', '<=' ) ) {
		$maps = get_posts( 'post_type=map&posts_per_page=-1' );
		$map_locations = get_posts( 'post_type=map-location&posts_per_page=-1' );

		if ( $maps || $map_locations ) {
			add_action( 'admin_notices', 'routes_show_update_notice' );
		} else {
			update_option( 'routes_plugin_version', PRETTYROUTES_PLUGIN_VERSION );
		}
	}
}
add_action( 'admin_menu', 'routes_updater_hook_for_v2' );

/**
 * Get prettyroutes version
 */
function routes_get_version() {
	$current_version = get_option( 'routes_plugin_version', PRETTYROUTES_PLUGIN_VERSION );
	return $current_version;
}

/**
 * Run the updater scripts
 */
function routes_update() {	
	$updates_done = 0;
	$versions     = array(
		'1.2.0' => 'routes_updater_1_2_0',
		'2.0.0' => 'routes_updater_2_0_0',
	);
	
	foreach ( $versions as $version => $updater ) {
		if ( version_compare( routes_get_version(), $version ) < 0 ) {
			if ( $updater ) {
				call_user_func( $updater );
			}

			update_option( 'routes_plugin_version', $version );
			$updates_done++;
		}
	}

	if ( $updates_done > 0 ) {
		add_action( 'admin_notices', 'routes_show_update_success_notice' );
	}

	// update version to latest
	update_option( 'routes_plugin_version', PRETTYROUTES_PLUGIN_VERSION );
}

/**
 * Update notice for admin
 */
function routes_show_update_notice() {
	echo '<div class="error"><p>Warning - your PrettyRoutes plugin data must be updated. Please backup your data and <a href="' . add_query_arg('routes-update', '1', admin_url('/')) . '">click here</a> to proceed.</p></div>';
}

/**
 * Update success notice for admin
 */
function routes_show_update_success_notice() {
	echo '<div class="updated"><p>Your PrettyRoutes data has been updated.</p></div>';
}

/**
 * Updater script for plugin v1.2.0
 */
function routes_updater_1_2_0() {
	$routes = get_posts( 'post_type=route&posts_per_page=-1' );
	foreach ( $routes as $p ) {
		update_post_meta( $p->ID, '_route_type', 'road' );
	}
}

/**
 * Updater script for plugin v2.0.0
 */
function routes_updater_2_0_0() {
	$routes = get_posts( 'post_type=route&posts_per_page=-1' );

	foreach ( $routes as $p ) {
		$route           = get_post_meta( $p->ID, '_route_route', true );
		$route_points    = explode( '|', $route );
		$waypoints_count = max( 0, count( $route_points ) - 2 );

		for ( $i=0; $i < $waypoints_count; $i++ ) {
			$pin_enabled     = get_post_meta( $p->ID, '_route_waypoint_' . $i . '_pin_enabled', true );
			$tooltip_enabled = get_post_meta( $p->ID, '_route_waypoint_' . $i . '_tooltip_enabled', true );
			$tooltip_close   = get_post_meta( $p->ID, '_route_waypoint_' . $i . '_tooltip_close', true );
			$tooltip_content = get_post_meta( $p->ID, '_route_waypoint_' . $i . '_tooltip_content', true );
			$pin             = get_post_meta( $p->ID, '_route_waypoint_' . $i . '_pin', true );

			update_post_meta( $p->ID, '_route_waypoints_-_pin_enabled_' . $i, $pin_enabled );
			update_post_meta( $p->ID, '_route_waypoints_-_tooltip_enabled_' . $i, $tooltip_enabled );
			update_post_meta( $p->ID, '_route_waypoints_-_tooltip_close_' . $i, $tooltip_close );
			update_post_meta( $p->ID, '_route_waypoints_-_tooltip_content_' . $i, $tooltip_content );
			update_post_meta( $p->ID, '_route_waypoints_-_pin_' . $i, $pin );
		}
	}
}
