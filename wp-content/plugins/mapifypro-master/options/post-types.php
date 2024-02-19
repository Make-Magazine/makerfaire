<?php  

// Maps
register_post_type( 'map', array(
	'labels'              => array(
		'name'	             => __( 'Maps', 'mpfy' ),
		'singular_name'      => __( 'Map', 'mpfy' ),
		'add_new'            => __( 'Add New', 'mpfy' ),
		'add_new_item'       => __( 'Add new Map', 'mpfy' ),
		'view_item'          => __( 'View Map', 'mpfy' ),
		'edit_item'          => __( 'Edit Map', 'mpfy' ),
	    'new_item'           => __( 'New Map', 'mpfy' ),
	    'view_item'          => __( 'View Map', 'mpfy' ),
	    'search_items'       => __( 'Search Maps', 'mpfy' ),
	    'not_found'          => __( 'No Maps found', 'mpfy' ),
	    'not_found_in_trash' => __( 'No Maps found in Trash', 'mpfy' ),
	    'all_items'          => mpfy_get_icon( 'maps' ) . __( 'Maps', 'mpfy' ),
	),
	'public'              => false,
	'exclude_from_search' => true,
	'show_ui'             => true,
	'capability_type'     => 'post',
	'hierarchical'        => false,
	'rewrite'             => false,
	'query_var'           => true,
	'supports'            => array( 'title' ),
	'show_in_menu'        => 'mapify.php',
) );

// Map Locations
register_post_type( 'map-location', array(
	'labels'              => array(
		'name'	             => __( 'Map Locations', 'mpfy' ),
		'singular_name'      => __( 'Map Location', 'mpfy' ),
		'add_new'            => __( 'Add New', 'mpfy' ),
		'add_new_item'       => __( 'Add new Map Location', 'mpfy' ),
		'view_item'          => __( 'View Map Location', 'mpfy' ),
		'edit_item'          => __( 'Edit Map Location', 'mpfy' ),
	    'new_item'           => __( 'New Map Location', 'mpfy' ),
	    'view_item'          => __( 'View Map Location', 'mpfy' ),
	    'search_items'       => __( 'Search Map Locations', 'mpfy' ),
	    'not_found'          => __( 'No Map Locations found', 'mpfy' ),
	    'not_found_in_trash' => __( 'No Map Locations found in Trash', 'mpfy' ),
		'all_items'          => mpfy_get_icon( 'map-locations' ) . __( 'Map Locations', 'mpfy' ),
	),
	'public'              => true,
	'exclude_from_search' => true,
	'show_ui'             => true,
	'capability_type'     => array( 'map_location', 'map_locations' ),
	'map_meta_cap'        => true,
	'hierarchical'        => false,
	'rewrite'             => array(
		"slug"       => "map-location",
		"with_front" => false,
	),
	'query_var'           => true,
	'has_archive'         => 'map-locations',
	'supports'            => array( 'title', 'editor', 'thumbnail' ),
	'show_in_menu'        => 'mapify.php',
) );

// Map Drawer
register_post_type( 'map-drawer', array(
	'labels'              => array(
		'name'	             => __( 'Map Areas', 'mpfy' ),
		'singular_name'      => __( 'Map Area', 'mpfy' ),
		'add_new'            => __( 'Draw New Area', 'mpfy' ),
		'add_new_item'       => __( 'Draw New Area', 'mpfy' ),
		'view_item'          => __( 'View Area', 'mpfy' ),
		'edit_item'          => __( 'Edit Area', 'mpfy' ),
	    'new_item'           => __( 'Draw New Area', 'mpfy' ),
	    'view_item'          => __( 'View Area', 'mpfy' ),
	    'search_items'       => __( 'Search Areas', 'mpfy' ),
	    'not_found'          => __( 'No Maps found', 'mpfy' ),
	    'not_found_in_trash' => __( 'No Maps found in Trash', 'mpfy' ),
		'all_items'          => mpfy_get_icon( 'map-areas' ) . __( 'Map Areas', 'mpfy' ),
	),
	'public'              => false,
	'exclude_from_search' => true,
	'show_ui'             => true,
	'capability_type'     => 'post',
	'hierarchical'        => false,
	'rewrite'             => false,
	'query_var'           => true,
	'supports'            => array( 'title' ),
	'show_in_menu'        => 'mapify.php',
) );

do_action( 'mpfy_post_types_registered' );
