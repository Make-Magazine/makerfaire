<?php
/* Side bar Layout */
add_action("gform_entry_detail_sidebar_before", "add_sidebar_sections", 10,2);
function add_sidebar_sections($form, $lead) {
  $sidebar  = '';
  $sidebar .= display_entry_info_box($form, $lead);
  $sidebar .= display_entry_rating_box($form, $lead);
  $sidebar .= display_entry_notes_box($form, $lead);
  $sidebar .= display_flags_prelim_locs($form, $lead);
  $sidebar .= display_sched_loc_box($form, $lead);
  $sidebar .= display_ticket_code_box($form, $lead);
  $sidebar .= display_form_change_box($form, $lead);
  $sidebar .= display_dupCopy_entry_box($form, $lead);
  $sidebar .= display_send_conf_box($form, $lead);
  echo $sidebar;
}

function display_entry_info_box($form, $lead) {
  $mode       = empty( $_POST['screen_mode'] )  ? 'view' : $_POST['screen_mode'];
	$street     = (isset($lead['101.1'])          ? $lead['101.1']:'');
	$street2    = (!empty($lead["101.2"]))        ? $lead["101.2"].'<br />' : '' ;
	$city       = (isset($lead["101.3"])          ? $lead["101.3"]:'');
	$state      = (isset($lead["101.4"])          ? $lead["101.4"]:'');
	$zip        = (isset($lead["101.5"])          ? $lead["101.5"]:'');
	$country    = (isset($lead["101.6"])          ? $lead["101.6"]:'');
	$email      = (isset($lead["98"])             ? $lead["98"]:'');
	$phone      = (isset($lead["99"])             ? $lead["99"]:'');
	$phonetype  = (isset($lead["148"])            ? $lead["148"]:'');
  $return =
   '<div id="infoboxdiv" class="postbox">
      <div id="minor-publishing">
        <table width="100%" class="entry-status">'.
          mf_sidebar_entry_status( $form['id'], $lead ) .
          mf_sidebar_disp_meta_field($form['id'], $lead, 'res_status' ) .
          mf_sidebar_disp_meta_field($form['id'], $lead, 'res_assign' ) .
       '</table>
        <small>Change a selection above to update entry</small>
        <hr />
        Contact:<div style="padding:5px">'. (isset($lead['96.3'])?$lead['96.3']:'').' '. (isset($lead['96.6'])?$lead['96.6']:'').'<br />'.
        $street  .'<br />'.
        $street2 .'<br />'.
        $city    .', '. $state.'  '. $zip.'<br />'.
        $country .'<br />
        <a href="mailto:'. $email.'">'. $email.'</a><br />'.
        $phonetype.':  '. $phone.'<br />
      </div>'.
      __( 'Filled out: ', 'gravityforms' ). esc_html( GFCommon::format_date( $lead['date_created'], false, 'Y/m/d' ) ) .'<br /><br/>
      '. do_action( 'gform_entry_info', $form['id'], $lead ) .
   '</div>
    <div id="delete-action" style="float:none;">';
      switch ( $lead['status'] ) {
        case 'spam' :
          if ( GFCommon::spam_enabled( $form['id'] ) ) {
            $return .= '<a onclick="jQuery(\'#action\').val(\'unspam\'); jQuery(\'#entry_form\').submit()" href="#">'. __( 'Not Spam', 'gravityforms' ) .'</a>';
            $return .= GFCommon::current_user_can_any( 'gravityforms_delete_entries' ) ? '|' : '';
          }
          if ( GFCommon::current_user_can_any( 'gravityforms_delete_entries' ) ) {
            $return .= "
            <a class=\"submitdelete deletion\" onclick=\"
            if ( confirm('". __( ';You are about to delete this entry. \'Cancel\' to stop, \'OK\' to delete.', 'gravityforms' ) ."') ) {
              jQuery('#action').val('delete');
              jQuery('#entry_form').submit();
              return true;
            }
            return false;\" href=\"#\">". __( 'Delete Permanently', 'gravityforms' )."</a>";
          }
          break;
        case 'trash' :
          $return .= "<a onclick=\"jQuery('#action').val('restore'); jQuery('#entry_form').submit()\" href=\"#\">". __( 'Restore', 'gravityforms' ) ."</a>";

          if ( GFCommon::current_user_can_any( 'gravityforms_delete_entries' ) ) {
            $return .= "| <a class=\"submitdelete deletion\"
              onclick=\"if ( confirm('". __('You are about to delete this entry. \'Cancel\' to stop, \'OK\' to delete.', 'gravityforms' ) ."') ) {"
                    . "jQuery('#action').val('delete'); "
                    . "jQuery('#entry_form').submit(); return true;} return false;\"
              href=\"#\">". __( 'Delete Permanently', 'gravityforms' ) ."</a>";
          }
          break;

        default :
          if ( GFCommon::current_user_can_any( 'gravityforms_delete_entries' ) ) {
            $return .= "<a class=\"submitdelete deletion\" onclick=\"jQuery('#action').val('trash'); jQuery('#entry_form').submit()\" href=\"#\">".
                        __( 'Move to Trash', 'gravityforms' ) ."</a> ". (GFCommon::spam_enabled( $form['id'] ) ? '|' : '');
          }
          if ( GFCommon::spam_enabled( $form['id'] ) ) {
            $return .= "<a class=\"submitdelete deletion\" onclick=\"jQuery('#action').val('spam'); jQuery('#entry_form').submit()\" href=\"#\">". __( 'Mark as Spam', 'gravityforms' ) ."</a>";
          }
      } //end switch
      if ( GFCommon::current_user_can_any( 'gravityforms_edit_entries' ) && $lead['status'] != 'trash' ) {
        $button_text      = $mode == 'view' ? __( 'Edit', 'gravityforms' ) : __( 'Update', 'gravityforms' );
        $disabled         = $mode == 'view' ? '' : ' disabled="disabled" ';
        $update_button_id = $mode == 'view' ? 'gform_edit_button' : 'gform_update_button';
        $button_click     = $mode == 'view' ? "jQuery('#screen_mode').val('edit');" : "jQuery('#action').val('update'); jQuery('#screen_mode').val('view');";
        $update_button    = '<input id="' . $update_button_id . '" ' . $disabled . ' class="button button-large button-primary" type="submit" tabindex="4" value="' . $button_text . '" name="save" onclick="' . $button_click . '"/>';
        $return .= apply_filters( 'gform_entrydetail_update_button', $update_button );
        if ( $mode == 'edit' ) {
          $return .= '&nbsp;&nbsp;<input class="button button-large" type="submit" tabindex="5" value="' . __( 'Cancel', 'gravityforms' ) . '" name="cancel" onclick="jQuery(\'#screen_mode\').val(\'view\');"/>';
        }
      }
  $return .= "
      </div>
    </div>";
  return $return;
}

