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

function get_makerfaire_status_counts( $form_id ) {
	global $wpdb;
	$lead_details_table_name = RGFormsModel::get_lead_details_table_name();
	$sql             = $wpdb->prepare(
			"SELECT count(0) as entries,value as label FROM $lead_details_table_name
			      join wp_rg_lead lead 
                                    on  lead.id = $lead_details_table_name.lead_id and 
                                        lead.status = 'active'
                        where field_number='303'
			and $lead_details_table_name.form_id=%d
			group by value",
			$form_id
	);

	$results = $wpdb->get_results( $sql, ARRAY_A );
	return $results;

}
