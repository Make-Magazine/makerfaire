<?php
/*
 * Custom MF modifications to the gravity forms entry listy view
 */
add_filter('gform_pre_entry_list', 'mf_custom_toolbar', 10, 2);

function mf_custom_toolbar($form_id) {      
   $view = (isset($_GET['view']) ? $_GET['view'] : '');
   $page = (isset($_GET['page']) ? $_GET['page'] : '');
   if (($view == '' || $view == 'entries') && $page == 'gf_entries') {
      //append the filter results
      $form = GFAPI::get_form($form_id);
      $fieldSep = '|';
      $output = '<span class="gf_admin_page_subtitle">';

      if (isset($_GET['filterField']) && is_array($_GET['filterField'])) {
         foreach ($_GET['filterField'] as $key => $value) {
            $strpos_row_key = strpos($value, $fieldSep);
            if ($strpos_row_key !== false) { //multi-field filter
               $filterValues = explode($fieldSep, $value);
               $field_id = $filterValues[0];
               $filter_operation = $filterValues[1];
               $fieldValue = $filterValues[2];
               if ($field_id === 'entry_id') {
                  $fieldName = 'Entry ID';
               } elseif (is_numeric($field_id) && $field_id == 0) {
                  $fieldName = 'Any Form Field';
               } else {
                  $field = GFFormsModel::get_field($form, $field_id);
                  if ($field) {
                     $fieldName = (isset($field['adminLabel']) && $field['adminLabel'] != '' ? $field['adminLabel'] : $field['label']);
                     if (is_array($field['choices'])) {
                        foreach ($field['choices'] as $choice) {
                           if ($choice['value'] == $fieldValue) {
                              $fieldValue = $choice['text'];
                           }
                        }
                     }
                  } else {
                     $meta = GFFormsModel::get_entry_meta(array($form['id']));
                     $metaField = $meta[$field_id];
                     if ($metaField) {
                        $fieldName = (isset($metaField['label']) && $metaField['label'] !== '' ? $metaField['label'] : $filterValues[0]);
                        if (is_array($metaField['filter']['choices'])) {
                           foreach ($metaField['filter']['choices'] as $choice) {
                              if ($choice['value'] == $fieldValue)
                                 $fieldValue = $choice['value'];
                           }
                        }
                     }else {
                        $fieldName = $field_id;
                     }
                  }
               }

               //remove the current variable so we can allow for a 'delete' link
               $newArray = $_GET['filterField'];
               $newOutput = '';
               unset($newArray[$key]);
               foreach ($newArray as $newValue) {
                  $newOutput .= '&filterField[]=' . $newValue;
               }

               //get admin title for the field.
               $newURL = "?page=gf_entries&view=entries&id=" . $form_id;
               $newURL .= (rgget('orderby') != '' ? "&orderby=" . rgget('orderby') : '');
               $newURL .= (rgget('order') != '' ? "&order=" . rgget('order') : '');
               $newURL .= (rgget('star') != '' ? "&star=" . rgget('star') : '');
               $newURL .= (rgget('read') != '' ? "&read=" . rgget('read') : '');
               $newURL .= (rgget('filter') != '' ? "&filter=" . rgget('filter') : '');

               $newURL .= $newOutput;

               $output .= '<span class="gf_admin_page_formname">' . stripslashes($fieldName) . ($filterValues[1] != 'is' ? ' (' . stripslashes($filterValues[1]) . ') ' : '') . ': ' . stripslashes($fieldValue);
               $output .= ' <a style="color:red" href="javascript:document.location = \'' . $newURL . '\';">X</a></span>';
            }
         }
      }
      $output .= '</span>';
      echo $output;
   }
}

add_filter('gform_entries_field_value', 'modify_field_display_values', 10, 4);

