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

function custom_rewrite_tag() {  
  add_rewrite_tag('%faire_id%', '([^&]+)');  
  add_rewrite_tag('%entryslug%', '([^&]+)');  //page-entryarchives.php
}

add_action('init', 'custom_rewrite_tag', 10, 0);