function display_entry_rating_box($form, $lead) {
  $return = '';
  /* Ratings Sidebar Area */
  global $wpdb;
  // Retrieve any ratings
  $entry_id       = $lead['id'];
  $sql            = "SELECT user_id, rating, ratingDate FROM `wp_rg_lead_rating` where entry_id = ".$entry_id;
  $ratingTotal    = 0;
  $ratingNum      = 0;
  $ratingResults  = '';
  $user_ID        = get_current_user_id();
  $currRating     = '';

  foreach($wpdb->get_results($sql) as $row){
    $user = get_userdata( $row->user_id );

    //don't display current user in the list of rankings
    if($user_ID!=$row->user_id){
      $ratingResults .= '<tr><td style="text-align: center;">'.$row->rating.'</td><td>'.$user->display_name.'</td><td class="alignright">'.date("m-d-Y", strtotime($row->ratingDate)).'</td></tr>';
    }else{
      $currRating = $row->rating;
    }
    $ratingTotal += $row->rating;
    $ratingNum++;
  }

  $ratingAvg = ($ratingNum!=0?round($ratingTotal/$ratingNum):0);
  $return .=
    '<div class="postbox">
    <h3>Entry Rating:
      <a href="#" onclick="return false;"
        data-toggle="popover" data-trigger="hover"
        data-placement="top" data-html="true"
        data-content="1 = No way<br/>2 = Low priority<br/>3 = Yes, If there’s room<br/>4 = Yes definitely<br/>5 = Hell yes">
        (?)
      </a>'. $ratingAvg .' stars
    </h3>

    <div class="entryRating inside">
      <span class="star-rating">
        <input type="radio" name="rating" value="1" '. ($currRating==1?'checked':'').'><i></i>
        <input type="radio" name="rating" value="2" '. ($currRating==2?'checked':'').'><i></i>
        <input type="radio" name="rating" value="3" '. ($currRating==3?'checked':'').'><i></i>
        <input type="radio" name="rating" value="4" '. ($currRating==4?'checked':'').'><i></i>
        <input type="radio" name="rating" value="5" '. ($currRating==5?'checked':'').'><i></i>
      </span>
      (Your Rating)<br/>
      <span id="updateMSG" style="font-size:smaller">Average Rating: '. $ratingAvg.' Stars from '. $ratingNum.' users.</span>';
      if($ratingResults!=''){
        $return .=  '<table cellspacing="0" style="padding:10px 0">'
                    . '<tr>'
                    . '   <td class="entry-view-field-name">Rating</td>'
                    . '   <td class="entry-view-field-name">User</td>'
                    . '   <td class="entry-view-field-name">Date Rated</td>'
                    . '</tr>'.
                    '</table>'.$ratingResults;
      }
      $return .=
    '</div>
  </div>';
  return $return;
}

