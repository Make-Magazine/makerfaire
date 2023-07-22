<?php
/* 
 * When an entry is accepted, check if it has a master form id set.
 * If it does, create a master form entry
 */
function entry_accepted_cb( $entry ) {  
  //pull form information for this entry
  $form = GFAPI::get_form($entry['form_id']);
  
  //check if a master entry needs to be created
  if(isset($form['master_form_id']) && $form['master_form_id']!=''){    
    $master_data = copy_entry_to_new_form($entry, $form['master_form_id']);
      
    //move multi images from maker interest form to master form
    if($entry['form_id']==258){
      $master_data[855] =  $entry[21];      
    }        

    //first check if we've already created a master entry. if we have, update it
    if(isset($entry['master_entry_id'])&& $entry['master_entry_id']!='') {
      //TBD this statement is removing the token 
      //GFAPI::update_entry( $master_data, $entry['master_entry_id'] );
    }else{        
      //otherwise, create master entry

      //set the master form id
      $master_data['form_id'] = $form['master_form_id'];
      
      $master_entry_id = GFAPI::add_entry($master_data);
      $master_entry    = GFAPI::get_entry($master_entry_id);
     
      $master_form     = GFAPI::get_form($form['master_form_id']);
      gform_update_meta( $entry['id'], 'master_entry_id', $master_entry_id);
      
      // this filter triggers the easy pass through plugin to generate a token
      apply_filters( 'gform_entry_post_save', $master_entry, $master_form );
    }
    
  }    
}

/*
 * this logic copies information from 1 entry into another based on parameter names set in form 2
 * It will return the entry object for the new Form
 */
function copy_entry_to_new_form ($fromEntry){  
  $from_form = GFAPI::get_form($fromEntry['form_id']);
  $fieldIDarr = array(
    'project-name'         => 151,
    'contact-email'        =>  98,
    'exhibit-contain-fire' =>  83,
    'interactive-exhibit'  =>  84,
    'fire-safety-issues'   =>  85,
    'serving-food'         =>  44,
    'you-are-entity'       =>  45,
    'plans-type'           =>  55,
    'short-project-desc'   =>  16,
    'entry-id'             => $fromEntry['id']);    

  $parmsArray = array();  
  
  //find the submitted original entry id
  foreach ($from_form['fields'] as $field) {
    $parmName = '';
    $value = '';
    switch($field->type) {
      //parameter name is stored in a different place
      case 'name':
      case 'address':
        foreach($field->inputs as $key=>$input) {
          if ($input['name']!='') {
            $parmsArray[] =  array('from_id' => $input['id'], 'to_param' => $input['name']);            
          }
        }
        break;
      //TBD - why are you using input name here for to_param
      //where is radio?
      case 'checkbox':
        foreach($field->inputs as $key=>$input) {
          if ($input['name']!='') {
            $parmsArray[] =  array('from_id' => $input['id'], 'to_param' => $field->inputName);            
          }
        }        
        break;         
      
      default:
        if($field->inputName!=''){
          $parmsArray[] =  array('from_id' => $field->id, 'to_param'=>$field->inputName);        
        }
        
        break;   
    }
  }

  //set the master form id
  $toEntry = array();
  foreach($parmsArray as $fieldInfo){
    $parmName = $fieldInfo['to_param'];

    //if parm name starts with 'field-', pull from that field id
    $pos = strpos($parmName, 'field-');
          
    if ($pos !== false) { //populate by field ID?
      //strip the 'field-' from the parameter name to get the field number
      $toFieldID = str_replace("field-", "", $parmName);      
    }else{ //are we populating by specific parameter name        
      if(isset($fieldIDarr[$parmName])){
        $toFieldID = $fieldIDarr[$parmName];
      }else{
        error_log('unknown parameter name');
        error_log(print_r($fieldInfo,TRUE));
        continue;
      }      
    }

    $fromFieldId = $fieldInfo['from_id'];

    if(isset($fromEntry[$fromFieldId])){
      $toEntry[$toFieldID] = $fromEntry[$fromFieldId];              
    }else{
      //this is to be sure to blank out any radio buttons that have been unset
      $toEntry[$toFieldID] = '';      
    }
      
  }
  
  return $toEntry;
}

/* We will copy over all supplemental fields into original entry */
function update_original_entry($form,$origEntryID){
  //Loop thru form fields 
  foreach ($form['fields'] as $field) {
     //  Do not update values from read only fields
     if(!$field->gwreadonly_enable){
      // If the field type is checkbox, name or address, we need to ensure we blank out data for previously submitted information
      switch($field->type) {
        case 'checkbox':                  
        case 'name':
        case 'address':
          foreach($field->inputs as $input){
            $updField = $input['id'];
            $inputID  = str_replace(".", "_", $updField);
            /*
             * if the field is set, update with submitted value, else, update with blanks
             */
            $updValue =  (isset($_POST['input_'.$inputID]) ? $_POST['input_'.$inputID] : '');
            GFAPI::update_entry_field( $origEntryID, $updField, stripslashes($updValue) );          
          }
          break;
        default:
          //find submitted value
          $updValue =  (isset($_POST['input_'.$field['id']])?$_POST['input_'.$field['id']]:'');
          GFAPI::update_entry_field( $origEntryID, $field['id'], stripslashes($updValue) );
          break;
      }           
    }
  }
}

