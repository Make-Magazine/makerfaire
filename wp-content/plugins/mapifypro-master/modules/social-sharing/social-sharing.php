<?php

// Enqueue front-end assets
function mpfy_ss_enqueue_assets() {
	if ( ! defined( 'MPFY_LOAD_ASSETS' ) ) {
		return;
	}
	
	// Load popup styles
	wp_enqueue_style( 'mpfy-social-sharing', plugins_url( 'modules/social-sharing/style.css', MAPIFY_PLUGIN_FILE ), array(), MAPIFY_PLUGIN_VERSION );

	// Load popup behaviors
	wp_enqueue_script( 'mpfy-social-sharing', plugins_url( 'modules/social-sharing/functions.js', MAPIFY_PLUGIN_FILE ), array( 'jquery' ), mpfy_get_cache_buster_version(), true );

	// load sharethis
	$load = mpfy_carbon_get_theme_option( 'mpfy_load_sharethis' );
	$load = $load ? $load : 'y';

	if ( 'y' === $load ) {
		$script_tag_attrs = array(
			'id'    => 'mpfy-sharethis-buttons',
			'async' => 'async',
			'src'   => 'https://platform-api.sharethis.com/js/sharethis.js',
		);

		if ( function_exists( 'wp_print_script_tag' ) ) {			
			wp_print_script_tag( $script_tag_attrs ); // add async attributes on wordpress 5.7 or above
		} else {
			wp_enqueue_script( 'mpfy-sharethis-buttons', 'https://platform-api.sharethis.com/js/sharethis.js', array(), MAPIFY_PLUGIN_VERSION, true );			
		}
	}
}
add_action( 'wp_footer', 'mpfy_ss_enqueue_assets' );

// Add sharing services to Mapify popup
function mpfy_ss_popup_buttons( $post_id, $map_id ) {
	include_once( 'blocks/social-buttons.php' );
}
add_action( 'mpfy_popup_before_section', 'mpfy_ss_popup_buttons', 10, 2 );
