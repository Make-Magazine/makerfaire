<?php
/** Trigger certain functionality when editing fields */
add_filter( 'gravityview-inline-edit/entry-updated', 'gravityedit_custom_trigger_notifications', 10, 5 );

//need to trigger the change status functionality when using inline edit that is normally triggered using the sidebar
function gravityedit_custom_trigger_notifications( $update_result, $entry = array(), $form_id = 0, $gf_field = null, $original_entry = array() ) { 
    if($gf_field->id=='303'){
        $_POST['entry_info_status_change'] = $entry['303'];
        $entry = GFAPI::get_entry( $entry['id'] );
	    $form = GFAPI::get_form( $form_id  );

        //need to pass the original entry object so correct functionality gets triggered.
        set_entry_status($original_entry,$form);        
    }    
}