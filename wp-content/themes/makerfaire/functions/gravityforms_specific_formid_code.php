<?php

/* Specific logic for the Barnes and Noble forms */
add_filter( 'gform_pre_validation_43', 'update_bn_fields', 10, 2 );
function update_bn_fields($form ) {
    //add the selected value to the form
    foreach ( $form['fields'] as &$field ) {
        if ( $field->id != 341 && $field->id != 342 && $field->id != 343   ) {
            continue;
        }

        $choices = array();
        $choices[] = array( 'text' => $_POST['input_'.$field->id], 'value' => $_POST['input_'.$field->id] );
        $field->choices = $choices;
    }
    return $form;
}


//for the Barnes and Noble forms the store preference is set by JS.
//If the back button is selected we need to populate these fields
add_filter( 'gform_pre_render_43', 'BN_storeSelect' );
function BN_storeSelect( $form ) {
  if(isset($_POST["input_341"]) || isset($_POST["input_342"]) || isset($_POST["input_343"])){
    //add selected values to form
    foreach ( $form['fields'] as &$field ) {
      if($field->id==341 || $field->id==342 || $field->id==343){
        $choices = array();
        $storeSel = rgpost("input_".$field->id);
        $choices[] = array( 'text' => $storeSel, 'value' => $storeSel);
        $field->choices = $choices;
      }
    }
  }
  return($form);
}
