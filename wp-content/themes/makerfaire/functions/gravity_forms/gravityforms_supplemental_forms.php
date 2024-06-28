<?php
/* Supplemental forms are used to allow makers to submit additional data that is then written back to their 
   entry. */ 
 
//after the supplemental form is submitted, copy the data back to the original entry   
add_action('gform_after_submission', 'update_original_data', 10, 2 ); //$entry, $form
function update_original_data($entry, $form ){  
  global $wpdb;
  $ep_token = rgget( 'ep_token' );
  
  //nothing to copy here
  if($ep_token==''){
    return;
  }

  //find the associated entry id based on the token
  $updateEntryID = $wpdb->get_var(
    $wpdb->prepare(
      "SELECT entry_id FROM wp_gf_entry_meta WHERE `meta_key` = '%s' AND `meta_value` = '%s'",
      'fg_easypassthrough_token',
      $ep_token
    )
  );

  if(isset($updateEntryID) && $updateEntryID!=''){
    gform_update_meta( $entry['id'], 'entry_id', $updateEntryID );
    update_original_entry($form,$updateEntryID, $entry);
  }
}

/* This function copies data from the supplemental form back into the 
   Master/Original Entry. Field ID's are noted in the parameter name by 
   prepending field- to the field ID.
   $form = supplemental form Object
   $origEntryID = Master/Original entry to push data to 
 */
function update_original_entry($form, $origEntryID, $suppEntry){
  //Loop thru form fields 
  foreach ($form['fields'] as $field) {        
     //  Do not update values from read only fields
     if(!$field->gwreadonly_enable){            
      $parmName = $field->inputName;                           
      $pos = strpos($parmName, 'field-');
      if ($pos !== false) { //populate by field ID?
        $updField = str_replace("field-", "", $parmName);
      }else{
        $updField = '';
      }
      
      switch($field->type) {
        case 'checkbox':    
          if($updField!=''){
            //loop through and set all checkbox fields blank            
            foreach($field->inputs as $input){          
              $fromField =  $input['id'];
              //find decimal point
              $fromArr = explode('.', $fromField);
              $decPoint = $fromArr[1];
              $inputID  = str_replace(".", "_", $fromField);              
              $updValue =  (isset($_POST['input_'.$inputID])?$_POST['input_'.$inputID]:'');            
              
              GFAPI::update_entry_field( $origEntryID, (int) $updField.'.'.$decPoint, stripslashes($updValue) );
            }  
          }
          break;              
        case 'name':
        case 'address':          
          // loop through all inputs and set
          foreach($field->inputs as $input){          
            $updField='';
            $fromField =  $input['id'];

            //parameter name is stored at the input level            
            $parmName =  (isset($input['name'])?$input['name']:'');
            
            $pos = strpos($parmName, 'field-');
            if ($pos !== false) { //populate by field ID?
              $updField = str_replace("field-", "", $parmName);
            }
            
            //if the update field id was set, push back to the original entry
            if($updField!=''){
              $inputID  = str_replace(".", "_", $fromField);              
              $updValue =  (isset($_POST['input_'.$inputID])?$_POST['input_'.$inputID]:'');    
             
              GFAPI::update_entry_field( $origEntryID, $updField, stripslashes($updValue) );
            }                        
          }
          break;
        case 'list':          
          //if the field id was set, push back to the original entry
          if($updField!=''){
            $updValue = ''; //blank out update field in case all values are deleted

            //if the field was populated, link through and build the data
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
                 
            }   
            GFAPI::update_entry_field( $origEntryID, $updField, $updValue);  
          }                        
          break;
        case 'fileupload':
          /* unable to set a parameter name for file upload fields, so the field ID must be the same 
             on the supplemental form and the master form. 
             In addition the full file path can only be retrieved from the supplemental entry, 
             not the post fields 
          */
          $updValue= (isset($suppEntry[$field->id])?$suppEntry[$field->id] :'');   
    
          GFAPI::update_entry_field( $origEntryID, $field->id, stripslashes($updValue) );  
          break;
        //skip these  
        case 'page':
        case 'section':  
        case 'html':
          break;  
        default:
          //if the field id was set, push back to the original entry
          if($updField!=''){
            //find submitted value
            if(isset($_POST['input_'.$field->id])){
              $updValue =  $_POST['input_'.$field->id];                 
              GFAPI::update_entry_field( $origEntryID, $updField, stripslashes($updValue) );
            }
          }
          
          
          break;
      }
           
    }
  }
/*
  //uploaded files - these must match supplemental field id to master/original field id 
  if(isset($_POST['gform_uploaded_files'])){

    $uploaded_files = json_decode(stripslashes($_POST['gform_uploaded_files']));    

    if(is_array($uploaded_files)){
      foreach($uploaded_files as $key=>$value){
        $inputID  = str_replace("input_", "", $key);    
        echo 'inputID='.$inputID.' update value is '.$suppEntry[$inputID];
        die();
        $updValue= (isset($suppEntry[$inputID])?$suppEntry[$inputID] :'');  
        GFAPI::update_entry_field( $origEntryID, $inputID, stripslashes($updValue) );      
      }
    }    
  }*/

  //hard coded fields to push back to master entry - TBD get form of $origEntryID and if form type=master, do the below

  //update Maker 1 name(96) with field 96 - Contact Name
  if(isset($_POST['input_96_3'])) GFAPI::update_entry_field( $origEntryID, '160.3', $_POST['input_96_3'] );
  if(isset($_POST['input_96_6'])) GFAPI::update_entry_field( $origEntryID, '160.6', $_POST['input_96_6'] );      

  //update Maker 1 email(161) with field 98 - Contact Email
  if(isset($_POST['input_98'])) GFAPI::update_entry_field( $origEntryID, '161', $_POST['input_98']);   

  //need to trigger the gform_after_update_entry to trigger RMT logic
  $origEntry = GFAPI::get_entry($origEntryID);
  $origForm  = GFAPI::get_form($origEntry['form_id']); 
  do_action( 'gform_after_update_entry', $origForm, $origEntryID, $origEntry );  
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