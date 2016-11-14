<?php
/* Side bar Layout */
add_action("gform_entry_detail_sidebar_before", "add_sidebar_sections", 10,2);
function add_sidebar_sections($form, $lead) {
  display_entry_info_box($form, $lead);
  display_entry_rating_box($form, $lead);
  display_entry_notes_box($form, $lead);
  display_flags_prelim_locs($form, $lead);
  display_sched_loc_box($form, $lead);
  display_ticket_code_box($form, $lead);
  display_form_change_box($form, $lead);
  display_dupCopy_entry_box($form, $lead);
  display_send_conf_box($form, $lead);
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
  ?>
  <div id="infoboxdiv" class="postbox">
    <div id="minor-publishing">
      <?php mf_sidebar_entry_status( $form['id'], $lead ); ?><br/>
      <?php mf_sidebar_disp_meta_field($form['id'], $lead, 'res_status' ); ?><br/>
      <?php mf_sidebar_disp_meta_field($form['id'], $lead, 'res_assign' ); ?><br/>
      Contact:<div style="padding:5px"><?php echo (isset($lead['96.3'])?$lead['96.3']:'');  ?> <?php echo (isset($lead['96.6'])?$lead['96.6']:'');  ?><br />
        <?php echo $street; ?><br />
        <?php echo $street2; ?>
        <?php echo $city; ?>, <?php echo $state; ?>  <?php echo $zip; ?><br />
        <?php echo $country; ?><br />
        <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a><br />
        <?php echo $phonetype; ?>:  <?php echo $phone; ?><br />

      </div>
      <?php _e( 'Filled out: ', 'gravityforms' ); ?><?php echo esc_html( GFCommon::format_date( $lead['date_created'], false, 'Y/m/d' ) ) ?><br />
      <br/>
      <?php do_action( 'gform_entry_info', $form['id'], $lead );?>
    </div>
    <div id="delete-action" style="float:none;">
      <?php
        switch ( $lead['status'] ) {
          case 'spam' :
            if ( GFCommon::spam_enabled( $form['id'] ) ) {
              ?>
              <a onclick="jQuery('#action').val('unspam'); jQuery('#entry_form').submit()" href="#"><?php _e( 'Not Spam', 'gravityforms' ) ?></a>
              <?php
              echo GFCommon::current_user_can_any( 'gravityforms_delete_entries' ) ? '|' : '';
            }
            if ( GFCommon::current_user_can_any( 'gravityforms_delete_entries' ) ) {
              ?>
              <a class="submitdelete deletion" onclick="if ( confirm('<?php _e( ';You are about to delete this entry. \'Cancel\' to stop, \'OK\' to delete.', 'gravityforms' ) ?>') ) {jQuery('#action').val('delete'); jQuery('#entry_form').submit(); return true;} return false;" href="#"><?php _e( 'Delete Permanently', 'gravityforms' ) ?></a>
              <?php
            }
            break;
          case 'trash' :
            ?>
            <a onclick="jQuery('#action').val('restore'); jQuery('#entry_form').submit()" href="#"><?php _e( 'Restore', 'gravityforms' ) ?></a>
            <?php
            if ( GFCommon::current_user_can_any( 'gravityforms_delete_entries' ) ) { ?>
              | <a class="submitdelete deletion"
                onclick="if ( confirm('<?php _e('You are about to delete this entry. \'Cancel\' to stop, \'OK\' to delete.', 'gravityforms' ) ?>') ) {jQuery('#action').val('delete'); jQuery('#entry_form').submit(); return true;} return false;"
                href="#"><?php _e( 'Delete Permanently', 'gravityforms' ) ?></a>
            <?php
            }
            break;

          default :
            if ( GFCommon::current_user_can_any( 'gravityforms_delete_entries' ) ) {
            ?>
              <a class="submitdelete deletion"
              onclick="jQuery('#action').val('trash'); jQuery('#entry_form').submit()"
              href="#"><?php _e( 'Move to Trash', 'gravityforms' ) ?></a>
              <?php
              echo GFCommon::spam_enabled( $form['id'] ) ? '|' : '';
            }
            if ( GFCommon::spam_enabled( $form['id'] ) ) {
              ?>
              <a class="submitdelete deletion"
                onclick="jQuery('#action').val('spam'); jQuery('#entry_form').submit()"
                href="#"><?php _e( 'Mark as Spam', 'gravityforms' ) ?></a>
              <?php
            }
        } //end switch
        if ( GFCommon::current_user_can_any( 'gravityforms_edit_entries' ) && $lead['status'] != 'trash' ) {
          $button_text      = $mode == 'view' ? __( 'Edit', 'gravityforms' ) : __( 'Update', 'gravityforms' );
          $disabled         = $mode == 'view' ? '' : ' disabled="disabled" ';
          $update_button_id = $mode == 'view' ? 'gform_edit_button' : 'gform_update_button';
          $button_click     = $mode == 'view' ? "jQuery('#screen_mode').val('edit');" : "jQuery('#action').val('update'); jQuery('#screen_mode').val('view');";
          $update_button    = '<input id="' . $update_button_id . '" ' . $disabled . ' class="button button-large button-primary" type="submit" tabindex="4" value="' . $button_text . '" name="save" onclick="' . $button_click . '"/>';
          echo apply_filters( 'gform_entrydetail_update_button', $update_button );
          if ( $mode == 'edit' ) {
            echo '&nbsp;&nbsp;<input class="button button-large" type="submit" tabindex="5" value="' . __( 'Cancel', 'gravityforms' ) . '" name="cancel" onclick="jQuery(\'#screen_mode\').val(\'view\');"/>';
          }
        }
        ?>
    </div>
  </div>  <?php
}

