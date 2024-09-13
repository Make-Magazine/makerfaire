<?php
/**
 * Template Name: Entry
 *
 * @version 2.1
 */
global $wp_query;
global $wpdb;
$entryId = (isset($wp_query->query_vars['e_id']) ? $wp_query->query_vars['e_id'] : '');
$editEntry = (isset($wp_query->query_vars['edit_slug']) ? $wp_query->query_vars['edit_slug'] : '');
$entry = GFAPI::get_entry($entryId);

// The opengraph cards for sharing. This is necessary as otherwise yoast is not pulling dynamic data
$sharing_cards = new mf_sharing_cards();

// give admin, editor and reviewer user roles special ability to see all entries
$user = wp_get_current_user();
$adminView = false;
if (array_intersect(array('administrator', 'editor', 'reviewer'), $user->roles)) {
    $adminView = true;
}

$displayMakers = true;
$displayFormType = true;

//entry not found
if (isset($entry->errors)) {
    $form_id = '';
    $formType = '';
    $entry = array();
    $faire =
        $faireShort =
        $timeZone =
        $project_short =
        $project_photo =
        $project_title = '';
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
                if ($value != '') {
                    if (stripos($value, 'sponsor') !== false) {
                        $exhibit_type[$key] = 'Exhibit';
                    } else {
                        $exhibit_type[$key] = $value;
                    }
                }
            }
        }
    } else { // otherwise the exhibit type is just the form type
        if (stripos($formType, 'sponsor') !== false) {
            // if the form type is a kind of sponsor, it should be shown as an exhibit, and the maker info shouldn't be shown
            $exhibit_type[] = 'Exhibit';
            $displayMakers = false;
        } else {
            $exhibit_type[] = $formType;
        }
    }
    $exhibit_type = array_unique($exhibit_type);

    //build an array of field information for updating fields
    foreach ($form['fields'] as $field) {
        $fieldID = $field->id;
        $fieldData[$fieldID] = $field;
    }

    //set defaults
    $faire = $show_sched = $faireShort = $faire_end = $url_sub_path = $faire_dates = '';
    $faire_name = $faireShort = $faire_start = $faire_end = $faire_year = '';
    $timeZone = 'America/Los_Angeles'; 
    
    if ($form_id != '') {
        $formSQL = "select faire_name, faire, id, show_sched, start_dt, end_dt, url_path, time_zone "
                 . "from wp_mf_faire "
                 . "where FIND_IN_SET ($form_id, wp_mf_faire.form_ids)> 0 "
                 . "order by ID DESC limit 1";

        $results = $wpdb->get_row($formSQL);

        if ($wpdb->num_rows > 0) {            
            $faire_name = $results->faire_name;            
            $faireShort = $results->faire;            
            $show_sched = $results->show_sched;
            $faire_start = $results->start_dt;
            $faire_end = $results->end_dt;
            $faire_year = substr($faire_start, 0, 4);
            $faire_dates = date_format(date_create($faire_start), "F jS") . "-" . date_format(date_create($faire_end), "jS");
            $url_sub_path = $results->url_path;            
            $timeZone = $results->time_zone;
        }
    }

    // build array of categories
    $mainCategoryName = '';
    $mainCategoryIcon = '<i class="fa fa-rocket" aria-hidden="true"></i>';
    $categories = array();

    if (isset($entry['320']) && $entry['320'] != '') {
        $mainCategory = get_term($entry['320']);
        $mainCategoryName = (isset($mainCategory->name) ? $mainCategory->name : '');
        if(isset($mainCategory->taxonomy)) {
            $mainCategoryIconType = get_field('icon_type', $mainCategory->taxonomy . '_' . $mainCategory->term_id);
            // get the mainCategory icon from the mf category taxonomy, if indeed one is set
            if ($mainCategoryIconType == "uploaded_icon") {
                $mainCategoryIcon = '<picture class="main-category-icon"><img src="' . get_field('uploaded_icon', $mainCategory->taxonomy . '_' . $mainCategory->term_id)['url'] . '" height="27px" width="27px" aria-hidden="true" /></picture>';
            } else {
                $fa = get_field('font_awesome', $mainCategory->taxonomy . '_' . $mainCategory->term_id);
                if (!empty($fa)) {
                    $mainCategoryIcon = '<i class="fa ' . $fa . '" aria-hidden="true"></i>';
                }
            }
        }

        $categories[] = $mainCategoryName;
    }

    // get terms from secondary catetgories
    foreach ($entry as $key => $value) {
        if (strpos($key, '321.') !== false && $value != null) {
            if (get_term($value)->name != $mainCategoryName) {
                $categories[] = get_term($value)->name;
            }
        }
    }
    // if main category is not set, grab the first category that is
    if ($mainCategoryName == '' && isset($categories[0])) {
        $mainCategoryName = $categories[0];
    }

    $categoryDisplay = (!empty($categories) ? display_categories($categories) : '');

    //get makers info
    $makers = getMakerInfo($entry);

    // showcase var, can be blank, parent or child
    $showcase = '';

    $project_name = (isset($entry['151']) ? $entry['151'] : '');  //Change Project Name

    $project_photo = (isset($entry['22']) ? $entry['22'] : '');
    //for BA24, the single photo was changed to a multi image which messed things up a bit
    $photo = json_decode($project_photo);
    if (is_array($photo)) {
        $project_photo = $photo[0];
    }

    // this returns an array of image urls from the additional images field
    $project_gallery = (isset($entry['878']) ? json_decode($entry['878']) : '');

    //if the main project photo isn't set but the photo gallery is, use the first image in the photo gallery
    if ($project_photo == '' && is_array($project_gallery)) {
        $project_photo = $project_gallery[0];
    }
    // check if project photo is too small to treat normally
    $proj_photo_size = file_exists($project_photo) ? getimagesize($project_photo) : array(750, 500);

    // these are the images we're using for the responsvie image sources
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
    $location = $scheduleOutput = "";
    if($show_sched){
        $scheduleOutput = display_entry_schedule($entry);
    }

    $project_website = (isset($entry['27']) ? $entry['27'] : '');  //Website
    $project_social = getSocial(isset($entry['906']) ? $entry['906'] : '');
    $project_video = (isset($entry['32']) ? $entry['32'] : '');     //Video
    $project_video2 = (isset($entry['386']) ? $entry['386'] : '');     //Video2
    $project_title = (isset($entry['151']) ? esc_html($entry['151']) : ''); //Title
    $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);
}

