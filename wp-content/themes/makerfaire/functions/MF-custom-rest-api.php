<?php

/* adds a custom REST API endpoint of makerfaire */
add_action('rest_api_init', function () {
   //get faire data such as Meet the Makers and Schedule information based on form id(s)
   register_rest_route('makerfaire', '/v2/fairedata/(?P<type>[a-z0-9\-]+)/(?P<formids>[a-z0-9\-]+)', array(
       'methods' => 'GET',
       'callback' => 'mf_fairedata'
   ));
   register_rest_route('makerfaire', '/v2/fairedata/(?P<type>[a-z0-9\-]+)/(?P<formids>[a-z0-9\-]+)/(?P<faireid>[a-z0-9\-]+)', array(
       'methods' => 'GET',
       'callback' => 'mf_fairedata'
   ));

   //get ribbon information by year
   register_rest_route('makerfaire', '/v2/mfRibbons/(?P<year>[a-z0-9\-]+)', array(
       'methods' => 'GET',
       'callback' => 'mf_ribbons'
   ));

   //update an entry
   register_rest_route('makerfaire', '/v2/entry/(?P<type>[a-z0-9\-]+)/(?P<entryid>[a-z0-9\-]+)', array(
       'methods' => 'GET',
       'callback' => 'mf_updateEntry'
   ));

   //makerfaire api for makershare
   register_rest_route('makerfaire', '/v2/mfapi/(?P<type>[a-z0-9\-]+)/(?P<key>[a-z0-9\-]+)', array(
       'methods' => 'GET',
       'callback' => 'mf_mfapi'
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
   include_once( $api_path );
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
   $type    = $request['type'];
   $formIDs = $request['formids'];
   $faireID = $request['faireid'];
   
   if ($type != '' && $formIDs != '') {
      $data = array();
      switch ($type) {
         case 'mtm':
            $data = getMTMentries($formIDs,$faireID);            
            break;
         case 'categories':
            $data = getCategories($formIDs);
            break;
         case 'schedule':
            $schedule = getSchedule($formIDs);
            $category = getCategories($formIDs);
            $data = array_merge($schedule, $category);
            break;
      }
   } else {
      $data['error'] = 'Error: Type or Form IDs not submitted';
   }

   wp_send_json($data);
   exit;
}

function getMTMentries($formIDs,$faireID) {   
   $data['entity'] = array();
   $formIDarr = array_map('intval', explode("-", $formIDs));

   global $wpdb;
   
   //find if the show location switch is turned on
   $showLoc = false;
   $query = "select show_sched from wp_mf_faire where faire = '".$faireID."'";
   
   $result = $wpdb->get_row($query);
   //var_dump($result);
   if($result->show_sched==="1") $showLoc=true;         
   
   //find all active entries for selected forms
   $query = "SELECT  lead.id                         AS entry_id, 
                     (SELECT meta_value 
                      FROM   wp_gf_entry_meta 
                      WHERE  meta_key = 303 
                             AND entry_id = lead.id) AS entry_status, 
                     (SELECT meta_value 
                      FROM   wp_gf_entry_meta 
                      WHERE  meta_key = 22 
                             AND entry_id = lead.id) AS proj_photo, 
                     (SELECT meta_value 
                      FROM   wp_gf_entry_meta 
                      WHERE  meta_key = 151 
                             AND entry_id = lead.id) AS proj_name, 
                     (SELECT meta_value 
                      FROM   wp_gf_entry_meta 
                      WHERE  meta_key = 16 
                             AND entry_id = lead.id) AS short_desc, 
                     (SELECT meta_value 
                      FROM   wp_gf_entry_meta 
                      WHERE  meta_key = 320 
                             AND entry_id = lead.id) AS prime_cat, 
                     (SELECT Group_concat(meta_value) 
                      FROM   wp_gf_entry_meta 
                      WHERE  meta_key LIKE'%321%' 
                             AND entry_id = lead.id 
                      GROUP  BY entry_id)            AS second_cat, 
                     (SELECT Group_concat(meta_value) 
                      FROM   wp_gf_entry_meta 
                      WHERE  meta_key LIKE'%304%' 
                             AND entry_id = lead.id 
                      GROUP  BY entry_id)            AS flags,
                     (SELECT Group_concat(wp_mf_faire_area.area)      
                      FROM `wp_mf_location` 
                      left outer join wp_mf_faire_subarea on wp_mf_location.subarea_id = wp_mf_faire_subarea.id 
                      left outer join wp_mf_faire_area on wp_mf_faire_subarea.area_id = wp_mf_faire_area.id 
                      WHERE wp_mf_location.entry_id = lead.id
                      GROUP  BY entry_id) AS area,
                     (SELECT Group_concat(wp_mf_faire_subarea.nicename)
                      FROM `wp_mf_location` 
                      left outer join wp_mf_faire_subarea on wp_mf_location.subarea_id = wp_mf_faire_subarea.id 
                      left outer join wp_mf_faire_area on wp_mf_faire_subarea.area_id = wp_mf_faire_area.id 
                      WHERE wp_mf_location.entry_id = lead.id
                      GROUP  BY entry_id) AS subarea 
              FROM   wp_gf_entry AS lead 
              WHERE  lead.status = 'active' 
                     AND lead.form_id IN(" . implode(",", $formIDarr) . ")";
   
   $results = $wpdb->get_results($query);

   //build entry array
   $entries = array();
   foreach ($results as $result) {      
      //check if the flag 'no-public-view' is set. if it is, do not return this entry
      $flags = explode(",", $result->flags);
      
      //only return Accepted entries and entries who do not have the 'no-public-view' flag set
      if($result->entry_status === 'Accepted' && !in_array('no-public-view', $flags)){
         $flag = (in_array('Featured Maker', $flags)?'Featured Maker':'');
         
         //project photo
         $projPhoto = $result->proj_photo;
         $fitPhoto = legacy_get_resized_remote_image_url($projPhoto, 350, 350);
         
         if ($fitPhoto == NULL)
            $fitPhoto = $projPhoto;
          
         //find out if there is an override image for this page
         $overrideImg = findOverride($result->entry_id, 'mtm');
         if ($overrideImg != '')
            $fitPhoto = $overrideImg;
         
         $makerList = getMakerList($result->entry_id);        
         
         //get array of categories. set name based on category id
         $category = array();
         $leadCategory = explode(',',$result->second_cat);
         foreach($leadCategory as $leadCat){
            $category[] = htmlspecialchars_decode(get_CPT_name($leadCat));
         }
         $category[] = htmlspecialchars_decode(get_CPT_name($result->prime_cat));
         $categories = implode(',',$category);
         //don't return location information if the show location isn't set
         $location = ($showLoc?($result->area==NULL?'':$result->area):'');
         $data['entity'][] = array(
               'id'     => $result->entry_id,
               'name'   => $result->proj_name,
               'large_img_url' => $fitPhoto,               
               'categories' => $categories,
               'description' => $result->short_desc,
               'flag' => $flag, //only set if flag is set to 'Featured Maker'
               'makerList' => $makerList,
               'location' => $location
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

function getSchedule($formIDs) {
   global $wpdb;
   $data = array();
   $data['schedule'] = array();
   $formIDarr = array_map('intval', explode("-", $formIDs));
   $query = "SELECT schedule.entry_id, schedule.start_dt as time_start, schedule.end_dt as time_end, schedule.type,
              lead_detail.form_id, area.area, subarea.subarea, subarea.nicename, subarea.sort_order,
              lead_detail.meta_value as entry_status, DAYOFWEEK(schedule.start_dt) as day,
              location.latitude, location.longitude,
              (select meta_value as value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = schedule.entry_id AND wp_gf_entry_meta.meta_key like '22')  as photo,
              (select meta_value as value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = schedule.entry_id AND wp_gf_entry_meta.meta_key like '217') as mkr1_photo,
              (select meta_value as value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = schedule.entry_id AND wp_gf_entry_meta.meta_key like '151') as name,
              (select meta_value as value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = schedule.entry_id AND wp_gf_entry_meta.meta_key like '16')  as short_desc,              
              (select group_concat( meta_value separator ',') as cat from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = schedule.entry_id AND wp_gf_entry_meta.meta_key like '%304%') as flags,
              (select group_concat( meta_value separator ',') as cat from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = schedule.entry_id AND (wp_gf_entry_meta.meta_key like '%320%' OR wp_gf_entry_meta.meta_key like '%321%')) as category
             FROM wp_mf_schedule as schedule
               left outer join wp_mf_location as location on location_id = location.id
               left outer join wp_mf_faire_subarea subarea on subarea.id = location.subarea_id
               left outer join wp_mf_faire_area area on area.id = subarea.area_id
               left outer join wp_gf_entry as lead on schedule.entry_id = lead.id
               left outer join wp_gf_entry_meta as lead_detail on schedule.entry_id = lead_detail.entry_id and lead_detail.meta_key = '303'
               where lead.status = 'active' and lead_detail.meta_value='Accepted' "
           . " and lead_detail.form_id in(" . implode(",", $formIDarr) . ") "
           /* code to hide scheduled items as they occur
             . "   and schedule.end_dt >= now()+ INTERVAL -7 HOUR  " */
           . "order by subarea.sort_order";

   //retrieve project name, img (22), maker list, topics
   foreach ($wpdb->get_results($query) as $row) {
      $form = GFAPI::get_form($row->form_id);
      $form_type = $form['form_type'];

      $makerList = getMakerList($row->entry_id);
      $makerArr = array();

      //get array of categories. set name based on category id
      $category = array();
      $leadCategory = explode(',',$row->category);
      foreach($leadCategory as $leadCat){
         $category[] = htmlspecialchars_decode(get_CPT_name($leadCat));
      }
      
      $catList = implode(',',$category);
      
      if ($form_type == 'Presentation') {
         $projPhoto = ($row->mkr1_photo != '' ? $row->mkr1_photo : $row->photo);
      } else {
         $projPhoto = $row->photo;
      }
      //find out if there is an override image for this page
      $overrideImg = findOverride($row->entry_id, 'schedule');
      if ($overrideImg != '')
         $projPhoto = $overrideImg;

      $fitPhoto = legacy_get_resized_remote_image_url($projPhoto, 200, 200);
      if ($fitPhoto == NULL)
         $fitPhoto = $row->photo;

      //format start and end date
      $startDay  = date_create($row->time_start);
      $startDate = date_format($startDay, 'Y-m-d') . 'T' . date_format($startDay, 'H:i:s');
      $keyDate   = date_format($startDay, 'Y-m-d');

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

      //set stage name
      $stage = ($row->nicename != '' ? $row->nicename : $row->subarea);
      //"2016-05-21T11:55:00-07:00"
      $data['schedule'][] = array(
          'id' => $row->entry_id,
          'time_start' => $startDate,
          'time_end' => $endDate,
          'name' => htmlspecialchars_decode($row->name, ENT_QUOTES),
          'thumb_img_url' => $fitPhoto,
          'maker_list' => $makerList,
          'nicename' => $stage,
          'stageOrder' => (int) ($row->sort_order != '' ? $row->sort_order : 0),
          'category' => $catList,
          'latitude' => $row->latitude,
          'longitude' => $row->longitude,
          'day' => (int) $row->day,
          'desc' => htmlspecialchars_decode($row->short_desc, ENT_QUOTES),
          'type' => ucwords($type),
          'flags' => $row->flags
      );
   }

   return $data;
}

function getMakerList($entryID) {
   $makerList = '';
   $data = array();
   global $wpdb;
   $query = "SELECT *
              FROM wp_gf_entry_meta as lead_detail
              where lead_detail.entry_id = $entryID "
           . "and cast(meta_key as char) in('160.3', '160.6', '158.3', '158.6', '155.3', '155.6', "
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

   if (isset($fieldData['105'])) {
      $whoListed = strtolower($fieldData['105']);
      $isGroup = false;
      $isGroup = (strpos($whoListed, 'group') !== false);
      $isOneMaker = false;
      $isOneMaker = (strpos($whoListed, 'one') !== false);

      if ($isGroup) {
         $makerList = (isset($fieldData['109']) ? $fieldData['109'] : '');
      } elseif ($isOneMaker) {
         $makerList = (isset($fieldData['160.3']) ? $fieldData['160.3'] : '') . (isset($fieldData['160.6']) ? ' ' . $fieldData['160.6'] : '');
      } else {
         $makerArr = array();
         if (isset($fieldData['160.3']))
            $makerArr[] = (isset($fieldData['160.3']) ? $fieldData['160.3'] . ' ' : '') . (isset($fieldData['160.6']) ? $fieldData['160.6'] : '');
         if (isset($fieldData['158.3']))
            $makerArr[] = $fieldData['158.3'] . ' ' . $fieldData['158.6'];
         if (isset($fieldData['155.3']))
            $makerArr[] = $fieldData['155.3'] . ' ' . $fieldData['155.6'];
         if (isset($fieldData['156.3']))
            $makerArr[] = $fieldData['156.3'] . ' ' . $fieldData['156.6'];
         if (isset($fieldData['157.3']))
            $makerArr[] = $fieldData['157.3'] . ' ' . $fieldData['157.6'];
         if (isset($fieldData['159.3']))
            $makerArr[] = $fieldData['159.3'] . ' ' . $fieldData['159.6'];
         if (isset($fieldData['154.3']))
            $makerArr[] = $fieldData['154.3'] . ' ' . $fieldData['154.6'];

         $makerList = implode(", ", $makerArr);
      }
   }

   return $makerList;
}
