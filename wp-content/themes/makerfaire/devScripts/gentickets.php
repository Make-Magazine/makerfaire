<?php
include 'db_connect.php';
global $wpdb;
error_reporting(E_ALL); ini_set('display_errors', '1');

//form id passed in url
$form_id = (isset($_GET['form_id'])?$_GET['form_id']:0);
$token = OAUTH_TOKEN;

//no form passed? Error
if($form_id==0){
  echo 'Please sumbit form_id variable';
}else{
  if (!class_exists('eventbrite')) {
    require_once('../classes/eventbrite.php');
  }
  
  $eventbrite = new eventbrite($token);

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
  $sql =  "SELECT wp_gf_entry.id as lead_id, ".
            "(select group_concat(meta_value) from wp_gf_entry_meta where wp_gf_entry_meta.entry_id =wp_gf_entry.id and wp_gf_entry_meta.meta_key like '879.%') as weekend"
        . "  FROM wp_gf_entry "
        . "left outer join wp_gf_entry_meta on wp_gf_entry_meta.entry_id =wp_gf_entry.id and wp_gf_entry_meta.meta_key=303 "
        . " WHERE wp_gf_entry.status = 'active' and wp_gf_entry.form_id = ".$form_id." and wp_gf_entry_meta.meta_value='Accepted'";

  //$sql = $sql .= ' and wp_gf_entry.id in(74565, 74751, 74427, 73937, 75075, 73876, 73911, 73921, 74997, 74618, 74620, 75047) ';        
  //$sql = $sql .= ' and wp_gf_entry.id in(74751, 75075, 73911, 74842) ';        
  
  // 74565 maker and presenter - week 2 correct1  
  // 74427 presenter and sponsor - week 2 correct1
  // 73937 presenter and maker - both weekends correct1
  // 73876 performer and maker - both weekends  correct1
  // 73921 performer and sponsor - week 2 correct1
  // 74997 maker only - both weekends correct1
  // 74618 sponsor only - week 2 correct1
  // 74620 startup sponosor - both weekends correct1
  // 75047 - maker no weekend set correct1
  
  // 74842 0 show management both weekends
  // 73911 performer and presenter - both weekends
  // 75075 performer - week 1 
  // 74751 presenter only - week 1

  $results = $wpdb->get_results($sql);
  $accCount = 0;
  $entCount = 0;

  //loop thru the forms accepted records
  foreach($results as $row){
    $entCount++;
    $entryID  = $row->lead_id;

    //for each entry, find the exhibit type
    $entry    = GFAPI::get_entry( $entryID );

    $entReturn = get_value_by_label('entLevel', $form, $entry);
    $entReturn = (array) $entReturn;
    
    $exit = FALSE;
    foreach ($entReturn as $exhibitArr){
      $entLevel= strtolower($exhibitArr['value']);
      switch ($exhibitArr['value']) {
        case 'Maker':
        case 'Sponsor':
        case 'Startup Sponsor':
        case 'Show Management':          
          $exit = TRUE;
          break;                          
      }

      if($exit) {
        break;
      }      
    }
    
    $entWkndArr = explode(",",$row->weekend);
    
    //sql to pull all tickets available for this entry
    $tktSQL = "select event_ticket_id, eb_ticket_type.ticket_type, eb_ticket_type.qty, eb_ticket_type.hidden, eb_ticket_type.weekend_ind, eb_ticket_type.discount, ticketID,  eb_event.EB_event_id as eventID " .
              "from eb_ticket_type ". 
              "left outer join eb_eventToTicket on eb_ticket_type.event_ticket_id = eb_eventToTicket.ID ".
              "left outer join eb_event on eb_event.ID=eb_eventToTicket.eventID " .
              "left outer join eb_entry_access_code on `EBticket_id` = event_ticket_id and entry_id = ".$entryID.", ".
              "wp_mf_form_types ".
    
              "where wp_mf_form_types.form_type='".$form_type."' ".
              "AND eb_eventToTicket.eventID in (".$event_id.") ".
              "AND eb_ticket_type.form_type = wp_mf_form_types.ID ".
              "AND eb_ticket_type.entLevel='".strtolower($entLevel)."' ".
              "AND eb_eventToTicket.ticketID is not null ".
              "AND entry_id is NULL";              
         
    $tck_results = $wpdb->get_results($tktSQL);
    foreach($tck_results as $tck_row){
      //if this ticket is for a specific weekend, let's make sure the entry is exhibiting that weekend
      if($tck_row->weekend_ind !='' && !in_array($tck_row->weekend_ind,$entWkndArr)){        
        continue;
      }
      //only do 100 eventbrite requests at a time, then kill task
      $accCount++;
      
      //generate random 3 digit number for access code
      $rand = rand(0,999);

      //generate access code for each ticket type
      $hidden     = $tck_row->hidden;
      $accessCode = "Z-".$tck_row->ticket_type.$entryID.$rand;      
      
      $ticket_ids = (array) json_decode($tck_row->ticketID);
      $ticket_ids = array_map('strval', $ticket_ids);
      if($tck_row->discount==0){
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
      }else{
        $args = array(
          "discount" => 
          array(
            "type" => "coded",
            "code" => $accessCode,
            "percent_off" => number_format($tck_row->discount,2),
            "event_id" => $tck_row->eventID,
            "ticket_class_ids" => $ticket_ids,
            "quantity_available" => $tck_row->qty    
          )
        );
      }        
      echo 'for entry '.$entryID.' $entLevel = '.$entLevel.' ';              
      echo 'Generating '.$accessCode.' EventID ='.$tck_row->eventID.' quantity '.$tck_row->qty.' weekend '.$tck_row->weekend_ind.'<br/>';      
 
        //call eventbrite to create access code
        $access_codes = $eventbrite->post('/organizations/27283522055/discounts/', $args);
              
        if(isset($access_codes->status_code) && $access_codes->status_code==400){
          echo 'error in call to EB<br/>';
          echo $access_codes->error_description.'<br/>';        
          exit;
        }
        
        //save access codes to db
        $dbSQL = 'INSERT INTO `eb_entry_access_code`(`entry_id`, `access_code`, `hidden`,EBticket_id) '
                . ' VALUES ('.$entryID.',"'.$accessCode.'",'.$hidden.','.$tck_row->event_ticket_id.')'
                . ' on duplicate key update access_code = "'.$accessCode.'"';

        $wpdb->get_results($dbSQL);
      if($accCount>100) exit;
    }
    if($accCount>100) exit;
  }
  echo 'Updated '.$accCount.' accepted records in form '.$form_id.'<br/>';
}