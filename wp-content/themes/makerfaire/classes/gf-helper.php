<?php
/**
 * Instead of passing DataTables AJAX requests through admin-ajax.php, directly access the data
 *
 * @since 1.3
 *
 * @param boolean $use_direct_access Default false
 */
//add_filter( 'gravityview/datatables/direct-ajax', '__return_true' );

/* Rewrite rules */
function custom_rewrite_rule() {
	add_rewrite_rule('^mf/([^/]*)/([^/]*)/?','index.php?pagename=maker-faire-gravity-forms-display-page&makerfaire=$matches[1]&entryid=$matches[2]','top');
	add_rewrite_rule('^mfarchives/([^/]*)/?','index.php?pagename=entry-archives&entryslug=$matches[1]','top');
}
add_action('init', 'custom_rewrite_rule', 10, 0);


function custom_rewrite_tag() {
	add_rewrite_tag('%entryid%', '([^&]+)');
	add_rewrite_tag('%entryslug%', '([^&]+)');
	add_rewrite_tag('%makerfaire%', '([^&]+)');
}
add_action('init', 'custom_rewrite_tag', 10, 0);

/* Gravity Forms Specific Helper calls*/


function add_grav_forms(){
	$role = get_role('editor');
	$role->add_cap('gform_full_access');
}
add_action('admin_init','add_grav_forms');

add_filter( 'gform_next_button', 'gform_next_button_markup' );
function gform_next_button_markup( $next_button ) {

	$next_button = '<span class="container-gnb">'. $next_button . '</span>';

	return $next_button;
}

add_filter( 'gform_previous_button', 'gform_previous_button_markup' );
function gform_previous_button_markup( $previous_button ) {

	$previous_button = '<span class="container-gpb">'. $previous_button . '</span>';

	return $previous_button;
}



//add_filter('gform_submit_button','form_submit_button');
//function form_submit_button($button){
//	return '<input id="gform_submit_button_' . $form['id'] . '" class="gform_button gform_submit_button button" type="submit" onclick="if(window["gf_submitting_' . $form['id'] . '"]){return false;} if( !jQuery("#gform_' . $form['id'] . '")[0].checkValidity || jQuery("#gform_' . $form['id'] . '")[0].checkValidity()){window["gf_submitting_' . $form['id'] . '"]=true;} " value="Submit">';
//}

/* Styles to adjust admin screen go here */
add_action( 'admin_head', 'remove_gf_form_toolbar' );

function remove_gf_form_toolbar(){
	?>
     <style>
      		#gf_form_toolbar li.gf_form_toolbar_editor {
		   	    display:none;
		    		}
		    		#gf_form_toolbar li.gf_form_toolbar_settings {
		    display:none;
		    		}
	 #notifications_container {
	 	/*display:none;*/
	 	}

	 	#entry_form div#submitdiv {
	 	display:none;
	 	}
	 	.detail-view-print {
	 	margin-bottom: 20px;
	 	}
     </style>
<?php
}

add_action( 'admin_bar_menu', 'toolbar_link_to_mypage', 999 );

function toolbar_link_to_mypage( $wp_admin_bar ) {
  $locations = get_registered_nav_menus();
  $menus = wp_get_nav_menus();
  $menu_locations = get_nav_menu_locations();

  $location_id = 'mf-admin-bayarea-register-menu';
  if (isset($menu_locations[ $location_id ])) {
    foreach ($menus as $menu) {
      // If the ID of this menu is the ID associated with the location we're searching for
      if ($menu->term_id == $menu_locations[ $location_id ]) {
        // This is the correct menu
        $menu_items = wp_get_nav_menu_items($menu);

        $args = array(
            'id'    => 'mf_admin_parent',
            'title' => 'MF Admin',
            'meta'  => array( 'class' => 'my-toolbar-page' ),
        );

        $wp_admin_bar->add_node( $args );
        buildFaireDrop($wp_admin_bar);

        //build faire specific admin
        foreach ( (array) $menu_items as $key => $menu_item ) {
          if($menu_item->menu_item_parent==0){
            // each MF Admin menu has a parent item set that will tell us which faire to add these menu item's too
            $faire = $menu_item->attr_title;
          }else{
            $args = array(
             'id'    => $menu_item->object_id,
             'title' => $menu_item->title,
             'href'  => $menu_item->url,
             'meta'  => array( 'class' => 'my-toolbar-page' ),
             'parent' => 'mf_admin_parent_'.$faire
            );

           $wp_admin_bar->add_node( $args );
          }
        }
      }
    }
  }

  //new york
  $location_id = 'mf-admin-newyork-register-menu';
  if (isset($menu_locations[ $location_id ])) {
    foreach ($menus as $menu) {
      // If the ID of this menu is the ID associated with the location we're searching for
      if ($menu->term_id == $menu_locations[ $location_id ]) {
        // This is the correct menu
        $menu_items = wp_get_nav_menu_items($menu);
        $wp_admin_bar->add_node( $args );

        foreach ( (array) $menu_items as $key => $menu_item ) {
          if($menu_item->menu_item_parent==0){
            //build faire specific admin
            $faire = $menu_item->attr_title;
          }else{
            $args = array(
                    'id'    => $menu_item->object_id,
                    'title' => $menu_item->title,
                    'href'  => $menu_item->url,
                    'meta'  => array( 'class' => 'my-toolbar-page' ),
                    'parent' => 'mf_admin_parent_'.$faire
            );
          }
          $wp_admin_bar->add_node( $args );
        }
      }
    }
  }

  //faire setup
  $location_id = 'mf-admin-fairesetup-register-menu';

  if (isset($menu_locations[ $location_id ])) {
    foreach ($menus as $menu) {
      // If the ID of this menu is the ID associated with the location we're searching for
      if ($menu->term_id == $menu_locations[ $location_id ]) {
        // This is the correct menu
        $menu_items = wp_get_nav_menu_items($menu);
        foreach ( (array) $menu_items as $key => $menu_item ) {

          $args = array(
              'id'    => $menu_item->object_id,
              'title' => $menu_item->title,
              'href'  => $menu_item->url,
              'meta'  => array( 'class' => 'my-toolbar-page' ),
              'parent' => 'mf_admin_parent_fairesetup'
          );

          $wp_admin_bar->add_node( $args );
        }
      }
    }
  }
}

