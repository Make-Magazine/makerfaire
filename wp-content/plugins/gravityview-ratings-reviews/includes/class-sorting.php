<?php
/**
 * Handle sorting by Ratings Reviews fields
 *
 * @package   GravityView_Ratings_Reviews
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

class GravityView_Ratings_Reviews_Sorting extends GravityView_Ratings_Reviews_Component {

	public function load() {

		add_filter( 'gravityview/common/sortable_fields', array( $this, 'add_sort_field' ), 10, 2 );

		add_filter( 'gravityview_get_entries', array( $this, 'update_sorting' ), 50, 3 );

	}

	/**
	 * Add the possibility to configure the View to sort by the average rating
	 * This will add the reviews_link field to the Filter & Sort metabox sort field dropdown.
	 *
	 * Requires GravityView 1.11.3+
	 *
	 * @param array $fields Sub-set of GF form sortable fields
	 * @param int $form_id  GF Form ID
	 *
	 * @return array
	 */
	function add_sort_field( $fields, $form_id ) {

		$fields['reviews_link'] = array(
			'type' => 'reviews_link',
			'label' => __( 'Reviews Link', 'gravityview-ratings-reviews' ),
		);

		return $fields;

	}

	/**
	 * Sort entries based on ratings value
	 * 
	 * @param array $parameters Array with `search_criteria`, `sorting` and `paging` keys.
	 * @param array $args View configuration args. {
	 *      @type int $id View id
	 *      @type int $page_size Number of entries to show per page
	 *      @type string $sort_field Form field id to sort
	 *      @type string $sort_direction Sorting direction ('ASC', 'DESC', or 'RAND')
	 *      @type string $start_date - Ymd
	 *      @type string $end_date - Ymd
	 *      @type string $class - assign a html class to the view
	 *      @type string $offset (optional) - This is the start point in the current data set (0 index based).
	 * }
	 * @param int $form_id ID of Gravity Forms form
	 *
	 * @return mixed
	 */
	function update_sorting( $original_parameters = array(), $args = array(), $form_id = 0 ) {

		$parameters = $original_parameters;

		$view = \GV\View::by_id( \GV\Utils::get( $args, 'id' ) );

		if ( ! $view ) {
			gravityview()->log->error( 'View not found with $context_view_id of #{view_id}', array( 'view_id' => \GV\Utils::get( $args, 'id' ) ) );
			return $original_parameters;
		}

		$sort_field = rgars( $parameters, 'sorting/key', rgar( $args, 'sort_field' ) );
		$sort_direction = rgars( $parameters, 'sorting/direction', rgar( $args, 'sort_direction' ) );

		if( empty( $sort_field ) || ! in_array( $sort_field, array( 'stars', 'votes', 'reviews_link' ) ) ) {
			return $original_parameters;
		}

		if ( 'reviews_link' === $sort_field ) {

			$ratings_type = $view->settings->get( 'entry_review_type' );

			if( empty( $ratings_type ) )  {
				gravityview()->log->error('{method} Empty ratings type view setting.', array( 'method' => __METHOD__ ) );
				return $original_parameters;
			}

			$sort_field = $ratings_type;
		}

		$parameters['sorting']['key'] = ( 'stars' === $sort_field ) ? 'gravityview_ratings_stars' : 'gravityview_ratings_votes';
		$parameters['sorting']['is_numeric'] = true;
		$parameters['sorting']['direction'] = $sort_direction;

		return $parameters;
	}
}