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
//add_action('cron_update_mfTables', 'update_mfTables',10,3);

function update_mfTables($form,$limit=0,$start=0){
  error_log('updating maker tables for form '. $form.' ('.$start.', '.$limit.')');

  global $wpdb;
  $sql = "Select id
            from wp_gf_entry
           where form_id  = $form "
       . " ORDER BY `wp_gf_entry`.`id` ASC "
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
//add_action('cron_eb_ticketing', 'cron_genEBtickets');

function cron_genEBtickets(){
  global $wpdb;
  $sql =  "SELECT           entry_id "
        . "FROM             wp_mf_faire, wp_gf_entry_meta "
        . "LEFT OUTER JOIN  eb_entry_access_code ON wp_gf_entry_meta.entry_id = eb_entry_access_code.entry_id "
        . "WHERE  wp_gf_entry_meta.meta_key ='303' and wp_gf_entry_meta.meta_value='Accepted' "
        . "  AND end_dt > now() "
        . "  AND FIND_IN_SET (wp_gf_entry_meta.form_id,wp_mf_faire.form_ids)> 0 "
        . "  AND eb_entry_access_code.EBticket_id is NULL "
        . "  AND (select EB_event_id from eb_event where wp_mf_faire_id = wp_mf_faire.id limit 1) is not NULL"
        . "  AND wp_gf_entry_meta.form_id != 120 ";

$results = $wpdb->get_results($sql);
  foreach($results as $entry){
    echo 'Creating ticket codes for '.$entry->entry_id.'<br/>';
    $response = genEBtickets($entry->entry_id);
    if(isset($response['msg']))
      echo 'Ticket Response - '.$response['msg'].'<br/>';
  }
}