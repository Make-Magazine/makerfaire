<?php
include 'db_connect.php';
echo('beginning process<br/>');
$form_id = (isset($_GET['formID'])?$_GET['formID']:46);
  global $wpdb;
  $sql = "Select wp_rg_lead.id, wp_rg_lead.form_id"
          . " from wp_rg_lead"
          . " where wp_rg_lead.status = 'active'"
          ."  and wp_rg_lead.form_id = $form_id"
          . " ORDER BY `wp_rg_lead`.`ID` ASC";

  $form  = GFAPI::get_form($form_id);

  $results = $wpdb->get_results($sql);
  foreach($results as $row){
    $entry = GFAPI::get_entry(esc_attr($row->id));
    echo 'updating '.$entry['id'].'<br/>';

    //format Entry information
    $entryData = GFRMTHELPER::gravityforms_format_record($entry,$form);
    //var_dump($entryData);
    //update maker table information
    GFRMTHELPER::updateMakerTable($entryData);
  }
  echo('ending process');
