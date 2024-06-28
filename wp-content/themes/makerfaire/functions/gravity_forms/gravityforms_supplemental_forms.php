<?php
/* Supplemental forms are used to allow makers to submit additional data that is then written back to their 
   entry. */

//after the supplemental form is submitted, copy the data back to the original entry   
add_action('gform_after_submission', 'update_original_data', 10, 2); //$entry, $form
function update_original_data($entry, $form) {
  global $wpdb;
  $ep_token = rgget('ep_token');

  //nothing to copy here
  if ($ep_token == '') {
    return;
  }

  //find the associated entry id based on the token
  $updateEntryID = $wpdb->get_var(
    $wpdb->prepare(
      "SELECT entry_id FROM wp_gf_entry_meta WHERE `meta_key` = '%s' AND `meta_value` = '%s'",
      'fg_easypassthrough_token',
      $ep_token
    )
  );

  if (isset($updateEntryID) && $updateEntryID != '') {
    gform_update_meta($entry['id'], 'entry_id', $updateEntryID);
    update_original_entry($form, $updateEntryID, $entry);
  }
}

/* This function copies data from the supplemental form back into the 
   Master/Original Entry. Field ID's are noted in the parameter name by 
   prepending field- to the field ID.
   $form = supplemental form Object
   $origEntryID = Master/Original entry to push data to 
 */
function update_original_entry($form, $origEntryID, $suppEntry) {
  //Loop thru form fields 
  foreach ($form['fields'] as $field) {
    $updField = $field->origFieldID;
    //  Do not update values from read only fields
    if (!$field->gwreadonly_enable && $updField != '') {
      switch ($field->type) {
        case 'checkbox':
          if ($updField != '') {
            //loop through and set all checkbox fields blank            
            foreach ($field->inputs as $input) {
              $fromField =  $input['id'];
              //find decimal point
              $fromArr = explode('.', $fromField);
              $decPoint = $fromArr[1];
              $inputID  = str_replace(".", "_", $fromField);
              $updValue =  (isset($_POST['input_' . $inputID]) ? $_POST['input_' . $inputID] : '');
              
              GFAPI::update_entry_field($origEntryID, (int) $updField . '.' . $decPoint, stripslashes($updValue));
            }
          }
          break;
        case 'name':
        case 'address':
          // loop through all inputs and set
          foreach ($field->inputs as $input) {
            $fromField =  $input['id'];

            $inputID  = str_replace(".", "_", $fromField);
            $updValue =  (isset($_POST['input_' . $inputID]) ? $_POST['input_' . $inputID] : '');
            
            GFAPI::update_entry_field($origEntryID, $updField, stripslashes($updValue));
          }
          break;
        case 'list':
          $updValue = ''; //blank out update field in case all values are deleted

          //if the field was populated, link through and build the data
          if (isset($_POST['input_' . $field->id])) {
            $options = array();
            foreach ($field->choices as $choice) {
              $options[] = $choice['value'];
            }

            if (is_array($_POST['input_' . $field->id])) {
              $input_value = $_POST['input_' . $field->id];
              $num_list_items = count($_POST['input_' . $field->id]) - 1;

              $x = 0;
              $output = array();
              while ($x <= $num_list_items) {
                $list_array = array();
                foreach ($options as $option) {
                  $list_array[$option] = $input_value[$x];
                  $x++;
                }
                $output[] = $list_array;
              }
            }

            //list data is stored serialized        
            $updValue = maybe_serialize($output);
          }
          
          GFAPI::update_entry_field($origEntryID, $updField, $updValue);

          break;
        case 'fileupload':
          $updValue = (isset($suppEntry[$field->id]) ? $suppEntry[$field->id] : '');          
          GFAPI::update_entry_field($origEntryID, $updField, stripslashes($updValue));

          //skip these  
        case 'page':
        case 'section':
        case 'html':
          break;
        default:
          if ($field->id == 886) {
          }
          //find submitted value
          if (isset($_POST['input_' . $field->id])) {
            $updValue =  $_POST['input_' . $field->id];
            
            GFAPI::update_entry_field($origEntryID, $updField, stripslashes($updValue));
          }
          break;
      }
    }
  }
  //need to trigger the gform_after_update_entry to trigger RMT logic
  $origEntry = GFAPI::get_entry($origEntryID);
  $origForm  = GFAPI::get_form($origEntry['form_id']);
  do_action('gform_after_update_entry', $origForm, $origEntryID, $origEntry);
}

