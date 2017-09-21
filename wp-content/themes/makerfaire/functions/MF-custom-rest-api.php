<?php
/* adds a custom REST API endpoint of makerfaire*/
add_action( 'rest_api_init', function () {

	register_rest_route( 'makerfaire', '/v2/fairedata/(?P<type>[a-z0-9\-]+)/(?P<formids>[a-z0-9\-]+)', array(
		'methods' => 'GET',
		'callback' => 'mf_fairedata'
	));

  register_rest_route( 'makerfaire', '/v2/entry/(?P<type>[a-z0-9\-]+)/(?P<entryid>[a-z0-9\-]+)', array(
		'methods' => 'GET',
		'callback' => 'mf_updateEntry'
	));

  //makerfaire api for makershare
  register_rest_route( 'makerfaire', '/v2/mfapi/(?P<type>[a-z0-9\-]+)/(?P<key>[a-z0-9\-]+)', array(
		'methods' => 'GET',
		'callback' => 'mf_mfapi'
	));
});

function mf_mfapi( WP_REST_Request $request ) {
  // Define the API version.
  define( 'MF_API_VERSION', 'v3' );

  // Set the post per page for our queries
  define( 'MF_POSTS_PER_PAGE', 2000 );

  // Set the API keys to run this API.
  //define( 'MF_API_KEY', sanitize_text_field( get_option( 'make_app_api_key' ) ) );
  // MIIBOgIBAAJBAKUSW7BsZCFVHarjMixDfaVy6OjB2QKtlfMoNQEafDW%2FJx%2BZzWBY
  define( 'MF_API_KEY', 'f22c7aba-5c1e-478f-9513-09aa7e28105d' );
  $allowed_types = array(
    'category',
    'account',
    'project'
  );

  $type     = $request['type'];
  $key      = $request['key'];
  if ( empty( $key ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    echo '<h2>Invalid: No Key.</h2>';

    return;
  } elseif ( $key !== MF_API_KEY ) {
    header( 'HTTP/1.0 403 Forbidden' );

    echo '<h2>Invalid: Parameter Not Valid - "' . esc_html( $_REQUEST['key'] ) . '"</h2>';
    return;
  } elseif ( empty( $type ) ) {
    header( 'HTTP/1.0 403 Forbidden' );

    echo '<h2>Invalid: Type</h2>'.$type;
    return;
  } elseif ( ! in_array( $type, $allowed_types ) ) {
    header( 'HTTP/1.0 403 Forbidden' );

    echo '<h2>Invalid: Parameter Not Valid - "' . esc_html( $_REQUEST['type'] ) . '"</h2>';
    return;
  }else{
    header('HTTP/1.1 200 OK');
  } /*elseif ( empty( $faire ) ) {
    header( 'HTTP/1.0 403 Forbidden' );

    echo '<h2>Invalid: Faire</h2>';
    return;
  }*/


  /**
   * RUN THE CONTROLLER
   * Process the passed query string and fetch the appropriate API section
   */

  // Get the appropriate API file.
  $api_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/makerfaire/api/' . sanitize_title( MF_API_VERSION ) . '/' . sanitize_title( $type ) . '/index.php';



  // Prevent Path Traversal
  if ( strpos( $api_path, '../' ) !== false || strpos( $api_path, "..\\" ) !== false || strpos( $api_path, '/..' ) !== false || strpos( $api_path, '\..' ) !== false )
    return;

  // Make sure the api file exists...
  //if ( ! file_exists( $api_path ) )
  if (!file_exists($api_path))
    return;

  // Set the JSON header
  header( 'Content-type: application/json' );
  //print_r($api_path);
  //return;
  // Load the file and process everything
  include_once( $api_path );

}

function mf_updateEntry( WP_REST_Request $request ) {
  $type     = $request['type'];
  $entry_id  = $request['entryid'];
  if($type=='accept'){
    wp_set_current_user(20);
    $current_user = wp_get_current_user();

    $lead         = GFAPI::get_entry( $entry_id );
    $form_id      = isset($lead['form_id']) ? $lead['form_id'] : 0;
    $form         = RGFormsModel::get_form_meta($form_id);
    $_POST['entry_info_status_change'] = 'Accepted';
    set_entry_status($lead,$form);
  }
}
function mf_fairedata( WP_REST_Request $request ) {
	$type     = $request['type'];
  $formIDs  = $request['formids'];
  if($type != '' && $formIDs != '') {
    $data = array();
    switch ($type) {
      case 'mtm':
        $entity   = getMTMentries($formIDs);
        $category = getCategories($formIDs);
        $data     = array_merge($entity, $category);
        break;
      case 'categories':
        $data = getCategories($formIDs);
        break;
      case 'schedule':
        $schedule = getSchedule($formIDs);
        $category = getCategories($formIDs);
        $data     = array_merge($schedule, $category);
        break;
    }

  } else {
    $data['error'] = 'Error: Type or Form IDs not submitted';
  }

  $return = 'your type is '.$type.' and your formids are ';
  $formArr = explode("-",$formIDs);
  foreach($formArr as $formID){
    $return .= $formID.' ';
  }
  //return $data;
  wp_send_json($data);
  exit;
}

function getMTMentries($formIDs) {
  $data['entity'] = array();
  $formIDarr = array_map('intval', explode("-", $formIDs));

  global $wpdb;
  //find all active entries for selected forms
  $query = "select lead_detail.lead_id, lead_detail.field_number, lead_detail.value
            from    wp_rg_lead_detail lead_detail
            left outer join wp_rg_lead as lead on lead_detail.lead_id = lead.id
            where lead.status = 'active'
              and lead_detail.form_id in(".implode(",",$formIDarr).")
              and (field_number like '22' OR
                   field_number like '16' OR
                   field_number like '151' OR
                   field_number like '303' OR
                   field_number like '320' OR
                   field_number like '32.1%' OR
                   field_number like '304.%')
            ORDER BY `lead_detail`.`lead_id`  ASC";

  $results = $wpdb->get_results($query);

  //build entry array
  $entries = array();
  foreach($results as $result){
    $entries[$result->lead_id]['id'] = $result->lead_id;
    $entries[$result->lead_id][$result->field_number] = $result->value;
  }

  shuffle ($entries);
  //randomly order entries
  foreach($entries as $entry){
    $leadCategory = array();
    $flag = '';
    if($entry['303']=='Accepted'){
      //build category array
      foreach($entry as $leadKey=>$leadValue){
        $pos = strpos($leadKey, '321'); //4 additional categories
        if ($pos !== false) {
          $leadCategory[]=$leadValue;
        }

        //main catgory
        $pos = strpos($leadKey, '320');
        if ($pos !== false) {
          $leadCategory[]=$leadValue;
        }

        //flags
        $pos = strpos($leadKey, '304'); // flags
        if ($pos !== false) {
          //echo $leadValue.'   ';
          $pos2 = strpos($leadValue, 'Featured');
          if ($pos2 !== false) {
            //echo 'featured maker ';
            $flag = $leadValue;
          }
        }
      }

      $projPhoto = (isset($entry['22']) ? $entry['22']:'');
      $fitPhoto  = legacy_get_resized_remote_image_url($projPhoto,230,181);
      $featImg   = legacy_get_resized_remote_image_url($projPhoto,800,500);
      if($fitPhoto == NULL) $fitPhoto = $projPhoto;
      if($featImg == NULL)  $featImg = $projPhoto;
      //maker list
      $makerList = getMakerList($entry['id']);

      $data['entity'][] = array(
          'id'                => $entry['id'],
          'name'              => $entry['151'],
          'large_img_url'     => $fitPhoto,
          'featured_img'      => $featImg,
          'category_id_refs'  => array_unique($leadCategory),
          'description'       => $entry['16'],
          'flag'              => $flag, //only set if flag is set to 'Featured Maker'
          'makerList'         => $makerList
          );
    }
  } //end foreach $entries
  return $data;
} //end getMTMentries

  function getCategories($formIDs) {
    $data = array();
    $formIDarr = array_map('intval', explode("-", $formIDs));

    foreach($formIDarr as $form_id){
      $form = GFAPI::get_form( $form_id );
      if(is_array($form['fields'])) {
        foreach($form['fields'] as $field) {
          if($field->id==320){
            foreach($field->choices as $choice) {
              if($choice['value']!='') {
                $data['category'][] = array('id'=>absint( $choice['value'] ),'name'=>html_entity_decode( esc_js( $choice['text'] ) ));
              }
            }
          }
          if($field->id==321){
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
              lead_detail.form_id, area.area, subarea.subarea, subarea.nicename,
              lead_detail.value as entry_status, DAYOFWEEK(schedule.start_dt) as day,
              location.latitude, location.longitude,
              (select value from wp_rg_lead_detail where lead_id = schedule.entry_id AND field_number like '22')  as photo,
              (select value from wp_rg_lead_detail where lead_id = schedule.entry_id AND field_number like '151') as name,
              (select value from wp_rg_lead_detail where lead_id = schedule.entry_id AND field_number like '16')  as short_desc,
              (select group_concat( value separator ', ') as cat   from wp_rg_lead_detail where lead_id = schedule.entry_id AND (field_number like '%320%' OR field_number like '%321%')) as category
               FROM wp_mf_schedule as schedule
               left outer join wp_mf_location as location on location_id = location.id
               left outer join wp_mf_faire_subarea subarea on subarea.id = location.subarea_id
               left outer join wp_mf_faire_area area on area.id = subarea.area_id
               left outer join wp_rg_lead as lead on schedule.entry_id = lead.id
               left outer join wp_rg_lead_detail as lead_detail on
                   schedule.entry_id = lead_detail.lead_id and field_number = 303
               where lead.status = 'active' and lead_detail.value='Accepted' "
            . " and lead_detail.form_id in(".implode(",",$formIDarr).") order by subarea.sort_order";
/* code to hide scheduled items as they occur
               . " and lead_detail.form_id in(".implode(",",$formIDarr).") "
            . "   and schedule.end_dt >= now()+ INTERVAL -7 HOUR  "
            . "order by subarea.sort_order";*/

    //retrieve project name, img (22), maker list, topics
    foreach($wpdb->get_results($query) as $row){
      $makerList = getMakerList($row->entry_id);
      $makerArr = array();

      //remove duplicates
      $catArr = explode(", ", $row->category);
      $catArr = array_unique($catArr);
      $catList = implode(", ",$catArr);
      //find out if there is an override image for this page
      //$overrideImg = findOverride($entry['id'],'mtm');
      //$projPhoto = ($row->photo=='' ? $entry['22']: $overrideImg);
      $projPhoto = $row->photo;
      $fitPhoto  = legacy_get_resized_remote_image_url($projPhoto,200,200);
      if($fitPhoto==NULL) $fitPhoto = $row->photo;

      //format start and end date
      $startDay   = date_create($row->time_start);
      $startDate  = date_format($startDay,'Y-m-d').'T'.date_format($startDay,'H:i:s');
      $keyDate    = date_format($startDay,'Y-m-d');

      $endDate = date_create($row->time_end);
      $endDate = date_format($endDate,'Y-m-d').'T'.date_format($endDate,'H:i:s');

      //set default values for type if not set
      if($row->type ==''){
        $form = GFAPI::get_form( $row->form_id );
        $form_type = $form['form_type'];
        //demo, performance, talk, workshop
        if($form_type == 'Performance') {
          $type = 'performance';
        }else{
          $type = 'talk';
        }
      }else{
        $type = $row->type;
      }

      //set stage name
      $stage = ($row->nicename != '' ? $row->nicename: $row->subarea);
      //"2016-05-21T11:55:00-07:00"
      $data['schedule'][$keyDate][] = array(
            'id'            => $row->entry_id,
            'time_start'    => $startDate,
            'time_end'      => $endDate,
            'name'          => htmlspecialchars_decode($row->name,ENT_QUOTES),
            'thumb_img_url' => $fitPhoto,
            'maker_list'    => $makerList,
            'nicename'      => $stage,
            'category'      => $catList,
            'latitude'      => $row->latitude,
            'longitude'     => $row->longitude,
            'day'           => (int) $row->day,
            'desc'          => htmlspecialchars_decode($row->short_desc,ENT_QUOTES),
            'type'          => ucwords($type)
      );

    }

    return $data;
  }

  function getMakerList($entryID) {
    $makerList = '';
    $data = array(); global $wpdb;
    $query = "SELECT *
              FROM wp_rg_lead_detail as lead_detail
              where lead_detail.lead_id = $entryID "
           . "and cast(field_number as char) in('160.3', '160.6', '158.3', '158.6', '155.3', '155.6', "
           . "'156.3', '156.6', '157.3', '157.6', '159.3', '159.6', '154.3', '154.6', '109', '105')";
    $entryData = $wpdb->get_results($query);
    //field 105 - who would you like listed
    //    one maker, a group or association, a list of makers
    /* Maker Name field #'s -> 1 - 160, 2 - 158, 3 - 155, 4 - 156, 5 - 157, 6 - 159, 7 - 154
     * Group Name - 109
     */
    $fieldData = array();
    foreach($entryData as $field){
      $fieldData[$field->field_number] = $field->value;
    }

    if(isset($fieldData[105])){
      $whoListed = strtolower($fieldData['105']);
      $isGroup =false;
      $isGroup    = (strpos($whoListed, 'group') !== false);
      $isOneMaker = false;
      $isOneMaker = (strpos($whoListed, 'one') !== false);

      if($isGroup) {
        $makerList = $fieldData[109];
      }elseif($isOneMaker){
        $makerList = $fieldData['160.3']. ' ' .$fieldData['160.6'];
      }else{
        $makerArr = array();
        if(isset($fieldData['160.3']))  $makerArr[] = (isset($fieldData['160.3'])?$fieldData['160.3']. ' ':'') .(isset($fieldData['160.6'])?$fieldData['160.6']:'');
        if(isset($fieldData['158.3']))  $makerArr[] = $fieldData['158.3']. ' ' .$fieldData['158.6'];
        if(isset($fieldData['155.3']))  $makerArr[] = $fieldData['155.3']. ' ' .$fieldData['155.6'];
        if(isset($fieldData['156.3']))  $makerArr[] = $fieldData['156.3']. ' ' .$fieldData['156.6'];
        if(isset($fieldData['157.3']))  $makerArr[] = $fieldData['157.3']. ' ' .$fieldData['157.6'];
        if(isset($fieldData['159.3']))  $makerArr[] = $fieldData['159.3']. ' ' .$fieldData['159.6'];
        if(isset($fieldData['154.3']))  $makerArr[] = $fieldData['154.3']. ' ' .$fieldData['154.6'];

        $makerList = implode(", ", $makerArr);
      }
    }

    return $makerList;
  }