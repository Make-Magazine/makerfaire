<?php
/* Side bar Layout */
add_action("gform_entry_detail_sidebar_before", "add_sidebar_sections", 10, 2);
function add_sidebar_sections($form, $lead) {
  $mode = empty($_POST['screen_mode']) ? 'view' : $_POST['screen_mode'];
  //the form is being pulled from the &id parameter in the url.  if they change the lid parameter in the url but not the id, the form object will be wrong here
  $form = GFAPI::get_form($lead['form_id']);
  $sidebar  = '<div id="side-sortables" class="meta-box-sortables ui-sortable">';
  $sidebar .= display_entry_info_box($form, $lead);
  if ($mode == 'view') {
    $sidebar .= display_entry_rating_box($form, $lead);
    if (isset($form['form_type']) && $form['form_type'] != 'Default') {
      $sidebar .= display_entry_fee_mgmt_box($form, $lead);
      $sidebar .= display_exhibit_type_box($form, $lead);
      $sidebar .= display_final_wknd_box($form, $lead);
    }

    $sidebar .= display_entry_notes_box($form, $lead);
    if (isset($form['form_type']) && $form['form_type'] != 'Default') {
      $sidebar .= display_flags_prelim_locs($form, $lead);
      $sidebar .= display_sched_loc_box($form, $lead);
    }

    //get list of forms
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM `wp_gf_form` where is_active = 1 and is_trash = 0");
    $formList = array();
    foreach ($results as $formObj) {
      $formList[] = array('id' => $formObj->id, 'title' => $formObj->title);
    }

    if (isset($form['form_type']) && $form['form_type'] != 'Default') {
      $sidebar .= display_form_change_box($form, $lead, $formList);
      $sidebar .= display_dupCopy_entry_box($form, $lead, $formList);
    }
    $sidebar .= display_send_conf_box($form, $lead);
  }
  $sidebar .= '</div>';
  echo $sidebar;
}

function addExpandBox($data, $title, $boxID, $boxClass = '') {
  return '<div id="' . $boxID  . '" class="postbox ' . $boxClass . '">'
    . '<div class="postbox-header">
					<h2 class="hndle ui-sortable-handle" style="flex-wrap: wrap; line-height: inherit;">' . $title . '</h2>'
    . '  	<div class="handle-actions hide-if-no-js">
						<button type="button" class="handle-order-higher" aria-disabled="false" aria-describedby="print-handle-order-higher-description"><span class="screen-reader-text">Move up</span><span class="order-higher-indicator" aria-hidden="true"></span></button>
						<span class="hidden" id="submitdiv-handle-order-higher-description">Move ' . $title . ' box up</span>
						<button type="button" class="handle-order-lower" aria-disabled="false" aria-describedby="submitdiv-handle-order-lower-description">
							<span class="screen-reader-text">Move down</span>
							<span class="order-lower-indicator" aria-hidden="true"></span>
						</button>
						<span class="hidden" id="submitdiv-handle-order-lower-description">Move ' . $title . ' box down</span>
						<button type="button" class="handlediv" aria-expanded="true">
							<span class="screen-reader-text">Toggle panel: ' . $title . '</span>
							<span class="toggle-indicator" aria-hidden="true"></span>
						</button>
				 	</div>
			 	</div> <!-- close .postbox-header --> 	
	          	 <div class="inside">' .
    $data . '
	             </div> <!-- close .inside -->
            </div><!-- close postbox -->';
}

