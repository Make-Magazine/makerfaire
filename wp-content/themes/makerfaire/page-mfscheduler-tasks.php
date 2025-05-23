<?php

/**
 * Template Name: mfcheduler-tasks
 */
// Check that all required fields are passed before running anything and assign them to variables
//error_reporting ( E_ALL );
//ini_set ( 'display_errors', '1' );

global $wp_query;
http_response_code(200);

if ($_SERVER ['REQUEST_METHOD'] == 'GET') {
  header('Content-Type: application/json');
  $faire_id = $_GET['faire_id'];

  $request = json_decode(file_get_contents('php://input'));
  $result = readWithAssociation($faire_id, '');
  $jsonencodedresults = safe_json_encode($result ['data']);
  echo $jsonencodedresults;
  exit();
} elseif ($_SERVER ['REQUEST_METHOD'] == 'POST') {
  header('Content-Type: application/json');

  $model = json_decode(file_get_contents('php://input'));
  $type = $_GET['type'];
  $faire_id = $_GET['faire_id'];

  switch ($type) {
    case 'create' :
      $subareaid = $model->SubareaID;
      $sched_type = $model->PresentationType;
      $start = date('Y-m-d H:i:s', strtotime($model->Start));
      $end = date('Y-m-d H:i:s', strtotime($model->End));
      $entries = (isset($model->Entries[0]) ? $model->Entries[0] : array());
      $model->locationID = add_entry_schedule($faire_id, $subareaid, $start, $end, $entries, $sched_type);
      $model->StatusColor = get_statuscolor_bylocationID($model->locationID);
      $result = $model; // $result=1 ; // $result = $result->createWithAssociation('Meetings', 'MeetingAttendees', $columns, $request->models, 'MeetingID', array('Attendees' => 'AttendeeID'));
      break;
    case 'update' :
      $locationID = $model->locationID;
      $subareaid = $model->SubareaID;
      $sched_type = $model->PresentationType;
      $start = date('Y-m-d H:i:s', strtotime($model->Start));
      $end = date('Y-m-d H:i:s', strtotime($model->End));
      $entries = $model->Entries [0];
      $result = update_entry_schedule($locationID, $faire_id, $subareaid, $start, $end, $entries, $sched_type);
      break;
    case 'destroy' :
      $locationID = $model->locationID;
      remove_entry_schedule($locationID);
      $result = $locationID;
      break;
    default :
      break;
  }
  echo safe_json_encode($result, JSON_NUMERIC_CHECK);

  exit();
}
/* Get Entry Status by LocationID */

