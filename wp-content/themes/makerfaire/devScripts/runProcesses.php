<?php
include 'db_connect.php';
echo('beginning process<br/>');
  global $wpdb;
  $sql = "Select wp_rg_lead.id, wp_rg_lead.form_id"
          . " from wp_rg_lead"
          . " where wp_rg_lead.status <> 'trash'"
          //. " and wp_rg_lead.form_id in (46,45,49,47,71)"
          ."  and wp_rg_lead.form_id = 45"
         // . " and wp_rg_lead.id = 55180"
          . " ORDER BY `wp_rg_lead`.`ID` ASC";

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
    echo 'updating '.$entry['id'].'<br/>';

    //format Entry information
    $entryData = GFRMTHELPER::gravityforms_format_record($entry,$form);
//var_dump($entryData);
    //update maker table information
    GFRMTHELPER::updateMakerTable($entryData);
  }
  echo('ending process');
