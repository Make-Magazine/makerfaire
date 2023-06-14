<?php
/**
 * Gravity Forms Geolocation - Mashup Maps.
 *
 * @package gravityforms-geolocation.
 *
 * @author Eyal Fitoussi
 *
 * @since 3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * GFGEO_Mashup_Map class
 *
 * @since 3.0
 */
class GFGEO_Mashup_Map {

	/**
	 * Plugin's prefix.
	 *
	 * @var string
	 */
	public $prefix = 'gfgeo';

	/**
	 * Maps object.
	 *
	 * This is an array that will hold all the maps objects and pass theme to JS to generate the maps.
	 *
	 * @var array
	 */
	public static $maps = array();

	/**
	 * Enable markers clusterer?
	 *
	 * @var boolean
	 */
	public static $clusters_enabled = false;

	/**
	 * Shortcode default arguments.
	 *
	 * @var array
	 */
	protected $args = array(
		'element_id'          => 0,
		'form_id'             => 0,
		'include_entries'     => '',
		'exclude_entries'     => '',
		'geocoders_id'        => '',
		'info_window_content' => 'formatted_address',
		'map_height'          => '400px',
		'map_width'           => '100%',
		'map_type'            => 'ROADMAP',
		'entries_count'       => 500,
		'scrollwheel_zoom'    => 1,
		'map_markers'         => '',
		'group_markers'       => 'markers_clusterer',
		'clusters_path'       => 'https://raw.githubusercontent.com/googlemaps/js-marker-clusterer/gh-pages/images/m',
	);

	/**
	 * [__construct description].
	 *
	 * @param array $atts [description].
	 */
	public function __construct( $atts = array() ) {

		// get the shortcode atts.
		$this->args = shortcode_atts( $this->args, $atts );

		if ( 'markers_clusterer' === $this->args['group_markers'] ) {
			self::$clusters_enabled = true;
		}

		// set random element id if not exists.
		$this->args['element_id'] = ! empty( $this->args['element_id'] ) ? $this->args['element_id'] : wp_rand( 10, 1000 );

		if ( ! empty( $this->args['map_markers'] ) ) {

			$map_markers               = explode( ',', $this->args['map_markers'] );
			$this->args['map_markers'] = array();

			foreach ( $map_markers as $map_marker ) {

				$map_marker = explode( '|', $map_marker );

				if ( ! empty( $map_marker[0] ) && ! empty( $map_marker[1] ) ) {
					$this->args['map_markers'][ $map_marker[0] ] = $map_marker[1];
				}
			}
		} else {
			$this->args['map_markers'] = array();
		}
	}

	/**
	 * Generate the map HTMl element.
	 *
	 * @since 3.0
	 *
	 * @param  [type] $args      [description].
	 *
	 * @param  [type] $locations [description].
	 *
	 * @return [type]            [description]
	 */
	public function get_map_element( $args, $locations ) {

		$prefix         = esc_attr( $this->prefix );
		$args           = apply_filters( $this->prefix . '_mashup_map_output_args', $args );
		$args['prefix'] = $prefix;
		$map_id         = esc_attr( $args['map_id'] );
		$map_name       = ! empty( $args['map_name'] ) ? esc_attr( $args['map_name'] ) : 'gfgeo-mashup-map';

		$args = array_map( 'esc_attr', $args );

		$output['open']          = '<div id="' . $prefix . '-mashup-map-wrapper-' . $map_id . '" class="' . $prefix . '-mashup-map-wrapper ' . $map_name . '" data-map_id="' . $map_id . '" style="width:' . esc_attr( $args['map_width'] ) . ';height:' . esc_attr( $args['map_height'] ) . '">';
		$output['resize_toggle'] = '<span id="' . $prefix . '-resize-map-toggle-' . $map_id . '" class="' . $prefix . '-resize-map-toggle ' . $prefix . '-icon-resize-full" style="display:none;" title="' . __( 'Resize map', 'GJM' ) . '"></span>';
		$output['map']           = '<div id="' . $prefix . '-map-' . $map_id . '" data-map-id="' . $map_id . '" class="' . $prefix . '-map" style="width:100%; height:100%"></div>';
		$output['loader']        = '<i id="' . $prefix . '-map-loader-' . $map_id . '" class="' . $prefix . '-map-loader ' . $prefix . '-icon-spin-thin animate-spin"></i>';
		$output['close']         = '</div>';

		// modify the map element.
		$output = apply_filters( $this->prefix . '_mashup_map_output', $output, $args, $locations );

		// Generate the map object.
		$this->get_map_object( $args, $locations );

		return implode( ' ', $output );
	}

