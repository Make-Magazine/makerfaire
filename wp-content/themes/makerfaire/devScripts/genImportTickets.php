<?php
include 'db_connect.php';
error_reporting(E_ALL); ini_set('display_errors', '1');


/*
 *    Generate SF bazaar tickets
   2 ME maker entry passes
   6 SD sat/sun discounts
   6 FD Fri discounts   
*/
//$entryArr = array(70531, 70532, 70533, 70534, 70535, 70536, 70537, 70538, 70539, 70540, 70541);
   //$entryArr = array(70542, 70543, 70544, 70545, 70546, 70547, 70548, 70549, 70550, 70551, 70552);
   //$entryArr = array(70553, 70554, 70555, 70556, 70557, 70558, 70559, 70560, 70561, 70562, 70563);
   //$entryArr = array(70564, 70565, 70566, 70567, 70568, 70569, 70570, 70571, 70572, 70573, 70574);
   $entryArr = array(70575, 70576, 70577, 70578, 70579, 70580);



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
  //1 = show
  /* SF Bazaar
   * 
      2 ME maker entry passes
      6 SD sat/sun discounts
      6 FD Fri discounts         
   *                             eventID      ticket_id
    ME (Maker Entry Pass)   = 2 57650358775	106612909
    SD (Complimentary)      = 6 55937113412	103742828
    FD (Saturday Discount)  = 6 55937113412	103742827
   */

  $tickets = array();
  
  /*  ME (Maker Entry Pass) = 2  57650358775	106612909*/
  $tickets[] =  array(
      'eventID'     => 57650358775,
      'ticket_type' => 'ME',
      'ticket_id'   => '106612909',
      'hidden'      => 0,
      'qty'         => 2
      );

   /* SD (Complimentary)      = 6 55937113412	103742828 */  
  $tickets[] =  array(
      'eventID'     => 55937113412,
      'ticket_type' => 'SD',
      'ticket_id'   =>'103742828',
      'hidden'      => 0,
      'qty'         => 6
      );

  /* FD (Saturday Discount)  = 6 55937113412	103742827 */
  $tickets[] =  array(
      'eventID'     => 55937113412,
      'ticket_type' => 'FD',
      'ticket_id'   => '103742827',
      'hidden'      => 0,
      'qty'         => 6
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
