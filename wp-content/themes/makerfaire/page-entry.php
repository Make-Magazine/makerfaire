<?php
/**
 * Template Name: Entry
 *
 * @version 2.1
 */
global $wp_query;
global $wpdb;
$entryId = (isset($wp_query->query_vars['e_id'])?$wp_query->query_vars['e_id']:'');
$editEntry = (isset($wp_query->query_vars['edit_slug'])?$wp_query->query_vars['edit_slug']:'');
$entry = GFAPI::get_entry($entryId);

//error_log(print_r($entry, TRUE));
// The opengraph cards for sharing
$sharing_cards = new mf_sharing_cards();

// give admin, editor and reviewer user roles special ability to see all entries
$user = wp_get_current_user();
$adminView = false;
if (array_intersect(array('administrator', 'editor', 'reviewer'), $user->roles)) {
    $adminView = true;
}

//entry not found
if (isset($entry->errors)) {
    $form_id = '';
    $formType = '';
    $entry = array();
    $faire = '';
    $faireShort = '';
    $timeZone = '';
} else {
    //find out which faire this entry is for to set the 'look for more makers link'
    $form_id = $entry['form_id'];
    $form = GFAPI::get_form($form_id);

    //check exhibit type (formerly known as form type)
    $exhibit_type = array();
    $formType = $form['form_type'];

    if ($formType == "Master") {                    
        foreach ($entry as $key => $value) {
            if (strpos($key, '339.') === 0) {         
                if($value!='') $exhibit_type[$key] = $value;       
                if(stripos($value, 'sponsor') !== false){
                    $formType = "Sponsor";
                    $sponsorshipLevel = $value;                    
                }                                             
            }          
        }        
    } else { // otherwise the exhibit type is just the form type
        $exhibit_type[] = $formType;
        if ($formType == "Sponsor") {
            $sponsorshipLevel = (isset($entry["442.3"])?$entry["442.3"]:'');
        }
    }

    //build an array of field information for updating fields
    foreach ($form['fields'] as $field) {
        $fieldID = $field->id;
        $fieldData[$fieldID] = $field;
    }

    //if the form was submitted
    $submit = filter_input(INPUT_POST, 'edit_entry_page');
    if ($submit != '') {
        entryPageSubmit($entryId);
        $entry = GFAPI::get_entry($entryId);
    }

    $faire = $show_sched = $faireShort = $faire_end = '';
    if ($form_id != '') {
        $formSQL = "select faire_name as pretty_faire_name, replace(lower(faire_name),' ','-') as faire_name, faire_location, faire, id,show_sched,start_dt, end_dt, url_path, faire_map, program_guide, time_zone "
                . " from wp_mf_faire where FIND_IN_SET ($form_id, wp_mf_faire.form_ids)> 0 order by ID DESC limit 1";

        $results = $wpdb->get_row($formSQL);
        
        if ($wpdb->num_rows > 0) {
            $faire = $results->faire_name;
            $faire_name = $results->pretty_faire_name;
            $faire_location_db = $results->faire_location;
            $faireShort = $results->faire;
            $faireID = $results->id;
            $show_sched = $results->show_sched;
            $faire_start = $results->start_dt;
            $faire_end = $results->end_dt;
            $faire_year = substr($faire_start, 0, 4);
            $url_sub_path = $results->url_path;
            $faire_map = $results->faire_map;
            $program_guide = $results->program_guide;
            $timeZone = $results->time_zone;
        }
    }
    
    // build array of categories
    $mainCategory = '';
    $categories = array();    

    if (isset($entry['320']) && $entry['320']!='') {
        $mainCategory = (isset(get_term($entry['320'])->name)?get_term($entry['320'])->name:'');
        $categories[] = $mainCategory;
    }

    // get terms from secondary catetgories
    foreach ($entry as $key => $value) {
        if (strpos($key, '321.') !== false && $value != null) {
            if (get_term($value)->name != $mainCategory) {
                $categories[] = get_term($value)->name;
            }
        }
    }
    // if main category is not set, grab the first category that is
    if($mainCategory == '' && isset($categories[0])) {
        $mainCategory = $categories[0];
    }
    
    $categoryDisplay = (!empty($categories)?display_categories($categories):'');

    //get makers info
    $makers = getMakerInfo($entry);

    //For BA23, a change was made to only use field 217. 
    //If 111, group photo is set, use that. Else if 217, Maker Photo is set, use that
    $groupphoto = '';
    if(isset($entry['111']) && $entry['111'] != ''){
        $groupphoto = $entry['111'];        
    }elseif(isset($entry['217']) && $entry['217'] != ''){
        $groupphoto = $entry['217'];        
    }    

    //for BA24, the single photo was changed to a multi image which messed things up a bit
    $photo = json_decode($groupphoto);
    if (is_array($photo) && !empty($photo)) {
      $groupphoto = $photo[0];
    }

    $project_name = (isset($entry['151']) ? $entry['151'] : '');  //Change Project Name
    
    $project_photo = (isset($entry['22']) ? $entry['22'] : '');
    //for BA24, the single photo was changed to a multi image which messed things up a bit
    $photo = json_decode($entry['22']);
    if (is_array($photo)) {
      $project_photo = $photo[0];
    }

    // this returns an array of image urls from the additional images field
    $project_gallery = (isset($entry['878']) ? json_decode($entry['878']):'');

    //if the main project photo isn't set but the photo gallery is, use the first image in the photo gallery
    if($project_photo=='' && is_array($project_gallery)){
        $project_photo = $project_gallery[0];
    }
    // check if project photo is too small to treat normally
    $proj_photo_size = getimagesize( $project_photo );

    // here is where we would want to check the size of the image
    $project_photo_large  = legacy_get_resized_remote_image_url($project_photo, 1050, 700);
    $project_photo_medium = legacy_get_resized_remote_image_url($project_photo, 765, 510);
    $project_photo_small  = legacy_get_resized_remote_image_url($project_photo, 420, 280);
        
    $project_short = (isset($entry['16']) ? $entry['16'] : '');    // Description
    $presentation_title  = (isset($entry['880']) ? $entry['880'] : ''); 
    $presentation_description = (isset($entry['882']) ? $entry['882'] : '');

    //field 287 and field 877 can be used in a form for any text input question.
    //We will display these on the entry detail form
    $field_287 = '';
    $field_887 = '';
    if (isset($entry['287'])) {
        $field_287 = $entry['287']; //What are the problems you aim to help solve with this project?
        $field = GFFormsModel::get_field($form, 287);
        $label_287 = (is_object($field)) ? $field->label : "";
    }
    //for VMF2020 we used field 123 instead of 887
    if (strpos($faireShort, "VMF") === 0) {
        if (isset($entry['123'])) {
            $field_887 = $entry['123']; //What are some of the major challenges you have encountered and how did you address them?
            $field = GFFormsModel::get_field($form, 123);
            $label_887 = (is_object($field)) ? $field->label : "";
        }
    } else {
        if (isset($entry['877'])) {
            $field_877 = $entry['877']; //What are some of the major challenges you have encountered and how did you address them?
            $field = GFFormsModel::get_field($form, 877);
            $label_877 = (is_object($field)) ? $field->label : "";
        }
    }

    $friday = (isset($entry['879.3']) && !empty($entry['879.3'])  ? 1 : 0); // is it on friday
    $location = entry_location($entry);

    $project_website = (isset($entry['27']) ? $entry['27'] : '');  //Website
    $project_social = getSocial(isset($entry['906']) ? $entry['906'] : '');
    $project_video = (isset($entry['32']) ? $entry['32'] : '');     //Video
    $project_video2 = (isset($entry['386']) ? $entry['386'] : '');     //Video2
    $project_title = (isset($entry['151']) ? esc_html($entry['151']) : ''); //Title
    $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);
}

