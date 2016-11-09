<?php

//adding custom meta fields
add_filter('gform_entry_meta', 'custom_entry_meta', 10, 2);

function custom_entry_meta($entry_meta, $form_id) {
  //data will be stored with the meta key named score
  //  label - entry list will use Score as the column header
  //  is_numeric - used when sorting the entry list, indicates whether the data should be treated as numeric when sorting
  //  is_default_column - when set to true automatically adds the column to the entry list, without having to edit and add the column for display
  //  update_entry_meta_callback - indicates what function to call to update the entry meta upon form submission or editing an entry
  //entry rating
  $entry_meta['entryRating'] = array(
      'label' => 'Rating',
      'is_numeric' => true,
      'update_entry_meta_callback' => 'def_entry_rating',
      'is_default_column' => true,
      'filter' => array(
          'operators' => array('is', 'isnot', '<', '>'),
          'choices' => array(
              array('value' => '0', 'text' => 'Unrated'),
              array('value' => '1', 'text' => '1 Stars'),
              array('value' => '2', 'text' => '2 Stars'),
              array('value' => '3', 'text' => '3 Stars'),
              array('value' => '4', 'text' => '4 Stars'),
              array('value' => '5', 'text' => '5 Stars'),
          )
      )
  );

  //create new meta field to hold original entry id
  $entry_meta['entry_id'] = array(
      'label' => 'Original Entry ID',
      'is_numeric' => true,
      'is_default_column' => false
  );

  //create new meta field to hold resource status and resource assign to
  $entry_meta['res_status'] = array(
      'label' => 'Resource Status',
      'is_numeric' => false,
      'is_default_column' => false,
      'filter' => array(
          'operators' => array('is', 'isnot', '<', '>'),
          'choices' => array(
              array('value' => 'ready', 'text' => 'Ready'),
              array('value' => 'review', 'text' => 'Review'),
              array('value' => 'sent', 'text' => 'Sent'),
          )
      )
  );
  $entry_meta['res_assign'] = array(
      'label' => 'Resource Assign To',
      'is_numeric' => false,
      'is_default_column' => false,
      'update_entry_meta_callback' => 'def_entry_res_status',
      'filter' => array(
          'operators' => array('is', 'isnot', '<', '>'),
          'choices' => array(
              array('value' => 'na', 'text' => 'Not Assigned'),
              array('value' => 'jay', 'text' => 'Jay'),
              array('value' => 'jonathan', 'text' => 'Jonathan'),
              array('value' => 'kerry', 'text' => 'Kerry'),
              array('value' => 'louise', 'text' => 'Louise'),
              array('value' => 'siana', 'text' => 'Siana'),
              array('value' => 'other', 'text' => 'Other'),
          )
      )
  );
  return $entry_meta;
}

//set the default value for entry rating
function update_entry_ID_meta($key, $lead, $form) {
  //default entry_-_id
  //$value = '';
  return $value;
}

//set the default value for entry rating
function def_entry_rating($key, $lead, $form) {
  //default rating
  $value = '0';
  return $value;
}

//set the default value for entry rating
function def_entry_res_status($key, $lead, $form) {
  $value = '0';
  return $value;
}

//AJAX
//update entry meta data
add_action('wp_ajax_update-entry-meta', 'update_entry_metaField');

function update_entry_metaField() {
  $entry_id = $_POST['meta_entry_id'];
  $meta_key = $_POST['meta_key'];
  $meta_value = $_POST['meta_value'];
  // update custom meta field
  gform_update_meta($entry_id, $meta_key, $meta_value);
  echo 'updated';
  // IMPORTANT: don't forget to "exit"
  exit;
}



//formats the ratings field that are displayed in the entries list
add_filter( 'gform_entries_field_value', 'format_ratings', 10, 4 );
function format_ratings( $value, $form_id, $field_id,$entry ) {
  if($field_id=='entryRating'){
    if($value==0){
        return 'No Rating';
    }else{
        return $value .' stars';
    }
  }else{
    //check if this field is a custom meta field
    $meta_key = $field_id;
    //check if meta field - return display value
    $meta = GFFormsModel::get_entry_meta(array( $form_id));
    if(isset($meta[$meta_key])){
      $metaField = $meta[$meta_key];
      if(is_array($metaField['filter']['choices'])){
        foreach($metaField['filter']['choices'] as $choice){
          if($choice['value']==$value)
            $value = $choice['text'];
        }
      }
    }
  }
  return $value;
}


//remove the default entry detail box on the sidebar as we have our own custom one
add_filter('gform_entry_detail_meta_boxes','remove_gf_meta_box',10,3);

function remove_gf_meta_box($meta_boxes, $entry, $form) {
  unset($meta_boxes['submitdiv']);
  return $meta_boxes;
}