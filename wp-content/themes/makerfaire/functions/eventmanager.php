<?php
/* Functions specific to EventManager Plugin */

// Add our Region Taxonomy
add_action( 'init', 'register_taxonomy_regions' );
function register_taxonomy_regions() {

	$labels = array(
		'name' => _x( 'Regions', 'regions' ),
		'singular_name' => _x( 'Region', 'regions' ),
		'search_items' => _x( 'Search Regions', 'regions' ),
		'popular_items' => _x( 'Popular Regions', 'regions' ),
		'all_items' => _x( 'All Regions', 'regions' ),
		'parent_item' => _x( 'Parent Region', 'regions' ),
		'parent_item_colon' => _x( 'Parent Region:', 'regions' ),
		'edit_item' => _x( 'Edit Region', 'regions' ),
		'update_item' => _x( 'Update Region', 'regions' ),
		'add_new_item' => _x( 'Add New Region', 'regions' ),
		'new_item_name' => _x( 'New Region', 'regions' ),
		'separate_items_with_commas' => _x( 'Separate regions with commas', 'regions' ),
		'add_or_remove_items' => _x( 'Add or remove Regions', 'regions' ),
		'choose_from_most_used' => _x( 'Choose from most used Regions', 'regions' ),
		'menu_name' => _x( 'Regions', 'regions' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => array( 'slug' => 'regions', 'with_front' => false ),
		'query_var' => true,
		'show_in_rest' => true
	);

	register_taxonomy( 'regions', array('event'), $args );
}
function events_taxonomy_register(){
    register_taxonomy_for_object_type('regions',EM_POST_TYPE_EVENT);
    register_taxonomy_for_object_type('regions',EM_POST_TYPE_LOCATION);
}
if(post_type_exists('event')) {
    add_action('init','events_taxonomy_register',100);
}