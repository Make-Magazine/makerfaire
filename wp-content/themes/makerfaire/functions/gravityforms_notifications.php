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
