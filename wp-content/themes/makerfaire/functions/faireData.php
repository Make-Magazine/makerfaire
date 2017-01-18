<?php

function get_faire_by_shortid($faire_id='') {
  global $wpdb;

  $faireData = array();
  if($faire_id!=''){
    //based on maker email retrieve maker information from the DB
    $results = $wpdb->get_row("SELECT * FROM wp_mf_faire WHERE faire='$faire_id'", ARRAY_A );
;
    //if faire found
    if ( null !== $results ) {
      $faireData['faire_name']        = $results['faire_name'];
      $faireData['url_path']          = $results['url_path'];
      $faireData['faire_location']    = $results['faire_location'];
      $faireData['form_ids']          = $results['form_ids'];
      $faireData['non_public_forms']  = $results['non_public_forms'];
      $faireData['start_dt']          = $results['start_dt'];
      $faireData['end_dt']            = $results['end_dt'];
      $faireData['time_zone']         = $results['time_zone'];
      $faireData['show_sched']        = $results['show_sched'];
    }
  }
  return $faireData;
}

