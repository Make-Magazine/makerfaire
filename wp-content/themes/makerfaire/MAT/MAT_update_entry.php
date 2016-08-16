<?php
/* This logic is called when the maker updates his entry via the main public
 * facing entry screen */
  include '../../../../wp-load.php';
  $value    = $_POST['value'];
  $entry_id = $_POST['entry_id'];
  $field    = $_POST['id'];
  //trigger update
  $entry    = GFAPI::get_entry( $entry_id );
  $form_id  = $entry['form_id'];
  $form = GFAPI::get_form($form_id);

  //TBD - prevent sql injection
  switch ($field) {
    case 'project_title':
      GFAPI::update_entry_field( $entry_id, 151, $value);
      break;
    case 'project_short':
      GFAPI::update_entry_field( $entry_id, 16, $value);
      break;
    case 'website':
      GFAPI::update_entry_field( $entry_id, 27, $value);
      break;
    case 'video':
      GFAPI::update_entry_field( $entry_id, 32, $value);
      break;
    //group information
    case 'groupname':
      GFAPI::update_entry_field( $entry_id, 109, $value);
      break;
    case 'groupbio':
      GFAPI::update_entry_field( $entry_id, 110, $value);
      break;
    //maker bio
    case 'maker1bio':
      GFAPI::update_entry_field( $entry_id, 234, $value);
      break;
    case 'maker2bio':
      GFAPI::update_entry_field( $entry_id, 258, $value);
      break;
    case 'maker3bio':
      GFAPI::update_entry_field( $entry_id, 259, $value);
      break;
    case 'maker4bio':
      GFAPI::update_entry_field( $entry_id, 260, $value);
      break;
    case 'maker5bio':
      GFAPI::update_entry_field( $entry_id, 261, $value);
      break;
    case 'maker6bio':
      GFAPI::update_entry_field( $entry_id, 262, $value);
      break;
    case 'maker7bio':
      GFAPI::update_entry_field( $entry_id, 263, $value);
      break;
    //first name
    case 'maker1fname':
      GFAPI::update_entry_field( $entry_id, 160.3, $value);
      break;
    case 'maker2fname':
      GFAPI::update_entry_field( $entry_id, 158.3, $value);
      break;
    case 'maker3fname':
      GFAPI::update_entry_field( $entry_id, 155.3, $value);
      break;
    case 'maker4fname':
      GFAPI::update_entry_field( $entry_id, 156.3, $value);
      break;
    case 'maker5fname':
      GFAPI::update_entry_field( $entry_id, 157.3, $value);
      break;
    case 'maker6fname':
      GFAPI::update_entry_field( $entry_id, 159.3, $value);
      break;
    case 'maker7fname':
      GFAPI::update_entry_field( $entry_id, 154.3, $value);
      break;

    //last name
    case 'maker1lname':
      GFAPI::update_entry_field( $entry_id, 160.6, $value);
      break;
    case 'maker2lname':
      GFAPI::update_entry_field( $entry_id, 158.6, $value);
      break;
    case 'maker3lname':
      GFAPI::update_entry_field( $entry_id, 155.6, $value);
      break;
    case 'maker4lname':
      GFAPI::update_entry_field( $entry_id, 156.6, $value);
      break;
    case 'maker5lname':
      GFAPI::update_entry_field( $entry_id, 157.6, $value);
      break;
    case 'maker6lname':
      GFAPI::update_entry_field( $entry_id, 159.6, $value);
      break;
    case 'maker7lname':
      GFAPI::update_entry_field( $entry_id, 154.6, $value);
      break;
    default:
      $value = 'Error';
      break;
    }

  gf_do_action( array( 'gform_after_update_entry', $form_id), $form, $entry_id, $entry );

  //wp_send_json($value);
  echo $value;
