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
    
    //first check if we've already created a master entry. if we have, update it
    if(isset($entry['master_entry_id']) && $entry['master_entry_id']!='') {
      //TBD this statement is removing the token 
      //GFAPI::update_entry( $master_data, $entry['master_entry_id'] );
    }else{        //otherwise, create master entry                        
      //warning HARD CODING to follow

      //transfer the created_by to the master entry so they have edit rights to it
      if(isset( $entry['created_by'])) $master_data['created_by'] = $entry['created_by'];

      //set maker 1 name and email from the contact fields
      
      //160 - maker 1 name
      $master_data['input_160.3'] = (isset($entry['1.3'])?$entry['1.3']:'');
      $master_data['input_160.6'] = (isset($entry['1.6'])?$entry['1.6']:'');       
      
      //161 - maker 1 email
      $master_data['input_161'] = (isset($entry['98'])?$entry['98']:'');               

      //110 - group bio
      $master_data['input_110'] = (isset($entry['8']) ? $entry['8'] :'');       

      //field 27 - Project Website
        /* This is a list field. we need to pull out the first website and populate it  */
      if(isset($entry['99'])){
        $website = unserialize($entry['99']);      
      }       

      //validate the website to ensure it's a valid url      
      if (isset($website[0]) && wp_http_validate_url($website[0])) {
        //check to ensure they have http:// or https:// as part of their url
        $master_data['input_27'] = trim($website[0]);
      }  else{
        $master_data['input_27'] = '';
      }
            
      $master_entry = GFAPI::submit_form($form['master_form_id'],$master_data);      
      if ( is_wp_error( $master_entry ) ) {
        $error_message = $master_entry->get_error_message();
        GFCommon::log_debug( __METHOD__ . '(): GFAPI Error Message => ' . $error_message );        
        return;
      }else{ 
        if(!isset($master_entry['entry_id'])){
          error_log('master entry id is not set');          
          error_log(print_r($master_entry,TRUE));
        }else{
          //move multi images from maker interest form to master form
          //update images here as the submit form doesn't work well with upload files
          GFAPI::update_entry_field( $master_entry['entry_id'], '878', $entry['21'] );       
                       
          //set master_entry_id meta field
          gform_update_meta( $entry['id'], 'master_entry_id', $master_entry['entry_id']);     
        }              
      }  
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
          if ($input['label']!='') {
            //we need to calculate the to parmater and add in the various decimal points
            $to_param = substr($input['id'], strpos($input['id'], ".") + 1);
            $parmsArray[] =  array('from_id' => $input['id'], 'to_param' => $field->inputName.'.'.$to_param);            
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

    $toFieldID = 'input_'.str_replace('.','_',$toFieldID);
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
            if(isset($_POST['input_'.$inputID] )){
              $updValue =  $_POST['input_'.$inputID];            
              GFAPI::update_entry_field( $origEntryID, $updField, stripslashes($updValue) );          
            }            
          }
          break;
        case 'list':                              
          if(isset($_POST['input_'.$field->id])){
            $options=array();
            foreach($field->choices as $choice){
              $options[] = $choice['value'];
            }  
            
            if(is_array($_POST['input_'.$field->id])){
              $input_value = $_POST['input_'.$field->id];
              $num_list_items = count($_POST['input_'.$field->id]) - 1;              

              $x=0;
              $output = array();
              while($x <= $num_list_items){
                $list_array = array();
                foreach($options as $option){
                  $list_array[$option] = $input_value[$x]; 
                  $x++;
                }    
                $output[]=$list_array;                        
              }
            }

            //list data is stored serialized        
            $updValue = maybe_serialize($output);            
             
            $serialize = 'a:2:{i:0;a:2:{s:8:"Platform";s:8:"facebook";s:9:"Your Link";s:20:"https://facebook.com";}i:1;a:2:{s:8:"Platform";s:7:"twitter";s:9:"Your Link";s:19:"https://twitter.com";}}';
 
            GFAPI::update_entry_field( $origEntryID, $field->id, $updValue);  
          }          
          
          break;
        case 'page':
        case 'section':  
        case 'html':
          break;  
        default:
          //find submitted value
          if(isset($_POST['input_'.$field->id])){
            $updValue =  $_POST['input_'.$field->id];                    
            GFAPI::update_entry_field( $origEntryID, $field->id, stripslashes($updValue) );
          }
          
          break;
      }
           
    }
  }
  //uploaded files
  if(isset($_POST['gform_uploaded_files'])){
    $uploaded_files = json_decode(stripslashes($_POST['gform_uploaded_files']));    
    
    foreach($uploaded_files as $key=>$value){
      $inputID  = str_replace("input_", "", $key);      
      GFAPI::update_entry_field( $origEntryID, $inputID, stripslashes($value) );      
    }

  }

  //hard coded fields to push back to master entry - TBD get form of $origEntryID and if form type=master, do the below

  //update Maker 1 name(96) with field 96 - Contact Name
  if(isset($_POST['input_96_3'])) GFAPI::update_entry_field( $origEntryID, '160.3', $_POST['input_96_3'] );
  if(isset($_POST['input_96_6'])) GFAPI::update_entry_field( $origEntryID, '160.6', $_POST['input_96_6'] );      

  //update Maker 1 email(161) with field 98 - Contact Email
  if(isset($_POST['input_98'])) GFAPI::update_entry_field( $origEntryID, '161', $_POST['input_98']);   

}

/* DO NOT USE - OLD function - replaced with update_original_entry 
Used to update linked fields back to original entry */
function OLD_updLinked_fields($form,$origEntryID){
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
