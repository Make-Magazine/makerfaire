<?php

/**
 * v2 of the Maker Faire API - Manage Entries used for the Maker Portal
 *
 * This is the call for the API to handle entry data for participangs.
 *
 * @version 2.0
 *
 */

// Stop any direct calls to this file
defined('ABSPATH') or die('This file cannot be called directly!');

$type   = (!empty($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : null);
$email  = (!empty($_REQUEST['email']) ? sanitize_text_field($_REQUEST['email']) : false);

// Double check again we have requested this file
if ($type == 'maker-portal') {
  $data = getAllEntries($email);

  // Output the JSON
  echo json_encode($data);
  exit;
}

function getAllEntries($email, $formID = '', $page = '', $years = '') {
  global $wpdb;

  //check if current users email
  require_once(get_template_directory() . '/models/maker.php');

  //instantiate the model
  $maker   = new maker($email);

  $return = array();
  $return['data'] = array();

  //TBD - Dynamically build the form array based on formtype
  $forms = array(278, 260);

  $user = get_user_by('email', $email);

  //TBD also filter based on created by email
  $search_criteria = array(
    'status' => 'active',
    'field_filters' => array(
      'mode' => 'any',
      array(
        'key'   => '98',
        'value' => $email
      )
    )
  );

  $sorting         = array();
  $paging          = array('offset' => 0, 'page_size' => 999);
  $total_count     = 0;
  foreach ($forms as $formID) {
    //get form data
    $form            = GFAPI::get_form($formID);

    //get faire information
    $faire = $wpdb->get_row("SELECT * FROM wp_mf_faire where FIND_IN_SET ($formID,wp_mf_faire.form_ids)> 0 order by ID DESC limit 1");
    $faire_name   = $faire->faire_name;
    $faire_end_dt = $faire->end_dt;

    //Maker Portal messaging    
    $text = GFCommon::replace_variables(rgar($form, 'mat_message'), $form, array(), false, false);
    $text = do_shortcode($text); //process any conditional logic  
    
    //get entry information
    $entries         = GFAPI::get_entries($formID, $search_criteria, $sorting, $paging, $total_count);

    //convert the field info into a usable array
    $field_array = array();
    foreach ($form['fields'] as $field) {
      $field_array[$field['id']] = $field;
    }

    $return_entries = array();
    foreach ($entries as $entry) {
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

      //GV edit link
      $GVeditLink = do_shortcode('[gv_entry_link action="edit" return="url" view_id="687928" entry_id="'.$entry['id'].'"]');

      //easy passthrough token
      $ep_token = isset($entry['fg_easypassthrough_token'])?$entry['fg_easypassthrough_token']:'';
      
      //set logistics links
      $logistics_links = array();      
      
      if(isset($form['show_supp_forms']) && $form['show_supp_forms']){
        $fieldArr = fieldOutput(339, $entry, $field_array, $form);
        foreach($fieldArr['value'] as $exhibit_type){
          if(stripos($exhibit_type, 'exhibit')!== false && isset($form['exhibit_supp_form_URL'])){
            $logistics_links[] = array('title'=>'Exhibit Logistics', 'link'=>$form['exhibit_supp_form_URL'].'?ep_token='.$ep_token);
          }elseif(stripos($exhibit_type, 'present')!== false && isset($form['presentation_supp_form_URL'])){
            $logistics_links[] = array('title'=>'Presenter Logistics', 'link'=>$form['presentation_supp_form_URL'].'?ep_token='.$ep_token);
          }elseif(stripos($exhibit_type, 'perform')!== false && isset($form['performer_supp_form_URL'])){
            $logistics_links[] = array('title'=>'Performer Logistics', 'link'=>$form['performer_supp_form_URL'].'?ep_token='.$ep_token);
          }elseif(stripos($exhibit_type, 'workshop')!== false && isset($form['workshop_supp_form_URL'])){
            $logistics_links[] = array('title'=>'Workshop Logistics', 'link'=>$form['workshop_supp_form_URL'].'?ep_token='.$ep_token);
          }elseif(stripos($exhibit_type, 'sponsor')!== false || stripos($exhibit_type, 'startup sponsor') !== false){  
            $logistics_links[] = array('title'=>'Sponsor Order Form', 'link'=>'/bay-area/sponsor-order-form/?ep_token='.$ep_token);            
          }
        }  
      }
            

      $return_entries[] = array(
        'project_name'  => $entry['151'],
        'project_id'    => $entry['id'],
        'status'        => $entry['303'],
        'description'   => $entry['16'],
        'flags'         => $flags,
        'req_entry_type' => (isset($entry['896'])?$entry['896']:''),
        'entry_type'    => $exhibit_types,
        'photo'         => $maker_photo,
        'maker_name'    => $maker_name,
        'prelim_loc'    => $prelim_loc,
        'prime_cat'     => html_entity_decode(get_CPT_name($entry['320'])),
        'tasks'         => $maker->get_tasks_by_entry($entry['id']),
        'tickets'       => entryTicketing($entry, 'MAT'),
        'gv_edit_link'  => $GVeditLink,
        'ep_token'      => $ep_token,
        'links'         => $logistics_links
      );
    }
    
    $return['data'][$faire_name] = 
      array('faire_end_dt'    => $faire_end_dt, 
            'maker_messaging' => $text,            
            'entries'         => $return_entries);    
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
  }

  if ($value == '')  return array();
  return array('label' => $label, 'type' => $type, 'value' => $value);
}
