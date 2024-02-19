<?php

// Load the textdomain
function crowd_load_textdomain() {
	$dir = dirname( plugin_basename( CROWD_PLUGIN_FILE ) ) . DIRECTORY_SEPARATOR . 'languages';
	load_plugin_textdomain( 'crowd', false, $dir );
}
add_action( 'plugins_loaded', 'crowd_load_textdomain' );

// Enqueue front-end assets
function crowd_enqueue_assets() {
	if ( ! defined( 'MPFY_LOAD_ASSETS' ) || is_admin() ) {
		return false;
	}

	global $post;

	wp_enqueue_style( 'redactor', plugins_url( 'assets/redactor.css', CROWD_PLUGIN_FILE ) );
	wp_enqueue_style( 'crowdmaps', plugins_url( 'assets/crowdmaps.css', CROWD_PLUGIN_FILE ) );

	wp_enqueue_script( 'plupload-all' );
	wp_enqueue_script( 'jquery-blockui', plugins_url( 'assets/js/jquery.blockUI.js', CROWD_PLUGIN_FILE ), array( 'jquery' ), false, true );
	wp_enqueue_script( 'jquery-form', plugins_url( 'assets/js/jquery.form.js', CROWD_PLUGIN_FILE), array( 'jquery' ), false, true );
	wp_enqueue_script( 'redactor', plugins_url( 'assets/js/redactor.min.js', CROWD_PLUGIN_FILE ), array( 'jquery' ), false, true );
	wp_register_script( 'crowdmaps', plugins_url( 'assets/js/crowdmaps.js', CROWD_PLUGIN_FILE ), array( 'mapify' ), mpfy_get_cache_buster_version(), true );
	wp_localize_script( 'crowdmaps', 'crowdMapsStrings', array(
		'marker_created_content'   => __( 'Nice! You can now drag and drop your location as needed or you can enter a specific address on the next step.', 'crowd' ),
		'marker_created_button'    => __( 'All set? Click here to add details...' ),
		'marker_submitted_content' => __( 'This location has been submitted.', 'crowd' ),
		'post_id'                  => $post->ID,
	) );
	wp_enqueue_script( 'crowdmaps' );
}
add_action( 'wp_footer', 'crowd_enqueue_assets' );

// add_image_size for crowd-preview 
function crowd_add_image_sizes() {
    add_image_size( 'crowd-preview', 80, 80, true );
}
add_action( 'after_setup_theme', 'crowd_add_image_sizes' );