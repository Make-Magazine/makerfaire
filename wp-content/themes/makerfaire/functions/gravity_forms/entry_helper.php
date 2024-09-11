<?php
//functions for the entry page
// this spits out schedule blocks
function display_entry_schedule($entry) {
    global $wpdb;
    global $show_sched;
    global $location;
    global $url_sub_path;

    //set entry id
    $entry_id = $entry['id'];

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

    $schedule = "";
    // we default to believing an entry doesn't have a schedule. if starts dates are found, this will change
    $has_schedule = false;

    if ($wpdb->num_rows > 0) {
        $prev_start_dt = NULL;
        $prev_location = NULL;
        $multipleLocations = NULL;
        $schedule = '<div class="schedule-items">';

        //split the results into base location and schedule        
        foreach ($results as $row) {
            //schedule data     
            if (!is_null($row->start_dt)) { // if there is no start date, it's a base location
                $start_dt = strtotime($row->start_dt);
                $current_start_dt = date("l, F j", $start_dt);
                $date = date('D j F Y', $start_dt);
                $dow = date('D', $start_dt);
                $day = date('j', $start_dt);
                $current_location = ($row->nicename != '' ? $row->nicename : $row->subarea);

                if ($prev_start_dt == NULL) {
                    $schedule .= "<div class='schedule-item'>
                                    <div class='schedule-calendar'>
                                        <span class='schedule-dow'>" . $dow . "</span>
                                        <span class='schedule-day'>" . $day . "</span>
                                        <img src='/wp-content/themes/makerfaire/images/calendar-blank.svg' width='65' height='72' aria-label='" . $date . "' title='" . $date . "' alt='" . $date . "' title='" . $date . "' />
                                    </div>
                                    <div class='schedule-details'>";
                }

                if ($prev_start_dt != $current_start_dt) {
                    //This is not the first new date
                    if ($prev_start_dt != NULL) {
                        $schedule .= '</div></div>    
                                      <div class="schedule-item">
                                        <div class="schedule-calendar">
                                            <span class="schedule-dow">' . $dow . '</span>
                                            <span class="schedule-day">' . $day . '</span>
                                            <img src="/wp-content/themes/makerfaire/images/calendar-blank.svg" width="65" height="72" aria-label="' . $date . '" alt="' . $date . '" title="' . $date . '" />
                                        </div>
                                        <div class="schedule-details">';
                    }
                    $prev_start_dt = $current_start_dt;
                    $prev_location = null;
                    $multipleLocations = TRUE;
                }

                // this is a new location
                if ($prev_location != $current_location) {
                    $prev_location = $current_location;
                    $schedule .= '<b class="location">' . $current_location . '</b>';
                }
                $schedule .= '<div class="schedule-start">' . date("g:i a", $start_dt) . '</div>';
                /* if you wanted to show the booth name
                if ($row->location != '') {
                    $schedule .= $row->location;
                } */

                // if there any start dates were found, we should show a schedule
                $has_schedule = true;

            } else {
                //base location at faire
                //set primary location
                if (empty($location) || $location == "") {
                    $location = ($row->nicename != '' ? $row->nicename : $row->subarea);
                }
                           
            }
        } //end for each loop       
        if ($multipleLocations == TRUE) { // this is kind of a mess to require this
            $schedule .= "</div></div>";
        }
        $schedule .= "<a href='/" . $url_sub_path . "/schedule/'>See Details</a></div>";
    } //end if location data found

    $return = '';

    if ($show_sched && $has_schedule) {
        $return .=  '<h4>Schedule</h4>'
            . $schedule;
    }



    return $return;
}

