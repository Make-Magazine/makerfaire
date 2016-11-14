<?php

/*
 * This action is fired before the detail is displayed on the entry detail page
 */

add_action("gform_entry_detail_content_before", "mf_entry_detail_head", 10, 2);

/*
 *  Funtion to modify the header on the entry detail page
 */
function mf_entry_detail_head($form, $lead) {
  //get form from entry id in $lead incase the form was changed ($form only represents the original form)
  $form_id = $lead['form_id'];
  $form    = RGFormsModel::get_form_meta($form_id);
  $page_title =
   '<span>'. __( 'Entry #', 'gravityforms' ) . absint( $lead['id'] ).'</span>';
  $page_subtitle =
    '<span class="gf_admin_page_subtitle">'
  . '  <span class="gf_admin_page_formid">ID: '. $form_id . '</span>'
  . '  <span class="gf_admin_page_formname">Form Name: '. $form['title'] .'</span>';
  $statuscount=get_makerfaire_status_counts( $form_id );
  foreach($statuscount as $statuscount){
    $page_subtitle .= '<span class="gf_admin_page_formname">'.  $statuscount['label'].'('.  $statuscount['entries'].')</span>';
  }
  $page_subtitle .= '</span>';

  //return to entries link
  $outputVar = '';
  if(isset($_GET['filterField'])){
    foreach($_GET['filterField'] as $newValue){
        $outputVar .= '&filterField[]='.$newValue;
    }
  }
  $outputURL = admin_url( 'admin.php' ) . "?page=mf_entries&view=entries&id=".$form['id']  . $outputVar;
  if(isset($_GET['sort']))    $outputURL .= '&sort='.rgget('sort');
  if(isset($_GET['filter']))  $outputURL .= '&filter='.rgget( 'filter' );
  if(isset($_GET['dir']))     $outputURL .= '&dir='.rgget( 'dir' );
  if(isset($_GET['star']))    $outputURL .= '&star='.rgget( 'star' );
  if(isset($_GET['read']))    $outputURL .= '&read='.rgget( 'read' );
  if(isset($_GET['paged']))   $outputURL .= '&paged='.rgget( 'paged' );
  if(isset($_GET['faire']))   $outputURL .= '&faire='.rgget( 'faire' );
  $outputURL = '<a href="'. $outputURL .'">Return to entries list</a>';

  ?>
  <script>
    //remove sections for form switcher and form name editing
    jQuery('h2.gf_admin_page_title #gform_settings_page_title').removeClass("gform_settings_page_title gform_settings_page_title_editable");
    jQuery('h2.gf_admin_page_title #gform_settings_page_title').prop('onclick',null).off('click');
    jQuery('.form_switcher_arrow').remove();
    jQuery('#form_switcher_container').remove();
    //change page title
    jQuery('h2.gf_admin_page_title #gform_settings_page_title').html('<?php echo $page_title;?>');

    //change page subtitle to have status counts
    jQuery('h2.gf_admin_page_title .gf_admin_page_subtitle').html('<?php echo $page_subtitle;?>');

    //add in Return to Entries List link
    jQuery('h2.gf_admin_page_title div.gf_entry_detail_pagination').append('<?php echo $outputURL;?>');

  </script>
  <?php
}

/*
 *        AJAX Section
 * Process sidebar updates via ajax
 * This is where our custom post action handing occurs
 */