function display_entry_notes_box($form, $lead) {
  /* Notes Sidebar Area */
  $return = '
  <div class="postbox">
    <h3>
      <label for="name">'. __( 'Notes', 'gravityforms' ) .' </label>
    </h3>' .wp_nonce_field( 'gforms_update_note', 'gforms_update_note' ) .
   '<div class="inside">';

  $notes = RGFormsModel::get_lead_notes( $lead['id'] );

  //getting email values
  $email_fields = GFCommon::get_email_fields( $form );
  $emails = array();

  foreach ( $email_fields as $email_field ) {
    if ( ! empty( $lead[ $email_field->id ] ) ) {
      $emails[] = $lead[ $email_field->id ];
    }
  }
  //displaying notes grid
  $subject = '';
  $return .= notes_sidebar_grid( $notes, true, $emails, $subject );

  $return .=
   '</div>
  </div>';
  return $return;
}

function display_flags_prelim_locs($form, $lead) {
  $return = '';
  $mode       = empty( $_POST['screen_mode'] )  ? 'view' : $_POST['screen_mode'];
  /* Entry Management Sidebar Area */
  if ($mode == 'view') {
    $return = '<div class="postbox">';
    // Create Update button for sidebar entry management
    $entry_sidebar_button = '<input type="button" name="update_management" value="Update Management" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_entry_management\');"/>';
    $msgBox = '<span class="updMsg update_entry_managementMsg"></span>';
    $return .= $entry_sidebar_button. $msgBox;

    // Load flags and prelim location section
    $return .= mf_sidebar_entry_info( $form['id'], $lead );

    // Create Update button for sidebar entry management
    $return .= $entry_sidebar_button.$msgBox;
    $return .= '</div>';
	}
  return $return;
}

function display_sched_loc_box($form, $lead) {
  $return = '';
  $mode       = empty( $_POST['screen_mode'] )  ? 'view' : $_POST['screen_mode'];
	/* Scheduling Management Sidebar Area */
	if ($mode == 'view') {
    $return .= '<div class="postbox schedBox">';
    // Load Entry Sidebar details: schedule
    $return .=  mf_sidebar_entry_schedule( $form['id'], $lead );
		$return .= '</div>';
  }
  return $return;
}

