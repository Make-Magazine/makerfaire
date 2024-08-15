<?php
//These are functions that need to be called after a new entry is submitted
add_action( 'gform_after_submission', 'mf_after_gf_submission', 10, 2 );
add_action('gform_post_add_entry', 'mf_after_gf_submission', 10, 2 ); 

function mf_after_gf_submission( $entry, $form ){
    create_makeco_user( $entry, $form );    

    //process supplemental form logic	
	update_original_data($entry, $form);

    //update maker info tables, process RMT rules
    GFRMTHELPER::gravityforms_makerInfo($entry, $form, 'new');
    
    //determines if tasks need to be assigned 
	processTasks( $entry, $form);
    
    //determine if tasks need to be completed
    maybeCompleteTasks ($entry, $form);
		
    create_expofp_exhibitor( $entry, $form );
    return;
}