function buildFaireDrop($wp_admin_bar){
    //build faire drop downs
    global $wpdb;
    $sql = "select *, count(*) as count from wp_mf_faire, wp_rg_lead
                where FIND_IN_SET (wp_rg_lead.form_id,wp_mf_faire.form_ids)> 0 and
                        wp_rg_lead.status = 'active'
                group by wp_mf_faire.faire
                ORDER BY `wp_mf_faire`.`start_dt` DESC";
    foreach($wpdb->get_results($sql) as $row){
        //parent menu
        $args = array(
        'id'    => 'mf_admin_parent_'.$row->faire,
        'title' => $row->faire_name.' ('.$row->count.')',
        'meta'  => array( 'class' => 'my-toolbar-page' ),
        'href'  => admin_url( 'admin.php' ) . '?page=mf_entries&faire='.$row->faire,
        'parent' => 'mf_admin_parent'
        );
        $wp_admin_bar->add_node( $args );

        //build submenu, with form names
        $formSQL = "
            SELECT form_id,form.title,count(*) as count
                    FROM `wp_rg_lead` join wp_rg_form form
                    WHERE form.id = form_id and `form_id` IN (".$row->form_ids.") and status = 'active'
                    group by form_id
                    ORDER BY FIELD(form_id, ".$row->form_ids.")";

            foreach($wpdb->get_results($formSQL) as $formRow){
                $adminURL = admin_url( 'admin.php' ) . "?page=mf_entries&view=entries&id=".$formRow->form_id;

                $args = array(
                        'id'    => 'mf_admin_child_'.$formRow->form_id,
                        'title' => $formRow->title.' ('.$formRow->count.')',
                        'href'  => $adminURL,
                        'meta'  => array( 'class' => 'my-toolbar-page' ),
                        'parent' => 'mf_admin_parent_'.$row->faire);
                $wp_admin_bar->add_node( $args );

                //build submenu of entry status
                $statusSql = "SELECT wp_rg_lead_detail.id,value,count(*)as count FROM `wp_rg_lead_detail` join wp_rg_lead on wp_rg_lead.id = lead_id WHERE wp_rg_lead.form_id = ".$formRow->form_id."    AND wp_rg_lead_detail.field_number = 303 and status = 'active' group by value";

                foreach($wpdb->get_results($statusSql) as $statusRow){
                    $args = array(
                        'id'    => 'mf_admin_subchild_'.$statusRow->id,
                        'title' => $statusRow->value.' ('.$statusRow->count.')',
                        'href'  => $adminURL.'&sort=0&dir=DESC&'.urlencode('filterField[]').'=303|is|'.str_replace(' ','+',$statusRow->value),
                        'meta'  => array( 'class' => 'my-toolbar-page' ),
                        'parent' => 'mf_admin_child_'.$formRow->form_id);
                    $wp_admin_bar->add_node( $args );
                }
            }
            //add scheduling link

            $args = array(
                     'id'    => 'mf_admin_subchild_'.$statusRow->id,
                     'title' => 'Scheduling',
                     'href'  => 'http://makerfaire.com/wp-content/applications/scheduler/makerfaire-scheduling.php?faire_id='.$row->faire,
                     'meta'  => array( 'class' => 'my-toolbar-page' ),
                     'parent' => 'mf_admin_parent_'.$row->faire);
                 $wp_admin_bar->add_node( $args );
    }

    $args = array(
            'id'    => 'mf_admin_parent_rmt',
            'title' => 'RMT',
            'href'  => 'http://makerfaire.com/resource-mgmt/',
            'meta'  => array( 'class' => 'my-toolbar-page' ),
            'target' => '_blank',
            'parent' => 'mf_admin_parent'
            );

    $wp_admin_bar->add_node( $args );
    return $wp_admin_bar;
}

