<?php
/*
 * this script will hold all the cronjobs for makerfaire
 */

/*
 * Cron process to create MAT records for specific forms
 */
//add_action('cron_update_mfTables', 'update_mfTables',10,3);
function update_mfTables($form, $limit = 0, $start = 0) {
    error_log('updating maker tables for form ' . $form . ' (' . $start . ', ' . $limit . ')');

    global $wpdb;
    $sql = "Select id
            from wp_gf_entry
           where form_id  = $form "
        . " ORDER BY `wp_gf_entry`.`id` ASC ";
    if ($limit != "0") {
        $sql .= " limit " . $start . ', ' . $limit;
    }
    $results = $wpdb->get_results($sql);
    foreach ($results as $row) {
        error_log('processing ' . $row->id);
        //update maker table information
        GFRMTHELPER::updateMakerTables($row->id);
    }
    error_log('end updating maker tables for form ' . $form . ' (' . $start . ', ' . $limit . ')');
}

/* This cron job is triggered daily.
 * It looks for accepted records on current faires and checks if tickets have been created for them.
    If they have not, it will start the process to request new tickets.
 */
//add_action('cron_eb_ticketing', 'cron_genEBtickets');
function cron_genEBtickets() {
    global $wpdb;
    $sql =  "SELECT           entry_id "
        . "FROM             wp_mf_faire, wp_gf_entry_meta "
        . "LEFT OUTER JOIN  eb_entry_access_code ON wp_gf_entry_meta.entry_id = eb_entry_access_code.entry_id "
        . "WHERE  wp_gf_entry_meta.meta_key ='303' and wp_gf_entry_meta.meta_value='Accepted' "
        . "  AND end_dt > now() "
        . "  AND FIND_IN_SET (wp_gf_entry_meta.form_id,wp_mf_faire.form_ids)> 0 "
        . "  AND eb_entry_access_code.EBticket_id is NULL "
        . "  AND (select EB_event_id from eb_event where wp_mf_faire_id = wp_mf_faire.id limit 1) is not NULL"
        . "  AND wp_gf_entry_meta.form_id != 120 ";

    $results = $wpdb->get_results($sql);
    foreach ($results as $entry) {
        echo 'Creating ticket codes for ' . $entry->entry_id . '<br/>';
        $response = genEBtickets($entry->entry_id);
        if (isset($response['msg']))
            echo 'Ticket Response - ' . $response['msg'] . '<br/>';
    }
}

