<?php
/* Rewrite Rules */
function maker_url_vars($rules) {
  $newrules = array();

  //classic entry page for flagship faires
  $newrules['maker/entry/(\d*)/?(.*)$/?'] = 'index.php?post_type=page&pagename=entry-page-do-not-delete&e_id=$matches[1]&edit_slug=$matches[2]';
  
  //classic schedule page
  $newrules['([^\/]*)/schedule/([^/]+)/?$'] = 'index.php?pagename=$matches[1]/schedule&sched_dow=$matches[2]';
  $newrules['([^\/]*)/schedule/([^/]+)/([^/]+)/?$'] = 'index.php?pagename=$matches[1]/schedule&sched_dow=$matches[2]&sched_type=$matches[3]';
  
  //create maker signs
  $newrules['maker-sign/(\d*)/?(.*)$/?'] = 'index.php?makersign=true&eid=$matches[1]&faire=$matches[2]';
  $newrules['^maker-sign/([^/]*)/([^/]*)$'] = '/wp-content/themes/makerfaire/generate_pdf/makersigns.php?eid=$matches[1]&faire=$matches[2]';        

  //create maker load in pass
  $newrules['loadin/(\d*)/?(.*)$/?'] = 'index.php?loadin=true&eid=$matches[1]&type=$matches[2]';
  $newrules['^loadin/([^/]*)/([^/]*)$'] = '/wp-content/themes/makerfaire/generate_pdf/loadInPass.php?eid=$matches[1]&type=$matches[2]';        
  
  //kendo scheduler - page-mfscheduler.php
  $newrules['^mfscheduler/([^/]*)/?'] = 'index.php?pagename=mfscheduler&faire_id=$matches[1]';
  $newrules['^mfscheduler-tasks/?'] = 'index.php?pagename=mfscheduler-tasks';

  return $newrules + $rules;
}

add_filter('rewrite_rules_array', 'maker_url_vars');

function mf_add_rewrite_rules( $rules ) {
  $new = array();
  $new['yearbook/([^/]+)-projects/(.+)/?$'] = 'index.php?faire_year=$matches[1]&projects=$matches[2]';  
  $new['yearbook/([^/]+)-faires/(.+)/?$']   = 'index.php?faire_year=$matches[1]&yb_faires=$matches[2]';  
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
function mf_filter_post_type_link( $post_link, $id=0 ) {
  $post = get_post( $id );
  if ( $post->post_type == 'projects' ) {
    $faireData = get_field("faire_information", $post->ID);				
    $faire_year = (isset($faireData["faire_year"]) ? $faireData["faire_year"] : '');
    return str_replace( '%faire_year%', $faire_year, $post_link );    
  }elseif ( $post->post_type == 'yb_faires' ) {    
    $start_date = get_field("start_date", $post->ID);			
    $faire_year = date('Y', strtotime($start_date));	 
    
    return str_replace( '%faire_year%', $faire_year, $post_link );    
  }
  return $post_link;
}
add_filter( 'post_type_link', 'mf_filter_post_type_link', 10, 2 );


/* Query Vars */
add_filter( 'query_vars', 'makerfaire_register_query_var' );
function makerfaire_register_query_var( $vars ) {
    $vars[] = 'type';       //page-api.php, page-mfapi.php
    $vars[] = 'e_id';       //page-entry.php
    $vars[] = 'edit_slug';  //page-entry.php    
    $vars[] = 'faire_id';   //page-mfscheduler.php
    $vars[] = 'token';      //page-maker-checkin.php, page-mfscheduler.php, page-onsite-checkin.php, page-onsite-pinning.php
    $vars[] = 'makersign';  //classes/makerfaire-helper.php
    $vars[] = 'loadin';  //classes/makerfaire-helper.php
    $vars[] = 'faire';      //generate_pdf/makersigns.php
    $vars[] = 'eid';        //generate_pdf/makersigns.php
    $vars[] = "sched_type"; //page-schedule.php
        
    return $vars;
}

//rewrite yearbook projects CPT
function custom_rewrite_tag() {
  global $wp_rewrite;

  //change the CPT structure of yearbook projects cpt to include the faire year
  $projects_structure = 'yearbook/%faire_year%-projects/%projects%';
  $wp_rewrite->add_rewrite_tag("%projects%", '([^/]+)', "project=");
  $wp_rewrite->add_permastruct('projects', $projects_structure, false);

  add_rewrite_tag('%faire_id%', '([^&]+)');  
  add_rewrite_tag('%entryslug%', '([^&]+)');  //page-entryarchives.php
}

add_action('init', 'custom_rewrite_tag', 10, 0);