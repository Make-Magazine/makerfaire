<?php

function mpfy_mc_clustering_enabled($value, $map_id) {
	$map = new Mpfy_Map($map_id);
	$value = $map->get_clustering_enabled();
	return $value;
}
add_filter('mpfy_clustering_enabled', 'mpfy_mc_clustering_enabled', 10, 2);