function display_entry_rating_box($form, $lead) {
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
  ?>
  <div class="postbox">
    <h3>Entry Rating:
      <a href="#" onclick="return false;"
        data-toggle="popover" data-trigger="hover"
        data-placement="top" data-html="true"
        data-content="1 = No way<br/>2 = Low priority<br/>3 = Yes, If there’s room<br/>4 = Yes definitely<br/>5 = Hell yes">
        (?)
      </a>
      <?php echo $ratingAvg ?> stars
    </h3>

    <div class="entryRating inside">
      <span class="star-rating">
        <input type="radio" name="rating" value="1" <?php echo ($currRating==1?'checked':'');?>><i></i>
        <input type="radio" name="rating" value="2" <?php echo ($currRating==2?'checked':'');?>><i></i>
        <input type="radio" name="rating" value="3" <?php echo ($currRating==3?'checked':'');?>><i></i>
        <input type="radio" name="rating" value="4" <?php echo ($currRating==4?'checked':'');?>><i></i>
        <input type="radio" name="rating" value="5" <?php echo ($currRating==5?'checked':'');?>><i></i>
      </span>
      (Your Rating)<br/>
      <span id="updateMSG" style="font-size:smaller">Average Rating: <?php echo $ratingAvg; ?> Stars from <?php echo $ratingNum;?> users.</span>
      <?php
      if($ratingResults!=''){
        echo '<table cellspacing="0" style="padding:10px 0">'
        . '     <tr>'
            . '   <td class="entry-view-field-name">Rating</td>'
            . '   <td class="entry-view-field-name">User</td>'
            . '   <td class="entry-view-field-name">Date Rated</td>'
            . '</tr></table>'.$ratingResults;
      }
      ?>
    </div>
  </div>
  <?php
}

function display_entry_notes_box($form, $lead) {
  /* Notes Sidebar Area */?>
  <div class="postbox">
    <h3>
      <label for="name"><?php _e( 'Notes', 'gravityforms' ); ?></label>
    </h3>

    <?php wp_nonce_field( 'gforms_update_note', 'gforms_update_note' ) ?>
    <div class="inside">
      <?php
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
        notes_sidebar_grid( $notes, true, $emails, $subject );
        ?>
    </div>
  </div>
  <?php
}

function display_flags_prelim_locs($form, $lead) {
  $mode       = empty( $_POST['screen_mode'] )  ? 'view' : $_POST['screen_mode'];
  /* Entry Management Sidebar Area */
  if ($mode == 'view') {  ?>
    <div class='postbox'>
      <?php
      // Create Update button for sidebar entry management
      $entry_sidebar_button = '<input type="button" name="update_management" value="Update Management" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'update_entry_management\');"/>';
      echo $entry_sidebar_button;
      // Load flags and prelim location section
      mf_sidebar_entry_info( $form['id'], $lead );
      ?>
      <?php
      // Create Update button for sidebar entry management
      echo $entry_sidebar_button;	?>
    </div>
    <?php
	}
}

