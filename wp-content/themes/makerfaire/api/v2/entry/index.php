<?php
/**
 * v2 of the Maker Faire API
 *    This API is used to update info on the entry
 * @version 2.0
 */

// Stop any direct calls to this file
defined('ABSPATH') or die('This file cannot be called directly!');

$token   = (!empty($_REQUEST['token']) ? sanitize_text_field($_REQUEST['token']) : null);
$return  = '';
// Double check again we have requested this file
if ($type == 'entry') {
  if(is_null($token)){
    $return = 'Token is Required';
  }else{    
    //get entry from token
    $entryID = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT entry_id FROM wp_gf_entry_meta WHERE `meta_key` = '%s' AND `meta_value` = '%s'",
        'fg_easypassthrough_token',
        $token
      )
    ); 
    if($entryID){
      $header_msg = "Thank you for confirming your participation.";
      gform_update_meta( $entryID, "mf_confirmed", 'yes');
      $load_in_time = (!empty($_REQUEST['load_in_time']) ? sanitize_text_field($_REQUEST['load_in_time']) : null);
      if(!empty($load_in_time)) {
        if(empty(gform_get_meta($entryID, "load_in_time"))) {
          gform_update_meta( $entryID, "load_in_time", $load_in_time);
          $header_msg = "Thank you for confirming you'll be loading in " . $load_in_time;
        } else {
          $header_msg = "You have already confirmed your load in day.";
        }
      } 
      // update a meta for their load in time
      $return = '<h2>' . $header_msg . '</h2><h3>Please respond to your Confirmation Email or reach out to <a href="mailto:makers@make.co">makers@make.co</a> with any questions about your setup.<br /><br />See you at the Faire!</h3><br />';  
    }else{
      $return = 'Invalid Token';  
    }
    
  }
  // Output the JSON
  echo $return;
  
  //exit;
}
