<?php
//These are functions that need to be called after a new entry is submitted
add_action( 'gform_after_submission', 'mf_after_gf_submission', 10, 2 );
add_action('gform_post_add_entry', 'mf_after_gf_submission', 10, 2 ); 

function mf_after_gf_submission( $entry, $form ){
    create_makeco_user( $entry, $form ); // /functions/gravity_forms/create_makeco_user.php

    //process supplemental form logic	
	update_original_data($entry, $form); // /functions/gravity_forms/supplemental_forms.php

    //update maker info tables, process RMT rules
    GFRMTHELPER::gravityforms_makerInfo($entry, $form, 'new'); // /classes/gf-rmt-helper.php
    
    //determines if tasks need to be assigned 
	processTasks( $entry, $form); // /functions/gravity_forms/tasks.php
    
    //determine if tasks need to be completed
    maybeCompleteTasks ($entry, $form); // /functions/gravity_forms/tasks.php
		
    //create an exhibitor in ExpoFP based off the entry data
    create_expofp_exhibitor( $entry, $form ); // /functions/gravity_forms/expofp.php
    return;
}