	/**
	 * Generate the map object that will pass to the JS function.
	 *
	 * @param  array $map_args   map settings/arguments.
	 *
	 * @param  array $locations  locations to display on the map.
	 *
	 * @return [type]            [description]
	 */
	public function get_map_object( $map_args = array(), $locations = array() ) {

		// randomize map ID if not exists.
		$map_id = ! empty( $map_args['map_id'] ) ? $map_args['map_id'] : wp_rand( 100, 1000 );

		// default map options.
		$map_options = array(
			'backgroundColor'        => '#f1f1f1',
			'disableDefaultUI'       => false,
			'disableDoubleClickZoom' => false,
			'draggable'              => true,
			'draggableCursor'        => '',
			'draggingCursor'         => '',
			'fullscreenControl'      => false,
			'keyboardShortcuts'      => true,
			'mapMaker'               => false,
			'mapTypeControl'         => true,
			'mapTypeControlOptions'  => true,
			'mapTypeId'              => 'ROADMAP',
			'maxZoom'                => null,
			'minZoom'                => null,
			'zoomLevel'              => 13,
			'noClear'                => false,
			'rotateControl'          => true,
			'scaleControl'           => true,
			'scrollwheel'            => true,
			'streetViewControl'      => true,
			'styles'                 => null,
			'tilt'                   => null,
			'zoomControl'            => true,
			'resizeMapControl'       => true,
			'panControl'             => true,
		);

		// push the map args into the global array of maps.
		self::$maps[ $map_id ] = array(
			'args'        => $map_args,
			'map_options' => $map_options,
			'locations'   => $locations,
		);

		// allow plugins modify the map args.
		self::$maps[ $map_id ] = apply_filters( $this->prefix . '_mashup_map_element', self::$maps[ $map_id ], $map_id );

		return self::$maps[ $map_id ];
	}

	/**
	 * Info-window content.
	 *
	 * @param  array $entry           entry values.
	 *
	 * @param  array $fields          fields ID to display in the info-window.
	 *
	 * @param  array $geocoder_data   geocoder field values.
	 *
	 * @param  array $geocoder_fields geocoder fields data/options.
	 *
	 * @param  array $args            shortcode args.
	 *
	 * @return [type]         [description]
	 */
	public function get_info_window_content( $entry, $fields, $geocoder_data, $geocoder_fields, $args ) {

		$output         = array();
		$output['wrap'] = '<ul class="' . $this->prefix . '-info-window-wrapper ' . $this->prefix . '-mashup-map-info-window">';

		foreach ( $fields as $field ) {

			if ( 'formatted_address' === $field ) {

				$output['formatted_address']  = '<li class="' . $this->prefix . '-iw-single-field-wrapper field-formatted-address">';
				$output['formatted_address'] .= '<span class="' . $this->prefix . '-iw-content-field field-' . $field . '">' . esc_attr( $geocoder_data['formatted_address'] ) . '</span>';
				$output['formatted_address'] .= '</li>';

			} else {

				$label     = '';
				$label_css = '';

				if ( strpos( $field, '|' ) !== false ) {

					$label_field = explode( '|', $field );

					$label     = $label_field[0];
					$field     = $label_field[1];
					$label_css = ' has-label ';
				}

				if ( strpos( $field, '.' ) !== false ) {

					$field = explode( '.', $field );

					if ( ! is_array( $entry[ $field[0] ] ) ) {
						$entry[ $field[0] ] = maybe_unserialize( $entry[ $field[0] ] );
					}

					if ( is_array( $entry[ $field[0] ] ) && isset( $entry[ $field[0] ][ $field[1] ] ) && '' !== $entry[ $field[0] ][ $field[1] ] ) {
						$field_value = esc_attr( $entry[ $field[0] ][ $field[1] ] );
					} else {
						$field_value = __( 'N/A', 'gfgeo' );
					}

					$output[ $field[0] . '.' . $field[1] ] = '<li class="' . $this->prefix . '-iw-single-field-wrapper field-' . $field[0] . '-' . $field[1] . ' ' . $label_css . '">';

					if ( ! empty( $label ) ) {
						$output[ $field[0] . '.' . $field[1] ] .= '<span class="' . $this->prefix . '-iw-label">' . esc_attr( $label ) . ':&nbsp;</span>';
					}

					$output[ $field[0] . '.' . $field[1] ] .= '<span class="' . $this->prefix . '-iw-field">' . $field_value . '</span>';
					$output[ $field[0] . '.' . $field[1] ] .= '</li>';

				} else {

					if ( isset( $entry[ $field ] ) && '' !== $entry[ $field ] ) {
						$field_value = esc_attr( $entry[ $field ] );
					} else {
						$field_value = __( 'N/A', 'gfgeo' );
					}

					$output[ $field ] = '<li class="' . $this->prefix . '-iw-single-field-wrapper field-' . $field . ' ' . $label_css . '">';

					if ( ! empty( $label ) ) {
						$output[ $field ] .= '<span class="' . $this->prefix . '-iw-label">' . esc_attr( $label ) . ':&nbsp;</span>';
					}

					$output[ $field ] .= '<span class="' . $this->prefix . '-iw-field field-' . $field . '">' . $field_value . '</span>';

					$output[ $field ] .= '</li>';
				}
			}
		}

		$output['/wrap'] = '</ul>';

		$output = apply_filters( $this->prefix . '_mashup_map_info_window_content', $output, $entry, $fields, $geocoder_data, $geocoder_fields, $args );

		return implode( '', $output );
	}