function get_statuscolor_bylocationID($locationID) {
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $select_query = sprintf("SELECT 
      wp_mf_entity.status
	FROM
	    `wp_mf_schedule`
			JOIN 
		 wp_mf_location location ON  location.entry_id = `wp_mf_schedule`.`entry_id`
	           AND wp_mf_schedule.location_id = location.ID
	        JOIN
	    wp_mf_faire ON wp_mf_schedule.faire = wp_mf_faire.faire
			JOIN 
		wp_mf_entity ON wp_mf_entity.lead_id = wp_mf_schedule.entry_id
		
	WHERE
	     location.ID = '$locationID'");
  $total = 0;
  $result = $mysqli->query($select_query);
  $schedule_entries = array();
  $returnStatus = "";
  if ($result) {
    while ($row = $result->fetch_assoc()) {
      $returnStatus = $row ['status'];
    }
  } else {
    echo ('Error :' . $select_query . ':(' . $mysqli->errno . ') ' . $mysqli->error);
  }

  return status_to_color($returnStatus);
}

/* Modify Set Entry Status */

function add_entry_schedule($faire_id, $subarea_id, $entry_schedule_start, $entry_schedule_end, $entry_info_entry_id, $sched_type) {
  // $entry_schedule_change = (isset($_POST['entry_schedule_change']) ? $_POST['entry_schedule_change'] : '');
  // $entry_schedule_start = (isset($_POST['datetimepickerstart']) ? $_POST['datetimepickerstart'] : '');
  // $entry_schedule_end = (isset($_POST['datetimepickerend']) ? $_POST['datetimepickerend'] : '');
  // $entry_info_entry_id = (isset($_POST['entry_info_entry_id']) ? $_POST['entry_info_entry_id'] : '');
  // location fields
  $location_id = 'NULL';
  add_entry_location($entry_info_entry_id, $subarea_id, $location_id);

  // $form_id=$lead['form_id'];
  // set the location
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $insert_query = sprintf("INSERT INTO `wp_mf_schedule`
					(`entry_id`,
					location_id,
					`faire`,
					`start_dt`,
					`end_dt`,
          `type`)
					SELECT $entry_info_entry_id,$location_id,wp_mf_faire.faire,'$entry_schedule_start', '$entry_schedule_end','$sched_type'
					from wp_mf_faire where faire= '$faire_id'
					");

  // MySqli Insert Query
  $insert_row = $mysqli->query($insert_query);
  if ($insert_row) {
    $result_id = $mysqli->insert_id;
  } else {
  	$result_id = 0;
    echo ('Error :' . $insert_query . ':(' . $mysqli->errno . ') ' . $mysqli->error);
  }

  return $result_id;
}

/* Modify Set Entry Status */

function update_entry_schedule($locationID, $faire_id, $subarea_id, $entry_schedule_start, $entry_schedule_end, $entry_info_entry_id, $sched_type) {
  // set the location
  $result_message = "";
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    $result_message = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $insert_query = sprintf("UPDATE `wp_mf_schedule`
				SET
				`entry_id` = $entry_info_entry_id,
				`start_dt` = '$entry_schedule_start',
				`end_dt` = '$entry_schedule_end',
        `type` = '$sched_type'
				WHERE `ID` = $locationID ");
  $insert_row = $mysqli->query($insert_query);
  if ($insert_row) {
    $insert_query = sprintf("UPDATE `wp_mf_location`,`wp_mf_schedule`
					SET `wp_mf_location`.`entry_id` = $entry_info_entry_id,
      				`wp_mf_location`.`subarea_id` = $subarea_id
				WHERE `wp_mf_schedule`.location_id = `wp_mf_location`.ID AND `wp_mf_schedule`.ID = $locationID");
    $insert_row = $mysqli->query($insert_query);
    if ($insert_row) {
      $result_id = $mysqli->insert_id;
    } else {
      $result_message = ('Error :' . $insert_query . ':(' . $mysqli->errno . ') ' . $mysqli->error);
    }
  } else {
    $result_message = ('Error :' . $insert_query . ':(' . $mysqli->errno . ') ' . $mysqli->error);
  }
  // MySqli Insert Query


  return $result_message;
}

/* Modify Set Entry Status */

function remove_entry_schedule($schedule_id) {
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $delete_query = sprintf("   DELETE s,l
      FROM `wp_mf_schedule` s
      JOIN 
		 wp_mf_location l ON  l.entry_id = s.`entry_id`
	           AND s.location_id = l.ID
     WHERE s.`ID` = $schedule_id");
  // MySqli Insert Query
  $mysqlresults = $mysqli->query($delete_query);
  if ($mysqlresults) {
    $result_id = $schedule_id;
  } else {
    echo ('Error :' . $delete_query . ':(' . $mysqli->errno . ') ' . $mysqli->error);
  }
  ;
  return $result_id;
}

function read_schedule($faire_id, $subarea_id, &$total) {
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $select_query = sprintf("SELECT  CONCAT(wp_mf_maker.`First Name`, ' ', wp_mf_maker.`Last Name`) as maker_name, 
	    `wp_mf_schedule`.`ID`,
	    `wp_mf_schedule`.`entry_id`,
	    location.subarea_id,
	    `wp_mf_schedule`.`faire`,
	   `wp_mf_schedule`.`start_dt`,
	    `wp_mf_schedule`.`end_dt`,
        wp_mf_entity.presentation_title,
        wp_mf_entity.presentation_type,
        wp_mf_entity.desc_short,
      `wp_mf_schedule`.`type`,
      wp_mf_entity.status, wp_mf_entity.form_id
        
	FROM
	    `wp_mf_schedule`
			JOIN 
		 wp_mf_location location ON  location.entry_id = `wp_mf_schedule`.`entry_id`
	           AND wp_mf_schedule.location_id = location.ID
	        JOIN
	    wp_mf_faire ON wp_mf_schedule.faire = wp_mf_faire.faire
			JOIN 
		wp_mf_entity ON wp_mf_entity.lead_id = wp_mf_schedule.entry_id
			LEFT JOIN 
    wp_mf_maker_to_entity ON wp_mf_entity.lead_id = wp_mf_maker_to_entity.entity_id and wp_mf_maker_to_entity.`maker_type`='presenter'
			LEFT JOIN 
		wp_mf_maker ON wp_mf_maker.maker_id = wp_mf_maker_to_entity.maker_id
	WHERE
	     wp_mf_faire.faire = '$faire_id'
	ORDER BY  start_dt ASC");
  $total = 0;
  $result = $mysqli->query($select_query);
  $schedule_entries = array();

  if ($result) {
    while ($row = $result->fetch_assoc()) {
      //error_log(print_r($row, TRUE));
      $total ++;
      // order entries by subarea(stage), then date
      $stage = $row ['subarea_id'];
      $status = $row ['status'];
      $start_dt = new DateTime(@$row ['start_dt']);
      // $start_dt_formatted = date('Y/j/n h:i:s A',$start_dt);
      $end_dt = new DateTime(@$row ['end_dt']);
      $sched_type = @$row ['type'];
      $schedule_entry_id = $row ['ID'];
      $entry_ids = array(
          $row['entry_id']
      );
      $names = array(
          $row['presentation_title']
      );
      $form = $row ['form_id'];
      $presentername = (isset($row['maker_name'])) ? $row['maker_name'] : 'TBD';
      $title = preg_replace("/[^a-z0-9 ]/i", "", $row['presentation_title']) . ' (Maker: ' . $presentername . ') ';
      $maker_name = $row['maker_name'];

      // build array
      /* $schedule_entries [] = array (
        'locationID' => $schedule_entry_id,
        'start' => $start_dt,
        'end' => $end_dt,
        'isAllDay' => false,
        'description' => '',
        'recurrenceId' => null,
        'recurrenceRule' => null,
        'recurrenceException' => null,
        'ownerID' => 2,
        'title' => 'Test',
        'subareaId' => $stage,
        'entries' => $entry_ids
        );
       */
      $schedule_entries [] = array(
          'locationID' => $schedule_entry_id,
          'Start' => $start_dt->format(DateTime::ISO8601),
          'End' => $end_dt->format(DateTime::ISO8601),
          'IsAllDay' => false,
          'SubareaID' => $stage,
          'Entries' => $entry_ids,
          'Names' => $names,
          'Form' => $form,
          'Event' => $maker_name,
          'Title' => $title,
          'StatusColor' => status_to_color($status),
          'PresentationType' => $sched_type);
    }
  } else {
    echo ('Error :' . $select_query . ':(' . $mysqli->errno . ') ' . $mysqli->error);
  }

  return $schedule_entries;
}

function status_to_color($entry_status) {
  $result = '';
  switch ($entry_status) {
    case 'Accepted' :
      $result = '#3fafed'; // $result = $result->createWithAssociation('Meetings', 'MeetingAttendees', $columns, $request->models, 'MeetingID', array('Attendees' => 'AttendeeID'));
      break;
    case 'Proposed' :
    case 'Wait List' :
      $result = '#005E9A'; // $result = $result->updateWithAssociation('Meetings', 'MeetingAttendees', $columns, $request->models, 'MeetingID', array('Attendees' => 'AttendeeID'));
      break;
    case 'Cancelled' :
    case 'No Show' :
    case 'Rejected' :
      $result = '#ed1d21'; // $result = $result->destroyWithAssociation('Meetings', 'MeetingAttendees', $request->models, 'MeetingID');
      break;
    default :
      $result = '#333'; // $result = $result->readWithAssociation('Meetings', 'MeetingAttendees', 'MeetingID', array('AttendeeID' => 'Attendees'), array('MeetingID', 'RoomID'), $request);
      break;
  }

  return $result;
}

/* Modify Set Entry Status */

function add_entry_location($entry_id, $subarea_id, &$location_id = '') {
  $entry_info_entry_id = $entry_id;

  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  $insert_query = sprintf("
				INSERT INTO `wp_mf_location`
				(`entry_id`,
				`subarea_id`,
				`location`,
				`location_element_id`)
				Select $entry_info_entry_id
				,$subarea_id
				,''
				,3;");
  // MySqli Insert Query
  $insert_row = $mysqli->query($insert_query);
  if ($insert_row) {
    $result_id = $mysqli->insert_id;
  } else {
  	$result_id = 0;
    echo ('Error :' . $insert_query . ':(' . $mysqli->errno . ') ' . $mysqli->error);
  }
  
  $location_id = $result_id;
}

function readWithAssociation($faire_id, $subarea_id) {
  $result = array();
  $total = 0;
  $schedule_entries = read_schedule($faire_id, $subarea_id, $total);
  $result ['total'] = $total;
  $result ['data'] = $schedule_entries;
  return $result;
}

function safe_json_encode($value) {
  if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
    $encoded = json_encode($value, JSON_PRETTY_PRINT);
  } else {
    $encoded = json_encode($value);
  }
  switch (json_last_error()) {
    case JSON_ERROR_NONE:
      return $encoded;
    case JSON_ERROR_DEPTH:
      return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
    case JSON_ERROR_STATE_MISMATCH:
      return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
    case JSON_ERROR_CTRL_CHAR:
      return 'Unexpected control character found';
    case JSON_ERROR_SYNTAX:
      return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
    case JSON_ERROR_UTF8:
      $clean = utf8ize($value);
      return safe_json_encode($clean);
    default:
      return 'Unknown error'; // or trigger_error() or throw new Exception()
  }
}

function utf8ize($mixed) {
  if (is_array($mixed)) {
    foreach ($mixed as $key => $value) {
      $mixed[$key] = utf8ize($value);
    }
  } else if (is_string($mixed)) {
    return utf8_encode($mixed);
  }
  return $mixed;
}

?>