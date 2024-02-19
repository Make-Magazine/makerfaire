<?php

/**
 * The model class that responsible for mapify-map-drawer data
 * Displayed as "Map Areas" on admin sidebar
 * 
 * @since    1.0.0
 */

namespace Acf_Mapifypro\Model;

/**
 * Class Mapify_Map_Drawer
 * 
 * @since    1.0.0
 */
class Mapify_Map_Drawer {

	/**
	 * Post ID of the current map drawer
	 * 
	 * @since    1.0.0
	 * @var      int
	 */
	public $post_id;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Post ID.
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * Get map area coordinates.
	 * 
	 * @since    1.1.0
	 */
	public function get_area_coordinates() {
		// area coordinates
		$area_coordinates = get_post_meta( $this->post_id, 'mpfy_area_coordinates', true );
		return ! empty( $area_coordinates ) ? $area_coordinates : '';
	}

	/**
	 * Get map area's image.
	 * 
	 * @since    1.1.0
	 * @param    string          $image_size    The size of image to return.
	 * @return   string|false    Image url of the selected image size. Return false if no image.
	 */
	public function get_area_image( $image_size = 'medium' ) {
		$images = get_field( 'map_drawer_image', $this->post_id );
		
		// must a valid image
		if ( ! isset( $images['url'] ) ) return false;

		// return the selected image size, otherwise the original size url
		if ( isset( $images['sizes'][ $image_size ] ) ) {
			return $images['sizes'][ $image_size ];
		} else {
			return $images['url'];
		}
	}

	/**
	 * Get map area's border color.
	 * 
	 * @since    1.1.0
	 */
	public function get_area_border_color() {
		$border_color = get_field( 'map_drawer_border_color', $this->post_id );
		return $border_color ? $border_color : '#1e73be';
	}
	
	/**
	 * Get map area's fill color.
	 * 
	 * @since    1.1.0
	 */
	public function get_area_fill_color() {
		$fill_color = get_field( 'map_drawer_fill_color', $this->post_id );
		return $fill_color ? $fill_color : '#1e73be';
	}

	/**
	 * Get map area's fill color.
	 * 
	 * @since    1.1.0
	 */
	public function get_area_fill_opacity() {
		$fill_opacity_percentage = get_field( 'map_drawer_fill_opacity_percentage', $this->post_id );
		$fill_opacity_percentage = $fill_opacity_percentage ? $fill_opacity_percentage : 40;
		$fill_opacity            = $fill_opacity_percentage / 100;
		
		return $fill_opacity;
	}

	/**
	 * Get map area's description.
	 * 
	 * @since    1.1.0
	 */
	public function get_area_description() {
		$description = get_field( 'map_drawer_description', $this->post_id );
		return $description ? $description : '';
	}

	/**
	 * Set map area coordinates.
	 * 
	 * @since    1.1.0
	 * @param    string    $area_coordinates    JSON encoded map coordinates.
	 */
	public function set_area_coordinates( $area_coordinates ) {
		$area_coordinates = ! empty( $area_coordinates ) ? sanitize_text_field( $area_coordinates ) : '';
		update_post_meta( $this->post_id, 'mpfy_area_coordinates', $area_coordinates );
	}

}