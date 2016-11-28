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
    $entryid = rgpost('input_' . $entryID['id']);
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

add_filter( 'gform_pre_render', 'populate_html' ); //all forms

/*
 * this logic is for page 2 of 'linked forms'
 * It will take the entry id submitted on page one and use that to pull in various data from the original form submission
 */

function populate_html($form) {
  if($form['form_type']=='Other'){
    //this is a 2-page form with the data from page one being displayed in an html field on page 2
    $current_page = GFFormDisplay::get_current_page($form['id']);
    $html_content = "The information you have submitted is as follows:<br/><ul>";
    if ($current_page == 2) {
      foreach ($form['fields'] as &$field) {
        if ($field->inputName == 'entry-id') {
          $entry_id = rgpost('input_' . $field->id);
        }
      }

      $fieldIDarr['project-name'] = 151;
      $fieldIDarr['short-project-desc'] = 16;
      $fieldIDarr['exhibit-contain-fire'] = 83;
      $fieldIDarr['interactive-exhibit'] = 84;
      $fieldIDarr['fire-safety-issues'] = 85;
      $fieldIDarr['serving-food'] = 44;
      $fieldIDarr['you-are-entity'] = 45;
      $fieldIDarr['plans-type'] = "55";

      //find the project name for submitted entry-id
      $entry = GFAPI::get_entry($entry_id);
      foreach ($form['fields'] as &$field) {
        if (isset($fieldIDarr[$field->inputName])) {
          if ($field->inputName == 'plans-type') {
            $planstypevalues = array();
            for ($i = 1; $i <= 6; $i++) {
              if (isset($entry['55.' . $i]) && !empty($entry['55.' . $i])) {
                $planstypevalues[] = $entry['55.' . $i];
              }
            }
            $field->defaultValue = implode(',', $planstypevalues);
          } else {
            $field->defaultValue = $entry[$fieldIDarr[$field->inputName]];
          }
        }
      }
    }
  }
  return($form);
}


//when a linked form is submitted, find the initial formid based on entry id
// and add the fields from the linked form to that original entry
add_action( 'gform_after_submission', 'GSP_after_submission', 10, 2 );
function GSP_after_submission($entry, $form ){
  // update meta
  $updateEntryID = get_value_by_label('entry-id', $form, $entry);
  gform_update_meta( $entry['id'], 'entry_id', $updateEntryID['value'] );
}


