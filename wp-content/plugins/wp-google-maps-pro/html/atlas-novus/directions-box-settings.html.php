<div id="wpgmza-directions-box-settings">
	<div id="open-route-service-key-notice">
		<?php
		_e('This feature requires an API key from OpenRouteService to function. Please obtain a key from <a href="https://openrouteservice.org/dev/#/home">the OpenRouteService Developer Console</a> and paste your key into Maps &rarr; Settings &rarr; Advanced in the "OpenRouteService API key" field.', 'wp-google-maps')
		?>		
	</div>

	<!-- Enable directions -->
	<fieldset class="wpgmza-row">
		<div class="wpgmza-col">
			<legend><?php _e("Enable Directions", "wp-google-maps"); ?></legend>
		</div>
		<div class="wpgmza-col">
			<div class='switch'>
				<input type='checkbox' id='directions_enabled' name='directions_enabled' class="postform cmn-toggle cmn-toggle-round-flat"/>
				<label for='directions_enabled'></label>
			</div>
		</div>
	</fieldset>
		
	<!-- Not relevant to atlas novus -->
	<fieldset id="wpgmza-directions-box-style" class="wpgmza-hidden">
		<legend><?php _e("Directions Box Style", "wp-google-maps"); ?> </legend>
		<span>
			<ul>
				<li>
					<input name="directions_box_style"
						type="radio"
						value="default"
						checked/>
					<?php
					esc_html_e('Default', 'wp-google-maps');
					?>
				</li>
				<li>
					<input name="directions_box_style"
						type="radio"
						value="modern"/>
					<?php
					esc_html_e('Modern', 'wp-google-maps');
					?>
				</li>
			</ul>
		</span>
	</fieldset>
	
	<!-- Anchor -->
	<fieldset class="wpgmza-row">
		<legend><?php _e("Placement", "wp-google-maps"); ?></legend>
		
		<select name="directions_box_component_anchor" id="directions_box_component_anchor" class='wpgmza-anchor-control' data-default="LEFT" data-anchors="LEFT,RIGHT,ABOVE,BELOW"></select>
	</fieldset>


	<!-- Open by default -->
	<fieldset id="wpgmza-directions-box-open-by-default">
		<legend><?php _e("Open by default","wp-google-maps"); ?></legend>
		
		<select id="wpgmza_dbox" name="dbox" class="postform">
			<option value="1">
				<?php _e("No", "wp-google-maps"); ?>
			</option>
			<option value="6">
				<?php _e("Yes", "wp-google-maps"); ?>
			</option>
		</select>
	</fieldset>

	<!-- Directions bbox width - Not relevant in Atlas novus -->
	<fieldset id="wpgmza-directions-box-width" class="wpgmza-hidden">
		<legend><?php _e("Directions Box Width", "wp-google-maps"); ?></legend>
		<div class="wpgmza-inline-field">
			<input id="dbox_width"
				name="dbox_width"
				size="4"
				maxlength="4"
				value="100"
				class="small-text"/>
			<select name="wpgmza_dbox_width_type">
				<option value="%">
					<?php _e('%', 'wp-google-maps'); ?>
				</option>
				<option value="px">
					<?php _e('px', 'wp-google-maps'); ?>
				</option>
			</select>
		</div>
	</fieldset>
	
	<!-- Default To -->
	<fieldset>
		<legend><?php _e("Default 'To' address", "wp-google-maps"); ?></legend>
		<div class="wpgmza-inline-field">
			<input name="default_to" class="wpgmza-address" type="text" />
		</div>
	</fieldset>
	
	<!-- Default From -->
	<fieldset>
		<legend><?php _e("Default 'From' address", "wp-google-maps"); ?></legend>
		<div class="wpgmza-inline-field">
			<input name="default_from" class="wpgmza-address" type="text" />
		</div>
	</fieldset>
	
	<!-- Behaviour -->
	<fieldset>
		<legend><?php _e('Behaviour', 'wp-google-maps'); ?></legend>
		<ul>
			<li>
				<label>
					<input name="directions_behaviour" type="radio" value="default" checked/>
					<?php _e("Default", "wp-google-maps"); ?>
				</label>
				
				<div class="hint"><?php _e("Display directions on the page", "wp-google-maps"); ?></div>
			</li>

			<li>
				<label>
					<input name="directions_behaviour" type="radio" value="external"/>
					<?php _e("External", "wp-google-maps"); ?>
				</label>
				
				<div class="hint"><?php _e("Open Google / Apple maps in a new tab", "wp-google-maps"); ?></div>
			</li>

			<li>
				<label>
					<input name="directions_behaviour" type="radio" value="intelligent"/>
					<?php _e("Intelligent", "wp-google-maps"); ?>
				</label>
				
				<div class="hint"><?php _e("Display directions on the page on desktop devices, open Google / Apple maps mobile app on mobile devices", "wp-google-maps"); ?></div>
			</li>

			<li>
				<label>
					<input name="force_google_directions_app" type="checkbox"/>
					<?php _e("Force Google Maps mobile app", "wp-google-maps"); ?>
				</label>
				
				<div class="hint"><?php _e("Force iOS devices to use the Google Maps mobile app for directions", "wp-google-maps"); ?></div>
			</li>
		</ul>
	</fieldset>

	<!-- Origin Icon -->
	<fieldset>
		<legend><?php _e("Origin Icon", "wp-google-maps"); ?></legend>
		<div id="directions_origin_icon_picker_container"></div>
    </fieldset>

    <!-- Desitnation Icon -->
    <fieldset>
		<legend><?php _e("Destination Icon", "wp-google-maps"); ?></legend>
    	<div id="directions_destination_icon_picker_container"></div>
    </fieldset>
	
	<!-- Route color -->
	<fieldset>
		<legend><?php _e("Route Color", "wp-google-maps"); ?></legend>
		<input id="directions_route_stroke_color" name="directions_route_stroke_color" type="text" data-support-palette="false" data-support-alpha="false" data-container=".map_wrapper" class="wpgmza-color-input" value="#4F8DF5" />
	</fieldset>

	<!-- Route Weight -->
  	<fieldset>
    	<legend><?php _e("Route Weight", "wp-google-maps"); ?></legend>
		<input id="directions_route_stroke_weight"
			type="number"
			name="directions_route_stroke_weight"
			max="100"
			min="1"
			value="4"
			class="small-text"/>
	</fieldset>

	<!-- Route Opacity -->
	<fieldset>
		<legend><?php _e("Route Opacity", "wp-google-maps"); ?></legend>
		<input id="directions_route_stroke_opacity"
			type="number"
			name="directions_route_stroke_opacity"
			max="1"
			min="0"
			step="0.01"
			value="0.8"
			class="number"/>
	</fieldset>

	<!-- Fit map bounds -->
	<fieldset class="wpgmza-row">
		<div class="wpgmza-col">
			<legend><?php _e("Fit map bounds to route", "wp-google-maps"); ?></legend>
		</div>
		
		<div class="wpgmza-col">
			<div class='switch switch-inline'>
				<input type='checkbox' 
					id='directions_fit_bounds_to_route' 
					name='directions_fit_bounds_to_route' 
					class='postform cmn-toggle cmn-toggle-round-flat'/>
				<label for='directions_fit_bounds_to_route'></label>
			</div>
		</div>
	</fieldset>
</div>