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
    if($table=='wp_rmt_entry_resources'){
      $sql = "insert into ".$table.' ('.implode(',',$fields).',user) VALUES ("'.implode('","',$values).'",'.$current_user->ID.')'
              . ' on duplicate key update qty='.$insertArr['qty'].',comment="'.htmlspecialchars($insertArr['comment']).'",user="'.$current_user->ID.'"';
    }elseif($table=='wp_rmt_entry_attributes'){
      $sql = "insert into ".$table.' ('.implode(',',$fields).',user) VALUES ("'.implode('","',$values).'",'.$current_user->ID.')'
              . ' on duplicate key update value="'.htmlspecialchars($insertArr['value']).'",comment="'.htmlspecialchars($insertArr['comment']).'",user="'.$current_user->ID.'"';
    }else{
      $sql = "insert into ".$table.' ('.implode(',',$fields).',user) VALUES ("'.implode('","',$values).'",'.$current_user->ID.')';
    }

    //update change report for new recources/attributes/attention records added thru wp-admin
    $user = $current_user->ID;
    $entryID  = (isset($insertArr['entry_id'])?$insertArr['entry_id']:0);
    $qty      = (isset($insertArr['qty'])?$insertArr['qty']:0);
    $comment  = (isset($insertArr['comment'])?htmlspecialchars($insertArr['comment']):'');
    $attvalue = (isset($insertArr['value'])?htmlspecialchars($insertArr['value']):'');
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
      $infosql = "select wp_rmt_entry_resources.*, wp_rmt_resources.token,
                    (select category 
                       from wp_rmt_resource_categories 
                      where wp_rmt_resource_categories.ID = wp_rmt_resources.resource_category_id) as resource_type  
                  from wp_rmt_entry_resources"
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
    if($fieldName==='resource_id'){
        //set value before and value after to the resource type name        
        $newValue     = $wpdb->get_var('SELECT token FROM `wp_rmt_resources` WHERE `ID` = '.$newValue);
        $field_before = $res['token'];
        //field label should be the resource item
        $fieldLabel = 'RMT Resource: '.$res['resource_type'];
    }else{
        $fieldLabel = 'RMT '.$type.': '.$res['token'].' - '.$fieldName;
        $field_before = $res[$fieldName];
    }
    //add to change report array  
    $chgRPTins[] = RMTchangeArray($current_user->ID, $res['entry_id'], 0, $fieldID, $field_before, $newValue, $fieldLabel);
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

/*
 *      Entry Detail  AJAX Section
 * Process sidebar updates via ajax
 * This is where our custom post action handing occurs
 */

add_action( 'wp_ajax_mf-update-entry', 'mf_admin_MFupdate_entry' );
function mf_admin_MFupdate_entry(){
  //Get the current action
  $mfAction = $_POST['mfAction'];
  $response = array('rebuild'=>'', 'rebuildHTML'=>'');
  //Only process if there was a gravity forms action
  if (!empty($mfAction)){
    $entry_id     = $_POST['entry_id'];
    $lead         = GFAPI::get_entry( $entry_id );
    $form_id      = isset($lead['form_id']) ? $lead['form_id'] : 0;
    $form         = RGFormsModel::get_form_meta($form_id);

    switch ($mfAction ) {
      // Entry Management Update
      case 'update_entry_management' :
        set_entry_status_content($lead,$form);
        break;
      case 'update_entry_status' :
        set_entry_status($lead,$form,$entry_id);
        break;
      case 'update_ticket_code' :
        $ticket_code  = $_POST['entry_ticket_code'];
        $entry_id     = $entry_id;
        mf_update_entry_field($entry_id,'308',$ticket_code);
        break;
      case 'update_entry_schedule' :
        set_entry_schedule($lead,$form);
        break;
      case 'delete_entry_schedule' :
        delete_entry_schedule($lead,$form);
        break;
      case 'change_form_id' :
        $response['entryID'] = set_form_id($lead,$form);
        break;
      case 'duplicate_entry_id' :
        $response['entryID'] = duplicate_entry_id($lead,$form);
        break;
      case 'send_conf_letter' :
        //first update the schedule if one is set
        set_entry_schedule($lead,$form);
        //then send confirmation letter
        $notifications_to_send = GFCommon::get_notifications_to_send( 'confirmation_letter', $form, $lead );
        foreach ( $notifications_to_send as $notification ) {
          if($notification['isActive']){
            GFCommon::send_notification( $notification, $form, $lead );
          }
        }
        mf_add_note( $entry_id, 'Confirmation Letter sent');
        break;
      //Sidebar Note Add
      case 'add_note_sidebar' :
        add_note_sidebar($lead, $form);
        break;
      //Sidebar Note Delete
      case 'delete_note_sidebar' :
        if(is_array($_POST['note'])){
          delete_note_sidebar($_POST['note']);
        }
        break;
      case 'update_fee_mgmt' :
        set_feeMgmt($lead,$form);
        break;
      default:
        $response['result'] = 'Error: Invalid Action Passed';
        break;
    }

    //update the change report with any changes
    GVupdate_changeRpt($form,$entry_id,$lead);
    $response['result'] = 'updated';
  } else{
    $response['result'] = 'Error: No Action Passed';
  }

  //get updated lead
  $lead = GFAPI::get_entry( $entry_id );
  //rebuild schedule sidebar to send back
  if($mfAction == 'update_entry_schedule' || $mfAction == 'delete_entry_schedule') {
    $response['rebuild']     = 'schedBox';
    $response['rebuildHTML'] = display_sched_loc_box($form, $lead);
  }elseif($mfAction=='add_note_sidebar'){
    $response['rebuild']     = 'notesbox';
    $response['rebuildHTML'] = display_entry_notes_box($form, $lead);
  }

  processTasks( $lead, $form);
  wp_send_json( $response );
  // IMPORTANT: don't forget to "exit"
  exit;
}

