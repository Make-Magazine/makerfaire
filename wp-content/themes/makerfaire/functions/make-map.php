<?php

add_action( 'rest_api_init', function () {
	register_rest_route( 'makemap/v1', 'mapdata/(?P<formid>[a-z0-9\-]+)', array(
		'methods' => 'GET',
		'callback' => 'build_map_JSON',
                'permission_callback' => '__return_true'
	));

});

function build_map_JSON( WP_REST_Request $request){
  global $wpdb;

  $formID  = (int) $request['formid'];

  /* These fields need to be set in the form
     * Event Name     - mmap_eventname  - name
     * Event URL      - mmap_url        - faire_url
     * Event City     - mmap_city       - venue_address_city
     * Event State    - mmap_state      - venue_address_state
     * Event ZipCode  - mmap_zip        - venue_address_postal_code
     * Event Country  - mmap_country    - venue_address_country
     * Event Type     - mmap_type       - category
     * Event Start Dt - mmap_start_dt   - event_start_dt
     * Event End Date - mmap_end_dt     - event_end_dt
     * Event lat      - mmap_lat        - lat
     * Event lng      - mmap_lng        - lng
     * Event Date     - mmap_eventdate  - event_dt
     */

  //array of parameter names
  $fieldNames = array('mmap_eventname', 'mmap_url', 'mmap_city', 'mmap_state', 'mmap_zip', 'mmap_country',
      'mmap_type', 'mmap_start_dt', 'mmap_end_dt', 'mmap_lat', 'mmap_lng', 'mmap_eventdate');


  $retFields = array(); //initialize field id array
  $form = GFAPI::get_form($formID);    //pull requested form data

  // search the form for the required parameter names.
  foreach($form['fields'] as $field){
    //Find the paramater name for this field if set
    if($field['type']=='name' || $field['type']=='address'){
      if(is_array($field['inputs'])){
        foreach($field['inputs'] as $choice){
          if(isset($choice['name']) && $choice['name']!='' && in_array($choice['name'],$fieldNames)){
            $retFields[] = array('id' => $choice['id'], 'name' => $choice['name']);
          }
        }
      }else{
        //var_dump($field);
      }

    }else{
      $lead_key = $field['inputName'];
      if ($lead_key !='' && in_array($lead_key,$fieldNames)) {
        $retFields[] = array(
          'id'    => $field['id'],
          'value' => $lead_key
        );
      }
    }
  }

  /*
   * now let's pull the entry data for the specified forms and return the above fields
   */

  //Initialize the locations array
  $points = array();
  $search_criteria['status'] = 'active';
  $entries = GFAPI::get_entries($formID, $search_criteria, null, array('offset' => 0, 'page_size' => 9999));
  foreach($entries as $entry){
    $point = array();
    $point['id']=$entry['id'];
    foreach($retFields as $field){
      $fieldName = $field['value'];
      $fieldID   = $field['id'];
      $point[$fieldName]  = (isset($entry[$fieldID])?$entry[$fieldID]:'');
    }
    //only send data if lat and long are populated
    if($point['mmap_lat'] !='' && $point['mmap_lng']!=''){
      array_push($points, $point);
    }
  }

  // Merge the header and the entities
  $merged = array("Locations"=>$points);

  // Output the JSON
  echo json_encode( $merged );

  exit;
}