function display_entry_info_box($form, $lead) {
  $mode       = empty($_POST['screen_mode'])  ? 'view' : $_POST['screen_mode'];
  $street     = (isset($lead['101.1'])          ? $lead['101.1'] : '');
  $street2    = (!empty($lead["101.2"]))        ? $lead["101.2"] . '<br />' : '';
  $city       = (isset($lead["101.3"])          ? $lead["101.3"] : '');
  $state      = (isset($lead["101.4"])          ? $lead["101.4"] : '');
  $zip        = (isset($lead["101.5"])          ? $lead["101.5"] : '');
  $country    = (isset($lead["101.6"])          ? $lead["101.6"] : '');
  $email      = (isset($lead["98"])             ? $lead["98"] : '');
  $phone      = (isset($lead["99"])             ? $lead["99"] : '');
  $phonetype  = (isset($lead["148"])            ? $lead["148"] : '');

  $master_entry_id = '';
  if (isset($form['master_form_id']) && $form['master_form_id'] != '') {
    $master_entry_id  = (isset($lead["master_entry_id"]) ? $lead["master_entry_id"] : '');
    $master_entry_link = '/wp-admin/admin.php?page=gf_entries&view=entry&id=' . $form['master_form_id'] . '&lid=' . $master_entry_id;
  }
  $resource_display = '';
  $resource_displayText = '';
  if ($mode == 'view' && (isset($form['form_type']) && $form['form_type'] != 'Default')) {
    $resource_display = mf_sidebar_disp_meta_field($form['id'], $lead, 'res_status') . mf_sidebar_disp_meta_field($form['id'], $lead, 'res_assign');
    $resource_displayText = '<small>Change a selection above to update entry</small><hr /> ';
  }

  $return =
    '<table width="100%" class="entry-status">' .
    mf_sidebar_entry_status($form, $lead) .
    '<tr><td colspan="2"><hr /></td></tr>' . $resource_display .
    '</table>' .
    $resource_displayText .
    'Contact: 
		<div style="padding:5px">' . (isset($lead['96.3']) ? $lead['96.3'] : '') . ' ' . (isset($lead['96.6']) ? $lead['96.6'] : '') . '<br />' .
    $street  . '<br />' .
    $street2 . '<br />' .
    $city    . ', ' . $state . '  ' . $zip . '<br />' .
    $country . '<br />
        	<a href="mailto:' . $email . '">' . $email . '</a><br />' .
    $phonetype . ':  ' . $phone . '<br />
      	</div>' .
    __('Filled out: ', 'gravityforms') . esc_html(GFCommon::format_date($lead['date_created'], false, 'Y/m/d')) . '<br /><br/>'
    . do_action('gform_entry_info', $form['id'], $lead)
    . ($master_entry_id != '' ? 'Master Entry ID: <a href="' . $master_entry_link . '">' . $master_entry_id . '</a>' : '') .
    '<div id="delete-action" style="float:none;">';
  switch ($lead['status']) {
    case 'spam':
      if (GFCommon::spam_enabled($form['id'])) {
        $return .= '<a onclick="jQuery(\'#action\').val(\'unspam\'); jQuery(\'#entry_form\').submit()" href="#">' . __('Not Spam', 'gravityforms') . '</a>';
        $return .= GFCommon::current_user_can_any('gravityforms_delete_entries') ? '|' : '';
      }
      if (GFCommon::current_user_can_any('gravityforms_delete_entries')) {
        $return .= "
            <a class=\"submitdelete deletion\" onclick=\"
            if ( confirm('" . __(';You are about to delete this entry. \'Cancel\' to stop, \'OK\' to delete.', 'gravityforms') . "') ) {
              jQuery('#action').val('delete');
              jQuery('#entry_form').submit();
              return true;
            }
            return false;\" href=\"#\">" . __('Delete Permanently', 'gravityforms') . "</a>";
      }
      break;
    case 'trash':
      $return .= "<a onclick=\"jQuery('#action').val('restore'); jQuery('#entry_form').submit()\" href=\"#\">" . __('Restore', 'gravityforms') . "</a>";

      if (GFCommon::current_user_can_any('gravityforms_delete_entries')) {
        $return .= "| <a class=\"submitdelete deletion\"
              onclick=\"if ( confirm('" . __('You are about to delete this entry. \'Cancel\' to stop, \'OK\' to delete.', 'gravityforms') . "') ) {"
          . "jQuery('#action').val('delete'); "
          . "jQuery('#entry_form').submit(); return true;} return false;\"
              href=\"#\">" . __('Delete Permanently', 'gravityforms') . "</a>";
      }
      break;

    default:
      if (GFCommon::current_user_can_any('gravityforms_delete_entries')) {
        $return .= "<a class=\"submitdelete deletion\" onclick=\"jQuery('#action').val('trash'); jQuery('#entry_form').submit()\" href=\"#\">" .
          __('Move to Trash', 'gravityforms') . "</a> " . (GFCommon::spam_enabled($form['id']) ? '|' : '');
      }
      if (GFCommon::spam_enabled($form['id'])) {
        $return .= "<a class=\"submitdelete deletion\" onclick=\"jQuery('#action').val('spam'); jQuery('#entry_form').submit()\" href=\"#\">" . __('Mark as Spam', 'gravityforms') . "</a>";
      }
  } //end switch
  $return .= "</div><!-- close #delete-action -->";
  if (GFCommon::current_user_can_any('gravityforms_edit_entries') && $lead['status'] != 'trash') {
    $button_text      = $mode == 'view' ? __('Edit', 'gravityforms') : __('Update', 'gravityforms');
    $disabled         = $mode == 'view' ? '' : ' disabled="disabled" ';
    $update_button_id = $mode == 'view' ? 'gform_edit_button' : 'gform_update_button';
    $button_click     = $mode == 'view' ? "jQuery('#screen_mode').val('edit');" : "jQuery('#action').val('update'); jQuery('#screen_mode').val('view');";
    $update_button    = '<input id="' . $update_button_id . '" ' . $disabled . ' class="button button-large button-primary" type="submit" tabindex="4" value="' . $button_text . '" name="save" onclick="' . $button_click . '"/>';
    $return .= apply_filters('gform_entrydetail_update_button', $update_button);
    if ($mode == 'edit') {
      $return .= '&nbsp;&nbsp;<input class="button button-large" type="submit" tabindex="5" value="' . __('Cancel', 'gravityforms') . '" name="cancel" onclick="jQuery(\'#screen_mode\').val(\'view\');"/>';
    }
  }

  $title = 'Entry Information';
  return addExpandBox($return, $title, 'entry-info');
}

