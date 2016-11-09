<?php

/*
 * Custom MF modifications to the gravity forms entry listy view
 */
add_filter( 'gform_toolbar_menu', 'mf_custom_toolbar', 10, 2 );
function mf_custom_toolbar( $menu_items, $form_id ) {
  $menu_items = array(); //empty out the gravity form toolbar.  this will be replaced by a custom MF toolbar
  if($_GET['view']=='entries') {
    echo return_MF_navigation();
    //append the filter results
    $form         = GFAPI::get_form( $form_id );
    ?>
    <span class="gf_admin_page_subtitle">
      <?php
      if(isset($_GET['field_id'])){
        foreach($_GET['field_id'] as $key=>$value){
          $strpos_row_key = strpos( $value, '|' );
          if ( $strpos_row_key !== false ) { //multi-field filter
            $filterValues = explode("|",$value);
            $fieldValue = $filterValues[2];
            if($filterValues[0]=='entry_id'){
              $fieldName = 'Entry ID';
            }elseif(is_numeric($filterValues[0]) && $filterValues[0]==0){
              $fieldName = 'Any Form Field';
            }else{
              $field = GFFormsModel::get_field( $form, $filterValues[0] );
              if ( $field ) {
                $fieldName= (isset($field['adminLabel'])&&$field['adminLabel']!=''?$field['adminLabel']:$field['label']);
              }else{
                $meta = GFFormsModel::get_entry_meta(array( $form['id']));
                $metaField = $meta[$filterValues[0]];
                if($metaField){
                  $fieldName  = (isset($metaField['label'])&&$metaField['label']!=''?$metaField['label']:$filterValues[0]);
                  if(is_array($metaField['filter']['choices'])){
                    foreach($metaField['filter']['choices'] as $choice){
                      if($choice['value']==$filterValues[2])
                        $fieldValue = $choice['text'];
                    }
                  }

                }else{
                  $fieldName = $filterValues[0];
                }
              }
            }

            $newArray = $_GET['filterField'];
            $newOutput = '';
            unset($newArray[$key]);
            foreach($newArray as $newValue){
              $newOutput .= '&filterField[]='.$newValue;
            }
            //get admin title for the field.
            $newURL  = "?page=gf_entries&view=entries&id=" . $form_id;
            $newURL .= ($sort_field     != '' ? "&sort=" . $sort_field : '');
            $newURL .= ($sort_direction !=' ' ? "&dir=" . $sort_direction : '');
            $newURL .= ($star           != '' ? "&star=" . $star:'');
            $newURL .= ($read           != '' ? "&read=" . $read:'');
            $newURL .= ($filter         != '' ? "&filter=" . $filter : '');
            $newURL .= ($faire          != '' ? "&faire=" . $faire : '');
            $newURL .= $newOutput;
            echo '<span class="gf_admin_page_formname">'.$fieldName.($filterValues[1]!='is'?' ('.$filterValues[1].') ':'').': '.$fieldValue;
            echo ' <a style="color:red" href="javascript:document.location = \''.$newURL.'\';">X</a></span>';
          }
        }
      } ?>
    </span><?php
  } 
  return $menu_items;
}
function return_MF_navigation(){
  global $wpdb;
  //pull from the faire table - faire, faire location, faire name, and faire form ID's
  $sql = "select faire, faire_name, faire_location, form_ids
          from wp_mf_faire
          ORDER BY faire_location ASC, `wp_mf_faire`.`start_dt` DESC";

  foreach($wpdb->get_results($sql) as $row){
    //create an array keyed by faire location- Bay Area, New York, DC
    $faireNav[$row->faire_location][$row->faire] = array('faire-name' => $row->faire_name, 'url'=>admin_url( 'admin.php' ) . '?page=gf_entries&faire='.$row->faire, 'count'=>0);

    //build an array of form id's removing any blank spaces before hand
    $formids = explode(",", trim($row->form_ids));
    $faireCount = 0;
    //loop thru form ids
    foreach($formids as $formID){
      $formSQL = "SELECT form.title, value as entry_status, count(*) as count
                  FROM  wp_rg_lead
                  JOIN  wp_rg_lead_detail
                          ON  wp_rg_lead.id = wp_rg_lead_detail.lead_id AND
                              wp_rg_lead_detail.field_number = 303
                  JOIN  wp_rg_form form
                          ON  wp_rg_lead.form_id = form.id AND
                              is_trash != 1
                  WHERE wp_rg_lead.status = 'active' AND
                        wp_rg_lead.form_id= $formID
                  group by entry_status";
      $formCount = 0;
      foreach($wpdb->get_results($formSQL) as $formRow){
        $formCount += $formRow->count;
        $faireCount += $formRow->count;
        $adminURL = admin_url( 'admin.php' ) . "?page=gf_entries&view=entries&id=".$formID;
        $faireNav[$row->faire_location][$row->faire]['forms'][$formID]['status'][] = array(
              'status'  =>  $formRow->entry_status,
              'url'     =>  $adminURL.'&sort=0&dir=DESC&filterField[]=303|is|'.str_replace(' ','+',$formRow->entry_status),
              'count'   =>  $formRow->count);

        $faireNav[$row->faire_location][$row->faire]['forms'][$formID]['data'] = array(
          'formName'  => $formRow->title,
          'url'       => $adminURL,
          'count'     => $formCount);
      }
    }
    //populate faire count
    $faireNav[$row->faire_location][$row->faire]['count'] = $faireCount;
  }

  //build the nav output
  $nav ='<nav id="faire_nav"><ul>';

  foreach($faireNav as $locKey=>$locations){
    //first build list of locations
    $nav .='<li class="dropdown"><a href="#" style="text-decoration: none" class="dropdown-toggle" data-toggle="dropdown">'.$locKey.'</a>';

    if(is_array($locations)){ //break this down by Faire
      $nav .= '<ul>';
      foreach($locations as $faireKey=>$faire) {
        $nav .= '<li><a href="'.$faire['url'].'">'.$faire['faire-name'].' ('.$faire['count'].')</a>';
        if(is_array($faire['forms'])){  //break this down by Form
          $nav .= '<ul>';
          foreach($faire['forms'] as $formID=>$form) {
            $nav .= '<li><a href="'.$form['data']['url'].'">'.$form['data']['formName'].' ('.$form['data']['count'].')</a>';
            if(is_array($form['status'])){  //break this down by status
              $nav .= '<ul>';
              foreach($form['status'] as $formstatus){
                $nav .= '<li><a href="'.$formstatus['url'].'">'.$formstatus['status'].' ('.$formstatus['count'].')</a></li>';
              }
              $nav .= '</ul>';  //end status break down
            }
            $nav .= '</li>';
          }
          $nav .= '</ul>';  //end form break down
        }
        $nav .= '</li>';
      }
      $nav .= '</ul>';  //end faire  break down
    }
    $nav .= '</li>';
  }
  $nav .= '</ul>';  //end location  break down
  $nav .= '</nav>';



  return $nav;
}


