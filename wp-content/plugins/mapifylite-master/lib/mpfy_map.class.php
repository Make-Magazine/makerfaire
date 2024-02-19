<?php
class Mpfy_Map {
	private $post;

	static function get_map_modes() {
		$map_modes = apply_filters( 'mpfy_map_modes', array(
			'map'=>'Map',
		) );
		return $map_modes;
	}

	static function get_all_maps() {
		$map_limit = apply_filters('mpfy_map_limit', 1);
		$raw = new WP_Query('post_type=map&posts_per_page=' . $map_limit . '&orderby=title&order=asc&post_status=any');

		$maps = array();
		foreach ($raw->posts as $r) {
			$maps[$r->ID] = $r->post_title;
		}
		return $maps;
	}

	static function get_first_map_id() {
		$map = get_posts('post_type=map&posts_per_page=1&post_status=any');
		if (isset($map[0])) {
			return $map[0]->ID;
		}
		return 0;
	}

	function __construct($map_id) {
		$this->post = get_post($map_id);
		if (is_null($this->post)) {
			$this->post = (object) array(
				'ID'=>0,
				'post_title'=>'N/A',
				'post_content'=>'N/A',
			);
		}
	}

	function get_id($return_translated_id=false) {
		global $sitepress;
		$id = $this->post->ID;
		if ($return_translated_id && $sitepress) {
			$id = icl_object_id($id, 'map', true);
		}
		return $id;
	}

	function get_title() {
		return $this->post->post_title;
	}

	function get_mode() {
		$value = get_post_meta( $this->get_id(), '_map_mode', true );
		$value = ( $value ) ? $value : 'map';

		$supported_modes = self::get_map_modes();
		if ( ! isset( $supported_modes[ $value ] ) ) {
			$value = 'map';
		}

		return $value;
	}

	function get_center() {
		$center = get_post_meta($this->get_id(), '_map_main_location', true);

		if ( stristr( $center, ',' ) ) {
			$center = array_map( 'floatval', explode( ',', $center ) );
		} else {
			$main_location = get_post_meta( $this->get_id(), '_map_main_location', true );
			$center = array();
			if ( $main_location != 0 ) {
				$main_location = new Mpfy_Map_Location($main_location);
				$center = $main_location->get_coordinates();
			} else {
				$center = array(
					floatval( get_post_meta( $this->get_id(), '_map_google_center-lat', true ) ),
					floatval( get_post_meta( $this->get_id(), '_map_google_center-lng', true ) )
				);
			}
			if ( ! array_filter( $center ) ) {
				$center = array( -5, -5 );
			}
		}

		return array_slice( $center, 0, 2 );
	}

	function get_zoom_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_enable_zoom', true);
		return $value;
	}

	function get_manual_zoom_enabled() {
		$zoom_neabled = $this->get_zoom_enabled();
		$value = mpfy_meta_to_bool($this->get_id(), '_map_enable_zoom_manual', $zoom_neabled);
		return $value;
	}

	function get_zoom_level() {
		$value = get_post_meta($this->get_id(), '_map_google_center-zoom', true);
		$value = is_numeric( $value ) ? intval( $value ) : 3;
		return $value;
	}

	function get_use_my_location_enabled() {
		$value = apply_filters( 'mpfy_map_enable_use_my_location', false, $this->get_id() );
		return $value;
	}

	function get_default_pin_image() {
		$value = get_post_meta($this->get_id(), '_map_pin', true);
		return $value;
	}

	function get_animate_tooltips() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_animate_tooltips', true);
		return $value;
	}

	function get_tooltip_image_orientation() {
		$value = apply_filters( 'mpfy_map_tooltip_image_orientation', 'left', $this->get_id() );
		return $value;
	}

	function get_animate_pinpoints() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_animate_pinpoints', true);
		return $value;
	}

	function get_type() {
		$value = apply_filters( 'mpfy_map_type', 'road', $this->get_id() );
		return $value;
	}

	function get_tags() {
		$value = apply_filters( 'mpfy_map_tags', array(), $this->get_id( true ) );
		return $value;
	}

	function get_search_enabled() {
		$value = apply_filters( 'mpfy_map_search_enabled', false, $this->get_id() );
		return $value;
	}

	function get_search_radius_unit_name() {
		$unit = $this->get_search_radius_unit();
		$value = ($unit == 'km') ? 'Kilometers' : 'Miles';
		return $value;
	}

	function get_search_radius_unit() {
		$value = apply_filters( 'mpfy_map_search_radius_unit', 'mi', $this->get_id() );
		return $value;
	}

	function get_search_radius_options() {
		$value = array(5, 10, 25, 50, 100, 500);
		return $value;
	}

	function get_search_radius() {
		$value = apply_filters( 'mpfy_map_search_radius', 5, $this->get_id() );
		return $value;
	}

	function get_search_region_bias() {
		$value = apply_filters( 'mpfy_map_search_region_bias', '', $this->get_id() );
		return $value;
	}

	function get_search_center_behavior() {
		$value = apply_filters( 'mpfy_map_search_center', false, $this->get_id() );
		return $value;
	}

	function get_filters_center_behavior() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_filters_center', false);
		return $value;
	}

	function get_filters_enabled() {
		$value = apply_filters( 'mpfy_map_filters_enabled', false, $this->get_id() );
		return $value;
	}

	function get_filters_list_enabled() {
		$value = apply_filters( 'mpfy_map_filters_list_enabled', false, $this->get_id() );
		return $value;
	}

	// Module: map-clustering
	function get_clustering_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_enable_clustering', false);
		return $value;
	}

	// this filter removes the forced "%" prefix and suffix for meta_query LIKE and replaces @!mpfy!@ with an unescaped %
	function filter_meta_query_like($pieces) {
		if ( !empty( $pieces['where'] ) ) {
			$pieces['where'] = preg_replace( '/(.*?)LIKE \'\%(.*?)\%\'/', "\$1LIKE '\$2'", $pieces['where'] );
			$pieces['where'] = str_replace('@!mpfy!@', '%', $pieces['where'] );
		}
		// Usage
		// add_filter( 'get_meta_sql', array($this, 'filter_meta_query_like'));
		// [execute your WP_Query]
		// remove_filter( 'get_meta_sql', array($this, 'filter_meta_query_like'));
		return $pieces;
	}

	function get_locations($as_location_objects=true) {
		$query = new WP_Query(array(
			'post_type'=>mpfy_get_supported_post_types(),
			'posts_per_page'=>-1,
			'fields'=>'ids',
			'orderby'=>'menu_order',
			'order'=>'ASC',
			'meta_query'=>array(
				'relation'=>'OR',
				array(
					'key'=>'_map_location_map_id',
					'value'=>$this->get_id(),
				),
			),
		));

		$posts = $query->posts;

		$locations = array();

		foreach ($posts as $pid) {
			if ($as_location_objects) {
				$locations[] = new Mpfy_Map_Location($pid);
			} else {
				$locations[] = (object) array('ID'=>$pid);
			}
		}

		return $locations;
	}
}
