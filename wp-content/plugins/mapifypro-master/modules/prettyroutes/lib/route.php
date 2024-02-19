<?php
class PrettyRoutes_Route {
	static function load($post_id) {
		return new self($post_id);
	}

	function __construct($post_id) {
		$this->valid = false;
		$this->id = $post_id;

		$coords = carbon_get_post_meta($this->id, 'route_route');
		$coords = explode('|', $coords);

		$tooltip_background = '#000000';
		$tooltip_background = routes_hex2rgb($tooltip_background);
		$tooltip_background[] = 0.71;

		$waypoint_options = carbon_get_post_meta($this->id, 'route_waypoints', 'complex');

		$this->route_color = carbon_get_post_meta($this->id, 'route_color');
		$this->route_color = $this->route_color ? $this->route_color : '#00AAFF';

		$this->init_location_tags();

		$this->type = carbon_get_post_meta($this->id, 'route_type');
		$this->pins = array();
		foreach ( $coords as $data ) {
			$data = explode( ',', $data );
			$type = $data[0];

			$p = new stdClass();
			$p->type = $type;
			$p->latlng = array( $data[1], $data[2] );
			if ( ! array_filter( $p->latlng ) ) {
				continue;
			}

			if ($type != 'origin' && $type != 'destination') {
				$type = str_replace('-', '_', $type);
			}

			if ( substr( $type, 0, 8 ) === 'waypoint' ) {
				$index = preg_replace( '/[^\d]+/', '', $type );
				$isset = isset( $waypoint_options[ $index ] );
				$image = $isset ? $waypoint_options[ $index ]['pin'] : '';
				$p->pin_enabled = $isset ? $waypoint_options[ $index ]['pin_enabled'] : 'yes';
				$p->tooltip_enabled = $isset ? $waypoint_options[ $index ]['tooltip_enabled'] : 'yes';
				$p->tooltip_close = $isset ? $waypoint_options[ $index ]['tooltip_close'] : 'auto';
				$tooltip_content = $isset ? $waypoint_options[ $index ]['tooltip_content'] : '';
			} else {
				$image = carbon_get_post_meta($this->id, 'route_' . $type . '_pin');
				$p->pin_enabled = carbon_get_post_meta($this->id, 'route_' . $type . '_pin_enabled');
				$p->tooltip_enabled = carbon_get_post_meta($this->id, 'route_' . $type . '_tooltip_enabled');
				$p->tooltip_close = carbon_get_post_meta($this->id, 'route_' . $type . '_tooltip_close');
				$tooltip_content = carbon_get_post_meta($this->id, 'route_' . $type . '_tooltip_content');
			}

			$p->pin_enabled = ($p->pin_enabled) ? $p->pin_enabled : 'yes';
			$p->pin_enabled = (bool) ($p->pin_enabled == 'yes');

			$p->tooltip_enabled = ($p->tooltip_enabled) ? $p->tooltip_enabled : 'yes';
			$p->tooltip_enabled = (bool) ($p->tooltip_enabled == 'yes');

			$p->tooltip_close = ($p->tooltip_close) ? $p->tooltip_close : 'auto';

			$p->tooltip_content = wpautop(trim($tooltip_content));

			$p->tooltip_background = $tooltip_background;

			$image = routes_image($image, 'full', true);
			if ($image) {
				$p->pin_image = $image[0];
				$p->pin_anchor = array(
					round($image[1] / 2),
					$image[2],
				);
			} else {
				$p->pin_image = false;
				$p->pin_anchor = false;
			}

			$this->pins[$type] = $p;
		}

		if (count($this->pins) >= 2) {
			$this->valid = true;
		}
	}

	protected function init_location_tags() {
		$this->location_tags = get_the_terms( $this->id, 'location-tag' );
		if ( empty( $this->location_tags ) || is_wp_error( $this->location_tags ) ) {
			$this->location_tags = [];
		}
		$this->location_tags = array_map( function( $tag ) {
			return $tag->term_id;
		}, $this->location_tags );
	}

	function is_valid() {
		return $this->valid;
	}
}
