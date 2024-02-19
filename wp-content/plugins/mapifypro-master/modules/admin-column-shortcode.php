<?php
function mpfy_add_shortcode_column($columns) {
	$title_index = 0;
	foreach ($columns as $k => $t) {
		if ($k == 'title') {
			break;
		}
		$title_index ++;
	}
	$columns = array_slice($columns, 0, $title_index + 1) + array('shortcode'=>'Shortcode') + array_slice($columns, $title_index + 1);
    return $columns;
}
add_filter('manage_edit-map_columns', 'mpfy_add_shortcode_column');

function mpfy_shortcode_column_content($column, $post_id) {
	if ($column == 'shortcode') {
		echo '<span style="display: inline-block; padding: 1px 5px; background: #eee; border-radius: 3px;">[custom-mapping map_id="' . $post_id . '" height="400"]</span>';
	}
}
add_action('manage_map_posts_custom_column', 'mpfy_shortcode_column_content', 10, 2);
