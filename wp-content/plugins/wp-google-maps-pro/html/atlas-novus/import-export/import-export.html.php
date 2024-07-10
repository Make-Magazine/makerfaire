<!-- Import tab -->
<div id="import-tab">
	<div class="heading">
		<?php _e("Import", "wp-google-maps"); ?>
	</div>
	<div id="import_files">
		<div class="tab-row">
			<div class="title"><?php esc_html_e( 'Source', 'wp-google-maps' ); ?></div>
			<div id="import_via">
				<label>
					<input type="radio" name="import_data_type" class="import_data_type" value="URL" checked="checked"/>
					<?php esc_html_e( 'URL', 'wp-google-maps' ); ?>
				</label>

				<br/>
				
				<label>
					<input type="radio" name="import_data_type" class="import_data_type" value="file"/>
					<?php esc_html_e( 'File', 'wp-google-maps' ); ?>
				</label>
				
				<br/>
				
				<label>
					<input type="radio" name="import_data_type" class="import_data_type" value="bulk_jpeg"/>
					<?php esc_html_e("Bulk JPEG", "wp-google-maps"); ?>
				</label>
				
				<br/>
				
				<label>
					<input type="radio" name="import_data_type" class="import_data_type" value="airtable"/>
					<?php esc_html_e("Airtable", "wp-google-maps"); ?>
				</label>
			</div>
		</div>

		<br>

		<div class="tab-row">
			<div class="title"></div>
				
			<div class="import_config_container tab-stretch-right">
				<!-- URL -->
				<div id="import_from_url" class="wpgmza-import-upload-panel">
					<input id="wpgmaps_import_url" placeholder="<?php esc_attr_e( 'Import URL', 'wp-google-maps' ); ?>" type="text" style="max-width:500px;width:100%;"/>
					
					<br>
					<br>
					<span class="description" style="display:inline-block;max-width:500px;">
						<?php esc_html_e( 'If using a Google Sheet URL, the sheet must be public or have link sharing turned on.', 'wp-google-maps' ); ?>
					</span>
					
					<br>
					<br>
					<button id="wpgmaps_import_url_button" class="wpgmza_general_btn wpgmza-button wpgmza-button-primary">
						<?php esc_html_e( 'Import', 'wp-google-maps' ); ?>
					</button>
				</div>

				<!-- FILE -->
				<div id="import_from_file" class="wpgmza-import-upload-panel" style="display:none;">
					<input name="wpgmaps_import_file" id="wpgmaps_import_file" type="file"/>
					<br><br>
					<span name="import_accepts"></span>

					<br><br>
					<?php esc_html_e( 'Max upload size', 'wp-google-maps' ); ?>: 
					<span name="max_upload_size"></span>

					<span id="wpgmaps_import_file_name" class="wpgmza-hidden" />
					<br/>
					<br/>

					<button id="wpgmaps_import_upload_button" class="wpgmza_general_btn wpgmza-button wpgmza-button-primary">
						<?php esc_html_e( 'Upload', 'wp-google-maps' ); ?>
					</button>
					<br>
					
					<span id="wpgmaps_import_upload_spinner" class="spinner" style="float:none;margin-bottom:8px;"></span>
				
					<div id="wpgmaps_import_file_list">
						<table id="wpgmap_import_file_list_table" class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;padding:0;">
							<thead>
								<tr>
									<th style="font-weight:bold;">
										<?php esc_html_e( 'Import Uploads', 'wp-google-maps' ); ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<strong>
											<span 
												name="post_title"
												class="import_file_title" 
												style="font-size:larger;"></span>
										</strong>
										<br>
										<a href="javascript:void(0);" class="import_import" data-import-id><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></a>
										|
										<a href="javascript:void(0);" class="import_delete" data-import-id><?php esc_html_e( 'Delete', 'wp-google-maps' ); ?></a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<!-- JPEG -->
				<div id="import_from_bulk_jpeg" class="wpgmza-import-upload-panel" style="display: none;">
						
					<p>
						<input name="bulk_jpeg_files" type="file" multiple accept="image/jpeg"/>
					</p>
					
					<p id="bulk_jpeg_status"></p>
					
					<button id="wpgmaps_import_bulk_jpeg_button" class="wpgmza_general_btn wpgmza-button wpgmza-button-primary">
						<?php esc_html_e( 'Import', 'wp-google-maps' ); ?>
					</button>
					
				</div>

				<!-- Airtables Integration -->
				<div id="import_from_airtable" class="wpgmza-import-upload-panel" style="display: none;">

					<input id="wpgmaps_import_airtable_url" placeholder="<?php esc_attr_e( 'Airtable URL', 'wp-google-maps' ); ?>" type="text" style="max-width:500px;width:100%;"/>
					<br/>
					<span class="description" style="display:inline-block;max-width:500px;">
						<?php esc_html_e( 'Link to Airtable', 'wp-google-maps' ); ?>
					</span>

					<br/>
					<br/>

					<input id="wpgmaps_import_airtable_api" placeholder="<?php esc_attr_e( 'Airtable Personal Access Token', 'wp-google-maps' ); ?>" type="text" style="max-width:500px;width:100%;"/>
					<br/>
					<span class="description" style="display:inline-block;max-width:500px;">
						<?php esc_html_e( 'Airtable Personal Access Token (Or Legacy API Key)', 'wp-google-maps' ); ?>
					</span>
					<br/>
					<br/>

					<button id="wpgmaps_import_airtable_button" class="wpgmza_general_btn wpgmza-button wpgmza-button-primary">
						<?php esc_html_e( 'Import', 'wp-google-maps' ); ?>
					</button>	
				</div>

				<!-- Airtables Integration -->
				<div id="import_from_integration" class="wpgmza-import-upload-panel" style="display: none;">
					<button id="wpgmaps_import_integration_button" class="wpgmza_general_btn wpgmza-button wpgmza-button-primary">
						<?php esc_html_e( 'Import', 'wp-google-maps' ); ?>
					</button>
				</div>

			</div>
		</div>
	</div>

	<div class="tab-row">
		<div id="import_loader" style="display:none;" class="wpgmza-stretch wpgmza-import-loader">
			<div class='wpgmza-preloader'>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
			</div>
			<div class='wpgmza-loader-message'></div>
			<progress value="0" max="1" class="wpgmza-progress-bar wpgmza-hidden"></progress>
			<div class='wpgmza-loader-steps'></div>
		</div>
		<div id="import_options" style="display:none;"></div>
	</div>
