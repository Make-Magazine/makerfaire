<?php
function mpfy_mlqm_migrate() {
	$locations = Mpfy_Map_Location::get_all_locations();

	foreach ($locations as $id => $title) {
		mpfy_duplicate_location_map_meta($id);
	}
}
