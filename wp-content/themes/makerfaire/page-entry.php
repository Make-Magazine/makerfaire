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
    $formType = $form['form_type'];

    if ($formType == "Master") {
        if( (isset($entry['339.4']) && stripos($entry['339.4'], 'sponsor')) || (isset($entry['339.5']) && stripos($entry['339.5'], 'sponsor')) ) {
            $formType = "Sponsor";
            $sponsorshipLevel = isset($entry['339.4']) ? $entry['339.4'] : $entry['339.5'];
        }
    }
    

    //error_log(print_r($form, TRUE));

    if ($formType == "Sponsor") {
        $sponsorshipLevel = $entry["442.3"];
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
        $formSQL = "select faire_name as pretty_faire_name, replace(lower(faire_name),' ','-') as faire_name,  faire_location, faire, id,show_sched,start_dt, end_dt, url_path, faire_map, program_guide, time_zone "
                . " from wp_mf_faire where FIND_IN_SET ($form_id, wp_mf_faire.form_ids)> 0";

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
        $mainCategory = get_term($entry['320'])->name;
        $categories[] = $mainCategory;
    }
    foreach ($entry as $key => $value) {
        if (strpos($key, '321.') !== false && $value != null) {
            if (get_term($value)->name != $mainCategory) {
                $categories[] = get_term($value)->name;
            }
        }
    }
    
    $categoryDisplay = (!empty($categories)?display_categories($categories):'');

    //get makers info
    $makers = getMakerInfo($entry);

    //BA23 - we are only using field 217 for both the maker photo and the group photo    
    $groupphoto = (isset($entry['217']) ? $entry['217'] : '');


    $project_name = (isset($entry['151']) ? $entry['151'] : '');  //Change Project Name
    $project_photo = (isset($entry['22']) ? legacy_get_fit_remote_image_url($entry['22'], 750, 500) : '');

    // this returns an array of image urls from the additional images field
    $project_gallery = (isset($entry['878']) ? explode(",", str_replace(array( '[', ']', '"' ), '', $entry['878'])) : '');

    //if the main project photo isn't set but the photo gallery is, use the first image in the photo gallery
    if($project_photo=='' && is_array($project_gallery)){
        $project_photo = $project_gallery[0];
    }
    
    
    $project_short = (isset($entry['16']) ? $entry['16'] : '');    // Description
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

    $project_website = (isset($entry['27']) ? $entry['27'] : '');  //Website
    $project_video = (isset($entry['32']) ? $entry['32'] : '');     //Video
    $project_video2 = (isset($entry['386']) ? $entry['386'] : '');     //Video2
    $project_title = (isset($entry['151']) ? esc_html($entry['151']) : ''); //Title
    $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);
}

//set sharing card data
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

/* Lets check if we are coming from the MAT tool -
 * if we are, and user is logged in and has access to this record
 *   Display edit functionality
 */
$makerEdit = false;
if ($editEntry == 'edit') {
    //check if loggest in user has access to this entry
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
if($formType == 'Master') {
    $displayFormType = false;
}


// Project Inline video
$video = '';
if (!empty($project_video) && validate_url($project_video)) {
    $dispVideo = str_replace('//vimeo.com', '//player.vimeo.com/video', $project_video);
    //youtube has two type of url formats we need to look for and change
    $videoID = parse_yturl($dispVideo);
    if ($videoID != false) {
        $dispVideo = 'https://www.youtube.com/embed/' . $videoID;
    }
    $video = '<div class="entry-video">
              <div class="embed-youtube">
                <iframe src="' . $dispVideo . '" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
              </div>
            </div>';
}

$video2 = '';
if (!empty($project_video2) && validate_url($project_video2)) {
    $dispVideo = str_replace('//vimeo.com', '//player.vimeo.com/video', $project_video2);
    //youtube has two type of url formats we need to look for and change
    $videoID = parse_yturl($dispVideo);
    if ($videoID != false) {
        $dispVideo = 'https://www.youtube.com/embed/' . $videoID;
    }
    $video2 = '<div class="entry-video">
              <div class="embed-youtube">
                <iframe src="' . $dispVideo . '" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
              </div>
            </div>';
}

//decide if display maker info
$dispMakerInfo = true;
if ($formType == 'Sponsor' || $formType == 'Startup Sponsor' || !$displayMakers) {
    $dispMakerInfo = false;
}


//build the html for the page
/* JS used only if the person visiting this page can edit the information on it */
?>
<div class="clear"></div>
<script type="text/javascript">
    jQuery(document).ready(function () {

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
    });
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
        if($formType == "Sponsor") {
            include TEMPLATEPATH . '/pages/page-entry-view.php';
        } else {
            include TEMPLATEPATH . '/pages/page-entry-sponsor-view.php';
        }
        if ($makerEdit) {
            //use the edit entry public info page
            include TEMPLATEPATH . '/pages/page-entry-edit.php';
        }
    } else { //entry is not active
        echo '<div class="container"><h2>Invalid entry</h2></div>';
        echo '<div class="entry-footer">' . displayEntryFooter() . '</div>';
    }
    ?>
