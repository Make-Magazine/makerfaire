<?php

/* This function checks if the entry-id set on the form is valid
 * If it is, then it compares the entered email to see if it matches the previous
 * one used on the entry. if it all passes, then they can move to the next step
 * otherwise it returns errors
 */
add_filter('gform_validation', 'custom_validation');

function custom_validation($validation_result) {
  $form = $validation_result['form'];

  // determine if entry-id and contact-email id's are in the submitted form
  // and what their field id's are
  $entryID = get_value_by_label('entry-id', $form);
  $contact_email = get_value_by_label('contact-email', $form);

  //make sure we are in the right form
  if (!empty($entryID) && !empty($contact_email)) {
    $entryid   = rgpost('input_' . $entryID['id']);
    $sub_email = rgpost('input_' . $contact_email['id']);

    //check if entry-id is valid
    $entry = GFAPI::get_entry($entryid);

    if (is_array($entry) && $entry['status']=='active') {
      //finding Field with ID of 1 and marking it as failed validation
      foreach ($form['fields'] as &$field) {
        if ($field->id == $contact_email['id']) {     //contact_email
          $entryForm = GFAPI::get_form($entry['form_id']);
          $ef_email = get_value_by_label('contact-email', $entryForm, $entry);
          $contactEmail = $ef_email['value'];

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


add_action( 'gform_pre_submission', 'pre_submission' );
function pre_submission($form){
    //check if this is a linked form
    $updateEntryID = get_value_by_label('entry-id', $form);
    if(is_array($updateEntryID)) {
      $entFldID     = $updateEntryID['id'];
      $origEntryID  = $_POST['input_'.$entFldID]; //entry id we are linking to

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
              if ($input['name']!='') {
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
                  mf_update_entry_field( $origEntryID, $updField, stripslashes($updValue) );

                }
              }else{
                //find submitted value
                $updValue =  (isset($_POST['input_'.$field['id']])?$_POST['input_'.$field['id']]:'');
                mf_update_entry_field( $origEntryID, $updField, stripslashes($updValue) );
              }
            }
          }
        }
      } //end foreach loop

      /* After we are done updating the fields, go back in and update RMT on the linked entry */
      $origEntry = GFAPI::get_entry($origEntryID);
      $origform_id = $origEntry['form_id'];
      $origForm = GFAPI::get_form($origform_id);
      GFRMTHELPER::gravityforms_makerInfo($origEntry,$origForm,$type='update');
    }
}

//when a linked form is submitted, find the initial formid based on entry id
// and add the fields from the linked form to that original entry
add_action( 'gform_after_submission', 'GSP_after_submission', 10, 2 );
function GSP_after_submission($entry, $form ){
  // update meta
  $updateEntryID = get_value_by_label('entry-id', $form, $entry);
  if(isset($updateEntryID['value']))
    gform_update_meta( $entry['id'], 'entry_id', $updateEntryID['value'] );
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