function display_ticket_code_box($form, $lead) {
  $return = '<div class="postbox">';
    // Load Entry Sidebar details: Ticket Code (Field 308)
    $return .= mf_sidebar_entry_ticket( $form['id'], $lead );
  $return .= '</div>';
  return $return;
}

function display_form_change_box($form, $lead) {
  $output = '<div class="postbox">';
  //load 'Change Form' form
  $forms   = GFAPI::get_forms(true,false);  // Load Fields to show on entry info
  $output .= '<h4><label class="detail-label" for="entry_form_change">Change Form:</label></h4>';
  $output .= '<select style="width:250px" name="entry_form_change">';
  foreach( $forms as $choice ){
    $selected = '';
    if ($choice['id'] == $lead['form_id']) $selected=' selected ';
    $output .= '<option '.$selected.' value="'.$choice['id'].'">'.$choice['title'].'</option>';
  }
  $output .= '</select>';
  $output .= '<input type="button" name="change_form_id" value="Change Form" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'change_form_id\');"/><br />';
  $output .= '<span class="updMsg change_form_idMsg"></span>';
  $output .=  '</div>';

  return $output;
}

function display_dupCopy_entry_box($form, $lead) {
  $output = '<div class="postbox">';

  //load Duplicate/Copy Entry form
  $forms = GFAPI::get_forms(true,false);  // Load Fields to show on entry info

  $output .= '<h4><label class="detail-label" for="entry_form_copy">Duplicate/Copy Entry ID '.$lead['id'].'</label></h4>';
  $output .= 'Into Form:<br/>';
  $output .= '<select style="width:250px" name="entry_form_copy">';
  foreach( $forms as $choice ) {
    $selected = '';
    if ($choice['id'] == $form['id']) $selected=' selected ';
    $output .= '<option '.$selected.' value="'.$choice['id'].'">'.$choice['title'].'</option>';
  }
  $output .=  '</select><br/><br/>';
  $output .= '<input type="button" name="duplicate_entry_id" value="Duplicate Entry" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'duplicate_entry_id\');"/><br />';
  $output .= '<span class="updMsg duplicate_entry_idMsg"></span>';
  $output .= '</div>';
  return $output;
}

function display_send_conf_box($form, $lead) {
  return '<div class="postbox">
            <div class="detail-view-print">
              <br/>
              <!--button to trigger send confirmation letter event -->
              <input type="button" name="send_conf_letter" value="Send Confirmation Letter" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'send_conf_letter\');"/>
              <span class="updMsg send_conf_letterMsg"></span>
            </div>
          </div>';
}

/* Notes Sidebar Grid Function */
function notes_sidebar_grid( $notes, $is_editable, $emails = null, $subject = '' ) {
  $return = '
    <table class="widefat fixed entry-detail-notes">
      <tbody id="the-comment-list" class="list:comment">';
	$count = 0;
  $notes_count = sizeof( $notes );
  foreach ( $notes as $note ) {
    $count ++;
    $is_last = $count >= $notes_count ? true : false;
    $return .= '<tr valign="top" class="note'. $note->id .'">';
    if ( $is_editable && GFCommon::current_user_can_any( 'gravityforms_edit_entry_notes' ) ) {
      $return .= '<td class="check-column" scope="row" style="padding:9px 3px 0 0">
                    <input type="checkbox" value="'. $note->id .'" name="note[]" />
                  </td>';
    }
    $return .= '<td class="entry-detail-note'. ($is_last ? ' lastrow' : '') .'">';
    $class   = $note->note_type ? " gforms_note_{$note->note_type}" : '';
    $return .= '<div style="margin-top: 4px;">
                  <div class="note-avatar">'.apply_filters( 'gform_notes_avatar', get_avatar( $note->user_id, 48 ), $note ).
               '</div>
                <h6 class="note-author">' .esc_html( $note->user_name ). '</h6>
              <p class="note-email">
                <a href="mailto:'. esc_attr( $note->user_email ).'">'. esc_html( $note->user_email ) .'</a><br />'.
                __( 'added on ', 'gravityforms' ).
                esc_html( GFCommon::format_date( $note->date_created, false ) ) .
             '</p>
            </div>
            <div class="detail-note-content'.$class. '">'.
              html_entity_decode( $note->value ) .
           '</div>
          </td>
        </tr>';
    }
    $return .=
      '</tbody>
    </table>';

  if ( sizeof( $notes ) > 0 && $is_editable && GFCommon::current_user_can_any( 'gravityforms_edit_entry_notes' ) ) {
    $return .= '
    <input type="button" name="delete_note_sidebar" value="Delete Selected Note(s)" class="button" style="width:100%;padding-bottom:2px;" onclick="updateMgmt(\'delete_note_sidebar\');">
    <span class="updMsg delete_note_sidebarMsg"></span>';
  }
  return $return;
}

