<?php
//Define the Project Custom Post Type
add_action( 'init', 'register_cpt_projects');

//Register the projects custom post type
function register_cpt_projects() {
	//define labels
	$labels = array(			
		"name"=> __(  "Projects",  'makerfaire' ),		
		'singular_name' => __('Project', 'makerfaire'),

		"menu_name"=> __(  "Projects",  'makerfaire' ),
		"all_items"=> __(  "All Projects", 'makerfaire' ),
		"edit_item"=> __(  "Edit Project", 'makerfaire' ),
		"view_item"=> __(  "View Project", 'makerfaire' ),
		"view_items"=> __(  "View Projects", 'makerfaire' ),
		"add_new_item"=> __(  "Add New Project", 'makerfaire' ),
		
		"new_item"=> __(  "New Project",	 'makerfaire' ),		
		"search_items"=> __(  "Search Project", 'makerfaire' ),
		"not_found"=> __(  "No Projects found", 'makerfaire' ),
		"not_found_in_trash"=> __(  "No Projects found in Trash", 'makerfaire' ),
		"archives"=> __(  "Project Archives", 'makerfaire' ),
		"attributes"=> __(  "Project Attributes", 'makerfaire' ),
		
		"insert_into_item"=> __(  "Insert into project", 'makerfaire' ),
		"uploaded_to_this_item"=> __(  "Uploaded to this project", 'makerfaire' ),
		"filter_items_list"=> __(  "Filter projects list", 'makerfaire' ),
		"filter_by_date"=> __(  "Filter projects by date", 'makerfaire' ),
		"items_list_navigation"=> __(  "Projects list navigation", 'makerfaire' ),
		"items_list"=> __(  "Projects list", 'makerfaire' ),
		"item_published"=> __(  "Project published.", 'makerfaire' ),
		"item_published_privately"=> __(  "Project published privately.",  'makerfaire' ),
		"item_reverted_to_draft"=> __(  "Project reverted to draft.", 'makerfaire' ),
		"item_scheduled"=> __(  "Project scheduled.", 'makerfaire' ),
		"item_updated"=> __(  "Project updated.", 'makerfaire' ),
		"item_link"=> __(  "Project Link", 'makerfaire' ),
		"item_link_description"=> __(  "A link to a project.",  'makerfaire' ) 
	);
	
	$args = array(
		'labels' => $labels,
		'hierarchical' => true,		
		'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'trackbacks', 'custom-fields','page-attributes'),
		'taxonomies' => array( 'mf-global-category'),
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
		'menu_position' => 40,
		'rewrite' => false	
	);

	register_post_type( 'projects', $args );

}


//Add the MF Global Categories taxonomy
add_action( 'init', 'register_taxonomy_mf_global_categories' );
function register_taxonomy_mf_global_categories() {

	$labels = array(
		'name' => __( 'MF Global Categories', 'makerfaire' ),
		'singular_name' => __( 'MF Global Category', 'makerfaire' ),
		'search_items' => __( 'MF Global Categories', 'makerfaire' ),
		'popular_items' => __( 'MF Global Content Categories', 'makerfaire' ),
		'all_items' => __( 'All MF Global Categories', 'makerfaire' ),
		'parent_item' => __( 'Parent MF Global Category', 'makerfaire' ),
		'parent_item_colon' => __( 'Parent MF Global Category:', 'makerfaire' ),
		'edit_item' => __( 'Edit MF Global Category', 'makerfaire' ),
		'update_item' => __( 'Update MF Global Category', 'makerfaire' ),
		'add_new_item' => __( 'Add New MF Global Category', 'makerfaire' ),
		'new_item_name' => __( 'New MF Global Category', 'makerfaire' ),
		'separate_items_with_commas' => __( 'Separate MF Global categories with commas', 'makerfaire' ),
		'add_or_remove_items' => __( 'Add or remove MF Global Categories', 'makerfaire' ),
		'choose_from_most_used' => __( 'Choose from most used MF Global Categories', 'makerfaire' ),
		'menu_name' => __( 'MF Global Categories', 'makerfaire' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => true,
		'query_var' => true,
		'show_in_rest' => true
	);

	register_taxonomy( 'mf-global-category', array('project'), $args );
}

//add columns to the list view
add_filter( 'manage_projects_posts_columns', 'projects_posts_columns',999,1 );
function projects_posts_columns( $columns ) {
	$columns = array(
		'cb' => $columns['cb'],
		'title' => __( 'Title' ),
		'exhibit_photo' => __( 'Photo', 'makerfaire' ),
		'faire_name' => __( 'Faire', 'makerfaire' ),
		'faire_year' => __( 'Faire Year', 'makerfaire' ),		
	  );

  return $columns;
}

add_action( 'manage_projects_posts_custom_column', 'projects_content_column', 10, 2);
function projects_content_column( $column, $post_id ) {
	$faireData = get_field("faire_information", $post_id);				
    
	// faire column
	switch ($column){
		case 'exhibit_photo':
			echo get_the_post_thumbnail( $post_id, array(80, 80) );
			break;
		case 'faire_name':
			echo $faireData["faire_name"];
			break;
		case 'faire_year':
			echo $faireData["faire_year"];
			break;	
	}

}

//add columns to be sortable
add_filter( 'manage_edit-projects_sortable_columns', 'projects_sortable_columns');
function projects_sortable_columns( $columns ) {
  $columns['faire_name'] = 'faire_name';
  $columns['faire_year'] = 'faire_year';
  return $columns;
}

//tell wordpress how to find the acf data
add_action( 'pre_get_posts', 'smashing_posts_orderby' );
function smashing_posts_orderby( $query ) {
  if( ! is_admin() || ! $query->is_main_query() ) {
    return;
  }

  if ( 'faire_name' === $query->get( 'orderby') ) {
    $query->set( 'orderby', 'meta_value' );
    $query->set( 'meta_key', 'faire_information_faire_name' );
  }

  if ( 'faire_year' === $query->get( 'orderby') ) {
    $query->set( 'orderby', 'meta_value' );
    $query->set( 'meta_key', 'faire_information_faire_year' );
  }
}