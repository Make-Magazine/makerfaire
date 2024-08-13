<?php

add_action( 'gform_after_submission', 'create_expofp_exhibitor', 999, 2 );
function create_expofp_exhibitor( $entry, $form ) {
    $expofpId = isset($form['expofp_event_id']) ? $form['expofp_event_id'] : "";
    if($expofpId == "") { 
        return;
    }
    // only create the exhibit if the type is Exhibit, Sponsor, StartUp Sponsor, Show Management or ‘Not Sure Yet’
    $write_to_expofp = false;
    foreach ($entry as $key => $value) {
        if (strpos($key, '339.') === 0) {
            if ($value != '') {
                if (str_contains(strtolower($value), 'exhibit') == true || str_contains(strtolower($value), 'sponsor') == true || str_contains(strtolower($value), 'show') == true || str_contains(strtolower($value), 'not sure') == true) {
                    $write_to_expofp = true;
                }
            }
        }
    }
    if($write_to_expofp == true){
        //error_log(print_r($entry, true));
        $expofpToken = EXPOFP_TOKEN;
        $result = createExpoFpExhibit($entry, $form, $expofpToken, $expofpId);
        $exhibitor_id = json_decode($result)->id;

        // Now add the featured image to the exhibitor id on ExpoFP
        $image = json_decode($entry['22'])[0];
        updateExpoFpImage($expofpToken, $exhibitor_id, $image);
    }
}

add_action('gform_after_update_entry', 'update_expofp_exhibitor', 10, 2 ); //$form, $entry_id
function update_expofp_exhibitor($form, $entry_id) {
    $expofpId = isset($form['expofp_event_id']) ? $form['expofp_event_id'] : "";
    if($expofpId == "") { 
        return;
    }
    //need to set $entry and reset $form as gravity view removes admin only fields
    $entry = GFAPI::get_entry(esc_attr($entry_id));

    // only create the exhibit if the type is Exhibit, Sponsor, StartUp Sponsor, Show Management or ‘Not Sure Yet’
    $write_to_expofp = false;
    foreach ($entry as $key => $value) {
        if (strpos($key, '339.') === 0) {
            if ($value != '') {
                if (str_contains(strtolower($value), 'exhibit') == true || str_contains(strtolower($value), 'sponsor') == true || str_contains(strtolower($value), 'show') == true || str_contains(strtolower($value), 'not sure') == true) {
                    $write_to_expofp = true;
                }
            }
        }
    }
    if($write_to_expofp == true){
        // get the Expo FP token
        $expofpToken = EXPOFP_TOKEN;
        $exhibitor_id = gform_get_meta( $entry_id, 'expofp_exhibit_id');

        if(isset($exhibitor_id)) { // this meta is set when the exhibit is created
            // after update, if the status is canceled or rejected, delete
            //error_log($entry['303']);
            if($entry['303'] == "Cancelled" || $entry['303'] == "Rejected") {
                deleteExpoFpExhibit($exhibitor_id, $expofpToken);
            } else { // otherwise, update exhibitor with new title, description, image, etc
                updateExpoFpExhibit($entry, $form, $expofpToken, $expofpId, $exhibitor_id);
                $image = json_decode($entry['22'])[0];
                updateExpoFpImage($expofpToken, $exhibitor_id, $image);
            }
        } else {
            // it should have already created the exhibitor in ExpoFP when the entry was submitted, but we can catch it here if not
            createExpoFpExhibit($entry, $form, $expofpToken, $expofpId);
        }
    }
}

