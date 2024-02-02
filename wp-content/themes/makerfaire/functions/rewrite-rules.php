<?php
// BEGINING AMAZING HACKS
function maker_url_vars($rules) {
  $newrules = array();

  //classic entry page for flagship faires
  $newrules['maker/entry/(\d*)/?(.*)$/?'] = 'index.php?post_type=page&pagename=entry-page-do-not-delete&e_id=$matches[1]&edit_slug=$matches[2]';
   
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
  $new['yearbook/([^/]+)-projects/(.+)/?$'] = 'index.php?faire_year=$matches[1]&projects=$matches[2]';  
  $new['yearbook/([^/]+)-faires/(.+)/?$']   = 'index.php?faire_year=$matches[1]&event=$matches[2]';  
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
    $faire_year = (isset($faireData["faire_year"]) ? $faireData["faire_year"] : '');
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
    $vars[] = 'type';       //page-api.php, page-mfapi.php
    $vars[] = 'e_id';       //page-entry.php
    $vars[] = 'edit_slug';  //page-entry.php    
    $vars[] = 'faire_id';   //page-mfscheduler.php
    $vars[] = 'token';      //page-mfscheduler.php
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
  $projects_structure = 'yearbook/%faire_year%-projects/%projects%';
  $wp_rewrite->add_rewrite_tag("%projects%", '([^/]+)', "project=");
  $wp_rewrite->add_permastruct('projects', $projects_structure, false);

  add_rewrite_tag('%faire_id%', '([^&]+)');  
  add_rewrite_tag('%entryslug%', '([^&]+)');  //page-entryarchives.php
  add_rewrite_tag('%entryid%', '([^&]+)');    //classes/mf-gf-exhibit-view.php
  add_rewrite_tag('%makerfaire%', '([^&]+)'); //classes/mf-gf-exhibit-view.php
}

add_action('init', 'custom_rewrite_tag', 10, 0);