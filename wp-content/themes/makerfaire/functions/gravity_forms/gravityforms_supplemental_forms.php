<?php
/* Supplemental forms are used to allow makers to submit additional data that is then written back to their 
   entry. This can be done by either using a token from GF EasyPassthrough or by requiring the makers to
   enter their entry ID and contact email. */ 
 
 
   /*This function checks if the entry-id set on the form is valid
 * If it is, then it compares the entered email to see if it matches the previous
 * one used on the entry. if it all passes, then they can move to the next step
 * otherwise it returns errors
 */

 add_filter('gform_validation', 'custom_validation');
/* Supplemental forms via entry ID and contact email */
function custom_validation($validation_result) {
  $form = $validation_result['form'];

  //supplemental forms are always a form type of other
  if(isset($form['form_type']) && $form['form_type']=='Other'){
    // determine if entry-id and contact-email id's are in the submitted form
    $entryID = get_value_by_label('entry-id', $form);
    $contact_email = get_value_by_label('contact-email', $form);
    
    if (!empty($entryID) && !empty($contact_email)) {
      $entryid   = rgpost('input_' . $entryID['id']);
      $sub_email = rgpost('input_' . $contact_email['id']);

      //check if entry is valid
      $entry = GFAPI::get_entry($entryid);
      
      if (is_array($entry) && $entry['status']=='active') {        
        foreach ($form['fields'] as &$field) {
          if ($field->id == $contact_email['id']) {     //contact_email
            //pull contact email from original entry
            $contactEmail = (isset($entry['98'])? $entry['98']:'');
            
            //check if the submitted email matches the contact email on the entry
            if (strtolower($sub_email) != strtolower($contactEmail)) {
              // set the form validation to false
              $validation_result['is_valid'] = false;
              $field->failed_validation = true;
              $field->validation_message = 'Email does not match contact email on the project';
            }
          }
        }
      } else {
        // set the form validation to false
        $validation_result['is_valid'] = false;
        //finding Field with ID of 1 and marking it as failed validation
        foreach ($form['fields'] as &$field) {
          if ($field->id == $entryID['id']) {
            // set the form validation to false
            $validation_result['is_valid'] = false;
            $field->failed_validation = true;
            $field->validation_message = 'Invalid Project ID';
            break;
          }
        }
      }
    }        
  } //end check form type

  //Assign modified $form object back to the validation result
  $validation_result['form'] = $form;
  return $validation_result;
}

add_filter( 'gform_pre_render', 'populate_fields' ); //all forms
add_filter( 'gform_pre_validation', 'populate_fields' );
add_filter( 'gform_admin_pre_render', 'populate_fields' );
add_filter( 'gform_pre_submission_filter', 'populate_fields' );

/*
 * this logic is for page 2 of 'linked forms'
 * It will take the entry id submitted on page one and use that
 *    to pull in various data from the original form submission
 */

