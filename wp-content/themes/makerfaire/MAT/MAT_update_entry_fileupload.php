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
    case 'maker_img1':
      $fieldNum=217;
      break;
    case 'maker_img2':
      $fieldNum=224;
      break;
    case 'maker_img3':
      $fieldNum=223;
      break;
    case 'maker_img4':
      $fieldNum=222;
      break;
    case 'maker_img5':
      $fieldNum=220;
      break;
    case 'maker_img6':
      $fieldNum=221;
      break;
    case 'maker_img7':
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