function display_entry_rating_box($form, $lead) {
  /* Ratings Sidebar Area */
  global $wpdb;
  // Retrieve any ratings
  $entry_id       = $lead['id'];
  $sql            = "SELECT user_id, rating, ratingDate FROM `wp_mf_lead_rating` where entry_id = " . $entry_id;

  $ratingTotal    = 0;
  $ratingNum      = 0;
  $ratingResults  = '';
  $user_ID        = get_current_user_id();
  $currRating     = '';

  foreach ($wpdb->get_results($sql) as $row) {
    $user = get_userdata($row->user_id);

    //don't display current user in the list of rankings
    if ($user_ID != $row->user_id) {
      $ratingResults .= '<tr><td style="text-align: center;">' . $row->rating . '</td><td>' . $user->display_name . '</td><td class="alignright">' . date("m-d-Y", strtotime($row->ratingDate)) . '</td></tr>';
    } else {
      $currRating = $row->rating;
    }
    $ratingTotal += $row->rating;
    $ratingNum++;
  }

  $ratingAvg = ($ratingNum != 0 ? round($ratingTotal / $ratingNum) : 0);
  $return =
    '<div class="entryRating">
      <span class="star-rating">
        <input type="radio" name="rating" value="1" ' . ($currRating == 1 ? 'checked' : '') . '><i></i>
        <input type="radio" name="rating" value="2" ' . ($currRating == 2 ? 'checked' : '') . '><i></i>
        <input type="radio" name="rating" value="3" ' . ($currRating == 3 ? 'checked' : '') . '><i></i>
        <input type="radio" name="rating" value="4" ' . ($currRating == 4 ? 'checked' : '') . '><i></i>
        <input type="radio" name="rating" value="5" ' . ($currRating == 5 ? 'checked' : '') . '><i></i>
      </span>
      (Your Rating)<br/>
      <span id="updateMSG" style="font-size:smaller">Average Rating: ' . $ratingAvg . ' Stars from ' . $ratingNum . ' users.</span>';
  if ($ratingResults != '') {
    $return .=  '<table cellspacing="0" style="padding:10px 0">'
      . '<tr>'
      . '   <td class="entry-view-field-name">Rating</td>'
      . '   <td class="entry-view-field-name">User</td>'
      . '   <td class="entry-view-field-name">Date Rated</td>'
      . '</tr>'
      . $ratingResults
      . '</table>';
  }
  $return .=
    '</div>';
  $title = 'Entry Rating:
            <a href="#" onclick="return false;"
              data-toggle="popover" data-trigger="hover"
              data-placement="top" data-html="true"
              data-content="1 = No way<br/>2 = Low priority<br/>3 = Yes, If there’s room<br/>4 = Yes definitely<br/>5 = Hell yes">
              (?)
            </a>' . $ratingAvg . ' stars';
  return addExpandBox($return, $title, 'entry-rating');
}
function display_entry_fee_mgmt_box($form, $lead) {
  $fieldName  = 'entry_info_fee_mgmt';
  $field_id   = '442';

  $return     = field_display($lead, $form, $field_id, $fieldName);
  $return .= '<input type="button" name="update_fee_mgmt" value="Update Fee Management" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_fee_mgmt\');"/>';
  $return .= '<span class="updMsg update_fee_mgmtMsg"></span>';
  return addExpandBox($return, 'Fee Management', 'fee-mgmt');
}

/* update exhibit type */
function display_exhibit_type_box($form, $lead) {
  $fieldName  = 'entry_exhibit_type';
  $field_id   = '339';

  $return     = field_display($lead, $form, $field_id, $fieldName);
  $return .= '<input type="button" name="update_exhibit_type" value="Update Exhibit Type" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_exhibit_type\');"/>';
  $return .= '<span class="updMsg update_exhibit_typeMsg"></span>';
  return addExpandBox($return, 'Exhibit Type', 'exhibit_type');
}

