<?php

/*
 * This function is to dynamically populate any form field based on parameter name
 */
add_filter( 'gform_pre_render', 'populate_html' ); //all forms
function populate_html($form) {
  foreach ($form['fields'] as &$field) {
    if ($field->inputName == 'entry-id') {
      $entry_id = rgpost('input_' . $field->id);
    }
  }
}

