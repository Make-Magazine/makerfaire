<?php
/* adds a custom REST API endpoint of makerfaire */
add_action('rest_api_init', function () {
    //get faire data such as Meet the Makers and Schedule information based on form id(s) or faire year
    register_rest_route('makerfaire', '/v2/fairedata/(?P<type>[a-z0-9\-]+)/(?P<dataids>[a-z0-9\-]+)', array(
        'methods' => 'GET',
        'callback' => 'mf_fairedata',
        'permission_callback' => '__return_true'
    ));
    register_rest_route('makerfaire', '/v2/fairedata/(?P<type>[a-z0-9\-]+)/(?P<dataids>[a-z0-9\-]+)/(?P<faireid>[a-z0-9\-]+)', array(
        'methods' => 'GET',
        'callback' => 'mf_fairedata',
        'permission_callback' => '__return_true'
    ));

    //get ribbon information by year
    register_rest_route('makerfaire', '/v2/mfRibbons/(?P<year>[a-z0-9\-]+)', array(
        'methods' => 'GET',
        'callback' => 'mf_ribbons',
        'permission_callback' => '__return_true'
    ));

    //update an entry
    register_rest_route('makerfaire', '/v2/entry/(?P<type>[a-z0-9\-]+)/(?P<entryid>[a-z0-9\-]+)', array(
        'methods' => 'GET',
        'callback' => 'mf_updateEntry',
        'permission_callback' => '__return_true'
    ));

    //makerfaire api for makershare
    register_rest_route('makerfaire', '/v2/mfapi/(?P<type>[a-z0-9\-]+)/(?P<key>[a-z0-9\-]+)', array(
        'methods' => 'GET',
        'callback' => 'mf_mfapi',
        'permission_callback' => '__return_true'
    ));
});

function mf_mfapi(WP_REST_Request $request) {
    // Define the API version.
    define('MF_API_VERSION', 'v3');

    // Set the post per page for our queries
    define('MF_POSTS_PER_PAGE', 2000);

    // Set the API keys to run this API.
    //define( 'MF_API_KEY', sanitize_text_field( get_option( 'make_app_api_key' ) ) );
    // MIIBOgIBAAJBAKUSW7BsZCFVHarjMixDfaVy6OjB2QKtlfMoNQEafDW%2FJx%2BZzWBY
    define('MF_API_KEY', 'f22c7aba-5c1e-478f-9513-09aa7e28105d');
    $allowed_types = array(
        'category',
        'account',
        'project'
    );

    $type = $request['type'];
    $key = $request['key'];
    if (empty($key)) {
        header('HTTP/1.0 403 Forbidden');
        echo '<h2>Invalid: No Key.</h2>';

        return;
    } elseif ($key !== MF_API_KEY) {
        header('HTTP/1.0 403 Forbidden');

        echo '<h2>Invalid: Parameter Not Valid - "' . esc_html($_REQUEST['key']) . '"</h2>';
        return;
    } elseif (empty($type)) {
        header('HTTP/1.0 403 Forbidden');

        echo '<h2>Invalid: Type</h2>' . $type;
        return;
    } elseif (!in_array($type, $allowed_types)) {
        header('HTTP/1.0 403 Forbidden');

        echo '<h2>Invalid: Parameter Not Valid - "' . esc_html($_REQUEST['type']) . '"</h2>';
        return;
    } else {
        header('HTTP/1.1 200 OK');
    } /* elseif ( empty( $faire ) ) {
      header( 'HTTP/1.0 403 Forbidden' );

      echo '<h2>Invalid: Faire</h2>';
      return;
      } */


    /**
     * RUN THE CONTROLLER
     * Process the passed query string and fetch the appropriate API section
     */
    // Get the appropriate API file.
    $api_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/makerfaire/api/' . sanitize_title(MF_API_VERSION) . '/' . sanitize_title($type) . '/index.php';



    // Prevent Path Traversal
    if (strpos($api_path, '../') !== false || strpos($api_path, "..\\") !== false || strpos($api_path, '/..') !== false || strpos($api_path, '\..') !== false)
        return;

    // Make sure the api file exists...
    //if ( ! file_exists( $api_path ) )
    if (!file_exists($api_path))
        return;

    // Set the JSON header
    header('Content-type: application/json');
    //print_r($api_path);
    //return;
    // Load the file and process everything
    include_once($api_path);
}

