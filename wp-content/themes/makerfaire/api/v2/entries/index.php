<?php

/**
 * v2 of the Maker Faire API - MAP
 *
 * This is the call for the API to handle map data for faires.
 *
 * This page specifically handles the Faire map location data.
 *
 * @version 2.0
 *
 * Read from location_elements
 */

// Stop any direct calls to this file
defined('ABSPATH') or die('This file cannot be called directly!');

$type = (!empty($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : null);
$form = (!empty($_REQUEST['form']) ? sanitize_text_field($_REQUEST['form']) : false);


// Double check again we have requested this file
if ($type == 'entries') {
  $data = getAllEntries($form);

  // Output the JSON
  echo json_encode($data);

  exit;
}

function getAllEntries($formID = '', $page = '', $years = '') {
  $return = array();

  $search_criteria = array('status' => 'active');
  $sorting         = array();
  $paging          = array('offset' => 0, 'page_size' => 999);
  $total_count     = 0;
  $entries         = GFAPI::get_entries($formID, $search_criteria, $sorting, $paging, $total_count);
  $form            = GFAPI::get_form($formID);

  //convert this into a usable array
  $field_array = array();
  foreach ($form['fields'] as $field) {
    $field_array[$field['id']] = $field;
  }

  $data = array();

  //pull admin review layout file
  $file = file_get_contents(ABSPATH . "/review/templates/admin_review.txt");

  //build the output layout
  preg_match_all("/\[tab\]\s*(.[\S\s]*?)\s*\[\/tab\]/", $file, $tabs);
  $tabArr = array();
  foreach ($tabs[1] as $key => $tab) {
    //find the title
    preg_match_all("/\[title\]\s*(.[\S\s]*?)\s*\[\/title\]/", $tab, $title_array);
    $title = (!empty($title_array[1][0]) ? $title_array[1][0] : 'tab-' . $key);
    $tab_name = strtolower(str_replace(' ', '-', $title));

    //build the tab content
    preg_match_all("/\[tab_content\]\s*(.[\S\s]*?)\s*\[\/tab_content\]/", $tab, $tab_content_arr);
    $tab_content = array();
    //there should only be 1 tab content per tab
    if ($tab_content_arr) {
      $blocks = retrieve_blocks($tab_content_arr[1][0]);
      if (!empty($blocks)) {
        $tab_content['initial']['blocks'] = $blocks;
      }
    }

    //build the expand section
    preg_match_all("/\[expand\]\s*(.[\S\s]*?)\s*\[\/expand\]/", $tab, $expand_content_arr);

    $expand_return = array();
    //there should only be 1 tab content per tab
    if ($expand_content_arr && isset($expand_content_arr[1][0])) {
      $blocks = retrieve_blocks($expand_content_arr[1][0]);
      if (!empty($blocks)) {
        $tab_content['expand']['blocks'] = $blocks;
      }
    }

    $tabArr[$tab_name] = array(
      'title'       => $title,
      'tab_content' => $tab_content
    );
  }

  foreach ($entries as $entry) {
    $tabData = array();

    foreach ($tabArr as $tabkey => $tab) {
      //default the tabData            
      $tabData[$tabkey] = $tab;

      $tabContent = (array) $tab['tab_content'];
      //loop through tab content - initial and expand section(if set)
      foreach ($tabContent as $dataKey => $dataType) {
        $blockData = array();
        $blocks = (array) $dataType['blocks'];

        //loop through blocks            
        foreach ($blocks as $blockKey => $block) {
          $columnData = array();
          $columns = (array) $block['columns'];

          //loop through columns
          foreach ($columns as $columnKey => $column) {
            $fieldData = array();

            //loop through fields
            foreach ($column as $field_id) {
              $arg = '';
              //check if an argument was passed
              if (strpos($field_id, ':') !== false) {
                $arg = str_replace(':', '', strstr($field_id, ':'));
                $field_id = substr($field_id, 0, strpos($field_id, ":"));
              }
              $fieldOutput = fieldOutput($field_id, $entry, $field_array, $form, $arg);
              if (!empty($fieldOutput))
                $fieldData['field-' . $field_id] = $fieldOutput;
            }
            if (!empty($fieldData))
              $columnData[$columnKey] = $fieldData; //write field data to columns
          }
          if (!empty($columnData))
            $blockData[$blockKey] = array('columns' => $columnData); //write column data to blocks
        }
        if (!empty($blockData)) {
          $tabData[$tabkey]['tab_content'][$dataKey] = array('blocks' => $blockData); //write block data to tabs
        } else {
          //since there is no data in the initial or expanded section, remove it
          unset($tabData[$tabkey]['tab_content'][$dataKey]);
        }
      }
      //if there is no data in the tab, remove it
      if (empty($tabData[$tabkey]['tab_content']))
        unset($tabData[$tabkey]);
    }

    //if group, use the group name, else use the main contact name
    if (strpos($entry['105'], 'group')  !== false) {
      $maker_name = (isset($entry['109']) ? $entry['109'] : '');
    } else {
      $maker_name = (isset($entry['96.3']) ? $entry['96.3'] : '') .
        ' ' .
        (isset($entry['96.6']) ? $entry['96.6'] : '');
    }

    //for BA24, the single photo was changed to a multi image which messed things up a bit
    $maker_photo = $entry['22'];

    $photo = json_decode($entry['22']);
    if (is_array($photo)) {
      $maker_photo = $photo[0];
    }

    //put exhibit type in a comma separated array
    $fieldArr = fieldOutput(339, $entry, $field_array, $form);
    $exhibit_types = (isset($fieldArr['value']) && $fieldArr['value'] != '' ? implode(", ", $fieldArr['value']) : '');

    //flags
    $fieldArr = fieldOutput(304, $entry, $field_array, $form);
    $flags    = (isset($fieldArr['value']) && $fieldArr['value'] != '' ? implode(", ", $fieldArr['value']) : '');

    //prelimLoc
    $fieldArr = fieldOutput(302, $entry, $field_array, $form);
    $prelim_loc    = (isset($fieldArr['value']) && $fieldArr['value'] != '' ? implode(", ", $fieldArr['value']) : '');

    $return['makers'][] = array(
      'tabs'          => $tabData,
      'project_name'  => $entry['151'],
      'project_id'    => $entry['id'],
      'status'        => $entry['303'],
      'description'   => $entry['16'],
      'flags'         => $flags,
      'entry_type'    => $exhibit_types,
      'photo'         => $maker_photo,
      'maker_name'    => $maker_name,
      'prelim_loc'    => $prelim_loc,
      'prime_cat'     => html_entity_decode(get_CPT_name($entry['320']))
    );
  }

  return $return;
}

function retrieve_blocks($content = '') {
  if (empty($content)) {
    return 'no content submitted';
  }

  $return = array();
  //find all blocks
  preg_match_all("/\[block\]\s*(.[\S\s]*?)\s*\[\/block\]/", $content, $blocks);
  foreach ($blocks[1] as $block) {
    $columnArr = array();
    preg_match_all("/\[column\]\s*(.[\S\s]*?)\s*\[\/column\]/", $block, $columns);
    foreach ($columns[1] as $column) {
      $fieldData = array();

      //find fields   
      preg_match_all("/\[field\]\s*(.[\S\s]*?)\s*\[\/field\]/", (string)$column, $fields);

      //loop through fields
      foreach ($fields[1] as $field_id) {
        $fieldData[] = $field_id;
      }
      $columnArr[] = $fieldData; //write field data to columns        
    }
    if (!empty($columnArr)) {
      $return[]['columns'] = $columnArr;
    }
  }

  return $return;
}

function fieldOutput($fieldID, $entry, $field_array, $form, $arg = '') {
  //set default values
  $label = $fieldID;

  $value = (isset($entry[$fieldID]) ? $entry[$fieldID] : '');
  $type  = 'text';

  //find this field in the form object                
  if (isset($field_array[$fieldID])) {
    $field_data = $field_array[$fieldID];
    $label = ($field_data['adminLabel'] != '' ? $field_data['adminLabel'] : $field_data['label']);
    $type = $field_data['type'];
    switch ($field_data['type']) {
      case 'website':
        if ($fieldID == 32) {
          $type = 'video';
        }
      case 'fileupload':
        if ($field_data['multipleFiles']) {
          $type = 'multipleFiles';
          $value = json_decode($value);

          //if the array is empty, set this back to blank
          if (empty($value))   $value = '';
        }
        $value = str_replace('http://', 'https://', $value);
        break;

      case 'address':
        $value = array();

        foreach ($field_data['inputs'] as $input) {
          if (isset($entry[$input['id']]) && $entry[$input['id']] != '') {
            $input = array('label' => $input['label'], 'value' => $entry[$input['id']]);
            $value[] = $input;
          }
        }

        //if the array is empty, set this back to blank
        if (empty($value))   $value = '';
        break;
      case 'name':
        $fnameID = $fieldID . '.3';
        $lnameID = $fieldID . '.6';
        $value = $entry[$fnameID] . ' ' . $entry[$lnameID];
        break;
      case 'checkbox':
        $value = array();
        foreach ($field_data['inputs'] as $input) {
          if (isset($entry[$input['id']]) && $entry[$input['id']] != '') {
            //field 321 is stored as category number, use cross reference to find text value
            if ($fieldID == '321') {
              $input = html_entity_decode(get_CPT_name($entry[$input['id']]));
            } else {
              $input = $entry[$input['id']];
            }
            $value[] = $input;
          }
        }

        //if the array is empty, set this back to blank
        if (empty($value))   $value = '';
        break;
      case 'list':
        $list = unserialize($value);
        if (is_array($list)) {
          foreach ($list as $list_key => $item) {
            $list[$list_key]["Your Link"] = ($item["Your Link"] != '' ? '<a href="' . $item["Your Link"] . '" target="_blank">' . $item["Your Link"] . '</a>' : '');
          }
          $value = $list;
        }


        break;
      default:
        if (isset($entry[$fieldID])) {
          //field 320 and 321 is stored as category number, use cross reference to find text value
          if ($fieldID == '320') {
            $value = html_entity_decode(get_CPT_name($entry[$fieldID]));
          } else {
            $value = $entry[$fieldID];
          }
        }
        break;
    }
  } else {
    //$entry['rmt'] = 
    switch ($fieldID) {
      case 'rmt':
        $type  = 'html';
        $label = 'Assigned Resources';
        //$value = entryResources($entry, TRUE);        
        break;
      case 'notes':
        $type  = 'notes';
        $label = '';
        $value = GFAPI::get_notes(array('entry_id' => $entry['id'], 'note_type' => 'user'), array('key' => 'id', 'direction' => 'DESC'));
        if ($value == '') $value = '&nbsp;';
        break;
      case 'notes_table':
        $type  = 'html';
        $label = 'Notes';
        $value = '<p>Enter Email: <input type="email" placeholder="example@make.co" id="toEmail' . $entry['id'] . '" size="40" /></p>' .
          ' <textarea	id="new_note_' . $entry['id'] . '"	style="width: 90%; height: 240px;" cols=""	rows=""></textarea>' .
          ' <input type="button" value="Add Note" class="button updButton" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'add_note_sidebar\',\'' . $entry['id'] . '\');"/>' .
          ' <span class="updMsg" id="add_noteMSG_' . $entry['id'] . '"></span>';
        break;
      case 'flags':
        $type = 'html';
        $label = 'Flags';

        //flags        
        $value     = field_display($entry, $form, '304', 'entry_flags_' . $entry['id']);

        break;
      case 'prelim_loc':
        $type = 'html';
        $label = 'Preliminary Location';

        //preliminary locations        
        $value     = field_display($entry, $form, '302', 'entry_prelim_loc_' . $entry['id']);
        $value    .= '<textarea id="location_comment_' . $entry['id'] . '">' . (isset($entry['307']) ? $entry['307'] : '') . '</textarea>';
        break;
      case 'exhibit_type':
        $type = 'html';
        $label = 'Entry Type';
        $value     = field_display($entry, $form, '339', 'admin_exhibit_type_' . $entry['id']);

        break;
      case 'edit_status':
        $type = 'html';
        $label = 'Status';
        $field303 = RGFormsModel::get_field($form, '303');
        $value = '    <select id="entryStatus_' . $entry['id'] . '" name="entry_info_status_change">';
        if (isset($field303['choices'])) {
          foreach ($field303['choices'] as $choice) {
            $selected = '';
            if ($entry[$field303['id']] == $choice['text']) $selected = ' selected ';
            $value .= '<option ' . $selected . ' value="' . $choice['text'] . '">' . $choice['text'] . '</option>';
          }
        }
        break;
      case 'fee_mgmt':
        $type = 'html';
        $label = 'Fee Management';
        $value     = field_display($entry, $form, '442', 'info_fee_mgmt_' . $entry['id']);
        break;
      case 'schedule_loc':
        //$type = 'html';
        //$label = 'Schedule/Location';
        //$value = mf_sidebar_entry_schedule( $form['id'], $entry );
        break;
      case 'update_admin_button':
        $type  = 'html';
        $label = '';
        $value = '<p><input type="button" id="updAdmin' . $entry['id'] . '" value="Update Admin" class="button updButton" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_admin\', \'' . $entry['id'] . '\');"/></p>
                  <p><span class="updMsg" id="updAdminMSG' . $entry['id'] . '"></span></p>';
        break;
      case 'final_location':
        $type  = 'html';
        $label = 'Final Location';
        //$value= 'final location is'.display_schedule($form['id'],$entry,$section='sidebar');
        break;
      case 'rmt':
        $type  = 'listRepeat';
        $label = 'Resources';
        break;
      case 'date_created':
        $type  = 'text';
        $date  = date_create($entry[$fieldID]);
        $value = date_format($date, "m/d/Y");
        $label = 'Submitted On';
        break;
      case 'other_entries':
        $type = 'html';
        $label = '';
        $value = '';
        $value = getAddEntries($entry[98], $entry['id']);
        break;
      case 'public_entry_page':
        $type   = 'html';
        $label  = '';
        $value  = '<a href="/maker/entry/' . $entry['id'] . '" target="_none">Public Entry Page</a>';
        break;
      case 'notifications_sent':
        $type = 'notes';
        $label = 'Notifications Sent';
        $value = GFAPI::get_notes(array('entry_id' => $entry['id'], 'note_type' => 'notification'), array('key' => 'id', 'direction' => 'DESC'));

        break;
      case 'send_notifications':
       // $type = 'html';
        //$label = 'Send Notifications';
        //$value = get_form_notifications($form, $entry['id']);
        break;
    }
  }
  if ($arg == 'no_label')  $label = '';
  if ($value == '')  return array();
  return array('label' => $label, 'type' => $type, 'value' => $value);
}

function getAddEntries($email, $currEntryID) {
  global $wpdb;

  $addEntriesCnt = 0;
  //additional Entries
  $addEntries = '<table width="100%">
  <thead>
    <tr>      
      <th>Record ID</th>
      <th>Project Name</th>
      <th>Form Name</th>
      <th>Date Submitted</th>      
      <th>Status</th>
    </tr>
  </thead>';

  $addEntriesCnt = 0;
  $sql = 'SELECT  distinct(entry_id), form_id, ' .
    ' (SELECT meta_value FROM wp_gf_entry_meta detail2 WHERE detail2.entry_id = wp_gf_entry_meta.entry_id AND meta_key = 151 ) as projectName, ' .
    ' (SELECT meta_value FROM wp_gf_entry_meta detail2 WHERE detail2.entry_id = wp_gf_entry_meta.entry_id AND meta_key = 303 ) as status, ' .
    ' (SELECT status FROM wp_gf_entry WHERE wp_gf_entry.id = wp_gf_entry_meta.entry_id) as lead_status, ' .
    ' (SELECT date_created FROM wp_gf_entry WHERE wp_gf_entry.id = wp_gf_entry_meta.entry_id) as date_created ' .
    'FROM wp_gf_entry_meta ' .
    'JOIN wp_gf_form on wp_gf_form.id = wp_gf_entry_meta.form_id ' .
    'WHERE meta_value = "' . $email . '" ' .
    'AND entry_id != ' . $currEntryID . ' ' .
    'AND is_trash != 1 ' .
    'ORDER BY entry_id DESC';

  $results = $wpdb->get_results($sql);
  $exclude_type = array('Attendee', 'Invoice', 'Default');

  foreach ($results as $addData) {
    $form = GFAPI::get_form($addData->form_id);

    //exclude certain form types
    if (isset($form['form_type']) && !in_array($form['form_type'], $exclude_type)) {
      $outputURL = admin_url('admin.php') . "?page=gf_entries&view=entry&id=" . $addData->form_id . '&lid=' . $addData->entry_id;
      $addEntriesCnt++;
      $addEntries .= '<tr>';
      $date=date_create($addData->date_created);
      
      $addEntries .= '<td><a target="_blank" href="' . $outputURL . '">' . $addData->entry_id . '</a></td>'
        . '<td>' . $addData->projectName . '</td>'
        . '<td>' . $form['title'] . '</td>'
        . '<td>' . date_format($date,"m-d-Y") . '</td>'
        . '<td>' . ($addData->lead_status == 'active' ? $addData->status : ucwords($addData->lead_status)) . '</td>'
        . '</tr>';
    }
  }

  $addEntries .= '</table>';

  //don't return an empty table 
  if ($addEntriesCnt == 0) $addEntries = '';
  return $addEntries;
}

//create a dropdown list of available notifications for this form
function get_form_notifications($form, $entryID) {
  $form_id = $form['id'];
  $return  =
    '<div id="sendNotifications'.$entryID.'>'.
      '<div class="message" style="display:none;"></div>' . 
      '<input type="hidden" id="gfnonce_'.$entryID.'" value="'.wp_create_nonce( 'gf_resend_notifications' ).'" />';
  $notifications = GFCommon::get_notifications('resend_notifications', $form);

  if (!is_array($notifications) || count($form['notifications']) <= 0) {
    $return .= '<p class="description">' . esc_html_e('You cannot resend notifications for this entry because this form does not currently have any notifications configured.', 'gravityforms') . '</p>';
    $return .= '<a href="' . admin_url("admin.php?page=gf_edit_forms&view=settings&subview=notification&id={$form_id}") . '" class="button">' . esc_html_e('Configure Notifications', 'gravityforms') . '</a>';
  } else {
    $return  .= '<select id="gform_notifications_'.$entryID.'">';
    foreach ($notifications as $notification) {
      $return .= '<option class="gform_notifications" value="' . esc_attr($notification['id']) . '" id="notification_' . esc_attr($notification['id']) . '" onclick="toggleNotificationOverride();">'.esc_html($notification['name']) . '</option>';      
    }
    $return  .= '</select>';

    $return .= '<div id="notifications_override_settings" style="display:none;">
                  <p class="description" style="padding-top:0; margin-top:0; width:99%;">You may override the default notification settings
                    by entering a comma delimited list of emails to which the selected notifications should be sent.</p>
                  <label for="notification_override_email">' . esc_html__('Send To', 'gravityforms') . ' ' . '</label>
                  <input type="text" name="notification_override_email" id="notification_override_email_'.$entryID.'" style="width:99%;" />
                  <br /><br />
                </div>';

    $return .=  '<input type="button" name="notification_resend" value="' . esc_attr('Resend', 'gravityforms') . '" class="button" style="" onclick="ResendNotifications('.$entryID.','.$form_id.');" />' .
                '<span id="please_wait_container" style="display:none; margin-left: 5px;">' .
                ' <i class="gficon-gravityforms-spinner-icon gficon-spin"></i> ' . esc_html__('Resending...', 'gravityforms') .
                '</span>';
  }
  $return .= '</div>';

  return $return;
}