function display_sched_loc_box($form, $lead) {
  $mode       = empty( $_POST['screen_mode'] )  ? 'view' : $_POST['screen_mode'];
	/* Scheduling Management Sidebar Area */
	if ($mode == 'view') {
		?>
		<div class='postbox schedBox'>
      <?php
      // Load Entry Sidebar details: schedule
      mf_sidebar_entry_schedule( $form['id'], $lead );
      ?>
		</div>  <?php
  }
}

function display_ticket_code_box($form, $lead) {
  ?>
  <div class='postbox'>
    <?php
    // Load Entry Sidebar details: Ticket Code (Field 308)
    mf_sidebar_entry_ticket( $form['id'], $lead );
    ?>
  </div>
  <?php
}

function display_form_change_box($form, $lead) {
  ?>
  <div class='postbox'>
    <?php  //load 'Change Form' form
    $forms = GFAPI::get_forms(true,false);  // Load Fields to show on entry info
    echo ('<h4><label class="detail-label" for="entry_form_change">Form:</label></h4>');
    echo ('<select style="width:250px" name="entry_form_change">');
    foreach( $forms as $choice ){
      $selected = '';
      if ($choice['id'] == $lead['form_id']) $selected=' selected ';
      echo('<option '.$selected.' value="'.$choice['id'].'">'.$choice['title'].'</option>');
    }
    echo '</select>';
    echo '<input type="button" name="change_form_id" value="Change Form" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'change_form_id\');"/><br />';
    ?>
  </div>
  <?php
}

function display_dupCopy_entry_box($form, $lead) {
  ?>
  <div class='postbox'>
    <?php
    //load Duplicate/Copy Entry form

    $forms = GFAPI::get_forms(true,false);  // Load Fields to show on entry info

    echo ('<h4><label class="detail-label" for="entry_form_copy">Duplicate/Copy Entry ID '.$lead['id'].'</label></h4>');
    echo 'Into Form:<br/>';
    echo ('<select style="width:250px" name="entry_form_copy">');
    foreach( $forms as $choice ) {
      $selected = '';
      if ($choice['id'] == $form['id']) $selected=' selected ';
      echo('<option '.$selected.' value="'.$choice['id'].'">'.$choice['title'].'</option>');
    }
    echo '</select><br/><br/>';
    echo '<input type="button" name="duplicate_entry_id" value="Duplicate Entry" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'duplicate_entry_id\');"/><br />';
    ?>
  </div>
  <?php
}

function display_send_conf_box($form, $lead) {
  ?>
  <div class='postbox'>
    <div class="detail-view-print">
      <?php
      //button to trigger send confirmation letter event
      echo '<input type="button" name="send_conf_letter" value="Send Confirmation Letter" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'send_conf_letter\');"/>';
      echo '  <div class="clear"></div>';?>
    </div>
  </div>
	<?php
}

/* Notes Sidebar Grid Function */
function notes_sidebar_grid( $notes, $is_editable, $emails = null, $subject = '' ) {
    ?>
  <table class="widefat fixed entry-detail-notes">
    <tbody id="the-comment-list" class="list:comment">
      <?php
			$count = 0;
			$notes_count = sizeof( $notes );
			foreach ( $notes as $note ) {
				$count ++;
				$is_last = $count >= $notes_count ? true : false;
				?>
        <tr valign="top">
          <?php
          if ( $is_editable && GFCommon::current_user_can_any( 'gravityforms_edit_entry_notes' ) ) {
          ?>
          <td class="check-column" scope="row" style="padding:9px 3px 0 0">
            <input type="checkbox" value="<?php echo $note->id ?>" name="note[]" />
          </td>
          <?php } ?>
          <td class="entry-detail-note<?php echo $is_last ? ' lastrow' : '' ?>">
            <?php
            $class = $note->note_type ? " gforms_note_{$note->note_type}" : '';
            ?>
            <div style="margin-top: 4px;">
              <div class="note-avatar">
                <?php echo apply_filters( 'gform_notes_avatar', get_avatar( $note->user_id, 48 ), $note ); ?>
              </div>
              <h6 class="note-author">
                <?php echo esc_html( $note->user_name ) ?>
              </h6>
              <p class="note-email">
                <a href="mailto:<?php echo esc_attr( $note->user_email ) ?>"><?php echo esc_html( $note->user_email ) ?></a><br />
                <?php _e( 'added on', 'gravityforms' ); ?>
                <?php echo esc_html( GFCommon::format_date( $note->date_created, false ) ) ?>
              </p>
            </div>
            <div class="detail-note-content<?php echo $class ?>">
              <?php echo html_entity_decode( $note->value ) ?>
            </div>
          </td>
        </tr>
      <?php }?>
    </tbody>
  </table>
  <?php
  if ( sizeof( $notes ) > 0 && $is_editable && GFCommon::current_user_can_any( 'gravityforms_edit_entry_notes' ) ) {
    ?>
    <input type="button" name="delete_note_sidebar" value="Delete Selected Note(s)" class="button" style="width:100%;padding-bottom:2px;" onclick="updateMgmt('delete_note_sidebar');">
    <?php
  }
}