/* Modify Set Entry Status */
function set_entry_status($lead,$form){
  global $wpdb;
  $entry_id = $lead['id'];
	$acceptance_status_change  = $_POST['entry_info_status_change'];
  $acceptance_current_status = isset($lead['303']) ? $lead['303'] : '';

	$is_acceptance_status_changed = (strcmp($acceptance_current_status, $acceptance_status_change) != 0);

	if (!empty($entry_id)){
		if (!empty($acceptance_status_change)){
      //Update Field for Acceptance Status
      mf_update_entry_field($entry_id,'303',$acceptance_status_change);

      //Reload entry to get any changes in status
      $lead['303'] = $acceptance_status_change;

			//Handle acceptance status changes
			if ($is_acceptance_status_changed ){
        if($acceptance_status_change == 'Accepted' || $acceptance_status_change == 'Interested'){
          //trigger an action
          do_action('entry-accepted', $lead);

          /*
           * If the status is accepted, trigger a cron job to generate EventBrite Tickets.
           * The cron job will trigger action sidebar_entry_update
           */
          wp_schedule_single_event(time() + 1,'sidebar_entry_update', array($entry_id));

          //lock attribute 19 and 20 upon setting the status to accepted (original space size and exposure are locked)
          $wpdb->update('wp_rmt_entry_attributes',array('lockBit'=>1),array('attribute_id'=>19,'entry_id'=>$lead['id']),array('%d'),array('%d','%d'));
          $wpdb->update('wp_rmt_entry_attributes',array('lockBit'=>1),array('attribute_id'=>20,'entry_id'=>$lead['id']),array('%d'),array('%d','%d'));

          //if the form type is invoice, set the invoice date
          if(isset($form['create_invoice']) && $form['create_invoice']=='yes'){
            //get post id
            $fieldData       = get_value_by_label('inv_post_id', $form, $lead);
            $post_id         = (is_array($fieldData) && $fieldData['value']!= '' ? $fieldData['value']:0);
            if($post_id!=0){
              update_field('invoice_date', date('m/d/Y'), $post_id);
            }
          }
        }

        if($acceptance_status_change == 'Cancelled'){
          //$wpdb->delete( 'wp_mf_location', array( 'entry_id' => $lead['id'] ) );
        }

				//Create a note of the status change.
				$results = mf_add_note($entry_id, 'EntryID:'.$entry_id.' status changed to '.$acceptance_status_change);

				//Handle notifications for acceptance
				$notifications_to_send = GFCommon::get_notifications_to_send( 'mf_acceptance_status_changed', $form, $lead );
        foreach ( $notifications_to_send as $notification ) {          
          // The isActive paramater is not always set. 
          // If it's not set, assume the notification is turned on
          $active = TRUE;
          if(!isset($notification['isActive']) || 
            (isset($notification['isActive']) && $notification['isActive'])){
              GFCommon::send_notification( $notification, $form, $lead );
          }
				}

        //update maker table information
        GFRMTHELPER::updateMakerTables($entry_id);
			}
		}
	}
}

