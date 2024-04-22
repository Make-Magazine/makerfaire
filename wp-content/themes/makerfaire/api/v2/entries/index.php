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
    if ($tab_content_arr){      
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
              if(!empty($fieldOutput))
                $fieldData['field-' . $field_id] = $fieldOutput;
            }
            if(!empty($fieldData))
              $columnData[$columnKey] = $fieldData; //write field data to columns
          }
          if(!empty($columnData))
            $blockData[$blockKey] = array('columns' => $columnData); //write column data to blocks
        }
        if(!empty($blockData)){
          $tabData[$tabkey]['tab_content'][$dataKey] = array('blocks' => $blockData); //write block data to tabs
        }else{
          //since there is no data in the initial or expanded section, remove it
          unset($tabData[$tabkey]['tab_content'][$dataKey]);          
        }                         
      }
      //if there is no data in the tab, remove it
      if(empty($tabData[$tabkey]['tab_content']))
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
    $exhibit_types = (isset($fieldArr['value']) && $fieldArr['value']!=''? implode(", ",$fieldArr['value']):'');
    
    //flags
    $fieldArr = fieldOutput(304, $entry, $field_array, $form);
    $flags    = (isset($fieldArr['value']) && $fieldArr['value']!=''? implode(", ",$fieldArr['value']):'');

    //prelimLoc
    $fieldArr = fieldOutput(302, $entry, $field_array, $form);
    $prelim_loc    = (isset($fieldArr['value']) && $fieldArr['value']!=''? implode(", ",$fieldArr['value']):'');

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
    if(!empty($columnArr)){
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
        if(is_array($list)){
          foreach($list as $list_key=>$item){
            $list[$list_key]["Your Link"] = ($item["Your Link"]!=''?'<a href="'.$item["Your Link"] .'" target="_blank">'.$item["Your Link"].'</a>':''); 
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
        $value = GFAPI::get_notes(array('entry_id' => $entry['id'], 'note_type' => 'user'), array( 'key' => 'id', 'direction' => 'DESC' ));
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
        $value    .= '<input type="button" id="updFlags' . $entry['id'] . '" value="Update Flags" class="button updButton" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_flags\', \'' . $entry['id'] . '\');"/>';
        $value    .= '<span class="updMsg" id="updFlagsMSG' . $entry['id'] . '"></span>';
        break;
      case 'prelim_loc':
        $type = 'html';
        $label = 'Preliminary Location';

        //preliminary locations
        $value     = field_display($entry, $form, '302', 'entry_prelim_loc_' . $entry['id']);
        $value    .= '<textarea id="location_comment_' . $entry['id'].'">'.(isset($entry['307'])?$entry['307']:'').'</textarea>';
        $value    .= '<input type="button" id="updPrelimLoc' . $entry['id'] . '" value="Update Preliminary Location" class="button updButton" onclick="updateMgmt(\'update_prelim_loc\', \'' . $entry['id'] . '\');"/>';
        $value    .= '<span class="updMsg" id="updPrelimLocMSG' . $entry['id'] . '"></span>';
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
      case 'exhibit_type':
        $type = 'html';
        $label = 'Entry Type';
        
        $value     = field_display($entry,$form,'339','admin_exhibit_type_' . $entry['id']);
        $value .= '<input type="button" name="updExhibitType' . $entry['id'] . '" value="Update Entry Type" class="button updButton" onclick="updateMgmt(\'update_exhibit_type\', \'' . $entry['id'] . '\');"/>';
        $value .= '<span class="updMsg" id="updExhibitTypeMsg' . $entry['id'] . '"></span>';
        break;
      case 'edit_status':
        $type = 'html';
        $label = '';
        $value = 
        '<table width="100%" class="entry-status">'.  
           mf_sidebar_entry_status( $form, $entry ) .
           '<tr><td colspan="2"><hr /></td></tr>'.
        '</table>';
        break;  
      case 'fee_mgmt':
        $type = 'html';
        $label = 'Fee Management';
        
        $value     = field_display($entry,$form,'442','info_fee_mgmt_' . $entry['id']);
        $value .= '<input type="button" name="updFeeMgmt' . $entry['id'] . '" value="Update Fee Management" class="button updButton" onclick="updateMgmt(\'update_fee_mgmt\', \'' . $entry['id'] . '\');"/>';
        $value .= '<span class="updMsg" id="updFeeMgmtMsg' . $entry['id'] . '"></span>';  
        break;
      case 'other_entries':
        $type = 'html';
        $label = '';
        $value = '';
        //$value = getAddEntries($entry[98], $entry['id']);
        break;
      case 'public_entry_page':
        $type   = 'html';
        $label  = '';
        $value  = '<a href="/maker/entry/'.$entry['id'].'" target="_none">Public Entry Page</a>';
        break;
    }
  }
  if ($arg == 'no_label')  $label = '';
  if($value=='')  return array();
  return array('label' => $label, 'type' => $type, 'value' => $value);
}

function getAddEntries($email, $currEntryID) {
  global $wpdb;

  $addEntriesCnt = 0;
  //additional Entries
  $addEntries = '<table width="100%">
  <thead>
    <tr>      
      <th>Record ID   </th>
      <th>Project Name</th>
      <th>Form Name   </th>
      <th>Status      </th>
    </tr>
  </thead>';

  $addEntriesCnt = 0;

  $results = $wpdb->get_results('SELECT  *,
                                    (SELECT meta_value FROM wp_gf_entry_meta detail2 WHERE detail2.entry_id = wp_gf_entry_meta.entry_id AND meta_key = 151 ) as projectName,
                                    (SELECT meta_value FROM wp_gf_entry_meta detail2 WHERE detail2.entry_id = wp_gf_entry_meta.entry_id AND meta_key = 303 ) as status,
                                    (SELECT status FROM wp_gf_entry WHERE wp_gf_entry.id = wp_gf_entry_meta.entry_id) as lead_status
                              FROM wp_gf_entry_meta
                              JOIN wp_gf_form on wp_gf_form.id = wp_gf_entry_meta.form_id
                             WHERE meta_value = "' . $email . '"' .
    '  AND entry_id != ' . $currEntryID . '
                          GROUP BY entry_id
                          ORDER BY entry_id');
                          
  foreach ($results as $addData) {
    $outputURL = admin_url('admin.php') . "?page=gf_entries&view=entry&id=" . $addData->form_id . '&lid=' . $addData->entry_id;
    $addEntriesCnt++;
    $addEntries .= '<tr>';
        
    $addEntries .= '<td><a target="_blank" href="' . $outputURL . '">' . $addData->entry_id . '</a></td>'
      . '<td>' . $addData->projectName . '</td>'
      . '<td>' . $addData->title . '</td>'
      . '<td>' . ($addData->lead_status == 'active' ? $addData->status : ucwords($addData->lead_status)) . '</td>'
      . '</tr>';
  }

  $addEntries .= '</table>';
  return $addEntries;
}