function add_note_sidebar($lead, $form){
	global $current_user;

	$user_data = get_userdata( $current_user->ID );
	$project_name = $lead['151'];
	$email_to     = $_POST['gentry_email_notes_to_sidebar'];

	$email_note_info = '';

	//emailing notes if configured
	if ( !empty($email_to) ) {
		GFCommon::log_debug( 'GFEntryDetail::lead_detail_page(): Preparing to email entry notes.' );
		$email_to      = $_POST['gentry_email_notes_to_sidebar'];
		$email_from    = $current_user->user_email;
		$email_subject = stripslashes( 'Response Required Maker Application: '.$lead['id'].' '.$project_name);
		$entry_url = get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=gf_entries&view=mfentry&id=' . $form['id'] . '&lid=' . rgar( $lead, 'id' );
		$body = stripslashes( $_POST['new_note_sidebar'] ). '<br /><br />Please reply in entry:<a href="'.$entry_url.'">'.$entry_url.'</a>';
		$headers = "From: \"$email_from\" <$email_from> \r\n";
		//Enable HTML Email Formatting in the body
		add_filter( 'wp_mail_content_type','wpse27856_set_content_type' );
		$result  = wp_mail( $email_to, $email_subject, $body, $headers );
		//Remove HTML Email Formatting
		remove_filter( 'wp_mail_content_type','wpse27856_set_content_type' );
		$email_note_info = '<br /><br />:SENT TO:['.implode(",", $email_to).']';
	}

	mf_add_note( $lead['id'],  nl2br(stripslashes($_POST['new_note_sidebar'].$email_note_info)));

}

function wpse27856_set_content_type(){
	return "text/html";
}