</div><!--entry-page-->

<?php
get_footer();

function display_entry_schedule($entry_id) {
    global $wpdb;
    global $faireID;
    global $faire;
    global $show_sched;
    global $backMsg;
    global $url_sub_path;
    global $faire_map;
    global $program_guide;
    global $timeZone;

    $backlink = "/" . $url_sub_path . "/meet-the-makers/";

    $faire_url = "/" . $faire;

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
    $return = "";
    $return .= '<div class="faireTitle padbottom"><h3 class="faireName">' . ucwords(str_replace('-', ' ', $faire)) . '</h3></div>';
    if (!$show_sched) {
        return $return;
    }

    if ($wpdb->num_rows > 0) {

        $return .= '<div id="entry-schedule">
                   <div class="row padbottom">';

        $prev_start_dt = NULL;
        $prev_location = NULL;
        $multipleLocations = NULL;

        foreach ($results as $row) {
            if (!is_null($row->start_dt)) {
                $start_dt = strtotime($row->start_dt);
                $end_dt = strtotime($row->end_dt);
                $current_start_dt = date("l, F j", $start_dt);
                $current_location = $row->area . ' - ' . ($row->nicename != '' ? $row->nicename : $row->subarea);

                if ($prev_start_dt == NULL) {
                    $return .= '<div class="entry-date-time col-xs-12">';
                }

                if ($prev_start_dt != $current_start_dt) {
                    //This is not the first new date
                    if ($prev_start_dt != NULL) {
                        $return .= '</div><div class="entry-date-time col-xs-12">';
                    }
                    $return .= '<h5><span id="startDT">' . $current_start_dt . '</span></h5><br/>';
                    $prev_start_dt = $current_start_dt;
                    $prev_location = null;
                    $multipleLocations = TRUE;
                }
                // this is a new location
                if ($prev_location != $current_location) {
                    $prev_location = $current_location;
                    $return .= '<small class="text-muted">' . $current_location . '</small><br />';
                }

                $return .= '<small class="text-muted">Time:</small> <span id="dispStartTime">' . date("g:i a", $start_dt) . '</span> - <span id="dispEndTime">' . date("g:i a", $end_dt) . '</span></small><br />';
                //spacetime tool wants date in ISO format - July 2, 2017 5:01:00
                $return .= '<input id="start_dt" name="start_dt" value="' . date("F j, Y H:i:s", $start_dt) . '" type="hidden">';
                $return .= '<input id="end_dt" name="end_dt" value="' . date("F j, Y H:i:s", $end_dt) . '" type="hidden">';
                $return .= select_Timezone($timeZone);
            } else {
                global $faire_start;
                global $faire_end;
                $return .= '<div class="entry-date-time col-xs-12">';

                $faire_start = strtotime($faire_start);
                $faire_end = strtotime($faire_end);

                $dateRange = progDateRange($faire_start, $faire_end);

                //tbd change this to be dynamically populated
                if ($dateRange != "" && $dateRange != null) {
                    $return .= '<h5>' . natural_language_join($dateRange) . ': ' . date("F j", $faire_start) . '-' . date("j", $faire_end) . '</h5>';
                }
                $return .= '<small class="text-muted">LOCATION:</small> ' . $row->area . ' - ' . ($row->nicename != '' ? $row->nicename : $row->subarea) . '</small>';

                $return .= '</div>'; // end date time location block
            }
        }
        $return .= '</div>
              </div>';
        if ($multipleLocations == TRUE) { // this is kind of a mess to require this
            $return .= "</div>";
        }
    }

    return $return;
}

