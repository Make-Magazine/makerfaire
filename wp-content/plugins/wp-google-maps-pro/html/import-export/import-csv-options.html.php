<div class="wpgmza-import-options-inner">
	<h2 data-wpgmza-import-source=''><?php esc_html_e( 'Import CSV', 'wp-google-maps' ); ?></h2>
	<p>
		<strong><?php esc_html_e('Source', 'wp-google-maps'); ?>:</strong> <span data-name='source'></span><br>
		<strong><?php esc_html_e('Data Type', 'wp-google-maps'); ?>:</strong> <span data-name='import_type'></span><br>
		<strong><?php esc_html_e('Rows', 'wp-google-maps'); ?>:</strong> <span data-name='total_rows'></span>
	</p>

	<h3><?php esc_html_e("Options", "wp-google-maps"); ?></h3>
	<div data-import-type='dataset'>
		<div class="wpgmza-row wpgmza-margin-b-20 geocode-option-wrap">
			<div class="wpgmza-col-4">
				<?php esc_html_e( 'Find Addresses', 'wp-google-maps' ); ?>
			</div>

			<div class="wpgmza-col-8">
				<div class="switch">
					<input id="geocode_import" class="csv_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" data-option='geocode'>
					<label for="geocode_import"></label>
				</div>
				
				<br>
				<span>
					<?php esc_html_e( 'Requires Google Maps Geocoding API to be enabled.', 'wp-google-maps' ); ?>
				</span> 
				<a href="https://www.wpgmaps.com/documentation/creating-a-google-maps-api-key/" target="_blank">[?]</a>
			</div>
		</div>

		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-4">
				<?php esc_html_e("Use map ID's specified in file", "wp-google-maps"); ?>
			</div>

			<div class="wpgmza-col-8">
				<div class="switch">
					<input id="keep_map_id" name="keep_map_id" class="cmn-toggle cmn-toggle-round-flat" type="checkbox" data-option='keep_map_id'>
					<label for="keep_map_id"></label>
				</div>
			</div>
		</div>

		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-4">
				<?php esc_html_e( 'Apply import data to', 'wp-google-maps' ); ?>
			</div>

			<div class="wpgmza-col-8">
				<div class="switch">
					<input id="apply_import" class="csv_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" data-option='apply'>
					<label for="apply_import"></label>
				</div>
			</div>
		</div>

		<div class="wpgmza-row wpgmza-margin-b-20" id="maps_apply_import">
			<div class="wpgmza-col-4">
				&nbsp;
			</div>

			<div class="wpgmza-col-8">
				<table class="wp-list-table widefat fixed striped wpgmza-listing">
					<thead>
						<tr>
							<th></th>
							<th><?php esc_html_e( 'ID', 'wp-google-maps' ); ?></th>
							<th><?php esc_html_e( 'Title', 'wp-google-maps' ); ?></th>
						</tr>
					</thead>
					
					<tbody>
						<!-- Dynamically filled -->
					</tbody>
				</table>
				<button id="maps_apply_select_all" class="wpgmza_general_btn">
					<?php esc_html_e( 'Select All', 'wp-google-maps' ); ?>
				</button> 

				<button id='maps_apply_select_none' class='wpgmza_general_btn'>
					<?php esc_html_e( 'Select None', 'wp-google-maps' ); ?>
				</button>
			</div>
		</div>
	</div>

	<div data-import-type='shared'>
		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-4">
				<?php esc_html_e("Remap Columns", "wp-google-maps"); ?>
			</div>

			<div class="wpgmza-col-8">
				<div class="switch">
					<input id="remap_columns" class="cmn-toggle cmn-toggle-round-flat" type="checkbox" data-option='remap_columns'>
					<label for="remap_columns"></label>
				</div>
				
				<br>

				<div class="remap_wrapper">

				</div>
			</div>
		</div>
		
		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-4">
				<?php esc_html_e( 'Batch Import Size', 'wp-google-maps' ); ?>
			</div>

			<div class="wpgmza-col-8">
				<input id='batch_size' data-option='batch_size' value="100" type="number" class="wpgmza-margin-b-20">
				<div class="helper">
					<span data-helper='create_update'><?php esc_html_e("Max rows to import in each batch, only adjust this if you are experiencing issues with imports", "wp-google-maps"); ?></span>
				</div>
			</div>
		</div>

		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-4">
				<?php esc_html_e( 'Mode', 'wp-google-maps' ); ?>
			</div>

			<div class="wpgmza-col-8">
				<select id='import_mode' data-option='mode' class="wpgmza-margin-b-20">
					<option value="create_update">Create & Update</option>
					<option value="replace">Replace All</option>
				</select>
				
				<div class="helper">
					<span data-helper='create_update'><?php esc_html_e("If a matching ID is found, it will be updated. If no matches are found, a new item will be created", "wp-google-maps"); ?></span>
					<span data-helper='replace'><?php esc_html_e("All items will be removed from the map, and the data within the CSV will be used to create new items", "wp-google-maps"); ?></span>
				</div>
			</div>
		</div>

		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-4">
				<?php esc_html_e( 'Delete import file after import', 'wp-google-maps' ); ?>
			</div>

			<div class="wpgmza-col-8">
				<div class="delete-after-import">
					<div class="switch">
						<input id="delete_import" class="csv_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" data-option='delete_import'>
						<label for="delete_import"></label>
					</div>
				</div>
			</div>
		</div>

	</div>

	<!-- This should be modularized and shared with all importers -->
	<br><br>

	<div id="import-schedule-csv-options">
		<h2><?php esc_html_e( 'Scheduling Options', 'wp-google-maps' ); ?></h2>
		
		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-4">
				<?php esc_html_e( 'Start Date', 'wp-google-maps' ); ?>
			</div>

			<div class="wpgmza-col-8">
				<input type="date" id="import-schedule-csv-start" class="import-schedule-csv-options" data-option="start">
			</div>
		</div>
		
		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-4">
				<?php esc_html_e( 'Interval', 'wp-google-maps' ); ?>
			</div>

			<div class="wpgmza-col-8">
				<select id="import-schedule-csv-interval" class="import-schedule-csv-options"></select>
			</div>	
		</div>	
	</div>

	<p>
		<button id="import-csv" class="wpgmza_general_btn"><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></button>
		<button id="import-schedule-csv" class="wpgmza_general_btn"><?php esc_html_e( 'Schedule', 'wp-google-maps' ); ?></button>
		<button id="import-schedule-csv-cancel" class="wpgmza_general_btn"><?php esc_html_e( 'Cancel', 'wp-google-maps' ); ?></button>
	</p> 

	<!-- These scripts really shouldn't be loaded in this way --> 
	<script type="text/javascript">
		/**
		 * This should be turned into a modular class, so that it doesn't need to be served in line
		 * 
		 * For now (2022-04-05) we will leave it this way, but modernize some of the code to improve reliability
		 * 
		 * With that said, this needs to be reworked as soon as we can
		 * 
		*/
		(function($) {
			const container = $('.wpgmza-import-options-inner');
			const doingEdit = container.data('editing');
			
			if(doingEdit){
				container.find('.maps_apply').prop('checked', false);	
			}

			/* 
			 * General events 
			*/
			container.find('#maps_apply_select_all').click(function () {
				container.find('.maps_apply').prop('checked', true);
			});

			container.find('#maps_apply_select_none').click(function () {
				container.find('.maps_apply').prop('checked', false);
			});

			container.find('#apply_import').change(function () {
				if ($(this).prop('checked')) {
					container.find('#maps_apply_import').show();
				} else {
					container.find('#maps_apply_import').hide();
				}
			});

			container.find('#apply_import').trigger('change');

			container.find('#remap_columns').change(function () {
				if ($(this).prop('checked')) {
					container.find('.remap_wrapper').show();
				} else {
					container.find('.remap_wrapper').hide();
				}
			});

			container.find('#remap_columns').trigger('change');

			container.find('select[data-option="mode"]').change(function(){
				$(this).next('.helper').find('span').hide();
				$(this).next('.helper').find('span[data-helper="' + $(this).val() + '"]').show();
			});

			container.find('select[data-option="mode"]').trigger('change');

			/*
			 * Cancel schedule button
			*/
			container.find('#import-schedule-csv-cancel').click(function(){
				container.find('#import-csv,.delete-after-import').show();
				container.find('#import-schedule-csv-cancel').hide();
				container.find('#import-schedule-csv-options').slideUp(300);
			});

			const localizedStrings = container.data('localized-strings');

			/*
			 * Import button
			*/
			container.find('#import-csv').click(function(){
				const import_options = csv_get_import_options();

				if(!import_options){
					/* Bail */
					return;
				}
				
				if($('#import_loader').hasClass('wpgmza-import-loader')){
					/* New layout for loaders */
					$('.wpgmza-import-loader .wpgmza-loader-message').text(localizedStrings.importing_notice);
					$('.wpgmza-import-loader .wpgmza-progress-bar').val(0).removeClass("wpgmza-hidden");
				} else {
					/* Old HTML supports, some importers will still use this approach in legacy mode */
					$('#import_loader_text').html('<br/>' + localizedStrings.importing_notice + '<br/><progress id="wpgmza-import-csv-progress"/>');
				}
				
				$('#import_loader').show();
				$('#import_options').hide();
				
				const source = $("[data-wpgmza-import-source]").attr("data-wpgmza-import-source");

				const data = {
					action  : 'wpgmza_import',
					options : import_options, 
					wpgmaps_security : WPGMZA.import_security_nonce
				};

				if(container.data('import-id')){
					data.import_id = container.data('import-id');
				}

				if(container.data('import-url')){
					data.import_url = container.data('import-url');
				}

				/*
				 * In the first version the system would start an import progress ping, using an interval
				 *
				 * This is being switched out for an event driven approach instead in V9.0.0
				*/
				wp.ajax.send({
					data: data,
					success: function (data) {
						if(data && data instanceof Object){
							if(data.batch && data.batchClass){
								/* This is a batched import, we need to check on it's progress repeatedly until complete */
								checkBatchUntilComplete(data.batch, data.batchClass);
							} else {
								$('#import_loader').hide();
								
								if (typeof data !== 'undefined' && data.hasOwnProperty('id')) {
									let type = "success";
									if(data.notices.length > 0){
										type = "warning";
									}
									
									wpgmaps_import_add_notice('<p>' + localizedStrings.import_complete + '</p>', type);
									
									for(var i = 0; i < data.notices.length; i++) {
										wpgmaps_import_add_notice('<p>' + data.notices[i] + '</p>', 'error', true);
									}
									
									if (data.hasOwnProperty('del') && 1 === data.del){
										$('#import_options').html('');
										$('#import-list-item-' + data.id).remove();
										$('#import_files').show();
										return;
									}
									
									$('#import_options').show();
								}

							}
						} else {								
							$('#import_loader').hide();
							$('#import_options').show();
						}						
					},
					error: function (data) {
						let string = (typeof data == "string" ? data : data.statusText);
						
						if (typeof data !== 'undefined') {
							wpgmaps_import_add_notice(data, 'error');
						}

						$('#import_loader').hide();
						$('#import_options').show();
					}
				});
			});

			$('#import-schedule-csv').click(function(){
				if ($('#import-csv').is(':visible')) {
					$('#import-csv,.delete-after-import').hide();
					$('#import-schedule-csv-cancel').show();
					$('#import-schedule-csv-options').slideDown(300);
				} else {
					const import_options = csv_get_import_options();
					
					if(!import_options){
						/* Bail */
						return;
					}

					if (Object.keys(import_options).length < 1){
						alert(localizedStrings.schedule_map_warning);
						return;
					}
					
					if ($('#import-schedule-csv-start').val().length < 1){
						alert(localizedStrings.schedule_date_warning);
						return;
					}
					
					if($('#import_loader').hasClass('wpgmza-import-loader')){
						/* New layout for loaders */
						$('.wpgmza-import-loader .wpgmza-loader-message').text(localizedStrings.scheduling_notice);
						$('.wpgmza-import-loader .wpgmza-progress-bar').val(0).addClass("wpgmza-hidden");
					} else {
						/* Old approach */
						$('#import_loader_text').html('<br>' + localizedStrings.scheduling_notice);
					}



					$('#import_loader').show();
					$('#import_options').hide();

					const data = {
						action   : 'wpgmza_import_schedule',
						options  : import_options,
						start    : container.find('#import-schedule-csv-start').val(),
						interval : container.find('#import-schedule-csv-interval').val(),
						wpgmaps_security : WPGMZA.import_security_nonce
					};

					if(container.data('import-id')){
						data.import_id = container.data('import-id');
					}

					if(container.data('import-url')){
						data.import_url = container.data('import-url');
					}	

					if(container.data('schedule-id')){
						data.schedule_id = container.data('schedule-id');
					}				

					wp.ajax.send({
						data: data,
						success: function (data) {
							if (typeof data !== 'undefined' && data.hasOwnProperty('schedule_id') && data.hasOwnProperty('next_run')) {
								wpgmaps_import_add_notice('<p>' + localizedStrings.scheduling_complete + '</p>');
								
								$('#import_loader').hide();
								$('#import_options').html('').hide();
								$('#import_files').show();
								$('a[href="#schedule-tab"').click();
								
								let schedule_listing = '<tr id="import-schedule-list-item-' + data.schedule_id + '">'
								schedule_listing += 	 '<td><strong><span class="import_schedule_title" style="font-size:larger;">' + data.title + '</span></strong><br>';
								schedule_listing += 		'<a href="javascript:void(0);" class="import_schedule_edit" data-schedule-id="' + data.schedule_id + '">';
								schedule_listing += 			localizedStrings.schedule_edit
								schedule_listing += 		'</a> | ';
								schedule_listing += 		'<a href="javascript:void(0);" class="import_schedule_delete" data-schedule-id="' + data.schedule_id + '">';
								schedule_listing += 			localizedStrings.schedule_delete
								schedule_listing += 		'</a> | ';
								schedule_listing += 			((data.next_run.length < 1 || !data.next_run) ? localizedStrings.schedule_not_found : localizedStrings.schedule_next_run + ': ' + data.next_run); 
								schedule_listing += 	'</td>';
								schedule_listing += '</tr>';

								if ($('#import-schedule-list-item-' + data.schedule_id).length > 0){
									$('#import-schedule-list-item-' + data.schedule_id).replaceWith(schedule_listing);
								} else {
									$('#wpgmap_import_schedule_list_table tbody').prepend(schedule_listing);
								}
								wpgmaps_import_setup_schedule_links(data.schedule_id);
								$('#wpgmaps_import_schedule_list').show();
							}
						},
						error: function (data) {
							if (typeof data !== 'undefined') {
								wpgmaps_import_add_notice(data, 'error');
								$('#import_loader').hide();
								$('#import_options').show();
							}
						}
					});
				}
			});

			function csv_get_import_options(){
				const options = {};
				const applyIds = [];

				container.find('input,select').each(function(){
					const item = $(this);
					let name = false;
					if(item.data('option')){
						/* Preferred data-option reference */
						name = item.data('option');
					} else if (item.attr('name')){
						/* Use default field 'name' */
						name = item.attr('name');
					} else if (item.attr('id')){
						/* We have an ID, but this usually needs more in depth filtering */
						name = item.attr('id');
					}

					if(name.indexOf('[]') !== -1){
						const arrName = name.replace('[]', '');
						if(!(options[arrName]) || !(options[arrName] instanceof Array)){
							options[arrName] = [];
						}
					}

					let value = item.val();
					if(item.attr('type')){
						switch(item.attr('type')){
							case 'checkbox':
								value = item.prop('checked') ? true : false;
								break;
							case 'number':
								value = parseInt(value);
								break;
						}
					}

					if(item.hasClass('maps_apply')){
						/* Don't add this directly to options, this gets combined before sending */
						if(value){
							applyIds.push(parseInt(item.val()));
						}
					} else if(options[name.replace('[]', '')] && options[name.replace('[]', '')] instanceof Array){
						options[name.replace('[]', '')].push(value);
					} else {
						options[name] = value;
					}
				});

				if(options.apply){
					/* Apply to specific maps */
					options.keep_map_id = false; 

					if(applyIds.length){
						/* Should really just be sent as an array, but ya this will have to stay as is */
						options.applys = applyIds.join(',');
					} else {
						alert(localizedStrings.import_map_select_warning);
						return false;
					}
				}

				return options;
			}

			function checkBatchUntilComplete(batch, batchClass){
				const data = {
					action  : 'wpgmza_batch_import_status',
					batch : parseInt(batch), 
					batch_class : batchClass,
					wpgmaps_security : WPGMZA.import_security_nonce
				};

				wp.ajax.send({
					data: data,
					success: function (data) {
						if(data && data instanceof Object){
							if(data.ping){
								if(data.percentage){
									if($('#import_loader').hasClass('wpgmza-import-loader')){
										/* New layout for loaders */
										$('.wpgmza-import-loader .wpgmza-progress-bar').val(data.percentage);
										$('.wpgmza-import-loader .wpgmza-loader-steps').html(data.playhead + " of " + (data.steps - 1) + " processed");
									} else {
										/* Old approach*/
										$('#import_loader_text').find('progress').attr('value', data.percentage);
									}
								}

								setTimeout(function(){
									/* We should continue polling - We do this in a few seconds to reduce server strain */
									checkBatchUntilComplete(batch, batchClass);
								}, 2000)
							} else {
								/* The import should be complete, so let's let the user know */
								wpgmaps_import_add_notice('<p>' + localizedStrings.import_complete + '</p>', 'success');

								$('#import_loader').hide();
								$('#import_options').show();

								$('.wpgmza-import-loader .wpgmza-loader-steps').html("");

								if(data.errors && data.errors instanceof Array){
									for(let error of data.errors){
										wpgmaps_import_add_notice('<p>' + error + '</p>', 'error', true);
									}
								}

								if (data.deleted_file){
									$('#import_options').html('');
									$('#import-list-item-' + data.deleted_file).remove();
									$('#import_files').show();
									return;
								}
							}
						}
					},
					error: function(data){
						let string = (typeof data == "string" ? data : data.statusText);
						
						if (typeof data !== 'undefined') {
							wpgmaps_import_add_notice(data, 'error');
						}

						$('#import_loader').hide();
						$('#import_options').show();
					}
				});
			}
			
		})(jQuery);
	</script>
</div>