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
  $data = getAllEntries2($form);

  // Output the JSON
  echo json_encode($data);

  exit;
}

function getAllEntries2($formID = '', $page = '', $years = '') {
  $return = array();

  $search_criteria = array('status' => 'active');
  $sorting         = array();
  $paging          = array('offset' => 0, 'page_size' => 999);
  $total_count     = 0;
  $entries         = GFAPI::get_entries($formID, $search_criteria, $sorting, $paging, $total_count);
  $form            = GFAPI::get_form($formID);
  /*
  global $wpdb;
  $query =  "SELECT meta.meta_key as field_id, meta.meta_value as value, entry.id as entry_id, ".
              "entry.form_id, entry.date_created, entry.date_updated ".
          "FROM       wp_gf_entry_meta meta ".
          "inner join wp_gf_entry entry ".
          "on         meta.entry_id = entry.id ".
          "where      entry.form_id=260 and entry.status='active'
          ORDER BY    entry_id DESC, meta_key ASC";
  $results = $wpdb->get_results($query,ARRAY_A);

  //build entry array
  $entries = array();
  
  foreach ($results as $result) {        
      $entry_id = $result['entry_id'];
      if(empty($entries[$entry_id])){
          $entries[$entry_id] = array(
              'entry_id'      =>$entry_id,
              'form_id'=>$result['form_id'],
              'date_created'=>$result['date_created'],
              'date_updated'=>$result['date_updated']);
      }

      $field_id = $result['field_id'];
      $value = $result['value'];        
      $entries[$entry_id][$field_id]=$value;                
  }    
*/
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
    if ($tab_content_arr)
      $tab_content['initial']['blocks'] =  retrieve_blocks2($tab_content_arr[1][0]);

    //build the expand section
    preg_match_all("/\[expand\]\s*(.[\S\s]*?)\s*\[\/expand\]/", $tab, $expand_content_arr);

    $expand_return = array();
    //there should only be 1 tab content per tab
    if ($expand_content_arr && isset($expand_content_arr[1][0])) {
      $blocks = retrieve_blocks2($expand_content_arr[1][0]);
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
              $fieldData['field-' . $field_id] = fieldOutput2($field_id, $entry, $field_array);
            }
            $columnData[$columnKey] = $fieldData; //write field data to columns
          }
          $blockData[$blockKey] = array('columns' => $columnData); //write column data to blocks
        }
        $tabData[$tabkey]['tab_content'][$dataKey] = array('blocks' => $blockData); //write block data to tabs                                
      }
    }
    //if group, use the group name, else use the main contact name
    if (strpos($entry['105'], 'group')  !== false) {
      $maker_name = (isset($entry['109']) ? $entry['109'] : '');
    } else {
      $maker_name = (isset($entry['96.3']) ? $entry['96.3'] : '') .
        ' ' .
        (isset($entry['96.6']) ? $entry['96.6'] : '');
    }

    $return['makers'][] = array(
      'tabs'          => $tabData,
      'project_name'  => $entry['151'],
      'project_id'    => $entry['id'],
      'status'        => $entry['303'],
      'description'   => $entry['16'],
      'photo'         => $entry['22'],
      'maker_name'    => $maker_name
    );
  }

  return $return;
}

function retrieve_blocks2($content = '') {
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
    $return[]['columns'] = $columnArr;
  }

  return $return;
}

function fieldOutput2($fieldID, $entry, $field_array) {
  global $form;
  
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
        $value = unserialize($value);
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
      case 'final_location':
        $type  = 'html';
        $label = 'Final Location';
        //$value = display_schedule($formID, $entry, 'summary');;        
        break;
      case 'notes':
        $type  = 'notes';
        $label = 'Notes';
        $value = GFAPI::get_notes(array('entry_id' => $entry['id'], 'note_type' => 'user'));
        break;
      case 'flags':
        $type = 'html';
        $label = 'Flags';
      
        //flags        
        $value     = field_display($entry, $form, '304', 'entry_flags_'.$entry['id']);
        $value    .= '<input type="button" id="updFlags'.$entry['id'].'" value="Update Flags" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_flags\', \''.$entry['id'].'\');"/>';
        $value    .= '<span class="updMsg" id="updFlagsMSG'.$entry['id'].'"></span>';
        break;
      case 'prelim_loc':  
        $type = 'html';
        $label = 'Preliminary Location';
      
        //preliminary locations
        $value     = field_display($entry, $form, '302', 'entry_prelim_loc_'.$entry['id']);
        $value    .= '<input type="button" id="updPrelimLoc'.$entry['id'].'" value="Update Preliminary Location" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_prelim_loc\', \''.$entry['id'].'\');"/>';
        $value    .= '<span class="updMsg" id="updPrelimLocMSG'.$entry['id'].'"></span>';
        
        break;
      
      case 'notes_table':
        $type  = 'html';
        $label = '';
        $value = '<p>Enter Email: <input type="email" placeholder="example@make.co" id="toEmail'.$entry['id'].'" size="40" /></p>' .
          ' <textarea	id="new_note_'.$entry['id'].'"	style="width: 90%; height: 240px;" cols=""	rows=""></textarea>' .
          ' <input type="button" value="Add Note" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'add_note_sidebar\',\''.$entry['id'].'\');"/>' .
          ' <span class="updMsg" id="add_noteMSG_'.$entry['id'].'"></span>';
        break;
      case 'final_location':
        $type  = 'html';
        $label = 'Final Location';
        break;
      case 'rmt':
        $type  = 'listRepeat';
        $label = 'Resources';
        break;
      case 'date_created':
        $type  = 'text';
        $date  = date_create($entry[$fieldID]);
        $value = date_format($date, "m/d/Y");
        $label = 'Created';
        break;
    }
  }

  return array('label' => $label, 'type' => $type, 'value' => $value);
}
