<?php
/* This function is called when the maker updates his entry via the main public
 * facing entry screen */
function MAT_update_entry() {
  global $wpdb;
  $value    = $_POST['value'];
  $entry_id = $_POST['entry_id'];
  $field    = $_POST['id'];
  //TBD - prevent sql injection
  switch ($field) {
    case 'project_title':
      //update field 151
      GFAPI::update_entry_field( $entry_id, 151, $value);
      break;
    case 'project_short':
      //update short description
      GFAPI::update_entry_field( $entry_id, 16, $value);
      break;
    case 'website':
      GFAPI::update_entry_field( $entry_id, 27, $value);
      break;
    case 'video':
      GFAPI::update_entry_field( $entry_id, 32, $value);
      break;
    case 'groupname':
      GFAPI::update_entry_field( $entry_id, 109, $value);
      break;
    case 'groupbio':
      GFAPI::update_entry_field( $entry_id, 110, $value);
      break;
    }
  //wp_send_json($value);
  echo $value;
  // IMPORTANT: don't forget to "exit"
  exit;
}
add_action('wp_ajax_MAT-update-entry','MAT_update_entry');