function populate_fields($form) {
  if( !class_exists( 'GFFormDisplay' ) ) {
    return $form;
  }
  $jqueryVal = '';
  if(isset($form['form_type']) && $form['form_type']=='Other'){
    //this is a 2-page form with the data from page one being displayed in an html field on following pages
    $current_page = GFFormDisplay::get_current_page($form['id']);

    if ($current_page > 1) {
      //find the submitted entry id
      $return = get_value_by_label('entry-id', $form, array());

      $entry_id = rgpost('input_'.$return['id']);

      //is entry id set?
      if($entry_id != '') {
        //pull the original entry
        $entry = GFAPI::get_entry($entry_id); //original entry ID
        $form_id = $form['id'];

        //find the submitted original entry id
        foreach ($form['fields'] as &$field) {
          $parmName = '';
          $value = '';
          switch($field->type) {
            //parameter name is stored in a different place
            case 'name':
            case 'address':
              foreach($field->inputs as $key=>$input) {
                if ($input['name']!='') {
                  $parmName =  $input['name'];
                  $pos = strpos($parmName, 'field-');
                  if ($pos !== false) { //populate by field ID?
                    $field_id = str_replace("field-", "", $input['name']);
                    $field->inputs[$key]['defaultValue'] = $entry[$field_id];
                    $jqueryVal .= "jQuery('#input_".$form_id."_".str_replace('.','_',$field_id)."').val('".$entry[$field_id]."');";
                  }
                }
              }
              break;
          }

          if (isset($field->inputName) && $field->inputName != '') {
            $parmName = $field->inputName;

            //check for 'field-' to see if the value should be populated by original entry field data
            $pos = strpos($parmName, 'field-');

            //populate field using field id's from original form
            if ($pos !== false) { //populate by field ID?
              //strip the 'field-' from the parameter name to get the field number
              $field_id = str_replace("field-", "", $parmName);
              $fieldType = $field->type;

              switch ($fieldType) {
                case 'name':
                  foreach($field->inputs as &$input) {  //loop thru name inputs
                    if(isset($input['name']) && $input['name'] != ''){  //check if parameter name is set
                      $pos = strpos($input['name'], 'field-');
                      if ($pos !== false) { //is it requesting to be set by field id?
                        //strip the 'field-' from the parameter name to get the field number
                        $field_id = str_replace("field-", "", $input['name']);
                        $input['content'] = (isset($entry[$field_id]) ? $entry[$field_id] : '');;
                      }
                    }
                  }
                  break;
                case 'checkbox':
                  //find which fields are set
                  foreach($field->inputs as $key=>$input) {
                    //need to get the decimal indicator from the input in order to set the field id
                    if (($pos = strpos($input['id'], ".")) !== FALSE) {
                      $decPos = substr($input['id'], $pos+1);
                    }
                    $fieldNum = $field_id.'.'.$decPos;
                    //check if field is set in the entry
                    if(!empty($entry[$fieldNum])) {
                      if($field->choices[$key]['value'] == $entry[$fieldNum]){
                        $field->choices[$key]['isSelected'] = true;
                        $jqueryVal .= "jQuery( '#choice_".$form_id."_".str_replace('.','_',$input['id'])."' ).prop( 'checked', true );";
                      }
                    }
                  }

                  break;
                default:
                  $field->defaultValue = (isset($entry[$field_id]) ? $entry[$field_id] : "");
                  break;
              }
            }elseif ( strpos( $field->inputName, 'rmt_att_lock' ) !== false ||
                      strpos( $field->inputName, 'rmt_res_cat_lock') !==false
                    ) {  //is paramater name set to a merge field?
              $text = $field->inputName;
              $field->defaultValue = rmt_lock_ind($text, $entry_id);
            } else { //populate by specific parameter name
              //populate fields
              $fieldIDarr = array(
                  'project-name'         => 151,
                  'contact-email'        =>  98,
                  'exhibit-contain-fire' =>  83,
                  'interactive-exhibit'  =>  84,
                  'fire-safety-issues'   =>  85,
                  'serving-food'         =>  44,
                  'you-are-entity'       =>  45,
                  'plans-type'           => "55",
                  'short-project-desc'   =>  16,
                  'entry-id'             => $entry_id);

              //find the project name for submitted entry-id
              if (isset($fieldIDarr[$parmName])) {
                if ($parmName == 'plans-type') {
                  $planstypevalues = array();
                  for ($i = 1; $i <= 6; $i++) {
                    if (isset($entry['55.' . $i]) && !empty($entry['55.' . $i])) {
                      $planstypevalues[] = $entry['55.' . $i];
                    }
                  }
                  $value = implode(',', $planstypevalues);
                } elseif($parmName == 'entry-id') {
                  $value = $entry_id;
                } else {
                  $value = $entry[$fieldIDarr[$parmName]];
                }
              }

              $field->defaultValue = $value;
            }
          }
        }
      }

    } //end check current page
  }
  if($jqueryVal!=''){
    ?>
    <script>
      jQuery( document ).ready(function() {
        <?php echo $jqueryVal;?>
      });
    </script>
    <?php
  }

  return $form;
}

/* This function copies data from the supplemental form back into the 
   Master/Original Entry. Field ID's are noted in the parameter name by 
   prepending field- to the field ID.
   $form = supplemental form Object
   $origEntryID = Master/Original entry to push data to 
 */
function update_original_entry($form, $origEntryID){
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
            $parmName =  $input['name'];
            
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
  
  //uploaded files - these must match supplemental field id to master/original field id 
  if(isset($_POST['gform_uploaded_files'])){
    $uploaded_files = json_decode(stripslashes($_POST['gform_uploaded_files']));    
    if(is_array($uploaded_files)){
      foreach($uploaded_files as $key=>$value){
        $inputID  = str_replace("input_", "", $key);      
        GFAPI::update_entry_field( $origEntryID, $inputID, stripslashes($value) );      
      }
    }    
  }

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
  
  if(isset($updateEntryID['value']) && $updateEntryID['value']!=''){
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