/* update final weekend */
function display_final_wknd_box($form, $lead) {
  $fieldName  = 'entry_final_weekend';
  $field_id   = '879';

  $return     = field_display($lead, $form, $field_id, $fieldName);
  $return .= '<input type="button" name="update_final_weekend" value="Update Activation Days" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_final_weekend\');"/>';
  $return .= '<span class="updMsg update_final_weekendMsg"></span>';
  return addExpandBox($return, 'Activation Days', 'final_weekend');
}
function display_entry_notes_box($form, $lead) {
  /* Notes Sidebar Area */
  $notes = RGFormsModel::get_lead_notes($lead['id']);

  //getting email values
  $email_fields = GFCommon::get_email_fields($form);
  $emails = array();

  foreach ($email_fields as $email_field) {
    if (!empty($lead[$email_field->id])) {
      $emails[] = $lead[$email_field->id];
    }
  }
  //displaying notes grid
  $subject = '';
  $return = notes_sidebar_grid($notes, true, $emails, $subject);

  return addExpandBox($return, 'Notes', 'notes', 'notesbox');
}

function display_flags_prelim_locs($form, $lead) {
  $return = '';
  $mode       = empty($_POST['screen_mode'])  ? 'view' : $_POST['screen_mode'];
  /* Entry Management Sidebar Area */
  if ($mode == 'view') {
    // Create Update button for sidebar entry management
    $entry_sidebar_button = '<input type="button" name="update_management" value="Update Management" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_entry_management\');"/>';
    $msgBox = '<span class="updMsg update_entry_managementMsg"></span>';
    $return = $entry_sidebar_button . $msgBox;

    // Load flags and prelim location section

    //flags
    $return    .= '<h4><label class="detail-label">Flags:</label></h4>';
    $fieldName  = 'entry_info_flags_change';
    $field_id   = '304';
    $return    .= field_display($lead, $form, $field_id, $fieldName);

    //preliminary locations
    $return     .= '<h4><label class="detail-label">Preliminary Location:</label></h4>';
    $changeHook  = 'entry_info_location_change';
    $field_id    = '302';
    $return     .= field_display($lead, $form, $field_id, $changeHook);

    $return     .= '<textarea name="entry_location_comment" id="entry_location_comment" style="width: 100%; height: 50px; margin-bottom: 4px;" cols="" rows="">' . (isset($lead['307']) ? $lead['307'] : '') . '</textarea>';

    // Create Update button for sidebar entry management
    $return     .= $entry_sidebar_button . $msgBox;

    return addExpandBox($return, 'Flags / Preliminary Location', 'flags');
  } else {
    return '';
  }
}

function field_display($lead, $form, $field_id, $fieldName) {
  $return    = '';

  $form_id = $form['id'];
  $field     = RGFormsModel::get_field($form, $field_id);

  //is this a valid field in the form
  if ($field != NULL) {
    $value   = RGFormsModel::get_lead_field_value($lead, $field);
    $return  = mf_checkbox_display($field, $value, $form_id, $fieldName, $field_id);
  }
  return $return;
}

function display_sched_loc_box($form, $lead) {
  $return = '';
  $mode       = empty($_POST['screen_mode'])  ? 'view' : $_POST['screen_mode'];
  /* Scheduling Management Sidebar Area */
  if ($mode == 'view') {
    // Load Entry Sidebar details: schedule
    $return =  mf_sidebar_entry_schedule($form['id'], $lead);
    return addExpandBox($return, 'Schedule/Location', 'schedule-location', 'schedBox');
  } else {
    return '';
  }
}

function display_ticket_code_box($form, $lead) {
  $return = '<div class="postbox">';

  // Load Entry Sidebar details: Ticket Code (Field 308)
  $field308 = RGFormsModel::get_field($form, '308');
  $return .= '<h4><label class="detail-label">Ticket Code:</label></h4>';
  $return .= '<input name="entry_ticket_code" id="entry_ticket_code type="text" style="margin-bottom: 4px;" value="' . (isset($lead['308']) ? $lead['308'] : '') . '" />';

  // Create Update button for ticket code
  $return .= '<input type="button" name="update_ticket_code" value="Update Ticket Code" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_ticket_code\');"/>';
  $return .= '<span class="updMsg update_ticket_codeMsg"></span>';

  $return .= '</div>';
  return $return;
}

