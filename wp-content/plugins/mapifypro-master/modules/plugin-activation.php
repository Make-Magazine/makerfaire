<?php

include_once( 'handle-plugin-conflict.php' );

/**
 * Reset the plugin loded status on:
 * - When plugin's failed to load
 * - After plugin update 
 * - On plugin deactivation
 */
function mpfy_reset_plugin_loaded_status() {
	$is_previously_loaded = get_option( 'mpfy_plugin_loaded' );

	if ( $is_previously_loaded ) {
		update_option( 'mpfy_plugin_loaded', 0 );
		mpfy_reset_conflict_status(); // reset plugin conflict status
	}
}
add_action( 'mpfy_plugin_failed_to_load', 'mpfy_reset_plugin_loaded_status' );
add_action( 'mpfy_version_updated', 'mpfy_reset_plugin_loaded_status' );
register_deactivation_hook( MAPIFY_PLUGIN_FILE, 'mpfy_reset_plugin_loaded_status' );

/**
 * Reset conflict status on plugin activation and deactivation
 */
function mpfy_activation_deactivation_hook_reset_conflict_status() {
	mpfy_reset_conflict_status(); // reset plugin conflict status
}
register_activation_hook( MAPIFY_PLUGIN_FILE, 'mpfy_activation_deactivation_hook_reset_conflict_status' );
register_deactivation_hook( MAPIFY_PLUGIN_FILE, 'mpfy_activation_deactivation_hook_reset_conflict_status' );

/**
 * When plugin's succesfully loaded
 */
function mpfy_loaded() {
	$is_previously_loaded = get_option( 'mpfy_plugin_loaded' );

	if ( ! $is_previously_loaded ) {
		mpfy_update_map_location_capability_type();
		update_option( 'mpfy_plugin_loaded', 1 );

		// register mpfy_contributor user role for CrowdMaps
		include_once( 'crowdmaps/modules/register-contributor-user-role.php' );
		
		// schedule to flush rewrite rules on the next minute
		$event_name = 'mpfy_flush_rewrite_rules';
		$next_event = wp_get_scheduled_event( $event_name );

		if ( ! $next_event ) {
			wp_schedule_single_event( time() + 60, $event_name );
		}		
	}	
}
add_action( 'mpfy_plugin_loaded', 'mpfy_loaded' );

/**
 * Scheduled action to flush rewrite rules
 */
function mpfy_flush_rewrite_rules_function() {
	flush_rewrite_rules();
}
add_action( 'mpfy_flush_rewrite_rules', 'mpfy_flush_rewrite_rules_function', 10 );

/**
 * Update location capability type
 */
function mpfy_update_map_location_capability_type() {
	$administrator = get_role( 'administrator' );
	$editor        = get_role( 'editor' );
	$author        = get_role( 'author' );
	$contributor   = get_role( 'contributor' );
	$cap_type      = array(
		'singular' => 'map_location',
		'plural'   => 'map_locations',
	);
	
	// add custom caps to administrator role
	$administrator->add_cap( "read_{$cap_type['singular']}" ); 
	$administrator->add_cap( "read_private_{$cap_type['plural']}" ); 
	$administrator->add_cap( "publish_{$cap_type['plural']}" ); 
	$administrator->add_cap( "edit_{$cap_type['plural']}" ); 
	$administrator->add_cap( "edit_{$cap_type['singular']}" ); 
	$administrator->add_cap( "edit_others_{$cap_type['plural']}" ); 
	$administrator->add_cap( "edit_private_{$cap_type['plural']}" ); 
	$administrator->add_cap( "edit_published_{$cap_type['plural']}" ); 
	$administrator->add_cap( "delete_{$cap_type['plural']}" ); 
	$administrator->add_cap( "delete_{$cap_type['singular']}" ); 
	$administrator->add_cap( "delete_private_{$cap_type['plural']}" ); 
	$administrator->add_cap( "delete_published_{$cap_type['plural']}" ); 
	$administrator->add_cap( "delete_others_{$cap_type['plural']}" );

	// add custom caps to editor role
	$editor->add_cap( "read_{$cap_type['singular']}" ); 
	$editor->add_cap( "read_private_{$cap_type['plural']}" ); 
	$editor->add_cap( "publish_{$cap_type['plural']}" ); 
	$editor->add_cap( "edit_{$cap_type['plural']}" ); 
	$editor->add_cap( "edit_{$cap_type['singular']}" ); 
	$editor->add_cap( "edit_others_{$cap_type['plural']}" ); 
	$editor->add_cap( "edit_private_{$cap_type['plural']}" ); 
	$editor->add_cap( "edit_published_{$cap_type['plural']}" ); 
	$editor->add_cap( "delete_{$cap_type['plural']}" ); 
	$editor->add_cap( "delete_{$cap_type['singular']}" ); 
	$editor->add_cap( "delete_private_{$cap_type['plural']}" ); 
	$editor->add_cap( "delete_published_{$cap_type['plural']}" ); 
	$editor->add_cap( "delete_others_{$cap_type['plural']}" );

	// add custom caps to author role
	$author->add_cap( "read_{$cap_type['plural']}" );
	$author->add_cap( "edit_{$cap_type['plural']}" ); 
	$author->add_cap( "edit_{$cap_type['singular']}" ); 
	$author->add_cap( "edit_published_{$cap_type['plural']}" );
	$author->add_cap( "delete_{$cap_type['plural']}" ); 
	$author->add_cap( "delete_{$cap_type['singular']}" ); 
	$author->add_cap( "delete_published_{$cap_type['plural']}" ); 
	$author->add_cap( "upload_files" ); 

	// add custom caps to contributor role
	$contributor->add_cap( "read_{$cap_type['plural']}" );
	$contributor->add_cap( "edit_{$cap_type['plural']}" ); 
	$contributor->add_cap( "edit_{$cap_type['singular']}" ); 
	$contributor->add_cap( "delete_{$cap_type['plural']}" ); 
	$contributor->add_cap( "delete_{$cap_type['singular']}" );
}