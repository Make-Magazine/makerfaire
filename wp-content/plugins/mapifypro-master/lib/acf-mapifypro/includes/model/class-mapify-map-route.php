<?php

/**
 * The model class that responsible for map route data
 *  
 * @since    1.1.7
 */

namespace Acf_Mapifypro\Model;

/**
 * Class Mapify_Map_Route
 * 
 * @since    1.1.7
 */
class Mapify_Map_Route {

	/**
	 * Post ID of the current map route
	 * 
	 * @since    1.1.7
	 * @var      int
	 */
	public $post_id;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.7
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * Get map ids
	 * 
	 * @since    1.1.7
	 * @return   array    Map ids (int)
	 */
	public function get_map_ids() {
		$value = get_post_meta( $this->post_id, '_route_map', true );
		$ids   = \explode( ',', \rtrim( $value, ',' ) );
		return $ids;
	}

	/**
	 * Set map ids
	 * 
	 * @since    1.1.7
	 * @param    array    $map_ids    Map ids.
	 */
	public function set_map_ids( $map_ids ) {
		if ( \is_array( $map_ids ) ) {
			$ids = \implode( ',', $map_ids );
			update_post_meta( $this->post_id, '_route_map', \ltrim( $ids, ',' ) );
		}
	}

	/**
	 * Add map id.
	 * 
	 * @since    1.1.7
	 * @param    int    Map ID.
	 */
	public function add_map_id( $map_id ) {
		$map_ids   = $this->get_map_ids();
		$map_ids   = \is_array( $map_ids ) ? $map_ids : array();
		$map_ids[] = $map_id;

		$this->set_map_ids( $map_ids );
	}

	/**
	 * Remove map id.
	 * 
	 * @since    1.1.7
	 * @param    int    Map ID.
	 */
	public function remove_map_id( $map_id ) {
		$map_ids = $this->get_map_ids();
		
		if( \is_array( $map_ids ) && in_array( $map_id, $map_ids ) ) {
			$map_ids = array_diff( $map_ids, array( $map_id ) );
			$this->set_map_ids( $map_ids );
		}
	}

}