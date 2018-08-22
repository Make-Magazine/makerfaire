<?php
include 'db_connect.php';
global $wpdb;
error_reporting(E_ALL); ini_set('display_errors', '1');
//form id passed in url
$form_id = (isset($_GET['form_id'])?$_GET['form_id']:0);

//no form passed? Error
if($form_id==0){
  echo 'Please sumbit form_id variable';
}else{
  if (!class_exists('eventbrite')) {
    require_once('../classes/eventbrite.class.inc');
  }
  $eventbrite = new eventbrite();
  $form = GFAPI::get_form($form_id);
  $form_type = (isset($form['form_type'])  ? $form['form_type'] : '');

  //get eventID for this form
  $sql = "select wp_mf_faire.ID, eb_event.ID as event_id, EB_event_id as eib "
          . " from wp_mf_faire, eb_event "
          . " where FIND_IN_SET ($form_id,wp_mf_faire.form_ids)> 0"
          . " and wp_mf_faire.ID = eb_event.wp_mf_faire_id";
  $faire = $wpdb->get_results($sql);
  if($wpdb->num_rows > 0){
    $eidArr = array();
    $EIBarr = array();
    //a faire can be set up with multiple events
    foreach($faire as $row){
      $eidArr[]               = $row->event_id;
      $EIBarr[$row->event_id] = $row->eib;
    }
    //MF table event ID
    $event_id = implode(",", $eidArr);
  }else{
    $event_id = 0;
  }

  //find all accepted entries in this form
  $sql =  "SELECT wp_gf_entry.id as lead_id, wp_gf_entry.ip "
        . "  FROM wp_gf_entry "
        . "left outer join wp_gf_entry_meta on wp_gf_entry_meta.entry_id =wp_gf_entry.id and wp_gf_entry_meta.meta_key=303 "
        . " WHERE wp_gf_entry.status = 'active' and wp_gf_entry.form_id = ".$form_id." and wp_gf_entry_meta.meta_value='Accepted'";
  $results = $wpdb->get_results($sql);
  $accCount = 0;
  $entCount = 0;

  //loop thru the forms accepted records
  foreach($results as $row){
    $entCount++;
    $entryID  = $row->lead_id;
    $entry_ip = $row->ip;

    //for each entry, find the entry or sponsor level
    $entry    = GFAPI::get_entry( $entryID );

    $entReturn = get_value_by_label('entLevel', $form, $entry);
    if(is_array($entReturn)){
      $entArray = end($entReturn);
      $entLevel = $entArray['value'];
    }else{
      $entLevel = $entReturn;
    }

    //generate random 4 digit number for access code
    $digits = 3;
    $charIP = (string) $entry_ip;
    $rand   =  substr(base_convert($charIP, 10, 36),0,$digits);

    //find missing tickets for this entry
    $tktSQL = "select eb_ticket_type.ticket_type, qty, eb_ticket_type.hidden,ticketID, eb_eventToTicket.eventID, eb_entry_access_code.access_code,entry_id "
         . "from eb_ticket_type "
            . "left outer join eb_eventToTicket on eb_eventToTicket.eventID in (".$event_id.") "
            . "                                and eb_eventToTicket.ticket_type = eb_ticket_type.ticket_type "
            . "left outer join eb_entry_access_code on `EBticket_id` = eb_eventToTicket.ticketID and entry_id = ".$entryID.", "
            . "wp_mf_form_types "
            . "where wp_mf_form_types.form_type='".$form_type."' "
            . "  and eb_ticket_type.event_id in (".$event_id.") "
            . "  and eb_ticket_type.form_type = wp_mf_form_types.ID"
            . "  and entLevel='".strtolower ($entLevel)."'"
            . "  and ticketID is not null and entry_id is NULL";
    $tck_results = $wpdb->get_results($tktSQL);
    foreach($tck_results as $tck_row){
      //only do 100 eventbrite requests at a time, then kill task
      $accCount++;
      //generate access code for each ticket type
      $hidden     = $tck_row->hidden;
      $accessCode = $tck_row->ticket_type.$entryID.$rand;
      $EB_event_id = $EIBarr[$tck_row->eventID];

      $args = array(
          'id'   => $EB_event_id,
          'data' => 'access_codes',
          'create' => array(
            'access_code.code'                => $accessCode,
            'access_code.ticket_ids'          => $tck_row->ticketID,
            'access_code.quantity_available'  => $tck_row->qty
          )
        );
      echo 'Missing tickets for '.$entryID. ' - ' .
              ' eventID '.$tck_row->eventID.
              ' ticket type '.$tck_row->ticket_type .
              ' ticket id '.$tck_row->ticketID .
              ' qty '.$tck_row->qty .
              ' access code '.$accessCode.
              ' hidden '.$tck_row->hidden .
              ' entry level '.$entLevel.
              '. Generating:<br/>';

        //call eventbrite to create access code
        $access_codes = $eventbrite->events($args);
        if(isset($access_codes->status_code) && $access_codes->status_code==400){
          $response['msg'] =  $access_codes->error_description;
          exit;
        }else{
          $response[$accessCode] = $access_codes->resource_uri;
        }

        //save access codes to db
        $dbSQL = 'INSERT INTO `eb_entry_access_code`(`entry_id`, `access_code`, `hidden`,EBticket_id) '
                . ' VALUES ('.$entryID.',"'.$accessCode.'",'.$hidden.','.$tck_row->ticketID.')'
                . ' on duplicate key update access_code = "'.$accessCode.'"';

        $wpdb->get_results($dbSQL);
      if($accCount>100) exit;
    }
    if($accCount>100) exit;
  }
  echo 'Updated '.$accCount.' accepted records in form '.$form_id.'<br/>';
}