function wpse27856_set_content_type(){
	return "text/html";
}

//creates box to update the ticket code field 308
function mf_sidebar_entry_ticket($form_id, $lead) {
  $form = GFAPI::get_form($form_id);
  $field308=RGFormsModel::get_field($form,'308');
  $output  = '<h4><label class="detail-label">Ticket Code:</label></h4>';
  $output .= '<input name="entry_ticket_code" id="entry_ticket_code type="text" style="margin-bottom: 4px;" value="'.(isset($lead['308'])?$lead['308']:'').'" />';

  // Create Update button for ticket code
  $entry_sidebar_button  = '<input type="button" name="update_ticket_code" value="Update Ticket Code" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_ticket_code\');"/>';
  $msgBox = '<span class="updMsg update_ticket_codeMsg"></span>';
	return $output . $entry_sidebar_button .$msgBox;
}

function mf_sidebar_entry_schedule($form_id, $lead) {
    global $wpdb;
    $output  = '<link rel="stylesheet" type="text/css" href="'.get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css"/>
                <h4><label class="detail-label">Schedule/Location:</label></h4>';
    $output .= display_schedule($form_id,$lead);
    // Set up the Add to Schedule Section
    $output .= '<h4 class="topBorder">Add New:</h4>';

    $locSql = "SELECT area.area, subarea.subarea, subarea.nicename, subarea.id as subarea_id
                FROM wp_mf_faire faire, wp_mf_faire_area area, wp_mf_faire_subarea subarea
                where FIND_IN_SET($form_id,faire.form_ids) and faire.ID = area.faire_id and subarea.area_id = area.ID
                order by area,subarea";

    $output .= 'Area - Subarea <select style="max-width:100%" name="entry_location_subarea_change" id="entry_location_subarea_change">';
    $output .= '<option value="none">None</option>';
    $subAreaArr = array();
    foreach($wpdb->get_results($locSql,ARRAY_A) as $row){
      $area_option = (strlen($row['area']) > 0) ? ' ('.$row['area'].')' : '' ;
      $subarea_option = ($row['subarea']!=''?$row['subarea']:$row['subarea']);
      $output .= '<option value="'.$row['subarea_id'].'">'.$row['area'].' - '.$subarea_option.'</option>';
      $subAreaArr[] = $row['subarea_id'];
    }
    $output .= "</select><br />";

    //create unique array of subareas
    array_unique($subAreaArr);
    $subAreaList = implode(",",$subAreaArr);

    $sql = "select distinct(location) as location, subarea_id from wp_mf_location";
    if($subAreaList!=''){
      $sql .= " where subarea_id in(".$subAreaList.") and location!=''";
    }

    $locArr = array();
    foreach($wpdb->get_results($sql,ARRAY_A) as $row){
      $locArr[$row['subarea_id']][]=$row['location'];
    }

    $output .= '<script>
                  var locationObj = ' .json_encode($locArr). '
                </script>';

    //create dropdown of current locations for selected subarea
    $output .= 'Location Code: (optional)<br/>';
    $output .= '<select id="locationSel"><option>Select Area - Subarea above</option></select><br/>';
    $output .= '<input type="text" name="update_entry_location_code" style="display:none" id="update_entry_location_code" /><br/>';

    // Load Fields to show on entry info
    $output .= '<br/>';
    $output .= 'Optional Schedule Start/End
                 <div class="clear"></div>' .
                '<div style="padding:10px 0;width:40px;float:left">Start: </div>' .
                '<div style="float:left"><input type="text" value="" name="datetimepickerstart" id="datetimepickerstart"></div>' .
                '<div class="clear" style="padding:10px 0;width:40px;float:left">End:</div>
                 <div style="float:left"><input type="text" value="" name="datetimepickerend" id="datetimepickerend"></div>
                 <div class="clear"></div>';

    // Create Update button for sidebar entry management
    $output .=  '<input type="button" name="update_entry_schedule" value="Add Location" class="button" style="width:auto;padding-bottom:2px; margin: 10px 0;" onclick="updateMgmt(\'update_entry_schedule\');"/><br />
                 <span class="updMsg update_entry_scheduleMsg"></span>';
    return $output;
}

