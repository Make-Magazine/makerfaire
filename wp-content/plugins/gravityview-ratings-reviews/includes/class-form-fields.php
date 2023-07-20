<?php
/**
 * Component that injects rating fields to the GravityForm
 *
 * @package   GravityView_Ratings_Reviews
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 0.1.1
 */

defined( 'ABSPATH' ) || exit;

class GravityView_Ratings_Reviews_Form_Fields extends GravityView_Ratings_Reviews_Component {

	/**
	 * Callback when this component is loaded by the loader.
	 *
	 * @since 0.1.1
	 *
	 * @return void
	 */
	public function load() {
		// Treat the Rating Fields on Export
		add_filter( 'gform_export_field_value', array( $this, 'export' ), 10, 4 );

		// Add the Ghost Fields when required
		add_filter( 'gform_form_post_get_meta', array( $this, 'add_fields' ) );

		// Allow the ordering for Rating Fields
		add_filter( 'gform_entry_meta', array( $this, 'entry_meta' ), 15 );

		// Add the required Rating Metas when new entry is added
		add_filter( 'gform_post_update_entry', array( $this, 'new_entry' ), 15, 2 );
		add_filter( 'gform_post_submission', array( $this, 'new_entry' ), 15, 2 );

	}

	/**
	 * Add the required Rating Metas when new entry is added
	 *
	 * @since  0.1.1
	 * @param  array $entry Entry Added
	 *
	 * @return void
	 */
	public function new_entry( $form_id = 0, $entry = array() ) {
		\GravityView_Ratings_Reviews_Helper::refresh_ratings( null, $entry['id'] );
	}

	/**
	 * Allow the ordering for Rating Fields by adding the Rating fields on the 'gform_entry_meta'
	 *
	 * @since  0.1.1
	 * @param  array  $fields Array of fields
	 *
	 * @return array         Fields after adding the rating ones
	 */
	public function entry_meta( $fields = array() ){

		$fields = array_merge( $fields, self::get_fields( null, false, true ) );

		return $fields;
	}

	/**
	 * Treat the Rating Fields on Export
	 *
	 * @since 0.1.1
	 * @param  mixed $value  [description]
	 * @param  int $form_id  [description]
	 * @param  string|int $field_id [description]
	 * @param  array $lead   [description]
	 *
	 * @return int|float Value from the Meta
	 */
	public function export( $value, $form_id, $field_id, $lead ){
		$fields = self::get_fields( $form_id );
		$fields_ids = wp_list_pluck( $fields, 'id' );
		if ( ! in_array( $field_id, $fields_ids ) ){
			return $value;
		}

		$field_id = str_replace( 'gravityview_ratings_', '', $field_id );

		$bridge_post_id = GravityView_Ratings_Reviews_Helper::get_post_bridge_id( $lead['id'] );
		$metas = GravityView_Ratings_Reviews_Helper::get_ratings_detailed( $bridge_post_id, $lead['id'] );

		if ( 'reviews_dump' === $field_id ){

			// Get the reviews data with metadata
			$data = GravityView_Ratings_Reviews_Helper::get_reviews_data( $bridge_post_id, 'any', true );

			$metas[ $field_id ] = self::escape_csv( $data );
		}

		return $metas[ $field_id ];
	}

	/**
	 * Gets a Array/Object and Turns into a JSON/PHP Serialized encoded for CSV string
	 * @param  array|object $data The Object that should be translated.
	 * @param  string $format Format used json|serialize
	 * @return string       An Empty array if data is empty, a Serialized string or JSON encoded
	 */
	public static function escape_csv( $data, $format = 'json' ){
		if ( empty( $data ) ) {
			return '';
		}
		if ( 'json' === $format ){
			$escaped = json_encode( $data );
		} else {
			$escaped = maybe_serialize( $data );
		}

		// GF Already does this trick!
		// $escaped = str_replace( '"', '""', $string ); // First off escape all " and make them ""

		if ( preg_match( '/,/', $escaped ) || preg_match( "/\n/", $escaped ) || preg_match( '/"/', $escaped ) ) { // Check if I have any commas or new lines
			return '"' . $escaped . '"'; // If I have new lines or commas escape them
		} else {
			return $escaped; // If no new lines or commas just return the value
		}
	}

	/**
	 * Check whether we're currently on the export screen
	 * @since 0.1.1
	 * @return boolean True: Yep; False: nope
	 */
	private static function is_export_screen() {
		return ( ! empty( $_GET['page'] ) && 'gf_export' === $_GET['page'] ) || ( ! empty( $_REQUEST['action'] ) && 'rg_select_export_form' === $_REQUEST['action'] );
	}