// new showcase function, can be used for parent or children
function showcase($entryID) {    
    global $showcase;

    $return = '';

    $showcase_info = get_showcase_entries($entryID);

    //if this isn't a showcase, get out of here
    if(!isset($showcase_info['type'])){
        return '';
    }

    $showcase = $showcase_info['type'];
    if($showcase == 'parent'){
        global $groupname;
        global $groupphoto;
        global $groupbio;
        global $entry;
        $groupsocial = getSocial(isset($entry['828']) ? $entry['828'] : '');
        $groupwebsite = isset($entry['112']) ? $entry['112'] : '';
        
        // we reuse the makerInfo section for projects here, as it's the same css
        $return .= '<section id="makerInfo" class="showcase-list makers-' . count($showcase_info['child_data']) . '">';
        foreach ($showcase_info['child_data'] as $parent) {
            $return .= '<a href="/maker/entry/' . $parent['child_entryID'] . '" class="entry-box">
                                        <img src="' . legacy_get_resized_remote_image_url($parent['child_photo'], 400, 400) . '"
                                            alt="' . $parent['child_title'] . ' Picture"
                                            onerror="this.onerror=null;this.src=\'/wp-content/themes/makerfaire/images/default-makey-medium.png\';" />
                                        <h3>' . $parent['child_title'] . '</h3>
                                    </a>';
        }
        $return .= '</section>';
        $return .= '<section class="showcase-list showcase-parent entry-box">
                            <div class="showcase-wrapper">
                                <div>
                                   <picture>
                                      <img src="' . legacy_get_resized_remote_image_url($groupphoto, 215, 215) . '" alt="' . $groupname . '" />
                                   </picture>
                                </div>
                                <div>
                                    <h2>' . $groupname . '</h2>
                                    <p>' . $groupbio . '</p>
                                    <p><a class="showcase-website" href="' . $groupwebsite . '">' . $groupwebsite . '</a></p>'
            . $groupsocial .
            '</div>
                            </div>
                    </section>';
    } elseif($showcase == 'child'){        
        $parent = $showcase_info['parent_data'];
        $return .= '<section class="showcase-list showcase-parent entry-box">
                            <div class="showcase-wrapper">
                                <div>
                                    <picture>
                                        <a href="/maker/entry/' . $parent['parent_id'] . '/">
                                            <img src="' . legacy_get_resized_remote_image_url($parent['parent_photo'], 215, 215) . '" alt="' . $parent['parent_title'] . '" />
                                        </a>
                                    </picture>
                                </div>
                                <div>
                                    <a href="/maker/entry/' . $parent['parent_id'] . '/"><h2>' . $parent['parent_title'] . ' Showcase Maker</h2></a>
                                    <p>' . $parent['parent_desc'] . '</p>
                                </div>
                            </div>
                    </section>';
    }
    return $return;
}