function createExpoFpExhibit($entry, $form, $expofpToken, $expofpId) {
    $url = "https://app.expofp.com/api/v1/add-exhibitor";
    $headers = array(
        'Accept: application/json', 
        'Content-Type: application/json'
    );

    //check if exhibitor is a sponsor
    $featured   = false;
    $formType   = $form['form_type'];
    $tags       = array();
    $categories = array();
    $rmt_data   = GFRMTHELPER::rmt_get_entry_data($entry['id']);
    $res_arr    = array();
    $attr_arr   = array();
    foreach($rmt_data['resources'] as $resource) {
        $categories[] = array("name" => $resource['token']);
        $res_arr[] = $resource['token'] . ":" . $resource['qty'];
    }
    foreach($rmt_data['attributes'] as $attribute) {
        $attr_arr[] = $attribute['token'] . ":" . $attribute['value'];
        $tags[] = $attribute['token'] . ":" . $attribute['value'];
    }
    $rmt_shown  = implode(', ', array_merge($res_arr, $attr_arr));

    if ($formType == "Master") {
        foreach ($entry as $key => $value) {
            if (strpos($key, '339.') === 0) {
                if ($value != '') {
                    if (stripos($value, 'sponsor') !== false) {
                        $featured = true;
                        array_push($tags, "Sponsor");
                    }
                    $categories[] = array("name" => $value);
                }
            }
        }
    } else { // otherwise the exhibit type is just the form type
        if (stripos($formType, 'sponsor') == true) {
            $featured = true;
            array_push($tags, "Sponsor");
        } 
        $categories["name"] = $formType;
    }

    // we also want to create a category for each entry id so we can set the entry id as a category in expoFP
    $categories[] = array("name" => $entry['id']);
    $entryID_url = "https://app.expofp.com/api/v1/add-category";
    $entryID_data = [
        "token" => $expofpToken,
        "name" => $entry['id'],
        "eventId" =>  $expofpId
    ];
    postCurl($entryID_url, $headers, json_encode($entryID_data), "POST");

    $data = [
        "token" => $expofpToken,
        "eventId" => $expofpId,
        "name" => $entry['151'],
        "description" => $entry['16'],
        "featured" => $featured,
        "address" => $rmt_shown, // this holds all the resources and attributes entered as a comma delimited string
        "website" => "https://makerfaire.com/maker/entry/" . $entry['id'], // this will be the maker/entry page
        "adminNotes" => "", // should we include this?
        "externalId" => $entry['id'],
        "categories" => $categories,
        "tags" => $tags
    ];

    //error_log(print_r(json_encode($data), TRUE));
    $result = postCurl($url, $headers, json_encode($data), "POST");
    //error_log(print_r($result, TRUE));
    $exhibitor_id = json_decode($result)->id;
    // set the gf_entry_meta to the exhibitor id. if this meta is present, we know we should update rather than add to ExpoFP
    gform_update_meta( $entry['id'], "expofp_exhibit_id", $exhibitor_id, $form['id']);

    return $result;
}

function updateExpoFpExhibit($entry, $form, $expofpToken, $expofpId, $exhibitor_id) {
    $url = "https://app.expofp.com/api/v1/update-exhibitor";
    $headers = array(
        'Accept: application/json', 
        'Content-Type: application/json'
    );

    //check if exhibitor is a sponsor
    $featured = false;
    $formType = $form['form_type'];
    $tags = array();
    $categories = array();
    $rmt_data   = GFRMTHELPER::rmt_get_entry_data($entry['id']);
    $res_arr    = array();
    $attr_arr   = array();
    foreach($rmt_data['resources'] as $resource) {
        $categories[] = array("name" => $resource['token']);
        $res_arr[] = $resource['token'] . ":" . $resource['qty'];
    }
    foreach($rmt_data['attributes'] as $attribute) {
        $attr_arr[] = $attribute['token'] . ":" . $attribute['value'];
        $tags[] = $attribute['token'] . ":" . $attribute['value'];
    }
    $rmt_shown  = implode(', ', array_merge($res_arr, $attr_arr));

    if ($formType == "Master") {
        foreach ($entry as $key => $value) {
            if (strpos($key, '339.') === 0) {
                if ($value != '') {
                    if (stripos($value, 'sponsor') !== false) {
                        $featured = true;
                        array_push($tags, "Sponsor");
                    }
                    $categories[] = array("name" => $value);
                }
            }
        }
    } else { // otherwise the exhibit type is just the form type
        if (stripos($formType, 'sponsor') == true) {
            $featured = true;
            array_push($tags, "Sponsor");
        } 
        $categories[] = array("name" => $formType);
    }

    // we also want to create a category for each entry id so we can set the entry id as a category in expoFP
    $categories[] = array("name" => $entry['id']);
    $entryID_url = "https://app.expofp.com/api/v1/add-category";
    $entryID_data = [
        "token" => $expofpToken,
        "name" => $entry['id'],
        "eventId" =>  $expofpId
    ];
    postCurl($entryID_url, $headers, json_encode($entryID_data), "POST");

    $data = [
        "token" => $expofpToken,
        "id" => $exhibitor_id,
        "name" => $entry['151'],
        "description" => $entry['16'],
        "featured" => $featured,
        "address" => $rmt_shown, // this holds all the resources and attributes entered as a comma delimited string
        "website" => "https://makerfaire.com/maker/entry/" . $entry['id'], // this will be the maker/entry page
        "categories" => $categories,
        "tags" => $tags
    ];

    //error_log(print_r(json_encode($data), TRUE));
    $result = postCurl($url, $headers, json_encode($data), "POST");
    //error_log(print_r($result, TRUE));

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