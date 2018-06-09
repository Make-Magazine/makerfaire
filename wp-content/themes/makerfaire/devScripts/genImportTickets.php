<?php
include 'db_connect.php';
error_reporting(E_ALL); ini_set('display_errors', '1');

//process entries 66667 to 66745
//$entryArr = array(66667, 66668, 66669, 66670, 66671, 66672, 66673, 66674, 66675, 66676, 66677);
//$entryArr = array(66678, 66679, 66680, 66681, 66682, 66683, 66684, 66685, 66686, 66687, 66688);
//$entryArr = array(66689, 66690, 66691, 66692, 66693, 66694, 66695, 66696, 66697, 66698, 66699);
//$entryArr = array(66700, 66701, 66702, 66703, 66704, 66705, 66706, 66707, 66708, 66709, 66710);
//$entryArr = array(66711, 66712, 66713, 66714, 66715, 66716, 66717, 66718, 66719, 66720, 66721);
//$entryArr = array(66722, 66723, 66724, 66725, 66726, 66727, 66728, 66729, 66730, 66731, 66732);
//$entryArr = array(66733, 66734, 66735, 66736, 66737, 66738, 66739, 66740, 66741, 66742, 66743);
//$entryArr = array(66744, 66745);

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
  /* SF Bazaar
   * Entries associated with 65230

      66667 to 66745

      2 Maker Entry Passes, ME
      6 Discount Tickets, SD and FD
    ME (Maker Entry Pass)   = 2 43630850047	82114030
    SD (Complimentary)      = 6 42720091945	80480133
    FD (Saturday Discount)  = 6 42720091945	80480132
   */

  $tickets = array();

  /*  ME (Maker Entry Pass) = 2   43630850047	82114030 */
  $tickets[] =  array(
      'eventID'     => 43630850047,
      'ticket_type' => 'ME',
      'ticket_id'   => '82114030',
      'hidden'      => 1,
      'qty'         => 2
      );

  /*  SD (Complimentary) = 6    42720091945	80480133  */
  $tickets[] =  array(
      'eventID'     => 42720091945,
      'ticket_type' => 'SD',
      'ticket_id'   =>'80480133',
      'hidden'      => 1,
      'qty'         => 6
      );

  /*  FD (Saturday Discount) = 6  42720091945	80480132 */
  $tickets[] =  array(
      'eventID'     => 42720091945,
      'ticket_type' => 'FD',
      'ticket_id'   => '80480132',
      'hidden'      => 1,
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