//set sharing card data (do we need this? can we remove)
if ((is_array($entry) && isset($entry['status']) && $entry['status'] == 'active' && isset($entry[303]) && $entry[303] == 'Accepted') || $adminView == true) {
    $sharing_cards->project_short = $project_short;
    $sharing_cards->project_photo = $project_photo;
    $sharing_cards->project_title = $project_title;
} else {
    $sharing_cards->project_title = 'Invalid Entry';
    $sharing_cards->project_photo = '';
    $sharing_cards->project_short = '';
}

//Url
global $wp;
$canonical_url = home_url($wp->request) . '/';
$sharing_cards->canonical_url = $canonical_url;

$sharing_cards->set_values();
get_header();

/* Lets check if we are coming from the Maker Portal -
 * if we are, and user is logged in and has access to this record
 *   Display edit functionality
 */
$makerEdit = false;
if ($editEntry == 'edit') {
    //check if logged in user has access to this entry
    $current_user = wp_get_current_user();

    //require_once our model
    require_once( get_template_directory() . '/models/maker.php' );

    //instantiate the model
    $maker = new maker($current_user->user_email);
    if ($maker->check_entry_access($entry)) {
        $makerEdit = true;
    }
}

//check if this entry has won any awards
$ribbons = checkForRibbons(0, $entryId);

// check if activity is hands on
$handsOn = handsOnMarker($entry);

// check if there's the potential to have a register field
$registerLink = (isset($entry[829]) ? $entry[829] : '');

$viewNow = (isset($entry[837]) ? $entry[837] : '');

//if this is a virtual faire, check if supplemental form was subimitted with links
if (strpos($faireShort, "VMF") === 0) { // special for virtual faires
    //check if supplemental form was submitted
    $linkedSQL = 'select entry_id from wp_gf_entry_meta where meta_key="entry_id" and meta_value = ' . $entryId;

    $linked_results = $wpdb->get_results($linkedSQL, ARRAY_A);
    foreach ($linked_results as $linked_result) {
        if (isset($linked_result['entry_id'])) {
            $linked_entryID = $linked_result['entry_id'];
            $linked_entry = GFAPI::get_entry($linked_entryID);
            $registerLink = (isset($linked_entry['829']) && $linked_entry['829'] != '' ? $linked_entry['829'] : $registerLink);
            $viewNow = (isset($linked_entry['52']) && $linked_entry['52'] != '' ? $linked_entry['52'] : $viewNow);
        }
    }
}
//$registerLink = ''; //post faire return blank for register link
//
// give admin and editor users special ability to see all entries
$user = wp_get_current_user();
$adminView = false;
if (array_intersect(array('administrator', 'editor'), $user->roles)) {
    $adminView = true;
}

