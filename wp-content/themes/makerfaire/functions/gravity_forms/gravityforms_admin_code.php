<?php

/* This adds new form types for the users to select when creating new gravity forms */
add_filter('gform_form_settings_fields', 'my_custom_form_setting', 10, 2);

function my_custom_form_setting($settings, $form) {
   global $wpdb;

   //create section for maker faire settings
   $mf_settings = array();
   $mf_settings['mf_settings']['title'] = 'Maker Faire Settings';

   // Add Form Type
   //build select with all form type options
   $sql = $wpdb->get_results("SELECT * FROM `wp_mf_form_types` order by form_type ASC");
   $choices_array = array();
   foreach ($sql as $result) {
      $choices_array[] = array('label' => $result->form_type, 'value' => $result->form_type);
   }

   $mf_settings['mf_settings']['fields'][] = array(
      'label'           => 'Form Type',
      'name'            => 'form_type',
      'default_value'   => 'Other',
      'type'            => 'select',
      'choices'         => $choices_array
   );

   // Add option to create invoice from form
   $choices_array = array(
      array('label' => 'Yes', 'value' => 'yes'),
      array('label' => 'No', 'value' => 'no'),
   );
   $mf_settings['mf_settings']['fields'][] = array(
      'label'           => 'Create Invoice',
      'name'            => 'create_invoice',
      'default_value'   => 'no',
      'type'            => 'radio',
      'horizontal'      => 1,
      'choices'         => $choices_array
   );

   // Add Maker Portal message
   $mf_settings['mf_settings']['fields'][] = array(
      'label'        => 'Maker Portal Messaging',
      'name'         => 'mat_message',
      'allow_html'   => 1,
      'type'         => 'textarea'
   );

   //add radio to display or hide edit entry resources link
   $choices_array = array(
      array('label' => 'Yes', 'value' => 'yes'),
      array('label' => 'No', 'value' => 'no'),
   );
   $mf_settings['mf_settings']['fields'][] = array(
      'label'           => 'Display Setup/Resources',
      'name'            => 'mat_disp_res_link',
      'default_value'   => 'no',
      'type'            => 'radio',
      'horizontal'      => 1,
      'choices'         => $choices_array
   );

   // Setup/Resources Modal layout
   $mf_settings['mf_settings']['fields'][] = array(
      'label'        => 'Setup/Resource Modal Layout',
      'name'         => 'mat_res_modal_layout',
      'allow_html'   => 1,
      'type'         => 'textarea'
   );

   // Supplemental Form URLs to show on the Maker Portal
   $mf_settings['mf_settings']['fields'][] = array(
				
						'name'  => 'show_supp_forms',
						'type'  => 'toggle',
						'label' => 'Show Supplemental Form Links in Maker Portal',
   );

   $mf_settings['mf_settings']['fields'][] = array(				
      'name'  => 'exhibit_supp_form_URL',
      'type'  => 'text',
      'label' => 'Link to Exhibit Supplemental Form',
      'dependency' => array(
         'live' => 1,
         'fields' => array(
            array(
               'field'=>'show_supp_forms'
            )
         )
      )
   );

   $mf_settings['mf_settings']['fields'][] = array(				
      'name'  => 'presentation_supp_form_URL',
      'type'  => 'text',
      'label' => 'Link to Presentation Supplemental Form',
      'dependency' => array(
         'live' => 1,
         'fields' => array(
            array(
               'field'=>'show_supp_forms'
            )
         )
      )
   );

   $mf_settings['mf_settings']['fields'][] = array(				
      'name'  => 'performer_supp_form_URL',
      'type'  => 'text',
      'label' => 'Link to Performer Supplemental Form',
      'dependency' => array(
         'live' => 1,
         'fields' => array(
            array(
               'field'=>'show_supp_forms'
            )
         )
      )
   );

   $mf_settings['mf_settings']['fields'][] = array(				
      'name'  => 'workshop_supp_form_URL',
      'type'  => 'text',
      'label' => 'Link to Workshop Supplemental Form',
      'dependency' => array(
         'live' => 1,
         'fields' => array(
            array(
               'field'=>'show_supp_forms'
            )
         )
      )
   );
   //place MakerFaire section after Form Basics, and then append all other sections after
   $newSettings = array_merge(array_slice($settings, 0, 1, true), 
   $mf_settings,
   array_slice($settings, 1, count($settings) - 1, true));

   return $newSettings;
}

