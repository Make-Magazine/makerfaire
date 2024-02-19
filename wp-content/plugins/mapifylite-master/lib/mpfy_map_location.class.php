<?php
class Mpfy_Map_Location {
	private $post;

	static function get_all_locations() {
		$raw = new WP_Query(array(
			'post_type'=>mpfy_get_supported_post_types(),
			'posts_per_page'=>-1,
			'post_status'=>'any',
		));

		$locations = array();
		foreach ($raw->posts as $r) {
			$locations[$r->ID] = $r->post_title;
		}
		return $locations;
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

	function get_id() {
		return $this->post->ID;
	}

	function get_title() {
		return $this->post->post_title;
	}

	function get_content() {
		return $this->post->post_content;
	}

	function get_thumbnail() {
		$value = apply_filters( 'mpfy_map_location_thumbnail', '', $this->get_id() );
		return $value;
	}

	function get_maps() {
		$maps = get_post_meta($this->get_id(), '_map_location_map', true);
		$maps = array_filter(array_map('intval', explode(',', $maps)));
		return $maps;
	}

	function get_coordinates() {
		$coordinates = explode(',', get_post_meta($this->get_id(), '_map_location_google_location', true));
		$coordinates = array_map( 'floatval', $coordinates );
		$coordinates = array_slice( $coordinates, 0, 2 );
		if ( count( $coordinates ) < 2 ) {
			$coordinates = array( 0, 0 );
		}
		return $coordinates;
	}

	function get_directions_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_location_popup_directions', true);
		return $value;
	}

	function get_directions_url() {
		$coordinates = $this->get_coordinates();
		$directions_url = 'http://www.google.com/maps/?saddr=&daddr=' . $coordinates[0] . ',' . $coordinates[1];
		$override = get_post_meta( $this->get_id(), '_map_location_directions_url', true );
		if ( !empty( $override ) ) {
			$directions_url = $override;
		}
		return esc_url( $directions_url );
	}

	function get_popup_enabled() {
		$value = apply_filters( 'mpfy_map_location_popup_enabled', false, $this->get_id() );
		return $value;
	}

	function get_tooltip_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_location_tooltip_show', true);
		return $value;
	}

	function get_tooltip_close_behavior() {
		$value = get_post_meta($this->get_id(), '_map_location_tooltip_close', true);
		$value = ($value) ? $value : 'auto';
		return $value;
	}

	function get_tooltip_text() {
		$value = get_post_meta($this->get_id(), '_map_location_tooltip', true);
		return $value;
	}

	function get_tooltip_content( $map_id = 0 ) {
		ob_start();
		$thumbnail = $this->get_thumbnail();

		do_action('mpfy_tooltip_content_before', $this->get_id());

		if ( $thumbnail ) {
			echo '<div class="mpfy-tooltip-image">' . $thumbnail . '</div>';
		}

		$text = $this->get_tooltip_text();
		$text = apply_filters( 'mpfy_map_location_tooltip_text', $text, $this->get_id(), $map_id );

		printf( '<div class="mpfy-tooltip-content"><p><strong>%s</strong><br />%s</p></div>', $this->get_title(), $text  );

		do_action('mpfy_tooltip_content_after', $this->get_id());

		$html = ob_get_clean();

		return $html;
	}

	function get_tags() {
		$value = wp_get_object_terms($this->get_id(), 'location-tag');
		$value = ( $value && !is_wp_error( $value ) ) ? $value : array();
		return $value;
	}

	function get_pin_image($map_id = 0) {
		$result = array(
			'url'=>'',
			'size'=>false,
			'anchor'=>array(0, 0),
		);

		if (!$map_id) {
			$map_id = $this->get_maps();
			$map_id = isset($map_id[0]) ? $map_id[0] : 0;
		}
		$map = new Mpfy_Map($map_id);

		$pin_image = apply_filters( 'mpfy_map_location_pin_image', $map->get_default_pin_image(), $this->get_id(), $map->get_id() );

		if ($pin_image) {
			$result['url'] = mpfy_get_file_real_url($pin_image);
			$file_path = mpfy_get_file_real_path($pin_image);
			if ($file_path) {
				$result['size'] = @getimagesize($file_path);
				if ($result['size']) {
					$result['anchor'] = array(
						round($result['size'][0] / 2),
						$result['size'][1],
					);
				}
			}
		}

		return $result;
	}

	function get_video_embed() {
		$video = Mpfy_Carbon_Video::create(get_post_meta($this->get_id(), '_map_location_video_embed', true));
		return $video->get_embed_code(640, 480);
	}

	function get_video_thumb() {
		$video = Mpfy_Carbon_Video::create($this->get_video_embed());
		return $video->get_image();
	}

	function get_gallery_images() {
		// get array gallery images
		$mapify_map_location = new \Acf_Mapifylite\Model\Mapify_Map_Location( $this->get_id() );
		$gallery_images      = $mapify_map_location->get_gallery_images();
		$value               = array();

		// re-format the array for frontend purpose
		if ( $gallery_images ) {
			foreach ( $gallery_images as $image_url ) {
				$value[] = array(
					'_type' => '_',
					'image' => $image_url,
				);
			}
		}

		return $value;
	}

	function get_address() {
		$value = get_post_meta($this->get_id(), '_map_location_address', true);
		return $value;
	}

	function get_address_line_2() {
		$value = get_post_meta($this->get_id(), '_map_location_address_2', true);
		return $value;
	}

	function get_city() {
		$value = get_post_meta($this->get_id(), '_map_location_city', true);
		return $value;
	}

	function get_state() {
		$value = get_post_meta($this->get_id(), '_map_location_state', true);
		return $value;
	}

	function get_zip() {
		$value = get_post_meta($this->get_id(), '_map_location_zip', true);
		return $value;
	}

	function get_country() {
		$value = get_post_meta($this->get_id(), '_map_location_country', true);
		return $value;
	}

	function get_formatted_address($format, $major_separator=' | ', $minor_separator=', ') {
		$data = $format;

		foreach ($data as $gid => $group) {
			foreach ($group as $fid => $field) {
				$m = 'get_' . $field;
				if (method_exists($this, $m)) {
					$data[$gid][$fid] = $this->{$m}();
				}
			}
			$data[$gid] = array_filter($data[$gid]);
			$data[$gid] = implode($minor_separator, $data[$gid]);
		}
		$data = array_filter($data);
		$data = implode($major_separator, $data);
		return $data;
	}
}