</div>

<!-- Schedule -->
<div id="schedule-tab" style="display:none;">
	<div class="heading">
		<?php _e("Schedule", "wp-google-maps"); ?>
	</div>

	<p class="description" style="max-width:600px;">
		<?php esc_html_e( 'Imports can be scheduled by url or uploaded file. To schedule an import, import as normal and select the Schedule button. Scheduled imports will be listed on this page and can be edited or deleted from here.', 'wp-google-maps' ); ?>
	</p>

	<div id="wpgmaps_import_schedule_list">
		<br/>
		<table id="wpgmap_import_schedule_list_table" class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;">
			<thead>
				<tr>
					<th>
						<?php esc_html_e( 'URL / Filename', 'wp-google-maps' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<strong>
							<span 
								class="import_schedule_title" 
								style="font-size:larger;"
								name="title">
							</span>
						</strong>
						
						<br/>
						
						<a href="javascript:void(0);" class="import_schedule_edit"><?php esc_html_e( 'Edit', 'wp-google-maps' ); ?>
						</a>
						|
						<a href="javascript:void(0);" class="import_schedule_delete"><?php esc_html_e( 'Delete', 'wp-google-maps' ); ?>
						</a>
						|
						<a href="javascript:void(0);" class="import_schedule_view_log"><?php esc_html_e( 'View Log', 'wp-google-maps' ); ?>
						</a>
						|
						<a href="javascript:void(0);" class="import_schedule_view_response"><?php esc_html_e( 'View Response', 'wp-google-maps' ); ?>
						</a>
						
						<span name="status"></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<!-- Export -->
<div id="export-tab" style="display:none;">
	<div class="heading">
		<?php _e("Export", "wp-google-maps"); ?> 
		<select class="export-type-selector">
			<option value="json"><?php esc_html_e('JSON', 'wp-google-maps'); ?></option>
			<option value="csv"><?php esc_html_e('CSV', 'wp-google-maps'); ?></option>
			<option value="kml"><?php esc_html_e('KML', 'wp-google-maps'); ?></option>
			<option value="settings"><?php esc_html_e('Settings', 'wp-google-maps'); ?></option>
		</select>
	</div>

	<div class="export-options" data-type='json'>
		<div class="wpgmza-row">
			<div class="wpgmza-col-3">
				<?php _e("Data Type", "wp-google-maps"); ?>
			</div>

			<div class="wpgmza-col-9">
				<!-- Categories -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="categories_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="categories_export"></label>
						<label for="categories_export">
							<?php esc_html_e( 'Categories', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>
				
				<!-- Custom Fields -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="customfields_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="customfields_export"></label>
						<label for="customfields_export">
							<?php esc_html_e( 'Custom Fields', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>
				
				<!-- Markers -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="markers_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="markers_export"></label>
						<label for="markers_export">
							<?php esc_html_e( 'Markers', 'wp-google-maps' ); ?>
						</label>

					</div>
				</div>

				<!-- Ratings -->
				<?php
					// TODO: Move to gold
					if(defined('WPGMZA_GOLD_VERSION') && version_compare(WPGMZA_GOLD_VERSION, '5.0.0', '>=')){
						?>
						
						<div class="tab-row has-hint">
							<div class="switch switch-inline">
								<input id="ratings_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
								<label for="ratings_export"></label>
								<label for="ratings_export">
									<?php esc_html_e( 'Ratings', 'wp-google-maps' ); ?>
								</label>
							</div>
						</div>
						<?php
					}
				?>

				<!-- Polygons -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="polygons_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="polygons_export"></label>
						<label for="polygons_export">
							<?php esc_html_e( 'Polygons', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>

				<!-- Polylines -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="polylines_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="polylines_export"></label>
						<label for="polylines_export">
							<?php esc_html_e( 'Polylines', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>

				<!-- Circles -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="circles_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="circles_export"></label>
						<label for="circles_export">
							<?php esc_html_e( 'Circles', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>

				<!-- Rectangles --> 
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="rectangles_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="rectangles_export"></label>
						<label for="rectangles_export">
							<?php esc_html_e( 'Rectangles', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>

				<!-- Heatmaps -->
				<div class="tab-row">
					<div class="switch switch-inline">
						<input id="datasets_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="datasets_export"></label>
						<label for="datasets_export">
							<?php esc_html_e( 'Heatmap Datasets', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>

				<!-- Pointlabels -->
				<div class="tab-row">
					<div class="switch switch-inline">
						<input id="pointlabels_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="pointlabels_export"></label>
						<label for="pointlabels_export">
							<?php esc_html_e( 'Point Labels', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>

				<!-- Image Overlays -->
				<div class="tab-row">
					<div class="switch switch-inline">
						<input id="imageoverlays_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="imageoverlays_export"></label>
						<label for="imageoverlays_export">
							<?php esc_html_e( 'Image Overlays', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>

			</div>
		</div>

		<br><br>

		<div class="wpgmza-row">
			<div class="wpgmza-col-3">
				<?php _e("Maps", "wp-google-maps"); ?>
			</div>

			<div class="wpgmza-col-9">
				<!-- We should do-away with the inline styles, this is not ideal -->
				<div id="wpgmza-import-target-map-panel" style="margin:0 0 1em 0;width:100%;">
					<table class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;">
						<thead style="display:block;border-bottom:1px solid #e1e1e1;">
							<tr style="display:block;width:100%;">
								<th style="width:2.2em;border:none;"/>
								<th style="width:80px;border:none;">
									<?php esc_html_e( 'ID', 'wp-google-maps' ); ?>
								</th>
								<th style="border:none;">
									<?php esc_html_e( 'Title', 'wp-google-maps' ); ?>
								</th>
							</tr>
						</thead>
						<tbody style="display:block;max-height:370px;overflow-y:scroll;">
						</tbody>
					</table>
					<button id="maps_export_select_all" class="wpgmza_general_btn wpgmza-button wpgmza-button-secondary">
						<?php esc_html_e( 'Select All', 'wp-google-maps' ); ?>
					</button>
					<button id='maps_export_select_none' class='wpgmza_general_btn wpgmza-button wpgmza-button-secondary'>
						<?php esc_html_e( 'Select None', 'wp-google-maps' ); ?>
					</button>
					<br/>
					<br/>
				</div>
			</div>
		</div>

		<div class="wpgmza-row">
			<div class="wpgmza-col-3"></div>
			<div class="wpgmza-col-9">
				<button id="export-json" class="wpgmza_general_btn wpgmza-button wpgmza-button-primary">
					<?php esc_html_e( 'Export', 'wp-google-maps' ); ?>
				</button>
			</div>
		</div>
	</div>

	<!-- CSV Exports -->
	<div class="export-options wpgmza-hidden" data-type="csv">
		<div class="wpgmza-row">
			<div class="wpgmza-col-3"><?php _e("Data Type", "wp-google-maps"); ?></div>
			<div class="wpgmza-col-9">
				<select class="export-csv-type-selector">
					<option value="maps"><?php _e("Maps", "wp-google-maps"); ?></option>
					<option value="markers" selected><?php _e("Markers", "wp-google-maps"); ?></option>
					<option value="polygons"><?php _e("Polygons", "wp-google-maps"); ?></option>
					<option value="polylines"><?php _e("Polylines", "wp-google-maps"); ?></option>
					<option value="circles"><?php _e("Circles", "wp-google-maps"); ?></option>
					<option value="rectangles"><?php _e("Rectangles", "wp-google-maps"); ?></option>
					<option value="pointlabels"><?php _e("Point Labels", "wp-google-maps"); ?></option>
					<option value="imageoverlays"><?php _e("Image Overlays", "wp-google-maps"); ?></option>
					<option value="datasets"><?php _e("Heatmap Datasets", "wp-google-maps"); ?></option>
				</select>
			</div>
		</div>

		<br>

		<div class="wpgmza-row">
			<div class="wpgmza-col-3">
				<?php _e("Maps", "wp-google-maps"); ?>
			</div>

			<div class="wpgmza-col-9">
				<!-- We should do-away with the inline styles, this is not ideal -->
				<div id="wpgmza-export-csv-map-panel" style="margin:0 0 1em 0;width:100%;">
					<table class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;">
						<thead style="display:block;border-bottom:1px solid #e1e1e1;">
							<tr style="display:block;width:100%;">
								<th style="width:2.2em;border:none;"/>
								<th style="width:80px;border:none;">
									<?php esc_html_e( 'ID', 'wp-google-maps' ); ?>
								</th>
								<th style="border:none;">
									<?php esc_html_e( 'Title', 'wp-google-maps' ); ?>
								</th>
							</tr>
						</thead>
						<tbody style="display:block;max-height:370px;overflow-y:scroll;">
						</tbody>
					</table>
					<button id="maps_export_csv_select_all" class="wpgmza_general_btn wpgmza-button wpgmza-button-secondary">
						<?php esc_html_e( 'Select All', 'wp-google-maps' ); ?>
					</button>
					<button id='maps_export_csv_select_none' class='wpgmza_general_btn wpgmza-button wpgmza-button-secondary'>
						<?php esc_html_e( 'Select None', 'wp-google-maps' ); ?>
					</button>
					<br/>
					<br/>
				</div>
			</div>
		</div>

		<br>
		<div class="wpgmza-row">
			<div class="wpgmza-col-3"></div>
			<div class="wpgmza-col-9">
				<button id="export-csv" class="wpgmza_general_btn wpgmza-button wpgmza-button-primary">
					<?php esc_html_e( 'Export', 'wp-google-maps' ); ?>
				</button>
			</div>
		</div>
	</div>

	<!-- KML Export -->
	<div class="export-options wpgmza-hidden" data-type="kml">
		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-3">
				<?php _e("Data Type", "wp-google-maps"); ?>
			</div>
			<div class="wpgmza-col-9">
				<!-- Markers -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="markers_kml" class="kml_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="markers_kml"></label>
						<label for="markers_kml">
							<?php esc_html_e( 'Markers', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>

				<!-- Polylines -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="polylines_kml" class="kml_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="polylines_kml"></label>
						<label for="polylines_kml">
							<?php esc_html_e( 'Polylines', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>

				<!-- Polygons -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="polygons_kml" class="kml_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="polygons_kml"></label>
						<label for="polygons_kml">
							<?php esc_html_e( 'Polygons', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>
			</div>
		</div>


		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-3">
				<?php _e("Preserve Styles", "wp-google-maps"); ?>
			</div>
			<div class="wpgmza-col-9">
				<!-- Markers -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="apply_styles_kml" class="kml_apply_styles cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="apply_styles_kml"></label>
						<label for="apply_styles_kml">
							<?php esc_html_e( 'Shape styles', 'wp-google-maps' ); ?> <?php esc_html_e("(beta)", "wp-google-maps"); ?>
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-3">
				<?php _e("Map", "wp-google-maps"); ?>
			</div>
			<div class="wpgmza-col-9 export-kml-options-map-select">

			</div>
		</div>
		<br>
		<div class="wpgmza-row">
			<div class="wpgmza-col-3"></div>
			<div class="wpgmza-col-9">
				<button id="export-kml" class="wpgmza_general_btn wpgmza-button wpgmza-button-primary">
					<?php esc_html_e( 'Export', 'wp-google-maps' ); ?>
				</button>
			</div>
		</div>
	</div>

	<!-- Settings Export -->
	<div class="export-options wpgmza-hidden" data-type="settings">
		<div class="wpgmza-row wpgmza-margin-b-20">
			<div class="wpgmza-col-3">
				<?php _e("Data Type", "wp-google-maps"); ?>
			</div>
			<div class="wpgmza-col-9">
				<!-- Global Settings -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="global_configuration" class="configuration_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="global_configuration"></label>
						<label for="global_configuration">
							<?php esc_html_e( 'Global Settings', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>

				<!-- Styling Settings -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="styling_configuration" class="configuration_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="styling_configuration"></label>
						<label for="styling_configuration">
							<?php esc_html_e( 'Styling Settings', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>

				<!-- Styling Settings -->
				<div class="tab-row has-hint">
					<div class="switch switch-inline">
						<input id="keys_configuration" class="configuration_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
						<label for="keys_configuration"></label>
						<label for="keys_configuration">
							<?php esc_html_e( 'API Keys', 'wp-google-maps' ); ?>
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="wpgmza-row">
			<div class="wpgmza-col-3">&nbsp;</div>
			<div class="wpgmza-col-9">
				<div class="wpgmza-notice wpgmza-card wpgmza-shadow-high export-global-settings-notice">
					<?php esc_html_e("Export will not include any map specific data such as markers, categories, shapes or marker fields.", "wp-google-maps"); ?>
					<br>
					<?php esc_html_e("Instead, this tool focuses on exporting global cofiguration options to assist with migrations to new sites.", "wp-google-maps"); ?>
				</div>
			</div>
		</div>

		<br>
		<div class="wpgmza-row">
			<div class="wpgmza-col-3"></div>
			<div class="wpgmza-col-9">
				<button id="export-settings" class="wpgmza_general_btn wpgmza-button wpgmza-button-primary">
					<?php esc_html_e( 'Export', 'wp-google-maps' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Backup -->
<div id="backups-tab" style="display:none;">
	<div class="heading">
		<?php _e("Backups (Beta)", "wp-google-maps"); ?>
	</div>

	<div class="notice notice-success wpgmza_backup_notice">

	</div>

	<div class="wpgmza_backups_info">
		<p class="description"><?php _e("WP Go Maps will automatically backup your data before an update is installed and when an import is performed. Manual backups can also be generated below.", "wp-google-maps"); ?></p>

		<p><?php _e("Automated backups will be removed when new backups of the same type are generated. Manual backups will not be removed by the backup module.", "wp-google-maps"); ?></p>

		<p><strong><?php _e("Important Note", "wp-google-maps"); ?>:</strong> <?php _e("This feature is in early beta and data backups cannot be guaranteed as they are dependent on file write access on your server", "wp-google-maps"); ?></p>
	</div>

	<div id="wpgmza_backups_content">

	</div>

	<p>
		<a href="#" id="wpgmza_new_backup_btn" class="wpgmza-button wpgmza-button-primary"><?php _e("Create Backup", "wp-google-maps"); ?></a>
	</p>
</div>