add_action( 'wp_ajax_mf-update-entry', 'mf_admin_MFupdate_entry' );
function mf_admin_MFupdate_entry(){
  //Get the current action
  $mfAction = $_POST['mfAction'];

  //Only process if there was a gravity forms action
  if (!empty($mfAction)){
    $entry_id     = $_POST['entry_id'];
    $lead         = GFAPI::get_entry( $entry_id );
    $form_id      = isset($lead['form_id']) ? $lead['form_id'] : 0;
    $form         = RGFormsModel::get_form_meta($form_id);

    switch ($mfAction ) {
      // Entry Management Update
      case 'update_entry_management' :
        set_entry_status_content($lead,$form);
        break;
      case 'update_entry_status' :
        set_entry_status($lead,$form,$entry_id);
        break;
      case 'update_ticket_code' :
        $ticket_code  = $_POST['entry_ticket_code'];
        $entry_id     = $entry_id;
        mf_update_entry_field($entry_id,'308',$ticket_code);
        break;
      case 'update_entry_schedule' :
        set_entry_schedule($lead,$form);
        break;
      case 'delete_entry_schedule' :
        delete_entry_schedule($lead,$form);
        break;
      case 'change_form_id' :
        set_form_id($lead,$form);
        break;
      case 'duplicate_entry_id' :
        duplicate_entry_id($lead,$form);
        break;
      case 'send_conf_letter' :
        //first update the schedule if one is set
        set_entry_schedule($lead,$form);
        //then send confirmation letter
        $notifications_to_send = GFCommon::get_notifications_to_send( 'confirmation_letter', $form, $lead );
        foreach ( $notifications_to_send as $notification ) {
          if($notification['isActive']){
            GFCommon::send_notification( $notification, $form, $lead );
          }
        }
        mf_add_note( $entry_id, 'Confirmation Letter sent');
        break;
      //Sidebar Note Add
      case 'add_note_sidebar' :
        add_note_sidebar($lead, $form);
        break;
      //Sidebar Note Delete
      case 'delete_note_sidebar' :
        if(is_array($_POST['note'])){
          delete_note_sidebar($_POST['note']);
        }
        break;
    }

    //update the change report with any changes
    GVupdate_changeRpt($form,$entry_id,$lead);
    // Return the original form which is required for the filter we're including for our custom processing.
    return $form;

  }
}

/* Modify Set Entry Status */
function set_entry_status($lead,$form){
  $entry_id = $lead['id'];
	$acceptance_status_change  = $_POST['entry_info_status_change'];
  $acceptance_current_status = isset($lead['303']) ? $lead['303'] : '';

	$is_acceptance_status_changed = (strcmp($acceptance_current_status, $acceptance_status_change) != 0);

	if (!empty($entry_id)){
		if (!empty($acceptance_status_change)){
      //Update Field for Acceptance Status
      mf_update_entry_field($entry_id,'303',$acceptance_status_change);

      //Reload entry to get any changes in status
      $lead['303'] = $acceptance_status_change;

			//Handle acceptance status changes
			if ($is_acceptance_status_changed ){
        if($acceptance_status_change == 'Accepted'){
          /*
           * If the status is accepted, trigger a cron job to generate EventBrite Tickets.
           * The cron job will trigger action sidebar_entry_update
           */
          wp_schedule_single_event(time() + 1,'sidebar_entry_update', array($entry_id));
          global $wpdb;
          //lock space size attribute if set
          $wpdb->get_results('update `wp_rmt_entry_attributes` set `lockBit` = 1 where attribute_id =  2 and entry_id='. $lead['id']);
        }

				//Create a note of the status change.
				$results = mf_add_note($entry_id, 'EntryID:'.$entry_id.' status changed to '.$acceptance_status_change);

				//Handle notifications for acceptance
				$notifications_to_send = GFCommon::get_notifications_to_send( 'mf_acceptance_status_changed', $form, $lead );
        foreach ( $notifications_to_send as $notification ) {
          if($notification['isActive']){
            GFCommon::send_notification( $notification, $form, $lead );
          }
				}

        //format Entry information
        $entryData = GFRMTHELPER::gravityforms_format_record($lead,$form);

        //update maker table information
        GFRMTHELPER::updateMakerTable($entryData);
			}
		}
	}
}