function mf_ribbons(WP_REST_Request $request) {
    $year = $request['year'];
    $data['ribbons'] = getRibbons($year);
    wp_send_json($data);
    exit;
}

function mf_updateEntry(WP_REST_Request $request) {
    $type = $request['type'];
    $entry_id = $request['entryid'];
    if ($type == 'accept') {
        wp_set_current_user(20);
        $current_user = wp_get_current_user();

        $lead = GFAPI::get_entry($entry_id);
        $form_id = isset($lead['form_id']) ? $lead['form_id'] : 0;
        $form = RGFormsModel::get_form_meta($form_id);
        $_POST['entry_info_status_change'] = 'Accepted';
        set_entry_status($lead, $form);
    }
}

function mf_fairedata(WP_REST_Request $request) {
    $type = $request['type'];
    $dataids = $request['dataids'];
    $faireID = $request['faireid'];

    if ($type != '' && $dataids != '') {
        $data = array();
        switch ($type) {
            case 'mtm':
                $data = getMTMentries($dataids, $faireID);
                break;
            case 'makerDir':
                $data = getMakerDirEntries($dataids);
                break;
            case 'categories':
                $data = getCategories($dataids);
                break;
            case 'schedule':
                $data = getSchedule($dataids, $faireID);
                break;
        }
    } else {
        $data['error'] = 'Error: Type or data ids not submitted';
    }

    wp_send_json($data);
    exit;
}
function getMakerDirEntries($years) {
    $data['entity'] = array();
    $yearsArr = array_map('intval', explode("-", $years));

    global $wpdb;
    // Data to return title, public_desc, project_photo, main_category, faire_name, faire_year, maker_list
    //find all active entries for selected years
    $query = "select blog_id, entry_id, title, public_desc, project_photo, main_category, category, faire_name, faire_year,
                (select concat(first_name, ' ', last_name)
                    from wp_mf_dir_maker_to_entry maker2entry 
                    left outer join wp_mf_dir_maker maker 
                        on maker2entry.maker_id = maker.maker_id 
                    where maker2entry.blog_id = wp_mf_dir_entry.blog_id and maker2entry.entry_id = wp_mf_dir_entry.entry_id 
                    and maker2entry.maker_type <> 'contact'
                order by maker_type limit 1) as maker, entry_link
                from wp_mf_dir_entry where faire_year in(" . implode(",", $yearsArr) . ") and LOWER(status)='accepted'";

    $results = $wpdb->get_results($query);

    //build entry array
    $entries = array();
    foreach ($results as $result) {

        $entry_id = $result->entry_id;
        $makerList = $result->maker;

        //project photo
        $projPhoto = $result->project_photo;
        $fitPhoto = legacy_get_resized_remote_image_url($projPhoto, 275, 266);

        // Check to see if the fit photo returned an image
        if ($fitPhoto == NULL)
            $fitPhoto = $projPhoto;

        //get array of categories. set name based on category id
        $category = array();

        $leadCategory = explode(',', $result->category);
        foreach ($leadCategory as $leadCat) {
            $value = html_entity_decode($leadCat, ENT_QUOTES);
            if ($value != '') $category[] = $value;
        }
        $primeCat = html_entity_decode($result->main_category, ENT_QUOTES);
        if ($primeCat != '')   $category[] = $primeCat;

        $desc = substr(html_entity_decode($result->public_desc, ENT_QUOTES), 0, 75);
        $data['entity'][] = array(
            'id' => $entry_id,
            'link' => $result->entry_link,
            'name' => substr(html_entity_decode($result->title, ENT_QUOTES), 0, 35),
            'large_img_url' => $projPhoto,
            'categories' => (array) $primeCat,
            'description' => $desc,
            'faire_year' => $result->faire_year,
            'faire_name' => trim($result->faire_name),
            'blog_id' =>    $result->blog_id,
            'makerList' => $makerList
        );
    }

    //randomly order entries
    shuffle($data['entity']);

    return $data;
}
//end getMakerDirEntries

