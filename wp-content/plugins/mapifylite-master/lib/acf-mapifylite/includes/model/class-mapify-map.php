<?php

/**
 * The model class that responsible for mapify-map data
 * Displayed as "Map Settings" on mapifyFree
 * 
 * @since    1.0.0
 */

namespace Acf_Mapifylite\Model;

/**
 * Class Mapify_Map
 * 
 * @since    1.0.0
 */
class Mapify_Map {

	/**
	 * Post ID of the current map
	 * 
	 * @since    1.0.0
	 * @var      int
	 */
	public $post_id;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * Get all map settings
	 * 
	 * @since    1.0.0
	 * @return   array    map settings
	 */
	public function get_all_map_settings() {
		return array(
			'map_mode'                   => $this->get_map_mode(),
			'pin_image_url'              => $this->get_pin_image_url(),
			'mouse_zoom_setting'         => $this->get_mouse_zoom_setting(),
			'manual_zoom_setting'        => $this->get_manual_zoom_setting(),
			'animated_tooltips_setting'  => $this->get_animated_tooltips_setting(),
			'animated_pinpoints_setting' => $this->get_animated_pinpoints_setting(),
			'map_image_url'              => $this->get_map_image_url(),
			'map_location_id'            => $this->get_map_location_id(),
			'map_location_details'       => $this->get_map_location_details(),
		);
	}

	/**
	 * Get map mode
	 * 
	 * @since    1.0.0
	 * @return   string    map|image
	 */
	public function get_map_mode() {
		$value = get_post_meta( $this->post_id, '_map_mode', true );
		return ! empty( $value ) ? $value : 'map';
	}

	/**
	 * Get pin image url
	 * 
	 * @since    1.0.0
	 * @return   string    image url
	 */
	public function get_pin_image_url() {
		return get_post_meta( $this->post_id, '_map_pin', true );
	}

	/**
	 * Get mouse zoom setting
	 * 
	 * @since    1.0.0
	 * @return   string    yes|no
	 */
	public function get_mouse_zoom_setting() {
		$value = get_post_meta( $this->post_id, '_map_enable_zoom', true );
		return ! empty( $value ) ? $value : 'yes';
	}

	/**
	 * Get manual zoom setting
	 * 
	 * @since    1.0.0
	 * @return   string    yes|no
	 */
	public function get_manual_zoom_setting() {
		$value = get_post_meta( $this->post_id, '_map_enable_zoom_manual', true );
		return ! empty( $value ) ? $value : 'yes';
	}

	/**
	 * Get animated tooltips setting
	 * 
	 * @since    1.0.0
	 * @return   string    yes|no
	 */
	public function get_animated_tooltips_setting() {
		$value = get_post_meta( $this->post_id, '_map_animate_tooltips', true );
		return ! empty( $value ) ? $value : 'yes';
	}

	/**
	 * Get animated pinpoints setting
	 * 
	 * @since    1.0.0
	 * @return   string    yes|no
	 */
	public function get_animated_pinpoints_setting() {
		$value = get_post_meta( $this->post_id, '_map_animate_pinpoints', true );
		return ! empty( $value ) ? $value : 'yes';
	}

	/**
	 * Get map image url
	 * 
	 * @since    1.0.0
	 * @return   string    image url
	 */
	public function get_map_image_url() {
		return get_post_meta( $this->post_id, '_map_image_big', true );
	}

	/**
	 * Get map location id
	 * Will be post_id of the map location
	 * Can also a zero (0) value for a manual location
	 * 
	 * @since    1.0.0
	 * @return   int    post_id
	 */
	public function get_map_location_id() {
		$value = get_post_meta( $this->post_id, '_map_main_location', true );
		return ! empty( $value ) ? intval( $value ) : 0;
	}

