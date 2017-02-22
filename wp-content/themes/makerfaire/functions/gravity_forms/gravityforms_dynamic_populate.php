<?php
/*
 * This function is to dynamically populate any form field based on parameter name
 */

add_action('gform_entry_post_save', 'calc_field_ind', 5, 2);
add_action('gform_after_update_entry', 'calc_field_pre_process', 5, 3 );
function calc_field_pre_process($form,$entry_id,$orig_entry=array()){
  $entry = GFAPI::get_entry(esc_attr($entry_id));
  //need to reset $form as gravity view removes admin only fields
  $form = GFAPI::get_form($entry['form_id']);
  calc_field_ind($entry, $form);
}

/* If the paramater name of a form field is set to field-ind,
 *   process the conditional logic
 *      If it passes - set field value to Yes
 *      If it fails  - set field value to No
 */
function calc_field_ind($entry, $form) {
  $entry_id = $entry['id'];
  $form_id  = $entry['form_id'];

  foreach ($form['fields'] as &$field) {
    if ($field->inputName == 'field-ind') {

      $field_id = $field->id;
      if(GFCommon::evaluate_conditional_logic( rgar( $field, 'conditionalLogic' ), $form, $entry ) ){
        $updField = 'Yes';
      } else {
        $updField = 'No';
      }

      //use mf_update_entry_field to update the db to avoid duplicates
      mf_update_entry_field( $entry_id, $field_id, $updField );

    }
  }
  return $entry;
}