function getMTMentries($formIDs = '', $faireID = '', $years = '') {
    global $wpdb;
    $data['entity'] = array();

    $formIDarr = array_map('intval', explode("-", $formIDs));

    //find if the show location switch is turned on
    $showLoc = false;

    /*  For Bay Area 23 we forced everyone to not show location    
    $query = "select show_sched from wp_mf_faire where faire = '" . $faireID . "'";

    $show_sched = $wpdb->get_var($query);
    
    //var_dump($result);
    if ($show_sched === "1")
        $showLoc = true;
      */

    //find all active entries for selected forms
    $query = "SELECT  entry.id                         AS entry_id, 
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '303'   AND entry_id = entry.id limit 1) AS entry_status, 
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '22'    AND entry_id = entry.id limit 1) AS proj_photo,
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '878'   AND entry_id = entry.id limit 1) AS proj_photo_gallery,  
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '217'   AND entry_id = entry.id limit 1) AS maker_photo,  
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '111'   AND entry_id = entry.id limit 1) AS group_photo,    
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '101.4' AND entry_id = entry.id limit 1) AS state,  
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '101.6' AND entry_id = entry.id limit 1) AS country,  
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '151'   AND entry_id = entry.id limit 1) AS proj_name, 
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '16'    AND entry_id = entry.id limit 1) AS short_desc, 
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '320'   AND entry_id = entry.id limit 1) AS prime_cat, 
                     (SELECT Group_concat(meta_value) FROM   wp_gf_entry_meta WHERE  meta_key LIKE '339.%' AND entry_id = entry.id GROUP  BY entry_id) AS types, 
                     (SELECT Group_concat(meta_value) FROM   wp_gf_entry_meta WHERE  meta_key LIKE '321.%' AND entry_id = entry.id GROUP  BY entry_id) AS second_cat, 
                     (SELECT Group_concat(meta_value) FROM   wp_gf_entry_meta WHERE  meta_key LIKE '304.%' AND entry_id = entry.id GROUP  BY entry_id) AS flags,
                     (SELECT Group_concat(meta_value) FROM   wp_gf_entry_meta WHERE  meta_key LIKE '879.%' AND entry_id = entry.id GROUP  BY entry_id) AS weekends, 
                     (SELECT Group_concat(wp_mf_faire_area.area)      
                      FROM `wp_mf_location` 
                      left outer join wp_mf_faire_subarea on wp_mf_location.subarea_id = wp_mf_faire_subarea.id 
                      left outer join wp_mf_faire_area on wp_mf_faire_subarea.area_id = wp_mf_faire_area.id 
                      WHERE wp_mf_location.entry_id = entry.id
                      GROUP  BY entry_id) AS area,
                      (SELECT Group_concat(wp_mf_faire_subarea.nicename)      
                      FROM `wp_mf_location` 
                      left outer join wp_mf_faire_subarea on wp_mf_location.subarea_id = wp_mf_faire_subarea.id 
                      WHERE wp_mf_location.entry_id = entry.id
                      GROUP  BY entry_id) AS subarea
              FROM   wp_gf_entry AS entry 
              WHERE  entry.status = 'active' 
                     AND entry.form_id IN(" . implode(",", $formIDarr) . ")";

    //legacy faires have location set, virtual faires do not. 
    if ($faireID == 'VMF2020') {
        $query = "SELECT  entry.id                         AS entry_id, 
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '303' AND entry_id = entry.id) AS entry_status, 
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '22'  AND entry_id = entry.id) AS proj_photo,                     
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '151' AND entry_id = entry.id) AS proj_name, 
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '16'  AND entry_id = entry.id) AS short_desc, 
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '320' AND entry_id = entry.id) AS prime_cat, 
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '321' AND entry_id = entry.id) AS types, 
                     (SELECT Group_concat(meta_value) FROM   wp_gf_entry_meta WHERE  meta_key LIKE '321.%' AND entry_id = entry.id GROUP  BY entry_id) AS second_cat, 
                     (SELECT Group_concat(meta_value) FROM   wp_gf_entry_meta WHERE  meta_key LIKE '304.%' AND entry_id = entry.id GROUP  BY entry_id) AS flags,
                     (SELECT meta_value FROM   wp_gf_entry_meta WHERE  meta_key = '320' AND entry_id = entry.id) AS area
              FROM   wp_gf_entry AS entry 
              WHERE  entry.status = 'active' 
                     AND entry.form_id IN(" . implode(",", $formIDarr) . ")";
    }

    $results = $wpdb->get_results($query);

    //build entry array
    $entries = array();
    foreach ($results as $result) {
        //check if the flag 'no-public-view' is set. if it is, do not return this entry
        $flags = isset($result->flags) ? explode(",", $result->flags) : array();

        //only return Accepted entries and entries who do not have the 'no-public-view' flag set
        if ($result->entry_status === 'Accepted' && !in_array('no-public-view', $flags)) {

            $flag = (in_array('Featured Maker', $flags) ? 'Featured Maker' : '');
            $handson = (in_array('Featured HandsOn', $flags) ? 'Featured HandsOn' : '');

            //project photo
            $projPhoto = isset($result->proj_photo) ? $result->proj_photo : "";
            //for BA24, the single photo was changed to a multi image which messed things up a bit
            $photo = json_decode($projPhoto);
            if (is_array($photo)) {
                $projPhoto = $photo[0];
            }

            //find out if there is an override image for this page
            $overrideImg = findOverride($result->entry_id, 'mtm');
            if ($overrideImg != '')
                $projPhoto = $overrideImg;

            //if the main project photo isn't set but the photo gallery is, use the first image in the photo gallery            
            $project_gallery = explode(",", str_replace(array('[', ']', '"'), '', $result->proj_photo_gallery ?? ''));
            if ($projPhoto == '' && is_array($project_gallery)) {
                $projPhoto = $project_gallery[0];
            }

            $largePhoto = legacy_get_resized_remote_image_url($projPhoto, 435, 290);
            $smallPhoto = legacy_get_resized_remote_image_url($projPhoto, 300, 200);

            $makerList = getMakerList($result->entry_id, $faireID);

            //get array of categories. set name based on category id
            $category = array();

            $leadCategory = isset($result->second_cat) ? explode(',', $result->second_cat) : array();
            foreach ($leadCategory as $leadCat) {
                $value = htmlspecialchars_decode(get_CPT_name($leadCat));
                if ($value != '') $category[] = $value;
            }
            // handle cases where no main category was set
            if ($result->prime_cat == "-- select a makerfaire category --") {
                $result->prime_cat = "";
            }
            $primeCat = isset($result->prime_cat) ? htmlspecialchars_decode(get_CPT_name($result->prime_cat)) : '';
            if ($primeCat != '')   array_unshift($category, $primeCat); // add the primary category to the start of the array

            $mainCategory = get_term($result->prime_cat);
            $mainCategoryIcon = '<i class="fa fa-rocket" aria-hidden="true"></i>';
            if(isset($mainCategory->taxonomy)) {
                $mainCategoryIconType = get_field('icon_type', $mainCategory->taxonomy . '_' . $mainCategory->term_id);
                // get the mainCategory icon from the mf category taxonomy, if indeed one is set
                if ($mainCategoryIconType == "uploaded_icon") {
                    $mainCategoryIcon = '<picture class="main-category-icon"><img src="' . get_field('uploaded_icon', $mainCategory->taxonomy . '_' . $mainCategory->term_id)['url'] . '" height="27px" width="27px" aria-hidden="true" /></picture>';
                } else {
                    $fa = get_field('font_awesome', $mainCategory->taxonomy . '_' . $mainCategory->term_id);
                    if (!empty($fa)) {
                        $mainCategoryIcon = '<a href="?category='.$primeCat.'" aria-label="Project Category" title="Project Category Icon"><i class="fa ' . $fa . '" aria-hidden="true"></i></a>';
                    }
                }
            }
            
            // Maker / Group Photos 
            $group_photo = (isset($result->group_photo)?$result->group_photo:'');
            $maker_photo = (isset($result->maker_photo) && $result->maker_photo != "[]" ? $result->maker_photo : $group_photo);                        
            
            $maker_photo_decoded = json_decode($maker_photo);
            if (is_array($maker_photo_decoded)) {
                $maker_photo = isset($maker_photo_decoded[0]) ? $maker_photo_decoded[0] : "";
            }
             
            if(empty($maker_photo)) {
                $maker_photo = isset($projPhoto) ? $projPhoto : "/wp-content/themes/makerfaire/images/default-makey-medium.jpg";
            }
            $maker_photo = legacy_get_resized_remote_image_url($maker_photo, 400, 400);

            $maker_location = (isset($result->state) ? $result->state:'') . 
                (isset($result->state)&& isset($result->country)? ", ":'') .
                (isset($result->country) ? $result->country : '');

            //Admin entry types (only for BA23 and forward)
            $types = explode(",", $result->types);
            if (($key = array_search("Show Management", $types)) !== false) {
                unset($types[$key]);
            }

            //replace Startup Sponsor with Exhibit
            array_walk_recursive(
                $types,
                function (&$value) {
                    $value = str_ireplace('Startup Sponsor', 'Exhibit', $value);
                }
            );

            //Replace Sponsor with Exhibit
            array_walk_recursive(
                $types,
                function (&$value) {
                    $value = str_ireplace('Sponsor', 'Exhibit', $value);
                }
            );
            
            //weekends               
            if ($faireID=='BA23' && isset($result->weekends)) {
                $weekends = explode(',', $result->weekends);
                foreach ($weekends as &$weekend) {
                    if ($weekend == 'Wk1' || $weekend == 'Fri1') {
                        $weekend = 'First Weekend';
                    } elseif ($weekend == 'Wk2' || $weekend == 'Fri2') {
                        $weekend = 'Second Weekend';
                    }
                }
            } else {
                $weekends = '';
            }

            //don't return location information if the show location isn't set

            //$location = ($showLoc?($result->area==NULL?'':$result->area):'');
            $locations = array();
            if ($showLoc && $result->area != NULL) {
                $locations = array_unique(explode(',', $result->subarea));
            }
            if ($faireID == 'VMF2020') {
                $locations = array(html_entity_decode(get_CPT_name($result->area)));
            }

            if ($locations == NULL)
                $locations = '';
            
            $data['entity'][] = array(
                'id' => $result->entry_id,
                'link' => '/maker/entry/' . $result->entry_id,
                'name' => $result->proj_name,
                'large_img_url' => $largePhoto,
                'small_img_url' => $smallPhoto,
                'categories' => $category,
                'main_cat_icon' => $mainCategoryIcon,
                'types' => array_unique($types),
                'description' => $result->short_desc,
                'flag' => $flag, //only set if flag is set to 'Featured Maker'
                'handson' => $handson, //only set if handson is set to 'Featured Handson'
                'makerList' => $makerList,
                'maker_photo' => $maker_photo,
                'maker_location' => $maker_location,
                'location' => $locations,
                'weekend' => $weekends
            );
        }
    }

    //randomly order entries
    shuffle($data['entity']);

    return $data;
}
//end getMTMentries

