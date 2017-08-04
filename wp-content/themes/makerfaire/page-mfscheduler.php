<?php
/**
 * Template Name: mfcheduler
 */

// Check that all required fields are passed before running anything and assign them to variables
 
get_header('admin'); 
global $wp_query;

$faire_id = ( ! empty( $wp_query->query_vars['faire_id'] ) ? sanitize_text_field( $wp_query->query_vars['faire_id'] ) : null );
//$_SERVER["DOCUMENT_ROOT"] . '/wp-blog-header.php';
//include('../../../../wp-blog-header.php');




$current_user = wp_get_current_user();
require_once 'lib/Kendo/Autoload.php';
$default_locations = isset($_GET['loc']) ? $_GET['loc']  : str_getcsv(get_default_locations($faire_id));
$default_locations =  isset($default_locations) ? $default_locations : "414";
?>
<script>
var $ = jQuery.noConflict();
</script>
<form>
<div class="k-floatwrap k-header k-scheduler-toolbar">
<?php
$locations_array = get_entry_locations ( $faire_id );
$select = new \Kendo\UI\MultiSelect('locationfilters');
// Set Defaults here in the value array, by stage id.
$select->dataSource ( $locations_array )
->change('onChange')
->value($default_locations)
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
    jQuery(document).ready(function() {
        // create ComboBox from select HTML element
        var input = jQuery("#input").data("kendoComboBox");
        var select = jQuery("#select").data("kendoComboBox");
        onChange(null);

    });
</script>
<script id="presentation-template" type="text/x-kendo-template">
# if(entries){ #
 <a target="_blank" title="#: title #" href="/wp-admin/admin.php?page=gf_entries&view=entry&id=9&lid=#: entries[0] #">#: entries[0] #</a>
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
	width: 10%;
}

.k-scheduler-times-all-day, .k-scheduler-header-all-day { display: none;}

/* .k-scheduler-content .k-scheduler-table,.k-scheduler-header .k-scheduler-table
	{
	 width: 3000px ;
}
*/

</style>
<script>
function onChange(e) {
    if ("kendoConsole" in window) {
    	 var multiSelect = jQuery("#locationfilters").data("kendoMultiSelect");
         var checked = multiSelect.value();
    		  kendoConsole.log("event: select (" + checked + ")" );

         var multiSelect = jQuery("#locationfilters").data("kendoMultiSelect");
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

		  var scheduler = jQuery("#scheduler").data("kendoScheduler");
		  //filter the resource data source
		  scheduler.resources[0].dataSource.filter(filter);

		  scheduler.view(scheduler.view().name); //refresh the currunt view
		}

};
</script>

<?php
function get_default_locations($faire_id) {
	$mysqli = new mysqli ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$default_locations = "";

	$result = $mysqli->query ( "SELECT 	`wp_mf_faire`.`default_locations`
			FROM wp_mf_faire
			where faire='$faire_id'" ) or trigger_error ( $mysqli->error );

	if ($result) {
		while ( $row = $result->fetch_row () ) {
			$default_locations = $row [0];	
      
		}
	}
	// Create Update button for sidebar entry management
	return $default_locations;
}
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
  
  $mysqli = new mysqli ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$sqlForDate =  "SELECT 	 `wp_mf_faire`.`start_dt`
			FROM  wp_mf_faire
			where faire='$faire_id'";
  $result = $mysqli->query ($sqlForDate ) or trigger_error ( $mysqli->error );
  $start_dt = '';
  if ($result) {
		while ( $row = $result->fetch_row () ) {
			 $start_dt = DateTime::createFromFormat('Y-m-d H:i:s', $row [0]); // your original DTO
      $start_dt->add(new DateInterval('P7D'));
   
     $start_dt = $start_dt->format('Y/m/d'); // your newly formatted date ready to be substituted into JS new Date();
    
			
		}
	}
    
	$transport = new \Kendo\Data\DataSourceTransport ();

	$create = new \Kendo\Data\DataSourceTransportCreate ();

	$create->url ( '/mfscheduler-tasks?faire_id='.$faire_id.'&type=create' )->contentType ( 'application/json' )->type ( 'POST' )->dataType('json');

	$read = new \Kendo\Data\DataSourceTransportRead ();

	$read->url ( '/mfscheduler-tasks?faire_id='.$faire_id.'&type=read' )->contentType ( 'application/json' )->type ( 'GET' )->dataType('json');

	$update = new \Kendo\Data\DataSourceTransportUpdate ();

	$update->url ( '/mfscheduler-tasks?faire_id='.$faire_id.'&type=update' )->contentType ( 'application/json' )->type ( 'POST' )->dataType('json');

	$destroy = new \Kendo\Data\DataSourceTransportDestroy ();

	$destroy->url ( '/mfscheduler-tasks?faire_id='.$faire_id.'&type=destroy' )->contentType ( 'application/json' )->type ( 'POST' )->dataType('json');

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
		->date(new DateTime ( $start_dt ) )->height ( 900 )->pdf ( $pdf )->addToolbarItem ( new \Kendo\UI\SchedulerToolbarItem ( 'pdf' ) )->addResource ( $subareasResource, $entriesResource )->group ( array (
			'resources' => array (
					'Stages'
			)
	) )->addView ( array (
			'type' => 'day',
			'majorTick' => 30,
			'showWorkHours' => true,
			'workWeekEnd' => 7,
			'workDayStart' => new DateTime ( '2016/5/20 15:00', new DateTimeZone ( 'UTC' ) ),
			'workDayEnd' => new DateTime ( '2016/5/22 00:00', new DateTimeZone ( 'UTC' ) )
	), array (
			'type' => 'workWeek',
			'majorTick' => 30,
			'selected' => true,
			'workWeekStart' => 5,
			'workWeekEnd' => 7,
			'showWorkHours' => true,
			'workDayStart' => new DateTime ( '2016/5/20 15:00', new DateTimeZone ( 'UTC' ) ),
			'workDayEnd' => new DateTime ( '2016/5/22 00:00', new DateTimeZone ( 'UTC' ) )
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