function display_form_change_box($form, $lead, $formList) {
  $output = '<select style="width:250px" name="entry_form_change">';
  foreach ($formList as $choice) {
    $selected = '';
    if ($choice['id'] == $lead['form_id']) $selected = ' selected ';
    $output .= '<option ' . $selected . ' value="' . $choice['id'] . '">' . $choice['title'] . '</option>';
  }
  $output .= '</select>';
  $output .= '<input type="button" name="change_form_id" value="Change Form" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'change_form_id\');"/><br />';
  $output .= '<span class="updMsg change_form_idMsg"></span>';

  return addExpandBox($output, 'Change Form', 'change-form');
}

function display_dupCopy_entry_box($form, $lead, $formList) {
  $title = 'Duplicate/Copy Entry ID ' . $lead['id'];
  $output = 'Into Form:<br/>';
  $output .= '<select style="width:250px" name="entry_form_copy">';
  foreach ($formList as $choice) {
    $selected = '';
    if ($choice['id'] == $lead['form_id']) $selected = ' selected ';
    $output .= '<option ' . $selected . ' value="' . $choice['id'] . '">' . $choice['title'] . '</option>';
  }
  $output .=  '</select><br/><br/>';
  $output .= '<input type="button" name="duplicate_entry_id" value="Duplicate Entry" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'duplicate_entry_id\');"/><br />';
  $output .= '<span class="updMsg duplicate_entry_idMsg"></span>';

  return addExpandBox($output, $title, 'dup-copy-entry');
}

function display_send_conf_box($form, $lead) {
  $title = 'Send Confirmation Letter';
  $output = '<div class="detail-view-print">
              <!--button to trigger send confirmation letter event -->
              <input type="button" name="send_conf_letter" value="Send Confirmation Letter" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'send_conf_letter\');"/>
              <span class="updMsg send_conf_letterMsg"></span>
          </div>';
  return addExpandBox($output, $title, 'send-conf-letter');
}

/* Notes Sidebar Grid Function */
function notes_sidebar_grid($notes, $is_editable, $emails = null, $subject = '') {
  $return = '
    <table class="widefat fixed entry-detail-notes">
      <tbody id="the-comment-list" class="list:comment">';
  $count = 0;
  $notes_count = sizeof($notes);
  foreach ($notes as $note) {
    $count++;
    $is_last = $count >= $notes_count ? true : false;
    $return .= '<tr valign="top" class="note' . $note->id . '">';
    if ($is_editable && GFCommon::current_user_can_any('gravityforms_edit_entry_notes')) {
      $return .= '<td class="check-column" scope="row" style="padding:9px 3px 0 0">
                    <input type="checkbox" value="' . $note->id . '" name="note[]" />
                  </td>';
    }
    $return .= '<td class="entry-detail-note' . ($is_last ? ' lastrow' : '') . '">';
    $class   = $note->note_type ? " gforms_note_{$note->note_type}" : '';
    $return .= '<div style="margin-top: 4px;">
                  <div class="note-avatar">' . apply_filters('gform_notes_avatar', get_avatar($note->user_id, 48), $note) .
      '</div>
                <h6 class="note-author">' . esc_html($note->user_name) . '</h6>
              <p class="note-email">
                <a href="mailto:' . esc_attr($note->user_email) . '">' . esc_html($note->user_email) . '</a><br />' .
      __('added on ', 'gravityforms') .
      esc_html(GFCommon::format_date($note->date_created, false)) .
      '</p>
            </div>
            <div class="detail-note-content' . $class . '">' .
      html_entity_decode($note->value) .
      '</div>
          </td>
        </tr>';
  }
  $return .=
    '</tbody>
    </table>';

  if (sizeof($notes) > 0 && $is_editable && GFCommon::current_user_can_any('gravityforms_edit_entry_notes')) {
    $return .= '<input type="button" name="delete_note_sidebar" value="Delete Selected Note(s)" class="button" style="width:100%;padding-bottom:2px;" onclick="updateMgmt(\'delete_note_sidebar\');">
    <span class="updMsg delete_note_sidebarMsg"></span>';
  }
  return $return;
}

function wpse27856_set_content_type() {
  return "text/html";
}

//creates box to update the ticket code field 308
function mf_sidebar_entry_ticket($form_id, $lead) {
}

