<?php
require_once('updater/all.php');

// Update hook and notice
function mpfy_plugin_update_hook() {
	if (isset($_GET['mpfy-update'])) {
		mpfy_update();
	}

	if (version_compare(mpfy_get_version(), MAPIFY_PLUGIN_VERSION) < 0) {
		add_action( 'admin_notices', 'mpfy_show_update_notice');
	}
}
add_action('admin_menu', 'mpfy_plugin_update_hook');

// Execute update
function mpfy_update() {
	include_once(MAPIFY_PLUGIN_DIR . '/updater.php');
	$updates_done = 0;
	foreach ($versions as $version => $updater) {
		if (version_compare(mpfy_get_version(), $version) < 0) {
			if ($updater) {
				$updater();
			}
			update_option('mpfy_plugin_version', $version);
			$updates_done ++;
		}
	}

	if ($updates_done > 0) {
		update_option('mpfy_flush_required', 'y');
		add_action('admin_notices', 'mpfy_show_update_success_notice');
	}

	// finally, update database to the current version
	if (version_compare(mpfy_get_version(), MAPIFY_PLUGIN_VERSION) < 0) {
		update_option('mpfy_plugin_version', MAPIFY_PLUGIN_VERSION);
	}
}

// An update has occurred - show data update notice
function mpfy_show_update_notice(){
	echo '<div class="error"><p>Warning - your ' . MAPIFY_PLUGIN_NAME . ' plugin data must be updated. Please backup your data and <a href="' . add_query_arg('mpfy-update', '1', admin_url('/')) . '">click here</a> to proceed.</p></div>';
}

// A data update has just completed - show success notice
function mpfy_show_update_success_notice(){
	echo '<div class="updated"><p>Your ' . MAPIFY_PLUGIN_NAME . ' data has been updated.</p></div>';
}