function getCategories($formIDs) {
    $data = array();
    $formIDarr = array_map('intval', explode("-", $formIDs));

    foreach ($formIDarr as $form_id) {
        $form = GFAPI::get_form($form_id);
        if (is_array($form['fields'])) {
            foreach ($form['fields'] as $field) {
                if ($field->id == 320) {
                    foreach ($field->choices as $choice) {
                        if ($choice['value'] != '') {
                            $data['category'][] = array('id' => absint($choice['value']), 'name' => html_entity_decode(esc_js($choice['text'])));
                        }
                    }
                }
                if ($field->id == 321) {
                    // var_dump($field);
                }
            }
        }
    }
    return $data;
}

function getSchedule($formIDs, $faireID) {
    global $wpdb;
    $data = array();
    $data['schedule'] = array();
    $formIDarr = array_map('intval', explode(",", $formIDs));
   

$query = "SELECT schedule.entry_id,
	schedule.start_dt as time_start, DAYOFWEEK(schedule.start_dt) as day,
	schedule.end_dt as time_end, schedule.type,
    
    (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = schedule.entry_id and meta_key='96.3') as maker_fname,
    (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = schedule.entry_id and meta_key='96.6') as maker_lname,

    (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = schedule.entry_id and meta_key='109') as group_name,
    (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = schedule.entry_id and meta_key='916') as presenter_list,      
	entity.form_type, entity.status as entry_status,
	subarea.subarea, subarea.nicename, subarea.sort_order,
	entity.project_photo as photo, entity.presentation_title as title, entity.desc_short as short_desc,
	(select group_concat(meta_value separator',')as cat 
     	from wp_gf_entry_meta 
     	where wp_gf_entry_meta.entry_id=schedule.entry_id 
     	AND wp_gf_entry_meta.meta_key like '304.%')as flags,
	entity.category as category 
    
FROM wp_mf_schedule as schedule 
	left outer join wp_mf_location 		location  	on location_id		 = location.id 
	left outer join wp_mf_faire_subarea subarea 	on subarea.id		 = location.subarea_id 
    left outer join wp_gf_entry 		entry 		on entry.id 		 = schedule.entry_id
    left outer join wp_mf_entity 		entity 		on entity.lead_id = schedule.entry_id    

where   entry.status='active' 
    and entry.form_id in(" . implode(",", $formIDarr) . ") 
    and schedule.faire ='$faireID' 
    and subarea.subarea is not null ".
 //" and schedule.end_dt >= now()+ INTERVAL -7 HOUR   
 " order by start_dt, end_dt;";
//echo $query;
    $schedule = $wpdb->get_results($query);

    //retrieve schedule information
    foreach ($schedule as $row) {
        //if this entry is not accepted, move along to the next record
        if($row->entry_status != 'Accepted')  continue;        
        $form_type = $row->form_type;

        //determine presenter names
        $maker_name = $row->maker_fname . ($row->maker_lname!=''?' '.$row->maker_lname:'');
        $group_name = $row->group_name; 
        $presenter_list = isset($row->presenter_list) ? unserialize($row->presenter_list) : '';
      
        //if presenter list is set, use this instead of the maker name
        if(is_array($presenter_list)){         
            $maker_name = implode(", ", $presenter_list);
        }        
         
        $makerList = $maker_name . ($group_name!=''?' - '.$group_name:'');
        
        //get array of categories. set name based on category id
        $category = array();
        $leadCategory = explode(',', $row->category);
        foreach ($leadCategory as $leadCat) {
            $category[] = htmlspecialchars_decode(get_CPT_name($leadCat));
        }

        $catList = implode(',', $category);
        
        $projPhoto = $row->photo;
        //for BA24, the single photo was changed to a multi image which messed things up a bit
        $photo = json_decode($projPhoto);
        if (is_array($photo)) {
            $projPhoto = $photo[0];
        }
        
        //find out if there is an override image for this page
        $overrideImg = findOverride($row->entry_id, 'schedule');
        if ($overrideImg != '')
            $projPhoto = $overrideImg;

        //if the main project photo isn't set but the photo gallery is, use the first image in the photo gallery            
        $project_gallery = explode(",", str_replace(array('[', ']', '"'), '', $row->proj_photo_gallery ?? ''));
        if ($projPhoto == '' && is_array($project_gallery)) {
            $projPhoto = $project_gallery[0];
        }

        $fitPhoto = legacy_get_resized_remote_image_url($projPhoto, 300, 200);
        if ($fitPhoto == NULL)
            $fitPhoto = $projPhoto;

        //format start and end date
        $startDay  = date_create($row->time_start);
        $startDate = date_format($startDay, 'Y-m-d') . 'T' . date_format($startDay, 'H:i:s');        

        $endDate = date_create($row->time_end);
        $endDate = date_format($endDate, 'Y-m-d') . 'T' . date_format($endDate, 'H:i:s');

        //set default values for schedule type if not set
        if ($row->type == '') {
            //demo, performance, talk, workshop
            if ($form_type == 'Performance') {
                $type = 'performance';
            } else {
                $type = 'talk';
            }
        } else {
            $type = $row->type;
        }
        //featured maker    
        $featured = '';
        if (strpos($row->flags ?? '', "Featured Maker") === 0) {
            $featured = 'Featured';
        }
        
        //set default values for schedule type if not set
        if (strpos($faireID ?? '', "VMF") === 0) { // special for virtual faires
            if ($row->type == 'talk' || $row->type == '') {
                $type = 'presentation';
            } else if ($row->type == 'demo') {
                $type = 'demonstration';
            }

            $linkedSQL = 'select entry_id from wp_gf_entry_meta where meta_key="entry_id" and meta_value = ' . $row->entry_id;

            $linked_results = $wpdb->get_results($linkedSQL, ARRAY_A);
            foreach ($linked_results as $linked_result) {
                if (isset($linked_result['entry_id'])) {
                    $linked_entryID = $linked_result['entry_id'];
                    $linked_entry = GFAPI::get_entry($linked_entryID);
                    $registration = (isset($linked_entry['829']) && $linked_entry['829'] != '' ? $linked_entry['829'] : '');
                    $viewNow = (isset($linked_entry['52']) && $linked_entry['52'] != '' ? $linked_entry['52'] : '');
                }
            }
        }
        //$registration =''; //post faire - return blank for registration link

        //set stage name
        $stage = ($row->nicename != '' ? $row->nicename : $row->subarea);
        
        // only include in the schedule if the schedtype is not workshop
        /*if($type == "workshop"){
            continue;
        } */
        
        $data['schedule'][] = array(
            'id'            => $row->entry_id,
            'time_start'    => $startDate,
            'time_end'      => $endDate,
            'name'          => isset($row->title) ? htmlspecialchars_decode($row->title, ENT_QUOTES) : '',
            'thumb_img_url' => $fitPhoto,            
            'maker_list'    => $makerList,
            'nicename'      => $stage,
            'stageClass'    => str_replace(' ', '-', strtolower($stage) ?? ''),
            'stageOrder'    => (int) ($row->sort_order != '' ? $row->sort_order : 0),
            'category'      => $catList,
            'day'           => date_format($startDay, 'l'),
            'hour'          => date_format($startDay, 'g:00 A'),
            'desc'          => isset($row->short_desc) ? htmlspecialchars_decode($row->short_desc, ENT_QUOTES) : '',
            'type'          => ucwords($type),
            //'flags'         => $row->flags,
            'featured'      => $featured,
            'additional'    => ''
        );
    }
    //error_log(print_r($data, TRUE));

    return $data;
}

