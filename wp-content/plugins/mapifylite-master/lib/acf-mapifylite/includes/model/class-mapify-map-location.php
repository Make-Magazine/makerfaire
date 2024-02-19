<?php

/**
 * The model class that responsible for mapify-map-location data
 * Displayed as "Map Locations" on mapifyFree
 * 
 * @since    1.0.0
 */

namespace Acf_Mapifylite\Model;

/**
 * Class Mapify_Map_Location
 * 
 * @since    1.0.0
 */
class Mapify_Map_Location {

	/**
	 * Post ID of the current map location
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
	 * Get maps (settings) data
	 * 
	 * @since    1.0.0
	 */
	public function get_maps() {
		$map_locations = array();
		
		// WP_Query args
		$args = array(
			'post_type'      => 'map',
			'posts_per_page' => -1, // -1 mean show all data
			'post_status'    => 'publish',
		);
		
		$the_query = new \WP_Query( $args );
		
		// iterate the found data
		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			$post_id     = get_the_ID();		
			$mapify_map  = new Mapify_Map( $post_id );
			$map_details = $mapify_map->get_map_location_details();

			// get uploader status
			$maptiles_uploader = new Maptiles_Uploader( $post_id );
			$has_image_mode    = 'tiles_download_completed' === $maptiles_uploader->status ? true : false;

			// set the data
			$map_locations[ $post_id ] = array(
				'title'          => get_the_title(),
				'mode'           => $mapify_map->get_map_mode(),
				'has_image_mode' => $has_image_mode,
			);
		}
		
		wp_reset_postdata();
		return $map_locations;
	}

	/**
	 * Get map id
	 * 
	 * @since    1.0.0
	 * @return   int
	 */
	public function get_map_id() {
		$value = get_post_meta( $this->post_id, '_map_location_map', true );
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
		$lat = get_post_meta( $this->post_id, '_map_location_google_location-lat', true );

		$lat = ! empty( $lat ) ? $lat : '-1.634541720929286';

		// centered lng
		$lng = get_post_meta( $this->post_id, '_map_location_google_location-lng', true );
		$lng = ! empty( $lng ) ? $lng : '119.9030508161835';

		// zoom level
		$zoom = get_post_meta( $this->post_id, '_map_location_google_location-zoom', true );
		$zoom = ! empty( $zoom ) ? $zoom : 10;

		// return map data
		return array(
			'centered_location' => array( $lat, $lng ),
			'selected_location' => array( $lat, $lng ),
			'zoom_level'        => $zoom,
		);
	}

	/**
	 * Get gallery images
	 * 
	 * @since    1.0.0
	 * @return   array    image urls
	 */
	public function get_gallery_images() {
		global $wpdb;

		$image_urls = $wpdb->get_col(
			$wpdb->prepare( "
					SELECT meta_value
					FROM $wpdb->postmeta
					WHERE post_id = %d 
					AND meta_key LIKE %s
					ORDER BY meta_key ASC
				",
				$this->post_id,
				"_map_location_gallery_images_-_image_%"
			)
		);

		// array of image url
		return $image_urls ? $image_urls : array();
	}

	/**
	 * Set map id
	 * 
	 * @since    1.0.0
	 * @param    int
	 */
	public function set_map_id( $map_id ) {
		update_post_meta( $this->post_id, '_map_location_map', intval( $map_id ) );
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
		update_post_meta( $this->post_id, '_map_location_google_location-lat', $lat );

		// centered lng
		$lng = ! empty( $lng ) ? sanitize_text_field( $lng ) : '119.9030508161835';
		update_post_meta( $this->post_id, '_map_location_google_location-lng', $lng );

		// zoom level
		$zoom = ! empty( $zoom ) ? intval( $zoom ) : 10;
		update_post_meta( $this->post_id, '_map_location_google_location-zoom', $zoom );

		// lat & lng
		update_post_meta( $this->post_id, '_map_location_google_location', $lat . ',' . $lng );
	}

	/**
	 * Set gallery images
	 * 
	 * @since    1.0.0
	 * @param    array    image urls
	 */
	public function set_gallery_images( $image_urls ) {
		if ( ! is_array( $image_urls ) ) return false;

		$gallery_images = $this->get_gallery_images();
		
		// first, delete any unused images from database
		foreach ( $gallery_images as $key => $value ) {
			if ( isset( $image_urls[ $key ] ) ) continue;
			delete_post_meta( $this->post_id, '_map_location_gallery_images_-_image_' . $key );
		}

		// then update the remain images
		foreach ( $image_urls as $key => $value ) {
			update_post_meta( $this->post_id, '_map_location_gallery_images_-_image_' . $key, $value );
		}
	}
}