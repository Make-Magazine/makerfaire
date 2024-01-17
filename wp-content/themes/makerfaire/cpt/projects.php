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
		"all_items"=> __(  "Projects", 'makerfaire' ),
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
		'taxonomies' => array( 'mf-project-cat','mf-year-cat', 'regions'),
		'public' => true,
		'menu_icon' => "https:\/\/global.makerfaire.com\/favicon-16x16.png",
		'show_ui' => true,
		//'show_in_menu' => 'edit.php?post_type=event',
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
		'rewrite' => false	
	);

	register_post_type( 'projects', $args );

}

add_action( 'init', 'register_taxonomy_projects_cpt' );
function register_taxonomy_projects_cpt() {
	//Add the Project Category
	register_taxonomy( 'mf-project-cat', array('projects'), 
		array(
			'labels' => array(
				'name' => __( 'Project Category', 'makerfaire' ),
				'singular_name' => __( 'Project Category', 'makerfaire' ),
				'search_items' => __( 'Project Categories', 'makerfaire' ),		
				'all_items' => __( 'All Project Categories', 'makerfaire' ),
				'parent_item' => __( 'Parent Project Category', 'makerfaire' ),
				'parent_item_colon' => __( 'Parent Project Category:', 'makerfaire' ),
				'edit_item' => __( 'Edit Project Category', 'makerfaire' ),
				'update_item' => __( 'Update Project Category', 'makerfaire' ),
				'add_new_item' => __( 'Add New Project Category', 'makerfaire' ),
				'new_item_name' => __( 'New Project Category', 'makerfaire' ),
				'separate_items_with_commas' => __( 'Separate Project categories with commas', 'makerfaire' ),
				'add_or_remove_items' => __( 'Add or remove Project Categories', 'makerfaire' ),
				'choose_from_most_used' => __( 'Choose from most used Project Categories', 'makerfaire' ),
				'menu_name' => __( 'Project Categories', 'makerfaire' ),
			),
			'public' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => 'edit.php?post_type=event',			
			'hierarchical' => true,
			//'rewrite' => array( 'slug' => 'project-categories', 'with_front' => false ),
			'query_var' => true,
			'show_in_rest' => true, 						
			'show_admin_column' => true				
			
		)
	);

	//Faire Year 
	register_taxonomy( 'mf-year-tax', array('projects'), 
		array(
			'labels' => array(
				'name' => __( 'Faire Year', 'makerfaire' ),
				'singular_name' => __( 'Faire Year', 'makerfaire' ),
				'search_items' => __( 'Faire Years', 'makerfaire' ),		
				'all_items' => __( 'All Faire Years', 'makerfaire' ),		
				'edit_item' => __( 'Edit Faire Year', 'makerfaire' ),
				'update_item' => __( 'Update Faire Year', 'makerfaire' ),
				'add_new_item' => __( 'Add New Faire Year', 'makerfaire' ),
				'new_item_name' => __( 'New Faire Year', 'makerfaire' ),		
				'menu_name' => __( 'Years', 'makerfaire' )
			),	
			'public' => true,						
			'hierarchical' => false,	
			'show_in_nav_menus' => false,
			'show_in_menu' => false,				
			'query_var' => true, 						
			'show_admin_column' => true			
		)		
	);	
}

//add columns to the list view
add_filter( 'manage_projects_posts_columns', 'projects_posts_columns',999,1 );
function projects_posts_columns( $columns ) {
	$columns = array(
		'cb' => $columns['cb'],
		'title' => __( 'Title' ),		
		'exhibit_photo' => __( 'Photo', 'makerfaire' ),
		'taxonomy-mf-project-cat' => __( 'Project Categories', 'makerfaire' ),
		'faire_name' => __( 'Faire', 'makerfaire' ),
		//'faire_year' => __( 'Faire Year', 'makerfaire' ),					
		'faire_region'	 => __( 'Region', 'makerfaire' ),	
		'faire_country'	 => __( 'Country', 'makerfaire' ),	
		'first_maker_name'	 => __( 'Maker', 'makerfaire' ),	
	  );

  return $columns;
}

add_action( 'manage_projects_posts_custom_column', 'projects_content_column', 10, 2);
function projects_content_column( $column, $post_id ) {
	$faireData = get_field("faire_information", $post_id);
	$project_location = get_field("project_location", $post_id);	
	$maker_data = get_field("maker_data");			
    
	// faire column
	switch ($column){
		case 'exhibit_photo':
			echo get_the_post_thumbnail( $post_id, array(80, 80) );
			break;
		case 'faire_name':			
			echo (isset($faireData["faire_post"]->post_title)?$faireData["faire_post"]->post_title:'');						
			break;
		case 'faire_year':					
			echo (isset($faireData["faire_year"]) ? $faireData["faire_year"] : '');			
			break;	
		case 'faire_country':					
			echo (isset($project_location["country"]) ? $project_location["country"] : '');			
			break;	
		case 'first_maker_name':
			//they only want the first maker name
			echo (!empty($maker_data) && isset($maker_data[0]["maker_or_group_name"]) ? $maker_data[0]["maker_or_group_name"] : '');			
			break;	
		case 'faire_region':			
			if($project_location["region"]){				
				echo (isset($project_location["region"]->name)?$project_location["region"]->name:'');						
			}
			
			break;	
	}

}

//add columns to be sortable
add_filter( 'manage_edit-projects_sortable_columns', 'projects_sortable_columns');
function projects_sortable_columns( $columns ) {
  $columns['faire_name'] 	= 'faire_name';  
  $columns['faire_year'] 	= 'faire_year';
  $columns['faire_country'] = 'faire_country';
  return $columns;
}

//tell wordpress how to sort the acf data
add_action( 'pre_get_posts', 'mf_projects_admin_orderby' );
function mf_projects_admin_orderby( $query ) {
  if( ! is_admin() || ! $query->is_main_query() ) {
    return;
  }

  if ( 'faire_year' === $query->get( 'orderby') ) {
    $query->set( 'orderby', 'meta_value' );
    $query->set( 'meta_key', 'faire_information_faire_year' );
  }
}
