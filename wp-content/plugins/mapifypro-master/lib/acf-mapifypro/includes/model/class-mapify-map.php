<?php

/**
 * The model class that responsible for mapify-map data
 * Displayed as "Map Settings" on mapifyFree
 * 
 * @since    1.0.0
 */

namespace Acf_Mapifypro\Model;

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
	 * MapifyPro supported post types for map location
	 * 
	 * @since    1.1.0
	 * @var      array
	 */
	public $supported_post_types;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $post_id ) {
		$this->post_id              = $post_id;
		$this->supported_post_types = mpfy_get_supported_post_types();
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
	 * Get map layer
	 * 
	 * @since    1.0.0
	 * @return   string    Map layer ID
	 */
	public function get_map_layer() {
		return apply_filters( 'mpfy_map_type', $this->post_id );
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
		$lat = ! empty( $lat ) ? $lat : ACF_MAPIFYPRO_DEFAULT_LAT;

		// centered lng
		$lng = get_post_meta( $this->post_id, '_map_google_center-lng', true );
		$lng = ! empty( $lng ) ? $lng : ACF_MAPIFYPRO_DEFAULT_LNG;

		// zoom level
		$zoom = get_post_meta( $this->post_id, '_map_google_center-zoom', true );
		$zoom = ! empty( $zoom ) ? $zoom : ACF_MAPIFYPRO_DEFAULT_ZOOM;

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
	public function get_map_locations( $type = 'selected' ) {
		$map_locations = array();
		
		// WP_Query args
		$args = array(
			'post_type'      => $this->supported_post_types,
			'posts_per_page' => -1, // -1 mean show all data
			'post_status'    => 'publish',			
			'orderby'        => 'title',			
			'order'          => 'asc',			
		);

		// Query type
		if ( 'selected' === $type ) {
			$args['meta_query'] = array(
				array(
					'key'     => '_map_location_map',
					'value'   => '(^|,)' . $this->post_id . '(,|$)', // select between comma(s)
					'compare' => 'REGEXP',
				),
			);
		}
		
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
		$lat = ! empty( $lat ) ? sanitize_text_field( $lat ) : ACF_MAPIFYPRO_DEFAULT_LAT;
		update_post_meta( $this->post_id, '_map_google_center-lat', $lat );

		// centered lng
		$lng = ! empty( $lng ) ? sanitize_text_field( $lng ) : ACF_MAPIFYPRO_DEFAULT_LNG;
		update_post_meta( $this->post_id, '_map_google_center-lng', $lng );

		// zoom level
		$zoom = ! empty( $zoom ) ? intval( $zoom ) : ACF_MAPIFYPRO_DEFAULT_ZOOM;
		update_post_meta( $this->post_id, '_map_google_center-zoom', $zoom );
	}

	/**
	 * Get location ids on this map.
	 * 
	 * @since    1.1.0
	 * @return   array    Map location IDs.
	 */
	public function get_location_ids() {
		// WP_Query args
		$args = array(
			'post_type'      => $this->supported_post_types,
			'posts_per_page' => -1, // -1 mean show all data
			'post_status'    => 'publish',
			'fields'         => 'ids',			
			'orderby'        => 'title',
			'order'          => 'asc',
			'meta_query'     => array(
				array(
					'key'     => '_map_location_map',
					'value'   => '(^|,)' . $this->post_id . '(,|$)', // select between comma(s)
					'compare' => 'REGEXP',
				),
			),
		);
		
		$the_query = new \WP_Query( $args );
		$ids       = array();

		if( $the_query->have_posts() ) {
			$ids = $the_query->posts;
		}

		wp_reset_postdata();

		return $ids;
	}

	/**
	 * Set location ids for this map.
	 * 
	 * @since    1.0.0
	 * @param    array    Map location IDs.
	 */
	public function set_location_ids( $location_ids ) {
		if ( ! \is_array( $location_ids ) || ! $location_ids ) return;

		$current_location_ids = $this->get_location_ids();
		$ids_to_add           = array_diff( $location_ids, $current_location_ids );
		$ids_to_remove        = array_diff( $current_location_ids, $location_ids );

		// ids_to_add
		foreach ( $ids_to_add as $id ) {
			$location = new Mapify_Map_Location( $id );
			$location->add_map_id( $this->post_id );
		}

		// ids_to_remove
		foreach ( $ids_to_remove as $id ) {
			$location = new Mapify_Map_Location( $id );
			$location->remove_map_id( $this->post_id );
		}
	}

	/**
	 * Get route ids on this map.
	 * 
	 * @since    1.1.7
	 * @return   array    Map route IDs.
	 */
	public function get_route_ids() {
		// WP_Query args
		$args = array(
			'post_type'      => 'route',
			'posts_per_page' => -1, // -1 mean show all data
			'post_status'    => 'publish',
			'fields'         => 'ids',			
			'orderby'        => 'title',
			'order'          => 'asc',
			'meta_query'     => array(
				array(
					'key'     => '_route_map',
					'value'   => '(^|,)' . $this->post_id . '(,|$)', // select between comma(s)
					'compare' => 'REGEXP',
				),
			),
		);
		
		$the_query = new \WP_Query( $args );
		$ids       = array();

		if( $the_query->have_posts() ) {
			$ids = $the_query->posts;
		}

		wp_reset_postdata();

		return $ids;
	}

	/**
	 * Set route ids for this map.
	 * 
	 * @since    1.1.7
	 * @param    array    Map route IDs.
	 */
	public function set_route_ids( $route_ids ) {
		if ( ! \is_array( $route_ids ) ) return;
		
		$current_route_ids = $this->get_route_ids();
		$ids_to_add        = array_diff( $route_ids, $current_route_ids );
		$ids_to_remove     = array_diff( $current_route_ids, $route_ids );

		// ids_to_add
		foreach ( $ids_to_add as $id ) {
			$route = new Mapify_Map_Route( $id );
			$route->add_map_id( $this->post_id );
		}

		// ids_to_remove
		foreach ( $ids_to_remove as $id ) {
			$route = new Mapify_Map_Route( $id );
			$route->remove_map_id( $this->post_id );
		}
	}

}