//decide if we should display this entry
$validEntry = false;
if (is_array($entry) && !empty($entry)) { //is this a valid entry?
    if (isset($entry[151]) && $entry[151] != '') {
        // if 
        if ((isset($entry['status']) && $entry['status'] === 'active' && //is the entry not trashed
                isset($entry[303]) && $entry[303] == 'Accepted') || //is the entry accepted?
                $adminView == true) {                                         // OR, if user is an administrator or editor they can see it all

            $validEntry = true; //display the entry
        }
    }
}

//check flags
$displayMakers = true;


$displayFormType = true;
foreach ($entry as $key => $field) {
    $pos = strpos($key, '304.');
    if ($pos !== false) {
        if ($field == 'no-public-view')
            $validEntry = false;
        if ($field == 'no-maker-display')
            $displayMakers = false;
        if ($field == 'hide-form-type')
            $displayFormType = false;
    }
}


// Project Inline video
$video = '';
if (!empty($project_video) && validate_url($project_video)) {        
    global $wp_embed;
    // We want only youtube or vimeo videos to display,
    if($project_video != '') {
        if( is_valid_video($project_video) ){
            $video = '<div class="entry-video">
                    <div class="embed-youtube">';
            $video .=  $wp_embed->run_shortcode('[embed]' . $project_video. '[/embed]');
            $video .= '</div>
                    </div>';
        } else if(str_contains(strtolower($project_video), 'instagram.com')) {
            $video = '<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="' . $project_video . '?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"> <a href="https://www.instagram.com/reel/C8FdRHfOY1r/?utm_source=ig_embed&amp;utm_campaign=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> <div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div> <div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"> <div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">View this post on Instagram</div></div><div style="padding: 12.5% 0;"></div> <div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(0px) translateY(7px);"></div> <div style="background-color: #F4F4F4; height: 12.5px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 12.5px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(9px) translateY(-18px);"></div></div><div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> <div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div></div><div style="margin-left: auto;"> <div style=" width: 0px; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> <div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> <div style=" width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div></div></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center; margin-bottom: 24px;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 224px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 144px;"></div></div></a><p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="https://www.instagram.com/reel/C8FdRHfOY1r/?utm_source=ig_embed&amp;utm_campaign=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none;" target="_blank">A post shared by Chris Stanley (@stanchris)</a></p></div></blockquote>
                        <script async src="//www.instagram.com/embed.js"></script>';
        }
    }
}

$video2 = '';
if (!empty($project_video2) && validate_url($project_video2)) {
    global $wp_embed;
    if($project_video != '') {
        if( is_valid_video($project_video) ){
            $video2 = '<div class="entry-video">
                    <div class="embed-youtube">';
            $video2 .=  $wp_embed->run_shortcode('[embed]' . $project_video2 . '[/embed]');
            $video2 .= '</div>
                    </div>';
        } else if(str_contains(strtolower($project_video2), 'instagram.com')) {
            $video = '<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="' . $project_video2 . '?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"> <a href="https://www.instagram.com/reel/C8FdRHfOY1r/?utm_source=ig_embed&amp;utm_campaign=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> <div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div> <div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"> <div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">View this post on Instagram</div></div><div style="padding: 12.5% 0;"></div> <div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(0px) translateY(7px);"></div> <div style="background-color: #F4F4F4; height: 12.5px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 12.5px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(9px) translateY(-18px);"></div></div><div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> <div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div></div><div style="margin-left: auto;"> <div style=" width: 0px; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> <div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> <div style=" width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div></div></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center; margin-bottom: 24px;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 224px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 144px;"></div></div></a><p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="https://www.instagram.com/reel/C8FdRHfOY1r/?utm_source=ig_embed&amp;utm_campaign=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none;" target="_blank">A post shared by Chris Stanley (@stanchris)</a></p></div></blockquote>
                        <script async src="//www.instagram.com/embed.js"></script>';
        }
    }    
}

//decide if display maker info
$dispMakerInfo = true;
if (!$displayMakers) {
    $dispMakerInfo = false;
}


//build the html for the page
/* JS used only if the person visiting this page can edit the information on it */
?>
<div class="clear"></div>
<script type="text/javascript">
/*    jQuery(document).ready(function () {

        jQuery(".timeZoneSelect").on("change", function () {
            var tzone = jQuery('#faire_tz').val();
            //start time
            var s = spacetime(jQuery("#start_dt").val(), tzone);
            s = s.goto(this.value);
            var dispStartTime = s.format('time');
            jQuery("#dispStartTime").text(dispStartTime);

            //set date
            var dispDate = s.format("{day}, {month} {date}");
            jQuery("#startDT").text(dispDate);

            //end time
            var e = spacetime(jQuery("#end_dt").val(), tzone);
            e = e.goto(this.value);
            dispEndTime = e.format('time');
            jQuery("#dispEndTime").text(dispEndTime);
        });
    });*/