function mf_sidebar_entry_schedule($form_id, $lead) {
  global $wpdb;
  $output  = '<link rel="stylesheet" type="text/css" href="' . get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css"/>';
  $output .= display_schedule($form_id, $lead);
  // Set up the Add to Schedule Section
  $output .= '<h4 class="topBorder">Add New:</h4>';

  $locSql = "SELECT area.area, subarea.subarea, subarea.nicename, subarea.id as subarea_id
                FROM wp_mf_faire faire, wp_mf_faire_area area, wp_mf_faire_subarea subarea
                where FIND_IN_SET($form_id,faire.form_ids) and faire.ID = area.faire_id and subarea.area_id = area.ID
                order by area,subarea";

  $output .= 'Area - Subarea <select style="max-width:100%" name="entry_location_subarea_change" id="entry_location_subarea_change">';
  $output .= '<option value="none">None</option>';
  $subAreaArr = array();
  foreach ($wpdb->get_results($locSql, ARRAY_A) as $row) {
    $area_option = (strlen($row['area']) > 0) ? ' (' . $row['area'] . ')' : '';
    $subarea_option = ($row['subarea'] != '' ? $row['subarea'] : $row['subarea']);
    $output .= '<option value="' . $row['subarea_id'] . '">' . $row['area'] . ' - ' . $subarea_option . '</option>';
    $subAreaArr[] = $row['subarea_id'];
  }
  $output .= "</select><br />";

  //create unique array of subareas
  array_unique($subAreaArr);
  $subAreaList = implode(",", $subAreaArr);

  $sql = "select distinct(location) as location, subarea_id from wp_mf_location";
  if ($subAreaList != '') {
    $sql .= " where subarea_id in(" . $subAreaList . ") and location!=''";
  }

  $locArr = array();
  foreach ($wpdb->get_results($sql, ARRAY_A) as $row) {
    $locArr[$row['subarea_id']][] = $row['location'];
  }

  $output .= '<script>
                  var locationObj = ' . json_encode($locArr) . '
                </script>';

  //create dropdown of current locations for selected subarea
  $output .= 'Booth ID: (optional)<br/>';
  $output .= '<select id="locationSel"><option>Select Area - Subarea above</option></select><br/>';
  $output .= '<input type="text" name="update_entry_location_code" style="display:none" id="update_entry_location_code" /><br/>';

  // Load Fields to show on entry info
  $output .= '<br/>';
  $output .= '<input type="checkbox" id="dispSchedSect" value="yes" />  Add Date/Time & Type (optional)
                <!-- Only show when #dispSchedSect is selected-->
                <div id="schedSect" style="display:none">
                  <label for="schedAdd">Start/End:</label>
                  <div class="clear"></div>
                  <div style="padding:10px 0;width:40px;float:left">Start: </div>
                  <div style="float:left"><input type="text" value="" name="datetimepickerstart" id="datetimepickerstart"></div>
                  <div class="clear" style="padding:10px 0;width:40px;float:left">End:</div>
                  <div style="float:left"><input type="text" value="" name="datetimepickerend" id="datetimepickerend"></div>
                  <div class="clear"></div>
                  <label for="typeSel">Type: </label>
                  <select id="typeSel">
                    <option value="">Please Select</option>
                    <option value="workshop">Workshop</option>
                    <option value="talk">Talk</option>
                    <option value="performance">Performance</option>
                    <option value="demo">Demo</option>
                  </select>
                </div>';
  // Create Update button for sidebar entry management
  $output .=  '<input type="button" name="update_entry_schedule" value="Add Location" class="button" style="width:auto;padding-bottom:2px; margin: 10px 0;" onclick="updateMgmt(\'update_entry_schedule\');"/><br />
                 <span class="updMsg update_entry_scheduleMsg"></span>';
  return $output;
}

