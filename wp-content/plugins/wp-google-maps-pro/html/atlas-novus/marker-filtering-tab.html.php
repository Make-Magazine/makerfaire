<!-- Map Settings - Marker Fields Tab -->
<div class="grouping" data-group="map-settings-marker-field">
	<div class="heading block has-back">
		<div class="item caret-left" data-group='map-settings'></div>
		<?php _e('Marker Fields', 'wp-google-maps'); ?>
	</div>

	<div class="settings" id="marker-filtering">
		<fieldset>
			<label><?php _e('Enable field filtering', 'wp-google-maps'); ?>:</label>
			<ul></ul>
		</fieldset>
		
		<div class="wpgmza-card wpgmza-shadow wpgmza-notice wpgmza-pos-relative" id="wpgmza-marker-filtering-tab-no-custom-fields-warning">
			<?php _e('You have no custom fields to filter on. Please add some in order to add custom field filters.', 'wp-google-maps'); ?>
		</div>
	</div>

</div>