</script>

<div class="entry-page">
    <?php if(isset($timeZone)) { ?>
        <input type="hidden" id="faire_tz" value="<?php echo $timeZone; ?>" />
    <?php
    }
    // If there is edit in the url, they get all these options
    if ($makerEdit) {
        $GVeditLink = do_shortcode('[gv_entry_link post_id="478584" action="edit" return="url" view_id="642359" entry_id="'.$entryId.'"]');
        ?>
        <div class="makerEditHead">
            <input type="hidden" id="entry_id" value="<?php echo $entryId; ?>" />
            <a class="pull-left" target="_blank" href="<?php echo $GVeditLink; ?>/">
                <i class="fas fa-image" aria-hidden="true"></i>Manage Your Photos
            </a>
            <!--
            <a class="pull-left" target="_blank" href="/maker-sign/<?php echo $entryId ?>/<?php echo $faireShort; ?>/">
                <i class="far fa-file-image" aria-hidden="true"></i>View Your Maker Sign
            </a>-->

            <span class="editLink pull-right">
                <a href="/bay-area/public-information/?ep_token=<?php echo (isset($entry['fg_easypassthrough_token'])?$entry['fg_easypassthrough_token']:'');?>"><i class="fas fa-edit" aria-hidden="true"></i>Manage Public Info</a>                
            </span>
            <div class="clear"></div>
        </div>
        <br/>
        <?php
    }

    if ($validEntry) {
        //display the normal entry public information page
        /*if($formType == "Sponsor") {
            include get_template_directory() . '/pages/page-entry-sponsor-view.php';
        } else {*/
            include get_template_directory() . '/pages/page-entry-view.php';
        //}
        if ($makerEdit) {
            //use the edit entry public info page
            include get_template_directory() . '/pages/page-entry-edit.php';
        }
    } else { //entry is not active
        echo '<div class="container invalid"><h2>Invalid entry</h2></div>';
        echo '<div class="entry-footer">' . displayEntryFooter() . '</div>';
    }
    ?>
</div><!--entry-page-->

<?php
get_footer();

function entry_location($entry) {
    global $wpdb;
    $entry_id=$entry['id'];
    $sql = "select location.entry_id, area.area, subarea.subarea, subarea.nicename, location.location
            from  wp_mf_location location
            join  wp_mf_faire_subarea subarea
                            ON  location.subarea_id = subarea.ID
            join wp_mf_faire_area area
                            ON subarea.area_id = area.ID
             where location.entry_id=" . $entry_id . " 
             group by area, subarea, location";
    $results = $wpdb->get_results($sql);
    
    $location = "";
    // how do we handle multiple locations????
    foreach($results as $result){
        // so this was because there was a workshop that also had a normal location
        if($result->subarea != "Workshop") {
            $location = $result->nicename;
        }
    }
    return $location;
}

// adapt this to only get the schedule
function schedule_block($entry) {    
    global $wpdb; 
    global $exhibit_type;
    global $location;

    //set entry id
    $entry_id=$entry['id'];

    $sql = "select location.entry_id, area.area, subarea.subarea, subarea.nicename, location.location, schedule.start_dt, schedule.end_dt
            from  wp_mf_location location
            join  wp_mf_faire_subarea subarea
                            ON  location.subarea_id = subarea.ID
            join wp_mf_faire_area area
                            ON subarea.area_id = area.ID
            left join wp_mf_schedule schedule
                    on location.ID = schedule.location_id
             where location.entry_id=$entry_id"
            . " group by area, subarea, location, schedule.start_dt"
            . " order by schedule.start_dt";
    $results = $wpdb->get_results($sql);

    $return = "<h2>" . implode(", ", $exhibit_type) . " Schedule</h2><div class='schedule-items'>";

    // this object has all the schedule details for each day
    $daysObj = new stdClass();

    if ($wpdb->num_rows > 0) {              
        foreach ($results as $row) {   
            //schedule data     
            if (!is_null($row->start_dt)) {
                $start_dt = strtotime($row->start_dt);
                $date_key = date('D-d', $start_dt);
                if(!property_exists($daysObj, $date_key)) {
                    $dayObj = new stdClass;
                    $dayObj->date = date('D d Y', $start_dt);
                    $dayObj->dow = date('D', $start_dt);
                    $dayObj->day = date('d', $start_dt);
                    $dayObj->location = $row->nicename;
                    $dayObj->times[] = date('g:i a', $start_dt);
                    // assign our dynamic object to the daysObj
                    $daysObj->$date_key = $dayObj;
                } else {
                    array_push($daysObj->$date_key->times, date('g:i a', $start_dt));
                }
            } else {
                //set primary location
                $location = $row->area . ' - ' . ($row->nicename != '' ? $row->nicename : $row->subarea);
            }
        } //end for each loop  
        
        foreach($daysObj as $key => $value) {
            $time_list = '';
            if(count($value->times) > 2) {
                $last = array_pop($value->times);
                $time_list = implode(', ', $value->times);
                if ($time_list) {
                    $time_list .= ', & ';
                }
                $time_list .= $last;
            } else {
                $time_list = implode(' & ', $value->times);
            }
            $return .= "<div class='schedule-item'>
                            <div class='schedule-calendar'>
                                <span class='schedule-dow'>" . $value->dow . "</span>
                                <span class='schedule-day'>" . $value->day . "</span>
                                <img src='/wp-content/themes/makerfaire/images/calendar-blank.svg' width='65' height='72' alt='" . $dayObj->date . "' title='" . $dayObj->date . "' />
                            </div>
                            <div class='schedule-details'>
                            <div class='location'>" . $value->location  . "</div>
                                <div class='schedule-start'>" . $time_list . "</div>
                            </div>
                        </div>";   
        }
        $return .= "</div>";
    } else { //end if location data found 
        // otherwise don't return anything
        $return = "";
    }
    // also, if the schedule object is empty, don't show the block
    if(empty((array) $daysObj)) {
        $return = "";
    }
    //var_dump($daysObj);

    return $return;
}