/**
 * Updates a single field of an entry.
 *
 * @since  1.9
 * @access public
 * @static
 *
 * @param int    $entry_id The ID of the Entry object
 * @param string $input_id The id of the input to be updated. For single input fields such as text, paragraph, website, drop down etc... this will be the same as the field ID.
 *                         For multi input fields such as name, address, checkboxes, etc... the input id will be in the format {FIELD_ID}.{INPUT NUMBER}. ( i.e. "1.3" )
 *                         The $input_id can be obtained by inspecting the key for the specified field in the $entry object.
 *
 * @param mixed  $value    The value to which the field should be set
 *
 * @return bool Whether the entry property was updated successfully
 */
 function mf_update_entry_field( $entry_id, $input_id, $value ) {
	global $wpdb;

	$entry = GFAPI::get_entry( $entry_id );
	if ( is_wp_error( $entry ) ) {
		return $entry;
	}

	$form = GFAPI::get_form( $entry['form_id'] );
	if ( ! $form ) {
		return false;
	}

	$field = GFFormsModel::get_field( $form, $input_id );

	$lead_detail_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}gf_entry_meta WHERE entry_id=%d AND  CAST(meta_key AS CHAR) ='%s' order by id DESC limit 1", $entry_id, $input_id ) );

	$result = true;
  $result = GFFormsModel::update_lead_field_value( $form, $entry, $field, $lead_detail_id, $input_id, $value );

	return $result;
}

/*
 * Add a single note
 */
function mf_add_note($leadid,$notetext){
	global $current_user;
	$user_data = get_userdata( $current_user->ID );
	RGFormsModel::add_note( $leadid, $current_user->ID, $user_data->display_name, $notetext );
}

/* Modify Set Entry Status */
function set_entry_status_content($lead,$form){
  $entry_id = $lead['id'];
	$location_change          = (isset($_POST['entry_info_location_change'])?$_POST['entry_info_location_change']:'');
	$flags_change             = (isset($_POST['entry_info_flags_change'])?$_POST['entry_info_flags_change']:'');
	$location_comment_change  = (isset($_POST['entry_location_comment'])?$_POST['entry_location_comment']:'');

	$field302 = RGFormsModel::get_field($form,'302');
	$field304 = RGFormsModel::get_field($form,'304');

	if (!empty($entry_id)){
		/* Clear out old choices */
		foreach(   $field304['inputs'] as $choice){
			mf_update_entry_field($entry_id,$choice['id'],'');
		}
		foreach(   $field302['inputs'] as $choice){
			mf_update_entry_field($entry_id,$choice['id'],'');
		}
		/* Save entries */
		if (!empty($location_change)){
			foreach($location_change as $location_entry){
				$exploded_location_entry=explode("_",$location_entry);
				$entry_info_entry[$exploded_location_entry[0]] = $exploded_location_entry[1];
				mf_update_entry_field($entry_id,$exploded_location_entry[0],$exploded_location_entry[1]);
			}
		}
		if (!empty($flags_change)){
			foreach($flags_change as $flags_entry){
				$exploded_flags_entry=explode("_",$flags_entry);
				$entry_info_entry[$exploded_flags_entry[0]] = $exploded_flags_entry[1];
				mf_update_entry_field($entry_id,$exploded_flags_entry[0],$exploded_flags_entry[1]);
			}
		}

		$entry_info_entry['307'] = $location_comment_change;
    mf_update_entry_field($entry_id,'307',$location_comment_change);

	}
}

/* Modify Form Id Status */
function set_form_id($lead,$form){
  $entry_id    = $lead['id'];
	$form_change = $_POST['entry_form_change'];

	error_log('$form_change='.$form_change);
	error_log('$entry_id='.$entry_id);
	$entry=GFAPI:: get_entry($entry_id);

  $is_form_id_changed = (strcmp($entry['form_id'], $form_change) !== 0);

	if (!empty($entry_id)){
		if (!empty($is_form_id_changed)){
			//Update Field for Acceptance Status
			$result = update_entry_form_id($entry_id,$form_change);
			error_log('UPDATE RESULTS = '.print_r($result,true));

      //add note about form change
      $newForm = RGFormsModel::get_form_meta($form_change);
      mf_add_note( $entry_id, 'Entry changed from '.$form['title'].' to '.$newForm['title']);
      $return = 'Entry '.$entry_id.' updated to Form '.$form_change;
		}else{
      $return = 'No change made to entry '.$entry_id.'. Selected form same as current form (form ID '.$form_change.')';
    }
	}
  return $return;
}

