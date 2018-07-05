<?php
/* check if logged in user has access to view this entry
    If the user has access to this entry return false
       This will override the GV logic to verify user */
function check_entry_display( $check_entry_display, $entry ) {
  global $current_user; global $wpdb;
  //check if entry was created by logged on user and if they have the correct role set
  if ( current_user_can( 'mat_view_created_entries') ) {
    if($entry['created_by']==$current_user->ID) return false;
  }

  //check if current users email
  require_once( get_template_directory().'/models/maker.php' );

  //instantiate the model
  $maker   = new maker($current_user->user_email);
  $query = "SELECT count(*)
            FROM   wp_mf_maker_to_entity
            left  outer join wp_mf_entity
                  on wp_mf_entity.lead_id = entity_id
            WHERE maker_id ='".$maker->maker_id."'
            AND   wp_mf_maker_to_entity.entity_id = ".$entry['id']."
            AND   status != 'trash'";
  $count = $wpdb->get_var($query);
  if($count > 0) return false;
  return $check_entry_display;
}
add_filter('gravityview/common/get_entry/check_entry_display', 'check_entry_display', 10, 2 );

/* add query var for MAT to edit the entry */
add_filter( 'query_vars', 'mat_register_query_var' );
function mat_register_query_var( $vars ) {
    $vars[] = 'edit_slug';
    return $vars;
}

/* makersign Rewrite Rules */
add_action('init', 'makersign_rewrite_rules');
function makersign_rewrite_rules() {
  add_rewrite_rule( '^maker-sign/([^/]*)/([^/]*)$', '/wp-content/themes/makerfaire/fpdi/makersigns.php?eid=$matches[1]&faire=$matches[2]', 'top' );
}

//hide the Approve/Reject Entry column in entry list
add_filter('gravityview/approve_entries/hide-if-no-connections', '__return_true');

//bypass the nonce verification on grvity view edit entry
add_filter('gravityview/edit_entry/verify_nonce', '__return_true');