function display_group($entryID) {
    global $wpdb;
    global $groupphoto;  
    
    $return = '';

    //look for all associated entries but exclude trashed entries
    $sql = "select wp_mf_lead_rel.*
          from wp_mf_lead_rel
          left outer join wp_gf_entry  child on wp_mf_lead_rel.childID = child.id
          left outer join wp_gf_entry parent on wp_mf_lead_rel.parentID = parent.id
          where (parentID=" . $entryID . " or childID=" . $entryID . ") and child.status != 'trash' and parent.status != 'trash' GROUP BY wp_mf_lead_rel.parentID";
    $results = $wpdb->get_results($sql);
    if ($wpdb->num_rows > 0) {
        if ($results[0]->parentID != $entryID) {            
            $type = 'child';
            $return .= '<section class="group-list entry-box">';
            foreach ($results as $row) {
                $link_entryID = ($type == 'parent' ? $row->childID : $row->parentID);
                $entry = GFAPI::get_entry($link_entryID);
                //Title
                $project_title = esc_html($entry['151']);
                $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);
                $project_photo = $entry['22'];
                $project_bio = $entry['110'];
                //$return .= '<span>Part of: <a href="/maker/entry/' . $link_entryID . '">' . $project_title . '</a></span>';
                $return .= '<div class="group-wrapper">
                                <div>
                                   <picture>
                                      <img src="' . legacy_get_resized_remote_image_url($project_photo, 215, 215) . '" alt="' . $project_title . '" />
                                   </picture>
                                </div>
                                <div>
                                    <h2>' . $project_title . ' Showcase Maker</h2>
                                    <p>' . $project_bio . '</p>
                                </div>
                            </div>';
            }
            return $return .= "</section>";
        }
    }
}

/* This function is used to display grouped entries and links */

function display_groupEntries($entryID) {
    global $wpdb;    
    $return = '';

    //look for all associated entries but exclude trashed entries
    $sql = "select wp_mf_lead_rel.*, title.meta_value as title
            from wp_mf_lead_rel 
            left outer join wp_gf_entry child on wp_mf_lead_rel.childID = child.id 
            left outer join wp_gf_entry_meta on child.id = wp_gf_entry_meta.entry_id and wp_gf_entry_meta.meta_key =303 
            left outer join wp_gf_entry_meta title on child.id = title.entry_id and title.meta_key =151 
            left outer join wp_gf_entry parent on wp_mf_lead_rel.parentID = parent.id 
            
            where (parentID=" . $entryID . " or childID=" . $entryID . ") 
            AND child.status != 'trash' 
            AND parent.status != 'trash' 
            AND wp_gf_entry_meta.meta_value='Accepted' 
            order by title;";
        
    $results = $wpdb->get_results($sql);
    if ($wpdb->num_rows > 0) {
        if ($results[0]->parentID == $entryID) {
            $return .= '<h4>Exhibits in this group:</h4>';
            $type = 'parent';            
            foreach ($results as $row) {
                $link_entryID = ($type == 'parent' ? $row->childID : $row->parentID);                
                
                //Title
                $project_title = esc_html($row->title);            
                $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);

                $return .= '<span><a href="/maker/entry/' . $link_entryID . '">' . $project_title . '</a></span><br/>';
            }            
        }
    }
    return $return;
}

// provide a linked list of the categories
function display_categories($catArray) {
    global $url_sub_path;
    $return = '<b>Categories:</b>';
    foreach ($catArray as $value) {
        $return .= ' <a href="/' . $url_sub_path . '/meet-the-makers/?category=' . str_replace("&amp;", "%26", $value) . '">' . $value . '</a>,';
    }
    return rtrim($return, ',');
}

