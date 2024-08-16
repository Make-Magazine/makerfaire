<?php
//These are functions that need to be called after an entry is updated
add_action('gform_after_update_entry', 'mf_after_gf_update_entry', 10, 3 ); //$form, $entry_id
function mf_after_gf_update_entry( $form, $entry_id, $orig_entry = array() ){    
    error_log('triggered after update entry for entry '.$entry_id);

    $entry = GFAPI::get_entry(esc_attr($entry_id));
    $form = GFAPI::get_form($entry['form_id']);

    //update maker info tables, process RMT rules
    GVupdate_changeRpt($form, $entry_id, $orig_entry);

    //update maker info tables, process RMT rules
    $entry = GFRMTHELPER::gravityforms_makerInfo($entry, $form);        

    //determines if tasks need to be assigned           
    processTasks( $entry, $form);
    
    //update the expoFP exhibitor
    update_expofp_exhibitor($form, $entry_id);
}