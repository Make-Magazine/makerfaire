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
		'show_in_quick_edit' => false,
   		 'meta_box_cb' => false,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => array( 'slug' => 'regions', 'with_front' => false ),
		'query_var' => true,
		'show_in_rest' => false
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

function getCountryName($code) {
    $json = file_get_contents("http://country.io/names.json");
    $countries = json_decode($json, TRUE);

    return array_key_exists($code,$countries) ? $countries[$code] : false;
}

//default Events menu item to go to all events (instead of defaulting to future)
add_action( 'admin_menu', 'change_media_label' );
function change_media_label(){
  global $submenu;
  if(isset($submenu["edit.php?post_type=event"])){	
	foreach($submenu["edit.php?post_type=event"] as $subMenuKey=>$subMenuItem){
		foreach($subMenuItem as $itemKey =>$subMenuLink){			
			if($subMenuLink == 'edit.php?post_type=event'){				
				$submenu["edit.php?post_type=event"][$subMenuKey][$itemKey] = $subMenuLink .'&scope=all';
			}
		}
	}
  }
}

//change the Events CPT labels
function change_post_object_label() {
    global $wp_post_types;
	
	$event_cpt = &$wp_post_types['event'];
	$event_cpt->menu_icon = "https:\/\/global.makerfaire.com\/favicon-16x16.png";
    $labels = &$wp_post_types['event']->labels;
    $labels->name = 'Faires';
	$labels->menu_name = 'Faires';
    $labels->singular_name = 'Faire';
    $labels->add_new = 'Add Faire';
    $labels->add_new_item = 'Add Faire';
    $labels->edit_item = 'Edit Faire';
    $labels->new_item = 'Faire';
    $labels->all_items = 'All Faires';
    $labels->view_item = 'View Faire';
    $labels->search_items = 'Search Faire';
    $labels->not_found = 'No Faire found';
    $labels->not_found_in_trash = 'No Faire found in Trash';    
}
add_action( 'init', 'change_post_object_label', 999 );