/**
 * Updates a form id of an entry.
 *
 * @param int    $entry_id The ID of the Entry object
 * @param int    $form_id The Form ID of the Entry object
  *
 * @param mixed  $value    The value to which the field should be set
 *
 * @return bool Whether the entry property was updated successfully
 */
 function update_entry_form_id( $entry_id, $form_id ) {
	global $wpdb;

	$lead_table        = 'wp_gf_entry';
	$lead_detail_table = 'wp_gf_entry_meta';
	$lead_meta_table   = 'wp_gf_entry_meta';
	$result     = $wpdb->query(
			$wpdb->prepare( "UPDATE $lead_table SET form_id={$form_id} WHERE id=%d ", $entry_id)
	);
	$wpdb->query(
		$wpdb->prepare( "UPDATE $lead_detail_table SET form_id={$form_id} WHERE entry_id=%d ", $entry_id)
	);
	$wpdb->query(
			$wpdb->prepare( "UPDATE $lead_meta_table SET form_id={$form_id} WHERE entry_id=%d ", $entry_id)
	);

	return $result;
}

/* Copy entry record into specific form*/
function duplicate_entry_id($lead,$form){
  $form_change = $_POST['entry_form_copy']; //selected form field
  $entry_id    = $lead['id'];

  error_log('$duplicating entry id ='.$entry_id.' into form '.$form_change);

  $result     = duplicate_entry_data($form_change,$entry_id);
  error_log('UPDATE RESULTS = '.print_r($result,true));
  return $result;
}

/**
 * Duplicates the contents of a specified entry id into the specified form
 * Adapted from forms_model.php, RGFormsModel::save_lead($Form, $lead) and
 * gravity -forms-addons.php for the gravity forms addon plugin
 * @param  array $form Form object.
 * @param  array $lead Lead object
 * @return void
 */
