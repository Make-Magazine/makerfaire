<?php
include 'db_connect.php';
global $wpdb;


if(isset($_GET['cron'])){
  echo('beginning process<br/>');
  switch ($_GET['cron']) {
    case 'genEBtickets':
      mancron_genEBtickets();
      break;
    case 'cronRmtData':
      if(isset($_GET['form'])){
        cronRmtData($_GET['form'],0,0);
      }else{
        echo 'You must pass a form ID';
      }
      break;
    case 'genManTickets':
      $entryID  = (isset($_GET['entryID'])?$_GET['entryID']:0);
      $parentID = (isset($_GET['parentID'])?$_GET['parentID']:0);
      genManTickets($entryID, $parentID);
      break;
    case 'createSignZip':
      $area = (isset($_GET['area'])?$_GET['area']:'');
      $area = str_replace('_',' ',$area ?? '');
      createMFSignZip($area);
      break;
    case 'update_mfTables':
      updateMfTablesPre();
      break;
    default:
      break;
  }


  echo 'ending process';
}else{
  echo 'Please add process name in cron variable to run.<br/>';
  echo 'Options are:<br/>'
  . '?cron=genEBtickets<br/>'
  . '?cron=cronRmtData&form=999<Br/>'
  . '?cron=genManTickets&entryID=999&parentID=999<br/>'
  . '?cron=createSignZip&area=abcd<br/>'
  . '?cron=update_mfTables&form=999&faire=ABCD&limit&start';
}

function updateMfTablesPre(){
  global $wpdb;
  $form  = (isset($_GET['form'])?$_GET['form']:'');
  $limit = (isset($_GET['limit'])?$_GET['limit']:0);
  $start = (isset($_GET['start'])?$_GET['start']:0);
  $faire = (isset($_GET['faire'])?$_GET['faire']:'');
  $type  = (isset($_GET['type'])?$_GET['type']:'');

  //check faire first
  if($faire!=''){
    if($type==''){
      echo "Fail. You need to pass a form type using the variable 'type' to continue<Br/>";
      echo 'Valid Form Types Are:';
      echo '<ul>';
      echo ' <li>Exhibit</li>';
      echo ' <li>Presentation</li>';
      echo ' <li>Performance</li>';
      echo ' <li>Startup Sponsor</li>';
      echo ' <li>Sponsor</li>';
      echo '</ul>';
    } else {
      $type = ($type == 'sstartup' ? 'Sponsor Startup': ucwords($type));
      $faireData = $wpdb->get_row( "select form_ids from wp_mf_faire where faire='".$faire."'");
      $formIDs = explode(',',trim($faireData->form_ids));
      foreach($formIDs as $formID){
        $formData = GFAPI::get_form($formID);
        $form_type = (isset($formData['form_type'])?$formData['form_type']:'');
        if($form_type=='') echo 'Form Type not set for form '.$formID.'<br/>';

        if($type != $form_type){
          continue;
        }else{
          echo 'updating MF tables for form '.$formID.'<br/>';
          echo ' form type = '.$formData['form_type'].'<br/>';
          update_mfTables($formID,$limit,$start);
        }
      }
    }
  }elseif($form!=''){
    //then use form if faire not set
    echo 'updating MF tables for form '.$form;
    $formData = GFAPI::get_form($form);
    echo ' form type = '.$formData['form_type'].'<br/>';
    update_mfTables($form,$limit,$start);
  }else{
    echo 'Fail. You need to at least give me a form id or faire id to use';
    echo 'Valid Form Types Are:';
    echo '<ul>';
    echo ' <li>Exhibit</li>';
    echo ' <li>Presentation</li>';
    echo ' <li>Performance</li>';
    echo ' <li>Startup Sponsor</li>';
    echo ' <li>Sponsor</li>';
    echo '</ul>';
  }
}

