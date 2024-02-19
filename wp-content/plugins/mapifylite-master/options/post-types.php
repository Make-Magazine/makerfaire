<?php  
register_post_type('map-location', array(
	'labels' => array(
		'name'	 => 'Map Locations',
		'singular_name' => 'Map Location',
		'add_new' => __( 'Add New' ),
		'add_new_item' => __( 'Add new Map Location' ),
		'view_item' => 'View Map Location',
		'edit_item' => 'Edit Map Location',
	    'new_item' => __('New Map Location'),
	    'view_item' => __('View Map Location'),
	    'search_items' => __('Search Map Locations'),
	    'not_found' =>  __('No Map Locations found'),
	    'not_found_in_trash' => __('No Map Locations found in Trash'),
	),
	'public' => true,
	'exclude_from_search' => true,
	'show_ui' => true,
	'capability_type' => 'post',
	'hierarchical' => false,
	'rewrite' => array(
		"slug" => "map-location",
		"with_front" => false,
	),
	'query_var' => true,
	'has_archive' => 'map-locations',
	'supports' => array('title', 'editor'),
	'show_in_menu' => 'mapify.php',
));

register_post_type('map', array(
	'labels' => array(
		'name'	 => 'Maps',
		'singular_name' => 'Map',
		'add_new' => __( 'Add New' ),
		'add_new_item' => __( 'Add new Map' ),
		'view_item' => 'View Map',
		'edit_item' => 'Edit Map',
	    'new_item' => __('New Map'),
	    'view_item' => __('View Map'),
	    'search_items' => __('Search Maps'),
	    'not_found' =>  __('No Maps found'),
	    'not_found_in_trash' => __('No Maps found in Trash'),
	),
	'public' => false,
	'exclude_from_search' => true,
	'show_ui' => true,
	'capability_type' => 'post',
	'hierarchical' => false,
	'rewrite' => false,
	'query_var' => true,
	'supports' => array('title'),
	'show_in_menu' => 'mapify.php',
));

do_action('mpfy_post_types_registered');
