<?php
/*
 * this script will hold all the cronjobs for makerfaire
 */

//for testing
/*define( 'BLOCK_LOAD', true );
require_once( '../../../../wp-config.php' );
require_once( '../../../../wp-includes/wp-db.php' );
$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
build_wp_mf_maker(); //for testing*/

//this cron action will create the JSON files used by the blue ribbon page
add_action('cron_ribbonJSON', 'build_ribbonJSON');

function build_ribbonJSON(){
  global $wpdb;
  require_once( TEMPLATEPATH. '/partials/ribbonJSON.php' );

  $yearSql  = $wpdb->get_results("SELECT distinct(year) FROM wp_mf_ribbons  where entry_id > 0 order by year desc");

  foreach($yearSql as $year){
    $json = createJSON($year->year);
    //write json file
    unlink(TEMPLATEPATH.'/partials/data/'.$year->year.'ribbonData.json'); //delete json file if exists
    $fp = fopen(TEMPLATEPATH.'/partials/data/'.$year->year.'ribbonData.json', 'w');//create json file

    fwrite($fp, $json);
    fclose($fp);
  }
}

/*
 * Cron process to create MAT records for specific forms
 */
add_action('cron_update_mfTables', 'update_mfTables',10,3);

function update_mfTables($form,$limit,$start){
  error_log('updating maker tables for form '. $form.' ('.$start.', '.$limit.')');

  global $wpdb;
  $sql = "Select id
            from wp_rg_lead
           where status <> 'trash' and form_id  = $form "
          //. " and id > 60701"
       . " ORDER BY `wp_rg_lead`.`id` ASC "
          //. "limit 0, 100"
          ;
  if($limit!="0"){
    $sql .= " limit ".$start.', '.$limit;
  }
  $results = $wpdb->get_results($sql);
  foreach($results as $row){
    error_log('processing '. $row->id);
    //update maker table information
    GFRMTHELPER::updateMakerTables($row->id);
  }
  error_log('end updating maker tables for form '. $form.' ('.$start.', '.$limit.')');
}

/* This cron job is triggered daily.
 * It looks for accepted records on current faires and checks if tickets have been created for them.
    If they have not, it will start the process to request new tickets.
 */
add_action('cron_eb_ticketing', 'cron_genEBtickets');

function cron_genEBtickets(){
  global $wpdb;
  $sql =  "SELECT lead_id "
        . "FROM   wp_mf_faire, wp_rg_lead_detail "
        . "       left outer join eb_entry_access_code on wp_rg_lead_detail.lead_id =eb_entry_access_code.entry_id "
        . "WHERE  field_number=303 and value='Accepted' "
          . " and end_dt > now() "
          . " and FIND_IN_SET (wp_rg_lead_detail.form_id,wp_mf_faire.form_ids)> 0 "
          . " and eb_entry_access_code.EBticket_id is NULL "
          . " and (select EB_event_id from eb_event where wp_mf_faire_id = wp_mf_faire.id limit 1) is not NULL";
  $sql = "select lead_id, EBticket_id "
          . "from wp_mf_faire, wp_rg_lead_detail "
          . "left outer join eb_entry_access_code on wp_rg_lead_detail.lead_id =eb_entry_access_code.entry_id "
          . "where field_number=303 and value='Accepted' "
          . "and end_dt > now() "
          . "and FIND_IN_SET (wp_rg_lead_detail.form_id,wp_mf_faire.form_ids)> 0 "
          . "and eb_entry_access_code.EBticket_id is NULL ORDER BY `wp_rg_lead_detail`.`lead_id` ASC";
  $results = $wpdb->get_results($sql);
  foreach($results as $entry){
    error_log('Creating ticket codes for '.$entry->lead_id);
    $response = genEBtickets($entry->lead_id);
    if(isset($response['msg']))
      error_log('Ticket Response - '.$response['msg']);
  }
}