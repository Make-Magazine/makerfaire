<div class="wpgmza-tabs-container wpgmza-wrap" id="wpgmaps_tabs">
	<!-- Tabs -->
	<ul class='settings-tabs-nav'>
		<?php 
		    /* Developer Hook (Action) - Add additional tabs to tools page */     
			do_action( 'wpgmza_admin_advanced_options_tabs' );
		?>

		<li class="wpgmza-legacy-import-option">
			<a href="#tabs-1">
				<?php esc_html_e( 'Map Data', 'wp-google-maps' ); ?> <small><?php esc_html_e("(Legacy)", "wp-google-maps"); ?></small>
			</a>
		</li>
		<li class="wpgmza-legacy-import-option">
			<a href="#tabs-2">
				<?php esc_html_e( 'Marker Data', 'wp-google-maps' ); ?> <small><?php esc_html_e("(Legacy)", "wp-google-maps"); ?></small>
			</a>
		</li>
		<li class="wpgmza-legacy-import-option">
			<a href="#tabs-3">
				<?php esc_html_e( 'Polygon Data', 'wp-google-maps' ); ?> <small><?php esc_html_e("(Legacy)", "wp-google-maps"); ?></small>
			</a>
		</li>
		<li class="wpgmza-legacy-import-option">
			<a href="#tabs-4">
				<?php esc_html_e( 'Polyline Data', 'wp-google-maps' ); ?> <small><?php esc_html_e("(Legacy)", "wp-google-maps"); ?></small>
			</a>
		</li>
		<li>
			<a href="#utilities">
				<?php esc_html_e( 'Utilities', 'wp-google-maps' ); ?>
			</a>
		</li>
	</ul>

	<?php 
		/* Developer Hook (Action) - Add additional tab content to tools page */     
		do_action( 'wpgmza_admin_advanced_options' );

		// Legacy nonce

		global $wpgmza_post_nonce;

		$real_post_nonce = wp_create_nonce('wpgmza');
	?>

	<!-- Map Data (Legacy) -->
	<div id="tabs-1">
		<div class="heading">
			<?php _e("Map Data", "wp-google-maps"); ?>
		</div>

		<form enctype="multipart/form-data" method="POST">
			<input name='real_post_nonce' value='<?php echo $real_post_nonce; ?>' type='hidden'/>
            <input name="wpgmza_security" type="hidden" value="<?php echo $wpgmza_post_nonce; ?>" />

			<!-- File -->
			<div class="tab-row">
				<div class="title">
					<?php esc_html_e('Upload Map CSV File', 'wp-google-maps'); ?>
				</div>

		    	<input name="wpgmza_csv_map_import" id="wpgmza_csv_map_import" type="file"/>
		    </div>
			
			<!-- Replace -->
			<div class="tab-row">
				<div class="title"><?php _e("Replace existing data with data in file","wp-google-maps"); ?></div>
            	<div class='switch switch-inline'>
            		<input name="wpgmza_csvreplace_map" id='wpgmza_csvreplace_map' class='cmn-toggle cmn-toggle-round-flat' type="checkbox" value="Yes" /> 
            		<label for='wpgmza_csvreplace_map'></label>
            	</div>
		    </div>        

		    <!-- Import Button -->
		    <div class="tab-row">
		    	<div class="title"></div>
		    	<div class="wpgmza-inline-field">
            		<input class='wpgmza_general_btn wpgmza-button wpgmza-button-primary' type="submit" name="wpgmza_uploadcsv_btn" value="<?php _e("Import CSV","wp-google-maps"); ?>" /> 
            		&nbsp;
					<a class='wpgmza-button' href="?page=wp-google-maps-menu-advanced&amp;action=export_all_maps" target="_BLANK" title="<?php _e("Download ALL map data to a CSV file","wp-google-maps"); ?>">
			    		<?php _e("Download CSV","wp-google-maps"); ?>
			    	</a>
            	</div>
		    </div>
        </form>
    </div>

    <!-- Marker Data (Legacy) -->
    <div id="tabs-2">
    	<div class="heading">
			<?php _e("Marker Data", "wp-google-maps"); ?>
		</div>

        <form enctype="multipart/form-data" method="POST">
			<input name='real_post_nonce' value='<?php echo $real_post_nonce; ?>' type='hidden'/>
            <input name="wpgmza_security" type="hidden" value="<?php echo $wpgmza_post_nonce; ?>" />
			
			<!-- File -->
			<div class="tab-row">
				<div class="title"><?php _e("Upload Marker CSV File", "wp-google-maps"); ?></div>	                
            	<input name="wpgmza_csvfile" id="wpgmza_csvfile" type="file"/>
            </div>

            <!-- Replace -->
            <div class="tab-row">
            	<div class="title"><?php _e("Replace existing data with data in file","wp-google-maps"); ?></div>
            	<div class='switch switch-inline'>
            		<input name="wpgmza_csvreplace" id='wpgmza_csvreplace' class='cmn-toggle cmn-toggle-round-flat' type="checkbox" value="Yes" /> 
            		<label for='wpgmza_csvreplace'></label>
            	</div>
            </div>

            <!-- Geocode -->
            <div class="tab-row">
            	<div class="title"><?php _e("Geocode","wp-google-maps"); ?></div>

                <div class='switch switch-inline'>
                	<input name="wpgmza_geocode" id='wpgmza_geocode' class='cmn-toggle cmn-toggle-round-flat' type="checkbox" value="Yes" /> 
                	<label for='wpgmza_geocode'></label>
                	<label for='wpgmza_geocode'><?php _e("Automatically geocode addresses to GPS co-ordinates if none are supplied", "wp-google-maps"); ?></label>
               	</div>
            </div>
            
            <!-- API Key -->
            <div class="tab-row" id='wpgmza_geocode_conditional'>
            	<div class="title"><?php _e("Google API Key (Required)","wp-google-maps"); ?></div>
            	<input name="wpgmza_api_key" type="text" value="" />
            </div>

            <!-- Buttons -->
            <div class="tab-row">
            	<div class="title"></div>
            	<div class="wpgmza-inline-field">
            		<input class='wpgmza_general_btn wpgmza-button wpgmza-button-primary' type="submit" name="wpgmza_uploadcsv_btn" value="<?php _e("Import CSV","wp-google-maps"); ?>" />
            		&nbsp;
            		<a class='wpgmza-button' href="?page=wp-google-maps-menu-advanced&amp;action=wpgmza_csv_export" target="_BLANK" title="<?php _e("Download ALL marker data to a CSV file","wp-google-maps"); ?>">
            			<?php _e("Download CSV","wp-google-maps"); ?>
        			</a>
            	</div>
			</div>

        </form>
    </div>
    
    <!-- Polygon Data (Legacy) -->
    <div id="tabs-3">
    	<div class="heading">
			<?php _e("Polygon Data", "wp-google-maps"); ?>
		</div>

    	<form enctype="multipart/form-data" method="POST">
			<input name='real_post_nonce' value='<?php echo $real_post_nonce; ?>' type='hidden'/>
            <input name="wpgmza_security" type="hidden" value="<?php echo $wpgmza_post_nonce; ?>" />
	        
	        <div class="tab-row">
	        	<div class="title"><?php _e("Upload Polygon CSV File","wp-google-maps"); ?></div>
                <input name="wpgmza_csv_polygons_import" id="wpgmza_csv_polygons_import" type="file"/>
	        </div>

	        <div class="tab-row">
	        	<div class="title"><?php _e("Replace existing data with data in file","wp-google-maps"); ?></div>

                <div class='switch switch-inline'>
                	<input name="wpgmza_csvreplace_polygon" id='wpgmza_csvreplace_polygon' class='cmn-toggle cmn-toggle-round-flat' type="checkbox" value="Yes" /> 
                	<label for='wpgmza_csvreplace_polygon'></label>
                </div>
	        </div>
	                
	        <div class="tab-row">
	        	<div class="title"></div>
	            <div class="wpgmza-inline-field">
	                <input class='wpgmza_general_btn wpgmza-button wpgmza-button-primary' type="submit" name="wpgmza_uploadcsv_btn" value="<?php _e("Import CSV","wp-google-maps"); ?>" />
	            	&nbsp;
	            	<a class='wpgmza-button' href="?page=wp-google-maps-menu-advanced&amp;action=export_polygons" target="_BLANK" title="<?php _e("Download ALL polygon data to a CSV file","wp-google-maps"); ?>">
	            		<?php _e("Download CSV","wp-google-maps"); ?>		
	            	</a>
	            </div>
	        </div>
        </form>
    </div>

    <!-- Polyline Data (Legacy) -->
    <div id="tabs-4">
    	<div class="heading">
			<?php _e("Polyline Data", "wp-google-maps"); ?>
		</div>
    	
    	<form enctype="multipart/form-data" method="POST">
			<input name='real_post_nonce' value='<?php echo $real_post_nonce; ?>' type='hidden'/>
            <input name="wpgmza_security" type="hidden" value="<?php echo $wpgmza_post_nonce; ?>" />
	        
	        <div class="tab-row">
	        	<div class="title"><?php _e("Upload Polyline CSV File","wp-google-maps"); ?></div>
                <input name="wpgmza_csv_polylines_import" id="wpgmza_csv_polylines_import" type="file"/>
	        </div>

	        <div class="tab-row">
	        	<div class="title"><?php _e("Replace existing data with data in file","wp-google-maps"); ?></div>
                <div class='switch switch-inline'>
                	<input name="wpgmza_csvreplace_polyline" id='wpgmza_csvreplace_polyline' class='cmn-toggle cmn-toggle-round-flat' type="checkbox" value="Yes" /> 
                	<label for='wpgmza_csvreplace_polyline'></label>
              	</div>
	        </div>        
				
			<div class="tab-row">
				<div class="title"></div>

	            <div class="wpgmza-inline-field">
	                <input class='wpgmza_general_btn wpgmza-button wpgmza-button-primary' type="submit" name="wpgmza_uploadcsv_btn" value="<?php _e("Import CSV","wp-google-maps"); ?>" />
	                &nbsp;
	                <a class='wpgmza-button' href="?page=wp-google-maps-menu-advanced&amp;action=export_polylines" target="_BLANK" title="<?php _e("Download ALL polyline data to a CSV file","wp-google-maps"); ?>">
	                	<?php _e("Download CSV","wp-google-maps"); ?>
					</a>
	            </div>    

	        </div>
        </form>
    </div>

    <!-- Utilities -->
    <div id='utilities'>
    	<div class="heading">
			<?php _e("Utilities", "wp-google-maps"); ?>
		</div>

		<div class="tab-row">
			<div class="title"><?php _e("Import Log", "wp-google-maps"); ?></div>
			<div class="tab-stretch-right">
				<textarea class='wpgmza_import_log_container script-tag wpgmza-stretch' disabled><?php echo wpgmaps_get_import_logs(); ?></textarea>

				<a id="wpgmza-clear-import-log" href="<?php echo admin_url('admin.php?page=wp-google-maps-menu-advanced&amp;clearlogs=true') ?>" class="wpgmza-button wpgmza-button-primary" title="<?php _e('Clear import logs, this cannot be undone', 'wp-google-maps'); ?>">
					<?php _e("Clear Log", "wp-google-maps"); ?>
				</a>
			</div>
		</div>
		
		<div class="tab-row">
			<div class="title">Duplicate Markers</div>

			<button id='wpgmza-remove-duplicates' type='button' class='wpgmza-button wpgmza-button-primary' title='<?php _e('Delete all markers with matching coordinates, address, title, link and description', 'wp-google-maps'); ?>'>
				<?php _e('Remove duplicate markers', 'wp-google-maps'); ?>
			</button>
		</div>
    </div>
</div>