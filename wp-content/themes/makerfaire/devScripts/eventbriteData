<?php

include 'db_connect.php';
//process entries 56615 - 56674
/*
for($i=56642;$i<=56674;$i++){
  prcNewEntry($i);
}*/
prcNewEntry(56614);
//process new entry
function prcNewEntry($entryID){
  global $wpdb;
  if (!class_exists('eventbrite')) {
    require_once('../classes/eventbrite.class.inc');
  }
  $eventbrite = new eventbrite();

  $entry    = GFAPI::get_entry($entryID);
  $form_id  = $entry['form_id'];
  $form = GFAPI::get_form($form_id);

  //create RMT data
  GFRMTHELPER::gravityforms_makerInfo($entry,$form);

  //generate eventbrite tickets
  /* SF Bazaar rules
    (MA, FC, SC ) hidden tickets - set to default qty of 2
    ME (Maker Entry Pass) = 2
    FD (Friday Discount) = 10
    SD (Saturday Discount) = 6 */

  $tickets = array();
  $tickets[] =  array('ticket_type' => 'MA',
      'ticket_id' =>'48998582',
                   'hidden'      => 1,
                   'qty'         => 2
      );
  $tickets[] =  array('ticket_type' => 'FC',
      'ticket_id' =>'44091563',
                   'hidden'      => 1,
                   'qty'         => 2
      );
  $tickets[] =  array('ticket_type' => 'SC',
      'ticket_id' =>'44091560',
                   'hidden'      => 1,
                   'qty'         => 2
      );
  $tickets[] =  array('ticket_type' => 'ME',
      'ticket_id' =>'44091559',
                   'hidden'      => 0,
                   'qty'         => 2
      );
  $tickets[] =  array('ticket_type' => 'FD',
      'ticket_id' =>'44091564',
                   'hidden'      => 0,
                   'qty'         => 10
      );
  $tickets[] =  array('ticket_type' => 'SD',
      'ticket_id' =>'44091561',
                   'hidden'      => 0,
                   'qty'         => 6
      );

  //generate access code for each ticket type
  $digits = 3;
  $charIP = $entry['ip'];
  $rand =  substr(base_convert($charIP, 10, 36),0,$digits);

  $EB_event_id = '21038172741';
  foreach($tickets as $ticket){
    $hidden = $ticket['hidden'];
    $accessCode = $ticket['ticket_type'].$entryID.$rand;
    $args = array(
      'id'   => $EB_event_id,
      'data' => 'access_codes',
      'create' => array(
        'access_code.code'               => $accessCode,
        'access_code.ticket_ids'         => $ticket['ticket_id'],
        'access_code.quantity_available' => $ticket['qty']
      )
    );

    //call eventbrite to create access code
    $access_codes = $eventbrite->events($args);
    if(isset($access_codes->status_code)&&$access_codes->status_code==400){
      $response['msg'] =  $access_codes->error_description;
      exit;
    }else{
      $response[$accessCode] = $access_codes->resource_uri;
    }

    //save access codes to db
    $dbSQL = 'INSERT INTO `eb_entry_access_code`(`entry_id`, `access_code`, `hidden`,EBticket_id) '
            . ' VALUES ('.$entryID.',"'.$accessCode.'",'.$hidden.','.$ticket['ticket_id'].')'
            . ' on duplicate key update access_code = "'.$accessCode.'"';

    $wpdb->get_results($dbSQL);
  }
}
