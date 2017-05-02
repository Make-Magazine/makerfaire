<?php

/* This adds new form types for the users to select when creating new gravity forms */
add_filter( 'gform_form_settings', 'my_custom_form_setting', 10, 2 );
function my_custom_form_setting( $settings, $form ) {
  global $wpdb;

  $form_type = rgar($form, 'form_type');
  if($form_type=='') $form_type='Other'; //default

  //build select with all form type options
  $sql = $wpdb->get_results("SELECT * FROM `wp_mf_form_types`");
  $select = '<select name="form_type">';
  foreach($sql as $result){
    $select .= '<option value="'.$result->form_type.'" '.($form_type==$result->form_type ?'selected':'').'>'.$result->form_type.'</option>';
  }
  $select .= '</select>';

  $settings['Form Basics']['form_type'] = '
    <tr><th><label for="my_custom_setting">Form Type</label></th>
      <td>'.$select .'</td></tr>';

  // Add option to create invoice from form
  $create_invoice = rgar($form, 'create_invoice');
  $settings['Form Basics']['create_invoice'] = '
    <tr>
      <th><label for="my_custom_setting">Create Invoice</label></th>
      <td>
        <input type="radio" name="create_invoice" '.($create_invoice=='no' ?'checked':'').' value="no"> No<br>
        <input type="radio" name="create_invoice" '.($create_invoice=='yes'?'checked':'').'  value="yes"> Yes
      </td>
    </tr>';

  // Add MAT message
  $mat_message = rgar($form, 'mat_message');
  $settings['Form Basics']['mat_message'] = '
    <tr><th><label for="my_custom_setting">MAT Messaging</label></th>
      <td><textarea rows="4" cols="50" name="mat_message">'.$mat_message.'</textarea></td></tr>';
  return $settings;
}

/* This will save the form type & MAT messaging selected by admin users */
add_filter( 'gform_pre_form_settings_save', 'save_form_type_form_setting' );
function save_form_type_form_setting($form) {
    $form['form_type']   = rgpost('form_type');
    $form['mat_message'] = rgpost('mat_message');
    $form['create_invoice'] = rgpost('create_invoice');
    return $form;
}



//=============================================
// Return field ID number based on the
// the Parameter Name for a specific form
//=============================================
function get_value_by_label($key, $form, $entry=array()) {
  $return = array();
  foreach ($form['fields'] as &$field) {
    $lead_key = $field['inputName'];
    if ($lead_key == $key) {
      $return['id']    = $field['id'];
      if(!empty($entry)){
        $return['value'] = $entry[$field['id']];
      }else{
        $return['value']='';
      }
      return $return;
    }
  }
  return '';
}


//=============================================================================
// Return all field IDs/number based on the Parameter Name for a specific form
//=============================================================================
function get_all_fieldby_name($key, $form, $entry=array()) {
  $return = array();
  foreach ($form['fields'] as &$field) {

    //paramater names are stored in a different place
    if($field['type']=='name' || $field['type']=='address'){
      foreach($field['inputs'] as $choice){
        if(isset($choice['name']) && $choice['name']==$key){
          $return[] = array('id' => $choice['id'], 'value' => (!empty($entry) ? $entry[$choice['id']]:''));
        }
      }
    }else{
      $lead_key = $field['inputName'];
      if ($lead_key == $key) {
        $return[] = array(
          'id'    => $field['id'],
          'value' => (!empty($entry) ? $entry[$field['id']]:'')
        );
      }
    }
  }
  return $return;
}

//=============================================
// Returns gravityform form field array based on field ID
//=============================================
function get_value_by_id($key, $form) {
  $return = array();
  foreach ($form['fields'] as &$field) {
    $lead_key = $field['id'];
    if ($lead_key == $key) {
      return $field;
    }
  }
  return array();
}
/*
 * function to allow easier testing of forms by skipping pages and going
 * directly to the page of the form you want to test
 * To skip a page, simply append the ?form_page=2 parameter to the URL of any
 * page on which you are displaying a Gravity Form
 */
add_filter("gform_pre_render", "gform_skip_page");
function gform_skip_page($form) {
  if(!rgpost("is_submit_{$form['id']}") && rgget('form_page') && is_user_logged_in())
    GFFormDisplay::$submission[$form['id']]["page_number"] = rgget('form_page');
  return $form;
}

/* This filter is triggered before the entry detail page is displayed in admin
 * Using this to add class of entryStandout to specific fields
 */
add_filter( 'gform_entry_field_value', 'entry_field_standout', 10, 4 );
function entry_field_standout( $value, $field, $lead, $form ) {
  //topics/category fields
  if (isset($field['id']) && $field['id'] != 64)
    return $value;

  $value = '<span class="entryStandout">'.$value.'</span>';
  return $value;
}

add_filter( 'gform_entries_column_filter', 'change_column_data', 10, 5 );
function change_column_data( $value, $form_id, $field_id, $entry, $query_string ) {
  //only change the data when form id is 1 and field id is 2
  if ( $form_id != 9) {
    return $value;
  }
  if($field_id == 'source_url'){
    $form = GFAPI::get_form( $entry['form_id'] );
    return $form['title'];
  }
  return $value;
}