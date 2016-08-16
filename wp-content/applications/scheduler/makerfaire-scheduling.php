<?php

include('../../../wp-blog-header.php');

header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Home</title>
        <link href="../content/css/web/kendo.common.min.css" rel="stylesheet" />
        <link href="../content/css/web/kendo.rtl.min.css" rel="stylesheet" />
        <link href="../content/css/web/kendo.default.min.css" rel="stylesheet" />
        <link href="../content/css/web/kendo.default.mobile.min.css" rel="stylesheet" />
        <link href="../content/css/dataviz/kendo.dataviz.min.css" rel="stylesheet" />
        <link href="../content/css/dataviz/kendo.dataviz.default.min.css" rel="stylesheet" />
        <link href="styles/examples.css" rel="stylesheet">
 		    <link href="../woahbar/woahbar.css" rel="stylesheet">
        
        <script src="../content/js/jquery.min.js"></script>
        <script src="../woahbar/woahbar.js"></script>
        <script src="../content/js/kendo.all.min.js"></script>
        <script src="../content/js/kendo.timezones.min.js"></script>
        <script src="../content/shared/js/console.js"></script>
        <script src="../content/shared/js/prettify.js"></script>
    </head>
    <body>
<?php 
use Kendo\Template;
//error_reporting ( E_ALL );
//ini_set ( 'display_errors', '1' );

require_once ("../../../wp-load.php");
 $current_user = wp_get_current_user(); 
require_once '../lib/Kendo/Autoload.php';
$faire_id = isset($_GET['faire_id']) ? $_GET['faire_id']  : 'BA16';

?>


<form>
<div class="k-floatwrap k-header k-scheduler-toolbar">
<?php
$locations_array = get_entry_locations ( $faire_id );
$select = new \Kendo\UI\MultiSelect('locationfilters');
$select->dataSource ( $locations_array )
->change('onChange')
->value(array('302','256','255','300','284','306','307'))
->dataTextField ( 'text' )
->dataValueField ( 'value' )
->placeholder ( 'Filter location ...' );;

echo $select->render();

?>
</div>
<?php
$scheduler = create_makerfaire_scheduler ( $faire_id );
echo $scheduler->render ();
?>

<script>
    $(document).ready(function() {
        // create ComboBox from select HTML element
        var input = $("#input").data("kendoComboBox");
        var select = $("#select").data("kendoComboBox");
        onChange(null);
        
    });
</script>
<script id="presentation-template" type="text/x-kendo-template">
# if(entries){ #
 <a target="_blank" title="#: title #" href="/wp-admin/admin.php?page=mf_entries&view=mfentry&lid=#: entries[0] #">#: entries[0] #</a>
# } #
<p>#: title #</p>
</script>
<!-- begin#woahbar -->
<div class="woahbar" style="display: none;">
	<span> <a class="woahbar-link" href="/wp-admin/">Back to wp-admin</a> Howdy, <?php echo $current_user->user_login;?>
	</span> <a class="close-notify" onclick="woahbar_hide();"> <img
		class="woahbar-up-arrow"
		src="/wp-content/applications/woahbar/woahbar-up-arrow.png" /></a>
</div>
<div class="woahbar-stub" style="display: none;">
	<a class="show-notify" onclick="woahbar_show();"> <img
		class="woahbar-down-arrow"
		src="/wp-content/applications/woahbar/woahbar-down-arrow.png" />
	</a>
</div>
<style>
/*
        Use the DejaVu Sans font for display and embedding in the PDF file.
        The standard PDF fonts have no support for Unicode characters.
    */
    
.k-scheduler-table a
{
    color: #eb1b26;
    font-weight: bold;
}
.k-scheduler {
	font-family: "DejaVu Sans", "Arial", sans-serif;
}

/* Hide toolbar, navigation and footer during export */
.woahbar,.k-pdf-export .k-scheduler-toolbar,.k-pdf-export .k-scheduler-navigation .k-nav-today,.k-pdf-export .k-scheduler-navigation .k-nav-prev,.k-pdf-export .k-scheduler-navigation .k-nav-next,.k-pdf-export .k-scheduler-footer
	{
	display: none;
}
</style>

<!-- Load Pako ZLIB library to enable PDF compression -->
<script src="../content/shared/js/pako.min.js"></script>
<style>
.k-scheduler-layout {
	table-layout: fixed;
}

.k-scheduler-layout>tbody>tr>td:first-child {
	width: 80px;
}

/* .k-scheduler-content .k-scheduler-table,.k-scheduler-header .k-scheduler-table
	{
	 width: 3000px ;
} 
*/

