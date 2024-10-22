<?php
/* Functions specific to EventManager Plugin */

// Add our Region Taxonomy
add_action('init', 'register_taxonomy_regions');
function register_taxonomy_regions() {

	$labels = array(
		'name' => _x('Region', 'regions'),
		'singular_name' => _x('Region', 'regions'),
		'search_items' => _x('Search Regions', 'regions'),
		'popular_items' => _x('Popular Regions', 'regions'),
		'all_items' => _x('All Regions', 'regions'),
		'edit_item' => _x('Edit Region', 'regions'),
		'update_item' => _x('Update Region', 'regions'),
		'add_new_item' => _x('Add New Region', 'regions'),
		'new_item_name' => _x('New Region', 'regions'),
		'separate_items_with_commas' => _x('Separate regions with commas', 'regions'),
		'add_or_remove_items' => _x('Add or remove Regions', 'regions'),
		'choose_from_most_used' => _x('Choose from most used Regions', 'regions'),
		'menu_name' => _x('Regions', 'regions'),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_in_quick_edit' => false,
		'meta_box_cb' => false,
		'show_tagcloud' => false,
		'hierarchical' => false,
		'query_var' => true,
		'show_in_rest' => false,
		'show_admin_column' => true,
		'show_in_menu' => false,
		'show_ui' => false
	);

	register_taxonomy('regions', array('event'), $args);
}

function events_taxonomy_register() {
	register_taxonomy_for_object_type('regions', EM_POST_TYPE_EVENT);
	register_taxonomy_for_object_type('regions', EM_POST_TYPE_LOCATION);
}
if (post_type_exists('event')) {
	add_action('init', 'events_taxonomy_register', 100);
}

//default Events menu item to go to all events (instead of defaulting to future)
add_action('admin_menu', 'change_media_label');
function change_media_label() {
	global $submenu;
	if (isset($submenu["edit.php?post_type=event"])) {
		foreach ($submenu["edit.php?post_type=event"] as $subMenuKey => $subMenuItem) {
			foreach ($subMenuItem as $itemKey => $subMenuLink) {
				if ($subMenuLink == 'edit.php?post_type=event') {
					$submenu["edit.php?post_type=event"][$subMenuKey][$itemKey] = $subMenuLink . '&scope=all';
				}
			}
		}
	}
}

//change the Events CPT labels
function change_post_object_label() {
	global $wp_post_types;

	$event_cpt = &$wp_post_types['event'];
	if(!empty($event_cpt)){
		$event_cpt->menu_icon = "https:\/\/global.makerfaire.com\/favicon-16x16.png";
	}

	$labels = &$wp_post_types['event']->labels;
	if(!empty($labels)){
		$labels->name = 'Faires';
		$labels->menu_name = 'Yearbook Faires';
		$labels->singular_name = 'Faire';
		$labels->add_new = 'Add a Faire';
		$labels->add_new_item = 'Add Faire';
		$labels->edit_item = 'Edit Faire';
		$labels->new_item = 'Faire';
		$labels->all_items = 'All Faires';
		$labels->view_item = 'View Faire';
		$labels->search_items = 'Search Faire';
		$labels->not_found = 'No Faire found';
		$labels->not_found_in_trash = 'No Faire found in Trash';
	}
}
add_action('init', 'change_post_object_label', 999);

//add country taxonomy
function register_taxonomy_countries() {
	$labels = array(
		'name' => _x('Country', 'regions'),
		'singular_name' => _x('Country', 'regions'),
		'search_items' => _x('Search Countries', 'regions'),
		'all_items' => _x('All Countries', 'regions'),
		'update_item' => _x('Update Country', 'regions'),
		'add_new_item' => _x('Add New Country', 'regions'),
		'new_item_name' => _x('New Country', 'regions'),
		'add_or_remove_items' => _x('Add or remove Countries', 'regions'),
		'menu_name' => _x('Countries', 'regions'),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'hierarchical' => false,
		'query_var' => true,
		'show_admin_column' => true,
		'show_in_menu' => false,
		'show_ui' => false
	);

	register_taxonomy('countries', array('event'), $args);
}
add_action('init', 'register_taxonomy_countries');

/* This isn't necessary if we have site rand() on
function search_and_filter_random_order( $query_args, $sfid ) {
	if(($sfid==661619 || $sfid==661622) && empty($_GET['sort_order'])) {
		session_start();
		// Detect first page and reset seed
		if( empty($_GET['sf_paged']) || $_GET['sf_paged'] == 0 || $_GET['sf_paged'] == 1 ) {
			if( isset( $_SESSION['seed'] ) ) {
				unset( $_SESSION['seed'] );
			}
		}
		// Get seed from session variable if it exists and store it
		$seed = false;
		if( isset( $_SESSION['seed'] ) ) {
			$seed = $_SESSION['seed'];
		}
		// Set new seed if none exists
		if ( ! $seed ) {
			$seed = rand();
			$_SESSION['seed'] = $seed;
		}
		//modify $query_args here before returning it
		$query_args['orderby'] = 'RAND(' . $seed . ')';
	}
	return $query_args;
}
add_filter( 'sf_edit_query_args', 'search_and_filter_random_order', 20, 2 );
*/

//set the yoast meta description dynamically
function set_yearbook_meta_desc($description) {
	$post_type = get_post_type();
	if ($post_type == 'projects') {
		//Pull faire specific information
		$faireData       = get_field("faire_information");
		$faire_year      = (isset($faireData["faire_year"]) ? $faireData["faire_year"] : '');
		//Pull associated faire post
		if(isset($faireData["faire_post"])){
			$faire_id   = $faireData["faire_post"];
			$faire_name = get_the_title($faire_id);
		}else{
			$faire_id   = '';
			$faire_name = '';
		}

		//Project Information
		$project_title   = get_the_title();
		$description = 'Maker Faire ' . $faire_name ." ".$faire_year. ' - ' . $project_title.' - '.html_entity_decode(get_field("exhibit_description"));		
	}elseif ($post_type == 'event') {		
		$faire_name = get_the_title();
		$faire_id 	= get_the_ID();
		$EM_Event 	= em_get_event($faire_id, 'post_id');
						
		// Dates
		$faire_year 			= date('Y', strtotime($EM_Event->event_start_date));
		$faire_date 			= date("F Y", strtotime($EM_Event->event_start_date));

		//faire location
		$event_location 		= $EM_Event->get_location();
		$faire_countries 		= em_get_countries();
		$faire_country 			= (isset($event_location->location_country)?$event_location->location_country:'');

		$faireInfo 				= get_field('faire_info');
		$faire_num_attendees 	= $faireInfo['number_of_attendees'];
		$faire_num_projects 	= $faireInfo['number_of_projects'];

		$description = 'Maker Faire '. $faire_name. ' ' .$faire_date .
			(isset($faire_countries[$faire_country])? ' - '. $faire_countries[$faire_country]:'') .
			($faire_num_projects  != '' ? ' - Projects: ' . number_format($faire_num_projects):'') .
			($faire_num_attendees != '' ? ' - Participants: ' . number_format($faire_num_attendees):'');
	}
	return $description;
}
add_filter('wpseo_metadesc', 'set_yearbook_meta_desc');