//return makers info
function getMakerInfo($entry) {
    $makers = array();

    /* for VMF2020 we had two nested forms, one for makers one for entries. We determined this was too confusing for
     * our makers, and switched to one parent form for entry with one nested form for multiple makers.
     */
    if (isset($entry['gpnf_entry_parent']) && $entry['gpnf_entry_parent'] != '') { //is this a nested form with parent information
        //pull maker information from nested form - VMF2020
        $makers = getMakerInfoNested($entry);
    } elseif (isset($entry['854'])) {
        // after VMF2020
        $makers = getMakerInfoNested($entry);
    } else {
        //pull information from legacy - pre VMF2020
        $makers = getMakerInfoLegacy($entry);
    }

    return $makers;
}

function getMakerInfoLegacy($entry) {
    //set group information
    global $isGroup;
    global $groupname;
    global $groupphoto;
    global $groupbio;
    global $groupsocial;
    $groupname = (isset($entry['109']) ? $entry['109'] : '');
    $groupphoto = (isset($entry['111']) ? $entry['111'] : '');
    $groupbio = (isset($entry['110']) ? $entry['110'] : '');
    $groupsocial = getSocial(isset($entry['828']) ? $entry['828'] : '');
    $groupwebsite = isset($entry['112']) ? $entry['112'] : '';

    // One maker
    // A list of makers (7 max)
    // A group or association
    $displayType = (isset($entry['105']) ? $entry['105'] : '');

    $isGroup = false;
    $isGroup = (stripos($displayType, 'group') !== false || stripos($displayType, 'team') !== false?true:false);

    $makers = array();
    //set maker information
    if (isset($entry['160.3']) && $entry['160.3'] != "")
        $makers[1] = array('firstname' => $entry['160.3'], 'lastname' => $entry['160.6'],
            'bio' => (isset($entry['234']) ? $entry['234'] : ''),
            'photo' => (isset($entry['217']) ? $entry['217'] : ''),
            'social' => getSocial(isset($entry['821']) ? $entry['821'] : ''),
            'website' => (isset($entry['209']) ? $entry['209'] : '')
        );
    if (isset($entry['158.3']) && $entry['158.3'] != "")
        $makers[2] = array('firstname' => $entry['158.3'], 'lastname' => $entry['158.6'],
            'bio' => (isset($entry['258']) ? $entry['258'] : ''),
            'photo' => (isset($entry['224']) ? $entry['224'] : ''),
            'social' => getSocial(isset($entry['822']) ? $entry['822'] : ''),
            'website' => (isset($entry['216']) ? $entry['216'] : '')
        );
    if (isset($entry['155.3']) && $entry['155.3'] != "")
        $makers[3] = array('firstname' => $entry['155.3'], 'lastname' => $entry['155.6'],
            'bio' => (isset($entry['259']) ? $entry['259'] : ''),
            'photo' => (isset($entry['223']) ? $entry['223'] : ''),
            'social' => getSocial(isset($entry['823']) ? $entry['823'] : ''),
            'website' => (isset($entry['215']) ? $entry['215'] : '')
        );
    if (isset($entry['156.3']) && $entry['156.3'] != "")
        $makers[4] = array('firstname' => $entry['156.3'], 'lastname' => $entry['156.6'],
            'bio' => (isset($entry['260']) ? $entry['260'] : ''),
            'photo' => (isset($entry['222']) ? $entry['222'] : ''),
            'social' => getSocial(isset($entry['824']) ? $entry['824'] : ''),
            'website' => (isset($entry['214']) ? $entry['214'] : '')
        );
    if (isset($entry['157.3']) && $entry['157.3'] != "")
        $makers[5] = array('firstname' => $entry['157.3'], 'lastname' => $entry['157.6'],
            'bio' => (isset($entry['261']) ? $entry['261'] : ''),
            'photo' => (isset($entry['220']) ? $entry['220'] : ''),
            'social' => getSocial(isset($entry['825']) ? $entry['825'] : ''),
            'website' => (isset($entry['213']) ? $entry['213'] : '')
        );
    if (isset($entry['159.3']) && $entry['159.3'] != "")
        $makers[6] = array('firstname' => $entry['159.3'], 'lastname' => $entry['159.6'],
            'bio' => (isset($entry['262']) ? $entry['262'] : ''),
            'photo' => (isset($entry['221']) ? $entry['221'] : ''),
            'social' => getSocial(isset($entry['826']) ? $entry['826'] : ''),
            'website' => (isset($entry['211']) ? $entry['211'] : '')
        );
    if (isset($entry['154.3']) && $entry['154.3'] != "")
        $makers[7] = array('firstname' => $entry['154.3'], 'lastname' => $entry['154.6'],
            'bio' => (isset($entry['263']) ? $entry['263'] : ''),
            'photo' => (isset($entry['219']) ? $entry['219'] : ''),
            'social' => getSocial(isset($entry['827']) ? $entry['827'] : ''),
            'website' => (isset($entry['212']) ? $entry['212'] : '')
        );
    // rather than have the page entry view have to do something different for groups, let's just put it at the front of the maker array
    if($isGroup) {
        array_unshift($makers, array(
            'firstname' => $groupname, 'lastname' => null,
            'bio' => $groupbio,
            'photo' => $groupphoto,
            'social' => $groupsocial,
            'website' => $groupwebsite
            )
        );
    }
    return $makers;
}

