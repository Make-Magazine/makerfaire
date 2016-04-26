<?php
include 'db_connect.php';
$wp_config_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
include $wp_config_path.'/wp-content/themes/makerfaire/classes/eventbrite.class.inc';
$eventbrite = new eventbrite();

// find all BA16 Accepted entries (that do not yet have ticket codes)
// and generate them
$sql = 'SELECT *, '
        . '(SELECT ip FROM `wp_rg_lead` where lead_id = wp_rg_lead.id) as ip FROM `wp_rg_lead_detail` '
        . 'left outer join `eb_entry_access_code` on lead_id = entry_id '
        . 'where form_id in(46,45,49,47,71) and field_number=303 and value="Accepted" and EBticket_id is NULL ';
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