<?php
//define the yearbook faires custompost type
function mf_yearbook_custom_post_type() {
	//define labels
	$labels = array(
		"name" => __("Yearbook Faires",  'makerfaire'),
		'singular_name' => __('Yearbook Faire', 'makerfaire'),

		"menu_name" => __("Yearbook Faires",  'makerfaire'),
		"all_items" => __("Faires", 'makerfaire'),
		"edit_item" => __("Edit Yearbook Faire", 'makerfaire'),
		"view_item" => __("View Yearbook Faire", 'makerfaire'),
		"view_items" => __("View Yearbook Faires", 'makerfaire'),
		"add_new" => __("Add New Faire", 'makerfaire'),
		"add_new_item" => __("Add New Yearbook Faire", 'makerfaire'),

		"new_item" => __("New Yearbook Faire",	 'makerfaire'),
		"search_items" => __("Search Yearbook Faire", 'makerfaire'),
		"not_found" => __("No Yearbook Faires found", 'makerfaire'),
		"not_found_in_trash" => __("No Yearbook Faires found in Trash", 'makerfaire'),
		"archives" => __("Yearbook Faire Archives", 'makerfaire'),
		"attributes" => __("Yearbook Faire Attributes", 'makerfaire'),

		"insert_into_item" => __("Insert into Yearbook Faire", 'makerfaire'),
		"uploaded_to_this_item" => __("Uploaded to this Yearbook Faire", 'makerfaire'),
		"filter_items_list" => __("Filter Yearbook Faires list", 'makerfaire'),
		"filter_by_date" => __("Filter Yearbook Faires by date", 'makerfaire'),
		"items_list_navigation" => __("Yearbook Faires list navigation", 'makerfaire'),
		"items_list" => __("Yearbook Faires list", 'makerfaire'),
		"item_published" => __("Yearbook Faire published.", 'makerfaire'),
		"item_published_privately" => __("Yearbook Faire published privately.",  'makerfaire'),
		"item_reverted_to_draft" => __("Yearbook Faire reverted to draft.", 'makerfaire'),
		"item_scheduled" => __("Yearbook Faire scheduled.", 'makerfaire'),
		"item_updated" => __("Yearbook Faire updated.", 'makerfaire'),
		"item_link" => __("Yearbook Faire Link", 'makerfaire'),
		"item_link_description" => __("A link to a Yearbook Faire.",  'makerfaire')
	);
	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes'),
		'taxonomies' => array('regions'),
		'public' => true,
		'menu_icon' => "https:\/\/global.makerfaire.com\/favicon-16x16.png",
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_in_rest' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => false,
		'query_var' => true,
		'can_export' => true,
		'capability_type' => 'post',
		'menu_position' => 25,
		//'rewrite' => false
		'rewrite' => array('slug' => 'yearbook/%faire_year%-faires')
	);

	register_post_type('yb_faires', $args);
}
add_action('init', 'mf_yearbook_custom_post_type');

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

	register_taxonomy('regions', array('yb_faires'), $args);
}

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

	register_taxonomy('countries', array('yb_faires'), $args);
}
add_action('init', 'register_taxonomy_countries');

//set the yoast meta description dynamically if not set manually
function set_yearbook_meta_desc($description) {
	//if the yoast desc is already set, keep that
	if ($description != '') return $description;

	$post_type = get_post_type();
	if ($post_type == 'projects') {
		//Pull faire specific information
		$faireData       = get_field("faire_information");
		$faire_year      = (isset($faireData["faire_year"]) ? $faireData["faire_year"] : '');
		//Pull associated faire post
		if (isset($faireData["faire_post"])) {
			$faire_id   = $faireData["faire_post"];
			$faire_name = get_the_title($faire_id);
		} else {
			$faire_id   = '';
			$faire_name = '';
		}

		//Project Information
		$project_title   = get_the_title();
		return 'Maker Faire ' . $faire_name . " " . $faire_year . ' - ' . $project_title . ' - ' . html_entity_decode(get_field("exhibit_description"));
	} elseif ($post_type == 'yb_faires') {
		$faire_name = get_the_title();
		$faire_id 	= get_the_ID();

		// Dates
		$start_date = get_field("start_date", $faire_id);
		$faire_year = date('Y', strtotime($start_date));

		//faire location		
		$faire_countries 		= ''; //tbd set this
		$faire_country 			= ''; //tbd set this

		$faireInfo 				= get_field('faire_info');
		$faire_num_attendees 	= (isset($faireInfo['number_of_attendees']) ? $faireInfo['number_of_attendees'] : '');
		$faire_num_projects 	= (isset($faireInfo['number_of_attendees']) ? $faireInfo['number_of_projects'] : '');

		return 'Maker Faire ' . $faire_name . ' ' . $faire_year .
			(isset($faire_countries[$faire_country]) ? ' - ' . $faire_countries[$faire_country] : '') .
			($faire_num_projects  != '' ? ' - Projects: ' . number_format($faire_num_projects) : '') .
			($faire_num_attendees != '' ? ' - Participants: ' . number_format($faire_num_attendees) : '');
	}
	return $description;
}
add_filter('wpseo_metadesc', 'set_yearbook_meta_desc');