function handsOnMarker($entry) {
    ################ For form exhibits, show if exhibit is hands on ################
    if (isset($entry['66'])) {
        if ($entry['66'] == "Yes") {
            return '<div class="hands-on"><span class="lnr lnr-checkmark-circle"></span> Hands-on Activity</div>';
        }
    }
}

function getSocial($entrySocial) {
	$socialBlock = '';

	if (isset($entrySocial)) {
		$entrySocial = (string) $entrySocial;
		$socialArray = (is_serialized($entrySocial)?unserialize($entrySocial):array());

		$socialBlock = '<span class="social-links reversed">';

		//only show the first 3 social links entered
		foreach (array_slice($socialArray, 0, 3) as $link) {
			//verify that the social media link provided is not blank and is a valid url
			if ($link && isset($link['Your Link']) && $link['Your Link'] != '' && validate_url($link['Your Link'])) {
				//platform was misspelled as plateform in some earlier forms
				if(isset($link['Platform'])){
					$platform = $link['Platform'];
				}elseif(isset($link['Plateform'])){
					$platform = $link['Plateform'];
				}
				//$platform = (isset($link['Platform'])?$link['Platform']:isset($link['Plateform'])?$link['Plateform']:'');
				$socialBlock .= '<a target="_blank" href="' . $link['Your Link'] . '" aria-label="Check them out on '.$platform.'" title="Check them out on '.$platform.'"><span>'.$platform.'</span></a>';
			}
		}
		$socialBlock .= '</span>';
	}

    return $socialBlock;
}

function progDateRange($faire_start, $faire_end) {
    $dates = array();
    $flexDate = $faire_start;
    while ($flexDate <= $faire_end) {
        $dates[] = date("l", $flexDate);
        $flexDate = strtotime('+1 day', $flexDate);
    }
    return $dates;
}

function natural_language_join(array $list, $conjunction = 'and') {
    $last = array_pop($list);
    if ($list) {
        return implode(', ', $list) . ' ' . $conjunction . ' ' . $last;
    }
    return $last;
}

function entryPageSubmit($entryId) {
    //get submitted data
    $form_id = filter_input(INPUT_POST, 'form_id', FILTER_SANITIZE_NUMBER_INT);
    $form = GFAPI::get_form($form_id);

    foreach ($_POST as $inputField => $value) {
        $pos = strpos($inputField, 'input_');
        if ($pos !== false) {
            $fieldID = str_replace('input_', '', $inputField);
            $fieldID = str_replace('_', '.', $fieldID);

            updateFieldValue($fieldID, $value, $entryId);
        }
    }

    //update maker table information
    GFRMTHELPER::updateMakerTables($entryId);
}

function updateFieldValue($fieldID, $newValue, $entryId) {
    global $fieldData;
    global $entry;
    global $form;
    $fieldInfo = $fieldData[(int) $fieldID];

    $fieldLabel = $fieldInfo['label'];

    //set who is updating the record
    $current_user = wp_get_current_user();
    $user = $current_user->ID;

    $form_id = $entry['form_id'];
    $chgRPTins = array();

    $entry_id = $entry['id'];

    if ($fieldInfo->type == 'fileupload') {
        $field = GFFormsModel::get_field($form, $fieldNum);
        $input_name = 'input_' . str_replace('.', '_', $fieldID);

        //validate uploaded file
        $field->validate($_FILES[$input_name], $form);
        if ($field->failed_validation) {
            echo $field->validation_message;
            echo 'failed';
            return;
        }
        $newValue = $field->upload_file($form_id, $_FILES[$input_name]);

        //trigger cron job to correct image orientation if needed
        triggerCronImg($entry, $form);

        //trigger update
        gf_do_action(array('gform_after_update_entry', $form_id), $form, $entry_id, $entry);

        //for security reason, we force to remove all uploaded file
        //@unlink($_FILES['value']);
    } else {
        $fieldValue = (isset($entry[$fieldID]) ? $entry[$fieldID] : '');
    }

    //update field and change report if needed
    if ($fieldValue != $newValue) {
        GFAPI::update_entry_field($entry_id, $fieldID, $newValue);
        $chgRPTins[] = RMTchangeArray($user, $entryId, $form_id, $fieldID, $fieldValue, $newValue, $fieldLabel);
        updateChangeRPT($chgRPTins);
        $entry[$fieldID] = $newValue;
    }
}

