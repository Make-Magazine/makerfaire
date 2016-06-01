<?php
/* This function is used to update entry resources and entry attributes via AJAX */
function update_entry_resatt() {
  global $wpdb;
  $ID        = $_POST['ID'];
  $table     = $_POST['table'];
  //set who is updating the record
  $current_user = wp_get_current_user();

  if($ID==0){ //add new record
    $insertArr = $_POST['insertArr'];
    foreach($insertArr as $key=>$value){
      $fields[] =$key;
      $values[] =$value;
    }
      $sql = "insert into ".$table.' ('.implode(',',$fields).',user) VALUES ("'.implode('","',$values).'",'.$current_user->ID.')';
  }else{ //update existing record
    $newValue  = $_POST['newValue'];
    $fieldName = $_POST['fieldName'];
    $sql = "update ".$table.' set '.$fieldName .'="'.$newValue.'",user= '.$current_user->ID.' where ID='.$ID;
  }

  $wpdb->get_results($sql);
  if($ID==0)  $ID = $wpdb->insert_id;

  //set lockBit to locked
  $sql = "update ".$table.' set lockBit=1 where ID='.$ID;
  $wpdb->get_results($sql);

  //return the ID
  $response = array('message'=>'Saved','ID'=>$ID,'user'=>$current_user->display_name,'dateupdate'=>current_time('m/d/y h:i a'));
  wp_send_json( $response );

  // IMPORTANT: don't forget to "exit"
  exit;
}
add_action( 'wp_ajax_update-entry-resAtt', 'update_entry_resatt' );

/* This function is used to delete entry resources and entry attributes via AJAX */
function delete_entry_resatt() {
  global $wpdb;
  $table = (isset($_POST['table']) ? $_POST['table']:'');
  $ID    = (isset($_POST['ID'])    ? $_POST['ID']:0);
  $response = array('table'=>$table,'ID'=>$ID);
  if($ID != 0 && $table != ''){
    $sql = "DELETE from ".$table ." where ID =".$ID;
    $wpdb->get_results($sql);
    $response = array('message'=>'Deleted','ID'=>$ID);
  }
  wp_send_json( $response );
  // IMPORTANT: don't forget to "exit"
  exit;
}
add_action( 'wp_ajax_delete-entry-resAtt', 'delete_entry_resatt' );

/* This function is used to delete entry resources and entry attributes via AJAX */
function update_lock_resAtt() {
  global $wpdb;
  $table = (isset($_POST['table']) ? $_POST['table']:'');
  $ID    = (isset($_POST['ID'])    ? $_POST['ID']:0);
  $lock  = (isset($_POST['lock']) && $_POST['lock']==0 ? 1:0);
  $response = array('table'=>$table,'ID'=>$ID);
  if($ID != 0 && $table != ''){
    $sql = "update ".$table.' set lockBit='.$lock.' where ID='.$ID;
    $wpdb->get_results($sql);
    $response = array('message'=>'Updatd','ID'=>$ID);
  }
  wp_send_json( $response );
  // IMPORTANT: don't forget to "exit"
  exit;
}
add_action('wp_ajax_update-lock-resAtt','update_lock_resAtt');

