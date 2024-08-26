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
if($webhook->Type == "booth_reserved") {
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

    $result = postCurl($url, $headers, json_encode($data), "POST");

    $exhibitorID = json_decode($result)->exhibitors[0]; // technically there can be more than one exhibitor assigned to a booth, but we aren't using booths like that

    $entry = gform_get_entry_byMeta("expofp_exhibit_id", $exhibitorID);
    error_log(print_r($entry, TRUE));

}