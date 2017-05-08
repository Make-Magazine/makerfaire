<?php
/* This logic is called when the maker updates his entry via the main public
 * facing entry screen */
  include '../../../../wp-load.php';


  $value    = htmlspecialchars(stripslashes($_POST['value']),ENT_QUOTES);

  $entry_id = $_POST['entry_id'];
  $field    = $_POST['id'];

  //trigger update
  $entry    = GFAPI::get_entry( $entry_id );
  $form_id  = $entry['form_id'];
  $form     = GFAPI::get_form($form_id);

  //set who is updating the record
  $current_user = wp_get_current_user();
  $user = $current_user->ID;
  $chgRPTins = array();

  //TBD - prevent sql injection (is this taken care of on GFAPI::update_entry_field?)
  switch ($field) {
    case 'project_title':
      $fieldID    = 151;
      $fieldLabel = 'Project / Exhibit Name';
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'project_short':
      $fieldID    = 16;
      $fieldLabel = 'Short Description';
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'website':
      $fieldID    = 27;
      $fieldLabel = 'Website';
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'video':
      $fieldID = 32;
      $fieldLabel = 'Website';
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    //group information
    case 'groupname':
      $fieldLabel = 'Group Name';
      $fieldID    = 109;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'groupbio':
      $fieldLabel = 'Group Bio';
      $fieldID    = 110;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    //maker bio
    case 'maker1bio':
      $fieldLabel = 'Maker 1 Bio';
      $fieldID    = 234;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker2bio':
      $fieldLabel = 'Maker 2 Bio';
      $fieldID    = 258;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker3bio':
      $fieldLabel = 'Maker 3 Bio';
      $fieldID    = 259;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker4bio':
      $fieldLabel = 'Maker 4 Bio';
      $fieldID    = 260;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker5bio':
      $fieldLabel = 'Maker 5 Bio';
      $fieldID    = 261;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker6bio':
      $fieldLabel = 'Maker 6 Bio';
      $fieldID    = 262;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker7bio':
      $fieldLabel = 'Maker 7 Bio';
      $fieldID    = 263;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    //first name
    case 'maker1fname':
      $fieldLabel = 'Maker 1 First Name';
      $fieldID    = 160.3;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker2fname':
      $fieldLabel = 'Maker 2 First Name';
      $fieldID    = 158.3;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker3fname':
      $fieldLabel = 'Maker 3 First Name';
      $fieldID    = 155.3;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker4fname':
      $fieldLabel = 'Maker 4 First Name';
      $fieldID    = 156.3;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker5fname':
      $fieldLabel = 'Maker 5 First Name';
      $fieldID    = 157.3;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker6fname':
      $fieldLabel = 'Maker 6 First Name';
      $fieldID    = 159.3;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker7fname':
      $fieldLabel = 'Maker 7 First Name';
      $fieldID    = 154.3;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;

    //last name
    case 'maker1lname':
      $fieldLabel = 'Maker 1 Last Name';
      $fieldID    = 160.6;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker2lname':
      $fieldLabel = 'Maker 2 Last Name';
      $fieldID    = 158.6;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker3lname':
      $fieldLabel = 'Maker 3 Last Name';
      $fieldID    = 155.6;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker4lname':
      $fieldLabel = 'Maker 4 Last Name';
      $fieldID    = 156.6;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker5lname':
      $fieldLabel = 'Maker 5 Last Name';
      $fieldID    = 157.6;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker6lname':
      $fieldLabel = 'Maker 6 Last Name';
      $fieldID    = 159.6;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    case 'maker7lname':
      $fieldLabel = 'Maker 7 Last Name';
      $fieldID    = 154.6;
      updateFieldValue($fieldID,$fieldLabel,$value);

      break;
    default:
      $value = 'Error';
      break;
    }

  //update maker table information
  GFRMTHELPER::updateMakerTables($entry_id);

  //wp_send_json($value);
  echo $value;

  function updateFieldValue($fieldID,$fieldLabel,$newValue) {
    global $user; global $entry; global $form_id;
    $chgRPTins = array();

    $entry_id = $entry['id'];
    $fieldValue = (isset($entry[$fieldID])?$entry[$fieldID]:'');
    //update field and change report if needed
    if($fieldValue!=$newValue){
      GFAPI::update_entry_field( $entry_id, $fieldID, $newValue);
      $chgRPTins = RMTchangeArray($user, $entry_id, $form_id, $fieldID, $fieldValue, $newValue, $fieldLabel);
      updateChangeRPT($chgRPTins);
    }
  }