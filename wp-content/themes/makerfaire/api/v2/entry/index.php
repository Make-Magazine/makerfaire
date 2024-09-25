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
      gform_update_meta( $entryID, "mf_confirmed", 'yes');
      $return = '<h2>Thank you for confirming your participation.</h2><h3>Please respond to your Confirmation Email or reach out to <a href="mailto:makers@make.co">makers@make.co</a> with any questions about your setup.<br /><br />See you at the Faire!</h3><br />';  
    }else{
      $return = 'Invalid Token';  
    }
    
  }
  // Output the JSON
  echo $return;
  
  //exit;
}