/**
 * Updates a single field of an entry.
 *
 * @since  1.9
 * @access public
 * @static
 *
 * @param int    $entry_id The ID of the Entry object
 * @param string $input_id The id of the input to be updated. For single input fields such as text, paragraph, website, drop down etc... this will be the same as the field ID.
 *                         For multi input fields such as name, address, checkboxes, etc... the input id will be in the format {FIELD_ID}.{INPUT NUMBER}. ( i.e. "1.3" )
 *                         The $input_id can be obtained by inspecting the key for the specified field in the $entry object.
 *
 * @param mixed  $value    The value to which the field should be set
 *
 * @return bool Whether the entry property was updated successfully
 */
 function mf_update_entry_field( $entry_id, $input_id, $value ) {
	global $wpdb;

	$entry = GFAPI::get_entry( $entry_id );
	if ( is_wp_error( $entry ) ) {
		return $entry;
	}

	$form = GFAPI::get_form( $entry['form_id'] );
	if ( ! $form ) {
		return false;
	}

	$field = GFFormsModel::get_field( $form, $input_id );

	$lead_detail_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}rg_lead_detail WHERE lead_id=%d AND  CAST(field_number AS CHAR) ='%s'", $entry_id, $input_id ) );

	$result = true;
	if ( ! isset( $entry[ $input_id ] ) || $entry[ $input_id ] != $value ){
		$result = GFFormsModel::update_lead_field_value( $form, $entry, $field, $lead_detail_id, $input_id, $value );
	}

	return $result;
}

/*
 * Add a single note
 */
function mf_add_note($leadid,$notetext){
	global $current_user;
	$user_data = get_userdata( $current_user->ID );
	RGFormsModel::add_note( $leadid, $current_user->ID, $user_data->display_name, $notetext );
}

/* Modify Set Entry Status */
function set_entry_status_content($lead,$form){
  $entry_id = $lead['id'];
	$location_change          = $_POST['entry_info_location_change'];
	$flags_change             = $_POST['entry_info_flags_change'];
	$location_comment_change  = $_POST['entry_location_comment'];

	$field302 = RGFormsModel::get_field($form,'302');
	$field304 = RGFormsModel::get_field($form,'304');

	if (!empty($entry_id)){
		/* Clear out old choices */
		foreach(   $field304['inputs'] as $choice){
			mf_update_entry_field($entry_id,$choice['id'],'');
		}
		foreach(   $field302['inputs'] as $choice){
			mf_update_entry_field($entry_id,$choice['id'],'');
		}
		/* Save entries */
		if (!empty($location_change)){
			foreach($location_change as $location_entry){
				$exploded_location_entry=explode("_",$location_entry);
				$entry_info_entry[$exploded_location_entry[0]] = $exploded_location_entry[1];
				mf_update_entry_field($entry_id,$exploded_location_entry[0],$exploded_location_entry[1]);
			}
		}
		if (!empty($flags_change)){
			foreach($flags_change as $flags_entry){
				$exploded_flags_entry=explode("_",$flags_entry);
				$entry_info_entry[$exploded_flags_entry[0]] = $exploded_flags_entry[1];
				mf_update_entry_field($entry_id,$exploded_flags_entry[0],$exploded_flags_entry[1]);
			}
		}

		$entry_info_entry['307'] = $location_comment_change;
    mf_update_entry_field($entry_id,'307',$location_comment_change);

	}
}

/* Modify Form Id Status */
function set_form_id($lead,$form){
  $entry_id    = $lead['id'];
	$form_change = $_POST['entry_form_change'];

	error_log('$form_change='.$form_change);
	error_log('$entry_id='.$entry_id);
	$entry=GFAPI:: get_entry($entry_id);

	$is_form_id_changed = (strcmp($entry['form_id'], $form_change) != 0);

	if (!empty($entry_id)){
		if (!empty($is_form_id_changed)){
			//Update Field for Acceptance Status
			$result = update_entry_form_id($entry,$form_change);
			error_log('UPDATE RESULTS = '.print_r($result,true));

      //add note about form change
      $newForm = RGFormsModel::get_form_meta($form_change);
      mf_add_note( $entry_id, 'Entry changed from '.$form['title'].' to '.$newForm['title']);
		}
	}
}