//=============================================
// Return field ID number based on the
// the Parameter Name for a specific form
//=============================================
function get_value_by_label($key, $form, $entry = array()) {
   $return = array();
   foreach ($form['fields'] as &$field) {
      $lead_key = $field['inputName'];
      if ($lead_key == $key) {
         //is this a checkbox field?
         if ($field['type'] == 'checkbox') {
            $retArray = array();

            foreach ($field['inputs'] as $input) {
               if (isset($entry[$input['id']]) && $entry[$input['id']] == $input['label']) {
                  $retArray[] = array('id' => $input['id'], 'value' => $input['label']);
               }
            }
            $return = $retArray;
         } else {
            $return['id'] = $field['id'];
            if (!empty($entry) && isset($entry[$field['id']])) {
               $return['value'] = $entry[$field['id']];
            } else {
               $return['value'] = '';
            }
         }
         return $return;
      }
   }
   return '';
}

//=============================================================================
// Return all field IDs/number based on the Parameter Name for a specific form
//=============================================================================
function get_all_fieldby_name($key, $form, $entry = array()) {
   $return = array();
   foreach ($form['fields'] as &$field) {

      //paramater names are stored in a different place
      if ($field['type'] == 'name' || $field['type'] == 'address') {
         foreach ($field['inputs'] as $choice) {
            if (isset($choice['name']) && $choice['name'] == $key) {
               $return[] = array('id' => $choice['id'], 'value' => (!empty($entry) && isset($entry[$choice['id']]) ? $entry[$choice['id']] : ''));
            }
         }
      } else {
         $lead_key = $field['inputName'];
         if ($lead_key == $key) {
            $return[] = array(
               'id' => $field['id'],
               'value' => (!empty($entry) && isset($entry[$field['id']]) ? $entry[$field['id']] : '')
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
   if (!rgpost("is_submit_{$form['id']}") && rgget('form_page') && is_user_logged_in())
      GFFormDisplay::$submission[$form['id']]["page_number"] = rgget('form_page');
   return $form;
}

/* This filter is triggered before the entry detail page is displayed in admin
 * Using this to add class of entryStandout to specific fields
 */
add_filter('gform_entry_field_value', 'entry_field_standout', 10, 4);

function entry_field_standout($value, $field, $lead, $form) {
   //topics/category fields
   if (isset($field['id']) && $field['id'] != 64)
      return $value;

   $value = '<span class="entryStandout">' . $value . '</span>';
   return $value;
}

add_filter('gform_entries_column_filter', 'change_column_data', 10, 5);

function change_column_data($value, $form_id, $field_id, $entry, $query_string) {
   if ($form_id != 9) {
      return $value;
   }
   if ($field_id == 'source_url') {
      $form = GFAPI::get_form($entry['form_id']);
      return $form['title'];
   }
   return $value;
}

/* This filter is used to correct the current form when in entry view.
 * When the entry id is set manually the form is not corrected
 */

add_filter('gform_admin_pre_render', 'correct_currententry_formid');

function correct_currententry_formid($form) {
   $current_page = (isset($_GET['page']) ? $_GET['page'] : '');
   $current_view = (isset($_GET['view']) ? $_GET['view'] : '');

   if ($current_page == 'gf_entries' && $current_view == "entry") {
      $current_formid = $_GET['id'];
      $current_entryid = (isset($current_entryid) ? $_GET['lid'] : 0);
      if ($current_entryid !== 0) {
         // Different form is in URL than in the form itself.
         global $wpdb;
         $result = $wpdb->get_results($wpdb->prepare("SELECT id,form_id from wp_gf_entry WHERE id=%d", $current_entryid));
         if ($result[0]) {
            if ($current_formid != $result[0]->form_id) {
               $form = GFFormsModel::get_form_meta(absint($result[0]->form_id));
            }
         }
      }
   }
   return $form;
}
