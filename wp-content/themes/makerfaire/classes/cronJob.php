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

/*
 * Cron process to create MAT records for specific forms
 */
add_action('cron_update_mfTables', 'update_mfTables',10,3);

function update_mfTables($form,$limit=0,$start){
  error_log('updating maker tables for form '. $form.' ('.$start.', '.$limit.')');

  global $wpdb;
  $sql = "Select id
            from wp_rg_lead
           where form_id  = $form "
       . " ORDER BY `wp_rg_lead`.`id` ASC "
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
          . " and (select EB_event_id from eb_event where wp_mf_faire_id = wp_mf_faire.id limit 1) is not NULL"
          . " and wp_rg_lead_detail.form_id != 120 ";

$results = $wpdb->get_results($sql);
  foreach($results as $entry){
    echo 'Creating ticket codes for '.$entry->lead_id.'<br/>';
    $response = genEBtickets($entry->lead_id);
    if(isset($response['msg']))
      echo 'Ticket Response - '.$response['msg'].'<br/>';
  }
}