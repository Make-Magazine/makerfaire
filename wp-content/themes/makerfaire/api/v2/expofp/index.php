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
    if($webhook->Type == "booth_reserved") {
        $result = getExpoFPWebhookResult($webhook);
        //error_log(print_r($result, TRUE));

        $exhibitorID  = $result->exhibitors[0]; // technically there can be more than one exhibitor assigned to a booth, but we aren't using booths like that
        $area_number  = explode('|', $result->type)[1]; // this gets the area id we placed after the pipe in the expoFP booth types
        $booth_name   = $result->name;
        // we've already stored the expofp exhibit id in the gf entry when the exhibit was first created in expofp
        $entry_id     = gform_get_entry_byMeta("expofp_exhibit_id", $exhibitorID);
        $space_size   = $result->size;

        // update the wp_mf_location table for this entry with the subarea and booth name we have had returned from expofp
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $insert_query = "INSERT INTO `wp_mf_location`(`entry_id`, `subarea_id`, `location`) "
        . " VALUES ($entry_id,$area_number,'$booth_name')";
        //MySqli Insert Query
        $insert_row = $mysqli->query($insert_query);

        // and then we update the final space size attribute
        GFRMTHELPER::rmt_update_attribute($entry_id, 2, $space_size, "", "ExpoFP");

    }
    if($webhook->Type == "booth_unassigned") {
        error_log("we are removing the location info");
        global $wpdb;

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($mysqli->connect_errno) {
            error_log("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
        }

        error_log(print_r($webhook, TRUE));
        $exhibitorID  = $webhook->exhibitorId;
        error_log($exhibitorID);
        $entry_id     = gform_get_entry_byMeta("expofp_exhibit_id", $exhibitorID);

        //delete from schedule and location table
        $delete_query =  "DELETE FROM `wp_mf_location` WHERE wp_mf_location.entry_id = $entry_id";
        $wpdb->get_results($delete_query);
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