/* DO NOT USE - OLD function - replaced with update_original_entry 
Used to update linked fields back to original entry */
function updLinked_fields($form,$origEntryID){
  //Loop thru form fields and look for parameter names of 'field-*'
  //  These are set to update original entry fields
  foreach ($form['fields'] as $field) {
    //find parameter name
    $parmName = '';
    switch($field->type) {
      //parameter name is stored in a different place
      case 'name':
      case 'address':
        foreach($field->inputs as $key=>$input) {
          if (isset($input['name']) && $input['name']!='') {
            $parmName =  $input['name'];
            $pos = strpos($parmName, 'field-');
            if ($pos !== false) { //populate by field ID?
              $field_id = str_replace("field-", "", $input['name']);
            }
          }
        }
        break;
    }

    if ($parmName=='' && $field->inputName != '') {
      $parmName = $field->inputName;
    }

    if($parmName!=''){
      /* Now that we have the parameter name, check if it contains 'field-' */
      $pos = strpos($parmName, 'field-');

      if ($pos !== false) {
        //find the field ID passed to update the linked entry
        $updField = str_replace("field-", "", $parmName);  //strip the 'field-' from the parameter name to get the field number

        //  Do not update values from read only fields
        if(!$field->gwreadonly_enable){
          //multiple field options to update
          if($field->type == 'checkbox'){
            foreach($field->inputs as $input){
              $updField = $input['id'];
              $inputID  = str_replace(".", "_", $updField);
              /*
               * if the field is set, update with submitted  value
               *  else, update with blanks
               */
              $updValue =  (isset($_POST['input_'.$inputID]) ? $_POST['input_'.$inputID] : '');
              GFAPI::update_entry_field( $origEntryID, $updField, stripslashes($updValue) );          
            }
          }else{
            //find submitted value
            $updValue =  (isset($_POST['input_'.$field['id']])?$_POST['input_'.$field['id']]:'');
            GFAPI::update_entry_field( $origEntryID, $updField, stripslashes($updValue) );
          }
        }
      }
    }
  } //end foreach loop
}

//when a supplemental form is submitted, find the initial formid based on entry id
// and add the fields from the supplemental form to that original entry
add_action('gform_after_update_entry', 'update_original_data_pre', 10, 3 );  // $form,$entry_id,$orig_entry=array()
function update_original_data_pre($form,$entry_id,$orig_entry=array()){
  $entry = GFAPI::get_entry(esc_attr($entry_id));
  update_original_data($entry, $form);
}

add_action('gform_after_submission', 'update_original_data', 10, 2 ); //$entry, $form
function update_original_data($entry, $form ){  
  // update meta
  $updateEntryID = get_value_by_label('entry-id', $form, $entry);
  
  if(isset($updateEntryID['value'])){
    gform_update_meta( $entry['id'], 'entry_id', $updateEntryID['value'] );
    update_original_entry($form,$updateEntryID['value']);
  }
}

function rmt_lock_ind($text, $entry_id) {
  $rmtLock = 'No'; //default
  global $wpdb;

  //resource lock indicator
  if (strpos($text, 'rmt_res_cat_lock') !== false) {
    $startPos        = strpos($text, 'rmt_res_cat_lock'); //pos of start of merge tag
    $RmtStartPos     = strpos($text, ':',$startPos);   //pos of start RMT field ID
    $closeBracketPos = strlen($text);

    //resource ID
    $RMTcatID = substr($text, $RmtStartPos+1,$closeBracketPos-$RmtStartPos-1);

    //is this a valid RMT field??
    if(is_numeric($RMTcatID)) {
      //find locked value of RMT field
      $lockCount = $wpdb->get_var('SELECT count(*) as count
        FROM `wp_rmt_entry_resources`
        left outer join wp_rmt_resources
            on wp_rmt_entry_resources.resource_id = wp_rmt_resources.id
        where wp_rmt_resources.resource_category_id = '.$RMTcatID.' and lockBit=1 and entry_id = '.$entry_id);
      $mergeTag = substr($text, $startPos,$closeBracketPos-$startPos+1);
      $rmtLock = str_replace($mergeTag, ($lockCount>0?'Yes':'No'), $text);
    }
  }


  //attribute lock indicator
  if (strpos($text, 'rmt_att_lock') !== false) {
    $startPos        = strpos($text, 'rmt_att_lock'); //pos of start of merge tag
    $RmtStartPos     = strpos($text, ':',$startPos);   //pos of start RMT field ID
    $closeBracketPos = strlen($text);

    //attribute ID
    $RMTid = substr($text, $RmtStartPos+1,$closeBracketPos-$RmtStartPos-1);

    //is this a valid RMT field??
    if(is_numeric($RMTid)) {
      //find locked value of RMT field
      $lockBit = $wpdb->get_var('SELECT lockBit FROM `wp_rmt_entry_attributes` where attribute_id = '.$RMTid. ' and entry_id = '.$entry_id.' limit 1');
      $mergeTag = substr($text, $startPos,$closeBracketPos-$startPos+1);
      $rmtLock = str_replace($mergeTag, ($lockBit==1?'Yes':'No'), $text);
    }
  }
  return $rmtLock;
}