function display_schedule($form_id,$lead,$section='sidebar'){
  global $wpdb;
  //first, let's display any schedules already entered for this entry
  $entry_id = $lead['id'];
  $sql = "select `wp_mf_schedule`.`ID` as schedule_id, `wp_mf_schedule`.`entry_id`, location.ID as location_id, location.location,area.area, subarea.subarea,
                  `wp_mf_faire`.`faire`, `wp_mf_schedule`.`start_dt`, `wp_mf_schedule`.`end_dt`, `wp_mf_schedule`.`day`, wp_mf_faire.time_zone,
                  subarea.ID as subarea_id

          from wp_mf_location location
          left outer join wp_mf_schedule on `wp_mf_schedule`.`entry_id` = ".$entry_id." and wp_mf_schedule.location_id = location.ID,
                          wp_mf_faire_subarea subarea, wp_mf_faire_area area,wp_mf_faire

          where location.entry_id=".$entry_id."
            and FIND_IN_SET(".$form_id.",wp_mf_faire.form_ids)
            and location.subarea_id = subarea.ID
            and subarea.area_id = area.ID
          order by area ASC, subarea ASC, start_dt ASC";

  $scheduleArr = array();
  foreach($wpdb->get_results($sql,ARRAY_A) as $row){
    //order entries by subarea(stage), then date
    $stage = ($row['subarea'] != NULL ? $row['area'].' - '.$row['subarea']: '');
    if($row['location']!='')    $stage .= ' ('.$row['location'].')';
    $start_dt = ($row['start_dt'] != NULL ? strtotime($row['start_dt'])  : '');
    $end_dt   = ($row['end_dt']   != NULL ? strtotime($row['end_dt'])    : '');
    $schedule_id = ($row['schedule_id'] != NULL ? (int) $row['schedule_id'] : '');
    $date     = ($start_dt != '' ? date("n/j/y",$start_dt) : '');
    $timeZone = $row['time_zone'];
    $subarea_id = $row['subarea_id'];

    //build array
    $schedules[$subarea_id]['location'] = $row['location_id'];
    $schedules[$subarea_id]['stage']    = $stage;
    if($date!=''){
      $schedules[$subarea_id]['schedule'][$date][$schedule_id] = array('start_dt' => $start_dt, 'end_dt' => $end_dt, 'timeZone'=>$timeZone);
    }
  }

  //make sure there is data to display
  if($wpdb->num_rows !=0){
    $output = '';
    //let's loop thru the schedule array now
    foreach($schedules as $data){
      $location_id = $data['location'];
      $stage       = $data['stage'];
      $output     .= '<div class="stageName">'.$stage.'</div>';

      $scheduleArr = (isset($data['schedule'])?$data['schedule']:'');
      if(is_array($scheduleArr)){
        foreach($scheduleArr as $date=>$schedule){
          if($date!=''){
            $output .= '<div>'.date('l n/j/y',strtotime($date)).'<br/>';
            $output .= '<div>';
            foreach($schedule as $schedule_id=>$schedData){
              $start_dt   = $schedData['start_dt'];
              $end_dt     = $schedData['end_dt'];
              $db_tz      = $schedData['timeZone'];

              //set time zone for faire
              $dateTime = new DateTime();
              $dateTime->setTimeZone(new DateTimeZone($db_tz));
              $timeZone = $dateTime->format('T');
              if($section!='summary'){
                $output .= '<input type="checkbox" value="'.$schedule_id.'" name="delete_schedule_id[]"></input>';
              }

              $output .= '<span class="schedDate">'.date("g:i A",$start_dt).' - '.date("g:i A",$end_dt).' ('.$timeZone.')</span><div class="clear"></div>';
            }
            $output .= '</div></div>';
          }
        }
      }else{ //if there is no schedule data
        //location only display checkbox to delete
        if($section!='summary'){
          $output .= '<input type="checkbox" value="'.$location_id.'" name="delete_location_id[]" /> <span class="schedDate">Remove Location</span>';
        }
        $output .= '<div class="clear"></div>';
      }
      $output .= '<br/>';
    }

    if($section!='summary'){
      $entry_delete_button = '<input type="button" name="delete_entry_schedule[]" value="Delete Selected" class="button"
                       style="width:auto;padding-bottom:2px;"
                      onclick="updateMgmt(\'delete_entry_schedule\');"/><br />';
      $updMsg    .= '<span class="updMsg delete_entry_scheduleMsg"></span>';
      $output .= $entry_delete_button.$updMsg;
    }
    $output .= '<br/>';
    return $output;
  }
}