/*
 * After Submission Gravity Forms Action Handling
 */
add_action( 'gform_after_submission', 'updateRMT', 10, 2 );
function updateRMT( $entry, $form ) {
  $result = GFRMTHELPER::gravityforms_makerInfo($entry,$form);
}

/* This function will write all user changes to entries to a database table to create a change report */
add_action('gform_after_update_entry', 'GVupdate_changeRpt', 10, 3 );
function GVupdate_changeRpt($form,$entry_id,$orig_entry){
  //get updated entry
  $updatedEntry = GFAPI::get_entry(esc_attr($entry_id));
  GFRMTHELPER::gravityforms_makerInfo($updatedEntry,$form);
  $updates = array();

  foreach($form['fields'] as $field){
    //send notification after entry is updated in maker admin
    $input_id = $field->id;

    //if field type is checkbox we need to compare each of the inputs for changes
    $inputs = $field->get_entry_inputs();
    $status_at_update = (isset($orig_entry['303'])?$orig_entry['303']:'');
    if ( is_array( $inputs ) ) {
      foreach ( $inputs as $input ) {
        $input_id = $input['id'];
        $origField    = (isset($orig_entry[$input_id])   ?  $orig_entry[$input_id ] : '');
        $updatedField = (isset($updatedEntry[$input_id]) ?  $updatedEntry[$input_id ] : '');
        $fieldLabel   = ($field['adminLabel']!=''?$field['adminLabel']:$field['label']);
        if($origField!=$updatedField){
          //update field id
          $updates[] = array('lead_id'=>$entry_id,
                            'field_id'=>$input_id,
                            'field_before'=>$origField,
                            'field_after'=>$updatedField,
                            'fieldLabel'=>$fieldLabel,
                            'status_at_update'=>$status_at_update);
        }
      }
    } else {
      $origField    = (isset($orig_entry[$input_id])   ?  $orig_entry[$input_id ] : '');
      $updatedField = (isset($updatedEntry[$input_id]) ?  $updatedEntry[$input_id ] : '');
      $fieldLabel   = ($field['adminLabel']!=''?$field['adminLabel']:$field['label']);
      if($origField!=$updatedField){
        //update field id
        $updates[] = array('lead_id'=>$entry_id,
                          'field_id'=>$input_id,
                          'field_before'=>$origField,
                          'field_after'=>$updatedField,
                          'fieldLabel'=>$fieldLabel,
                          'status_at_update'=>$status_at_update);
      }
    }
  }

  //check if there are any updates to process
  if(!empty($updates)){
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;//current user id
    $inserts = '';
    //field name

    //update database with this information
    foreach($updates as $update){
     if($inserts !='') $inserts.= ',';
      $inserts .= '('.$user_id.','.
              $update['lead_id'].','.
              $form['id'].','.
              $update['field_id'].',"'.
              $update['field_before'].'","'.
              $update['field_after'].'","'.
              $update['fieldLabel'].'","'.
              $update['status_at_update'] . '"'.
              ')';
    }

    $sql = "insert into wp_rg_lead_detail_changes (user_id, lead_id, form_id, field_id, field_before, field_after,fieldLabel,status_at_update) values " .$inserts;


    global $wpdb;
    $wpdb->get_results($sql);
  }
}

/*
 * After Note Added handle jdb sync
*/
add_action( 'gform_post_note_added', 'note_to_jdb', 10, 2 );
function note_to_jdb( $noteid,$entryid,$userid,$username,$note,$notetype ) {
	//error_log('$GFJDBHELPER:gravityforms_send_note_to_jdb:result:'.$noteid);
	//$result=GFJDBHELPER::gravityforms_send_note_to_jdb($entryid,$noteid,$note);
	//error_log('GFJDBHELPER:gravityforms_send_note_to_jdb:result:'.$result);

}

//action to modify field 320 to display the text instead of the taxonomy code
add_filter("gform_entry_field_value", "setTaxName", 10, 4);
function setTaxName($value, $field, $lead, $form){
    $field_type = RGFormsModel::get_input_type($field);

	if( in_array( $field_type, array('checkbox', 'select', 'radio') ) ){
		$value = RGFormsModel::get_lead_field_value( $lead, $field );
		return GFCommon::get_lead_field_display( $field, $value, $lead["currency"], true );
	}
	else{
		return $value;
	}

}

add_filter( 'gform_export_field_value', 'set_export_values', 10, 4 );
function set_export_values( $value, $form_id, $field_id, $lead ) {

    if($field_id==320){
        $form = GFAPI::get_form( $form_id );

        foreach( $form['fields'] as $field ) {
            if ( $field->id == $field_id) {
                if( in_array( $field->type, array('checkbox', 'select', 'radio') ) ){
                    $value = RGFormsModel::get_lead_field_value( $lead, $field );
                    return GFCommon::get_lead_field_display( $field, $value, $lead["currency"], true );
                }else{
                        return $value;
                }

            }

        }
    }
    return $value;
}

function createGUID($id){

        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid($id, true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// "}"
        return $uuid;
}