	/**
	 * Get map location details
	 * Will be consist an array of:
	 * - centered_location    array    [lat,lng]
	 * - selected_location    array    [lat,lng]
	 * - zoom_level           integer
	 * 
	 * @since    1.0.0
	 * @return   array    map data
	 */
	public function get_map_location_details() {
		// centered lat
		$lat = get_post_meta( $this->post_id, '_map_google_center-lat', true );
		$lat = ! empty( $lat ) ? $lat : '-1.634541720929286';

		// centered lng
		$lng = get_post_meta( $this->post_id, '_map_google_center-lng', true );
		$lng = ! empty( $lng ) ? $lng : '119.9030508161835';

		// zoom level
		$zoom = get_post_meta( $this->post_id, '_map_google_center-zoom', true );
		$zoom = ! empty( $zoom ) ? $zoom : 4;

		// return map data
		return array(
			'centered_location' => array( $lat, $lng ),
			'selected_location' => array( $lat, $lng ),
			'zoom_level'        => $zoom,
		);
	}

	/**
	 * Get map locations data of this map settings
	 * 
	 * @since    1.0.0
	 * @return   array    map locations @ [ $post_id => $post_title ]
	 */
	public function get_map_locations() {
		$map_locations = array();
		
		// WP_Query args
		$args = array(
			'post_type'      => 'map-location',
			'posts_per_page' => -1, // -1 mean show all data
			'post_status'    => 'publish',
			'meta_key'       => '_map_location_map',
			'meta_value'     => $this->post_id,
		);
		
		$the_query = new \WP_Query( $args );
		
		// iterate the found data
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$post_id = get_the_ID();

			// set the data
			$map_locations[ $post_id ] = array(
				'title' => get_the_title(),
				'lat'   => get_post_meta( $post_id, '_map_location_google_location-lat', true ),
				'lng'   => get_post_meta( $post_id, '_map_location_google_location-lng', true ),
			);
		}
		
		wp_reset_postdata();
		return $map_locations;
	}

	/**
	 * Set map mode
	 * 
	 * @since    1.0.0
	 * @param    string    $raw_mode    map|image
	 */
	public function set_map_mode( $raw_mode ) {
		$raw_mode = sanitize_text_field( $raw_mode );
		$map_mode = 'image' === $raw_mode ? $raw_mode : 'map'; // must be either `map` or `image`

		update_post_meta( $this->post_id, '_map_mode', $map_mode );
	}

	/**
	 * Set pin image url
	 * 
	 * @since    1.0.0
	 * @param    string    image url
	 */
	public function set_pin_image_url( $image_url ) {
		update_post_meta( $this->post_id, '_map_pin', esc_url_raw( $image_url ) );
	}

	/**
	 * Set map image url
	 * 
	 * @since    1.0.0
	 * @param    string    image url
	 */
	public function set_map_image_url( $image_url ) {
		update_post_meta( $this->post_id, '_map_image_big', esc_url_raw( $image_url ) );
	}

	/**
	 * Set map location id
	 * Will be post_id of the map location
	 * Can also a zero (0) value for a manual location
	 * 
	 * @since    1.0.0
	 * @param    int    post_id
	 */
	public function set_map_location_id( $post_id ) {
		update_post_meta( $this->post_id, '_map_main_location', intval( $post_id ) );
	}

	/**
	 * Set map location details
	 * 
	 * @since    1.0.0
	 * @param    string    $lat    map lattitude
	 * @param    string    $lng    map longitude
	 * @param    int       $zoom   map zoom level
	 */
	public function set_map_location_details( $lat, $lng, $zoom ) {
		// centered lat
		$lat = ! empty( $lat ) ? sanitize_text_field( $lat ) : '-1.634541720929286';
		update_post_meta( $this->post_id, '_map_google_center-lat', $lat );

		// centered lng
		$lng = ! empty( $lng ) ? sanitize_text_field( $lng ) : '119.9030508161835';
		update_post_meta( $this->post_id, '_map_google_center-lng', $lng );

		// zoom level
		$zoom = ! empty( $zoom ) ? intval( $zoom ) : 4;
		update_post_meta( $this->post_id, '_map_google_center-zoom', $zoom );
	}

}