<?php
/* This function is used to update entry resources and entry attributes via AJAX */
function update_entry_resatt() {
  global $wpdb;
  $rowID    = $_POST['ID'];
  $table    = $_POST['table'];

  //default values
  $entryID  = $qty = $resource_id = $attribute_id = $attention_id = 0;
  $comment  = $value = '';

  //set who is updating the record
  $current_user = wp_get_current_user();
  $user     = $current_user->ID;
  if ($rowID == 0) {
    $insertArr    = $_POST['insertArr'];

    //find the field data to add
    $entryID      = (isset($insertArr['entry_id']) ? $insertArr['entry_id'] : 0);
    $qty          = (isset($insertArr['qty']) ? $insertArr['qty'] : 0);
    $comment      = (isset($insertArr['comment']) ? htmlspecialchars($insertArr['comment']) : '');
    $value        = (isset($insertArr['value']) ? htmlspecialchars($insertArr['value']) : '');
    $resource_id  = (isset($insertArr['resource_id']) ? $insertArr['resource_id'] : 0);
    $attribute_id = (isset($insertArr['attribute_id']) ? $insertArr['attribute_id'] : 0);
    $attention_id = (isset($insertArr['attn_id']) ? $insertArr['attn_id'] : 0);

    if ($table == 'wp_rmt_entry_resources') {
      $rowID = GFRMTHELPER::rmt_update_resource($entryID, $resource_id, $qty, $comment);
    } elseif ($table == 'wp_rmt_entry_attributes') {
      $rowID = GFRMTHELPER::rmt_update_attribute($entryID, $attribute_id, $value, $comment);
    } else {
      //update/insert attention
      $rowID = GFRMTHELPER::rmt_update_attention($entryID, $attention_id, $comment);
    }
  } else {
    //find the field data to update    
    $newValue   = $_POST['newValue'];
    $fieldName  = $_POST['fieldName'];
    
    $entry = GFRMTHELPER::rmt_update_field($table, $fieldName, $newValue, $rowID);
  }

  //set lockBit to locked
  if ($table == 'wp_rmt_entry_resources' || $table == 'wp_rmt_entry_attributes') {
    if ($table == 'wp_rmt_entry_resources') {
      $type = 'resource';
    } elseif ($table == 'wp_rmt_entry_attributes') {
      $type = 'attribute';
    }
    $entry = GFRMTHELPER::rmt_set_lock_ind(1, $rowID, $type);
  }
  
  update_entry($entryID, $entry);

  //return the ID
  $response = array('message' => 'Saved', 'ID' => $rowID, 'user' => $current_user->display_name, 'dateupdate' => current_time('m/d/y h:i a'));
  wp_send_json($response);

  // IMPORTANT: don't forget to "exit"
  exit;
}
add_action('wp_ajax_update-entry-resAtt', 'update_entry_resatt');

/* This function is used to delete entry resources and entry attributes via AJAX */
function delete_entry_resatt() {
  $table    = (isset($_POST['table']) ? $_POST['table'] : '');
  $ID       = (isset($_POST['ID'])    ? $_POST['ID'] : 0);
  $entryID  = (isset($_POST['entry_id']) ? $_POST['entry_id'] : 0);

  $response = array('table' => $table, 'ID' => $ID);
  if ($ID != 0 && $table != '' && $entryID != 0) {
    $entry = GFRMTHELPER::rmt_delete($ID, $table, $entryID);
    $response = array('message' => 'Deleted', 'ID' => $ID);
  }
  
  update_entry($entryID, $entry);

  wp_send_json($response);
  // IMPORTANT: don't forget to "exit"
  exit;
}
add_action('wp_ajax_delete-entry-resAtt', 'delete_entry_resatt');

