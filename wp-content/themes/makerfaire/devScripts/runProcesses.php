<?php
include 'db_connect.php';
global $wpdb;
echo('beginning process<br/>');
$form_id = (isset($_GET['formID'])?$_GET['formID']:46);

if(isset($_GET['init'])){
  $init = $_GET['init'];
  switch ($init){
    case 'entity';
      $sql = "truncate wp_mf_entity";
      $wpdb->get_results($sql);
      echo 'entity table truncated<br/>';
      break;
    case 'maker':
      $sql = "truncate wp_mf_maker";
      $wpdb->get_results($sql);
      echo 'maker table truncated<br/>';
      break;
    case 'maker2entity':
      echo 'maker 2 entity table truncated<br/>';
      $sql = "truncate wp_mf_maker_to_entity";
      $wpdb->get_results($sql);
      break;
    default:
      echo 'all maker tables truncated<br/>';
      $sql = "truncate wp_mf_entity;";
      $wpdb->get_results($sql);
      $sql = "truncate wp_mf_maker;";
      $wpdb->get_results($sql);
      $sql = "truncate wp_mf_maker_to_entity;";
      $wpdb->get_results($sql);
      break;
  }
  //run sql to truncate selected tables
 // $wpdb->get_results($sql);
}

  $sql = "Select wp_rg_lead.id, wp_rg_lead.form_id"
          . " from wp_rg_lead"
          . " where wp_rg_lead.status = 'active'"
          ."  and wp_rg_lead.form_id = $form_id"
          . " ORDER BY `wp_rg_lead`.`ID` ASC";
if(isset($_GET['page'])){
  $page = $_GET['page'];
  $limit =500;
  if($page==1){
    $start = 0;
  }else{
    $start=($page-1)*$limit;
  }
  $sql.=" limit ".$start.', '.$limit;
}

  $form  = GFAPI::get_form($form_id);

  $results = $wpdb->get_results($sql);
  foreach($results as $row){
    $entry = GFAPI::get_entry(esc_attr($row->id));
    echo 'updating '.$entry['id'].'<br/>';

    $current_user = wp_get_current_user();

    //require_once our model
    require_once( get_template_directory().'/models/maker.php' );

    //instantiate the model
    $maker   = new maker($current_user->user_email);
    $maker->updateMakerTable();
  }
  echo('ending process');
