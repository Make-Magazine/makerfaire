<?php
// BEGINING AMAZING HACKS
function maker_url_vars($rules) {
  $newrules = array();

  //classic entry page for flagship faires
  $newrules['maker/entry/(\d*)/?(.*)$/?'] = 'index.php?post_type=page&pagename=entry-page-do-not-delete&e_id=$matches[1]&edit_slug=$matches[2]';
  
  //old meet the makers page - page are marked private and don't work
  //$newrules['([^\/]*)/meet-the-makers/topics/([^\/]*)/([0-9]{1,})/?$'] = 'index.php?post_type=page&pagename=listing-page-do-not-delete&f=$matches[1]&t_slug=$matches[2]&offset=$matches[3]';
  //$newrules['([^\/]*)/meet-the-makers/topics/([^\/]*)/?$'] = 'index.php?offset=1&post_type=page&pagename=listing-page-do-not-delete&f=$matches[1]&t_slug=$matches[2]';
  //$newrules['([^\/]*)/meet-the-makers/search/([0-9]{1,})/?$'] = 'index.php?post_type=page&pagename=search-results-page-do-not-delete&f=$matches[1]&offset=$matches[2]';  
  //$newrules['([^\/]*)/meet-the-makers/search/?$'] = 'index.php?offset=1&post_type=page&pagename=search-results-page-do-not-delete&f=$matches[1]';
  
  //classic schedule page
  $newrules['([^\/]*)/schedule/([^/]+)/?$'] = 'index.php?pagename=$matches[1]/schedule&sched_dow=$matches[2]';
  $newrules['([^\/]*)/schedule/([^/]+)/([^/]+)/?$'] = 'index.php?pagename=$matches[1]/schedule&sched_dow=$matches[2]&sched_type=$matches[3]';
  
  //create maker signs
  $newrules['maker-sign/(\d*)/?(.*)$/?'] = 'index.php?makersign=true&eid=$matches[1]&faire=$matches[2]';
  $newrules['^maker-sign/([^/]*)/([^/]*)$'] = '/wp-content/themes/makerfaire/fpdi/makersigns.php?eid=$matches[1]&faire=$matches[2]';        

  //kendo scheduler - page-mfscheduler.php
  $newrules['^mfscheduler/([^/]*)/?'] = 'index.php?pagename=mfscheduler&faire_id=$matches[1]';
  $newrules['^mfscheduler-tasks/?'] = 'index.php?pagename=mfscheduler-tasks';

  return $newrules + $rules;
}

add_filter('rewrite_rules_array', 'maker_url_vars');

function mf_add_rewrite_rules( $rules ) {
  $new = array();
  $new['([^/]+)/projects/(.+)/?$'] = 'index.php?faire_year=$matches[1]&projects=$matches[2]';  
  $new['([^/]+)/faires/(.+)/?$']   = 'index.php?faire_year=$matches[1]&event=$matches[2]';  
  return array_merge( $new, $rules ); // Ensure our rules come first
}
add_filter( 'rewrite_rules_array', 'mf_add_rewrite_rules' );

/**
 * Handle the '%faire_year%' URL placeholder
 *
 * @param str $link The link to the post
 * @param WP_Post object $post The post object
 * @return str
 */
function mf_filter_post_type_link( $link, $post ) {
  if ( $post->post_type == 'projects' ) {
    $faireData = get_field("faire_information", $post->ID);				
    $faire_year = (isset($faireData["faire_year"]->name)?$faireData["faire_year"]->name:'');
    $link = str_replace( '%faire_year%', $faire_year, $link );    
  }elseif ( $post->post_type == 'event' ) {    
    $event_start_date = get_post_meta( $post->ID, '_event_start_date', true );
    $faire_year = date('Y', strtotime($event_start_date));            
    $link = str_replace( '%faire_year%', $faire_year, $link );    
  }
  return $link;
}
add_filter( 'post_type_link', 'mf_filter_post_type_link', 10, 2 );