/* This function is used to delete entry resources and entry attributes via AJAX */
function update_lock_resAtt() {
  $ID    = (isset($_POST['ID'])    ? $_POST['ID'] : 0);
  $lock  = (isset($_POST['lock']) && $_POST['lock'] == 0 ? 1 : 0);
  $table = (isset($_POST['table']) ? $_POST['table'] : '');

  //determine type to update
  $type  = '';
  if ($table == 'wp_rmt_entry_resources') {
    $type = 'resource';
  } elseif ($table == 'wp_rmt_entry_attributes') {
    $type = 'attribute';
  }

  $response = array('table' => $table, 'ID' => $ID);

  if ($ID != 0 && $table != '' && $type != '') {
    //set who is updating the record
    $current_user = wp_get_current_user();
    $user     = $current_user->ID;

    $entry = GFRMTHELPER::rmt_set_lock_ind($lock, $ID, $type);
    $response = array('message' => 'Updated', 'ID' => $ID);
    
    $entry_id = $entry['id'];
        
    update_entry($entry_id, $entry);
  }  

  wp_send_json($response);
  // IMPORTANT: don't forget to "exit"
  exit;
}
add_action('wp_ajax_update-lock-resAtt', 'update_lock_resAtt');

/*
 *      Entry Detail  AJAX Section
 * Process sidebar updates via ajax
 * This is where our custom post action handing occurs
 */

add_action('wp_ajax_mf-update-entry', 'mf_admin_MFupdate_entry');
function mf_admin_MFupdate_entry() {
  //Get the current action
  $mfAction = $_POST['mfAction'];
  $response = array('rebuild' => '', 'rebuildHTML' => '');
  //Only process if there was a gravity forms action
  if (!empty($mfAction)) {
    $entry_id       = $_POST['entry_id'];
    $entry           = GFAPI::get_entry($entry_id);
    $original_entry = $entry;
    $form_id        = isset($entry['form_id']) ? $entry['form_id'] : 0;
    $form           = RGFormsModel::get_form_meta($form_id);

    switch ($mfAction) {
        // Entry Management Update      
      case 'update_entry_management':
        upd_flags_prelim_loc($entry, $form);
        break;
      case 'update_entry_status':
        set_entry_status($entry, $form, $entry_id);
        break;
      case 'update_ticket_code':
        $ticket_code  = $_POST['entry_ticket_code'];
        $entry_id     = $entry_id;
        mf_update_entry_field($entry_id, '308', $ticket_code);
        break;
      case 'update_entry_schedule':
        $response['insert_row'] = set_entry_schedule($entry, $form);
        break;
      case 'delete_entry_schedule':
        delete_entry_schedule($entry, $form);
        break;
      case 'change_form_id':
        $response['entryID'] = set_form_id($entry, $form);
        break;
      case 'duplicate_entry_id':
        $response['entryID'] = duplicate_entry_id($entry, $form);
        break;
      case 'send_conf_letter':
        //first update the schedule if one is set
        set_entry_schedule($entry, $form);
        //then send confirmation letter
        $notifications_to_send = GFCommon::get_notifications_to_send('confirmation_letter', $form, $entry);
        foreach ($notifications_to_send as $notification) {
          if ($notification['isActive']) {
            GFCommon::send_notification($notification, $form, $entry);
          }
        }
        mf_add_note($entry_id, 'Confirmation Letter sent');
        break;
        //Sidebar Note Add
      case 'add_note_sidebar':
        add_note_sidebar($entry, $form);
        break;
        //Sidebar Note Delete
      case 'delete_note_sidebar':
        if (is_array($_POST['note'])) {
          delete_note_sidebar($_POST['note']);
        }
        break;
      case 'update_admin': //update admin action updates flags, prelim loc, exhibit type, fee management AND status.
        upd_flags_prelim_loc($entry, $form, 'both');
        set_exhibit_type($entry, $form);
        set_feeMgmt($entry, $form);        
        break;
        
      case 'update_fee_mgmt':
        set_feeMgmt($entry, $form);
        break;
      case 'update_exhibit_type':
        set_exhibit_type($entry, $form);
        break;
      case 'update_final_weekend':
        set_final_weekend($entry, $form);
        break;
      default:
        $response['result'] = 'Error: Invalid Action Passed';
        return;
        break;
    }
    do_action('gform_after_update_entry', $form, $entry_id, $original_entry);

    $response['result'] = 'updated';
  } else {
    $response['result'] = 'Error: No Action Passed';
  }

  //get updated lead
  $entry = GFAPI::get_entry($entry_id);
  //rebuild schedule sidebar to send back
  if ($mfAction == 'update_entry_schedule' || $mfAction == 'delete_entry_schedule') {
    $response['rebuild']     = 'schedBox';
    $response['rebuildHTML'] = display_sched_loc_box($form, $entry);
  } elseif ($mfAction == 'add_note_sidebar') {
    $response['rebuild']     = 'notesbox';
    $response['rebuildHTML'] = display_entry_notes_box($form, $entry);
  }

  wp_send_json($response);
  // IMPORTANT: don't forget to "exit"
  exit;
}

