<?php
/*
/* Used in maker portal, this function is called by ajax to allow a user to cancel
 * an entry and to send a notification */
function makerCancelEntry(){
  global $wpdb;
  $entryID = (isset($_POST['cancel_entry_id']) ? $_POST['cancel_entry_id']:0);
  $reason  = (isset($_POST['cancel_reason'])   ? $_POST['cancel_reason']  :'');
  if($entryID!=0){
    //get entry data and form data
    $lead = GFAPI::get_entry(esc_attr($entryID));
    $form = GFAPI::get_form( $lead['form_id']);

    //Update Status to Cancelled
    mf_update_entry_field($entryID,'303','Cancelled');

    //Make a note of the cancellation
    $cancelText = "The Exhibit has been cancelled by the maker.  Reason given is: ".stripslashes($reason);
    mf_add_note($entryID,$cancelText);

    //update maker and entity table
    GFRMTHELPER::updateMakerTables($entryID); //update maker table information

    //Handle notifications for acceptance
    $notifications_to_send = GFCommon::get_notifications_to_send( 'maker_cancel_exhibit', $form, $lead );
    foreach ( $notifications_to_send as $notification ) {
      if($notification['isActive']){
          GFCommon::send_notification( $notification, $form, $lead );
      }
    }

    //kk now check if this is a master entry
    if($form['form_type']=='Master'){
      //find the initial entry and cancel that too
      $initial_entry_id = $wpdb->get_var('SELECT entry_id FROM wp_gf_entry_meta where meta_value='.$entryID.' and meta_key="master_entry_id" limit 1');      

      if($initial_entry_id != ''){
        //Update Status to Cancelled
        mf_update_entry_field($initial_entry_id,'303','Cancelled');

        //Make a note of the cancellation
        $cancelText = "The Exhibit has been cancelled by the maker.  Reason given is: ".stripslashes($reason);
        mf_add_note($initial_entry_id,$cancelText);
      }      
    }
    echo 'Thank You, Exhibit ID '.$entryID.' has been cancelled';
    gf_do_action( array( 'gform_after_update_entry', $lead['form_id']), $form, $entryID, $lead );
  }else{
    echo 'Error in cancelling this exhibit.';
  }

  exit;
}
add_action( 'wp_ajax_maker-cancel-entry', 'makerCancelEntry' );


//disable gravity view cache
add_filter('gravityview_use_cache', '__return_false');