/* Rewrite Rules */
//add_action('init', 'makerfaire_rewrite_rules');
function makerfaire_rewrite_rules() {
  //old makerfaire check in process
  //add_rewrite_rule( 'onsitecheckin/?([^/]*)', 'index.php?onsitecheckin=true&token=$matches[1]', 'top' );
  //add_rewrite_rule( 'processonsitecheckin/?([^/]*)', 'index.php?processonsitecheckin=true&token=$matches[1]', 'top' );
  //add_rewrite_rule( 'onsitepinning/?([^/]*)', 'index.php?onsitepinning=true&token=$matches[1]', 'top' );
  //add_rewrite_rule( 'processonsitepinning/?([^/]*)', 'index.php?processpinning=true&token=$matches[1]', 'top' );
  
  //page does not exist
  //add_rewrite_rule('^mf/([^/]*)/([^/]*)/?', 'index.php?pagename=maker-faire-gravity-forms-display-page&makerfaire=$matches[1]&entryid=$matches[2]', 'top');
  //old faire pages - linked only from ribbons.php - page is marked as private
  //add_rewrite_rule('^mfarchives/([^/]*)/?', 'index.php?pagename=entry-archives&entryslug=$matches[1]', 'top');    
}

/*
  add_action( 'wp_loaded','my_flush_rules' );

  // flush_rules() if our rules are not yet included
  function my_flush_rules(){
  $rules = get_option( 'rewrite_rules' );

  if ( ! isset( $rules['maker/entry/(\d*)/?'] ) || ! isset( $rules['([^/]*)/meet-the-makers/topics/([^/]*)/?'] )) {
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
  }

  } */
// END AMAZING HACKS


/* Query Vars */
add_filter( 'query_vars', 'makerfaire_register_query_var' );
function makerfaire_register_query_var( $vars ) {
    //$vars[] = 'processonsitecheckin'; //classes/makerfaire-helper.php
    //$vars[] = 'onsitecheckin';        //classes/makerfaire-helper.php
    //$vars[] = 'processonsitepinning'; //classes/makerfaire-helper.php
    //$vars[] = 'onsitepinning';        //classes/makerfaire-helper.php
    //$vars[] = 'makerid';
    //$vars[] = "sched_dow";    
    //$vars[] = 'api';
    //$vars[] = 's_keyword'; //page-makers-bykeyword.php    
    //$vars[] = 't_slug';     //page-makers-bytopic.php
    //$vars[] = 'offset';     //page-makers-bykeyword.php, page-makers-bytopic.php
    //$vars[] = 'f';          //page-makers-bykeyword.php, page-makers-bytopic.php

    $vars[] = 'type';       //page-api.php, page-mfapi.php
    $vars[] = 'e_id';       //page-entry.php
    $vars[] = 'edit_slug';  //page-entry.php    
    $vars[] = 'faire_id';   //page-mfscheduler.php
    $vars[] = 'token';      //page-maker-checkin.php, page-mfscheduler.php, page-onsite-checkin.php, page-onsite-pinning.php
    $vars[] = 'makersign'; //classes/makerfaire-helper.php
    $vars[] = 'faire';  //fpdi/makersigns.php
    $vars[] = 'eid';    //fpdi/makersigns.php
    $vars[] = "sched_type"; //page-schedule.php
        
    return $vars;
}

function custom_rewrite_tag() {
  // rewrites custom post type name
  global $wp_rewrite;

  //change the CPT structure of projects cpt to include the faire year
  $projects_structure = '%faire_year%/projects/%projects%';
  $wp_rewrite->add_rewrite_tag("%projects%", '([^/]+)', "project=");
  $wp_rewrite->add_permastruct('projects', $projects_structure, false);

  add_rewrite_tag('%faire_id%', '([^&]+)');  
  add_rewrite_tag('%entryslug%', '([^&]+)');  //page-entryarchives.php
  add_rewrite_tag('%entryid%', '([^&]+)');    //classes/mf-gf-exhibit-view.php
  add_rewrite_tag('%makerfaire%', '([^&]+)'); //classes/mf-gf-exhibit-view.php
}

add_action('init', 'custom_rewrite_tag', 10, 0);