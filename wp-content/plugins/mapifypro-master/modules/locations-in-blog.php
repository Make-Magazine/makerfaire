<?php
function mpfy_add_locations_to_blog($query) {
	if (is_admin()) {
		return false;
	}
	if ($query->is_main_query() && $query->get('category_name')) {
		$query->set('post_type', array('post', 'map-location'));
	}
}
add_action('pre_get_posts', 'mpfy_add_locations_to_blog', 100);

function mpfy_lb_add_category_taxonomy() {
	register_taxonomy_for_object_type('category', 'map-location');
}
add_action('mpfy_post_types_registered', 'mpfy_lb_add_category_taxonomy');