function displayEntryFooter() {    
    global $faire;
	global $faire_name;
    global $faire_year;
    global $show_sched;
    global $backMsg;
    global $url_sub_path;
    global $faire_map;
    global $makerEdit;

    $faire_location = "Bay Area";
    $faire_link = "/bay-area";

    if (strpos($faire, 'new-york') !== false) {
        $faire_location = "New York";
        $faire_link = "/new-york";
    }
    if (strpos($faire, 'virtual') !== false) {
        $faire_location = "";
        $faire_link = "";
    } else {
    	//if a valid url is added to the db, use that otherwise assume it's a MF link
    	if (validate_url($url_sub_path)) {
    		$faire_location = $faire_name;
    		$faire_link = $url_sub_path;
    	}else{
        	$faire_location = '';
        	$faire_link = '/' . $url_sub_path;
    	}
    }

    // we're going to check if the schedule page exists
    //find the parent page
    $parentPage = get_page_by_path($url_sub_path . '/');
    $schedulePage = '';
    $mtmPage = '';    
    if(isset($parentPage->ID)){        
    	$args = array('parent' => $parentPage->ID, 'meta_key' => '_wp_page_template', 'meta_value' => 'page-meet-the-makers.php');
    	$mtmPages = get_pages($args);
    	$mtmPage = (isset($mtmPages[0]) ? $mtmPages[0] : '');

    	$args = array('parent' => $parentPage->ID, 'meta_key' => '_wp_page_template', 'meta_value' => 'page-schedule.php');
    	$schedulePages = get_pages($args);
    	$schedulePage = (isset($schedulePages[0]) ? $schedulePages[0] : '');
    }
    $return = '';
    $return .= '<div class="faireActions container">';

    //set the 'backlink' text and link (only set on valid entries)    
    if ($faire != '') {
        $url = parse_url(wp_get_referer()); //getting the referring URL
        $url['path'] = rtrim($url['path'], "/"); //remove any trailing slashes
        $path = explode("/", $url['path']); // splitting the path
        $backlink = ($mtmPage && isset($mtmPage->ID) ? get_permalink($mtmPage->ID) : '');
        $backMsg = 'See all ' . $faire_year . ' makers';

        //overwrite the backlink to send makers back to the Maker Portal if $makerEdit = true
        if ($makerEdit) {
            $backlink = "/maker-portal/";
            $backMsg = 'Back to Your Maker Faire Portal';
        }    
        
        if (($mtmPage && isset($mtmPage->post_status) && $mtmPage->post_status == 'publish') || $backlink == "/maker-portal/") {
            $return .= '<div class="faireAction-box">
		            		<a class="btn universal-btn" href="' . $backlink . '"><h4>' . $backMsg . '</h4></a>
						</div>';
        }
    }
    if ($schedulePage && isset($schedulePage->post_status) && $schedulePage->post_status == 'publish') {
        $return .= '<div class="faireAction-box">
			<a class="btn universal-btn" href="' . get_permalink($schedulePage->ID) . '"><h4>View full schedule</h4></a>
		    </div>';
    }

    if ($faire_map != '' && $show_sched != 0) {
        $return .= '<div class="faireAction-box">
		        <a class="btn universal-btn" href="' . $faire_map . '"><h4>Download Map</h4></a>
		    </div>';
    }
    if ($faire_link != '' || $faire_location != '') {
        $return .= '<div class="faireAction-box">
		         <a class="btn universal-btn" href="' . $faire_link . '"><h4>' . ($faire_location != '' ? $faire_location : 'Faire') . ' Home</h4></a>
                    </div>';
    }
    $return .= '</div>';

    return $return;
}

function getMakerInfoNested($entry) {
    global $isGroup;

    global $groupname;
    global $groupphoto;
    global $groupbio;
    global $groupsocial;
    global $entryId;

    //pull group information from current entry
    if (isset($entry['844']) && $entry['844'] == 'Yes') {
        $isGroup = true;
        $groupname = (isset($entry['109']) ? $entry['109'] : '');
        $groupphoto = (isset($entry['111']) ? $entry['111'] : '');
        $groupbio = (isset($entry['110']) ? $entry['110'] : '');
        $groupsocial = getSocial(isset($entry['828']) ? $entry['828'] : '');
    }
    $child_entryID_array = (isset($entry['854'])?explode(",", $entry['854']):array()); //field 854 contains the makers, 852 contains the projects

    //get maker information
    $makers = array();

    foreach ($child_entryID_array as $child_entryID) {
        if ($child_entryID != $entryId) { //no need to process the entry we are looking at
            $child_entry = GFAPI::get_entry($child_entryID);

            if (!is_wp_error($child_entry) && $child_entry['form_id'] == 246) {
                $makers[] = array('firstname' => $child_entry['160.3'], 'lastname' => $child_entry['160.6'],
                    'bio' => (isset($child_entry['234']) ? $child_entry['234'] : ''),
                    'photo' => (isset($child_entry['217']) ? $child_entry['217'] : ''),
                    'social' => getSocial(isset($child_entry['821']) ? $child_entry['821'] : ''),
                    'website' => (isset($child_entry['209']) ? $child_entry['209'] : '')
                );
            }
        }
    }

    return $makers;
}
