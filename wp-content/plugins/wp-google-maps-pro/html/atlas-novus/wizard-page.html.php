<?php
	$currentUser = wp_get_current_user();
?>
<div class="wrap wpgmza-wrap wpgmza-writeup-block wpgmza-shadow-high wpgmza-wizard">
	<h1><?php _e("Welcome", "wp-google-maps"); ?>, <?php echo ucfirst($currentUser->display_name); ?></h2>
	<h2><?php _e("Let's make a map you'll love!", "wp-google-maps"); ?></h2>

	<hr>
 	
	<div class="wpgmza-wizard-steps">
		<div class="step" data-step="0">
			<h3><?php _e("What should we call your map?", "wp-google-maps"); ?></h3>
			<input type="text" class="wpgmza-text-align-center" name="map_title">
		</div>

		<div class="step" data-step="1">
			<h3><?php _e("Where is this map based?", "wp-google-maps"); ?></h3>
			<input type="text" class="wpgmza-text-align-center wpgmza-address" name="map_start_address">
		</div>

		<div class="step" data-step="2">
			<h3><?php _e("Where should we place your first marker?", "wp-google-maps"); ?></h3>
			<input type="text" class="wpgmza-text-align-center wpgmza-address" name="first_marker">

	        <p><?php _e("Leave this empty to skip marker creation", "wp-google-maps"); ?></p>
		</div>

		<div class="step" data-step="3">
			<h3><?php _e("Do you need a store locator?", "wp-google-maps"); ?></h3>
			<select name="store_locator_enabled">
				<option value="1"><?php _e("Yes!", "wp-google-maps"); ?></option>
				<option value="0"><?php _e("No thanks", "wp-google-maps"); ?></option>
			</select>

			<input type='hidden' name='store_locator_component_anchor' value='0'>
		</div>

		<div class="step" data-step="4">
			<h3><?php _e("Would you like to offer directions to users?", "wp-google-maps"); ?></h3>
			<select name="directions_enabled">
				<option value="1"><?php _e("Yes!", "wp-google-maps"); ?></option>
				<option value="0"><?php _e("No thanks", "wp-google-maps"); ?></option>
			</select>

			<input type="hidden" name='directions_box_component_anchor' value='1'>
		</div>

		<div class="step" data-step="5">
			<h3><?php _e("Do you want a marker listing?", "wp-google-maps"); ?></h3>
			<select name="wpgmza_listmarkers_by">
				<option value="8"><?php _e("Yes!", "wp-google-maps"); ?></option>
				<option value="0"><?php _e("No thanks", "wp-google-maps"); ?></option>
			</select>

			<input type="hidden" name='wpgmza_iw_type' value='4'>
			<input type="hidden" name='marker_listing_component_anchor' value='1'>
		</div>

		<div class="step" data-step="6">
			<h3><?php _e("How do you measure distances?", "wp-google-maps"); ?></h3>
			<select name="store_locator_distance">
				<option value="0"><?php _e("Kilometers", "wp-google-maps"); ?></option>
				<option value="1"><?php _e("Miles", "wp-google-maps"); ?></option>
			</select>
		</div>


		<div class="step" data-step="7">
			<h3><?php _e("Great work, we're all set!", "wp-google-maps"); ?></h3>
			<p><?php _e("We have everything we need to set up your map, you can go back and make changes, or go ahead and create the map", "wp-google-maps"); ?></p>
			<p><?php _e("The map editor will automatically open, allowing you to customize your map even further!", "wp-google-maps"); ?></p>
		</div>

		<div class="step-loader wpgmza-pos-relative wpgmza-hidden">
			<div class="wpgmza-preloader">
				<div></div>
				<div></div>
			</div>

			<h3>Creating map...</h3>
		</div>

		<div class="wpgmza-row step-controller">
			<div class="wpgmza-col-6">
				<div class="wpgmza-text-align-left">
					<div class="wpgmza-button prev-step-button">
		        		<i class="fa fa-chevron-left" aria-hidden="true" style="margin-left: 0; margin-right: 10px;"></i>
						<span><?php _e("Prev","wp-google-maps"); ?></span>
		        	</div>
		        </div>
			</div>
			
			<div class="wpgmza-col-6">
				<div class="wpgmza-text-align-right">
					<div class="wpgmza-button next-step-button" data-next="<?php _e("Next","wp-google-maps"); ?>" data-final="<?php _e("Create map","wp-google-maps"); ?>">
						<span><?php _e("Next","wp-google-maps"); ?></span>
		        		<i class="fa fa-chevron-right" aria-hidden="true"></i>
		        	</div>
		        </div>
		    </div>
		</div>
		
	</div>
</div>
