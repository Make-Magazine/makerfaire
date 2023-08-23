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