//creates box to update the ticket code field 308
function mf_sidebar_entry_ticket($form_id, $lead) {
    $form = GFAPI::get_form($form_id);
    $field308=RGFormsModel::get_field($form,'308');
    echo ('<h4><label class="detail-label">Ticket Code:</label></h4>');
    echo ('<input name="entry_ticket_code" id="entry_ticket_code type="text" style="margin-bottom: 4px;" value="'.(isset($lead['308'])?$lead['308']:'').'" />');

    // Create Update button for ticket code
    $entry_sidebar_button = '<input type="button" name="update_ticket_code" value="Update Ticket Code" class="button"
		 style="width:auto;padding-bottom:2px;"
		onclick="updateMgmt(\'update_ticket_code\');"/>';
	echo $entry_sidebar_button;
}
function mf_sidebar_entry_schedule($form_id, $lead) {
    global $wpdb;
    echo ('<link rel="stylesheet" type="text/css" href="'.get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css"/>
           <h4><label class="detail-label">Schedule/Location:</label></h4>');
    echo display_schedule($form_id,$lead);
    // Set up the Add to Schedule Section
    echo ('<h4 class="topBorder">Add New:</h4>');

    $locSql = "SELECT area.area, subarea.subarea, subarea.nicename, subarea.id as subarea_id
                FROM wp_mf_faire faire, wp_mf_faire_area area, wp_mf_faire_subarea subarea
                where FIND_IN_SET($form_id,faire.form_ids) and faire.ID = area.faire_id and subarea.area_id = area.ID
                order by area,subarea";

    echo ('Area - Subarea <select style="max-width:100%" name="entry_location_subarea_change" id="entry_location_subarea_change">');
    echo '<option value="none">None</option>';
    $subAreaArr = array();
    foreach($wpdb->get_results($locSql,ARRAY_A) as $row){
      $area_option = (strlen($row['area']) > 0) ? ' ('.$row['area'].')' : '' ;
      $subarea_option = ($row['subarea']!=''?$row['subarea']:$row['subarea']);
      echo '<option value="'.$row['subarea_id'].'">'.$row['area'].' - '.$subarea_option.'</option>';
      $subAreaArr[] = $row['subarea_id'];
    }
    echo("</select><br />");

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

    ?>
    <script>
      var locationObj = <?php echo json_encode($locArr);?>
    </script>
    <?php

    //create dropdown of current locations for selected subarea
    echo 'Location Code: (optional)<br/>';
    echo '<select id="locationSel"><option>Select Area - Subarea above</option></select><br/>';
    echo '<input type="text" name="update_entry_location_code" style="display:none" id="update_entry_location_code" /><br/>';

    // Load Fields to show on entry info
    echo '<br/>';
    echo 'Optional Schedule Start/End
          <div class="clear"></div>';
    echo '<div style="padding:10px 0;width:40px;float:left">Start: </div><div style="float:left"><input type="text" value="" name="datetimepickerstart" id="datetimepickerstart"></div>';
    echo '<div class="clear" style="padding:10px 0;width:40px;float:left">End:</div>
          <div style="float:left"><input type="text" value="" name="datetimepickerend" id="datetimepickerend"></div>
          <div class="clear"></div>';

    // Create Update button for sidebar entry management
    echo '
          <input type="button" name="update_entry_schedule" value="Add Location" class="button"
              style="width:auto;padding-bottom:2px;    margin: 10px 0;"
              onclick="updateMgmt(\'update_entry_schedule\');"/><br />';
    echo '  <div class="clear"></div><hr>';
}

function display_schedule($form_id,$lead,$section='sidebar'){
  global $wpdb;
  //first, let's display any schedules already entered for this entry
  $entry_id=$lead['id'];
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
    //let's loop thru the schedule array now
    foreach($schedules as $data){
      $location_id = $data['location'];
      $stage       = $data['stage'];
      echo '<div class="stageName">'.$stage.'</div>';
      //echo ($stage!='' && $stage != NULL?'<u>'.$stage.'</u>' : '');
      $scheduleArr = (isset($data['schedule'])?$data['schedule']:'');
      if(is_array($scheduleArr)){
        foreach($scheduleArr as $date=>$schedule){
          if($date!=''){
            //echo '<br/>';
            echo '<div>'.date('l n/j/y',strtotime($date)).'<br/>';
            echo '<div>';
            foreach($schedule as $schedule_id=>$schedData){
              $start_dt   = $schedData['start_dt'];
              $end_dt     = $schedData['end_dt'];
              $db_tz      = $schedData['timeZone'];

              //set time zone for faire
              $dateTime = new DateTime();
              $dateTime->setTimeZone(new DateTimeZone($db_tz));
              $timeZone = $dateTime->format('T');
              if($section!='summary'){
                echo '<input type="checkbox" value="'.$schedule_id.'" name="delete_schedule_id[]"></input>';
              }
              echo '<span class="schedDate">'.date("g:i A",$start_dt).' - '.date("g:i A",$end_dt).' ('.$timeZone.')</span><div class="clear"></div>';
            }
            echo '</div></div>';
          }
        }
      }else{ //if there is no schedule data
        //location only display checkbox to delete
        if($section!='summary'){
          echo ('<input type="checkbox" value="'.$location_id.'" name="delete_location_id[]" /> <span class="schedDate">Remove Location</span>');
        }
        //echo ($stage!=''&&$stage!=NULL?'<u>'.$stage.'</u><br/>':'');
        echo '<div class="clear"></div>';
      }
      echo '<br/>';
    }

    if($section!='summary'){
      $entry_delete_button = '<input type="button" name="delete_entry_schedule[]" value="Delete Selected" class="button"
                       style="width:auto;padding-bottom:2px;"
                      onclick="updateMgmt(\'delete_entry_schedule\');"/><br />';
      echo $entry_delete_button;
    }
    echo '<br/>';
  }
}

