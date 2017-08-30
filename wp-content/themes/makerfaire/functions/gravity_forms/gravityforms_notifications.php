<?php


//add new Notification event of - send confirmation letter and maker cancelled exhibit
add_filter( 'gform_notification_events', 'add_event' );
function add_event( $notification_events ) {
    $notification_events['confirmation_letter']   = __( 'Confirmation Letter', 'gravityforms' );
    $notification_events['maker_cancel_exhibit']  = __( 'Maker Cancelled Exhibit', 'gravityforms' );
    $notification_events['maker_delete_exhibit']  = __( 'Maker Deleted Exhibit', 'gravityforms' );
    $notification_events['maker_updated_exhibit'] = __( 'Maker Updated Entry', 'gravityforms' );
    $notification_events['manual']                = __( 'Send Manually', 'gravityforms' );
    return $notification_events;
}

add_filter( 'gform_notification', 'set_resource_status', 10, 3 );
function set_resource_status( $notification, $form, $entry ) {
  //error_log( 'notification event is '.$notification['event']);
  if($notification['event']=='confirmation_letter'){
    $entry_id = $entry['id'];
    //error_log( 'updating entry id '.$entry_id. ' resource status to sent' );

    //set lead meta field res_status to sent
    gform_update_meta( $entry_id, 'res_status','sent' );
  }
  return $notification;
}