/**
 * Updates a form id of an entry.
 *
 * @param int    $entry_id The ID of the Entry object
 * @param int    $form_id The Form ID of the Entry object
  *
 * @param mixed  $value    The value to which the field should be set
 *
 * @return bool Whether the entry property was updated successfully
 */
 function update_entry_form_id( $entry_id, $form_id ) {
	global $wpdb;

	$lead_table        = GFFormsModel::get_lead_table_name();
	$lead_detail_table = GFFormsModel::get_lead_details_table_name();
	$lead_meta_table   = GFFormsModel::get_lead_meta_table_name();
	$result     = $wpdb->query(
			$wpdb->prepare( "UPDATE $lead_table SET form_id={$form_id} WHERE id=%d ", $entry_id)
	);
	$wpdb->query(
		$wpdb->prepare( "UPDATE $lead_detail_table SET form_id={$form_id} WHERE lead_id=%d ", $entry_id)
	);
	$wpdb->query(
			$wpdb->prepare( "UPDATE $lead_meta_table SET form_id={$form_id} WHERE lead_id=%d ", $entry_id)
	);

	return $result;
}

/* Copy entry record into specific form*/
function duplicate_entry_id($lead,$form){
  $form_change = $_POST['entry_form_copy']; //selected form field
  $entry_id    = $lead['id'];

  error_log('$duplicating entry id ='.$entry_id.' into form '.$form_change);

  $result     = duplicate_entry_data($form_change,$entry_id);
  error_log('UPDATE RESULTS = '.print_r($result,true));
}

/**
 * Duplicates the contents of a specified entry id into the specified form
 * Adapted from forms_model.php, RGFormsModel::save_lead($Form, $lead) and
 * gravity -forms-addons.php for the gravity forms addon plugin
 * @param  array $form Form object.
 * @param  array $lead Lead object
 * @return void
 */
function duplicate_entry_data($form_change,$current_entry_id ){
  global $wpdb;

  $lead_table        = GFFormsModel::get_lead_table_name();
  $lead_detail_table = GFFormsModel::get_lead_details_table_name();
  $lead_meta_table   = GFFormsModel::get_lead_meta_table_name();

  //pull existing entries information
  $current_lead   = $wpdb->get_results($wpdb->prepare("SELECT * FROM $lead_table          WHERE      id=%d", $current_entry_id));
  $current_fields = $wpdb->get_results($wpdb->prepare("SELECT wp_rg_lead_detail.field_number, wp_rg_lead_detail.value, wp_rg_lead_detail_long.value as long_detail FROM $lead_detail_table left outer join wp_rg_lead_detail_long on  wp_rg_lead_detail_long.lead_detail_id = wp_rg_lead_detail.id WHERE lead_id=%d", $current_entry_id));

  // new lead
  $user_id    = $current_user && $current_user->ID ? $current_user->ID : 'NULL';
  $user_agent = GFCommon::truncate_url($_SERVER["HTTP_USER_AGENT"], 250);
  $currency   = GFCommon::get_currency();
  $source_url = GFCommon::truncate_url(RGFormsModel::get_current_page_url(), 200);
  $wpdb->query($wpdb->prepare("INSERT INTO $lead_table(form_id, ip, source_url, date_created, user_agent, currency, created_by) VALUES(%d, %s, %s, utc_timestamp(), %s, %s, {$user_id})", $form_change, RGFormsModel::get_ip(), $source_url, $user_agent, $currency));
  $lead_id    = $wpdb->insert_id;
  echo 'Entry '.$lead_id.' created in Form '.$form_change;

  //add a note to the new entry
  $results=mf_add_note( $lead_id, 'Copied Entry ID:'.$current_entry_id.' into form '.$form_change.'. New Entry ID ='.$lead_id);

  foreach($current_fields as $row){
    $fieldValue = ($row->field_number != 303? $row->value: 'Proposed');

    $wpdb->query($wpdb->prepare("INSERT INTO $lead_detail_table(lead_id, form_id, field_number, value) VALUES(%d, %s, %s, %s)",
            $lead_id, $form_change, $row->field_number, $fieldValue));

    //if detail long is set, add row for new record

    if($row->long_detail != 'NULL'){
      $lead_detail_id = $wpdb->insert_id;

      $wpdb->query($wpdb->prepare("INSERT INTO wp_rg_lead_detail_long(lead_detail_id, value) VALUES(%d, %s)",
            $lead_detail_id, $row->long_detail));
    }
  }

  //create RMT and maker/entity tables
  $entry    = GFAPI::get_entry($lead_id);
  $form_id  = $entry['form_id'];
  $form     = GFAPI::get_form($form_id);

  $result = GFRMTHELPER::gravityforms_makerInfo($entry,$form);
}