add_filter( 'gform_entries_field_value', 'modify_field_display_values', 10, 4 );
function modify_field_display_values( $value, $form_id, $field_id, $lead ) {
  //if this is a website, set it up as link
  if(is_numeric( $field_id )) {
    $form         = GFAPI::get_form( $form_id );
    $field        = RGFormsModel::get_field( $form, $field_id );
    $input_type =  $field->get_input_type();
    if ( $input_type == 'website') {
      if ( $field !== null ) {
        $value = $field->get_value_entry_list( $value, $lead, $field_id, $columns, $form );
        $value = "<a href='" . esc_attr( $value) . "' target='_blank' alt='" . esc_attr($value) . "' title='" . esc_attr( $value ) . "'>" . esc_attr( GFCommon::truncate_url( $value ) ) . '</a>';
      } else {
        $value = esc_html( $value );
      }
    }elseif($input_type=='fileupload') {
      $file_path = $lead[$field_id];

      if ( ! empty( $file_path ) ) {
        //displaying thumbnail (if file is an image) or an icon based on the extension
        $thumb     = GFEntryList::get_icon_url( $file_path );

        //replace images with an actual thumbnail of the image
        if (strpos($thumb, 'icon_image.gif') !== false) {
          $file_path = esc_attr( $file_path );

          //custom MF code
          //$file_path = legacy_get_resized_remote_image_url($file_path, 115, 115);
          $value     = "<a class='thickbox' href='$file_path' target='_blank' title='" . __( 'Click to view', 'gravityforms' ) . "'><img class='thickbox' style='width: 115px;' src='$file_path'/></a>";
        }
      }
    }
  }
  return $value;
}