function get_showcase_entries($entryID){
    $showcase_info = array();
    global $wpdb;
    //first see if this entry is a parent or child record, this helps speed up admin review    
    $sql = "SELECT parentID, childID ".
            "FROM wp_mf_lead_rel ".
            "WHERE (parentID=$entryID or childID=$entryID) limit 1";
    $results = $wpdb->get_row($sql,ARRAY_A);        

    //if no results found, exit. this is not a showcase or part of one
    if(empty($results)){
        return $showcase_info;
    }

    //now lets look at the first row and see if we are dealing with the child or the parent
    if($results['parentID']==$entryID) {
        $showcase_info['type']='parent';
    }else{
        $showcase_info['type']='child';
    }

    //now that we know the type let's either pull the parent or the child data
    if($showcase_info['type']=='parent'){
        //look for all associated entries but exclude trashed entries
        $sql = "SELECT parentID, childID, 
        (select meta_value from wp_gf_entry_meta where meta_key='151' and entry_id=childID) as child_title, 
        (select meta_value from wp_gf_entry_meta where meta_key='22' and entry_id=childID) as child_photo 
        FROM wp_mf_lead_rel 
        left outer join wp_gf_entry child on wp_mf_lead_rel.childID = child.id  
        left outer join wp_gf_entry_meta child_mf_status on child.id = child_mf_status.entry_id and child_mf_status.meta_key = '303' 
        left outer join wp_gf_entry parent on wp_mf_lead_rel.parentID = parent.id 
        left outer join wp_gf_entry_meta parent_mf_status on parent.id = parent_mf_status.entry_id and parent_mf_status.meta_key = '303' 
        WHERE (parentID=$entryID or childID=$entryID) 
        AND child.status != 'trash' 
        AND parent_mf_status.meta_value='Accepted' 
        AND parent.status != 'trash' 
        AND child_mf_status.meta_value='Accepted'
        ORDER BY child_title";
        $results = $wpdb->get_results($sql);

        //pull child data
        foreach ($results as $row) {            
            $showcase_info['child_data'][] = array(
                'child_entryID' =>$row->childID,
                'child_photo' =>$row->child_photo,
                'child_title' =>$row->child_title);
        }
    }else{
        //pull parent data
        $parent_id = $results['parentID'];
        
        //pull parent information
        $childSQL = "SELECT parentID, 
                        (select meta_value from wp_gf_entry_meta where meta_key='111' and entry_id=parentID) as parent_photo, 
                        (select meta_value from wp_gf_entry_meta where meta_key='109' and entry_id=parentID) as parent_title, 
                        (select meta_value from wp_gf_entry_meta where meta_key='110' and entry_id=parentID) as parent_description 
                FROM wp_mf_lead_rel 
                WHERE parentID=$parent_id 
                LIMIT 1";
        $parent = $wpdb->get_row($childSQL);
        
        $showcase_info['parent_data'] = array(
            'parent_id'     => $parent_id,
            'parent_photo'  => $parent->parent_photo,
            'parent_title'  => $parent->parent_title,
            'parent_desc'   => $parent->parent_description
        );
    }

    return $showcase_info;
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
    $groupphoto = "";
    //For BA23, a change was made to only use field 217. 
    //If 111, group photo is set, use that. Else if 217, Maker Photo is set, use that
    if (isset($entry['111']) && $entry['111'] != '') {
        $groupphoto = $entry['111'];
    } elseif (isset($entry['217']) && $entry['217'] != '') {
        $groupphoto = $entry['217'];
    }
    //for BA24, the single photo was changed to a multi image which messed things up a bit
    $photo = json_decode($groupphoto);
    if (is_array($photo) && !empty($photo)) {
        $groupphoto = $photo[0];
    }

    $groupbio = (isset($entry['110']) ? $entry['110'] : '');
    $groupsocial = getSocial(isset($entry['828']) ? $entry['828'] : '');
    $groupwebsite = isset($entry['112']) ? $entry['112'] : '';

    // One maker
    // A list of makers (7 max)
    // A group or association
    $displayType = (isset($entry['105']) ? $entry['105'] : '');

    $isGroup = false;
    $isGroup = (stripos($displayType, 'group') !== false || stripos($displayType, 'team') !== false ? true : false);

    $makers = array();
    //set maker information
    if (isset($entry['160.3']) && $entry['160.3'] != "")
        $makers[1] = array('firstname' => $entry['160.3'], 'lastname' => $entry['160.6'],
            'bio' => (isset($entry['234']) ? preg_replace('/\\\\["\']/', '"', $entry['234']) : ''), //remove backslashes from urls in the description 
            'photo' => (isset($entry['217']) ? $entry['217'] : ''),
            'social' => getSocial(isset($entry['821']) ? $entry['821'] : ''),
            'website' => (isset($entry['209']) ? $entry['209'] : '')
        );
    if (isset($entry['158.3']) && $entry['158.3'] != "")
        $makers[2] = array('firstname' => $entry['158.3'], 'lastname' => $entry['158.6'],
            'bio' => (isset($entry['258']) ? preg_replace('/\\\\["\']/', '"', $entry['258']) : ''),
            'photo' => (isset($entry['224']) ? $entry['224'] : ''),
            'social' => getSocial(isset($entry['822']) ? $entry['822'] : ''),
            'website' => (isset($entry['216']) ? $entry['216'] : '')
        );
    if (isset($entry['155.3']) && $entry['155.3'] != "")
        $makers[3] = array('firstname' => $entry['155.3'], 'lastname' => $entry['155.6'],
            'bio' => (isset($entry['259']) ? preg_replace('/\\\\["\']/', '"', $entry['259']) : ''),
            'photo' => (isset($entry['223']) ? $entry['223'] : ''),
            'social' => getSocial(isset($entry['823']) ? $entry['823'] : ''),
            'website' => (isset($entry['215']) ? $entry['215'] : '')
        );
    if (isset($entry['156.3']) && $entry['156.3'] != "")
        $makers[4] = array('firstname' => $entry['156.3'], 'lastname' => $entry['156.6'],
            'bio' => (isset($entry['260']) ? preg_replace('/\\\\["\']/', '"', $entry['260']) : ''),
            'photo' => (isset($entry['222']) ? $entry['222'] : ''),
            'social' => getSocial(isset($entry['824']) ? $entry['824'] : ''),
            'website' => (isset($entry['214']) ? $entry['214'] : '')
        );
    if (isset($entry['157.3']) && $entry['157.3'] != "")
        $makers[5] = array('firstname' => $entry['157.3'], 'lastname' => $entry['157.6'],
            'bio' => (isset($entry['261']) ? preg_replace('/\\\\["\']/', '"', $entry['261']) : ''),
            'photo' => (isset($entry['220']) ? $entry['220'] : ''),
            'social' => getSocial(isset($entry['825']) ? $entry['825'] : ''),
            'website' => (isset($entry['213']) ? $entry['213'] : '')
        );
    if (isset($entry['159.3']) && $entry['159.3'] != "")
        $makers[6] = array('firstname' => $entry['159.3'], 'lastname' => $entry['159.6'],
            'bio' => (isset($entry['262']) ? preg_replace('/\\\\["\']/', '"', $entry['262']) : ''),
            'photo' => (isset($entry['221']) ? $entry['221'] : ''),
            'social' => getSocial(isset($entry['826']) ? $entry['826'] : ''),
            'website' => (isset($entry['211']) ? $entry['211'] : '')
        );
    if (isset($entry['154.3']) && $entry['154.3'] != "")
        $makers[7] = array('firstname' => $entry['154.3'], 'lastname' => $entry['154.6'],
            'bio' => (isset($entry['263']) ? preg_replace('/\\\\["\']/', '"', $entry['263']) : ''),
            'photo' => (isset($entry['219']) ? $entry['219'] : ''),
            'social' => getSocial(isset($entry['827']) ? $entry['827'] : ''),
            'website' => (isset($entry['212']) ? $entry['212'] : '')
        );
    // rather than have the page entry view have to do something different for groups, let's just replace all makers with the group
    if ($isGroup) {
        $makers = array(array(
            'firstname' => $groupname, 'lastname' => null,
            'bio' => preg_replace('/\\\\["\']/', '"', $groupbio),
            'photo' => $groupphoto,
            'social' => $groupsocial,
            'website' => $groupwebsite
        ));
    }
    // if we are not using the makers 1-7 and it isn't a group, the makers array will be empty and we should instead try pulling from the default first name / last name
    if (!$makers) {
        // deal with the maker photo possibly being a multi image
        $makerphoto = (isset($entry['217']) && $entry['217'] != '') ? $entry['217'] : "";
        $photo = json_decode($makerphoto);
        if (is_array($photo) && !empty($photo)) {
            $makerphoto = $photo[0];
        }
        $makers = array(array(
            'firstname' => (isset($entry['96.3']) ? $entry['96.3'] : ''),
            'lastname'  => (isset($entry['96.6']) ? $entry['96.6'] : ''),
            'bio'       => (isset($entry['234']) ? $entry['234'] : ''),
            'photo'     => $makerphoto,
            'social'    => getSocial(isset($entry['821']) ? $entry['821'] : ''),
            'website'   => (isset($entry['209']) ? $entry['209'] : ''),
        ));
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
        $socialArray = (is_serialized($entrySocial) ? unserialize($entrySocial) : array());

        $socialBlock = '<span class="social-links reversed">';

        //only show the first 3 social links entered
        foreach (array_slice($socialArray, 0, 3) as $link) {
            //verify that the social media link provided is not blank and is a valid url
            if ($link && isset($link['Your Link']) && $link['Your Link'] != '' && validate_url($link['Your Link'])) {
                //platform was misspelled as plateform in some earlier forms
                if (isset($link['Platform'])) {
                    $platform = $link['Platform'];
                } elseif (isset($link['Plateform'])) {
                    $platform = $link['Plateform'];
                }
                //$platform = (isset($link['Platform'])?$link['Platform']:isset($link['Plateform'])?$link['Plateform']:'');
                $socialBlock .= '<a target="_blank" href="' . $link['Your Link'] . '" aria-label="Check them out on ' . $platform . '" title="Check them out on ' . $platform . '"><span>' . $platform . '</span></a>';
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
        } else {
            $faire_location = '';
            $faire_link = '/' . $url_sub_path;
        }
    }

    // we're going to check if the schedule page exists
    //find the parent page
    $parentPage = get_page_by_path($url_sub_path . '/');
    $schedulePage = '';
    $mtmPage = '';
    if (isset($parentPage->ID)) {
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
    $child_entryID_array = (isset($entry['854']) ? explode(",", $entry['854']) : array()); //field 854 contains the makers, 852 contains the projects

    //get maker information
    $makers = array();

    foreach ($child_entryID_array as $child_entryID) {
        if ($child_entryID != $entryId) { //no need to process the entry we are looking at
            $child_entry = GFAPI::get_entry($child_entryID);

            if (!is_wp_error($child_entry) && $child_entry['form_id'] == 246) {
                $makers[] = array('firstname' => $child_entry['160.3'], 'lastname' => $child_entry['160.6'],
                    'bio' => (isset($child_entry['234']) ? preg_replace('/\\\\["\']/', '"', $child_entry['234']) : ''),
                    'photo' => (isset($child_entry['217']) ? $child_entry['217'] : ''),
                    'social' => getSocial(isset($child_entry['821']) ? $child_entry['821'] : ''),
                    'website' => (isset($child_entry['209']) ? $child_entry['209'] : '')
                );
            }
        }
    }

    return $makers;
}

// Functions to pull entries based on meta value
function gform_get_entry_byMeta( $meta_key, $meta_value ) {
    global $wpdb;
    $results  = $wpdb->get_results('SELECT entry_id FROM wp_gf_entry_meta where meta_key="'.$meta_key.'" and meta_value="'.$meta_value.'" ORDER BY wp_gf_entry_meta.id DESC');
    $entry_id = isset( $results[0] ) ? $results[0]->entry_id : 0;
    
    return $entry_id;
}