function display_schedule($form_id, $lead, $section = 'sidebar') {
  global $wpdb;
  //first, let's display any schedules already entered for this entry
  $entry_id = $lead['id'];
  $sql = "select `wp_mf_schedule`.`ID` as schedule_id, `wp_mf_schedule`.`entry_id`,  `wp_mf_schedule`.type,
                  `wp_mf_schedule`.`start_dt`, `wp_mf_schedule`.`end_dt`, 
                  `wp_mf_schedule`.`day`, wp_mf_faire.time_zone, `wp_mf_faire`.`faire`, 
                  location.ID as location_id, location.location,
                  area.area, subarea.subarea,                                    
                  subarea.ID as subarea_id

          from wp_mf_location location
          left outer join wp_mf_schedule on `wp_mf_schedule`.`entry_id` = location.entry_id and wp_mf_schedule.location_id = location.ID,
          wp_mf_faire_subarea subarea, wp_mf_faire_area area,wp_mf_faire

          where location.entry_id=" . $entry_id . "
            and FIND_IN_SET(" . $form_id . ",wp_mf_faire.form_ids)
            and location.subarea_id = subarea.ID
            and subarea.area_id = area.ID
          order by area ASC, subarea ASC, start_dt ASC";

  $scheduleArr = array();
  
  foreach ($wpdb->get_results($sql, ARRAY_A) as $row) {
    $schedData   = array();
    //order entries by subarea(stage), then date
    $stage = ($row['subarea'] != NULL ? $row['area'] . ' - ' . $row['subarea'] : '');
    if ($row['location'] != '')    $stage .= ' (' . $row['location'] . ')';
    $start_dt = ($row['start_dt'] != NULL ? strtotime($row['start_dt'])  : '');
    $end_dt   = ($row['end_dt']   != NULL ? strtotime($row['end_dt'])    : '');
    $schedule_id = ($row['schedule_id'] != NULL ? (int) $row['schedule_id'] : '');
    $date     = ($start_dt != '' ? date("n/j/y", $start_dt) : '');
    $timeZone   = $row['time_zone'];
    $subarea_id = $row['subarea_id'];
    $type       = $row['type'];

    //build array
    $schedData['location'] = $row['location_id'];
    $schedData['stage']    = $stage;

    if ($date != '') {
      $schedData['schedule'][$date][$schedule_id] = array('start_dt' => $start_dt, 'end_dt' => $end_dt, 'timeZone' => $timeZone, 'type' => $type);
    }
    $schedules[]=$schedData;
  }

  //make sure there is data to display
  if ($wpdb->num_rows != 0) {
    $output = '<div id="locationList">';
    //let's loop thru the schedule array now
    foreach ($schedules as $data) {
      $location_id = $data['location'];
      $stage       = $data['stage'];
      $output     .= '<div class="stageName">' . $stage . '</div>';

      $scheduleArr = (isset($data['schedule']) ? $data['schedule'] : '');
      if (is_array($scheduleArr)) {
        foreach ($scheduleArr as $date => $schedule) {
          if ($date != '') {
            $output .= '<div><span class="schedDate">' . date('l n/j/y', strtotime($date)) . '</span>';
            $output .= '<div class="schedOuter">';
            foreach ($schedule as $schedule_id => $schedData) {
              $start_dt   = $schedData['start_dt'];
              $end_dt     = $schedData['end_dt'];
              $db_tz      = $schedData['timeZone'];

              //set time zone for faire
              $dateTime = new DateTime();
              $dateTime->setTimeZone(new DateTimeZone($db_tz));
              $timeZone = $dateTime->format('T');
              if ($section != 'summary') {
                $output .= '<input type="checkbox" value="' . $schedule_id . '" name="delete_schedule_id[]"></input>';
              }
              $output .= '<div class="schedInfo">';
              $output .= '  <span>' . date("g:i A", $start_dt) . ' - ' . date("g:i A", $end_dt) . ' (' . $timeZone . ')</span>';
              $output .= '  <div class="innerInfo">Type: ' . $schedData['type'] . '</div>';
              $output .= '</div>';
            }
            $output .= '</div></div>';
          }
        }
      } else { //if there is no schedule data
        //location only display checkbox to delete
        if ($section != 'summary') {
          $output .= '<input type="checkbox" value="' . $location_id . '" name="delete_location_id[]" /> '
            .  '<span class="schedDate">Remove Location</span>';
        }
        $output .= '<div class="clear"></div>';
      }      
    }

    if ($section != 'summary') {
      $output .= '<br/>';
      $entry_delete_button = '<input type="button" name="delete_entry_schedule[]" value="Delete Selected" class="button"
                                    style="width:auto;padding-bottom:2px;"
                                   onclick="updateMgmt(\'delete_entry_schedule\');"/><br />';
      $updMsg  = '<span class="updMsg delete_entry_scheduleMsg"></span>';
      $output .= $entry_delete_button . $updMsg;
      $output .= '<br/>';
    }
    
    $output .= '</div>';
    return $output;
  }
}

function sortFlagsByLabel($a, $b) {
  return strnatcmp($a['text'], $b['text']);
};