// find all NY17 Accepted entries (that do not yet have ticket codes)
// and generate them
/*
 *
$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
  $response = crtEBtickets($row['lead_id'],$row['form_id'],$row['ip']);
  if($response['msg']!='Access Codes generated.  Please refresh to see<br/>'){
    echo $response['msg'];
  }
}

function crtEBtickets($entryID,$form_id,$ip){
  global $eventbrite;

  global $mysqli;


  if($form_id == 46)  $form_type = 'Exhibit';
  if($form_id == 45)  $form_type = 'Performance';
  if($form_id == 49)  $form_type = 'Presentation';
  if($form_id == 47)  $form_type = 'Startup Sponsor';
  if($form_id == 71)  $form_type = 'Sponsor';
  //60 - Show Management (not needed???)

  $faire_id      = 3;
  //MF table event ID
  $event_id      = 1;
  //event brite event ID
  $EB_event_id   = 21038172741;

  //determine what ticket types to request
  $sql = 'select ticket_type, qty, hidden,
          (select ticketID FROM `eb_eventToTicket`
            where eventID = '.$event_id.' and
                  eb_eventToTicket.ticket_type = eb_ticket_type.ticket_type) as ticket_id
           from eb_ticket_type, wp_mf_form_types
          where wp_mf_form_types.form_type="'.$form_type.'" and
                eb_ticket_type.form_type = wp_mf_form_types.ID';
  $result = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");


  $response = array();
  $digits = 3;

  $rand   =  substr(base_convert($ip, 10, 36),0,$digits);
  while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
    //generate access code for each ticket type
    $hidden     = $row['hidden'];
    $accessCode = $row['ticket_type'].$entryID.$rand;
    $args = array(
      'id'   => $EB_event_id,
      'data' => 'access_codes',
      'create' => array(
        'access_code.code' => $accessCode,
        'access_code.ticket_ids'=>$row['ticket_id'],
        'access_code.quantity_available'=>$row['qty']
      )
    );
    //call eventbrite to create access code
    $access_codes = $eventbrite->events($args);
    if(isset($access_codes->error)){
      var_dump($access_codes);
      die;
    }else{
      $response[$accessCode] = $access_codes->resource_uri;
    }
    global $wpdb;
    //save access codes to db
    $dbSQL = 'INSERT INTO `eb_entry_access_code`(`entry_id`, `access_code`, `hidden`,EBticket_id) '
            . ' VALUES ('.$entryID.',"'.$accessCode.'",'.$hidden.','.$row['ticket_id'].')'
            . ' on duplicate key update access_code = "'.$accessCode.'"';

    $wpdb->get_results($dbSQL);
  }
  echo $accessCode.'<br/>';
}

 */