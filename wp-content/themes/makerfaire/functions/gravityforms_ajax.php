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

    //update change report for new recources/attributes/attention records added thru wp-admin
    $user = $current_user->ID;
    $entryID  = (isset($insertArr['entry_id'])?$insertArr['entry_id']:0);
    $qty      = (isset($insertArr['qty'])?$insertArr['qty']:0);
    $comment  = (isset($insertArr['comment'])?$insertArr['comment']:'');
    $attvalue = (isset($insertArr['value'])?$insertArr['value']:'');
    switch ($table) {
      case 'wp_rmt_entry_resources':
        $fieldID     = $insertArr['resource_id'];
        $res         = $wpdb->get_row('SELECT token FROM `wp_rmt_resources` where ID='.$fieldID);

        $chgRPTins[] = RMTchangeArray($user, $entryID, 0, $fieldID, '', $qty, 'RMT Resource: '.$res->token.' -  qty');
        $chgRPTins[] = RMTchangeArray($user, $entryID, 0, $fieldID, '', $comment, 'RMT Resource: '.$res->token.' - comment');
        break;
      case 'wp_rmt_entry_attributes':
        $attribute_id = $insertArr['attribute_id'];
        $res = $wpdb->get_row('SELECT token FROM `wp_rmt_entry_att_categories` where ID='.$attribute_id);

        $chgRPTins[] = RMTchangeArray($user, $entryID, 0, $attribute_id, '', $attvalue, 'RMT Attribute: '.$res->token.' -  value');
        $chgRPTins[] = RMTchangeArray($user, $entryID, 0, $attribute_id, '', $comment, 'RMT Attribute: '.$res->token.' -  comment');
        break;
      case 'wp_rmt_entry_attn':
        $fieldID = $insertArr['attn_id'];
        $res = $wpdb->get_row('SELECT value as token FROM wp_rmt_attn where ID='.$fieldID);

        $chgRPTins[] = RMTchangeArray($user, $entryID, 0, $fieldID, '', $comment, 'RMT Attention: '.$res->token.' -  comment');
        break;
      default:
        break;
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
    $chgRPTins[] = RMTchangeArray($current_user->ID, $res['entry_id'], 0, $fieldID, $res[$fieldName], $newValue, 'RMT '.$type.': '.$res['token'].' - '.$fieldName);
  }

  /* Add all changes and additions done thru wp-admin entry detail to the change report */
  if(!empty($chgRPTins))  updateChangeRPT($chgRPTins);

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
  //save resource/attribute
  $resAtt = $wpdb->get_row('SELECT * FROM '.$table .' where ID='.$ID);

  $response = array('table'=>$table,'ID'=>$ID);
  if($ID != 0 && $table != ''){
    $sql = "DELETE from ".$table ." where ID =".$ID;
    $wpdb->get_results($sql);
    $response = array('message'=>'Deleted','ID'=>$ID);
  }

  //update change report for deleted recources/attributes/attention records thru wp-admin
  //set who is updating the record
  $current_user = wp_get_current_user();
  $user     = $current_user->ID;
  $entryID  = (isset($_POST['entry_id'])?$_POST['entry_id']:0);
  $chgRPTins = array();

    switch ($table) {
      case 'wp_rmt_entry_resources':
        $fieldID     = $resAtt->resource_id;
        $res         = $wpdb->get_row('SELECT token FROM `wp_rmt_resources` where ID='.$fieldID);

        $chgRPTins[] = RMTchangeArray($user, $entryID, 0, $fieldID, $resAtt->qty,'', 'RMT Resource: '.$res->token.' -  qty');
        $chgRPTins[] = RMTchangeArray($user, $entryID, 0, $fieldID, $resAtt->comment, '', 'RMT Resource: '.$res->token.' - comment');
        break;
      case 'wp_rmt_entry_attributes':
        $attribute_id = $resAtt->attribute_id;
        $res = $wpdb->get_row('SELECT token FROM `wp_rmt_entry_att_categories` where ID='.$attribute_id);

        $chgRPTins[] = RMTchangeArray($user, $entryID, 0, $attribute_id, $resAtt->value,   '', 'RMT Attribute: '.$res->token.' -  value');
        $chgRPTins[] = RMTchangeArray($user, $entryID, 0, $attribute_id, $resAtt->comment, '', 'RMT Attribute: '.$res->token.' -  comment');
        break;
      case 'wp_rmt_entry_attn':
        $fieldID = $resAtt->attn_id;
        $res = $wpdb->get_row('SELECT value as token FROM wp_rmt_attn where ID='.$fieldID);

        $chgRPTins[] = RMTchangeArray($user, $entryID, 0, $fieldID, $resAtt->comment, '', 'RMT Attention: '.$res->token.' -  comment');
        break;
      default:
        break;
    }
  /* Add all changes and additions done thru wp-admin entry detail to the change report */
  if(!empty($chgRPTins))  updateChangeRPT($chgRPTins);
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