/* This is where we run code on the entry info screen.  Logic for action handling goes here */
function mf_sidebar_entry_info($form_id, $lead) {
  // Load Fields to show on entry info
	$form = GFAPI::get_form($form_id);

  //flags
  $output = '<h4><label class="detail-label">Flags:</label></h4>';
  $field = RGFormsModel::get_field($form,'304');
  $value   = RGFormsModel::get_lead_field_value( $lead, $field );
  $fieldName = 'entry_info_flags_change';
  $output .=  mf_checkbox_display($field, $value, $form_id, $fieldName);


  //preliminary locations
	$output   .= '<h4><label class="detail-label">Preliminary Location:</label></h4>';
  $field     = RGFormsModel::get_field($form,'302');
  $value     = RGFormsModel::get_lead_field_value( $lead, $field );
  $fieldName = 'entry_info_location_change';

  $output .= mf_checkbox_display($field, $value, $form_id, $fieldName);

	$output .= '<textarea name="entry_location_comment" id="entry_location_comment" style="width: 100%; height: 50px; margin-bottom: 4px;" cols="" rows="">'.(isset($lead['307'])?$lead['307']:'').'</textarea>';
  return $output;
}

function  mf_checkbox_display($field, $value, $form_id, $fieldName) {
  $choices = '';
  $is_entry_detail = $field->is_entry_detail();
  $is_form_editor  = $field->is_form_editor();
  $output = '';
  if ( is_array( $field->choices ) ) {
    $choice_number = 1;
    foreach ( $field->choices as $choice ) {
      if ( $choice_number % 10 == 0 ) { //hack to skip numbers ending in 0. so that 5.1 doesn't conflict with 5.10
        $choice_number ++;
      }

      $input_id = $field->id . '.' . $choice_number;

      if ( $is_entry_detail || $is_form_editor || $form_id == 0 ){
        $id = $field->id . '_' . $choice_number ++;
      } else {
        $id = $form_id . '_' . $field->id . '_' . $choice_number ++;
      }

      if ( ! isset( $_GET['gf_token'] ) && empty( $_POST ) && rgar( $choice, 'isSelected' ) ) {
        $checked = "checked='checked'";
      } elseif ( is_array( $value ) && RGFormsModel::choice_value_match( $field, $choice, rgget( $input_id, $value ) ) ) {
        $checked = "checked='checked'";
      } elseif ( ! is_array( $value ) && RGFormsModel::choice_value_match( $field, $choice, $value ) ) {
        $checked = "checked='checked'";
      } else {
        $checked = '';
      }

      $choice_value = $choice['value'];
      if ( $field->enablePrice ) {
        $price = rgempty( 'price', $choice ) ? 0 : GFCommon::to_number( rgar( $choice, 'price' ) );
        $choice_value .= '|' . $price;
      }
      $choice_value  = esc_attr( $choice_value );

      $output .= '<input type="checkbox" '.$checked.' name="entry_info_flags_change[]" style="margin: 3px;" value="'.$input_id.'_'.$choice_value.'" />'.$choice['text'].' <br />';
    }
  }
  return $output;
}

