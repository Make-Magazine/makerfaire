<?php

/**
 *
 * This is as a webhook for reading changes to ExpoFP
 *
 * @version 2.0
 *
 */

// Stop any direct calls to this file
defined('ABSPATH') or die('This file cannot be called directly!');

$body = file_get_contents('php://input');
$webhook = json_decode($body);

// we don't want to run calls on every webhook type, so we will confine it to just when booths are reserved (assigned and saved)
if(isset($webhook->Type)) {
    global $wpdb;
    // unfortunately, booth_reserved always seems to happen before booth_unassigned, therefore we need to save between deleting a booth and assigning it to a new place
    if($webhook->Type == "booth_reserved") {
        
        $result = getExpoFPWebhookResult($webhook);

        $exhibitorID  = $result->exhibitors[0]; // technically there can be more than one exhibitor assigned to a booth, but we aren't using booths like that
        $type         = explode('|', $result->type); // this gets the subarea id we placed after the pipe in the expoFP booth types
        $subarea_id   = (is_array($type) && isset($type[1])) ? $type[1] : "";
        $booth_name   = $result->name;
        // we've already stored the expofp exhibit id in the gf entry when the exhibit was first created in expofp
        $entry_id     = gform_get_entry_byMeta("expofp_exhibit_id", $exhibitorID);
        $space_size   = $result->size;

        // update the wp_mf_location table for this entry with the subarea and booth name we have had returned from expofp
        if($subarea_id != "") {
            $insert_query = "INSERT INTO `wp_mf_location`(`entry_id`, `subarea_id`, `location`) "
            . " VALUES ($entry_id,$subarea_id,'$booth_name')";
        
            $wpdb->query($insert_query);

            // set a gf_entry_meta for the boothname, so the unassign function can find out those booth details to delete
            $booth_data     = gform_get_meta( $entry_id, 'expofp_booth_name');
            $booth_array    = json_decode($booth_data, TRUE);
            if(!is_array($booth_array)) {
                $booth_array = array();
            }
            $booth_array[]  = array('booth_id' => $webhook->BoothId, 'booth_key' => $webhook->BoothKey);
            gform_update_meta( $entry_id, "expofp_booth_name", json_encode($booth_array));

            // and then we update the final space size attribute
            GFRMTHELPER::rmt_update_attribute($entry_id, 2, $space_size, "", "ExpoFP");
        } else {
            //error_log("Exhibit was placed without boothtype set - Entry ID: " . $entry_id);
        }
    } 
    if($webhook->Type == "booth_unassigned") {
        $exhibitorID  = $webhook->ExhibitorId;
        $entry_id     = gform_get_entry_byMeta("expofp_exhibit_id", $exhibitorID);
        $booth_data   = gform_get_meta( $entry_id, 'expofp_booth_name');
        $booth_array  = json_decode($booth_data, TRUE);
        $booth_key    = "";
        // check what booth data we have stored in the gform meta and remove any entries that match the id of the booth we are unassigning
        if(is_array($booth_array)){
            foreach($booth_array as $key => $booth) {
                if($booth['booth_id'] == $webhook->BoothId) {
                    $booth_key = $booth['booth_key'];
                    unset($booth_array[$key]);
                    break;
                }
            }
        }

        if($booth_key != "") {
            $expofpToken = EXPOFP_TOKEN;
            $url = "https://app.expofp.com/api/v1/get-booth";
            $headers = array(
                'Accept: application/json', 
                'Content-Type: application/json'
            );

            $data = [
                "token" => $expofpToken,
                "eventId" => $webhook->ExpoId,
                "name" => $booth_key,
            ];

            $result = json_decode(postCurl($url, $headers, json_encode($data), "POST"));
            $subarea_id = strlen($result->type) ? explode('|', $result->type)[1] : ""; // this gets the area id we placed after the pipe in the expoFP booth types

            //delete from schedule and location table
            if(!empty($subarea_id) && $subarea_id != "") {
                $delete_query =  "DELETE FROM `wp_mf_location` WHERE wp_mf_location.entry_id = $entry_id AND wp_mf_location.subarea_id = $subarea_id AND wp_mf_location.location = '$booth_key'";
            }
            $wpdb->get_results($delete_query);

            gform_update_meta( $entry_id, "expofp_booth_name", json_encode($booth_array));

            // delete the attribute from the entry that matches the final space size
            $attArray = GFRMTHELPER::rmt_get_entry_data($entry_id, 'attributes') ; 
            $rowID = '';
            foreach($attArray['attributes'] as $attribute){
                if($attribute['attribute_id']==2){
                    $rowID = $attribute['id'];
                    break;
                }
            }
            // only straight delete the attribute if there is nothing available to replace
            if($rowID!='' && empty($booth_array)){
                GFRMTHELPER::rmt_delete($rowID, "wp_rmt_entry_attributes", $entry_id);
            }

        } else {
            // this should only go off the first time
            //error_log("Error in expoFP booth_unassigned");
        }
    }
}
if(isset($webhook->type)) { // because of course they don't use consistent capitalization
    if($webhook->type == "exhibitor_upserted") {
        /* this doesn't go off at all when assigning or changing booths anyways
        error_log("exhibitor changed");
        error_log(print_r($webhook, TRUE));
        */
    }
}

function getExpoFPWebhookResult($webhook){
    // get the Expo FP token
    $expofpToken = EXPOFP_TOKEN;
    $url = "https://app.expofp.com/api/v1/get-booth";
    $headers = array(
        'Accept: application/json', 
        'Content-Type: application/json'
    );

    $data = [
        "token" => $expofpToken,
        "eventId" => $webhook->ExpoId,
        "name" => $webhook->BoothKey,
    ];

    return json_decode(postCurl($url, $headers, json_encode($data), "POST"));
}