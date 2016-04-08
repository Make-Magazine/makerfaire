<?php
include 'db_connect.php';
echo('beginning process<br/>');
  global $wpdb;
  $sql = "Select wp_rg_lead.id, wp_rg_lead.form_id, wp_rg_lead_meta.meta_value"
          . " from wp_rg_lead"
          . " left outer join wp_rg_lead_meta"
          . " on wp_rg_lead_meta.meta_key = 'res_status'"
          . " and wp_rg_lead_meta.lead_id = wp_rg_lead.ID"
          . " where wp_rg_lead.status <> 'trash'"
          . " and wp_rg_lead.form_id in (46,45,49,47,71)"
          . " and meta_value != 'ready'"
          . " ORDER BY `wp_rg_lead`.`ID` ASC";
  //and res_status !='ready'
  //form_id in (46,45,49,47,71) ORDER BY `wp_rg_lead`.`id` ASC";
  $form45  = GFAPI::get_form(45);
  $form46  = GFAPI::get_form(46);
  $form47  = GFAPI::get_form(47);
  $form49  = GFAPI::get_form(49);
  $form71  = GFAPI::get_form(71);
  $results = $wpdb->get_results($sql);
  foreach($results as $row){
    $form2use ='form'.$row->form_id;
    $form  = $$form2use;
    $entry = GFAPI::get_entry(esc_attr($row->id));
    echo 'updated '.$entry['id'].'<br/>';
    //GFRMTHELPER::gravityforms_makerInfo($entry,$form);
    //format Entry information
    $entryData = GFRMTHELPER::gravityforms_format_record($entry,$form);

    //build/update RMT data
    GFRMTHELPER::buildRmtData($entryData);
  }
  echo('ending process');