function display_group($entryID) {
    global $wpdb;
    global $faireID;
    global $faire;
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
            $title = '';
            $type = 'child';
            $return .= '<ul class="group-list">';
            foreach ($results as $row) {
                $link_entryID = ($type == 'parent' ? $row->childID : $row->parentID);
                $entry = GFAPI::get_entry($link_entryID);
                //Title
                $project_title = esc_html($entry['151']);
                $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);
                $return .= '<li>Part of: <a href="/maker/entry/' . $link_entryID . '">' . $project_title . '</a></li>';
            }
            return $return .= "</ul>";
        }
    }
}

/* This function is used to display grouped entries and links */

function display_groupEntries($entryID) {
    global $wpdb;
    global $faireID;
    global $faire;
    $return = '';

    //look for all associated entries but exclude trashed entries
    $sql = "select wp_mf_lead_rel.*
          from wp_mf_lead_rel
          left outer join wp_gf_entry  child on wp_mf_lead_rel.childID = child.id
          left outer join wp_gf_entry parent on wp_mf_lead_rel.parentID = parent.id
          where (parentID=" . $entryID . " or childID=" . $entryID . ") and child.status != 'trash' and parent.status != 'trash' GROUP BY wp_mf_lead_rel.childID";
    $results = $wpdb->get_results($sql);
    if ($wpdb->num_rows > 0) {
        if ($results[0]->parentID == $entryID) {
            $title = '<h4>Exhibits in this group:</h4>';
            $type = 'parent';
            $return .= $title . '<ul class="group-list">';
            foreach ($results as $row) {
                $link_entryID = ($type == 'parent' ? $row->childID : $row->parentID);
                $entry = GFAPI::get_entry($link_entryID);
                //Title
                $project_title = esc_html($entry['151']);
                $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);
                $return .= '<li><a href="/maker/entry/' . $link_entryID . '">' . $project_title . '</a></li>';
            }
            return $return .= "</ul>";
        }
    }
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

    // One maker
    // A list of makers (7 max)
    // A group or association
    $displayType = (isset($entry['105']) ? $entry['105'] : '');

    $isGroup = false;
    $isGroup = (stripos($displayType, 'group') !== false);

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

		$socialBlock = '<span class="social-links">';

		//only show the first 3 social links entered
		foreach ($socialArray as $link) {
			//verify that the social media link provided is not blank and is a valid url
			if ($link && isset($link['Your Link']) && $link['Your Link'] != '' && validate_url($link['Your Link'])) {

				//platform was misspelled as plateform in some earlier forms
				if(isset($link['Platform'])){
					$platform = $link['Platform'];
				}elseif(isset($link['Plateform'])){
					$platform = $link['Plateform'];
				}
				//$platform = (isset($link['Platform'])?$link['Platform']:isset($link['Plateform'])?$link['Plateform']:'');
				$socialBlock .= '<a target="_blank" href="' . $link['Your Link'] . '">'.$platform.'</a>';
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
    global $wpdb;
    global $faireID;
    global $faire;
	global $faire_name;
    global $faire_year;
    global $show_sched;
    global $backMsg;
    global $url_sub_path;
    global $faire_map;
    global $program_guide;
    global $makerEdit;
    global $faire_location_db;

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
    $parentPage = get_page_by_path('/' . $url_sub_path . '/');
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

        //overwrite the backlink to send makers back to MAT if $makerEdit = true
        if ($makerEdit) {
            $backlink = "/manage-entries/";
            $backMsg = 'Back to Your Maker Faire Portal';
        }

        if ($mtmPage && isset($mtmPage->post_status) && $mtmPage->post_status == 'publish' || $backlink == "/manage-entries/") {
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