function createMFSignZip($area) {
  global $wpdb;
  echo 'Creating zip file for '.$area.'<br/>';
  $response = array();
  $statusFilter = 'accAndProp';
  $type         = 'area';
  $faire        = 'BA17';
  $signType     = 'maker';


    //create array of subareas
    $sql = "SELECT wp_gf_entry.ID as entry_id, wp_gf_entry.form_id,
          (select meta_value as value from wp_gf_entry_meta where meta_key='303' and wp_gf_entry_meta.entry_id = wp_gf_entry.ID) as entry_status,
          wp_mf_faire_subarea.area_id, wp_mf_faire_area.area, wp_mf_location.subarea_id, wp_mf_faire_subarea.subarea,wp_mf_location.location
          FROM wp_mf_faire, wp_gf_entry
          left outer join wp_mf_location on wp_gf_entry.ID  = wp_mf_location.entry_id
          left outer join wp_mf_faire_subarea on wp_mf_location.subarea_id  = wp_mf_faire_subarea.id
          left outer join wp_mf_faire_area    on wp_mf_faire_subarea.area_id  = wp_mf_faire_area.id
          where faire = '$faire'
          and wp_gf_entry.status  != 'trash'
          and wp_mf_faire_area.area = '$area'
          and FIND_IN_SET (wp_gf_entry.form_id,wp_mf_faire.form_ids)> 0
          and FIND_IN_SET (wp_gf_entry.form_id,wp_mf_faire.non_public_forms)<= 0";

    $results = $wpdb->get_results($sql);
    $entries = array();

    foreach($results as $row){
      //exclude records based on status filter
      if($statusFilter =='accepted'   && $row->entry_status!='Accepted')  continue;
      if($statusFilter =='accAndProp' && ($row->entry_status!='Accepted' && $row->entry_status!='Proposed')){
        continue;
      }
      $area    = ($row->area    != NULL ? $row->area:'No-Area');
      $subarea = ($row->subarea != NULL ? $row->subarea:'No-subArea');

      //create friendly names for file creation
      $area = str_replace(' ','_',$area ?? '');
      $subarea = str_replace(' ','_',$subarea ?? '');
      //build array output based on selected type
      if($type=='area') {
        $entries[$area][$row->entry_status][] = $row->entry_id;
      }
      if($type=='subarea') {
        $entries[$area.'-'.$subarea][$row->entry_status][] = $row->entry_id;
      }
      if($type=='faire') {
        $entries['faire'][$row->entry_status][] = $row->entry_id;
      }
    } //end looping thru sql results

  $error = '';

  //build zip files based on selected type
  foreach($entries as $typeKey=>$entType){
     //create zip file
    $zip = new ZipArchive();

    $filepath = get_template_directory()."/signs/".$faire.'/'.$signType.'/';
    if (!file_exists($filepath.'zip')) {
      mkdir($filepath.'zip', 0777, true);
    }
    $filename = $faire."-".$typeKey."-faire".$signType.".zip";

    $zip->open($filepath.'zip/'.$filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    foreach($entType as $statusKey=>$status){
      $subPath = $typeKey.'/'.$statusKey.'/';
      foreach($status as $entryID) {
        //write zip file
        $file = $entryID.'.pdf';
        if (file_exists($filepath.$file)) {
          $zip->addFile($filepath.$file,$file);
        }else{
          $error .= 'Missing PDF for ' .$entryID.'<br/>';
        }
      }
    }
    //close zip file
    if (!$zip->status == ZIPARCHIVE::ER_OK)
      echo "Failed to write files to zip\n";
    $zip->close();
  } //end looping thru entry array
}
function mancron_genEBtickets(){
  global $wpdb;
  $testing=(isset($_GET['testing'])?TRUE:'');
  //run for new ticket id's 
  $runAll = (isset($_GET['runAll'])?TRUE:false);
  
  //this only looks for faires that are not past their end date
  if($runAll == true){
    //run through all entries looking for new tickets
    $sql =  "SELECT wp_gf_entry_meta.entry_id "
    . "FROM   wp_mf_faire, wp_gf_entry_meta "
    . "WHERE  meta_key='303' and meta_value='Accepted' "
      . " and end_dt > now() "
      . " and FIND_IN_SET (wp_gf_entry_meta.form_id,wp_mf_faire.form_ids)> 0 "     
      . " and (select EB_event_id from eb_event where wp_mf_faire_id = wp_mf_faire.id limit 1) is not NULL"          
      . " and wp_gf_entry_meta.form_id != 120 ";
  }else{ 
    //only look for new entries
    $sql =  "SELECT wp_gf_entry_meta.entry_id "
    . "FROM   wp_mf_faire, wp_gf_entry_meta "
    . "       left outer join eb_entry_access_code on wp_gf_entry_meta.entry_id =eb_entry_access_code.entry_id "
    . "WHERE  meta_key='303' and meta_value='Accepted' "
      . " and end_dt > now() "
      . " and FIND_IN_SET (wp_gf_entry_meta.form_id,wp_mf_faire.form_ids)> 0 "     
      . " and eb_entry_access_code.EBticket_id is NULL "
      . " and (select EB_event_id from eb_event where wp_mf_faire_id = wp_mf_faire.id limit 1) is not NULL"          
      . " and wp_gf_entry_meta.form_id != 120 "
      . " limit 20";
  } 

  $results = $wpdb->get_results($sql);
  foreach($results as $entry){    
    $response = genEBtickets($entry->entry_id, $testing);    
    if(isset($response['msg']))
      echo 'Creating ticket codes for '.$entry->entry_id . ' - '.$response['msg'];
  }
}

function cronRmtData($formID,$limit=0,$start=0) {
  echo 'Updating RMT for form '. $formID.'<br/>';

  global $wpdb;
  $sql = "Select id from wp_gf_entry where form_id  = $formID  ORDER BY `wp_gf_entry`.`id` ASC ";
  if($limit!="0"){
    $sql .= " limit ".$start.', '.$limit;
  }

  $results = $wpdb->get_results($sql);
  foreach($results as $row){
    echo 'processing '. $row->id.'<br/>';
    $entryID = $row->id;
    $entry    = GFAPI::get_entry($entryID);
    $form_id  = $entry['form_id'];
    $form     = GFAPI::get_form($form_id);

    //update maker table information
    GFRMTHELPER::buildRmtData($entry, $form);
  }
}

function genManTickets($entryID=0, $parentID=0){
  global $wpdb;
  if (!class_exists('eventbrite')) {
    require_once('../classes/eventbrite.class.inc');
  }
  $eventbrite = new eventbrite();

  //generate eventbrite tickets
  /*
    BA17:
      ME - 2 Maker Entry Passes - eid 26455360696, ticket id 52207452(event id 3)
      SC - 2 Comp tickets       - eid 25957796468, ticket id 52164508(event id 4)
      SD - 2 discount tickets   - eid 25957796468, ticket id 52164509(event id 4)

   * ME - 2 Maker Entry Passes -        eid: 31946847882  ticket code 61774227
     FD - 10 Friday discount tickets -  eid 31971408343   ticket code 61987493
     SD -6 Sat/Sun discount tickets -   eid 31971408343   ticket code 61987494
   */
  $tickets = array();
  $tickets[] =  array('ticket_type' => 'ME', 'ticket_id' => '61774227', 'hidden' => 0, 'qty' =>  2, 'eid' => 31946847882);
  $tickets[] =  array('ticket_type' => 'FD', 'ticket_id' => '61987493', 'hidden' => 0, 'qty' => 10, 'eid' => 31971408343);
  $tickets[] =  array('ticket_type' => 'SD', 'ticket_id' => '61987494', 'hidden' => 0, 'qty' =>  6, 'eid' => 31971408343);
  if($entryID!=0){
    //process tickets for single entry
    echo 'processing entry id '.$entryID;
  }elseif($parentID!=0){
    echo 'Processing parent id '.$parentID.'<br/>';
    //process group entry tickets
    $sql = "SELECT childID FROM `wp_mf_lead_rel` where parentID = ".$parentID;

    $results = $wpdb->get_results($sql);
    foreach($results as $row){
      $entryID = $row->childID;
      echo 'Creating tickets for '.$entryID.'<br/>';
      $entry    = GFAPI::get_entry($entryID);

      //generate access code for each ticket type
      $digits = 3;
      $charIP = (string) $entry['ip'];
      $rand   =  substr(base_convert($charIP, 10, 36),0,$digits);

      foreach($tickets as $ticket){
        $hidden     = $ticket['hidden'];
        $accessCode = $ticket['ticket_type'] . $entryID . $rand;
        $args = array(
          'id'   => $ticket['eid'],
          'data' => 'access_codes',
          'create' => array(
            'access_code.code'               => $accessCode,
            'access_code.ticket_ids'         => $ticket['ticket_id'],
            'access_code.quantity_available' => $ticket['qty']
          )
        );

        //call eventbrite to create access code
        $access_codes = $eventbrite->events($args);
        if(isset($access_codes->status_code) && $access_codes->status_code==400){
          var_dump($access_codes->error_description); echo '<br/>';
          exit;
        }else{
          var_dump($access_codes->resource_uri); echo '<br/>';
        }

        //save access codes to db
        $dbSQL = 'INSERT INTO `eb_entry_access_code`(`entry_id`, `access_code`, `hidden`,EBticket_id) '
                . ' VALUES ('.$entryID.',"'.$accessCode.'",'.$hidden.','.$ticket['ticket_id'].')'
                . ' on duplicate key update access_code = "'.$accessCode.'"';

        $wpdb->get_results($dbSQL);
      }
    }
  }
}