/* Modify Set Entry Status */
function set_entry_status($entry, $form) {
  global $wpdb;
  $entry_id = $entry['id'];
  $acceptance_status_change  = $_POST['entry_info_status_change'];
  $acceptance_current_status = isset($entry['303']) ? $entry['303'] : '';

  $is_acceptance_status_changed = (strcmp($acceptance_current_status, $acceptance_status_change) != 0);

  if (!empty($entry_id)) {
    if (!empty($acceptance_status_change)) {
      //Update Field for Acceptance Status
      mf_update_entry_field($entry_id, '303', $acceptance_status_change);

      //Reload entry to get any changes in status
      $entry['303'] = $acceptance_status_change;

      //Handle acceptance status changes
      if ($is_acceptance_status_changed) {
        if ($acceptance_status_change == 'Accepted' || $acceptance_status_change == 'Interested') {
          //trigger an action
          entry_accepted_cb($entry);

          /*
           * If the status is accepted, trigger a cron job to generate EventBrite Tickets.
           * The cron job will trigger action sidebar_entry_update
           */
          wp_schedule_single_event(time() + 1, 'sidebar_entry_update', array($entry_id));

          //lock attribute 19 and 20 upon setting the status to accepted (original space size and exposure are locked)
          $wpdb->update('wp_rmt_entry_attributes', array('lockBit' => 1), array('attribute_id' => 19, 'entry_id' => $entry['id']), array('%d'), array('%d', '%d'));
          $wpdb->update('wp_rmt_entry_attributes', array('lockBit' => 1), array('attribute_id' => 20, 'entry_id' => $entry['id']), array('%d'), array('%d', '%d'));

          //if the form type is invoice, set the invoice date
          if (isset($form['create_invoice']) && $form['create_invoice'] == 'yes') {
            //get post id
            $fieldData       = get_value_by_label('inv_post_id', $form, $entry);
            $post_id         = (is_array($fieldData) && $fieldData['value'] != '' ? $fieldData['value'] : 0);
            if ($post_id != 0) {
              update_field('invoice_date', date('m/d/Y'), $post_id);
            }
          }
        }

        if ($acceptance_status_change == 'Cancelled') {
          //$wpdb->delete( 'wp_mf_location', array( 'entry_id' => $entry['id'] ) );
        }

        //Create a note of the status change.
        $results = mf_add_note($entry_id, 'EntryID:' . $entry_id . ' status changed to ' . $acceptance_status_change);

        //Handle notifications for acceptance
        $notifications_to_send = GFCommon::get_notifications_to_send('mf_acceptance_status_changed', $form, $entry);
        foreach ($notifications_to_send as $notification) {
          // The isActive paramater is not always set. 
          // If it's not set, assume the notification is turned on
          if (
            !isset($notification['isActive']) ||
            (isset($notification['isActive']) && $notification['isActive'])
          ) {
            GFCommon::send_notification($notification, $form, $entry);
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
function mf_update_entry_field($entry_id, $input_id, $value) {
  global $wpdb;

  $entry = GFAPI::get_entry($entry_id);
  if (is_wp_error($entry)) {
    return $entry;
  }

  $form = GFAPI::get_form($entry['form_id']);
  if (!$form) {
    return false;
  }

  $field = GFFormsModel::get_field($form, $input_id);

  $entry_detail_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}gf_entry_meta WHERE entry_id=%d AND  CAST(meta_key AS CHAR) ='%s' order by id DESC limit 1", $entry_id, $input_id));

  $result = true;
  $result = GFFormsModel::update_lead_field_value($form, $entry, $field, $entry_detail_id, $input_id, $value);

  return $result;
}

/*
 * Add a single note
 */
function mf_add_note($entryid, $notetext) {
  global $current_user;
  $user_data = get_userdata($current_user->ID);
  RGFormsModel::add_note($entryid, $current_user->ID, $user_data->display_name, $notetext);
}

/* Update flags and Preliminary Location */
function upd_flags_prelim_loc($entry, $form, $type = 'both') {
  $entry_id = $entry['id'];

  $location_change          = (isset($_POST['entry_info_location_change']) ? $_POST['entry_info_location_change'] : array());
  $flags_change             = (isset($_POST['entry_info_flags_change']) ? $_POST['entry_info_flags_change'] : array());
  $location_comment_change  = (isset($_POST['entry_location_comment']) ? $_POST['entry_location_comment'] : '');

  //Preliminary Location  
  $field302 = RGFormsModel::get_field($form, '302');
  //Flags  
  $field304 = RGFormsModel::get_field($form, '304');

  if (!empty($entry_id)) {
    /* Determine which fields are being updated */
    if ($type === 'both' || $type === 'flags') {
      //first clear out all choices
      foreach ($field304['inputs'] as $choice) {
        mf_update_entry_field($entry_id, $choice['id'], '');
      }
      //now go in and set the ones that are checked
      if (!empty($flags_change)) {
        foreach ($flags_change as $flags_entry) {
          $exploded_flags_entry = explode("_", $flags_entry);
          $entry_info_entry[$exploded_flags_entry[0]] = $exploded_flags_entry[1];
          mf_update_entry_field($entry_id, $exploded_flags_entry[0], $exploded_flags_entry[1]);
        }
      }
    }

    if ($type === 'both' || $type === 'prelim_loc') {
      //first clear out all choices
      foreach ($field302['inputs'] as $choice) {
        mf_update_entry_field($entry_id, $choice['id'], '');
      }
      //now go in and set the ones that are checked
      if (!empty($location_change)) {
        foreach ($location_change as $location_entry) {
          $exploded_location_entry = explode("_", $location_entry);
          $entry_info_entry[$exploded_location_entry[0]] = $exploded_location_entry[1];
          mf_update_entry_field($entry_id, $exploded_location_entry[0], $exploded_location_entry[1]);
        }
      }
    }

    if ($location_comment_change != '') {
      $entry_info_entry['307'] = $location_comment_change;
      mf_update_entry_field($entry_id, '307', $location_comment_change);
    }
  }
}

/* Modify Form Id Status */
function set_form_id($entry, $form) {
  $entry_id    = $entry['id'];
  $form_change = $_POST['entry_form_change'];
  
  $entry = GFAPI::get_entry($entry_id);

  $is_form_id_changed = (strcmp($entry['form_id'], $form_change) !== 0);

  if (!empty($entry_id)) {
    if (!empty($is_form_id_changed)) {
      //Update Field for Acceptance Status
      $result = update_entry_form_id($entry_id, $form_change);      

      //add note about form change
      $newForm = RGFormsModel::get_form_meta($form_change);
      mf_add_note($entry_id, 'Entry changed from ' . $form['title'] . ' to ' . $newForm['title']);
      $return = 'Entry ' . $entry_id . ' updated to Form ' . $form_change;
    } else {
      $return = 'No change made to entry ' . $entry_id . '. Selected form same as current form (form ID ' . $form_change . ')';
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
function update_entry_form_id($entry_id, $form_id) {
  global $wpdb;

  $entry_table        = 'wp_gf_entry';
  $entry_detail_table = 'wp_gf_entry_meta';
  $entry_meta_table   = 'wp_gf_entry_meta';
  $result     = $wpdb->query(
    $wpdb->prepare("UPDATE $entry_table SET form_id={$form_id} WHERE id=%d ", $entry_id)
  );
  $wpdb->query(
    $wpdb->prepare("UPDATE $entry_detail_table SET form_id={$form_id} WHERE entry_id=%d ", $entry_id)
  );
  $wpdb->query(
    $wpdb->prepare("UPDATE $entry_meta_table SET form_id={$form_id} WHERE entry_id=%d ", $entry_id)
  );

  return $result;
}

/* Copy entry record into specific form*/
function duplicate_entry_id($entry, $form) {
  $form_change = $_POST['entry_form_copy']; //selected form field
  $entry_id    = $entry['id'];

  error_log('$duplicating entry id =' . $entry_id . ' into form ' . $form_change);

  $result     = duplicate_entry_data($form_change, $entry_id);
  error_log('UPDATE RESULTS = ' . print_r($result, true));
  return $result;
}

/**
 * Duplicates the contents of a specified entry id into the specified form
 * Adapted from forms_model.php, RGFormsModel::save_lead($Form, $entry) and
 * gravity -forms-addons.php for the gravity forms addon plugin
 * @param  array $form Form object.
 * @param  array $entry Lead object
 * @return void
 */
function duplicate_entry_data($form_change, $current_entry_id) {
  global $wpdb;
  global $current_user;

  $current_entry=gfapi::get_entry($current_entry_id);  
  $newEntry = array('form_id'=>$form_change,  
    'res_status' => (isset($current_entry['res_status'])?$current_entry['res_status']:''),
    'res_assign' => (isset($current_entry['res_assign'])?$current_entry['res_assign']:''),
    '303'        => 'Proposed'
  );

  //only copy over data for fields that exist in new form
  $newForm = GFAPI::get_form($form_change);
  foreach($newForm['fields'] as $field){            
    if($field->id==303) continue; //skips status
    
    if($field->type=="address" || $field->type=="name"){
      foreach($field->inputs as $input){        
        if(isset($current_entry[$input['id']])){
          $newEntry[$input['id']] = $current_entry[$input['id']];          
        }    
      }
    }elseif($field->type=="checkbox"){
      //get value(s) set on the original entry      
      $field_value   = RGFormsModel::get_lead_field_value($current_entry, $field);

      //we need both the label and the value set for each choice, so we need to combine these two
      $inputs   = (isset($field->inputs) ? $field->inputs : array());
      $choices  = (isset($field->choices) ? $field->choices : '');
      foreach ($choices as $chItem) {              
        //get the current field ID
        $key = array_search($chItem['text'], array_column($inputs, 'label'));
        $input_id = $inputs[$key]['id'];
               
        //is this item set in the current entry (regardless of input id)
        if(in_array($chItem['value'], $field_value)){            
          $newEntry[$input_id] = $chItem['value'];
        }else{
          $newEntry[$input_id] = '';
        }                            
      }
    }elseif($field->type=="fileupload" && $field->multipleFiles){
      //we converted some of our single file uploads to multi file uploads.      
      $file = json_decode($current_entry[$field->id]);      
      
      //if this does not convert to an array, then it is a single file and we need to convert it 
      if(is_array($file)){
        //no need to convert
        $upload = $current_entry[$field->id];
      }else{                
        $upload = json_encode(array($current_entry[$field->id]));        
      }
      
      $newEntry[$field->id] = $upload;      
    }elseif(isset($current_entry[$field->id])){
      //if this field was set in the old entry, copy it to the new entry
      $newEntry[$field->id] = $current_entry[$field->id];      
    }
  }

  $entry_id = GFAPI::add_entry($newEntry);
  
  $return = 'Entry ' . $entry_id . ' created in Form ' . $form_change;

  //if GF easypassthrough is active, we need to reset the token 
  $resetToken = (class_exists('GP_Easy_Passthrough') ? TRUE : FALSE);

  //add a note to the new entry
  mf_add_note($entry_id, 'Copied Entry ID:' . $current_entry_id . ' into form ' . $form_change . '. New Entry ID =' . $entry_id);
  
  return $return;
}

/* Modify Set Entry Schedule */
function set_entry_schedule($entry, $form) {
  $entry_id              = $entry['id'];
  $entry_schedule_start  = (isset($_POST['datetimepickerstart'])   ? $_POST['datetimepickerstart']   : '');
  $entry_schedule_end    = (isset($_POST['datetimepickerend'])     ? $_POST['datetimepickerend']     : '');
  $entry_schedule_end    = (isset($_POST['datetimepickerend'])     ? $_POST['datetimepickerend']     : '');
  $sched_type            = (isset($_POST['sched_type'])            ? $_POST['sched_type']            : '');

  //location fields
  $entry_location_subarea_change = (isset($_POST['entry_location_subarea_change']) ? $_POST['entry_location_subarea_change'] : '');

  $form_id = $entry['form_id'];

  //set the location
  $location_id = 'NULL';
  if ($entry_location_subarea_change != 'none') {
    set_entry_location($entry, $form, $location_id);
  }

  if ($entry_schedule_start != '' && $entry_schedule_end != '') {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_errno) {
      error_log("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
    }
    $insert_query = sprintf("INSERT INTO `wp_mf_schedule` (`entry_id`, location_id, `faire`, `start_dt`, `end_dt`, type)
		SELECT $entry_id,$location_id,wp_mf_faire.faire,'$entry_schedule_start', '$entry_schedule_end', '$sched_type'
		  from wp_mf_faire where find_in_set($form_id,form_ids) > 0");

    //MySqli Insert Query
    $insert_row = $mysqli->query($insert_query);
    if ($insert_row) {
      //echo 'Success! <br />';      
      return $mysqli->insert_id;
    } else {
      error_log('Error :' . $insert_query . ':(' . $mysqli->errno . ') ' . $mysqli->error);
    }
  }
}

/* Modify Set Entry Location */
function set_entry_location($entry, $form, &$location_id = '') {
  $entry_schedule_change      = $_POST['entry_location_subarea_change'];
  $entry_info_entry_id        = $entry['id'];
  $update_entry_location_code = $_POST['update_entry_location_code'];

  //$form_id=$entry['form_id'];

  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    error_log("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
  }

  $insert_query = "INSERT INTO `wp_mf_location`(`entry_id`, `subarea_id`, `location`, `location_element_id`) "
    . " VALUES ($entry_info_entry_id,$entry_schedule_change,'$update_entry_location_code',3)";
  //MySqli Insert Query
  $insert_row = $mysqli->query($insert_query);
  if ($insert_row) {
    //echo 'Success! <br />';
  } else {
    error_log('Error :' . $insert_query . ':(' . $mysqli->errno . ') ' . $mysqli->error);
  }

  setLocChgRpt($entry_schedule_change, $update_entry_location_code, $entry, 'add');
  $location_id = $mysqli->insert_id;
}

/* Delete entry schedule */
function delete_entry_schedule($entry, $form) {
  global $wpdb;

  $delete_entry_schedule = (isset($_POST['delete_schedule_id']) ? implode(',', ($_POST['delete_schedule_id']))    : '');
  $delete_entry_location = (isset($_POST['delete_location_id']) ? implode(',', ($_POST['delete_location_id'])) : '');

  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    error_log("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
  }

  //delete schedule and location
  if (!empty($delete_entry_schedule)) {
    //delete from schedule and location table
    $delete_query =  "DELETE `wp_mf_schedule`, `wp_mf_location`
                        FROM `wp_mf_schedule`, `wp_mf_location`
                       WHERE wp_mf_schedule.ID IN ($delete_entry_schedule) and location_id=wp_mf_location.id";
    $wpdb->get_results($delete_query);
  }

  //delete location only
  if (!empty($delete_entry_location)) {
    //update change report
    $location = $wpdb->get_row("SELECT subarea_id, location FROM wp_mf_location where wp_mf_location.ID IN ($delete_entry_location)");
    setLocChgRpt($location->subarea_id, $location->location, $entry, 'delete');

    //delete from schedule and location table
    $delete_query =  "DELETE FROM `wp_mf_location` WHERE wp_mf_location.ID IN ($delete_entry_location)";
    $wpdb->get_results($delete_query);
  }
}

function delete_note_sidebar($notes) {
  RGFormsModel::delete_notes($notes);
}

function add_note_sidebar($entry, $form) {
  global $current_user;

  $user_data = get_userdata($current_user->ID);
  $project_name = $entry['151'];
  $email_to     = $_POST['gentry_email_notes_to_sidebar'];
  if (is_array($email_to)) {
    $email_to = implode(', ', $email_to);
  }
  $email_note_info = '';

  //emailing notes if configured
  if (!empty($email_to)) {
    GFCommon::log_debug('GFEntryDetail::lead_detail_page(): Preparing to email entry notes.');

    //$email_to      = $_POST['gentry_email_notes_to_sidebar'];    
    $email_from    = $current_user->user_email;
    $email_subject = stripslashes('Entry Note: ' . $project_name . ' ' . $entry['id']);
    $entry_url = get_bloginfo('wpurl') . '/review/index.php?layout=list&search=' . rgar($entry, 'id');

    $body = stripslashes($_POST['new_note_sidebar']) . '<br /><br />' .
      'Please reply in entry:<a href="' . $entry_url . '">' . $entry_url . '</a>';
    $headers = "From: \"$email_from\" <$email_from> \r\n";

    //Enable HTML Email Formatting in the body
    add_filter('wp_mail_content_type', 'wpse27856_set_content_type');

    $result  = wp_mail($email_to, $email_subject, $body, $headers);

    //Remove HTML Email Formatting
    remove_filter('wp_mail_content_type', 'wpse27856_set_content_type');
    $email_note_info = '<br /><br />:SENT TO:[' . $email_to . ']';
  }

  mf_add_note($entry['id'],  nl2br(stripslashes($_POST['new_note_sidebar'] . $email_note_info)));
}

function set_feeMgmt($entry, $form) {
  $entry_id         = $entry['id'];
  $fee_mgmt_change  = $_POST['entry_info_fee_mgmt'];

  $field442 = RGFormsModel::get_field($form, '442');

  if (!empty($entry_id)) {
    /* Clear out old choices */
    foreach ($field442['inputs'] as $choice) {
      mf_update_entry_field($entry_id, $choice['id'], '');
    }

    /* Save entries */
    if (!empty($fee_mgmt_change)) {
      foreach ($fee_mgmt_change as $fee_mgmt) {
        $exploded_fee_mgmt = explode("_", $fee_mgmt);
        $entry_info_entry[$exploded_fee_mgmt[0]] = $exploded_fee_mgmt[1];
        mf_update_entry_field($entry_id, $exploded_fee_mgmt[0], $exploded_fee_mgmt[1]);
      }
    }
  }
}

//Update Exhibit Type
function set_exhibit_type($entry, $form) {
  $entry_id             = $entry['id'];
  $exhibit_type_change  = $_POST['entry_exhibit_type'];

  $field339 = RGFormsModel::get_field($form, '339');

  if (!empty($entry_id)) {
    /* Clear out old choices */
    foreach ($field339['inputs'] as $choice) {
      mf_update_entry_field($entry_id, $choice['id'], '');
    }

    /* Save entries */
    if (!empty($exhibit_type_change)) {
      foreach ($exhibit_type_change as $exhibit_type) {
        $exploded_exhibit_type = explode("_", $exhibit_type);
        $entry_info_entry[$exploded_exhibit_type[0]] = $exploded_exhibit_type[1];
        mf_update_entry_field($entry_id, $exploded_exhibit_type[0], $exploded_exhibit_type[1]);
      }
    }
  }
}

//Update Final Weekend
function set_final_weekend($entry, $form) {
  $entry_id             = $entry['id'];
  $final_weekend_change  = $_POST['entry_final_weekend'];

  $field879 = RGFormsModel::get_field($form, '879');

  if (!empty($entry_id)) {
    /* Clear out old choices */
    foreach ($field879['inputs'] as $choice) {
      mf_update_entry_field($entry_id, $choice['id'], '');
    }

    /* Save entries */
    if (!empty($final_weekend_change)) {
      foreach ($final_weekend_change as $final_weekend) {
        $exploded_final_weekend = explode("_", $final_weekend);
        $entry_info_entry[$exploded_final_weekend[0]] = $exploded_final_weekend[1];
        mf_update_entry_field($entry_id, $exploded_final_weekend[0], $exploded_final_weekend[1]);
      }
    }
  }
}

function setLocChgRpt($subarea, $locCode = '', $entry = array(), $type = 'add') {
  global $wpdb;
  $sql = "SELECT area, subarea "
    . "FROM `wp_mf_faire_subarea` "
    . "left outer join wp_mf_faire_area on wp_mf_faire_area.id = wp_mf_faire_subarea.area_id  "
    . "WHERE wp_mf_faire_subarea.`ID` = " . $subarea;
  $field = $wpdb->get_row($sql);
  $subarea = $field->subarea;
  $area = $field->area;

  if ($type == 'add') {
    $before = '';
    $after = $area . ' - ' . $subarea . ' (' . $locCode . ')';
  } else {
    $after = '';
    $before = $area . ' - ' . $subarea . ' (' . $locCode . ')';
  }

  global $current_user;
  $chgRptArr = array(
    array(
      'user_id'           => $current_user->ID,
      'lead_id'           => $entry['id'],
      'form_id'           => $entry['form_id'],
      'field_id'          => 0,
      'field_before'      => $before,
      'field_after'       => $after,
      'fieldLabel'        => 'Location ' . $type,
      'status_at_update'  => ''
    )
  );
  updateChangeRPT($chgRptArr);
}

function update_entry($entryID, $entry = array()) {  
  //check if $entryID is set
  if($entryID==0){
    //can we get entryID from the entry object?
    if(isset($entry['id'])){
      $entryID = $entry['id'];
    }else{
      //unable to process
      //write to the error log, we have an issue
      error_log('Missing Entry ID in RMT update_entry');
      return;
    }
  }

  //check if the entry object is set
  if (empty($entry)) {
    $entry = GFAPI::get_entry($entryID);              
  } 

  $form = GFAPI::get_form($entry['form_id']);              

  if (!empty($form) && $entryID != 0) {
    do_action('gform_after_update_entry', $form, $entryID);
  }
}

/* Update Existing Showcases */
add_action('wp_ajax_add-to-showcase', 'mf_admin_addToShowcase');
function mf_admin_addToShowcase() {
  $response = array();
  $parentID  = (isset($_POST['parentID'])?$_POST['parentID']:'');
  $childIDs  = (isset($_POST['childIDs'])?$_POST['childIDs']:'');
  $formID      = (isset($_POST['formID'])?$_POST['formID']:'');
  if($formID==''){
    $response['result'] = 'Error: Form ID not passed';    
  }elseif($parentID==''){
    //throw error
    $response['result'] = 'Error: Showcase ID not passed';    
  }else{    
    $childArr = explode(",",trim($childIDs));
    
    if($childIDs == '' || empty($childArr)){
      $response['result'] = 'Error: No Entries passed to add';    
    }else{
      foreach($childArr as $child){
        if($child!=''){
          $sql = "INSERT INTO `wp_mf_lead_rel`(`parentID`, `childID`, `form`) VALUES ($parentID,$child,$formID) on duplicate key UPDATE id=id";          
          global $wpdb;
          $wpdb->query($sql);
        }
      }
      $response['result'] = 'updated';
    }
  }
  
  // Make your array as json
	wp_send_json($response);
 
  // Don't forget to stop execution afterward.
  wp_die();
}