/* Modify Set Entry Schedule */
function set_entry_schedule($lead,$form){
  $entry_id              = $lead['id'];
	$entry_schedule_start  = (isset($_POST['datetimepickerstart'])   ? $_POST['datetimepickerstart']   : '');
	$entry_schedule_end    = (isset($_POST['datetimepickerend'])     ? $_POST['datetimepickerend']     : '');

  //location fields
  $entry_location_subarea_change = (isset($_POST['entry_location_subarea_change']) ? $_POST['entry_location_subarea_change'] : '');

	$form_id=$lead['form_id'];

  //set the location
  $location_id = 'NULL';
  if($entry_location_subarea_change!='none'){
    set_entry_location($lead,$form,$location_id);
  }

	if($entry_schedule_start!='' && $entry_schedule_end!=''){
    $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_errno) {
      echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $insert_query = sprintf("INSERT INTO `wp_mf_schedule` (`entry_id`, location_id, `faire`, `start_dt`, `end_dt`)
      SELECT $entry_id,$location_id,wp_mf_faire.faire,'$entry_schedule_start', '$entry_schedule_end'
        from wp_mf_faire where find_in_set($form_id,form_ids) > 0");

    //MySqli Insert Query
    $insert_row = $mysqli->query($insert_query);
    if($insert_row){
      echo 'Success! <br />';
    }else{
      echo ('Error :'.$insert_query.':('. $mysqli->errno .') '. $mysqli->error);
    };
  }
}

/* Modify Set Entry Location */
function set_entry_location($lead,$form,&$location_id=''){
	$entry_schedule_change      = $_POST['entry_location_subarea_change'];
	$entry_info_entry_id        = $lead['id'];
	$update_entry_location_code = $_POST['update_entry_location_code'];

	//$form_id=$lead['form_id'];

	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

  $insert_query = "INSERT INTO `wp_mf_location`(`entry_id`, `subarea_id`, `location`, `location_element_id`) "
                . " VALUES ($entry_info_entry_id,$entry_schedule_change,'$update_entry_location_code',3)";
	//MySqli Insert Query
	$insert_row = $mysqli->query($insert_query);
	if($insert_row){
		echo 'Success! <br />';
	}else{
		echo ('Error :'.$insert_query.':('. $mysqli->errno .') '. $mysqli->error);
	};
  $location_id = $mysqli->insert_id;
}

/* Delete entry schedule */
function delete_entry_schedule($lead,$form){
  global $wpdb;

	$delete_entry_schedule = (isset($_POST['delete_schedule_id']) ? implode(',',($_POST['delete_schedule_id']))    : '');
  $delete_entry_location = (isset($_POST['delete_location_id']) ? implode(',',($_POST['delete_location_id'])) : '');

	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

  //delete schedule and location
  if (!empty($delete_entry_schedule)){
    //delete from schedule and location table
    $delete_query =  "DELETE `wp_mf_schedule`, `wp_mf_location`
                        FROM `wp_mf_schedule`, `wp_mf_location`
                       WHERE wp_mf_schedule.ID IN ($delete_entry_schedule) and location_id=wp_mf_location.id";
    $wpdb->get_results($delete_query);
  }

  //delete location only
	if (!empty($delete_entry_location)){
    //delete from schedule and location table
    $delete_query =  "DELETE FROM `wp_mf_location` WHERE wp_mf_location.ID IN ($delete_entry_location)";
    $wpdb->get_results($delete_query);
  }
}

function delete_note_sidebar($notes){
    RGFormsModel::delete_notes( $notes);
}