function getMakerList($entryID, $faireID) {
    $makerList = '';
    $data = array();
    global $wpdb;

    if ($faireID == 'VMF2020') {
        $entry = GFAPI::get_entry($entryID);
        if (isset($entry['gpnf_entry_parent']) && $entry['gpnf_entry_parent'] != '') {
            //get parent information
            $parent_entry_ID = $entry['gpnf_entry_parent'];
            $parent_entry = GFAPI::get_entry($parent_entry_ID);
            if (is_wp_error($parent_entry)) {
                //echo 'there is an error';
                //var_dump($parent_entry);
                return array();
            } else {
                //pull group information from parent
                if (isset($parent_entry['844']) && $parent_entry['844'] == 'Yes') {
                    $isGroup = true;
                    $groupname = (isset($parent_entry['109']) ? $parent_entry['109'] : '');
                    $makerList .= "<p class='group-name'>" . $groupname . "</p>";
                }
            }
            //get maker information
            $makers = array();

            $child_entryID_array = explode(",", $parent_entry[854]);

            // pull the individual maker names... or at least the first one!
            foreach ($child_entryID_array as $child_entryID) {
                if ($child_entryID != $entryID) { //no need to process the entry we are looking at
                    $child_entry = GFAPI::get_entry($child_entryID);
                    //error_log(print_r($child_entry, TRUE));
                    if (!is_wp_error($child_entry) && $child_entry['form_id'] == 246) {
                        $makers = array('firstname' => $child_entry['160.3'], 'lastname' => $child_entry['160.6']);
                        $makerList .= implode(" ", $makers) . ", ";
                    }
                }
            }
            if (!$child_entryID_array[0] || $child_entryID_array[0] == "")
                $makerList = rtrim($makerList, ":");
            $makerList = rtrim($makerList, ", ");
        }
    } else {
        $query = "SELECT *
              FROM wp_gf_entry_meta as lead_detail
              where lead_detail.entry_id = $entryID "
            . "and cast(meta_key as char) in('96.3','96.6', '160.3', '160.6', '158.3', '158.6', '155.3', '155.6', "
            . "'156.3', '156.6', '157.3', '157.6', '159.3', '159.6', '154.3', '154.6', '109', '105')";
        $entryData = $wpdb->get_results($query);
        //field 105 - who would you like listed
        //    one maker, a group or association, a list of makers
        /* Maker Name field #'s -> 1 - 160, 2 - 158, 3 - 155, 4 - 156, 5 - 157, 6 - 159, 7 - 154
         * Group Name - 109
         */
        $fieldData = array();
        foreach ($entryData as $field) {
            $fieldData[$field->meta_key] = $field->meta_value;
        }

        
            $whoListed = (isset($fieldData['105'])?strtolower($fieldData['105']):'');
            $isGroup = (stripos($whoListed, 'group') !== false || stripos($whoListed, 'team') !== false ? true : false);            

            if ($isGroup) {
                $makerList = (isset($fieldData['109']) ? $fieldData['109'] : '');
            } else {
                $makerArr = array();
                if (isset($fieldData['160.3']))
                    $makerArr[] = (isset($fieldData['160.3']) ? $fieldData['160.3'] . ' ' : '') . (isset($fieldData['160.6']) ? $fieldData['160.6'] : '');
                if (isset($fieldData['158.3']))
                    $makerArr[] = $fieldData['158.3'] . ' ' . (isset($fieldData['158.6']) ? $fieldData['158.6'] : '');
                if (isset($fieldData['155.3']))
                    $makerArr[] = $fieldData['155.3'] . ' ' . (isset($fieldData['155.6']) ? $fieldData['155.6'] : '');
                if (isset($fieldData['156.3']))
                    $makerArr[] = $fieldData['156.3'] . ' ' . (isset($fieldData['156.6']) ? $fieldData['156.6'] : '');
                if (isset($fieldData['157.3']))
                    $makerArr[] = $fieldData['157.3'] . ' ' . (isset($fieldData['157.6']) ? $fieldData['157.6'] : '');
                if (isset($fieldData['159.3']))
                    $makerArr[] = $fieldData['159.3'] . ' ' . (isset($fieldData['159.6']) ? $fieldData['159.6'] : '');
                if (isset($fieldData['154.3']))
                    $makerArr[] = $fieldData['154.3'] . ' ' . (isset($fieldData['154.6']) ? $fieldData['154.6'] : '');

                //starting with BA24, we moved away from using Maker 1 name, to using primary contact name
                if (empty($makerArr)) {
                    if (isset($fieldData['96.3']))
                        $makerArr[] = $fieldData['96.3'] . ' ' . (isset($fieldData['96.6']) ? $fieldData['96.6'] : '');
                }
                $makerList = implode(", ", $makerArr);
            }
        
    }

    return $makerList;
}
