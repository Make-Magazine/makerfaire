<?php

function makerfaire_category_init() {
	register_taxonomy( 'makerfaire_category', array( 'post', 'page' ), array(
		'hierarchical'            => true,
		'public'                  => true,
		'show_in_nav_menus'       => true,
		'show_ui'                 => true,
		'query_var'               => 'makerfaire_category',
		'rewrite'                 => true,
		'capabilities'            => array(
			'manage_terms'  => 'edit_posts',
			'edit_terms'    => 'edit_posts',
			'delete_terms'  => 'edit_posts',
			'assign_terms'  => 'edit_posts'
		),
		'labels'                  => array(
			'name'                       =>  __( 'makerfaire categories', 'makerfaire' ),
			'singular_name'              =>  __( 'makerfaire category', 'makerfaire' ),
			'search_items'               =>  __( 'Search makerfaire categories', 'makerfaire' ),
			'popular_items'              =>  __( 'Popular makerfaire categories', 'makerfaire' ),
			'all_items'                  =>  __( 'All makerfaire categories', 'makerfaire' ),
			'parent_item'                =>  __( 'Parent makerfaire category', 'makerfaire' ),
			'parent_item_colon'          =>  __( 'Parent makerfaire category:', 'makerfaire' ),
			'edit_item'                  =>  __( 'Edit makerfaire category', 'makerfaire' ),
			'update_item'                =>  __( 'Update makerfaire category', 'makerfaire' ),
			'add_new_item'               =>  __( 'New makerfaire category', 'makerfaire' ),
			'new_item_name'              =>  __( 'New makerfaire category', 'makerfaire' ),
			'separate_items_with_commas' =>  __( 'makerfaire categories separated by comma', 'makerfaire' ),
			'add_or_remove_items'        =>  __( 'Add or remove makerfaire categories', 'makerfaire' ),
			'choose_from_most_used'      =>  __( 'Choose from the most used makerfaire categories', 'makerfaire' ),
			'menu_name'                  =>  __( 'MF Categories', 'makerfaire' ),
		),
	) );

}
add_action( 'init', 'makerfaire_category_init' );

//prelim locations
function prelim_loc_init() {
  //      Bay Area
	register_taxonomy( 'ba_prelim_loc', array( 'post', 'page' ), array(
		'hierarchical'            => false,
		'show_in_nav_menus'       => true,
		'show_ui'                 => true,
		'query_var'               => 'ba_prelim_loc',
		'capabilities'            => array(
			'manage_terms'  => 'edit_posts',
			'edit_terms'    => 'edit_posts',
			'delete_terms'  => 'edit_posts',
			'assign_terms'  => 'edit_posts'
		),
		'labels'                  => array(
			'name'                       =>  __( 'Bay Area Preliminary Locations', 'makerfaire' ),
			'singular_name'              =>  __( 'Bay Area Preliminary Location', 'makerfaire' ),
			'search_items'               =>  __( 'Search Locations', 'makerfaire' ),
			'popular_items'              =>  __( 'Popular Bay Area Preliminary Locations', 'makerfaire' ),
			'all_items'                  =>  __( 'All Bay Area Preliminary Locations', 'makerfaire' ),
			'edit_item'                  =>  __( 'Edit Bay Area Preliminary Location', 'makerfaire' ),
			'update_item'                =>  __( 'Update Bay Area Preliminary Location', 'makerfaire' ),
			'add_new_item'               =>  __( 'Add Preliminary Location', 'makerfaire' ),
			'new_item_name'              =>  __( 'New Bay Area Preliminary Location', 'makerfaire' ),
			'separate_items_with_commas' =>  __( 'Bay Area Preliminary Locations separated by comma', 'makerfaire' ),
			'add_or_remove_items'        =>  __( 'Add or remove Bay Area Preliminary Locations', 'makerfaire' ),
			'choose_from_most_used'      =>  __( 'Choose from the most used BA Preliminary Locations', 'makerfaire' ),
			'menu_name'                  =>  __( 'BA Prelim Locs', 'makerfaire' ),
		),
	) );

  //        New York
	register_taxonomy( 'ny_prelim_loc', array( 'post', 'page' ), array(
		'hierarchical'            => false,
		'show_in_nav_menus'       => true,
		'show_ui'                 => true,
		'query_var'               => 'ny_prelim_loc',
		'capabilities'            => array(
			'manage_terms'  => 'edit_posts',
			'edit_terms'    => 'edit_posts',
			'delete_terms'  => 'edit_posts',
			'assign_terms'  => 'edit_posts'
		),
		'labels'                  => array(
			'name'                       =>  __( 'New York Preliminary Locations', 'makerfaire' ),
			'singular_name'              =>  __( 'New York Preliminary Location', 'makerfaire' ),
			'search_items'               =>  __( 'Search Locations', 'makerfaire' ),
			'popular_items'              =>  __( 'Popular Locations', 'makerfaire' ),
			'all_items'                  =>  __( 'All New York Preliminary Locations', 'makerfaire' ),
			'edit_item'                  =>  __( 'Edit New York Preliminary Location', 'makerfaire' ),
			'update_item'                =>  __( 'Update New York Preliminary Location', 'makerfaire' ),
			'add_new_item'               =>  __( 'Add Preliminary Location', 'makerfaire' ),
			'new_item_name'              =>  __( 'New New York Preliminary Location', 'makerfaire' ),
			'separate_items_with_commas' =>  __( 'New York Preliminary Locations separated by comma', 'makerfaire' ),
			'add_or_remove_items'        =>  __( 'Add or remove New York Preliminary Locations', 'makerfaire' ),
			'choose_from_most_used'      =>  __( 'Choose from the most used NY Preliminary Locations', 'makerfaire' ),
			'menu_name'                  =>  __( 'NY Prelim Locs', 'makerfaire' ),
		),
	) );
}
add_action( 'init', 'prelim_loc_init' );

function get_CPT_name($category){
  $cat_id = (int) $category;
  if($cat_id!=0){
    $typeArr = array('makerfaire_category','ny_prelim_loc','ba_prelim_loc');

    foreach($typeArr as $type){
      $cat = get_term( $cat_id, $type );
      if ($cat && !is_wp_error( $cat ) )
        return $cat->name;
    }
  }
  return $category;
}