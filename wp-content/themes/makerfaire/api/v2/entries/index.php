<?php
/**
 * v2 of the Maker Faire API
 *    This API returns entry data by form
 * @version 2.0
 */

// Stop any direct calls to this file
defined('ABSPATH') or die('This file cannot be called directly!');

$type   = (!empty($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : null);
$formID = (!empty($_REQUEST['form']) ? sanitize_text_field($_REQUEST['form']) : false);

// Double check again we have requested this file
if ($type == 'entries' && $formID) {
  //get the current users capabilities
  $user = wp_get_current_user();
  $user_cap = $user->allcaps;

  //set the users edit capabilities  
  $edit_fee_mgmt        = (isset($user_cap['edit_fee_mgmt']) && $user_cap['edit_fee_mgmt'] ? true : false);
  $edit_entry_type      = (isset($user_cap['edit_entry_type']) && $user_cap['edit_entry_type'] ? true : false);
  $edit_prelim_loc      = (isset($user_cap['edit_prelim_loc']) && $user_cap['edit_prelim_loc'] ? true : false);
  $edit_flags           = (isset($user_cap['edit_flags']) && $user_cap['edit_flags'] ? true : false);
  $edit_status          = (isset($user_cap['edit_status']) && $user_cap['edit_status'] ? true : false);
  $notes_view           = (isset($user_cap['notes_view']) && $user_cap['notes_view'] ? true : false);
  $notes_send           = (isset($user_cap['notes_send']) && $user_cap['notes_send'] ? true : false);
  $notifications_resend = (isset($user_cap['notifications_resend']) && $user_cap['notifications_resend'] ? true : false);
  $view_notifications   = (isset($user_cap['view_notifications']) && $user_cap['view_notifications'] ? true : false);
  $view_rmt             = (isset($user_cap['view_rmt']) && $user_cap['view_rmt'] ? true : false);

  //set global data
  $form     = GFAPI::get_form($formID);
  $field303 = RGFormsModel::get_field($form, '303');
  $all_rmt  = GFRMTHELPER::rmt_table_data(); //used to build drop downs for resources, attributes and attention fields

  //build an array of available area/subarea combinations
  $locSql = "SELECT concat(area.area,' - ', subarea.subarea) as text, subarea.id as value
                FROM wp_mf_faire faire, wp_mf_faire_area area, wp_mf_faire_subarea subarea
                where FIND_IN_SET($formID,faire.form_ids) and faire.ID = area.faire_id and subarea.area_id = area.ID
                order by area,subarea";
  $locResults = $wpdb->get_results($locSql,ARRAY_A);

  //set entry data
  $data     = getAllEntries($formID);

  //RMT values for adding new resources, attributes, and attention items - this will be used by Vue  
  $data['rmt'] = array(
    'res_items'       => $all_rmt['resource_categories'],
    'res_types'       => $all_rmt['resources'],
    'att_items'       => $all_rmt['attItems'],
    'attn_items'      => $all_rmt['attnItems']
  );
  $data['locations'] = $locResults;

  // Output the JSON
  echo json_encode($data);

  exit;
}

function getAllEntries($formID = '') {
  $return = array();
  global $form;
  global $entry_notes;

  //get all active entries in this form
  $search_criteria = array('status' => 'active');
  $sorting         = array();
  $paging          = array('offset' => 0, 'page_size' => 999);
  $total_count     = 0;
  //adds roughly 1 second to the call
  $entries         = GFAPI::get_entries($formID, $search_criteria, $sorting, $paging, $total_count);

  //convert the form fields into a usable array
  $field_array = array();
  foreach ($form['fields'] as $field) {
    //combine choices and input options for ease of use later
    if ($field['type'] == 'checkbox') {
      //field with combined choices and inputs
      $mergedChoicesAndInputs = $choicesArray = array();
      //we need both the label and the value set for each choice, so we need to combine these two
      $inputs   = (isset($field->inputs) ? $field->inputs : '');
      $choices  = (isset($field->choices) ? $field->choices : '');
     
      foreach ($choices as $chItem) {
        foreach ($inputs as $inItem) {
          if (in_array($chItem["text"], $inItem)) {
            $chItem["id"] = $inItem['id'];
            $mergedChoicesAndInputs[] = $chItem;
          }
        }
      }
      //sort flags alphabetically                
      if ($field['id'] === "304") {
        usort($mergedChoicesAndInputs, "sortFlagsByLabel");
      }
      $field['mergedOptions'] = $mergedChoicesAndInputs;
    }

    $field_array[$field['id']] = $field;
  }

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
    //pull notifications only once per entry    
    $entry_notes = GFAPI::get_notes(array('entry_id' => $entry['id']), array('key' => 'id', 'direction' => 'DESC'));

    //modify the data
    //if group, use the group name, else use the main contact name
    if (strpos($entry['105'], 'group')  !== false) {
      $maker_name = (isset($entry['109']) ? $entry['109'] : '');
    } else {
      $maker_name = (isset($entry['96.3']) ? $entry['96.3'] : '') . ' ' . (isset($entry['96.6']) ? $entry['96.6'] : '');
    }

    //for BA24, the single photo was changed to a multi image which messed things up a bit
    $maker_photo = $entry['22']; //??? but this is the project photo??

    $photo = json_decode($entry['22']);
    if (is_array($photo)) {
      $maker_photo = $photo[0];
    }

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

    //build an array of categories with the prime category first
    $categoryArr = fieldOutput(321, $entry, $field_array, $form);
    array_unshift($categoryArr, html_entity_decode(get_CPT_name($entry['320'])));
    $categories = (isset($categoryArr['value']) && $categoryArr['value'] != '' ? implode(", ", $categoryArr['value']) : '');

    //flags
    $fieldArr = fieldOutput(304, $entry, $field_array, $form);
    $flags    = (isset($fieldArr['value']) && $fieldArr['value'] != '' ? implode(", ", $fieldArr['value']) : '');

    //put exhibit type in a comma separated array
    $fieldArr = fieldOutput(339, $entry, $field_array, $form);
    $exhibit_types = (isset($fieldArr['value']) && $fieldArr['value'] != '' ? implode(", ", $fieldArr['value']) : '');

    //prelimLoc
    $fieldArr = fieldOutput(302, $entry, $field_array, $form);
    $prelim_loc    = (isset($fieldArr['value']) && $fieldArr['value'] != '' ? implode(", ", $fieldArr['value']) : '');

    //set entry_placed indicator    
    $placed_meta    = gform_get_meta( $entry['id'], 'expofp_placed');
    $entry_placed   = ($placed_meta=='Placed'?$placed_meta:"0");    

    //set the return data
    $return['makers'][] = array(
      'tabs'            => $tabData,
      'project_name'    => $entry['151'],
      'project_id'      => $entry['id'],
      'status'          => $entry['303'],
      'description'     => $entry['16'],
      'flags'           => $flags,
      'entry_type'      => $exhibit_types,
      'entry_placed'    => $entry_placed,
      'photo'           => $maker_photo,
      'maker_name'      => $maker_name,
      'email'           => $entry['98'],
      'prelim_loc'      => $prelim_loc,
      'categories'      => $categories
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
  $label = $type = $value = $class = '';

  //set default values
  $label = $fieldID;

  $value = (isset($entry[$fieldID]) ? $entry[$fieldID] : '');
  $type  = 'text';
  $class = '';

  //find this field in the form object                
  if (isset($field_array[$fieldID])) {
    $field_data = $field_array[$fieldID];
    $label      = ($field_data['adminLabel'] != '' ? $field_data['adminLabel'] : $field_data['label']);
    $type       = $field_data['type'];

    switch ($field_data['type']) {
      case 'website':
        if ($fieldID == 32) {
          $type = 'video';
        }
        break;
      case 'fileupload':
        if ($field_data['multipleFiles']) {
          $type = 'multipleFiles';
          $value = json_decode($value);

          //if the array is empty, set this back to blank
          if (empty($value))   $value = '';
        }

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

    //default these items
    $label = $value = '';
    switch ($fieldID) {
      case 'update_admin_button':
        $type  = 'html';

        //only display the update admin button if the user can actually edit something
        global $edit_flags;
        global $edit_prelim_loc;
        global $edit_entry_type;
        global $edit_fee_mgmt;

        if ($edit_flags || $edit_prelim_loc || $edit_entry_type || $edit_fee_mgmt) {
          $value = '<p><input type="button" id="updAdmin' . $entry['id'] . '" value="Update Admin" class="button updButton" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_admin\', \'' . $entry['id'] . '\');"/></p>
                      <p><span class="updMsg" id="updAdminMSG' . $entry['id'] . '"></span></p>';
        }
        break;
      case 'date_created':
        //type defaults to 'text'
        $date  = date_create($entry[$fieldID]);
        $value = date_format($date, "m/d/Y");
        $label = 'Submitted On';
        break;
      case 'public_entry_page':
        $type  = 'html';
        $label = '';
        $value = '<a href="/maker/entry/' . $entry['id'] . '" target="_none">Public Entry Page</a>';
        break;

      case 'edit_public_info':
        $type = 'html';
        $value = '<a href="/maker/entry/' . $entry['id'] . '/edit/" target="_none">Edit Public Info</a>';
        break;
      case 'edit_status':
        $type  = 'html';
        $label = 'Status';
        global $field303;
        global $edit_status;

        if ($edit_status) {
          $value = '    <select id="entryStatus_' . $entry['id'] . '" name="entry_info_status_change" style="margin-bottom:10px;margin-right:10px">';
          if (isset($field303['choices'])) {
            foreach ($field303['choices'] as $choice) {
              $selected = '';
              if ($entry[$field303['id']] == $choice['text']) $selected = ' selected ';
              $value .= '<option ' . $selected . ' value="' . $choice['text'] . '">' . $choice['text'] . '</option>';
            }
          }

          $value .= '<p><input type="button" id="updStatus' . $entry['id'] . '" value="Change Status" class="button" style="width:auto;" onclick="updateMgmt(\'update_entry_status\', \'' . $entry['id'] . '\');"/></p>' .
            '<p><span class="updMsg" id="updStatusMSG' . $entry['id'] . '"></span></p>';
        } else {
          $value = $entry[$field303['id']];
        }

        break;

      case 'showcase_info':
        $type = 'html';
        $label = 'Showcase Information';

        $showcase_info = get_showcase_entries($entry['id']);

        //is this a showcase or part of one?
        if (isset($showcase_info['type'])) {
          $showcase = $showcase_info['type'];
          if ($showcase == 'parent') {
            $value = 'Entries that are part of this showcase:</br>';
            foreach ($showcase_info['child_data'] as $parent) {
              $value .= '<a href="/maker/entry/' . $parent['child_entryID'] . '" class="entry-box">' . $parent['child_title'] . '</h3></a><br/>';
            }
          } elseif ($showcase == 'child') {
            $parent = $showcase_info['parent_data'];
            $value = 'Part of Showcase <a href="/maker/entry/' . $parent['parent_id'] . '" target="none">' . $parent['parent_title'] . '</a>';
          }
        }
        break;


      //notes
      case 'notes_table':
        global $notes_send;
        if ($notes_send) {
          $type  = 'html';
          $label = 'Notes';
          $value = '<p>Enter Email: <input type="email" placeholder="example@make.co" id="toEmail' . $entry['id'] . '" size="40" /></p>' .
            ' <textarea	id="new_note_' . $entry['id'] . '"	style="width: 90%; height: 240px;" cols=""	rows=""></textarea>' .
            ' <input type="button" value="Add Note" class="button updButton" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'add_note_sidebar\',\'' . $entry['id'] . '\');"/>' .
            ' <span class="updMsg" id="add_noteMSG_' . $entry['id'] . '"></span>';
        }
        break;

      case 'notes':
        global $notes_view;
        global $entry_notes;

        //can this user view user notes?
        if ($notes_view && is_array($entry_notes)) {
          $type  = 'notes';
          $value = array();
          foreach ($entry_notes as $note) {
            if ($note->note_type == 'user') {
              $value[] = $note;
            }
          }

          if (empty($value)) $value = '&nbsp;';
        }

        break;
      case 'notifications_sent':
        global $view_notifications;
        global $entry_notes;

        //can this user view notifications?
        if ($view_notifications && is_array($entry_notes)) {
          $type  = 'notes';
          $label = 'Notifications Sent';
          $value = array();
          foreach ($entry_notes as $note) {
            if ($note->note_type == 'notification') {
              $value[] = $note;
            }
          }
        }
        break;
      case 'send_notifications':
        global $notifications_resend;
        if ($notifications_resend) {
          $type  = 'html';
          $label = 'Send Notifications';
          $value = get_form_notifications($form, $entry['id']);
        }

        break;

        //checkbox fields for edit
      case 'flags':
        global $edit_flags;
        $edit_cap  = ($edit_flags ? 'edit' : 'view');

        $label     = 'Flags';
        $type      = 'html';

        $fieldName = 'entry_flags_' . $entry['id'];
        $field     = (isset($field_array['304']) ? $field_array['304'] : '');
        $value     = get_checkbox_value($field, $entry, $fieldName, $edit_cap);
        break;

      case 'prelim_loc':
        global $edit_prelim_loc;
        $edit_cap  = ($edit_prelim_loc ? 'edit' : 'view');

        $label     = 'Preliminary Location';
        $type      = 'html';

        $fieldName = 'entry_prelim_loc_' . $entry['id'];
        $field     = (isset($field_array['302']) ? $field_array['302'] : '');
        $value     = get_checkbox_value($field, $entry, $fieldName, $edit_cap);

        if ($edit_prelim_loc) {
          $value .= '<textarea id="location_comment_' . $entry['id'] . '">' . (isset($entry['307']) ? $entry['307'] : '') . '</textarea>';
        }

        break;
      case 'exhibit_type':
        global $edit_entry_type;
        $edit_cap  = ($edit_entry_type ? 'edit' : 'view');

        $label     = 'Entry Type';
        $type      = 'html';

        $fieldName = 'admin_exhibit_type_' . $entry['id'];
        $field     = (isset($field_array['339']) ? $field_array['339'] : '');
        $value     = get_checkbox_value($field, $entry, $fieldName, $edit_cap);

        break;
      case 'fee_mgmt':
        global $edit_fee_mgmt;
        $edit_cap = ($edit_fee_mgmt ? 'edit' : 'view');

        $label     = 'Fee Management';
        $type      = 'html';

        $fieldName = 'info_fee_mgmt_' . $entry['id'];
        $field     = (isset($field_array['442']) ? $field_array['442'] : '');
        $value     = get_checkbox_value($field, $entry, $fieldName, $edit_cap);

        break;
      case 'final_location':
        $type  = 'html';
        $label = 'Final Location';        
        $value = display_schedule($form['id'], $entry, 'summary');
        break;
      
      //these slow down the api        
      case 'other_entries': //adds 2.5 seconds
        $type  = 'html';

        $value = getAddEntries($entry[98], $entry['id']);
        break;

      case 'rmt': //adds 2.5 seconds
        global $view_rmt;

        if ($view_rmt) {
          $type  = 'html';
          $value   = '<div id="rmt' . $entry['id'] . '">' . entryResources($entry) . '</div>';
        }
        break;
                              
      case 'schedule_loc':      
        $type = 'schedule';
        $value = mf_get_schedule_only($entry['id']);        
        
        break;
    }
  }
  if ($arg == 'no_label')  $label = '';
  if ($value == '')  return array();
  return array('label' => $label, 'type' => $type, 'value' => $value, 'class' => $class);
}

function getAddEntries($email, $currEntryID) {
  global $wpdb;

  $sql = 'SELECT distinct(entry_id), wp_gf_entry_meta.form_id, wp_gf_form.title as form_title, ' .
    '(SELECT meta_value FROM wp_gf_entry_meta detail2 WHERE detail2.entry_id = wp_gf_entry_meta.entry_id AND meta_key = "151" ) as projectName, ' .
    '(SELECT meta_value FROM wp_gf_entry_meta detail2 WHERE detail2.entry_id = wp_gf_entry_meta.entry_id AND meta_key = "303" ) as status, ' .
    'status as lead_status, wp_gf_entry.date_created ' .
    'FROM wp_gf_entry_meta ' .
    'left outer join wp_gf_entry on wp_gf_entry_meta.entry_id=wp_gf_entry.id ' .
    'left outer join wp_gf_form on wp_gf_entry_meta.form_id= wp_gf_form.id ' .
    'WHERE meta_value = "' . $email . '" ' .
    'AND entry_id != ' . $currEntryID . ' ' .
    'AND status="active"  ' .
    'ORDER BY `wp_gf_entry_meta`.`entry_id` DESC';

  $results = $wpdb->get_results($sql);

  //don't return an empty table 
  if ($wpdb->num_rows == 0) {
    return '';
  }

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
  $exclude_type = array('Attendee', 'Invoice', 'Default');

  foreach ($results as $addData) {
    $form = GFAPI::get_form($addData->form_id);

    //exclude certain form types
    if (isset($form['form_type']) && !in_array($form['form_type'], $exclude_type)) {
      $outputURL = admin_url('admin.php') . "?page=gf_entries&view=entry&id=" . $addData->form_id . '&lid=' . $addData->entry_id;

      $addEntries .= '<tr>';
      $date = date_create($addData->date_created);

      $addEntries .= '<td><a target="_blank" href="' . $outputURL . '">' . $addData->entry_id . '</a></td>'
        . '<td>' . $addData->projectName . '</td>'
        . '<td>' . $addData->form_title . '</td>'
        . '<td>' . date_format($date, "m-d-Y") . '</td>'
        . '<td>' . ucwords($addData->status) . '</td>'
        . '</tr>';
    }
  }

  $addEntries .= '</table>';

  return $addEntries;
}


//create a dropdown list of available notifications for this form
function get_form_notifications($form, $entryID) {
  $form_id = $form['id'];
  $return  =
    '<div id="sendNotifications' . $entryID . '>' .
    '<div class="message" style="display:none;"></div>' .
    '<input type="hidden" id="gfnonce_' . $entryID . '" value="' . wp_create_nonce('gf_resend_notifications') . '" />';
  $notifications = GFCommon::get_notifications('resend_notifications', $form);
  $key_values  = array_column($notifications, 'name');
  array_multisort($key_values, SORT_ASC, $notifications);

  if (!is_array($notifications) || count($form['notifications']) <= 0) {
    $return .= '<p class="description">' . esc_html_e('You cannot resend notifications for this entry because this form does not currently have any notifications configured.', 'gravityforms') . '</p>';
    $return .= '<a href="' . admin_url("admin.php?page=gf_edit_forms&view=settings&subview=notification&id={$form_id}") . '" class="button">' . esc_html_e('Configure Notifications', 'gravityforms') . '</a>';
  } else {
    $return  .= '<select id="gform_notifications_' . $entryID . '">';
    foreach ($notifications as $notification) {
      //the isActive only gets set when a notification is deactivated, and even then it's usually a space
      if (isset($notification['isActive']) && (!$notification['isActive'] || $notification['isActive'] == '')) {
        continue;
      }
      $return .= '<option class="gform_notifications" value="' . esc_attr($notification['id']) . '" id="notification_' . esc_attr($notification['id']) . '" onclick="toggleNotificationOverride();">' . esc_html($notification['name']) . '</option>';
    }
    $return  .= '</select>';

    $return .= '<div id="notifications_override_settings">
                  <p class="description" style="padding-top:0; margin-top:0; width:99%;">You may override the default notification settings
                    by entering a comma delimited list of emails to which the selected notifications should be sent.</p>
                  <label for="notification_override_email">' . esc_html__('Send To', 'gravityforms') . ' ' . '</label>
                  <input type="text" name="notification_override_email" id="notification_override_email_' . $entryID . '" style="width:50%;" />
                  <br /><br />
                </div>';

    $return .=  '<input type="button" name="notification_resend" value="' . esc_attr('Resend', 'gravityforms') . '" class="button" style="" onclick="ResendNotifications(' . $entryID . ',' . $form_id . ');" />' .
      '<span id="please_wait_container" style="display:none; margin-left: 5px;">' .
      ' <i class="gficon-gravityforms-spinner-icon gficon-spin"></i> ' . esc_html__('Resending...', 'gravityforms') .
      '</span>';
  }
  $return .= '</div>';

  return $return;
}

function get_checkbox_value($field, $entry, $fieldName, $edit_cap = 'view') {
  $value     = '';

  //is this a valid field in the form
  if ($field != NULL) {
    foreach ($field->mergedOptions as $option) {
      //is this option set on the entry?
      $checked = (isset($entry[$option["id"]]) && $entry[$option["id"]] != '' ? 'checked' : '');
      if ($edit_cap == 'edit') {
        $value .= '<input type="checkbox" ' . $checked . ' name="' . $fieldName . '[]" style="margin: 3px;" value="' . $option["id"] . '_' . $option['value'] . '" />' . $option['text'] . ' <br />';
      } elseif ($checked == 'checked') { //view only
        $value .= $option['text'] . '<br/>';
      }
    }
  }
  return $value;
}

function mf_get_schedule_only($entry_id){
  global $wpdb;
  $return = array();
  $sql = "SELECT wp_mf_schedule.id as sched_id, start_dt, end_dt, type, area, subarea, location ".
        "FROM `wp_mf_schedule` ".
        "left outer join wp_mf_location on wp_mf_schedule.entry_id = wp_mf_location.entry_id and ".
                        "wp_mf_schedule.location_id = wp_mf_location.id ".
        "left outer join wp_mf_faire_subarea on wp_mf_faire_subarea.id=wp_mf_location.subarea_id ".
        "left outer join wp_mf_faire_area   on wp_mf_faire_area.id = wp_mf_faire_subarea.area_id ".
        "WHERE wp_mf_schedule.entry_id=$entry_id ORDER BY `wp_mf_schedule`.`start_dt` ASC";
  $results = $wpdb->get_results($sql, ARRAY_A);
  
  return $results;
}