/* wp-admin changes */

//add columns to the list view
add_filter('manage_yb_faires_posts_columns', 'yb_faires_posts_columns', 999, 1);
function yb_faires_posts_columns($columns) {
	$columns = array(
		'cb' 				=> $columns['cb'],
		'title' 			=> __('Faire'),
		'start_date' 		=> __('Faire Start', 'makerfaire'),
		'faire_region' 		=> __('Region', 'makerfaire'),
		'faire_country' 	=> __('Country', 'makerfaire'),
	);

	return $columns;
}

add_action('manage_yb_faires_posts_custom_column', 'yb_faires_content_column', 10, 2);
function yb_faires_content_column($column, $post_id) {
	// faire column
	switch ($column) {
		case 'start_date':
			$start_date = get_field("start_date", $post_id);
			echo date('m/d/Y', strtotime($start_date));
			break;
		case 'faire_country':
			$faire_country = get_field("country", $post_id);
			echo $faire_country->name;
			break;
		case 'faire_region':
			$faire_region = get_field("region", $post_id);
			echo $faire_region->name;
			break;
	}
}

//add columns to be sortable
add_filter('manage_edit-yb_faires_sortable_columns', 'yb_faires_sortable_columns');
function yb_faires_sortable_columns($columns) {
	$columns['start_date'] 		 = 'start_date';

	//country and region sorting doesn't work as they are taxonomy id's not name
	//$columns['faire_country']  = 'faire_country';
	//$columns['faire_region']	 = 'faire_region';

	return $columns;
}

//tell wordpress how to sort and filter the data
add_action('pre_get_posts', 'mf_yb_faires_admin_data');
function mf_yb_faires_admin_data($query) {
	//only do this in admin
	if (!is_admin() || !$query->is_main_query()) {
		return;
	}
	if ($query->query_vars['post_type'] == 'yb_faires') {
		//check sort parameter
		if ($orderby = $query->get('orderby')) {
			switch ($orderby) {
				case 'start_date':
					$query->set('meta_key', 'start_date');
					$query->set('orderby', 'meta_value');
					break;
				default:
					// do nothing
					break;
			}
		}

		//filter data
		$meta_query    = array();		
		$faire_year    = (isset($_GET['faire_year'])    && $_GET['faire_year']    != '-1' ? $_GET['faire_year']:'');
		$faire_region  = (isset($_GET['faire_region'])  && $_GET['faire_region']  != '-1' ? $_GET['faire_region']:'');
		$faire_country = (isset($_GET['faire_country']) && $_GET['faire_country'] != '-1' ? $_GET['faire_country']:'');

		//faire year
		if ($faire_year != '') {			
			$meta_query[] = array('key' => 'start_date', 'value' => $faire_year, 'compare' => 'like');						
		}

		//faire region
		if ($faire_region != '') {
			$meta_query[] = array('key' => 'region', 'value' => $faire_region, 'compare' => '=');						
		}

		//faire country
		if ($faire_country != '') {			
			$meta_query[] = array( 'key' => 'country', 'value' => $faire_country, 'compare' => '=');						
		}

		//if there is anything to filter, add it to the wp query
		if(!empty($meta_query)){
			$meta_query['relation'] = 'AND';
			$query->query_vars['meta_query'] = $meta_query;			
			error_log(print_r($query->query_vars['meta_query'],TRUE));
		}		
		
	}
}

//remove the creation date filter
function yb_remove_date_filter($months) {
	global $post_type;
	if ($post_type == 'yb_faires' ||  $post_type == 'projects') {
		return array(); // remove the date created vilter
	}
	return $months; // otherwise return the original for other post types
}
add_filter('months_dropdown_results', 'yb_remove_date_filter');

