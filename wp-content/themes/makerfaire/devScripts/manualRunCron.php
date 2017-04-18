<?php
include 'db_connect.php';
global $wpdb;


if(isset($_GET['cron'])){
  echo('beginning process<br/>');
  if($_GET['cron']=='genEBtickets'){
    mancron_genEBtickets();
  }elseif($_GET['cron']=='build_ribbonJSON'){
    build_ribbonJSON();
  }elseif($_GET['cron']=='cronRmtData'){
    if(isset($_GET['form'])){
      cronRmtData($_GET['form'],0,0);
    }
  }
  echo('ending process');
}else{
  echo 'Please add process name in cron variable to run.<br/>';
  echo 'Options are:<br/>'
  . '?cron=genEBtickets<br/>'
  . '?cron=build_ribbonJSON<Br/>'
  . '?cron=cronRmtData&form=999<Br/>';
}

function mancron_genEBtickets(){
  global $wpdb;
  $sql =  "SELECT lead_id "
        . "FROM   wp_mf_faire, wp_rg_lead_detail "
        . "       left outer join eb_entry_access_code on wp_rg_lead_detail.lead_id =eb_entry_access_code.entry_id "
        . "WHERE  field_number=303 and value='Accepted' "
          . " and end_dt > now() "
          . " and FIND_IN_SET (wp_rg_lead_detail.form_id,wp_mf_faire.form_ids)> 0 "
          . " and eb_entry_access_code.EBticket_id is NULL "
          . " and (select EB_event_id from eb_event where wp_mf_faire_id = wp_mf_faire.id limit 1) is not NULL"
          . " and wp_rg_lead_detail.form_id != 120 "
          . " limit 20";
  /*$sql = "select lead_id, EBticket_id "
          . "from wp_mf_faire, wp_rg_lead_detail "
          . "left outer join eb_entry_access_code on wp_rg_lead_detail.lead_id =eb_entry_access_code.entry_id "
          . "where field_number=303 and value='Accepted' "
          . "and end_dt > now() "
          . "and FIND_IN_SET (wp_rg_lead_detail.form_id,wp_mf_faire.form_ids)> 0 "
          . "and eb_entry_access_code.EBticket_id is NULL ORDER BY `wp_rg_lead_detail`.`lead_id` ASC";*/
  $results = $wpdb->get_results($sql);
  foreach($results as $entry){
    echo 'Creating ticket codes for '.$entry->lead_id.'<br/>';
    $response = genEBtickets($entry->lead_id);
    if(isset($response['msg']))
      echo 'Ticket Response - '.$response['msg'].'<br/>';
  }
}

function cronRmtData($formID,$limit=0,$start=0) {
  echo 'Updating RMT for form '. $formID.'<br/>';

  global $wpdb;
  $sql = "Select id from wp_rg_lead where form_id  = $formID  ORDER BY `wp_rg_lead`.`id` ASC ";
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

