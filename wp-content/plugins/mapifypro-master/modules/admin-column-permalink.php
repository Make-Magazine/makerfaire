<?php
function mpfy_cp_add_permalink_column($columns) {
	$title_index = 0;
	foreach ($columns as $k => $t) {
		if ($k == 'title') {
			break;
		}
		$title_index ++;
	}
	$columns = array_slice($columns, 0, $title_index + 1) + array('url_extension'=>'URL Extension') + array_slice($columns, $title_index + 1);	

    return $columns;
}

function mpfy_cp_permalink_column_content($column, $post_id) {
	$map_location = new Mpfy_Map_Location($post_id);
	$has_map = $map_location->get_maps();
	if (!$has_map) {
		return false;
	}

	if ($column == 'url_extension') {
		echo 'Tooltip: <span style="display: inline-block; padding: 1px 5px; background: #eee; border-radius: 3px; font-size: 11px;">?mpfy-pin=' . $post_id . '&mpfy-tooltip</span><br />';
		echo 'Popup: <span style="display: inline-block; padding: 1px 5px; background: #eee; border-radius: 3px; font-size: 11px;">?mpfy-pin=' . $post_id . '</span>';
	}
}

function mpfy_cp_register_columns() {
	$post_types = mpfy_get_supported_post_types();
	foreach ($post_types as $pt) {
		add_filter('manage_edit-' . $pt . '_columns', 'mpfy_cp_add_permalink_column');
		add_action('manage_' . $pt . '_posts_custom_column', 'mpfy_cp_permalink_column_content', 10, 2);
	}
}
add_action('admin_init', 'mpfy_cp_register_columns');
