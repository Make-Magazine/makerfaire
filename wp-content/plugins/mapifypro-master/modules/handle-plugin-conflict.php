<?php

/**
 * Reset conflict status
 */
function mpfy_reset_conflict_status() {
	delete_option( 'mpfy_plugin_conflict' );
}

/**
 * Get conflict status
 */
function mpfy_get_conflict_status( $plugin_name ) {
	$option = get_option( 'mpfy_plugin_conflict', array() );
	$status = isset( $option[ $plugin_name ] ) ? $option[ $plugin_name ] : 'yes';
	return 'yes' === $status;
}

/**
 * Set conflict status
 */
function mpfy_set_conflict_status( $plugin_name, $status = true ) {
	$option = get_option( 'mpfy_plugin_conflict', array() );
	$option = is_array( $option ) ? $option : array();

	if ( $status ) {
		$option[ $plugin_name ] = 'yes';
	} else {
		$option[ $plugin_name ] = 'no';
	}

	update_option( 'mpfy_plugin_conflict', $option, true );
}