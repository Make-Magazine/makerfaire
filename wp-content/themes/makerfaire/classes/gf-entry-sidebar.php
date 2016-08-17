<?php
function mf_sidebar_entry_locations($form_id, $lead) {
	echo ('<link rel="stylesheet" type="text/css" href="'.get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css"/>
			<h4><label class="detail-label">Faire Locations:</label></h4>');
	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$entry_id=$lead['id'];
	$result = $mysqli->query("SELECT  `wp_mf_location`.`ID`,
                                    `wp_mf_location`.`entry_id`,
                                    `wp_mf_faire`.`faire`,
                                    `wp_mf_faire_area`.`area`,
                                    `wp_mf_faire_subarea`.`subarea`,
                                    `wp_mf_location`.`location`,
                                    `wp_mf_location`.`latitude`,
                                    `wp_mf_location`.`longitude`,
                                    `wp_mf_location`.`location_element_id`
                                        FROM `wp_mf_location`, wp_mf_faire_subarea, wp_mf_faire_area, wp_mf_faire
			 where entry_id=$entry_id
                         and   wp_mf_location.subarea_id = wp_mf_faire_subarea.ID
                         and   wp_mf_faire_subarea.area_id = wp_mf_faire_area.ID
                         and   wp_mf_faire_area.faire_id   = wp_mf_faire.ID") or trigger_error($mysqli->error);

	if ($result)
	{
		while($row = $result->fetch_row())
		{
			$area = (strlen($row[3]) > 0) ? ' ('.$row[3].')' : '' ;
			$subarea = $row[4];
			$location_code = $row[5];
			$location_entry_id = $row[0];
			echo ('<input type="checkbox" value="'.$location_entry_id.'" style="margin: 3px;" name="delete_location_id[]"></input>'.$subarea.$area.' '.$location_code.' <br />');

		}
		$entry_delete_button = '<input type="submit" name="delete_entry_locations[]" value="Delete Selected" class="button"
			 style="width:auto;padding-bottom:2px;"
			 onclick="jQuery(\'#action\').val(\'delete_entry_location\');"/><br />';
		echo $entry_delete_button;
	}
	$result_subareas = $mysqli->query("select   wp_mf_faire_area.area ,subarea "
                . "                        from     wp_mf_faire_subarea "
                . "                        join     wp_mf_faire_area on wp_mf_faire_subarea.area_id = wp_mf_faire_area.ID "
                . "                        join     wp_mf_faire on find_in_set($form_id,form_ids) > 0 and wp_mf_faire_area.faire_id=wp_mf_faire.ID "
                . "                        order by subarea,area") or trigger_error($mysqli->error);
	if (isset($result_subareas))
	{
	echo ('<h5>Add Location:</h5>');
	echo ('Subarea <select style="width:250px" name="entry_location_subarea_change">
			');
	while($row = $result_subareas->fetch_row())
	{
		$area_option = (strlen($row[0]) > 0) ? ' ('.$row[0].')' : '' ;
		$subarea_option = $row[1];
		echo '<option value="'.$subarea_option.'">'.$subarea_option.$area_option.'</option>';
	}
		echo("		</select><br />");

	// Create Update button for sidebar entry management
	$entry_sidebar_button = '
			Location Code: (optional) <input type="text" name="update_entry_location_code" id="update_entry_location_code" />
			<input type="submit" name="update_entry_location" value="Update Location" class="button"
			 style="width:auto;padding-bottom:2px;"
			onclick="jQuery(\'#action\').val(\'update_entry_location\');"/><br />';
	echo $entry_sidebar_button;
	}


}

//creates box to update the ticket code field 308
function mf_sidebar_entry_ticket($form_id, $lead) {
    $form = GFAPI::get_form($form_id);
    $field308=RGFormsModel::get_field($form,'308');
    echo ('<h4><label class="detail-label">Ticket Code:</label></h4>');
    echo ('<input name="entry_ticket_code" id="entry_ticket_code type="text" style="margin-bottom: 4px;" value="'.(isset($lead['308'])?$lead['308']:'').'" />');

    // Create Update button for ticket code
    $entry_sidebar_button = '<input type="submit" name="update_ticket_code" value="Update Ticket Code" class="button"
		 style="width:auto;padding-bottom:2px;"
		onclick="jQuery(\'#action\').val(\'update_ticket_code\');"/>';
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
          <input type="submit" name="update_entry_schedule" value="Add Location" class="button"
              style="width:auto;padding-bottom:2px;    margin: 10px 0;"
              onclick="jQuery(\'#action\').val(\'update_entry_schedule\');"/><br />';
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
                  echo '<input type="checkbox" value="'.$schedule_id.'" name="delete_entry_id[]"></input>';
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
        $entry_delete_button = '<input type="submit" name="delete_entry_schedule[]" value="Delete Selected" class="button"
                         style="width:auto;padding-bottom:2px;"
                        onclick="jQuery(\'#action\').val(\'delete_entry_schedule\');"/><br />';
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

	echo ('<textarea name="entry_location_comment" id="entry_location_comment"
					style="width: 100%; height: 50px; margin-bottom: 4px;" cols=""
					rows="">'.(isset($lead['307'])?$lead['307']:'').'</textarea>');


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
    $output = '<label>'.$meta[$meta_key]['label'].': </label>';
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

    echo ('<label class="detail-label" for="entry_info_status_change">Status: </label>');
    echo ('<select name="entry_info_status_change">');
    foreach( $field303['choices'] as $choice ){
      $selected = '';
      if ($lead[$field303['id']] == $choice['text']) $selected=' selected ';
      echo('<option '.$selected.' value="'.$choice['text'].'">'.$choice['text'].'</option>');
    }
    echo('</select><input type="submit" name="update_management" value="Save Status" class="btn btn-danger"
    onclick="jQuery(\'#action\').val(\'update_entry_status\');"/><br />');
  }else{
    echo ('<label class="detail-label" for="entry_info_status_change">Status: </label>');
    echo '&nbsp;&nbsp; '.$lead[303].'<br/>';
  }
}

function mf_sidebar_forms($form_id, $lead) {
	// Load Fields to show on entry info
	$forms = GFAPI::get_forms(true,false);
	echo ('<h4><label class="detail-label" for="entry_form_change">Form:</label></h4>');
	echo ('<select style="width:250px" name="entry_form_change">');
	foreach( $forms as $choice ){
		$selected = '';
		if ($choice['id'] == $lead['form_id']) $selected=' selected ';
		echo('<option '.$selected.' value="'.$choice['id'].'">'.$choice['title'].'</option>');
	}
	echo('</select><input type="submit" name="change_form_id" value="Change Form" class="button"
	 style="width:auto;padding-bottom:2px;"
	onclick="jQuery(\'#action\').val(\'change_form_id\');"/><br />');
}

function mf_sidebar_dup($form_id, $lead) {
	// Load Fields to show on entry info
	$forms = GFAPI::get_forms(true,false);

	echo ('<h4><label class="detail-label" for="entry_form_copy">Duplicate/Copy Entry ID '.$lead['id'].'</label></h4>');
	echo 'Into Form:<br/>';
        echo ('<select style="width:250px" name="entry_form_copy">');
	foreach( $forms as $choice )
	{
		$selected = '';

		if ($choice['id'] == $form_id) $selected=' selected ';

		echo('<option '.$selected.' value="'.$choice['id'].'">'.$choice['title'].'</option>');
	}
	echo('</select><Br/><br/><input type="submit" name="duplicate_entry_id" value="Duplicate Entry" class="button"
	 style="width:auto;padding-bottom:2px;"
	 onclick="jQuery(\'#action\').val(\'duplicate_entry_id\');"/><br />');
}

/* Side bar Layout */
add_action("gform_entry_detail_sidebar_before", "add_sidebar_text_before", 10,2);
function add_sidebar_text_before($form, $lead){
	$mode = empty( $_POST['screen_mode'] ) ? 'view' : $_POST['screen_mode'];
	$street  = (isset($lead['101.1'])?$lead['101.1']:'');
	$street2 = (!empty($lead["101.2"])) ? $lead["101.2"].'<br />' : '' ;
	$city    = (isset($lead["101.3"])?$lead["101.3"]:'');
	$state   = (isset($lead["101.4"])?$lead["101.4"]:'');
	$zip     = (isset($lead["101.5"])?$lead["101.5"]:'');
	$country = (isset($lead["101.6"])?$lead["101.6"]:'');
	$email   = (isset($lead["98"])?$lead["98"]:'');
	$phone   = (isset($lead["99"])?$lead["99"]:'');
	$phonetype = (isset($lead["148"])?$lead["148"]:'');

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
</div>

<?php /* Ratings Sidebar Area */
    global $wpdb;
    // Retrieve any ratings
    $entry_id=$lead['id'];
    $sql = "SELECT user_id, rating, ratingDate FROM `wp_rg_lead_rating` where entry_id = ".$entry_id;
    $ratingTotal = 0;
    $ratingNum   = 0;
    $ratingResults = '';
    $user_ID = get_current_user_id();
    $currRating = '';
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
          <?php if($ratingResults!=''){
              echo '<table cellspacing="0" style="padding:10px 0">'
              . '       <tr>'
                      . '   <td class="entry-view-field-name">Rating</td>'
                      . '   <td class="entry-view-field-name">User</td>'
                      . '   <td class="entry-view-field-name">Date Rated</td>'
                      . '</tr>'.$ratingResults.'</table>';
          }
          ?>
      </div>
    </div>
  <?php /* Notes Sidebar Area */?>
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
/* Entry Management Sidebar Area */
if ($mode == 'view') {
	?>
	<div class='postbox'>
	<?php
  // Create Update button for sidebar entry management
	$entry_sidebar_button = '<input type="submit" name="update_management" value="Update Management" class="button"
		style="width:auto;padding-bottom:2px;"
		onclick="jQuery(\'#action\').val(\'update_entry_management\');"/>';
	echo $entry_sidebar_button;
	// Load Entry Sidebar details
	mf_sidebar_entry_info( $form['id'], $lead );
	?>
	<?php
  // Create Update button for sidebar entry management
	echo $entry_sidebar_button;	?>
	</div>
	<?php
	}
	/* Scheduling Management Sidebar Area */
	if ($mode == 'view') {
		?>
		<div class='postbox schedBox'>
		<?php
		// Load Entry Sidebar details: schedule
		mf_sidebar_entry_schedule( $form['id'], $lead );
		?>
		</div>

		<div class='postbox'>
      <?php
      // Load Entry Sidebar details: Ticket Code (Field 308)
      mf_sidebar_entry_ticket( $form['id'], $lead );
      ?>
		</div>
		<div class='postbox'>
      <?php
      // Load Entry Sidebar details: Faire locations
      //mf_sidebar_entry_locations( $form['id'], $lead );
      ?>
		</div>

		<div class='postbox'>
      <?php
      //load 'Change Form' form
      mf_sidebar_forms($form['id'], $lead );
      ?>
    </div>

		<div class='postbox'>
      <?php
          //load Duplicate/Copy Entry form
          mf_sidebar_dup($form['id'], $lead );
      ?>
    </div>
  <?php } ?>
  <div class="detail-view-print">
    <?php
    //button to trigger send confirmation letter event
    echo '<input type="submit" name="send_conf_letter" value="Send Confirmation Letter" class="button"
			 style="width:auto;padding-bottom:2px;"
			onclick="jQuery(\'#action\').val(\'send_conf_letter\');"/>';
    echo '  <div class="clear"></div>';?>
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
    <input type="submit" name="delete_note_sidebar" value="Delete Selected Note(s)" class="button" style="width:100%;padding-bottom:2px;" onclick="jQuery('#action').val('delete_note_sidebar');">
    <?php
  }
}


// This is where our custom post action handing occurs
add_action("gform_admin_pre_render_action", "mf_admin_pre_render");
function mf_admin_pre_render(){
  //Get the current action
  $mfAction=RGForms::post( 'action' );

  //Only process if there was a gravity forms action
  if (!empty($mfAction)){
    $entry_info_entry_id=$_POST['entry_info_entry_id'];
    $lead = GFAPI::get_entry( $entry_info_entry_id );
    $form_id    =  isset($lead['form_id']) ? $lead['form_id'] : 0;
    $form = RGFormsModel::get_form_meta($form_id);
    $entry_status =  isset($lead['303']) ? $lead['303'] : '';

    switch ($mfAction ) {
      // Entry Management Update
      case 'update_entry_management' :
        set_entry_status_content($lead,$form);
        break;
      case 'update_entry_status' :
        set_entry_status($lead,$form);
        break;
      case 'update_ticket_code' :
        $ticket_code = $_POST['entry_ticket_code'];
        $entry_info_entry_id=$_POST['entry_info_entry_id'];
        mf_update_entry_field($entry_info_entry_id,'308',$ticket_code);
        break;
      case 'update_entry_schedule' :
        set_entry_schedule($lead,$form);
        break;
      case 'delete_entry_schedule' :
        delete_entry_schedule($lead,$form);
        break;
      case 'update_entry_location' :
        set_entry_location($lead,$form);
        break;
      case 'delete_entry_location' :
        delete_entry_location($lead,$form);
        break;
      case 'change_form_id' :
        set_form_id($lead,$form);
        break;
      case 'duplicate_entry_id' :
        duplicate_entry_id($lead,$form);
        break;
      case 'sync_jdb' :
        GFJDBHELPER::gravityforms_send_entry_to_jdb($entry_info_entry_id);
        break;
      case 'sync_status_jdb' :
        GFJDBHELPER::gravityforms_sync_status_jdb($entry_info_entry_id,$entry_status);
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
        mf_add_note( $entry_info_entry_id, 'Confirmation Letter sent');
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
    GVupdate_changeRpt($form,$entry_info_entry_id,$lead);
    // Return the original form which is required for the filter we're including for our custom processing.
    return $form;

  }
}

/* Modify Set Entry Status */
function set_entry_status_content($lead,$form){
	$location_change=$_POST['entry_info_location_change'];
	$flags_change=$_POST['entry_info_flags_change'];
	$location_comment_change=$_POST['entry_location_comment'];
	$acceptance_status_change=$_POST['entry_info_status_change'];
	$entry_info_entry_id=$_POST['entry_info_entry_id'];
	$acceptance_current_status = $lead['303'];
	$field302=RGFormsModel::get_field($form,'302');
	$field304=RGFormsModel::get_field($form,'304');

	$is_acceptance_status_changed = (strcmp($acceptance_current_status, $acceptance_status_change) != 0);

	if (!empty($entry_info_entry_id)){
		/* Clear out old choices */
		foreach(   $field304['inputs'] as $choice){
			mf_update_entry_field($entry_info_entry_id,$choice['id'],'');
		}
		foreach(   $field302['inputs'] as $choice){
			mf_update_entry_field($entry_info_entry_id,$choice['id'],'');
		}
		/* Save entries */
		if (!empty($location_change)){
			foreach($location_change as $location_entry){
				$exploded_location_entry=explode("_",$location_entry);
				$entry_info_entry[$exploded_location_entry[0]] = $exploded_location_entry[1];
				mf_update_entry_field($entry_info_entry_id,$exploded_location_entry[0],$exploded_location_entry[1]);
			}
		}
		if (!empty($flags_change)){
			foreach($flags_change as $flags_entry){
				$exploded_flags_entry=explode("_",$flags_entry);
				$entry_info_entry[$exploded_flags_entry[0]] = $exploded_flags_entry[1];
				mf_update_entry_field($entry_info_entry_id,$exploded_flags_entry[0],$exploded_flags_entry[1]);
			}
		}
		/*if (!empty($location_comment_change))
		{*/
		$entry_info_entry['307'] = $location_comment_change;
    mf_update_entry_field($entry_info_entry_id,'307',$location_comment_change);

		//}
	}
}

/* Modify Set Entry Status */
function set_entry_status($lead,$form){
	//$location_change=$_POST['entry_info_location_change'];
	//$flags_change=$_POST['entry_info_flags_change'];
	$location_comment_change=$_POST['entry_location_comment'];
	$acceptance_status_change=$_POST['entry_info_status_change'];
	$entry_info_entry_id=$_POST['entry_info_entry_id'];
	$acceptance_current_status = $lead['303'];

	$is_acceptance_status_changed = (strcmp($acceptance_current_status, $acceptance_status_change) != 0);

	if (!empty($entry_info_entry_id)){
		if (!empty($acceptance_status_change)){
      //Update Field for Acceptance Status
      $entry_info_entry['303'] = $acceptance_status_change;
      mf_update_entry_field($entry_info_entry_id,'303',$acceptance_status_change);
      //Reload entry to get any changes in status
      $lead['303'] = $acceptance_status_change;

			//Handle acceptance status changes
			if ($is_acceptance_status_changed ){
        if($acceptance_status_change=='Accepted'){
          /* If the status is accepted, trigger a cron job to
           *    generate EventBrite Tickets.
           *  The cron job will trigger action sidebar_entry_update
           */
          wp_schedule_single_event(time() + 1,'sidebar_entry_update', array($entry_info_entry_id));
          global $wpdb;
          //lock space size attribute if set
          $wpdb->get_results('update `wp_rmt_entry_attributes` set `lockBit` = 1 where attribute_id =  2 and entry_id='. $lead['id']);
        }
				//Create a note of the status change.
				$results=mf_add_note( $entry_info_entry_id, 'EntryID:'.$entry_info_entry_id.' status changed to '.$acceptance_status_change);
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

/* Copy entry record into specific form*/
function duplicate_entry_id($lead,$form){
  $form_change         = $_POST['entry_form_copy']; //selected form field
  $entry_info_entry_id = $_POST['entry_info_entry_id']; //id to copy

  error_log('$duplicating entry id ='.$entry_info_entry_id.' into form '.$form_change);

  $result     = duplicate_entry_data($form_change,$entry_info_entry_id);
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
  $user_id = $current_user && $current_user->ID ? $current_user->ID : 'NULL';
  $user_agent = GFCommon::truncate_url($_SERVER["HTTP_USER_AGENT"], 250);
  $currency = GFCommon::get_currency();
  $source_url = GFCommon::truncate_url(RGFormsModel::get_current_page_url(), 200);
  $wpdb->query($wpdb->prepare("INSERT INTO $lead_table(form_id, ip, source_url, date_created, user_agent, currency, created_by) VALUES(%d, %s, %s, utc_timestamp(), %s, %s, {$user_id})", $form_change, RGFormsModel::get_ip(), $source_url, $user_agent, $currency));
  $lead_id = $wpdb->insert_id;
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

/* Modify Form Id Status */
function set_form_id($lead,$form){
	$form_change=$_POST['entry_form_change'];
	$entry_info_entry_id=$_POST['entry_info_entry_id'];

	error_log('$form_change='.$form_change);
	error_log('$$entry_info_entry_id='.$entry_info_entry_id);
	$entry=GFAPI:: get_entry($entry_info_entry_id);

	$is_form_id_changed = (strcmp($entry['form_id'], $form_change) != 0);

	if (!empty($entry_info_entry_id)){
		if (!empty($is_form_id_changed)){
			//Update Field for Acceptance Status
			$result = update_entry_form_id($entry,$form_change);
			error_log('UPDATE RESULTS = '.print_r($result,true));

      //add note about form change
      $newForm = RGFormsModel::get_form_meta($form_change);
      mf_add_note( $entry_info_entry_id, 'Entry changed from '.$form['title'].' to '.$newForm['title']);
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

	$lead_table = GFFormsModel::get_lead_table_name();
	$lead_detail_table = GFFormsModel::get_lead_details_table_name();
	$lead_meta_table = GFFormsModel::get_lead_meta_table_name();
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



/* Modify Set Entry Status */
function set_entry_schedule($lead,$form){
	$entry_schedule_change = (isset($_POST['entry_schedule_change']) ? $_POST['entry_schedule_change'] : '');
	$entry_schedule_start  = (isset($_POST['datetimepickerstart'])   ? $_POST['datetimepickerstart']   : '');
	$entry_schedule_end    = (isset($_POST['datetimepickerend'])     ? $_POST['datetimepickerend']     : '');
	$entry_info_entry_id   = (isset($_POST['entry_info_entry_id'])   ? $_POST['entry_info_entry_id']   : '');

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
      SELECT $entry_info_entry_id,$location_id,wp_mf_faire.faire,'$entry_schedule_start', '$entry_schedule_end'
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

/* Modify Set Entry Status */
function delete_entry_schedule($lead,$form){
  $return = 'No Data';

	//$entry_schedule_change=$_POST['entry_schedule_change'];
	$delete_entry_schedule = (isset($_POST['delete_entry_id'])    ? implode(',',($_POST['delete_entry_id']))    : '');
  $delete_entry_location = (isset($_POST['delete_location_id']) ? implode(',',($_POST['delete_location_id'])) : '');

	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

  //delete schedule and location
	if ($delete_entry_schedule != ''){
    //delete from schedule and location table
    $delete_query =  "DELETE `wp_mf_schedule`, `wp_mf_location`
                        FROM `wp_mf_schedule`, `wp_mf_location`
                       WHERE wp_mf_schedule.ID IN ($delete_entry_schedule) and location_id=wp_mf_location.id";
    //MySqli Insert Query
    $mysqlresults = $mysqli->query($delete_query);
    if($mysqlresults){
      $return = 'Success! <br />';
    }else{
      $return = 'Error :'.$delete_query.':('. $mysqli->errno .') '. $mysqli->error;
    };
  }

  //delete location only
	if ($delete_entry_location != ''){
    //delete from schedule and location table
    $delete_query =  "DELETE FROM `wp_mf_location` WHERE wp_mf_location.ID IN ($delete_entry_location)";
    //MySqli Insert Query
    $mysqlresults = $mysqli->query($delete_query);
    if($mysqlresults){
      $return = 'Success! <br />';
    }else{
      $return = 'Error :'.$delete_query.':('. $mysqli->errno .') '. $mysqli->error;
    };
  }

  echo $return;
}

/* Modify Set Entry Status */
function set_entry_location($lead,$form,&$location_id=''){
	$entry_schedule_change      = $_POST['entry_location_subarea_change'];
	$entry_info_entry_id        = $_POST['entry_info_entry_id'];
	$update_entry_location_code = $_POST['update_entry_location_code'];

	//$form_id=$lead['form_id'];

	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
/*
	$insert_query = sprintf("INSERT INTO `wp_mf_location` (`entry_id`, `subarea_id`, `location`, `location_element_id`)
				Select $entry_info_entry_id, wp_mf_faire_subarea.ID,'$update_entry_location_code', 3
				from wp_mf_faire_subarea
          join wp_mf_faire_area on wp_mf_faire_subarea.area_id = wp_mf_faire_area.ID
          join wp_mf_faire on find_in_set($form_id,form_ids) > 0 and wp_mf_faire_area.faire_id=wp_mf_faire.ID
				where subarea='$entry_schedule_change';");*/
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

/* Modify Set Entry Status */
function delete_entry_location($lead,$form){
	$entry_schedule_change=$_POST['entry_location_change'];
	$delete_entry_schedule=implode(',',($_POST['delete_location_id']));

	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	if (isset($delete_entry_schedule)){
    $delete_query = sprintf("DELETE FROM `wp_mf_location`
        WHERE ID IN ($delete_entry_schedule)");
    //MySqli Insert Query
    $mysqlresults = $mysqli->query($delete_query);
    if($mysqlresults){
      echo 'Success! <br />';
    }else{
      echo ('Error :'.$delete_query.':('. $mysqli->errno .') '. $mysqli->error);
    };
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
		$entry_url = get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=mf_entries&view=mfentry&id=' . $form['id'] . '&lid=' . rgar( $lead, 'id' );
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

function delete_note_sidebar($notes){
    RGFormsModel::delete_notes( $notes);
}
function wpse27856_set_content_type(){
	return "text/html";
}

/*
 * Add a single note
 */
function mf_add_note($leadid,$notetext){
	global $current_user;
	$user_data = get_userdata( $current_user->ID );
	RGFormsModel::add_note( $leadid, $current_user->ID, $user_data->display_name, $notetext );
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