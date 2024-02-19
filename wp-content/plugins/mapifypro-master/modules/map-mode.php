<?php

define( 'MAPIFY_MAP_STYLES', array(
	'mapifypro-streets'             => 'MapifyPro Streets (3D Buildings)',
	'mapifypro-basic'               => 'MapifyPro Basic',
	'mapifypro-bright'              => 'MapifyPro Bright',
	'road'                          => 'OSM Road',
	'terrain'                       => 'Terrain',
	'watercolor'                    => 'Watercolor',
	'ink'                           => 'Ink',
	'pastel'                        => 'Pastel',
	'stamen-toner-background'       => 'Toner Background',
	'stamen-toner-lite'             => 'Toner Lite',
	'cartodb-positron'              => 'Positron',
	'cartodb-positron-no-labels'    => 'Positron (No Labels)',
	'cartodb-dark-matter'           => 'Dark Matter',
	'cartodb-dark-matter-no-labels' => 'Dark Matter (No Labels)',
	'cartodb-voyager'               => 'Voyager',
	'cartodb-voyager-grey-labels'   => 'Voyager (Grey Labels)',
	'cartodb-voyager-no-labels'     => 'Voyager (No Labels)',
	'esri-delorme'                  => 'Esri DeLorme',
	'esri-world-street-map'         => 'Esri World Street Map',
	'esri-world-topo-map'           => 'Esri World Topo Map',
	'esri-world-imagery'            => 'Esri World Imagery',
	'esri-world-gray-canvas'        => 'Esri World Gray Canvas',
) );

function mpfy_mtype_filter_map_type( $map_id ) {
	$value           = strtolower( get_post_meta( $map_id, '_map_google_mode', true ) );
	$supported_types = array_keys( MAPIFY_MAP_STYLES );
	
	return in_array( $value, $supported_types ) ? $value : 'osm';
}
add_filter( 'mpfy_map_type', 'mpfy_mtype_filter_map_type', 10 );