/* Lets check if we are coming from the Maker Portal -
 * if we are, and user is logged in and has access to this record
 *   Display edit functionality
 */
$makerEdit = false;
if ($editEntry == 'edit') {
    //check if logged in user has access to this entry
    $current_user = wp_get_current_user();

    //require_once our model
    require_once(get_template_directory() . '/models/maker.php');

    //instantiate the model
    $maker = new maker($current_user->user_email);
    if ($maker->check_entry_access($entry) || $adminView) {
        $makerEdit = true;
    }
}

//set sharing card data, this is necessary
if ((is_array($entry) && isset($entry['status']) && $entry['status'] == 'active' && isset($entry[303]) && $entry[303] == 'Accepted') || $adminView == true || $makerEdit) {
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

//decide if we should display this entry
$validEntry = false;
if (is_array($entry) && !empty($entry)) { //is this a valid entry?
    if (isset($entry[151]) && $entry[151] != '') {
        if ((isset($entry['status']) && $entry['status'] === 'active' && //is the entry not trashed
                isset($entry[303]) && $entry[303] == 'Accepted') || //is the entry accepted?
                $adminView == true) {                                         // OR, if user is an administrator or editor they can see it all

            $validEntry = true; //display the entry
        }
    }
    // is this a show management, other, or not sure in exhibit type? we don't want to show it
    if ((in_array('Show Management', $exhibit_type) || in_array('Not Sure Yet', $exhibit_type) || in_array('Other', $exhibit_type)) && $adminView == false) {
        $validEntry = false;
    }
}

//check flags
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

// if edit entry is true, this means the user viewing the entry is the user who created the entry and should be able to see it
if ($makerEdit) {
    $validEntry = true;    
}

// Project Inline video
$video = '';
if (!empty($project_video) && validate_url($project_video)) {
    global $wp_embed;
    // We want only youtube or vimeo videos to display,
    if ($project_video != '') {
        if (is_valid_video($project_video)) {
            $video = '<div class="entry-video">
                    <div class="embed-youtube">';
            $video .=  $wp_embed->run_shortcode('[embed]' . $project_video . '[/embed]');
            $video .= '</div>
                    </div>';
        } else if (str_contains(strtolower($project_video), 'instagram.com')) {
            $video = '<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="' . $project_video . '?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"> <a href="https://www.instagram.com/reel/C8FdRHfOY1r/?utm_source=ig_embed&amp;utm_campaign=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> <div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div> <div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"> <div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">View this post on Instagram</div></div><div style="padding: 12.5% 0;"></div> <div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(0px) translateY(7px);"></div> <div style="background-color: #F4F4F4; height: 12.5px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 12.5px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(9px) translateY(-18px);"></div></div><div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> <div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div></div><div style="margin-left: auto;"> <div style=" width: 0px; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> <div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> <div style=" width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div></div></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center; margin-bottom: 24px;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 224px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 144px;"></div></div></a><p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="https://www.instagram.com/reel/C8FdRHfOY1r/?utm_source=ig_embed&amp;utm_campaign=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none;" target="_blank">A post shared by Chris Stanley (@stanchris)</a></p></div></blockquote>
                        <script async src="//www.instagram.com/embed.js"></script>';
        }
    }
}

$video2 = '';
if (!empty($project_video2) && validate_url($project_video2)) {
    global $wp_embed;
    if ($project_video != '') {
        if (is_valid_video($project_video)) {
            $video2 = '<div class="entry-video">
                    <div class="embed-youtube">';
            $video2 .=  $wp_embed->run_shortcode('[embed]' . $project_video2 . '[/embed]');
            $video2 .= '</div>
                    </div>';
        } else if (str_contains(strtolower($project_video2), 'instagram.com')) {
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

<div class="entry-page">
    <?php if (isset($timeZone)) { ?>
        <input type="hidden" id="faire_tz" value="<?php echo $timeZone; ?>" />
    <?php
    }
    // If there is edit in the url, they get all these options          
    if ($makerEdit) {

    ?>
        <script>
            jQuery(function() {
                autoOpen = false;
                //if there is an error on the submission, auto open the modal
                if (jQuery(".gv-error").length) {
                    autoOpen = true;
                } else if (jQuery(".gv-notice").length) {
                    jQuery("#dialog-refresh").dialog({
                        dialogClass: 'update-message',
                        modal: true,
                        position: {
                            my: "top",
                            at: "top",
                            of: ".entry-page"
                        },
                    });
                }
                dialog = jQuery("#dialog-form").dialog({
                    autoOpen: autoOpen,
                    resizable: false,
                    width: 'auto',
                    height: "auto",
                    modal: true,
                    position: {
                        my: "top",
                        at: "top",
                        of: ".entry-page"
                    },
                    
                    open: function(event, ui) {
                        jQuery('.ui-widget-overlay').bind('click', function() {
                            jQuery('#dialog-form').dialog('close');
                        });
                    }
                });

                //open dialog/modal
                jQuery("#edit-photos").on("click", function() {
                    jQuery("#dialog-form").dialog("open");
                });

                jQuery(".gv-button-cancel").bind('click', function() {
                    jQuery('#dialog-form').dialog('close');
                });

                jQuery(".suggestions-toggle i").bind('click', function() {
                    jQuery('body').toggleClass( "hide-suggestions" );
                    jQuery(this).toggleClass('fa-toggle-on').toggleClass('fa-toggle-off');
                });
            });
        </script>
        <div class="makerEditHead">
            <!-- empty span to center the above text -->
            <span class="suggestions-toggle">
                Show suggestions:
                <i class="fa fa-toggle-on"></i>
            </span>

            <span style="font-size: 30px;">
                <i>This is a preview of your public entry page.</i>
            </span>

            <?php
            if (isset($form['gv_id_update_public_info']) && $form['gv_id_update_public_info'] != '') {
            ?>
                <button id="edit-photos">Edit Public Info</button>
            <?php
            } else {
            ?>
                <!-- empty span to center the above text -->
                <span>&nbsp;</span>
            <?php

            }
            ?>

            <!--
            <a class="pull-left" target="_blank" href="/maker-sign/<?php echo $entryId ?>/<?php echo $faireShort; ?>/">
                <i class="far fa-file-image" aria-hidden="true"></i>View Your Maker Sign
            </a>-->

        </div>
        <hr />
        <div id="dialog-form" title="Update Public Information">
            <?php
            echo do_shortcode('[gventry entry_id="' . $entryId . '" view_id="' . $form['gv_id_update_public_info'] . '" edit="1"]');
            ?>
        </div>

        <div id="dialog-refresh" style="display:none;">
            <b>Entry Updated.</b> <a href=".">Refresh page to see changes.</a>
        </div>
    <?php
    }

    if ($validEntry) {
        //display the normal entry public information page
        /*if($formType == "Sponsor") {
            include get_template_directory() . '/pages/page-entry-sponsor-view.php';
        } else {*/
        include get_template_directory() . '/pages/page-entry-view.php';
        //}

    } else { //entry is not active
        echo '<div class="container invalid"><h2>Invalid Entry</h2></div>';
        echo '<div class="entry-footer">' . displayEntryFooter() . '</div>';
    }
    ?>
</div><!--entry-page-->

<?php
get_footer();