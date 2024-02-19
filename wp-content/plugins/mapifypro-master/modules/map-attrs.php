<?php

if ( ! function_exists( 'mpfy_map_attrs_map_labels' ) ) {
	
	/**
	 * Set some Javascript's labels from map settings.
	 * This is a function for the filter hook `mpfy_map_attrs_data` located at map.html.php file
	 * 
	 * @param array $data Map attributes.
	 * @param int $map_id Map ID.
	 * 
	 * @return array Map attributes.
	 */
	function mpfy_map_attrs_map_labels( $data, $map_id ) {
		$NSR     = get_field( '_map_label_no_search_results', $map_id );
		$NSRWC   = get_field( '_map_label_no_search_results_with_closest', $map_id );
		$SGF     = get_field( '_map_label_search_geolocation_failure', $map_id );		
		$NSR_1   = ( $NSR && isset( $NSR['line_1'] ) ) ? $NSR['line_1'] : 'No locations were found.';
		$NSR_2   = ( $NSR && isset( $NSR['line_2'] ) ) ? $NSR['line_2'] : 'Please search again.';
		$NSRWC_1 = ( $NSRWC && isset( $NSRWC['line_1'] ) ) ? $NSRWC['line_1'] : 'No locations were found within your search criteria. Please search again.';
		$NSRWC_2 = ( $NSRWC && isset( $NSRWC['line_2'] ) ) ? $NSRWC['line_2'] : 'Or ...';
		$NSRWC_3 = ( $NSRWC && isset( $NSRWC['line_3'] ) ) ? $NSRWC['line_3'] : 'See the Closest Location';
		$SGF_1   = ( $SGF && isset( $SGF['line_1'] ) ) ? $SGF['line_1'] : 'Could not find the entered address.';
		$SGF_2   = ( $SGF && isset( $SGF['line_2'] ) ) ? $SGF['line_2'] : 'Please check your spelling and try again.';

		return array_merge( $data, array(
			'no_search_results'              => sprintf( '<p>%s<br />%s</p>', $NSR_1, $NSR_2 ),
			'no_search_results_with_closest' => sprintf( '<p>%s</p><p class="mpfy-or-text">%s <a href="#" class="mpfy-closest-pin">%s</a></p>', $NSRWC_1, $NSRWC_2, $NSRWC_3 ),
			'search_geolocation_failure'     => sprintf( '<p>%s<br />%s</p>', $SGF_1, $SGF_2 ),
		) );
	}

}

add_filter( 'mpfy_map_attrs_data', 'mpfy_map_attrs_map_labels', 10, 2 );