//add filters to the faire wp-admin list page
add_action('restrict_manage_posts', 'add_extra_tablenav');
function add_extra_tablenav($post_type) {
	global $wpdb;

	/** Ensure this is the correct Post Type*/
	if ($post_type !== 'yb_faires')
		return;

	/** Find unique faire year based on faire start date */
	$query = "select distinct year(meta_value) 
	from wp_postmeta 
	left outer join wp_posts on wp_postmeta.post_id = wp_posts.ID 
	 where meta_key in ('start_date') and wp_posts.post_status<>'trash'
	 and meta_value!= ''
	ORDER BY `year(meta_value)` ASC;";
	$results = $wpdb->get_col($query);

	/** Ensure there are years to show */
	if (!empty($results)) {
		$options = array();
		// get selected option if there is one selected
		$selectedYear = (isset($_GET['faire_year']) && $_GET['faire_year'] != '') ? $_GET['faire_year'] : -1;

		/** Grab all of the options that should be shown */
		$options[] = sprintf('<option value="-1">%1$s</option>', __('All Years', 'Makerfaire'));
		foreach ($results as $result) {
			if ($result == $selectedYear) {
				$options[] = sprintf('<option value="%1$s" selected>%2$s</option>', esc_attr($result), $result);
			} else {
				$options[] = sprintf('<option value="%1$s">%2$s</option>', esc_attr($result), $result);
			}
		}

		/** Add the faire year filter */
		echo '<select class="" id="faire_year" name="faire_year">';
		echo join("\n", $options);
		echo '</select>';
	}

	/* Faire Region filter */

	// Find unique faire regions
	$query = "SELECT DISTINCT pm.meta_value as term_id, wp_terms.name 
				FROM wp_postmeta pm 
				LEFT JOIN wp_posts p ON p.ID = pm.post_id 
				left outer join wp_terms on wp_terms.term_id=meta_value 
				WHERE pm.meta_key = 'region' 
				AND p.post_status<>'trash' 
				ORDER BY `wp_terms`.`name` ASC;";

	$results = $wpdb->get_results($query, ARRAY_A);

	//Ensure there are regions to show 
	if (!empty($results)) {
		$options = array();

		// get selected option if there is one selected
		$selectedRegion = (isset($_GET['faire_region']) && $_GET['faire_region'] != '') ? $_GET['faire_region'] : -1;

		/** Grab all of the options that should be shown */
		$options[] = sprintf('<option value="-1">%1$s</option>', __('All Regions', 'Makerfaire'));
		foreach ($results as $result) {
			if ($result['term_id'] == $selectedRegion) {
				$options[] = sprintf('<option value="%1$s" selected>%2$s</option>', esc_attr($result['term_id']), $result['name']);
			} else {
				$options[] = sprintf('<option value="%1$s">%2$s</option>', esc_attr($result['term_id']), $result['name']);
			}
		}

		/** Add the faire region filter */
		echo '<select class="" id="faire_region" name="faire_region">';
		echo join("\n", $options);
		echo '</select>';
	}

	/* Faire Country filter */

	// Find unique faire countries
	$query = "SELECT DISTINCT pm.meta_value as term_id, wp_terms.name 
				FROM wp_postmeta pm 
				LEFT JOIN wp_posts p ON p.ID = pm.post_id 
				left outer join wp_terms on wp_terms.term_id=meta_value 
				WHERE pm.meta_key = 'country' 
				AND p.post_status<>'trash' 
				ORDER BY `wp_terms`.`name` ASC;";

	$results = $wpdb->get_results($query, ARRAY_A);

	//Ensure there are countries to show 
	if (!empty($results)) {
		$options = array();

		// get selected option if there is one selected
		$selectedRegion = (isset($_GET['faire_country']) && $_GET['faire_country'] != '') ? $_GET['faire_country'] : -1;

		/** Grab all of the options that should be shown */
		$options[] = sprintf('<option value="-1">%1$s</option>', __('All Countries', 'Makerfaire'));
		foreach ($results as $result) {
			if ($result['term_id'] == $selectedRegion) {
				$options[] = sprintf('<option value="%1$s" selected>%2$s</option>', esc_attr($result['term_id']), $result['name']);
			} else {
				$options[] = sprintf('<option value="%1$s">%2$s</option>', esc_attr($result['term_id']), $result['name']);
			}
		}

		/** Add the faire country filter */
		echo '<select class="" id="faire_country" name="faire_country">';
		echo join("\n", $options);
		echo '</select>';
	}
}
