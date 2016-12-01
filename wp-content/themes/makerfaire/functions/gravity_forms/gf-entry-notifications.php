<?php
/**
* Gravity Wiz // Add Custom Notification Event to Gravity Forms
* http://gravitywiz.com
*/
add_filter( 'gform_notification_events', 'mf_custom_notification_event' );
function mf_custom_notification_event( $events ) {
// update 'My Custom Notification Event' to the user-friendly name of your notification event
// update 'my_custom_notification_event' to match your user-friendly name (it is a good practice to lowercase it and replace spaces with underscores)
$events['mf_acceptance_status_changed'] = __( 'Acceptance Status Changed' );
return $events;
} 