	/**
	 * Check whether we're currently on the gravity view screen
	 * @since 0.1.1
	 * @return boolean True: Yep; False: nope
	 */
	private static function is_gravityview_screen() {

		$is_gravityview = ( function_exists('gravityview') && gravityview()->request->is_admin( '', null ) ) && ! self::is_export_screen();

		return $is_gravityview;
	}

	/**
	 * Adding the required fields to the pages where it matters
	 *
	 * @since 0.1.1
	 * @param array $form Form Instance
	 *
	 * @return array Form Instance
	 */
	public function add_fields( $form ){

		if ( self::is_export_screen() ){

			$fields = self::get_fields( $form['id'] );

			/**
			 * @filter `gv_ratings_reviews_use_advanced_sorting` Allow users to include the Advanced Meta Sorting
			 * @param boolean $use_advanced_sorting Whether to add advanced sorting options (sort by 1 star ratings, 2 stars, etc) Default: false
			 * @param array $form Gravity Forms form array
			 */
			$has_advanced_fields = apply_filters( 'gv_ratings_reviews_use_advanced_sorting', false, $form );

			if ( self::is_gravityview_screen() && ! $has_advanced_fields ){
				foreach ( $fields as $key => $field ) {
					if ( isset( $field['gv_is_advanced'] ) && true !== $field['gv_is_advanced'] ){
						continue;
					}
					unset( $fields[ $key ] );
				}
			}

			$form['fields'] = array_merge( $form['fields'], $fields );

		}

		return $form;
	}

	/**
	 * Get the Fields related to
	 *
	 * @since  0.1.1
	 * @param (optional) int  $form_id        The Form id
	 * @param  boolean $instance_field Wether or not is to instanciate the Field
	 * @param  boolean $indexed        Return as a Indexed array
	 *
	 * @return array                  Fields
	 */
	public static function get_fields( $form_id = null, $instance_field = true, $indexed = false ){
		$fields = array(
			array(
				'id' => 'star_1',
				'label' => __( 'Total: 1 Star', 'gravityview-ratings-reviews' ),
				'gv_is_advanced' => true,
			),
			array(
				'id' => 'star_2',
				'label' => __( 'Total: 2 Stars', 'gravityview-ratings-reviews' ),
				'gv_is_advanced' => true,
			),
			array(
				'id' => 'star_3',
				'label' => __( 'Total: 3 Stars', 'gravityview-ratings-reviews' ),
				'gv_is_advanced' => true,
			),
			array(
				'id' => 'star_4',
				'label' => __( 'Total: 4 Stars', 'gravityview-ratings-reviews' ),
				'gv_is_advanced' => true,
			),
			array(
				'id' => 'star_5',
				'label' => __( 'Total: 5 Stars', 'gravityview-ratings-reviews' ),
				'gv_is_advanced' => true,
			),
			array(
				'id' => 'stars',
				'label' => __( 'Stars Rating', 'gravityview-ratings-reviews' ),
				'gv_is_advanced' => false,
			),
			array(
				'id' => 'vote_down',
				'label' => __( 'Total: Down Votes', 'gravityview-ratings-reviews' ),
				'gv_is_advanced' => true,
			),
			array(
				'id' => 'vote_neutral',
				'label' => __( 'Total: Neutral Votes', 'gravityview-ratings-reviews' ),
				'gv_is_advanced' => true,
			),
			array(
				'id' => 'vote_up',
				'label' => __( 'Total: Up Votes', 'gravityview-ratings-reviews' ),
				'gv_is_advanced' => true,
			),
			array(
				'id' => 'votes',
				'label' => __( 'Votes Rating', 'gravityview-ratings-reviews' ),
				'gv_is_advanced' => false,
			),

			array(
				'id' => 'total',
				'label' => __( 'Total: Ratings', 'gravityview-ratings-reviews' ),
				'gv_is_advanced' => true,
			),
		);

		if ( self::is_export_screen() ){
			$fields[] = array(
				'id' => 'reviews_dump',
				'label' => __( 'Review Comments & Data', 'gravityview-ratings-reviews' ),
			);
		}

		$default = array(
			'type' => 'hidden',
			'pageNumber' => 1,
			'conditionalLogic' => '',
			'formId' => $form_id,
			'displayOnly' => true,
			'isRequired' => false,
			'adminOnly' => true,
			'is_numeric' => true,
		);

		foreach ( $fields as $i => $field ) {
			$field = wp_parse_args( $field, $default );
			$field['id'] = 'gravityview_ratings_' . $field['id'];
			if ( true === $instance_field ){
				$fields[ $i ] = new GF_Field_Hidden( $field );
			} else {
				$fields[ $i ] = $field;
			}
		}

		if ( true === $indexed ){
			$_fields = array();
			foreach ( $fields as $key => $field ) {
				$_fields[ $field['id'] ] = $field;
			}
			return $_fields;
		}
		return $fields;
	}
}
