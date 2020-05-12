<?php
/**
 * Template Name: mfcheduler
 */
// Check that all required fields are passed before running anything and assign them to variables
http_response_code(200);

get_header('admin');
global $wp_query;
$faire_id = (!empty($wp_query->query_vars['faire_id']) ? sanitize_text_field($wp_query->query_vars['faire_id']) : null );
$current_user = wp_get_current_user();
require_once 'lib/Kendo/Autoload.php';
$default_locations = isset($_GET['loc']) ? $_GET['loc'] : str_getcsv(get_default_locations($faire_id));
$default_locations = isset($default_locations) ? $default_locations : "414";
?>
<script>
   var $ = jQuery.noConflict();
</script>
<form>
   <div class="k-floatwrap k-header k-scheduler-toolbar">
      <?php
      $locations_array = get_entry_locations($faire_id);
      $select = new \Kendo\UI\MultiSelect('locationfilters');
// Set Defaults here in the value array, by stage id.
      $select->dataSource($locations_array)
              ->change('onChange')
              ->value($default_locations)
              ->dataTextField('text')
              ->dataValueField('value')
              ->placeholder('Filter location ...');
      ;

      echo $select->render();
      ?>
   </div>
   <?php
   $scheduler = create_makerfaire_scheduler($faire_id);
   echo $scheduler->render();
   ?>
   <!-- Calendar Scheduler (Content Templates) -->

   <script>
      jQuery(document).ready(function () {
         // create ComboBox from select HTML element
         var input = jQuery("#input").data("kendoComboBox");
         var select = jQuery("#select").data("kendoComboBox");
         onChange(null);

      });
   </script>
   <script id="presentation-template" type="text/x-kendo-template">
      <div class="mf-entry-template" style="background-color:#: StatusColor #">
      # if(entries){ #
      <a target="_blank" title="#: title #" href="/wp-admin/admin.php?page=gf_entries&view=entry&id=#: form #&lid=#: entries[0] #">#: entries[0] #</a>

      # } #
      <p>#: title #</p></div>
   </script>
   <!-- begin#woahbar -->
   <div class="woahbar" style="display: none;">
      <span> <a class="woahbar-link" href="/wp-admin/">Back to wp-admin</a> Howdy, <?php echo $current_user->user_login; ?>
      </span> <a class="close-notify" onclick="woahbar_hide();"> <img
            class="woahbar-up-arrow"
            src="/wp-content/themes/makerfaire/lib/Kendo/woahbar/woahbar-up-arrow.png" /></a>
   </div>
   <div class="woahbar-stub" style="display: none;">
      <a class="show-notify" onclick="woahbar_show();"> <img
            class="woahbar-down-arrow"
            src="/wp-content/themes/makerfaire/lib/Kendo/woahbar/woahbar-down-arrow.png" />
      </a>
   </div>
	<style>
		/*
			  Use the DejaVu Sans font for display and embedding in the PDF file.
			  The standard PDF fonts have no support for Unicode characters.
		*/
		.k-floatwrap .k-header .k-scheduler-toolbar { padding: 0px !important; }
		.k-block, .k-draghandle, .k-grid-header, .k-grouping-header, .k-header, .k-pager-wrap, .k-treemap-tile, .k-scheduler .k-header .k-link, .k-scheduler .k-header .k-button {
			background-color: #005E9A !important;
		}
		.k-scheduler-toolbar > .k-scheduler-tools { margin-top: 10px; }
		.k-scheduler-toolbar > .k-scheduler-tools .k-button { border-color: #fff; border-radius: 10px; }
		.k-scheduler-toolbar > .k-scheduler-tools .k-button:hover { border-color: #151733; color: #151733; background-color: #fff !important;}
		.k-scheduler .k-header .k-link, .k-scheduler .k-header li { border-color: #005E9A; }
		.k-scheduler-views li {
			margin-left: 10px;
		}
		.k-scheduler-views li a {
			border: solid 1px #151733;
		}
		.mf-entry-template p,
		.k-scheduler-table a
		{
			color: #fff;
			overflow: hidden;
			font-weight: bold; 
		}
		.mf-entry-template a { color: #151733; font-weight: bold; }
		.k-scheduler {
			font-family: "DejaVu Sans", "Arial", sans-serif;
			font-size: 80%;
		}
		.mf-entry-template { 
			border-radius: 3px;
			padding: 3px;
			border: 1px solid #151733;
		}
		.k-event {
			border: 0px;
			background: transparent;
			text-align: center;
			overflow: visible;
		}
		.k-event-delete { 
			display: block !important;
			color: #151733 !important;
			border: solid 1px #151733;
			border-radius: 10px !important;
			background: #fff;
			position: absolute;
			top: -10px;
			right: -10px;
		 }
		.k-widget.k-window { border-radius: 10px; }
		.k-edit-field input[type="checkbox"] { margin: 20px; }

		/* Hide toolbar, navigation and footer during export */
		.woahbar,.k-pdf-export .k-scheduler-toolbar,.k-pdf-export .k-scheduler-navigation .k-nav-today,.k-pdf-export .k-scheduler-navigation .k-nav-prev,.k-pdf-export .k-scheduler-navigation .k-nav-next,.k-pdf-export .k-scheduler-footer
		{
			display: none;
		}
	</style>


   <style>
      .k-scheduler-layout {
         table-layout: fixed;
      }

      .k-scheduler-layout>tbody>tr>td:first-child {
         width: 10%;
      }
	   .k-scheduler-header-wrap th { color: #151733; }
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
            kendoConsole.log("event: select (" + checked + ")");

            var multiSelect = jQuery("#locationfilters").data("kendoMultiSelect");
            var checked = multiSelect.value();

            var filter = {
               logic: "or",
               filters: $.map(checked, function (value) {
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

      }
      ;
   </script>

   <?php

   function get_default_locations($faire_id) {
      $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if ($mysqli->connect_errno) {
         echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
      }
      $default_locations = "";

      $result = $mysqli->query("SELECT 	`wp_mf_faire`.`default_locations`
			FROM wp_mf_faire
			where faire='$faire_id'") or trigger_error($mysqli->error);

      if ($result) {
         while ($row = $result->fetch_row()) {
            $default_locations = $row [0];
         }
      }
      // Create Update button for sidebar entry management
      return $default_locations;
   }

   function get_entry_locations($faire_id) {
      $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if ($mysqli->connect_errno) {
         echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
      }
      $locations = array();
		
		$result = $mysqli->query("SELECT      `wp_mf_faire_subarea`.`id`,
           `wp_mf_faire_subarea`.`subarea`
           FROM  wp_mf_faire_subarea, wp_mf_faire_area, wp_mf_faire
           where faire='$faire_id'
           and   wp_mf_faire_subarea.area_id = wp_mf_faire_area.ID
           and   wp_mf_faire_area.faire_id   = wp_mf_faire.ID
			  order by sort_order ASC, subarea ASC") or trigger_error($mysqli->error);

      if ($result) {
         while ($row = $result->fetch_row()) {
            $subarea = $row [1];
            $subarea_id = $row [0];

            $locations [] = array(
                'text' => $subarea,
                'value' => $subarea_id
            );
         }
      } else {
         $locations [] = array(
             'text' => 'No Stages Setup',
             'value' => '-1'
         );
      }
      return $locations;
   }

   function get_entry_presentationTypes() {
      $presentationTypes = array();

      $presentationTypes [] = array('text' => "Unknown", 'value' => "unknown");
      $presentationTypes [] = array('text' => "Workshop", 'value' => "workshop");
      $presentationTypes [] = array('text' => "Talk", 'value' => "talk");
      $presentationTypes [] = array('text' => "Performance", 'value' => "performance");
      $presentationTypes [] = array('text' => "Demo", 'value' => "demo");
      // Create Update button for sidebar entry management
      return $presentationTypes;
   }

   function get_entries($faire_id) {
      $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if ($mysqli->connect_errno) {
         echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
      }
      $entries = array();

      $result = $mysqli->query("SELECT lead_id,presentation_title ,status
				FROM wp_mf_entity
				where faire='$faire_id'") or trigger_error($mysqli->error);

      if ($result) {
         while ($row = $result->fetch_row()) {
            $entry = preg_replace("/[^A-Za-z0-9 ]/", '', $row [1]);
            $entry_id = $row [0];
            $entry_status = $row [2];
            $entry_color = status_to_color($entry_status);
            $entry_title = "$entry_id ($entry - $entry_status)";
            $entries [] = array(
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

   function create_makerfaire_scheduler($faire_id) {

      $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      if ($mysqli->connect_errno) {
         echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
      }
      $sqlForDate = "SELECT `wp_mf_faire`.`start_dt`, `wp_mf_faire`.`end_dt` FROM  wp_mf_faire where faire='$faire_id'";
      $result = $mysqli->query($sqlForDate) or trigger_error($mysqli->error);
      $start_dt = '';
	  $end_dt = '';
      $start_dow = 0;
      $end_dow = 0;
      if ($result) {
         while ($row = $result->fetch_array()) {
			$start_dt = DateTime::createFromFormat('Y-m-d H:i:s', $row ['start_dt']); // your original DTO
			$start_dow = $start_dt->format('N');
			$start_dt = $start_dt->format('Y/m/d'); // your newly formatted date ready to be substituted into JS new Date();

			$end_dt = DateTime::createFromFormat('Y-m-d H:i:s', $row ['end_dt']); // your original DTO
			$end_dow = $end_dt->format('N');
			$end_dt = $end_dt->format('Y/m/d'); // your newly formatted date ready to be substituted into JS new Date();

			//DEBUG: Interval looks like it is no longer needed.
			//Why do we need to add 7 days to make this work???
			// see if we can add default beginning and ending time
			//$start_dt->add(new DateInterval(‘P7D’));
		}

      }
      $transport = new \Kendo\Data\DataSourceTransport ();

      $create = new \Kendo\Data\DataSourceTransportCreate ();

      $create->url('/mfscheduler-tasks?faire_id=' . $faire_id . '&type=create')->contentType('application/json')->type('POST')->dataType('json');

      $read = new \Kendo\Data\DataSourceTransportRead ();

      $read->url('/mfscheduler-tasks?faire_id=' . $faire_id . '&type=read')->contentType('application/json')->type('GET')->dataType('json');

      $update = new \Kendo\Data\DataSourceTransportUpdate ();

      $update->url('/mfscheduler-tasks?faire_id=' . $faire_id . '&type=update')->contentType('application/json')->type('POST')->dataType('json');

      $destroy = new \Kendo\Data\DataSourceTransportDestroy ();

      $destroy->url('/mfscheduler-tasks?faire_id=' . $faire_id . '&type=destroy')->contentType('application/json')->type('POST')->dataType('json');

      $transport->create($create)->read($read)->update($update)->destroy($destroy)->parameterMap('function(data) {
              return kendo.stringify(data);
          }');

      $model = new \Kendo\Data\DataSourceSchemaModel ();

      $locationIdField = new \Kendo\Data\DataSourceSchemaModelField('locationID');
      $locationIdField->type('number')->from('locationID')->nullable(true);

      $titleField = new \Kendo\Data\DataSourceSchemaModelField('title');
      $titleField->from('Title')->defaultValue('No title')->validation(array(
          'required' => false
      ));

      $startField = new \Kendo\Data\DataSourceSchemaModelField('start');
      $startField->type('date')->from('Start');

      $endField = new \Kendo\Data\DataSourceSchemaModelField('end');
      $endField->type('date')->from('End');

      $isAllDayField = new \Kendo\Data\DataSourceSchemaModelField('isAllDay');
      $isAllDayField->type('boolean')->from('IsAllDay');

      $subareaIdField = new \Kendo\Data\DataSourceSchemaModelField('subareaId');
      $subareaIdField->from('SubareaID')->nullable(true);

      $statusColorField = new \Kendo\Data\DataSourceSchemaModelField('StatusColor');
      $statusColorField->from('StatusColor')->nullable(true);

      $presentationTypeField = new \Kendo\Data\DataSourceSchemaModelField('presentationType');
      $presentationTypeField->from('PresentationType')->nullable(true);

      $entriesField = new \Kendo\Data\DataSourceSchemaModelField('entries');
      $entriesField->from('Entries')->nullable(true);

      $formField = new \Kendo\Data\DataSourceSchemaModelField('form');
      $formField->from('Form')->nullable(true);
      
      $model->id('locationID')
              ->addField($locationIdField)
              ->addField($titleField)
              ->addField($startField)
              ->addField($endField)
              ->addField($isAllDayField)
              ->addField($subareaIdField)
              ->addField($entriesField)
              ->addField($formField)
              ->addField($presentationTypeField)
              ->addField($statusColorField);

      $schema = new \Kendo\Data\DataSourceSchema ();
      $schema->model($model);

      $dataSource = new \Kendo\Data\DataSource ();
      $dataSource->transport($transport)->schema($schema)->batch(false);


      $typesResource = new \Kendo\UI\SchedulerResource ();
      $types_array = get_entry_presentationTypes();
      $typesResource->field('presentationType')->title('Type')->name('Types')->dataSource($types_array);
      $subareasResource = new \Kendo\UI\SchedulerResource ();
      $locations_array = get_entry_locations($faire_id);
      $subareasResource->field('subareaId')->title('Stage')->name('Stages')->dataSource($locations_array);


      $entries = get_entries($faire_id);
      $entriesResource = new \Kendo\UI\SchedulerResource ();
      $entriesResource->field('entries')->title('Presenter')->multiple(true)->name('Presenters')->dataSource($entries);

      $pdf = new \Kendo\UI\SchedulerPdf ();
      $pdf->fileName('Kendo UI Scheduler Export.pdf')->proxyURL('makerfaire-scheduling.php?type=save');

      $scheduler = new \Kendo\UI\Scheduler('scheduler');

      $scheduler->eventTemplateId('presentation-template')
			  ->timezone('America/Los_Angeles')
			  ->currentTimeMarker(false)
			  ->date(new DateTime($start_dt))
			  ->height(900)->pdf($pdf)
			  ->addToolbarItem(new \Kendo\UI\SchedulerToolbarItem('pdf'))
			  ->addResource($subareasResource, $entriesResource, $typesResource)
			  ->group(array('resources' => array('Stages')
					 ))->addView(array(
								'type' => 'day',
								'majorTick' => 30,
								'showWorkHours' => true,
								'workWeekEnd' => $end_dow,
								'workDayStart' => new DateTime('2016/5/20 17:00', new DateTimeZone('UTC')),
								'workDayEnd' => new DateTime('2016/5/22 02:00', new DateTimeZone('UTC'))
									), array(
								'type' => 'workWeek',
								'majorTick' => 30,
								'selected' => true,
								'workWeekStart' => $start_dow,
								'workWeekEnd' => $end_dow,
								'showWorkHours' => true,
								'workDayStart' => new DateTime('2016/5/20 17:00', new DateTimeZone('UTC')),
								'workDayEnd' => new DateTime('2016/5/22 02:00', new DateTimeZone('UTC'))
						), 'agenda')->dataSource($dataSource);

      return $scheduler;
   }

   function debug_to_console($data) {
      if (is_array($data)) {
         $output = "<script>console.log( 'Debug Objects: " . implode(',', $data) . "' );</script>";
      } else {
         $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
      }
      echo $output;
   }
   