/* This is where we run code on the entry info screen.  Logic for action handling goes here */
function mf_sidebar_entry_info($form_id, $lead) {
	// Load Fields to show on entry info
	$form = GFAPI::get_form($form_id);
	$field302=RGFormsModel::get_field($form,'302');
	$field303=RGFormsModel::get_field($form,'303');
	$field304=RGFormsModel::get_field($form,'304');
	$field307=RGFormsModel::get_field($form,'307');

	echo ('<h4><label class="detail-label">Flags:</label></h4>');
  if(is_array($field304['inputs'])){
    foreach($field304['inputs'] as $choice){
      $selected = '';
      if (stripslashes($lead[$choice['id']]) == stripslashes($choice['label'])) $selected=' checked ';
      echo('<input type="checkbox" '.$selected.' name="entry_info_flags_change[]" style="margin: 3px;" value="'.$choice['id'].'_'.$choice['label'].'" />'.$choice['label'].' <br />');
    }
  }

	echo ('<h4><label class="detail-label">Preliminary Location:</label></h4>');
  $locArray=array();

  foreach($lead as $key=>$field){
      if(strpos($key,'302')!== false){
          $locArray[]=stripslashes($field);
      }
  }

  if(is_array($field302['inputs'])){
    foreach($field302['inputs'] as $choice){
      $selected = '';
                  if(in_array(stripslashes($choice['label']),$locArray)) $selected=' checked ';
      //if (stripslashes($lead[$choice['id']]) == stripslashes($choice['label'])) $selected=' checked ';
      echo('<input type="checkbox" '.$selected.' name="entry_info_location_change[]" style="margin: 3px;" value="'.$choice['id'].'_'.$choice['label'].'" />'.$choice['label'].' <br />');
    }
  }

	echo ('<textarea name="entry_location_comment" id="entry_location_comment" style="width: 100%; height: 50px; margin-bottom: 4px;" cols="" rows="">'.(isset($lead['307'])?$lead['307']:'').'</textarea>');
}

function mf_sidebar_disp_meta_field($form_id, $lead,$meta_key='') {
  //get current value if set
  $metaValue = '';
  if($meta_key!=''){
    $metaValue = gform_get_meta( $lead['id'], $meta_key );
  }
  $output = '';
  //build input
  $meta = GFFormsModel::get_entry_meta(array( $form_id));
  if(isset($meta[$meta_key])){
    $output = '<label>'.$meta[$meta_key]['label'].':&nbsp;</label>';
    if(isset($meta[$meta_key]['filter']['choices'])){
      $choices = $meta[$meta_key]['filter']['choices'];
      $output .= '<select class="metafield" name="'.$meta_key.'" id="'.$meta_key.'">';
      if($metaValue=='')  $output .= '<option value=""></option>';
      foreach($choices as $option){
        $output .= '<option value="'.$option['value'].'"'.($metaValue==$option['value']?' selected ':'').'>'.$option['text'].'</option>';
      }
      $output .='</select>';
    }else{ //build as regular input text
      $output .= '<input class="metafield"  name="'.$meta_key.'" id="'.$meta_key.'" value="'.$metaValue.'" />';
    }
  }
  $output .= '<span id="'.$meta_key.'Status"></span>'; //updating progress field
  echo $output;
}

function mf_sidebar_entry_status($form_id, $lead) {
  echo ('<input type="hidden" name="entry_info_entry_id" value="'.$lead['id'].'">');
  if ( current_user_can( 'update_entry_status') ) {
    // Load Fields to show on entry info
    $form = GFAPI::get_form($form_id);
    $field303=RGFormsModel::get_field($form,'303');

    echo ('<label class="detail-label" for="entry_info_status_change">Status:&nbsp;</label>');
    echo ('<select name="entry_info_status_change">');
    foreach( $field303['choices'] as $choice ){
      $selected = '';
      if ($lead[$field303['id']] == $choice['text']) $selected=' selected ';
      echo('<option '.$selected.' value="'.$choice['text'].'">'.$choice['text'].'</option>');
    }
    echo '</select>&nbsp;';
    echo '<input type="button" name="update_management" value="Save Status" class="btn btn-danger" onclick="updateMgmt(\'update_entry_status\');" /><br />';
  }else{
    echo '<label class="detail-label" for="entry_info_status_change">Status:&nbsp;</label>';
    echo '&nbsp;&nbsp; '.$lead[303].'<br/>';
  }
}