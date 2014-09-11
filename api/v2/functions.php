<?php
/**
 * v2 of the Maker Faire API - Functions
 *
 * @version 2.0
 */

// Stop any direct calls to this file
defined( 'ABSPATH' ) or die( 'This file cannot be called directly!' );

function get_makers_from_app($app_id = 0) {

  if(!$app_id) return null;
		// Application Makers
		$maker_args = array(
			'post_type' 		=> 'maker',
			'posts_per_page' 	=> 20,
			'meta_query' => array(
				array('key' => 'mfei_record',
					'compare' => '=',
					'value' => absint( $app_id)
          )
			)
		);


		$makers = new WP_Query( $maker_args );

		if($makers->post_count == 0) { return null; }
		foreach ( $makers->posts as $maker ) {
			$maker_ids[] = absint( $maker->ID );
		}

  return $maker_ids;
}
