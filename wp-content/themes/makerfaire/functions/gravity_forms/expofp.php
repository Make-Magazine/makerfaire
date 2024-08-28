<?php
//create a new exhibitor on exopFP
function create_expofp_exhibitor( $entry, $form ) {
    //we only set this in our production env so we avoid writing test data to expoFP
    if(!defined('EXPOFP_TOKEN')){
        return;
    } 
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
        // but if the entry is cancelled or rejected, don't write (this is for exhibits created with devScript that have already been cancelled)
        if($entry['303'] == "Cancelled" || $entry['303'] == "Rejected") {
            $write_to_expofp = false;
        }
    }
    
    if($write_to_expofp == true){
        $expofpToken = EXPOFP_TOKEN;
        $result = createExpoFpExhibit($entry, $form, $expofpToken, $expofpId);
        $exhibitor_id = json_decode($result)->id;

        // Now add the featured image to the exhibitor id on ExpoFP
        $image = json_decode($entry['22'])[0];
        updateExpoFpImage($expofpToken, $exhibitor_id, $image);
    }
}

function update_expofp_exhibitor($form, $entry_id) {
    //we only set this in our production env so we avoid writing test data to expoFP
    if(!defined('EXPOFP_TOKEN')){     
        return;
    }

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
        //error_log($exhibitor_id);

        if($exhibitor_id) { // this meta is set when the exhibit is created
            // after update, if the status is canceled or rejected, delete
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

    $featured   = false;
    $formType   = $form['form_type'];
    $tags       = array();
    $categories = array();
    $rmt_data   = GFRMTHELPER::rmt_get_entry_data($entry['id']);
    $res_arr    = array();
    $attr_arr   = array();
    // add resources and attributes as tags
    foreach($rmt_data['resources'] as $resource) {
        $categories[] = array("name" => $resource['token']);
        $res_arr[] = $resource['token'] . ":" . $resource['qty'];
    }
    foreach($rmt_data['attributes'] as $attribute) {
        $attr_arr[] = $attribute['token'] . ":" . $attribute['value'];
        $tags[] = $attribute['token'] . ":" . $attribute['value'];
    }
    $rmt_shown  = implode(', ', array_merge($res_arr, $attr_arr));

    //check if exhibitor is a sponsor and add form type to categories
    if ($formType == "Master") {
        foreach ($entry as $key => $value) {
            if (strpos($key, '339.') === 0) {
                if ($value != '') {
                    if($value != "Presentation" && $value != "Performer" && $value != "Workshop") {
                        if (stripos($value, 'sponsor') !== false) {
                            $featured = true;
                            array_push($tags, "Sponsor");
                            $value = "Exhibit";
                        }
                        $categories[] = array("name" => $value);
                    }
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

    // now, add all the additional tags we want
    array_push($tags, "Status:" . $entry['303']); // status
    $placementRequest = isset($entry['68']) ? $entry['68'] : '';
    if($placementRequest != '') {
        array_push($tags, "Placement Request:" . $placementRequest); // placement request
    }
    $mobile = str_contains($entry['60'], "mobile") ? $entry['60'] : "";
    if(!empty($mobile)) {
        array_push($tags, "Mobile"); // it's mobile (in case they set a space request other than mobile, but marked mobile later)
    }
    $electrictyNeeds = isset($entry['75']) ? $entry['75'] : '';
    if(!empty($electricty)) {
        array_push($tags, "Electricty needs:" . $electrictyNeeds); // electricty needs
    }
    $tablesChairs = isset($entry['62']) ? $entry['62'] : '';
    if(!empty($tablesChairs)) {
        if(stripos($tablesChairs, "More than 1 table and 2 chairs") !== false) {
            $tablesChairs = $entry['347'] . "tables and " . $entry['348'] . " chairs";
        }
        array_push($tags, "Tables and chairs:" . $tablesChairs); // tables and chairs
    }
    
    // then, add all the category taxonomies the user selected as categories in expofp
    $mainCategoryName = html_entity_decode(get_term($entry[320])->name);
    $categories[] = array("name" => $mainCategoryName);
    foreach ($entry as $key => $value) {
        if (strpos($key, '321.') !== false && $value != null) {
            $categories[] = array("name" => html_entity_decode(get_term($value)->name));
        }
    }
    // additional data to be added as categories (Food, Fire, Exposure, Dark, Amplified, Repetitive, Loud)
    if(isset($entry['44']) && $entry['44'] == 'Yes') {
        $categories[] = array("name" => "Food");
    }
    if(isset($entry['83']) && $entry['83'] == 'Yes') {
        $categories[] = array("name" => "Fire");
    }
    if(isset($entry['69']) && $entry['69'] == 'Inside') {
        $categories[] = array("name" => "Inside");
    } else if($entry['69'] == 'Outside') {
        $categories[] = array("name" => "Outside");
    }
    if(isset($entry['71']) && $entry['71'] == 'Dark') {
        $categories[] = array("name" => "Dark");
    }
    if(isset($entry['72']) && stripos($entry['72'], 'Amplified') !== false) {
        $categories[] = array("name" => "Amplified");
    } else if(stripos($entry['72'], 'Repetitive') !== false) {
        $categories[] = array("name" => "Repetitive");
    } else if(stripos($entry['72'], 'Loud') !== false) {
        $categories[] = array("name" => "Loud");
    }

    // remove duplicate categories, as that would break the expofp api
    $categories = array_unique($categories, SORT_REGULAR);

    $data = [
        "token" => $expofpToken,
        "eventId" => $expofpId,
        "name" => $entry['151'] . " - " . $entry['id'],
        "description" => $entry['16'],
        "featured" => $featured,
        "address" => $rmt_shown, // this holds all the resources and attributes entered as a comma delimited string
        "website" => "https://makerfaire.com/maker/entry/" . $entry['id'], // this will be the maker/entry page
        "externalId" => $entry['id'],
        "categories" => array_values($categories), // array_values here removes empty array elements removed by array_unique, as that would also break the api
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

    //check if exhibitor is a sponsor and add form type to categories
    if ($formType == "Master") {
        foreach ($entry as $key => $value) {
            if (strpos($key, '339.') === 0) {
                if ($value != '') {
                    if($value != "Presentation" && $value != "Performer" && $value != "Workshop") {
                        if (stripos($value, 'sponsor') !== false) {
                            $featured = true;
                            array_push($tags, "Sponsor");
                            $value = "Exhibit";
                        }
                        $categories[] = array("name" => $value);
                    }
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
    
    // now, add all the additional tags we want
    array_push($tags, "Status:" . $entry['303']); // status
    $placementRequest = isset($entry['68']) ? $entry['68'] : '';
    if($placementRequest != '') {
        array_push($tags, "Placement Request:" . $placementRequest); // placement request
    }
    $mobile = str_contains($entry['60'], "mobile") ? $entry['60'] : "";
    if(!empty($mobile)) {
        array_push($tags, "Mobile"); // it's mobile (in case they set a space request other than mobile, but marked mobile later)
    }
    $electrictyNeeds = isset($entry['75']) ? $entry['75'] : '';
    if(!empty($electricty)) {
        array_push($tags, "Electricty needs:" . $electrictyNeeds); // electricty needs
    }
    $tablesChairs = isset($entry['62']) ? $entry['62'] : '';
    if(!empty($tablesChairs)) {
        if(stripos($tablesChairs, "More than 1 table and 2 chairs") !== false) {
            $tablesChairs = $entry['347'] . "tables and " . $entry['348'] . " chairs";
        }
        array_push($tags, "Tables and Chairs:" . $tablesChairs); // electricty needs
    }
    
    // then, add all the category taxonomies the user selected as categories in expofp
    $mainCategoryName = html_entity_decode(get_term($entry[320])->name);
    $categories[] = array("name" => $mainCategoryName);
    foreach ($entry as $key => $value) {
        if (strpos($key, '321.') !== false && $value != null) {
            $categories[] = array("name" => html_entity_decode(get_term($value)->name));
        }
    }
    // additional data to be added as categories (Food, Fire, Exposure, Dark, Amplified, Repetitive, Loud)
    if(isset($entry['44']) && $entry['44'] == 'Yes') {
        $categories[] = array("name" => "Food");
    }
    if(isset($entry['83']) && $entry['83'] == 'Yes') {
        $categories[] = array("name" => "Fire");
    }
    if(isset($entry['69']) && $entry['69'] == 'Inside') {
        $categories[] = array("name" => "Inside");
    } else if($entry['69'] == 'Outside') {
        $categories[] = array("name" => "Outside");
    }
    if(isset($entry['71']) && $entry['71'] == 'Dark') {
        $categories[] = array("name" => "Dark");
    }
    if(isset($entry['72']) && stripos($entry['72'], 'Amplified') !== false) {
        $categories[] = array("name" => "Amplified");
    } else if(stripos($entry['72'], 'Repetitive') !== false) {
        $categories[] = array("name" => "Repetitive");
    } else if(stripos($entry['72'], 'Loud') !== false) {
        $categories[] = array("name" => "Loud");
    }

    // remove duplicate categories, as that would break the expofp api
    $categories = array_unique($categories, SORT_REGULAR);

    $data = [
        "token" => $expofpToken,
        "id" => $exhibitor_id,
        "name" => $entry['151'] . " - " . $entry['id'],
        "description" => $entry['16'],
        "featured" => $featured,
        "address" => $rmt_shown, // this holds all the resources and attributes entered as a comma delimited string
        "website" => "https://makerfaire.com/maker/entry/" . $entry['id'], // this will be the maker/entry page
        "categories" => array_values($categories), // array_values here removes empty array elements removed by array_unique, as that would also break the api
        "tags" => $tags
    ];

    //error_log(print_r($data, TRUE));
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
