<?php
/* Functions specific to EventBrite ticketing process */

//generate Eventbrite Access Codes for specific entry
function ebAccessTokens() {
  $entryID  = $_POST['entryID'];
  $response = genEBtickets($entryID);
  wp_send_json($response);
  exit;
}
add_action('wp_ajax_ebAccessTokens', 'ebAccessTokens');

// This function is called with ajax to update the hidden indicator for
// the eventbrite access codes
function ebUpdateAC() {
  global $wpdb;
  $response = array();
  $accessCode = (isset($_POST['accessCode']) ? $_POST['accessCode'] : '');
  $checked = (isset($_POST['checked']) ? $_POST['checked'] : 0);
  if ($accessCode != '') {
    $sql = 'update eb_entry_access_code set hidden= ' . $checked . ' where access_code = "' . $accessCode . '"';
    $wpdb->get_results($sql);
    $response['msg'] = '';
  } else {
    $response['msg'] = 'Error Updating the hidden property of this ticket code.  Please alert dev of the entry and ticket code you were updating';
  }
  wp_send_json($response);
  exit;
}
add_action('wp_ajax_ebUpdateAC', 'ebUpdateAC');

/* Used to generate ticket codes */
function genEBtickets($entryID, $testing=FALSE) { 
  if (!is_numeric(($entryID))) {
    return;
  }

  global $wpdb;
  if (!class_exists('eventbrite')) {
    require_once(get_template_directory() . '/classes/eventbrite.php');
  }

  $token = OAUTH_TOKEN;

  $eventbrite = new eventbrite($token);

  $response = array();
  $entry    = GFAPI::get_entry($entryID);

  //pull form information
  $form_id  = $entry['form_id'];
  $form = GFAPI::get_form($form_id);
  $form_type = $form['form_type'];

  //get eventID for this form
  $eidArr = array();
  $EIBarr = array();
  $sql = "select wp_mf_faire.ID, eb_event.ID as event_id, EB_event_id as eib "
    . " from wp_mf_faire, eb_event "
    . " where FIND_IN_SET ($form_id,wp_mf_faire.form_ids)> 0"
    . " and wp_mf_faire.ID = eb_event.wp_mf_faire_id";

  $faire = $wpdb->get_results($sql);

  if ($wpdb->num_rows > 0) {
    //a faire can be set up with multiple events
    foreach ($faire as $row) {
      $eidArr[]               = $row->event_id;
      $EIBarr[$row->event_id] = $row->eib;
    }
    //MF table event ID
    $event_id = implode(",", $eidArr);
  } else {
    $event_id = 0;
  }

  // find the exhibit type using field 339
  /*
    Admin entry type - field 339
    values exhibit, presentation, performer, workshop, sponsor, startup sponsor, show management, unknown
  */
  $ent_type_arr = array_intersect_key($entry, array_flip(preg_grep('/^339./', array_keys($entry))));
  $ent_type = implode('-', array_filter($ent_type_arr));
  $entLevel = '';
  //setting entry type based on who gets the most tickets  
  if (stripos($ent_type, 'sponsor') !== false && stripos($ent_type, 'startup') === false) { //not startup sponsor
    $entLevel = 'sponsor'; //sponsor (not startup)
  } elseif (stripos($ent_type, 'exhibit') !== false) {
    $entLevel = 'exhibit';
  } elseif (stripos($ent_type, 'startup') !== false) {
    $entLevel = 'startup sponsor';
  } elseif (stripos($ent_type, 'workshop') !== false) {
    $entLevel = 'workshop';
  } elseif (stripos($ent_type, 'present') !== false) { //Presenters 
    $entLevel = 'presenter';
  } elseif (stripos($ent_type, 'perform') !== false) { //not startup sponsor 
    $entLevel = 'performer';
  }

  if($entLevel=='exhibit'){
    //determine if they are part of a showcase
    $sc_sql = "SELECT count(*) FROM `wp_mf_lead_rel` where childID=$entryID";

    $sc_count = $wpdb->get_var($sc_sql);
    if ($sc_count && $sc_count > 0) {
      $entLevel = 'showcase';
    }
  }

  //Final Weekend  
  $entWkndArr = array();
  foreach ($entry as $key => $value) {
    if (isset($key) && strpos($key, '879.') === 0) {
      $entWkndArr[$key] = $value;
    }
  }

  //sql to pull all tickets available for this entry
  $tktSQL = "select event_ticket_id, eb_ticket_type.ticket_type, eb_ticket_type.qty, eb_ticket_type.hidden, eb_ticket_type.weekend_ind, eb_ticket_type.discount, ticketID,  eb_event.EB_event_id as eventID " .
    "from eb_ticket_type " .
    "left outer join eb_eventToTicket on eb_ticket_type.event_ticket_id = eb_eventToTicket.ID " .
    "left outer join eb_event on eb_event.ID=eb_eventToTicket.eventID " .
    "left outer join eb_entry_access_code on `EBticket_id` = event_ticket_id and entry_id = " . $entryID . ", " .
    "wp_mf_form_types " .

    "where wp_mf_form_types.form_type='" . $form_type . "' " .
    "AND eb_eventToTicket.eventID in (" . $event_id . ") " .
    "AND eb_ticket_type.form_type = wp_mf_form_types.ID " .
    "AND eb_ticket_type.entLevel='" . strtolower($entLevel) . "' " .
    "AND eb_eventToTicket.ticketID is not null " .
    "AND entry_id is NULL";

  $tck_results = $wpdb->get_results($tktSQL);
  $ticket_generated=FALSE;
  foreach ($tck_results as $tck_row) {    
    if ($tck_row->qty == 0)  continue;
    //if this ticket is for a specific weekend, let's make sure the entry is exhibiting that weekend
    if ($tck_row->weekend_ind != '' && !in_array($tck_row->weekend_ind, $entWkndArr)) {
      continue;
    }
    //generate random 3 digit number for access code
    $rand = rand(0, 999);

    //generate access code for each ticket type
    $hidden     = $tck_row->hidden;
    $accessCode = "Z-" . $tck_row->ticket_type . $entryID . $rand;

    $ticket_ids = (array) json_decode($tck_row->ticketID);
    $ticket_ids = array_map('strval', $ticket_ids);
    if ($tck_row->discount == 0) {
      $args = array(
        "discount" =>
        array(
          "type" => "access",
          "code" => $accessCode,
          "event_id" => $tck_row->eventID,
          "ticket_class_ids" => $ticket_ids,
          "quantity_available" => $tck_row->qty
        )
      );
    } else {
      $args = array(
        "discount" =>
        array(
          "type" => "coded",
          "code" => $accessCode,
          "percent_off" => number_format($tck_row->discount, 2),
          "event_id" => $tck_row->eventID,
          "ticket_class_ids" => $ticket_ids,
          "quantity_available" => $tck_row->qty
        )
      );
    }

    //do not call eventbrite on local!!!
    if (DB_HOST == 'localhost' || $testing) {
      if($testing){
        echo 'you are NOT generating actual tickets - '.
        'for entry ' . $entryID .
        '|set ent_type = ' . $ent_type .
        '|calculated entry Level = ' . $entLevel . ' ' .
        '|Generating ' . $accessCode .
        '|ticketID =' . $tck_row->eventID .
        '|EventID =' . $tck_row->ticketID .
        '|quantity ' . $tck_row->qty .
        '|hidden ' . $tck_row->hidden . '<br/>';
      }else{
        error_log('you are NOT generating actual tickets - ' .
        'for entry ' . $entryID .
        '|set ent_type = ' . $ent_type .
        '|calculated entry Level = ' . $entLevel . ' ' .
        '|Generating ' . $accessCode .
        '|ticketID =' . $tck_row->eventID .
        '|EventID =' . $tck_row->ticketID .
        '|quantity ' . $tck_row->qty .
        '|hidden ' . $tck_row->hidden);
        //error_log(print_r($args, TRUE));
      }        
    } else {
      //call eventbrite to create access code    
      $access_codes = $eventbrite->post('/organizations/' . EB_ORG . '/discounts/', $args);

      if (isset($access_codes->status_code)) {
        error_log('error in call to EB for entry ID ' . $entryID . '. Error code - ' . $access_codes->status_code);
        error_log($access_codes->error_description);
        $response['msg'] = 'Error generating Access Codes';
        return $response;
      }
      //save access codes to db
      $dbSQL = 'INSERT INTO `eb_entry_access_code`(`entry_id`, `access_code`, `hidden`,EBticket_id) '
      . ' VALUES (' . $entryID . ',"' . $accessCode . '",' . $hidden . ',' . $tck_row->event_ticket_id . ')'
      . ' on duplicate key update access_code = "' . $accessCode . '"';

      $wpdb->get_results($dbSQL);
    }    
    $ticket_generated=TRUE;
  }

  if($ticket_generated){
    $response['msg'] = 'Access Codes generated.  Please refresh to see<br/>';
  }
  
  return $response;
}
add_action('sidebar_entry_update', 'genEBtickets', 10, 1);