</style>
<script>
function onChange(e) {
    if ("kendoConsole" in window) {
    	 var multiSelect = $("#locationfilters").data("kendoMultiSelect");
         var checked = multiSelect.value();
    		  kendoConsole.log("event: select (" + checked + ")" );

         var multiSelect = $("#locationfilters").data("kendoMultiSelect");
	     var checked = multiSelect.value();

		 var filter = {
		    logic: "or",
		    filters: $.map(checked, function(value) {
		      return {
		        operator: "eq",
		        field: "value",
		        value: value
		      };
		    })
		  };
		   
		  var scheduler = $("#scheduler").data("kendoScheduler"); 
		  //filter the resource data source
		  scheduler.resources[0].dataSource.filter(filter); 
		   
		  scheduler.view(scheduler.view().name); //refresh the currunt view 
		}

};
</script>

<!-- end#woahbar -->

</body></html>
<?php
function get_entry_locations($faire_id) {
	$mysqli = new mysqli ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$locations = array ();
	
	$result = $mysqli->query ( "SELECT 	 `wp_mf_faire_subarea`.`id`,
			`wp_mf_faire_subarea`.`subarea`
			FROM  wp_mf_faire_subarea, wp_mf_faire_area, wp_mf_faire
			where faire='$faire_id'
			and   wp_mf_faire_subarea.area_id = wp_mf_faire_area.ID
			and   wp_mf_faire_area.faire_id   = wp_mf_faire.ID" ) or trigger_error ( $mysqli->error );
	
	if ($result) {
		while ( $row = $result->fetch_row () ) {
			$subarea = $row [1];
			$subarea_id = $row [0];
			
			$locations [] = array (
					'text' => $subarea,
					'value' => $subarea_id,
					'color' => 'deepskyblue' 
			);
		}
	}
	// Create Update button for sidebar entry management
	return $locations;
}
function get_entries($faire_id) {
	$mysqli = new mysqli ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$entries = array ();
	
	$result = $mysqli->query ( "SELECT lead_id,presentation_title ,status
				FROM wp_mf_entity
				where faire='$faire_id'" ) or trigger_error ( $mysqli->error );
	
	if ($result) {
		while ( $row = $result->fetch_row () ) {
			$entry = preg_replace ( "/[^A-Za-z0-9 ]/", '', $row [1] );
			$entry_id = $row [0];
			$entry_status = $row [2];
			$entry_color = status_to_color ( $entry_status );
			$entry_title = "$entry_id ($entry - $entry_status)";
			$entries [] = array (
					'text' => $entry_title,
					'value' => $entry_id,
					'color' => $entry_color 
			);
		}
	}
	// Create Update button for sidebar entry management
	return $entries;
}
function status_to_color($entry_status) {
	$result = '';
	switch ($entry_status) {
		case 'Accepted' :
			$result = '#90EE90'; // $result = $result->createWithAssociation('Meetings', 'MeetingAttendees', $columns, $request->models, 'MeetingID', array('Attendees' => 'AttendeeID'));
			break;
		case 'Proposed' :
		case 'Wait List' :
			$result = '#FAFAD2'; // $result = $result->updateWithAssociation('Meetings', 'MeetingAttendees', $columns, $request->models, 'MeetingID', array('Attendees' => 'AttendeeID'));
			break;
		case 'Cancelled' :
		case 'No Show' :
		case 'Rejected' :
			$result = '#F08080'; // $result = $result->destroyWithAssociation('Meetings', 'MeetingAttendees', $request->models, 'MeetingID');
			break;
		default :
			$result = '#E0FFFF'; // $result = $result->readWithAssociation('Meetings', 'MeetingAttendees', 'MeetingID', array('AttendeeID' => 'Attendees'), array('MeetingID', 'RoomID'), $request);
			break;
	}
	
	return $result;
}


function create_makerfaire_scheduler($faire_id) {
	$transport = new \Kendo\Data\DataSourceTransport ();
	
	$create = new \Kendo\Data\DataSourceTransportCreate ();
	
	$create->url ( 'makerfaire-scheduling-tasks.php?faire_id='.$faire_id.'&type=create' )->contentType ( 'application/json' )->type ( 'POST' )->dataType('json');
	
	$read = new \Kendo\Data\DataSourceTransportRead ();
	
	$read->url ( 'makerfaire-scheduling-tasks.php?faire_id='.$faire_id.'&type=read' )->contentType ( 'application/json' )->type ( 'GET' )->dataType('json');
	
	$update = new \Kendo\Data\DataSourceTransportUpdate ();
	
	$update->url ( 'makerfaire-scheduling-tasks.php?faire_id='.$faire_id.'&type=update' )->contentType ( 'application/json' )->type ( 'POST' )->dataType('json');
	
	$destroy = new \Kendo\Data\DataSourceTransportDestroy ();
	
	$destroy->url ( 'makerfaire-scheduling-tasks.php?faire_id='.$faire_id.'&type=destroy' )->contentType ( 'application/json' )->type ( 'POST' )->dataType('json');
	
	$transport->create ( $create )->read ( $read )->update ( $update )->destroy ( $destroy )->parameterMap ( 'function(data) {
              return kendo.stringify(data);
          }' );
	
	$model = new \Kendo\Data\DataSourceSchemaModel ();
	
	$locationIdField = new \Kendo\Data\DataSourceSchemaModelField ( 'locationID' );
	$locationIdField->type ( 'number' )->from ( 'locationID' )->nullable ( true );
	
	$titleField = new \Kendo\Data\DataSourceSchemaModelField ( 'title' );
	$titleField->from ( 'Title' )->defaultValue ( 'No title' )->validation ( array (
			'required' => false 
	) );
	
	$startField = new \Kendo\Data\DataSourceSchemaModelField ( 'start' );
	$startField->type ( 'date' )->from ( 'Start' );
	
	$endField = new \Kendo\Data\DataSourceSchemaModelField ( 'end' );
	$endField->type ( 'date' )->from ( 'End' );
	
	$isAllDayField = new \Kendo\Data\DataSourceSchemaModelField ( 'isAllDay' );
	$isAllDayField->type ( 'boolean' )->from ( 'IsAllDay' );
		
	$subareaIdField = new \Kendo\Data\DataSourceSchemaModelField ( 'subareaId' );
	$subareaIdField->from ( 'SubareaID' )->nullable ( true );
	
	$entriesField = new \Kendo\Data\DataSourceSchemaModelField ( 'entries' );
	$entriesField->from ( 'Entries' )->nullable ( true );
	
	$model->id ( 'locationID' )->addField ( $locationIdField )->addField ( $titleField )->addField ( $startField )->addField ( $endField )->addField ( $isAllDayField )->addField ( $subareaIdField )->addField ( $entriesField );
	
	$schema = new \Kendo\Data\DataSourceSchema ();
	$schema->model ( $model );
	
	$dataSource = new \Kendo\Data\DataSource ();
	$dataSource->transport ( $transport )->schema ( $schema )->batch ( false );
	
	
	
	
	$subareasResource = new \Kendo\UI\SchedulerResource ();
	$locations_array = get_entry_locations ( $faire_id );
	
	$subareasResource->field ( 'subareaId' )->title ( 'Stage' )->name ( 'Stages' )->dataSource ( $locations_array );
	
	
	$entries = get_entries ( $faire_id );
	$entriesResource = new \Kendo\UI\SchedulerResource ();
	$entriesResource->field ( 'entries' )->title ( 'Presenter' )->multiple ( true )->name ( 'Presenters' )->dataSource ( $entries );
	
	$pdf = new \Kendo\UI\SchedulerPdf ();
	$pdf->fileName ( 'Kendo UI Scheduler Export.pdf' )->proxyURL ( 'makerfaire-scheduling.php?type=save' );
	
	$scheduler = new \Kendo\UI\Scheduler ( 'scheduler' );
	
	$scheduler->eventTemplateId ( 'presentation-template' )
		//->editable(array('update' => 'true','template'=>'customEditorTemplate'))
		->timezone('UTC')
		/* NOTE: For Next Faire, use Timezones from Faire table and use Local Timezone
		
		timezone: "Europe/London", // Use the London timezone*/
	
		->currentTimeMarker(false)
		->date(new DateTime ( '2016/10/01 00:00', new DateTimeZone ( 'UTC' ) ) )->height ( 900 )->pdf ( $pdf )->addToolbarItem ( new \Kendo\UI\SchedulerToolbarItem ( 'pdf' ) )->addResource ( $subareasResource, $entriesResource )->group ( array (
			'resources' => array (
					'Stages' 
			) 
	) )->addView ( array (
			'type' => 'day',
			'majorTick' => 30,
			'showWorkHours' => true,
			'workWeekEnd' => 7,
			'workDayStart' => new DateTime ( '2015/1/1 15:00', new DateTimeZone ( 'UTC' ) ),
			'workDayEnd' => new DateTime ( '2015/1/2 00:00', new DateTimeZone ( 'UTC' ) )
	), array (
			'type' => 'workWeek',
			'majorTick' => 30,
			'selected' => true,
			'workWeekStart' => 5,
			'workWeekEnd' => 7,
			'showWorkHours' => true,
			'workDayStart' => new DateTime ( '2015/1/1 15:00', new DateTimeZone ( 'UTC' ) ),
			'workDayEnd' => new DateTime ( '2015/1/2 00:00', new DateTimeZone ( 'UTC' ) )
	), 'agenda' )->dataSource ( $dataSource );
	
	return $scheduler;
}
function debug_to_console($data) {
	if (is_array ( $data ))
		$output = "<script>console.log( 'Debug Objects: " . implode ( ',', $data ) . "' );</script>";
	else
		$output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
	
		echo $output;
}


?>