<?php
add_filter('mpfy_map_location_custom_fields', 'mpfy_imp_map_location_custom_fields');
function mpfy_imp_map_location_custom_fields($custom_fields) {

	if ( isset( $custom_fields['map_location_city'] ) ) {
		$custom_fields['map_location_city']->help_text('Used for location search in image mode.');
	}
	if ( isset( $custom_fields['map_location_zip'] ) ) {
		$custom_fields['map_location_zip']->help_text('Used for location search in image mode.');
	}

	return $custom_fields;
}