function rmt_lock_ind($text, $entry_id) {
  $rmtLock = 'No'; //default
  global $wpdb;

  //resource lock indicator
  if (strpos($text, 'rmt_res_cat_lock') !== false) {
    $startPos        = strpos($text, 'rmt_res_cat_lock'); //pos of start of merge tag
    $RmtStartPos     = strpos($text, ':', $startPos);   //pos of start RMT field ID
    $closeBracketPos = strlen($text);

    //resource ID
    $RMTcatID = substr($text, $RmtStartPos + 1, $closeBracketPos - $RmtStartPos - 1);

    //is this a valid RMT field??
    if (is_numeric($RMTcatID)) {
      //find locked value of RMT field
      $lockCount = $wpdb->get_var('SELECT count(*) as count
        FROM `wp_rmt_entry_resources`
        left outer join wp_rmt_resources
            on wp_rmt_entry_resources.resource_id = wp_rmt_resources.id
        where wp_rmt_resources.resource_category_id = ' . $RMTcatID . ' and lockBit=1 and entry_id = ' . $entry_id);
      $mergeTag = substr($text, $startPos, $closeBracketPos - $startPos + 1);
      $rmtLock = str_replace($mergeTag, ($lockCount > 0 ? 'Yes' : 'No'), $text);
    }
  }


  //attribute lock indicator
  if (strpos($text, 'rmt_att_lock') !== false) {
    $startPos        = strpos($text, 'rmt_att_lock'); //pos of start of merge tag
    $RmtStartPos     = strpos($text, ':', $startPos);   //pos of start RMT field ID
    $closeBracketPos = strlen($text);

    //attribute ID
    $RMTid = substr($text, $RmtStartPos + 1, $closeBracketPos - $RmtStartPos - 1);

    //is this a valid RMT field??
    if (is_numeric($RMTid)) {
      //find locked value of RMT field
      $lockBit = $wpdb->get_var('SELECT lockBit FROM `wp_rmt_entry_attributes` where attribute_id = ' . $RMTid . ' and entry_id = ' . $entry_id . ' limit 1');
      $mergeTag = substr($text, $startPos, $closeBracketPos - $startPos + 1);
      $rmtLock = str_replace($mergeTag, ($lockBit == 1 ? 'Yes' : 'No'), $text);
    }
  }
  return $rmtLock;
}

//add new field to supplemental forms to indicate what field to populate in original entry
add_action('gform_field_advanced_settings', 'my_advanced_settings', 10, 2, 9999);
function my_advanced_settings($position, $form_id) {
  //create settings on position 50 (right after Admin Label)
  if ($position == 50) {
?>
    <li class="orig_fieldID_setting field_setting">
      <label for="field_admin_label">
        <?php _e('Field ID to populate from Original Entry', 'makerfaire'); ?>
      </label>
      <input type="text" id="field_orig_fieldID" onchange="SetFieldProperty('origFieldID', this.value);" class="fieldwidth-3" />
    </li>
  <?php
  }
}

//tell GF which fields we want this to show for
add_action('gform_editor_js', function () {
  ?>
  <script type="text/javascript">
    //only display the original field ID on supplemental forms
    if (form.form_type == 'Other') {
      // Add our setting to these field types
      fieldSettings.radio += ', .orig_fieldID_setting';
      fieldSettings.checkbox += ', .orig_fieldID_setting';
      fieldSettings.text += ', .orig_fieldID_setting';
      fieldSettings.textarea += ', .orig_fieldID_setting';
      fieldSettings.email += ', .orig_fieldID_setting';
      fieldSettings.phone += ', .orig_fieldID_setting';
      fieldSettings.number += ', .orig_fieldID_setting';
      fieldSettings.fileupload += ', .orig_fieldID_setting';

      // Make sure our field gets populated with its saved value
      jQuery(document).on("gform_load_field_settings", function(event, field, form) {
        jQuery("#field_orig_fieldID").val(field["origFieldID"]);
      });
    }
  </script>
<?php
});