function duplicate_entry_data($form_change,$current_entry_id ){
  global $wpdb;
  global $current_user;

  $lead_table        = 'wp_gf_entry';
  $lead_detail_table = 'wp_gf_entry_meta';
  $lead_meta_table   = 'wp_gf_entry_meta';

  //pull existing entries information
  $current_lead   = $wpdb->get_results($wpdb->prepare("SELECT * FROM $lead_table WHERE id=%d", $current_entry_id));
  $current_fields = $wpdb->get_results($wpdb->prepare("SELECT wp_gf_entry_meta.meta_key, wp_gf_entry_meta.meta_value "
                                                    . "  FROM $lead_detail_table "
                                                    . " WHERE entry_id=%d", $current_entry_id));

  // new lead
  $user_id    = $current_user && $current_user->ID ? $current_user->ID : 'NULL';
  $user_agent = GFCommon::truncate_url($_SERVER["HTTP_USER_AGENT"], 250);
  $currency   = GFCommon::get_currency();
  $source_url = GFCommon::truncate_url(RGFormsModel::get_current_page_url(), 200);
  $wpdb->query($wpdb->prepare("INSERT INTO $lead_table(form_id, ip, source_url, date_created, user_agent, currency, created_by) VALUES(%d, %s, %s, utc_timestamp(), %s, %s, {$user_id})", $form_change, RGFormsModel::get_ip(), $source_url, $user_agent, $currency));
  $lead_id    = $wpdb->insert_id;
  $return = 'Entry '.$lead_id.' created in Form '.$form_change;

  //add a note to the new entry
  $results=mf_add_note( $lead_id, 'Copied Entry ID:'.$current_entry_id.' into form '.$form_change.'. New Entry ID ='.$lead_id);

  foreach($current_fields as $row){
    $fieldValue = ($row->meta_key != '303'? $row->meta_value: 'Proposed');

    $wpdb->query($wpdb->prepare("INSERT INTO $lead_detail_table(entry_id, form_id, meta_key, meta_value) VALUES(%d, %s, %s, %s)",
            $lead_id, $form_change, $row->meta_key, $fieldValue));
  }

  //update/insert into maker tables
  GFRMTHELPER::updateMakerTables($lead_id);

  //copy resources and attributes
  $wpdb->get_results("INSERT INTO `wp_rmt_entry_resources` (`entry_id`, `resource_id`, `qty`, `comment`, `user`, `update_stamp`, `lockBit`)
          select '$lead_id', `resource_id`, `qty`, `comment`, '$user_id', now(),0 from wp_rmt_entry_resources where entry_id = $current_entry_id");

  $wpdb->get_results("INSERT INTO `wp_rmt_entry_attributes` (`entry_id`, `attribute_id`, `value`, `comment`, `user`, `update_stamp`, `lockBit`)
          select '$lead_id', `attribute_id`, `value`, `comment`,  '$user_id', now(), 0 from wp_rmt_entry_attributes where entry_id = $current_entry_id");

  return $return;
}

/* Modify Set Entry Schedule */
function set_entry_schedule($lead,$form){
   $entry_id              = $lead['id'];
	$entry_schedule_start  = (isset($_POST['datetimepickerstart'])   ? $_POST['datetimepickerstart']   : '');
	$entry_schedule_end    = (isset($_POST['datetimepickerend'])     ? $_POST['datetimepickerend']     : '');
   $entry_schedule_end    = (isset($_POST['datetimepickerend'])     ? $_POST['datetimepickerend']     : '');
   $sched_type            = (isset($_POST['sched_type'])            ? $_POST['sched_type']            : '');

   //location fields
   $entry_location_subarea_change = (isset($_POST['entry_location_subarea_change']) ? $_POST['entry_location_subarea_change'] : '');

	$form_id=$lead['form_id'];

	//set the location
	$location_id = 'NULL';
	if($entry_location_subarea_change!='none'){
		set_entry_location($lead,$form,$location_id);
	}

	if($entry_schedule_start!='' && $entry_schedule_end!=''){
		$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_errno) {
			error_log("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		}
		$insert_query = sprintf("INSERT INTO `wp_mf_schedule` (`entry_id`, location_id, `faire`, `start_dt`, `end_dt`, type)
		SELECT $entry_id,$location_id,wp_mf_faire.faire,'$entry_schedule_start', '$entry_schedule_end', '$sched_type'
		  from wp_mf_faire where find_in_set($form_id,form_ids) > 0");

		//MySqli Insert Query
		$insert_row = $mysqli->query($insert_query);
		if($insert_row){
			//echo 'Success! <br />';
		}else{
			error_log('Error :'.$insert_query.':('. $mysqli->errno .') '. $mysqli->error);
		}
   }
}

/* Modify Set Entry Location */
function set_entry_location($lead,$form,&$location_id=''){
	$entry_schedule_change      = $_POST['entry_location_subarea_change'];
	$entry_info_entry_id        = $lead['id'];
	$update_entry_location_code = $_POST['update_entry_location_code'];

	//$form_id=$lead['form_id'];

	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		error_log("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
	}

  $insert_query = "INSERT INTO `wp_mf_location`(`entry_id`, `subarea_id`, `location`, `location_element_id`) "
                . " VALUES ($entry_info_entry_id,$entry_schedule_change,'$update_entry_location_code',3)";
	//MySqli Insert Query
	$insert_row = $mysqli->query($insert_query);
	if($insert_row){
		//echo 'Success! <br />';
	}else{
		error_log('Error :'.$insert_query.':('. $mysqli->errno .') '. $mysqli->error);
	}

  setLocChgRpt($entry_schedule_change, $update_entry_location_code, $lead, 'add');
  $location_id = $mysqli->insert_id;
}

/* Delete entry schedule */
function delete_entry_schedule($lead,$form){
  global $wpdb;

	$delete_entry_schedule = (isset($_POST['delete_schedule_id']) ? implode(',',($_POST['delete_schedule_id']))    : '');
  $delete_entry_location = (isset($_POST['delete_location_id']) ? implode(',',($_POST['delete_location_id'])) : '');

	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		error_log("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
	}

  //delete schedule and location
  if (!empty($delete_entry_schedule)){
    //delete from schedule and location table
    $delete_query =  "DELETE `wp_mf_schedule`, `wp_mf_location`
                        FROM `wp_mf_schedule`, `wp_mf_location`
                       WHERE wp_mf_schedule.ID IN ($delete_entry_schedule) and location_id=wp_mf_location.id";
    $wpdb->get_results($delete_query);
  }

  //delete location only
	if (!empty($delete_entry_location)){
    //update change report
    $location = $wpdb->get_row( "SELECT subarea_id, location FROM wp_mf_location where wp_mf_location.ID IN ($delete_entry_location)" );
    setLocChgRpt($location->subarea_id, $location->location, $lead, 'delete');

    //delete from schedule and location table
    $delete_query =  "DELETE FROM `wp_mf_location` WHERE wp_mf_location.ID IN ($delete_entry_location)";
    $wpdb->get_results($delete_query);
  }
}

function delete_note_sidebar($notes){
    RGFormsModel::delete_notes( $notes);
}

function add_note_sidebar($lead, $form){
	global $current_user;

	$user_data = get_userdata( $current_user->ID );
	$project_name = $lead['151'];
	$email_to     = $_POST['gentry_email_notes_to_sidebar'];
   
	$email_note_info = '';

	//emailing notes if configured
	if ( !empty($email_to) ) {
		GFCommon::log_debug( 'GFEntryDetail::lead_detail_page(): Preparing to email entry notes.' );
		//$email_to      = $_POST['gentry_email_notes_to_sidebar'];
		$email_from    = $current_user->user_email;
		$email_subject = stripslashes( 'Response Required Maker Application: '.$lead['id'].' '.$project_name);
		$entry_url = get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=gf_entries&view=entry&id=' . $form['id'] . '&lid=' . rgar( $lead, 'id' );
		$body = stripslashes( $_POST['new_note_sidebar'] ). '<br /><br />Please reply in entry:<a href="'.$entry_url.'">'.$entry_url.'</a>';
		$headers = "From: \"$email_from\" <$email_from> \r\n";
		//Enable HTML Email Formatting in the body
		add_filter( 'wp_mail_content_type','wpse27856_set_content_type' );
		$result  = wp_mail( $email_to, $email_subject, $body, $headers );
		//Remove HTML Email Formatting
		remove_filter( 'wp_mail_content_type','wpse27856_set_content_type' );
		$email_note_info = '<br /><br />:SENT TO:['.implode(",", $email_to).']';
	}

	mf_add_note( $lead['id'],  nl2br(stripslashes($_POST['new_note_sidebar'].$email_note_info)));

}

function set_feeMgmt($lead,$form){
  $entry_id         = $lead['id'];
	$fee_mgmt_change  = $_POST['entry_info_fee_mgmt'];

	$field442 = RGFormsModel::get_field($form,'442');

	if (!empty($entry_id)){
		/* Clear out old choices */
		foreach($field442['inputs'] as $choice){
			mf_update_entry_field($entry_id,$choice['id'],'');
		}

		/* Save entries */
		if (!empty($fee_mgmt_change)){
			foreach($fee_mgmt_change as $fee_mgmt){
				$exploded_fee_mgmt = explode("_",$fee_mgmt);
				$entry_info_entry[$exploded_fee_mgmt[0]] = $exploded_fee_mgmt[1];
				mf_update_entry_field($entry_id,$exploded_fee_mgmt[0],$exploded_fee_mgmt[1]);
			}
		}
	}
}

function setLocChgRpt($subarea, $locCode='', $entry, $type='add'){
  global $wpdb;
  $sql = "SELECT area, subarea "
            . "FROM `wp_mf_faire_subarea` "
            . "left outer join wp_mf_faire_area on wp_mf_faire_area.id = wp_mf_faire_subarea.area_id  "
            . "WHERE wp_mf_faire_subarea.`ID` = ".$subarea;
  $field = $wpdb->get_row($sql);
  $subarea = $field->subarea;
  $area = $field->area;

  if($type=='add'){
    $before = '';
    $after = $area.' - '.$subarea.' ('.$locCode.')';
  }else{
    $after = '';
    $before = $area.' - '.$subarea.' ('.$locCode.')';
  }

    global $current_user;
    $chgRptArr = array(
      array('user_id'           => $current_user->ID,
            'lead_id'           => $entry['id'],
            'form_id'           => $entry['form_id'],
            'field_id'          => 0,
            'field_before'      => $before,
            'field_after'       => $after,
            'fieldLabel'        => 'Location '.$type,
            'status_at_update'  => ''));
    updateChangeRPT($chgRptArr);
  }