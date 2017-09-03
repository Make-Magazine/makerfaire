<?php
include 'db_connect.php';
error_reporting(E_ALL); ini_set('display_errors', '1');

//process entries 64121-64181, 64184-64187
//$entryArr = array(64121, 64122, 64123, 64124, 64125, 64126, 64127, 64128, 64129, 64130, 64181);
//$entryArr = array(64131, 64132, 64133, 64134, 64135, 64136, 64137, 64138, 64139, 64140, 64184);
//$entryArr = array(64141, 64142, 64143, 64144, 64145, 64146, 64147, 64148, 64149, 64150, 64185);
//$entryArr = array(64151, 64152, 64153, 64154, 64155, 64156, 64157, 64158, 64159, 64160, 64186);
//$entryArr = array(64161, 64162, 64163, 64164, 64165, 64166, 64167, 64168, 64169, 64170, 64187);
$entryArr = array(64171, 64172, 64173, 64174, 64175, 64176, 64177, 64178, 64179, 64180);

foreach($entryArr as $entryID){
  echo 'processing '.$entryID.'<br/>';
  prcNewEntry($entryID);
}


//process new entry
function prcNewEntry($entryID){
  global $wpdb;
  if (!class_exists('eventbrite')) {
    require_once('../classes/eventbrite.class.inc');
  }
  $eventbrite = new eventbrite();

  $entry    = GFAPI::get_entry($entryID);

  //generate eventbrite tickets
  //1 = hidden
  /* Bust Craftacular
    ME (Maker Entry Pass)   = 2
    SC (Complimentary)      = 2
    SD (Saturday Discount)  = 2 */

  $tickets = array();
  $tickets[] =  array(
      'eventID'     => 35774636902,
      'ticket_type' => 'ME',
      'ticket_id'   => '68772917',
      'hidden'      => 0,
      'qty'         => 2
      );
  $tickets[] =  array(
      'eventID'     => 35712361635,
      'ticket_type' => 'SC',
      'ticket_id'   =>'68660989',
      'hidden'      => 0,
      'qty'         => 2
      );
  $tickets[] =  array(
      'eventID'     => 35712361635,
      'ticket_type' => 'SD',
      'ticket_id'   => '68660990',
      'hidden'      => 0,
      'qty'         => 2
      );
  //generate access code for each ticket type
  $digits = 3;
  $charIP = $entry['ip'];
  $rand =  substr(base_convert($charIP, 10, 36),0,$digits);

  foreach($tickets as $ticket){
    $EB_event_id = $ticket['eventID'];
    $hidden     = $ticket['hidden'];
    $accessCode = $ticket['ticket_type'].$entryID.$rand;
    $args = array(
      'id'      => $EB_event_id,
      'data'    => 'access_codes',
      'create'  => array(
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
    echo 'generatd '.$accessCode.' for '.$entryID.'<br/>';
  }
}
