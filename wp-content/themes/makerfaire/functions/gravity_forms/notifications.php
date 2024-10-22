<?php
//add new Notification event of - send confirmation letter and maker cancelled exhibit
add_filter( 'gform_notification_events', 'add_event' );
function add_event( $notification_events ) {
    $notification_events['confirmation_letter']   = __( 'Confirmation Letter', 'gravityforms' );
    $notification_events['maker_cancel_exhibit']  = __( 'Maker Cancelled Exhibit', 'gravityforms' );
    $notification_events['maker_delete_exhibit']  = __( 'Maker Deleted Exhibit', 'gravityforms' );   
    $notification_events['manual']                = __( 'Send Manually', 'gravityforms' );
    $notification_events['mf_acceptance_status_changed'] = __( 'Acceptance Status Changed', 'gravityforms' );
    $notification_events['mf_entry_changed']      = __( 'Entry Updated', 'gravityforms' );
    return $notification_events;
}

add_filter( 'gform_notification', 'set_resource_status', 10, 3 );
function set_resource_status( $notification, $form, $entry ) {  
  if(isset($notification['event'])) {
    if($notification['event']=='confirmation_letter'){
      $entry_id = $entry['id'];
      
      //lock all resources for this entry
      $entry_resources = GFRMTHELPER::rmt_get_entry_data($entry_id, $type = 'resources');
      foreach($entry_resources['resources'] as $resource){        
        GFRMTHELPER::rmt_set_lock_ind(1, $resource['id'], 'resource');
      }
      
      //set lead meta field res_status to sent
      gform_update_meta( $entry_id, 'res_status','sent' );
    }
  }
  return $notification;
}

//trigger update notification after gravityview update 
add_action( 'gravityview/edit_entry/after_update', 'mf_entry_change_notification', 10, 3 );

function mf_entry_change_notification( $form, $entry_id, $object = null ) {
  $entry = GFAPI::get_entry($entry_id);
  //send notification of changes    
  $notifications_to_send = GFCommon::get_notifications_to_send('mf_entry_changed', $form, $entry);
  foreach ($notifications_to_send as $notification) {
    // The isActive paramater is not always set. 
    // If it's not set, assume the notification is turned on
    if (
      !isset($notification['isActive']) ||
      (isset($notification['isActive']) && $notification['isActive'])
    ) {
      GFCommon::send_notification($notification, $form, $entry);
    }
  }
}