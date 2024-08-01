<?php

add_action( 'gform_after_submission', 'create_expofp_exhibitor', 10, 2 );
function create_expofp_exhibitor( $entry, $form ) {
    //error_log(print_r($entry, true));
    $expofpId = $form['expofp_event_id'];
    $expofpToken = EXPOFP_TOKEN;

    $result = createExpoFpExhibit($entry, $form, $expofpToken, $expofpId);
    $exhibitor_id = json_decode($result)->id;

    // Now add the featured image to the exhibitor id on ExpoFP
    $image = json_decode($entry['22'])[0];
    updateExpoFpImage($expofpToken, $exhibitor_id, $image);
}

add_action('gform_after_update_entry', 'update_expofp_exhibitor', 10, 2 ); //$form, $entry_id
function update_expofp_exhibitor($form, $entry_id) {
    //need to set $entry and reset $form as gravity view removes admin only fields
    $entry = GFAPI::get_entry(esc_attr($entry_id));
    // get the Expo FP token
    $expofpToken = EXPOFP_TOKEN;
    $exhibitor_id = gform_get_meta( $entry_id, 'expofp_exhibit_id');
    if(isset($exhibitor_id)) { // this meta is set when the 
        // after update, if the status is canceled or rejected, delete
        error_log($entry['303']);
        if($entry['303'] == "Cancelled" || $entry['303'] == "Rejected") {
            deleteExpoFpExhibit($exhibitor_id, $expofpToken);
        } else { // otherwise, update exhibitor with new title, description, image, etc
            updateExpoFpExhibit($entry, $form, $expofpToken, $exhibitor_id);
            $image = json_decode($entry['22'])[0];
            updateExpoFpImage($expofpToken, $exhibitor_id, $image);
        }
    } else {
        // it should have already created the exhibitor in ExpoFP when the entry was submitted, but we can catch it here if not
        $expofpId = $form['expofp_event_id'];
        createExpoFpExhibit($entry, $form, $expofpToken, $expofpId);
    }
}

function createExpoFpExhibit($entry, $form, $expofpToken, $expofpId) {
    $url = "https://app.expofp.com/api/v1/add-exhibitor";
    $headers = array(
        'Accept: application/json', 
        'Content-Type: application/json'
    );

    //check if exhibitor is a sponsor
    $featured = false;
    $formType = $form['form_type'];
    $tags = array();

    if ($formType == "Master") {
        foreach ($entry as $key => $value) {
            if (strpos($key, '339.') === 0) {
                if ($value != '') {
                    if (stripos($value, 'sponsor') !== false) {
                        $featured = true;
                        array_push($tags, "Sponsor");
                    }
                }
            }
        }
    } else { // otherwise the exhibit type is just the form type
        if (stripos($formType, 'sponsor') == true) {
            $featured = true;
            array_push($tags, "Sponsor");
        } 
    }

    $data = [
        "token" => $expofpToken,
        "eventId" => $expofpId,
        "name" => $entry['151'],
        "description" => $entry['16'],
        "featured" => $featured,
        "address" => "1 Main Ave.", // this will hold the category list as a comma delimited strings
        "website" => "https://makerfaire.com/maker/entry/" . $entry['id'], // this will be the maker/entry page
        "adminNotes" => "", // should we include this?
        "externalId" => $entry['id'],
        "categories" => array(
            array(
                "name" => "10 x 10"
            )
        ),
        "tags" => $tags/*, meta data does what
        "metadata" => array(
            array(
                "key" => "metaKey",
                "value" => "metaValue"
            )
        )*/
    ];

    //error_log(print_r(json_encode($data), TRUE));
    $result = postCurl($url, $headers, json_encode($data), "POST");
    $exhibitor_id = json_decode($result)->id;
    // set the gf_entry_meta to the exhibitor id. if this meta is present, we know we should update rather than add to ExpoFP
    gform_update_meta( $entry['id'], "expofp_exhibit_id", $exhibitor_id, $form['id']);

    return $result;
}

function updateExpoFpExhibit($entry, $form, $expofpToken, $exhibitor_id) {
    $url = "https://app.expofp.com/api/v1/update-exhibitor";
    $headers = array(
        'Accept: application/json', 
        'Content-Type: application/json'
    );

    //check if exhibitor is a sponsor
    $featured = false;
    $formType = $form['form_type'];
    $tags = array();

    if ($formType == "Master") {
        foreach ($entry as $key => $value) {
            if (strpos($key, '339.') === 0) {
                if ($value != '') {
                    if (stripos($value, 'sponsor') !== false) {
                        $featured = true;
                        array_push($tags, "Sponsor");
                    }
                }
            }
        }
    } else { // otherwise the exhibit type is just the form type
        if (stripos($formType, 'sponsor') == true) {
            $featured = true;
            array_push($tags, "Sponsor");
        } 
    }

    $data = [
        "token" => $expofpToken,
        "id" => $exhibitor_id,
        "name" => $entry['151'],
        "description" => $entry['16'],
        "featured" => $featured,
        //"address" => "1 Main Ave.", // this will hold the category list as a comma delimited strings
        "website" => "https://makerfaire.com/maker/entry/" . $entry['id'], // this will be the maker/entry page
        "adminNotes" => "", // should we include this?
        "categories" => array(
            array(
                "name" => "10 x 10"
            )
        ),
        "tags" => $tags
    ];

    //error_log(print_r(json_encode($data), TRUE));
    $result = postCurl($url, $headers, json_encode($data), "POST");

    return $result;
}

function deleteExpoFpExhibit($exhibitor_id, $expofpToken) {
    $url = "https://app.expofp.com/api/v1/delete-exhibitor";
    $headers = array(
        'Accept: application/json', 
        'Content-Type: application/json'
    );
    $data = [
        "token" => $expofpToken,
        "id" => $exhibitor_id
    ];
    postCurl($url, $headers, json_encode($data), "POST");
}

function updateExpoFpImage($expofpToken, $exhibitor_id, $image) {
    $image_string = 'Token='.$expofpToken.'&exhibitorId='.$exhibitor_id.'&ImgUrl='.urlencode($image);
    $image_headers = array(
        'Content-Type: application/x-www-form-urlencoded'
    );
    postCurl("https://app.expofp.com/api/v1/set-exhibitor-leading-image", $image_headers, $image_string, "POST");
}