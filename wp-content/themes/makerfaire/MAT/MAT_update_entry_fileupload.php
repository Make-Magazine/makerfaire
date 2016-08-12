<?php
include '../../../../wp-load.php';
//check if there are files uploaded
if((isset($_FILES['value']['error']) && $_FILES['value'] == 0) ||
   (!empty($_FILES['value']['tmp_name']) && $_FILES['value']['tmp_name'] != 'none')) {
  if (0 == @filesize($_FILES['value']['tmp_name'])) {
     print "Empty or invalid file.";
     die();
  }

  $entry_id     = $_GET['entry_id'];
  $fieldName    = $_GET['id'];
  switch ($fieldName){
    case 'proj_img':
      $fieldNum=22;
      break;
    case 'groupphoto':
      $fieldNum=111;
      break;
    case 'maker1img':
      $fieldNum=217;
      break;
    case 'maker2img':
      $fieldNum=224;
      break;
    case 'maker3img':
      $fieldNum=223;
      break;
    case 'maker4img':
      $fieldNum=222;
      break;
    case 'maker5img':
      $fieldNum=220;
      break;
    case 'maker6img':
      $fieldNum=221;
      break;
    case 'maker7img':
      $fieldNum=219;
      break;
    default:
      $fieldNum=0;
  }

  //get form ID
  $entry    = GFAPI::get_entry( $entry_id );
  $form_id  = $entry['form_id'];
  $form = GFAPI::get_form($form_id);
  $field = GFFormsModel::get_field( $form, $fieldNum );
  $_FILES['input_'.$fieldNum] = $_FILES['value'];

  //validate uploaded file
  $field->validate( $_FILES['value'], $form);
  if($field->failed_validation){
    echo $field->validation_message;
    echo 'failed';
  }

  $upload = $field->upload_file($form_id, $_FILES['input_'.$fieldNum]);
  GFAPI::update_entry_field( $entry_id, $fieldNum, $upload);

  //trigger update
  gf_do_action( array( 'gform_after_update_entry', $form_id), $form, $entry_id, $entry );
  echo $upload;
  //for security reason, we force to remove all uploaded file
  @unlink($_FILES['value']);
} else {
  print "No file has been uploaded.";
  die();
}