	/**
	 * Modify the entries query to retrive specific entries based on included/exlucded entry IDs.
	 *
	 * @param  array $sql original sql query clauses.
	 *
	 * @return array      modified query clauses.
	 *
	 * @author Eyal Fitoussi
	 *
	 * @since 3.0
	 */
	public function query_clauses( $sql ) {

		if ( ! empty( $this->args['include_entries'] ) ) {
			$sql['where'] .= ' AND t1.id IN ( ' . $this->args['include_entries'] . ' ) ';
		}

		if ( ! empty( $this->args['exclude_entries'] ) ) {
			$sql['where'] .= ' AND t1.id NOT IN ( ' . $this->args['exclude_entries'] . ' )';
		}

		return apply_filters( $this->prefix . '_mashup_map_query_clauses', $sql, $this->args );
	}

	/**
	 * Get locations from entries.
	 *
	 * @return [type] [description]
	 */
	public function get_locations() {

		// If including or excluding specific entries.
		if ( ! empty( $this->args['include_entries'] ) || ! empty( $this->args['exclude_entries'] ) ) {
			add_filter( 'gform_gf_query_sql', array( $this, 'query_clauses' ) );
		}

		$query_args = apply_filters(
			$this->prefix . '_mashup_map_query_args',
			array(
				'form_id'         => $this->args['form_id'],
				'search_criteria' => array(),
				'sorting'         => null,
				'paging'          => array(
					'offset'    => 0,
					'page_size' => $this->args['entries_count'],
				),
				'total_count'     => null,
			)
		);

		// Get form entries.
		$entries = GFAPI::get_entries( $query_args['form_id'], $query_args['search_criteria'], $query_args['sorting'], $query_args['paging'], $query_args['total_count'] );

		// No locations to show.
		if ( empty( $entries ) ) {
			return array();
		}

		$locations          = array();
		$geocoders_id       = ! empty( $this->args['geocoders_id'] ) ? explode( ',', $this->args['geocoders_id'] ) : array();
		$info_window_fields = ! empty( $this->args['info_window_content'] ) ? explode( ',', $this->args['info_window_content'] ) : '';
		$geocoder_fields    = array();

		if ( empty( $geocoders_id ) || ! array_filter( $geocoders_id ) ) {

			$form = GFAPI::get_form( $this->args['form_id'] );

			foreach ( $form['fields'] as $field ) {

				if ( 'gfgeo_geocoder' === $field->type ) {
					$geocoders_id[] = $field->id;
				}
			}
		}

		// Loop through entries and collect the geocoders data.
		foreach ( $entries as $entry ) {

			foreach ( $entry as $field_id => $field_value ) {

				if ( in_array( $field_id, $geocoders_id ) ) {

					$geocoder_data      = maybe_unserialize( $field_value );
					$entry[ $field_id ] = $geocoder_data;

					if ( ! empty( $geocoder_data['status'] ) ) {

						// Get the field data of each geocoder. We run this once for each geocoder.
						if ( ! isset( $geocoder_fields[ $field_id ] ) ) {

							$geocoder_fields[ $field_id ] = GFFormsModel::get_field( $this->args['form_id'], $field_id );

							if ( empty( $this->args['map_markers'][ $field_id ] ) ) {
								$this->args['map_markers'][ $field_id ] = ! empty( $geocoder_fields[ $field_id ]->gfgeo_map_marker_url ) ? $geocoder_fields[ $field_id ]->gfgeo_map_marker_url : '';
							}
						}

						// Info-window content.
						$geocoder_data['id']                  = $field_id;
						$geocoder_data['marker_url']          = $this->args['map_markers'][ $field_id ];
						$geocoder_data['info_window_content'] = ! empty( $info_window_fields ) ? $this->get_info_window_content( $entry, $info_window_fields, $geocoder_data, $geocoder_fields, $this->args ) : false;
						$locations[]                          = apply_filters( $this->prefix . '_mashup_map_loop_location', $geocoder_data, $entry, $geocoder_fields, $this->args );
					}
				}
			}
		}

		return apply_filters( $this->prefix . '_mashup_map_locations', $locations, $entry, $geocoder_fields, $this->args );
	}

