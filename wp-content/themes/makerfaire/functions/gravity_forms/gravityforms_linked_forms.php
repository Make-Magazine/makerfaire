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

/*
 * this logic is for page 2 of 'linked forms'
 * It will take the entry id submitted on page one and use that to pull in various data from the original form submission
 */

function populate_fields($form) {
  if($form['form_type']=='Other'){
    //this is a 2-page form with the data from page one being displayed in an html field on page 2
    $current_page = GFFormDisplay::get_current_page($form['id']);

    if ($current_page > 1) {
      $entry_id = rgpost('input_3');

      //is entry id set?
      if($entry_id != '') {
        //pull the original entry
        $entry = GFAPI::get_entry($entry_id); //original entry ID
        $form_id = $form['id'];
        $jqueryVal = '';
        //find the submitted original entry id
        foreach ($form['fields'] as &$field) {
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

          if ($field->inputName != '') {
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
                    if(isset($input['name']) && $input['name']!=''){  //check if parameter name is set
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
                    if(!empty($entry[$input['id']])) {
                      if($field->choices[$key]['value'] == $entry[$input['id']]){
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
            }else { //populate by specific parameter name
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
  echo 'here';
  ?>
  <script>
    jQuery( document ).ready(function() {
      <?php echo $jqueryVal;?>
    });
  </script>
  <?php
  return $form;
}

//when a linked form is submitted, find the initial formid based on entry id
// and add the fields from the linked form to that original entry
add_action( 'gform_after_submission', 'GSP_after_submission', 10, 2 );
function GSP_after_submission($entry, $form ){
  // update meta
  $updateEntryID = get_value_by_label('entry-id', $form, $entry);
  gform_update_meta( $entry['id'], 'entry_id', $updateEntryID['value'] );
}
