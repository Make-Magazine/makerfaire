<?php
/* Functions specific to EventBrite ticketing process */

//generate Eventbrite Access Codes for entry
function ebAccessTokens(){
  $entryID  = $_POST['entryID'];
  $response = genEBtickets($entryID);
  wp_send_json($response);
  exit;
}
add_action('wp_ajax_ebAccessTokens', 'ebAccessTokens');

// This function is called with ajax to update the hidden indicator for
// the eventbrite access codes
function ebUpdateAC(){
  global $wpdb;
  $response = array();
  $accessCode = (isset($_POST['accessCode'])?$_POST['accessCode']:'');
  $checked = (isset($_POST['checked'])?$_POST['checked']:0);
  if($accessCode!=''){
    $sql = 'update eb_entry_access_code set hidden= '.$checked.' where access_code = "'.$accessCode.'"';
    $wpdb->get_results($sql);
    $response['msg'] = '';
  }else{
    $response['msg'] = 'Error Updating the hidden property of this ticket code.  Please alert dev of the entry and ticket code you were updating';
  }
  wp_send_json($response);
  exit;
}
add_action('wp_ajax_ebUpdateAC', 'ebUpdateAC');

/* Used to generate ticket codes */
function genEBtickets($entryID){
  if (!class_exists('eventbrite')) {
    require_once(TEMPLATEPATH.'/classes/eventbrite.class.inc');
  }

  global $wpdb;
  $response = array();
  $entry    = GFAPI::get_entry( $entryID );
  $form_id  = $entry['form_id'];

  if(is_array($entry))
    $form = GFAPI::get_form($form_id);

  $form_type = $form['form_type'];

  //get faire ID for this form
  $sql = "select wp_mf_faire.ID,eb_event.ID as event_id, EB_event_id "
          . " from wp_mf_faire,eb_event "
          . " where FIND_IN_SET ($form_id,wp_mf_faire.form_ids)> 0"
          . " and wp_mf_faire.ID = eb_event.wp_mf_faire_id";
  $faire = $wpdb->get_results($sql);
  $faire_id      = (isset($faire[0]->ID) ? $faire[0]->ID:'');

  //MF table event ID
  $event_id      = (isset($faire[0]->event_id) ? $faire[0]->event_id:'');

  //event brite event ID
  $EB_event_id   = (isset($faire[0]->EB_event_id) ? $faire[0]->EB_event_id:'');

  //determine what ticket types to request
  $sql = 'select  eb_ticket_type.ticket_type, qty, hidden,ticketID as ticket_id
          from    eb_ticket_type
                  left outer join eb_eventToTicket on
                    eb_eventToTicket.eventID = '.$event_id.' and
                    eb_eventToTicket.ticket_type = eb_ticket_type.ticket_type,
                  wp_mf_form_types
          where   wp_mf_form_types.form_type="'.$form_type.'"    and
                  eb_ticket_type.event_id = '.$event_id.'        and
                  eb_ticket_type.form_type = wp_mf_form_types.ID and
                  ticketID is not null';
  
  $results = $wpdb->get_results($sql);
  if($wpdb->num_rows > 0){
    $eventbrite = new eventbrite();

    $digits = 3;

    $charIP = (string) $entry['ip'];
    $rand   =  substr(base_convert($charIP, 10, 36),0,$digits);
    foreach($results as $row){
      //generate access code for each ticket type
      $hidden     = $row->hidden;
      $accessCode = $row->ticket_type.$entryID.$rand;
      //if this access Code has already been created, do not resend to EB
      $ACcount = $wpdb->get_var('select count(*) from eb_entry_access_code where access_code = "'.$accessCode .'"');

      if($ACcount==0){
        $args = array(
          'id'   => $EB_event_id,
          'data' => 'access_codes',
          'create' => array(
            'access_code.code' => $accessCode,
            'access_code.ticket_ids' => $row->ticket_id,
            'access_code.quantity_available' => $row->qty
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
                . ' VALUES ('.$entryID.',"'.$accessCode.'",'.$hidden.','.$row->ticket_id.')'
                . ' on duplicate key update access_code = "'.$accessCode.'"';

        $wpdb->get_results($dbSQL);
      }
    }
    $response['msg'] = 'Access Codes generated.  Please refresh to see<br/>';
  }
  return $response;
}
add_action( 'sidebar_entry_update', 'genEBtickets', 10, 1 );