function modify_field_display_values($value, $form_id, $field_id, $lead) {
   //if this is a website, set it up as link
   if (is_numeric($field_id)) {
      $form = GFAPI::get_form($form_id);
      $field = RGFormsModel::get_field($form, $field_id);
      $input_type = $field->get_input_type();
      $columns = '';
      if ($input_type == 'website') {
        if ($field !== null) {
            $value = $field->get_value_entry_list($value, $lead, $field_id, $columns, $form);
            $value = "<a href='" . esc_attr($value) . "' target='_blank' alt='" . esc_attr($value) . "' title='" . esc_attr($value) . "'>" . esc_attr(GFCommon::truncate_url($value)) . '</a>';
         } else {
            $value = esc_html($value);
         }
      } elseif ($input_type == 'fileupload') {
         $file_path = $lead[$field_id];

         if (!empty($file_path)) {
            //displaying thumbnail (if file is an image) or an icon based on the extension
            $thumb = GFEntryList::get_icon_url($file_path);

            //replace images with an actual thumbnail of the image
            if (strpos($thumb, 'icon_image.gif') !== false) {
               $file_path = esc_attr($file_path);
      
               $photo = legacy_get_fit_remote_image_url($file_path, 115, 115);
               $value = "<a href='$file_path' target='_blank' title='" . __('Click to view', 'gravityforms') . "'><img class='thickbox' style='width: 115px;' src='$photo'/></a>";
            }
         }
      }
   }else{
      //meta data
      global $wpdb;

      switch ($field_id){
         case 'area':
            $value = $wpdb->get_var("SELECT area FROM `wp_mf_location`  
               left outer join wp_mf_faire_subarea on wp_mf_faire_subarea.id=subarea_id
               left outer join wp_mf_faire_area on wp_mf_faire_subarea.area_id=wp_mf_faire_area.ID
            where entry_id=".$lead['id']);
            break;
         case 'subarea':
            $value = $wpdb->get_var("SELECT subarea FROM `wp_mf_location`  
               left outer join wp_mf_faire_subarea on wp_mf_faire_subarea.id=subarea_id
            where entry_id=".$lead['id']);
            break;
         case 'location':
            $value = $wpdb->get_var("SELECT location FROM `wp_mf_location`                
            where entry_id=".$lead['id']);
            break;            
      } 
   }
   return $value;
}

add_filter('gform_search_criteria_entry_list', 'multi_search_criteria_entry_list', 10, 2);

function multi_search_criteria_entry_list($search_criteria, $form_id) {
   $fieldSep = '|';
   $form = GFAPI::get_form($form_id);
   $filterField = rgget('filterField');

   if (isset($filterField) && is_array($filterField)) {
      foreach ($filterField as $key => $value) {
         $strpos_row_key = strpos($value, $fieldSep);
     
         if ($strpos_row_key !== false) { //multi-field filter
            $filterValues = explode($fieldSep, $value);
            $field_id = stripslashes($filterValues[0]);
            $search_operFF = stripslashes($filterValues[1]);
            $fieldValue = stripslashes($filterValues[2]);

            //let's check if an entry ID was entered in the 'All form fields' filter
            if ($field_id == "0" && is_numeric($fieldValue)) {
               $entry = GFAPI::get_entry($fieldValue);
               if (is_array($entry)) {
                  $field_id = 'id';
                  $search_operFF = 'is';
               }
            }
            if($field_id === "entry_id" ) $field_id = "id";
            
            $filter_operator = empty($search_operFF) ? 'is' : $search_operFF;

            $field = GFFormsModel::get_field($form, $key);
            if ($field) {
               $input_type = GFFormsModel::get_input_type($field);
               if ($field->type == 'product' && in_array($input_type, array('radio', 'select'))) {
                  $filter_operator = 'contains';
               }
            }

            $search_criteria['field_filters'][] = array(
               'key' => $field_id,
               'operator' => $filter_operator,
               'value' => $fieldValue,
            );
         }
      }
   }

   return $search_criteria;
}

// Add custom MF Edit link to the entry actions - this will include our multi filter options
//add_action( 'gform_entries_first_column_actions', 'add_MF_edit_link', 10, 5 );
/**
 * Add an Edit link to the GF Entry actions row
 * @param int $form_id      ID of the current form
 * @param int $field_id     The ID of the field in the first column, where the row actions are shown
 * @param string $value        The value of the `$field_id` field
 * @param array  $lead         The current entry data
 * @param string $query_string URL query string for a link to the current entry. Missing the `?page=` part, which is strange. Example: `gf_entries&view=entry&id=35&lid=5212&filter=&paged=1`
 */
function add_MF_edit_link($form_id = NULL, $field_id = NULL, $value = NULL, $lead = array(), $query_string = NULL) {
   if (isset($_GET['filterField']) && is_array($_GET['filterField'])) {
      $filterFields = $_GET['filterField'];
   } else {
      $filterFields = array();
   }

   $filter_qs = '';
   foreach ($filterFields as $filterField) {
      $filter_qs .= '&filterField[]=' . esc_attr($filterField);
   }

   $params = array(
       'page' => 'gf_entries',
       'view' => 'entry',
       'id' => (int) $form_id,
       'lid' => (int) $lead["id"],
       'screen_mode' => 'edit',
   );
   ?>

   <span class="edit edit_entry">
      |
      <a title="<?php esc_attr_e('Edit this entry', 'gravityview'); ?>" href="<?php echo esc_url(add_query_arg($params, admin_url('admin.php?page=' . $query_string)) . $filter_qs); ?>"><?php esc_html_e('MF Edit', 'gravityview'); ?></a>
   </span>
   <?php
}

//remove GF filter links from screen options
add_filter('gform_filter_links_entry_list', 'remove_gf_filter', 10, 3);

function remove_gf_filter($filter_links, $form, $include_counts) {
   $remove_array=array('unread','gv_approved', 'gv_disapproved','gv_unapproved');
   foreach($filter_links as $key=>$filter_item){
      if(in_array($filter_item['id'],$remove_array)){      
         unset($filter_links[$key]);
      }
   }
   return $filter_links;
}

//remove teh approve/dissaprove column added by gravity view
add_filter('gravityview/approve_entries/show-column','gravityview_show_approved_entries_column', 10, 2 );

function gravityview_show_approved_entries_column( $show_approve_column, $form_id ) {
  
  //we want to show the column for the gf info form
  if( $form_id === 253) {
    return true;
  }

  //all other forms, remove the approval column
  return false;
}

/* Quick fix for BA17 Add approve option  to entry list first column */
add_action('gform_entries_first_column_actions', 'first_column_actions', 10, 5);

function first_column_actions($form_id, $field_id, $value, $entry) {
   if ($form_id == 127) {
      $lead_id = $entry['id'];
      echo ' | <a href="javascript:approveEntry(' . $entry['id'] . ')">Accept</a>';
   }
}