<?php
function mpfy_crb_load_settings() {
	if (!defined('MPFY_CARBON_PLUGIN_ROOT')) {
		include_once(MAPIFY_PLUGIN_DIR . '/lib/carbon-datastore.php');
		include_once(MAPIFY_PLUGIN_DIR . '/lib/carbon-functions.php');
	}

	add_image_size('mpfy_location_tag', 31, 27, false);
}
add_action('after_setup_theme', 'mpfy_crb_load_settings', 11);
