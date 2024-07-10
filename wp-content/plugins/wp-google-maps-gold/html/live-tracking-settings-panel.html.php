<div>
	<h3>
		<?php
		esc_html_e('Live Tracking Settings', 'wp-google-maps');
		?>
	</h3>
	
	<fieldset>
		<legend>
			<?php
			esc_html_e('Enable Realtime Updates', 'wp-google-maps');
			?>
		</legend>

		<div class="switch switch-inline">
			<input 
				id="enable_live_tracking" 
				name="enable_live_tracking" 
				class="cmn-toggle cmn-toggle-yes-no"
				type="checkbox"/> 
				
			<label
				data-on="<?php esc_html_e('Yes', 'wp-google-maps'); ?>" 
				data-off="<?php esc_html_e('No', 'wp-google-maps'); ?>"
				for="enable_live_tracking"></label>

			<label for="enable_live_tracking">
			<small>
				<?php esc_html_e('Refresh maps at a set interval to allow users to watch location changes in realtime, on your website.', 'wp-google-maps'); ?>
			</small>
			</label>
		</div>

		
	</fieldset>

	<fieldset>
		<legend>
			<?php
			esc_html_e('Refresh Interval', 'wp-google-maps');
			?>
		</legend>

		<div>
			<select name='liveTrackingRefreshInterval'>
				<option value="60000"><?php esc_html_e('60 sec (Default)', 'wp-google-maps'); ?></option>
				<option value="45000"><?php esc_html_e('45 sec', 'wp-google-maps'); ?></option>
				<option value="30000"><?php esc_html_e('30 sec', 'wp-google-maps'); ?></option>
				<option value="20000"><?php esc_html_e('20 sec', 'wp-google-maps'); ?></option>
				<option value="15000"><?php esc_html_e('15 sec', 'wp-google-maps'); ?></option>
				<option value="10000"><?php esc_html_e('10 sec', 'wp-google-maps'); ?></option>
				<option value="5000"><?php esc_html_e('5 sec', 'wp-google-maps'); ?></option>
			</select>
			
			<div>
				<small>
					<?php esc_html_e('Adjust how frequently you would like realtime updates to be refreshed, this will increase resource load on your server.', 'wp-google-maps') ?>
					<br>
					<?php esc_html_e('Refresh intervals below 15 seconds will require a powerful server, as these frequencies tend to have a high impact on server load.', 'wp-google-maps') ?>
				</small>
			</div>
		</div>
	</fieldset>
	
	<h3>
		<?php
		esc_html_e('Live Tracking Devices', 'wp-google-maps');
		?>
		
		<i id="wpgmza-refresh-live-tracking-devices" class="fa fa-refresh" aria-hidden="true"></i>
	</h3>
	
	<p>
		<?php
		esc_html_e('Devices which have attempted to pair with your site will appear here. You must approve devices before they will appear on the map.', 'wp-google-maps');
		?>
	</p>
	
	<table id="wpgmza-live-tracking-devices" class="wp-list-table widefat fixed wpgmza-listing wpgmza-tracking-table">
		<thead>
			<tr>
				<td>
					<?php
					esc_html_e('Device ID', 'wp-google-maps');
					?>
				</td>
				<td>
					<?php
					esc_html_e('Name', 'wp-google-maps');
					?>
				</td>
				<td>
					<?php
					esc_html_e('Draw Polylines', 'wp-google-maps');
					?>
				</td>
				<td>
					<?php
					esc_html_e('Line Color and Weight', 'wp-google-maps');
					?>
				</td>
				<td>
					<?php
					esc_html_e('Manage Device', 'wp-google-maps');
					?>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td data-name="deviceID"></td>
				<td data-name="name"></td>
				<td>
					<label class='wpgmza-button-state-checkbox'>
						<input data-ajax-name="drawPolylines" type="checkbox"/>
						<span data-state='unchecked' class="wpgmza-button wpgmza-tracking-positive button"><?php esc_html_e('Enable', 'wp-google-maps'); ?></span>
						<span data-state='checked' class="wpgmza-button wpgmza-tracking-negative button"><?php esc_html_e('Disable', 'wp-google-maps'); ?></span>
					</label>
				</td>
				<td>
					<div class="wpgmza-tracking-polyline-config">
						<input data-ajax-name="polylineColor" type="color"/>
						<input data-ajax-name="polylineWeight" type="number" min="1" max="50"/>
					</div>
				</td>
				<td>
					<label class='wpgmza-button-state-checkbox'>
						<input data-ajax-name="approved" type="checkbox"/>
						<span data-state='unchecked' class="wpgmza-button wpgmza-tracking-positive button">
							<i class="fa fa-check-circle-o"></i> 
							<?php esc_html_e('Approve', 'wp-google-maps'); ?>
						</span>
						
						<span data-state='checked' class="wpgmza-button wpgmza-tracking-negative button">
							<i class="fa fa-times-circle-o"></i> 
							<?php esc_html_e('Revoke', 'wp-google-maps'); ?>
						</span>
					</label>
					
					<input type="hidden" data-ajax-name="id"/>
				</td>
			</tr>
		</tbody>
	</table>

	<p>
		<?php 
			_e('Need a link to our app? Take a look at our <a href="https://wpgmaps.com/location-tracking-app/" target="_BLANK">app directory!</a>', 'wp-google-maps');
		?>
	</p>
	
</div>