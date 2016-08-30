<?php
/* This function is used to update entry resources and entry attributes via AJAX */
function update_entry_resatt() {
  global $wpdb;
  $ID        = $_POST['ID'];
  $table     = $_POST['table'];
  //set who is updating the record
  $current_user = wp_get_current_user();
  $chgRptRec = array();

  if($ID==0){ //add new record
    $insertArr = $_POST['insertArr'];
    foreach($insertArr as $key=>$value){
      $fields[] = $key;
      $values[] = $value;
    }

    $sql = "insert into ".$table.' ('.implode(',',$fields).',user) VALUES ("'.implode('","',$values).'",'.$current_user->ID.')';
    $entry_id = $insertArr['entry_id'];
    //update change report for new recources/attributes/attention records added thru wp-admin
    switch ($table) {
      case 'wp_rmt_entry_resources':
        $fieldID = $insertArr['resource_id'];
        $type    = 'resource';
        $res = $wpdb->get_row('SELECT token FROM `wp_rmt_resources` where ID='.$fieldID);
        $token     = $res->token;
        $fields2Upd = array('qty','comment');

        break;
      case 'wp_rmt_entry_attributes':
        $fieldID = $insertArr['attribute_id'];
        $type    = 'attribute';
        $res = $wpdb->get_row('SELECT token FROM `wp_rmt_entry_att_categories` where ID='.$fieldID);
        $token     = $res->token;
        $fields2Upd = array('value','comment');

        break;
      case 'wp_rmt_entry_attn':
        $fieldID = $insertArr['attn_id'];
        $type    = 'Attention';
        $res = $wpdb->get_row('SELECT value as token FROM wp_rmt_attn where ID='.$fieldID);
        $token     = $res->token;
        $fields2Upd = array('comment');
        break;
      default:
        $fieldID = '';
        $type    = '';
        $token   = '';
        $fields2Upd = array();
        break;
    }

    foreach($fields2Upd as $fieldName){
      $chgRptRec[]= array('user_id'      => $current_user->ID, 'lead_id'           => $entry_id, 'form_id'    => 0,
                          'field_id'     => $fieldID,          'status_at_update'  => '',
                          'field_before' => '',                'field_after'       => $insertArr[$fieldName],
                          'fieldLabel'   => 'RMT '.$type.': '.$token.' - '.$fieldName);
    }

  }else{ //update existing record
    $newValue  = $_POST['newValue'];
    $fieldName = $_POST['fieldName'];
    $sql = "update ".$table.' set '.$fieldName .'="'.$newValue.'",user= '.$current_user->ID.' where ID='.$ID;

    //get data to update change report
    if($table=='wp_rmt_entry_resources'){
      $infosql = "select wp_rmt_entry_resources.*, wp_rmt_resources.token  from wp_rmt_entry_resources"
              . " left outer join wp_rmt_resources on wp_rmt_resources.ID=resource_id"
              . " where wp_rmt_entry_resources.ID=".$ID;
    }elseif($table=='wp_rmt_entry_attributes'){
      $infosql = "select wp_rmt_entry_attributes.*, wp_rmt_entry_att_categories.token from wp_rmt_entry_attributes"
              . " left outer join wp_rmt_entry_att_categories on wp_rmt_entry_att_categories.ID=attribute_id"
              . " where wp_rmt_entry_attributes.ID=".$ID;
    }elseif($table=='wp_rmt_entry_attn'){
      $infosql = "select wp_rmt_entry_attn.*, wp_rmt_attn.value as token from wp_rmt_entry_attn"
              . " left outer join wp_rmt_attn on wp_rmt_attn.ID=attn_id"
              . " where wp_rmt_entry_attn.ID=".$ID;
    }
    $res = $wpdb->get_row($infosql,ARRAY_A);

    switch ($table) {
      case 'wp_rmt_entry_resources':
        $fieldID = $res['resource_id'];
        $type    = 'Resource';
        break;
      case 'wp_rmt_entry_attributes':
        $fieldID = $res['attribute_id'];
        $type    = 'Attribute';
        break;
      case 'wp_rmt_entry_attn':
        $fieldID = $res['attn_id'];
        $type    = 'Attention';
        break;
      default:
        $fieldID = '';
        $type    = '';
        break;
    }
    //add to change report array
    $chgRptRec[]= array(
                'user_id'           => $current_user->ID,
                'lead_id'           => $res['entry_id'],
                'form_id'           => 0,
                'field_id'          => $fieldID,
                'field_before'      => $res[$fieldName],
                'field_after'       => $newValue,
                'fieldLabel'        => 'RMT '.$type.': '.$res['token'].' - '.$fieldName,
                'status_at_update'  => '');
  }

  /* Add all changes and additions done thru wp-admin entry detail to the change report */
  if(!empty($chgRptRec))  updateChangeRPT($chgRptRec);

  $wpdb->get_results($sql);
  if($ID==0)  $ID = $wpdb->insert_id;

  //set lockBit to locked
  if($table=='wp_rmt_entry_resources' || $table == 'wp_rmt_entry_attributes'){
    $sql = "update ".$table.' set lockBit=1 where ID='.$ID;
    $wpdb->get_results($sql);
  }

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