function  mf_checkbox_display($field, $value, $form_id, $fieldName, $field_id, $ability='edit') {
  $output = '';

  if ($field->type != 'checkbox') {
    return 'not a checkbox field';
  }

  //field with combined choices and inputs
  $mergedChoicesAndInputs = $choicesArray = array();

  //we need both the label and the value set for each choice, so we need to combine these two
  $inputs   = (isset($field->inputs) ? $field->inputs : '');
  $choices  = (isset($field->choices) ? $field->choices : '');
  if ($inputs == '') {
    return 'error in inputs';
  } elseif ($choices == '') {
    return 'error in choices';
  }

  foreach ($choices as $chItem) {
    foreach ($inputs as $inItem) {
      if (in_array(htmlspecialchars_decode($chItem["text"]), $inItem)) {
        $chItem["id"] = $inItem['id'];
        $mergedChoicesAndInputs[] = $chItem;
      }
    }
  }

  //sort flags alphabetically                
  if ($field_id === "304") {
    usort($mergedChoicesAndInputs, "sortFlagsByLabel");
  }

  //loop through the available choices and built the output  
  foreach ($mergedChoicesAndInputs as $choice) {
    $input_id = $choice["id"];

    $choiceValue = (!empty($choice['value']) ? $choice['value'] : $choice['text']);
    if (is_array($value)  && in_array($choiceValue, $value)) {
      $checked = "checked='checked'";
    } elseif (!is_array($value) && RGFormsModel::choice_value_match($field, $choice, $value)) {      
      $checked = "checked='checked'";
    } else {
      $checked = '';
    }

    $choice_value = $choice['value'];
    if ($field->enablePrice) {
      $price = rgempty('price', $choice) ? 0 : GFCommon::to_number(rgar($choice, 'price'));
      $choice_value .= '|' . $price;
    }
    $choice_value  = esc_attr($choice_value);

    
    if ($ability == 'edit') {
      $output .= '<input type="checkbox" ' . $checked . ' name="' . $fieldName . '[]" style="margin: 3px;" value="' . $input_id . '_' . $choice_value . '" />' . $choice['text'] . ' <br />';
    } elseif ($checked) {
      $output .= $choice['text'] . ' <br />';
    }
  }

  return $output;
}

function mf_sidebar_disp_meta_field($form_id, $lead, $meta_key = '') {
  //get current value if set
  $metaValue = '';
  if ($meta_key != '') {
    $metaValue = gform_get_meta($lead['id'], $meta_key);
  }
  $output  = '';
  //build input
  $meta = GFFormsModel::get_entry_meta(array($form_id));
  if (isset($meta[$meta_key])) {
    $output .= '<tr>';
    $output .= '  <td><label>' . $meta[$meta_key]['label'] . ':&nbsp;</label></td>';
    if (isset($meta[$meta_key]['filter']['choices'])) {
      $choices = $meta[$meta_key]['filter']['choices'];
      $output .= '<td><select class="metafield" name="' . $meta_key . '" id="' . $meta_key . '">';
      if ($metaValue == '')  $output .= '<option value=""></option>';
      foreach ($choices as $option) {
        $output .= '<option value="' . $option['value'] . '"' . ($metaValue == $option['value'] ? ' selected ' : '') . '>' . $option['text'] . '</option>';
      }
      $output .= '</select></td>';
    } else { //build as regular input text
      $output .= '<td><input class="metafield"  name="' . $meta_key . '" id="' . $meta_key . '" value="' . $metaValue . '" /></td>';
    }
    $output .= '  <td><span class="updMsg" id="' . $meta_key . 'Status"></span></td>'; //updating progress field
    $output .= '</tr>';
  }
  return $output;
}

function mf_sidebar_entry_status($form, $lead) {
  $output  = '<tr>';
  if (current_user_can('update_entry_status')) {
    $output .= '  <td>' .
      '<input type="hidden" name="entry_info_entry_id" value="' . $lead['id'] . '" />' .
      '<label class="detail-label" for="entry_info_status_change">Entry Status:&nbsp;</label>' .
      '  </td>';
    // Load Fields to show on entry info
    $field303 = RGFormsModel::get_field($form, '303');
    $output .= '  <td>';
    //$output .= '    <select name="entry_info_status_change" onchange="updateMgmt(\'update_entry_status\');">';
    $output .= '    <select id="entryStatus_' . $lead['id'] . '" name="entry_info_status_change">';
    if (isset($field303['choices'])) {
      foreach ($field303['choices'] as $choice) {
        $selected = '';
        if ($lead[$field303['id']] == $choice['text']) $selected = ' selected ';
        $output .= '<option ' . $selected . ' value="' . $choice['text'] . '">' . $choice['text'] . '</option>';
      }
    }
    $output .= '    </select></td>'
      . '</tr><tr><td>&nbsp;</td>';
    $output .= '<td><input type="button" name="update_management" value="Save Status" class="btn btn-danger" onclick="updateMgmt(\'update_entry_status\', ' . $lead['id'] . ');" /></td>';
    $output .= '<td><span class="updMsg update_entry_statusMsg" id="updStatusMsg' . $lead['id'] . '"></span></td>';
  } else {
    $output .= '<td><label class="detail-label" for="entry_info_status_change">Status:&nbsp;</label></td>';
    $output .= '<td>' . $lead[303] . '</td>';
  }
  $output  .= '</tr>';
  return $output;
}
