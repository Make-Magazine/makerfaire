<?php
//These are functions that need to be called after an entry is updated
add_action('gform_after_update_entry', 'mf_after_gf_update_entry', 10, 3 ); //$form, $entry_id
function mf_after_gf_update_entry( $form, $entry_id, $orig_entry = array() ){    
    
    $entry = GFAPI::get_entry(esc_attr($entry_id));
    $form  = GFAPI::get_form($entry['form_id']);

    //determine if anything has changed in entry, and if so update the change report
    GVupdate_changeRpt($form, $entry_id, $orig_entry); // /functions/gravity_forms/helper.php

    //update maker info tables, process RMT rules
    GFRMTHELPER::gravityforms_makerInfo($entry, $form); // /classes/gf-rmt-helper.php 

    //determines if tasks need to be assigned           
    processTasks( $entry, $form); // /functions/gravity_forms/tasks.php
    
    //update the expoFP exhibitor
    update_expofp_exhibitor($form, $entry_id); // /functions/gravity_forms/expofp.php

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