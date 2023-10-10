<?php
include 'db_connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$form = GFAPI::get_form('260');
$lead = GFAPI::get_entry(74782);
$notification_object = '';

//Handle notifications for acceptance
$notifications_to_send = GFCommon::get_notifications_to_send( 'manual', $form, $lead );
foreach ( $notifications_to_send as $notification ) {            
  // The isActive paramater is not always set. 
  // If it's not set, assume the notification is turned on
  if(!isset($notification['isActive']) || 
    (isset($notification['isActive']) && $notification['isActive'])){     
        if($notification["id"]=="65246cce829d9"){
            $notification_object = $notification;
            break;
        }                       
  }
}
if($notification_object!=''){
    $entry_array = array(74296, 73895, 73973, 74565, 74187, 74384, 74905);
    foreach($entry_array as $entry){
        echo 'sending notification for '.$entry.'<br/>';
        $lead = GFAPI::get_entry($entry);
        GFCommon::send_notification( $notification_object, $form, $lead );
    }    
}