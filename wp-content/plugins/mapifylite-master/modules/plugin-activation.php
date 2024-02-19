<?php

/**
 * On plugin activation.
 * Schedule to flush rewrite rules on the next minute.
 */
function mpfy_activate() {	
	$event_name = 'mpfy_flush_rewrite_rules';
	$next_event = wp_get_scheduled_event( $event_name );

	if ( ! $next_event ) {
		wp_schedule_single_event( time() + 60, $event_name );
	}
}
register_activation_hook( MAPIFY_PLUGIN_FILE, 'mpfy_activate' );

/**
 * Scheduled action to flush rewrite rules
 */
function mpfy_flush_rewrite_rules_function() {
	flush_rewrite_rules();
}
add_action( 'mpfy_flush_rewrite_rules', 'mpfy_flush_rewrite_rules_function', 10 );