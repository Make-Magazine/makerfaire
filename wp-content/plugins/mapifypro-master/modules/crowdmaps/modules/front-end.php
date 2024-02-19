<?php

function crowd_add_map_wrap_class( $wrap_classes, $map_id ) {
	$defaults = crowd_get_default_values();
	$enabled  = mpfy_meta_to_bool( $map_id, '_map_crowd_enabled', $defaults['map_crowd_enabled'] );
	
	if ( $enabled ) {
		$wrap_classes[] = 'crowdmaps-enabled-map';
	}

	return $wrap_classes;
}
add_filter( 'mpfy_map_wrap_classes', 'crowd_add_map_wrap_class', 10, 2 );