add_action('cron_expofp_sync', 'cron_expofp_sync');
function cron_expofp_sync($expoID = '') {
    global $wpdb;
    $chgReport = array();

    if (!defined('EXPOFP_TOKEN')) {
        return 'Error in cron_expofp_sync. EXPOFP_TOKEN is not set';
    }

    $expofpToken = EXPOFP_TOKEN;
    $url = "https://app.expofp.com/api/v1/list-exhibitors";
    $headers = array(
        'Accept: application/json',
        'Content-Type: application/json'
    );

    $data = [
        "token" => $expofpToken,
        "eventId" => $expoID
    ];

    $written = 0;
    // perform an API call to List all exhibitors on expoFP for the specific event id
    $exhibitors = json_decode(postCurl($url, $headers, json_encode($data), "POST"), TRUE);
    foreach ($exhibitors as $exhibitor) {
        //find the entryID        
        $entry_id = (isset($exhibitor['externalId'])&&$exhibitor['externalId']!=''?(int) $exhibitor['externalId']:'');
        if($entry_id==''){
            return 'Error in cron_expofp_sync. externalId is not set for exhibitior id='.$exhibitor['id']. ' name='.$exhibitor['name']; 
        }

        //if this was already placed from expoFP, save the current value
        $loc_sql = "SELECT subarea_id, location, ".
                        "concat(COALESCE(wp_mf_faire_subarea.subarea,''), ' (', COALESCE(location,''),')') as combined ".
                    "FROM `wp_mf_location` ".
                    "left outer join wp_mf_schedule on wp_mf_schedule.location_id= wp_mf_location.id ".
                    "left outer join wp_mf_faire_subarea on wp_mf_faire_subarea.ID=subarea_id ".
                    "where start_dt is NULL and wp_mf_location.entry_id=$entry_id ".
                    "ORDER BY wp_mf_location.entry_id;";
        $loc_results = $wpdb->get_results($loc_sql,ARRAY_A);
        $prev_value = implode(", ", array_column($loc_results, 'combined')); //for change report
        $prev_subareas = array_column($loc_results, 'subarea_id');         
        $prev_booths   = array_column($loc_results, 'location');         
           
        //delete any locations for this entry
        $delete_sql = "delete FROM `wp_mf_location` where (select start_dt from wp_mf_schedule where wp_mf_schedule.location_id= wp_mf_location.id) is NULL and wp_mf_location.entry_id=$entry_id";
        $wpdb->query($delete_sql);
               
        //check if a booth has been assigned to this entry
        if (isset($exhibitor['booths']) && !empty($exhibitor['booths'])) {      
            ++$written;         
            $booth_count = 0;        
            foreach ($exhibitor['booths'] as $booth_name) {        
                ++$booth_count;        
                //call expoFP to get booth details
                $url = "https://app.expofp.com/api/v1/get-booth";
                $headers = array(
                    'Accept: application/json',
                    'Content-Type: application/json'
                );

                $data = [
                    "token" => $expofpToken,
                    "eventId" => $expoID,
                    "name" => $booth_name
                ];

                // perform an API call to List all exhibitors on expoFP for the specific event id
                $booth_details  = json_decode(postCurl($url, $headers, json_encode($data), "POST"), TRUE);                
                $space_size     = (isset($booth_details['size'])?$booth_details['size']:'');
                $type           = (isset($booth_details['type'])?explode('|', $booth_details['type']):''); // this gets the subarea id we placed after the pipe in the expoFP booth types
                $subarea_id     = (is_array($type) && isset($type[1])) ? $type[1] : "";
                
                //subarea id found?
                if ($subarea_id != "") {                                                            
                    //find the form information
                    $entry = gfapi::get_entry($entry_id);
                    if(!is_array($entry)){
                        echo 'error in pulling entry information for '.$entry_id.'<br/>';
                        continue;
                    }
                    $form_id = (isset($entry['form_id'])?$entry['form_id']:0);
                                        
                    //set the location information for this entry with the subarea and booth name from expofp
                    $insert_query = "INSERT INTO `wp_mf_location`(`entry_id`, `subarea_id`, `location`) "
                                . " VALUES ($entry_id,$subarea_id,'$booth_name')";
                    $wpdb->query($insert_query);
                
                    // set a gf_entry_meta for expoFP placed
                    gform_update_meta( $entry_id, "expofp_placed",'Placed', $form_id);

                    // and then we update the final space size attribute
                    GFRMTHELPER::rmt_update_attribute($entry_id, 2, $space_size, "", "ExpoFP");
                    

                    //if this location/subarea was not previously set, make a note in the change report
                    if(!in_array($subarea_id,$prev_subareas) || !in_array($booth_name,$prev_booths)){   

                        echo "Moved " .$entry_id. " from " . $prev_value . " to " . explode("|", $booth_details['type'])[0] . " (" . $booth_name . ")<br/>";                        
                        
                        //set change report data                                                 
                        $chgReport[] = array(
                            'user_id'           => 0, //expoFP
                            'lead_id'           => $entry_id,
                            'form_id'           => 0,
                            'field_id'          => 0,
                            'field_before'      => addslashes($prev_value), 
                            'field_after'       => addslashes(explode("|", $booth_details['type'])[0] . " (" . $booth_name . ")"),
                            'fieldLabel'        => addslashes('ExpoFP Placement/Change'),
                            'status_at_update'  => '');      
                    }                    
                } else {
                    echo "Exhibit was placed without subarea set - Entry ID: " . $entry_id.'<br/>';
                    //error_log("Exhibit was placed without boothtype set - Entry ID: " . $entry_id);
                }                             
            }
            if($booth_count> 1){
                echo 'multiple booths set - Entry ID: ' . $entry_id.'</br>';
            }
        } else {                    
            //update meta field  "expofp_placed" to blank                        
            gform_delete_meta( $entry_id,'expofp_placed');            
        }                    
                        
    }

    //After you have processed all entries, call this function to update the change rpt
    if(!empty($chgReport)){        
        updateChangeRPT($chgReport);                    
    }  

    echo '<br/>';
    echo count($exhibitors) .' exhibitors received from ExpoFP<br/>';
    echo $written .' placed';
}