	/**
	 * Output mashup map.
	 */
	public function display() {

		$locations = $this->get_locations();

		// map arguments.
		$map_args = array(
			'map_id'        => $this->args['element_id'],
			'zoom_level'    => 'auto',
			'map_type'      => $this->args['map_type'],
			'map_width'     => $this->args['map_width'],
			'map_height'    => $this->args['map_height'],
			'scrollwheel'   => $this->args['scrollwheel_zoom'],
			'group_markers' => $this->args['group_markers'],
			'clusters_path' => $this->args['clusters_path'],
			'map_name'      => 'gfgeo-mashup-map',
		);

		// Run scripts.
		if ( is_admin() ) {
			add_action( 'admin_footer', array( 'GFGEO_Mashup_Map', 'enquque_scripts' ) );
		} else {
			add_action( 'wp_footer', array( 'GFGEO_Mashup_Map', 'enquque_scripts' ) );
		}

		// generate the map element.
		return $this->get_map_element( $map_args, $locations );
	}

	/**
	 * Enqueue and localize map's scripts and styles.
	 */
	public static function enquque_scripts() {

		// Abort if not maps to display.
		if ( empty( self::$maps ) ) {
			return;
		}

		// Enqueue Google Maps Library.
		if ( ! wp_script_is( 'google-maps', 'enqueued' ) ) {

			$google_url = GFGEO_Helper::get_google_maps_url();

			wp_register_script( 'google-maps', implode( '', $google_url ), array( 'jquery' ), GFGEO_VERSION, true );
			wp_enqueue_script( 'google-maps' );
		}

		if ( ! wp_script_is( 'gfgeo-map', 'enqueued' ) ) {

			wp_enqueue_script( 'gfgeo-map', GFGEO_URL . '/assets/js/gfgeo.maps.min.js', array( 'jquery' ), GFGEO_VERSION, true );

			// pass the mapObjects to JS.
			wp_localize_script( 'gfgeo-map', 'gfgeoMapObjects', self::$maps );
		}

		if ( ! wp_style_is( 'gfgeo', 'enqueued' ) ) {
			wp_enqueue_style( 'gfgeo', GFGEO_URL . '/assets/css/gfgeo.min.css', array(), GFGEO_VERSION );
		}

		// Enqueue clusters script.
		if ( true === self::$clusters_enabled && ! wp_enqueue_script( 'gfgeo-marker-cluster' ) ) {
			wp_enqueue_script( 'gfgeo-marker-cluster', GFGEO_URL . '/assets/js/google.markercluster.min.js', array(), GFGEO_VERSION, true );
		}
	}
}

/**
 * Gravity Geolocation mashup map shortcode.
 *
 * @param  array $atts shortcode aruments.
 *
 * @since 3.0
 *
 * @return void
 */
function gfgeo_mashup_map( $atts ) {

	if ( is_admin() && apply_filters( 'gfgeo_disable_shortcode_in_admin', false ) ) {
		return;
	}

	// make sure the class of the item exists.
	if ( ! class_exists( 'GFGEO_Mashup_Map' ) ) {
		return;
	}

	$mashup_map = new GFGEO_Mashup_Map( $atts );

	// output the map.
	return $mashup_map->display();
}
add_shortcode( 'gfgeo_mashup_map', 'gfgeo_mashup_map' );