function mf_sidebar_disp_meta_field($form_id, $lead,$meta_key='') {
  //get current value if set
  $metaValue = '';
  if($meta_key!=''){
    $metaValue = gform_get_meta( $lead['id'], $meta_key );
  }
  $output  = '';
  //build input
  $meta = GFFormsModel::get_entry_meta(array( $form_id));
  if(isset($meta[$meta_key])){
    $output .= '<tr>';
    $output .= '  <td><label>'.$meta[$meta_key]['label'].':&nbsp;</label></td>';
    if(isset($meta[$meta_key]['filter']['choices'])){
      $choices = $meta[$meta_key]['filter']['choices'];
      $output .= '<td><select class="metafield" name="'.$meta_key.'" id="'.$meta_key.'">';
      if($metaValue=='')  $output .= '<option value=""></option>';
      foreach($choices as $option){
        $output .= '<option value="'.$option['value'].'"'.($metaValue==$option['value']?' selected ':'').'>'.$option['text'].'</option>';
      }
      $output .='</select></td>';
    }else{ //build as regular input text
      $output .= '<td><input class="metafield"  name="'.$meta_key.'" id="'.$meta_key.'" value="'.$metaValue.'" /></td>';
    }
    $output .= '  <td><span class="updMsg" id="'.$meta_key.'Status"></span></td>'; //updating progress field
    $output .= '</tr>';
  }
  return $output;
}

function mf_sidebar_entry_status($form_id, $lead) {
  $output  = '<tr>';
  if ( current_user_can( 'update_entry_status') ) {
    $output .= '  <td>'.
                    '<input type="hidden" name="entry_info_entry_id" value="'.$lead['id'].'" />' .
                    '<label class="detail-label" for="entry_info_status_change">Entry Status:&nbsp;</label>' .
               '  </td>';
    // Load Fields to show on entry info
    $form     = GFAPI::get_form($form_id);
    $field303 = RGFormsModel::get_field($form,'303');
    $output .= '  <td>';
    $output .= '    <select name="entry_info_status_change" onchange="updateMgmt(\'update_entry_status\');">';
    foreach( $field303['choices'] as $choice ){
      $selected = '';
      if ($lead[$field303['id']] == $choice['text']) $selected=' selected ';
      $output .= '<option '.$selected.' value="'.$choice['text'].'">'.$choice['text'].'</option>';
    }
    $output .= '    </select></td>';
    //$output .= '<td><input type="button" name="update_management" value="Save Status" class="btn btn-danger" onclick="updateMgmt(\'update_entry_status\');" /></td>';
    $output .= '<td><span class="updMsg update_entry_statusMsg"></span></td>';
  }else{
    $output .= '<td><label class="detail-label" for="entry_info_status_change">Status:&nbsp;</label></td>';
    $output .= '<td>'.$lead[303].'</td>';
  }
  $output  .= '</tr>';
  return $output;
}