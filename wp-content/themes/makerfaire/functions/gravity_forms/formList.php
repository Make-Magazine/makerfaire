<?php
add_action('gform_form_list_columns', 'mfform_type_header', 10, 1 ); //$columns
function mfform_type_header($columns){
  $columns['mfform_type'] = esc_html__( 'Form Type', 'gravityforms' );
  return $columns;
}

add_action('gform_form_list_column_mfform_type',  'mfform_type_detail',10, 1);
function mfform_type_detail($item){
  $form = GFAPI::get_form($item->id);
  echo (isset($form['form_type'])?$form['form_type']: '');
}
