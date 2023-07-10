<?php

namespace GravityKit\GravityMaps;

use RGFormsModel;
use GravityView_View;
use GV\View;
use GVCommon;
use GF_Field;

/**
 * Holds map markers and generates the output for the Maps
 */
class Data {
	protected static $instances = [];

	/**
	 * @var Marker[] array of Marker
	 */
	var $markers = [];

	/**
	 * Whether the marker position is defined by an address field or a pair of fields with the 'coordinates' (lat/long)
	 *
	 * @var string
	 */
	var $position_mode = 'address';

	/**
	 * @var Data
	 */
	static $instance = null;

	function __wakeup() {
	}

	function __sleep() {
	}

	function __clone() {
	}

	/**
	 * If passing a View, the View will be processed by process_view()
	 *
	 * @param GravityView_View|null $view
	 */
	function __construct( $view = null ) {
		if ( ! $view instanceof GravityView_View ) {
			return;
		}

		$this->process_view( $view );
	}

	/**
	 * Take the View and create Markers based on the entries
	 *
	 * @param $view GravityView_View
	 */
	function process_view( $view ) {
		// get view map settings
		$ms = Admin::get_map_settings( $view->view_id );

		$position_fields = $this->get_position_fields( $ms, $view );

		if ( empty( $position_fields ) ) {
			return;
		}

		Markers::prime_cache( $view->entries, $position_fields );

		// Prepare marker info window if enabled
		$infowindow = empty( $ms['map_info_enable'] ) ? false : new InfoWindow( $ms );

		// get icon picker field
		$icon_field_id     = $this->get_icon_field( $view );
		$icon_field        = empty( $icon_field_id ) ? false : RGFormsModel::get_field( $view->getForm(), $icon_field_id );
		$custom_icon_field = Custom_Map_Icons::get_icon_options_field( $view );

		foreach ( (array) $view->entries as $entry ) {
			$icon_url  = empty( $icon_field ) ? null : RGFormsModel::get_lead_field_value( $entry, $icon_field );
			$icon_data = empty( $icon_url ) ? [] : [ $icon_url ];

			if ( $custom_icon_field ) {
				$entry_custom_icon = Custom_Map_Icons::get_selected_icon( $custom_icon_field, $entry );
			}

			if ( 'coordinates' === $this->position_mode ) {
				$marker = Marker::from_coordinate_fields( $entry, $position_fields[0], $position_fields[1], $icon_data );

				if ( ! empty( $custom_icon_field ) && empty( $icon_data ) ) {
					$marker->set_icon( $entry_custom_icon );
				}

				if ( $infowindow ) {
					$marker->set_infowindow_content( $infowindow->get_marker_content( $view, $entry, $marker->get_entry_url() ) );
				}

				/**
				 * @filter `gravityview/maps/marker/add` Modify the marker before it gets added to a map.
				 *
				 * @since  1.7.2
				 *
				 * @param Marker   $marker The marker about to be added.
				 * @param \GV\View $view   The View.
				 */
				$marker = apply_filters( 'gravityview/maps/marker/add', $marker, View::by_id( $view->view_id ) );

				if ( ! $marker instanceof Marker ) {
					continue;
				}

				if ( ! $marker->is_valid() ) {
					continue;
				}

				$this->add_marker( $marker );
			} else {
				foreach ( $position_fields as $position_field ) {
					$marker = Marker::from_address_field( $entry, $position_field, $icon_data );

					if ( ! empty( $custom_icon_field ) && empty( $icon_data ) ) {
						$marker->set_icon( $entry_custom_icon );
					}

					if ( $infowindow ) {
						$marker->set_infowindow_content( $infowindow->get_marker_content( $view, $entry, $marker->get_entry_url() ) );
					}

					/**
					 * @filter `gravityview/maps/marker/add` Modify the marker before it gets added to a map.
					 *
					 * @since  1.7.2
					 *
					 * @param Marker   $marker The marker about to be added.
					 * @param \GV\View $view   The View.
					 */
					$marker = apply_filters( 'gravityview/maps/marker/add', $marker, View::by_id( $view->view_id ) );

					if ( ! $marker instanceof Marker ) {
						gravityview()->log->debug(
							'A marker was not added to the map because it was not a valid Marker object.',
							[
								'marker' => $marker,
								'view' => $view,
							]
						);
						continue;
					}

					if ( ! $marker->is_valid() ) {
						gravityview()->log->debug(
							'A marker was not added to the map because it was not a valid Marker.',
							[
								'marker' => $marker,
								'view' => $view,
							]
						);

						continue;
					}

					$this->add_marker( $marker );
				}
			}
		}
	}

	/**
	 * @param $map_settings array View Map settings
	 * @param $view         object GravityView_View
	 *
	 * @return array|GF_Field
	 */
	public function get_position_fields( $map_settings, $view ) {
		/**
		 * @filter `gravityview/maps/markers/lat_long/fields_id` Enable marker position by feeding the latitude and longitude coordinates from form fields ids
		 * @since  1.2
		 *
		 * @param array            $lat_long_fields Array of latitude/longitude of Gravity Forms field IDs
		 * @param GravityView_View $view            Current View object
		 */
		$lat_long_field_ids = apply_filters( 'gravityview/maps/markers/lat_long/fields_id', [], $view );

		$this->position_mode = empty( $lat_long_field_ids ) || ! is_array( $lat_long_field_ids ) ? 'address' : 'coordinates';

		$position_fields = [];

		if ( 'coordinates' === $this->position_mode ) {
			foreach ( $lat_long_field_ids as $field_id ) {
				$position_fields[] = RGFormsModel::get_field( $view->getForm(), $field_id );
			}
		} else {
			// Address mode
			$address_fields_ids = ! empty( $map_settings['map_address_field'] ) ? $map_settings['map_address_field'] : [];
			$address_fields_ids = ( is_array( $address_fields_ids ) ) ? $address_fields_ids : [ $address_fields_ids ];

			if ( ! empty( $address_fields_ids ) ) {

				foreach ( $address_fields_ids as $field_id ) {
					/**
					 * @filter `gravityview/maps/markers/address/field_id` Customise the Address Field ID (to be used when address is in a different field than the GF Address field)
					 * @since  1.2
					 *
					 * @param mixed            $address_field_id Gravity Forms field ID
					 * @param GravityView_View $view             Current View object
					 */
					$field_id = apply_filters( 'gravityview/maps/markers/address/field_id', $field_id, $view );

					$position_fields[] = RGFormsModel::get_field( $view->getForm(), $field_id );
				}
			}
		}

		if ( empty( $position_fields ) ) {
			do_action( 'gravityview_log_error', __METHOD__ . ': No position fields were set', $map_settings );
		}

		return array_filter( $position_fields );
	}

	/**
	 * Get markers for map service.
	 *
	 * @param string $service The name of the service to use, if implemented
	 *
	 * @return array
	 */
	public static function get_markers( $service = 'google' ) {

		// If the method has been defined (like get_markers_google ), return it.
		if ( method_exists( __CLASS__, "get_markers_{$service}" ) ) {
			/** @see get_markers_google */
			return call_user_func( [ __CLASS__, "get_markers_{$service}" ] );
		} else {
			_doing_it_wrong( 'get_markers', 'The service you requested has not been implemented: ' . $service, '1.0.0' );
		}
	}

	/**
	 * Generate marker array for Google
	 *
	 * @see Marker::to_array
	 *
	 * @return array Nested array of markers, each generated by Marker::to_array
	 */
	protected static function get_markers_google() {
		$markers = self::get_instance()->markers;

		$markers_array = [];

		/** @var Marker $marker */
		foreach ( $markers as $marker ) {
			if ( $m_array = $marker->to_array() ) {
				$markers_array[] = $m_array;
			}
		}

		return array_filter( $markers_array, [ Markers::class, 'filter_valid_marker_data' ] );
	}

	/**
	 * Get the one true instance.
	 *
	 * @return Data
	 */
	public static function get_instance( $view = null ) {
		$view_id = ! empty( $view ) && ! empty( $view->view_id ) ? $view->view_id : -1;

		if ( empty( static::$instances[ $view_id ] ) ) {
			static::$instances[ $view_id ] = new Data( $view );
		}

		return static::$instances[ $view_id ];
	}

	/**
	 * Create a marker and add it to the $markers array
	 *
	 * @param Marker $marker
	 */
	public function add_marker( Marker $marker ) {
		$markers = &self::get_instance()->markers;

		// Do not add marker if one already exists with the same entry ID and field ID
		$unique_id = $marker->get_id();

		if ( empty( $markers[ $unique_id ] ) ) {
			$markers[ $unique_id ] = $marker;
		}
	}

	/**
	 * Fetch the first Map Icon field in the current view form
	 *
	 * @param $view object GravityView_View
	 *
	 * @return bool|int|string Field ID
	 */
	public function get_icon_field( GravityView_View $view ) {
		/**
		 * @filter `gravityview/maps/markers/icon/field_id` Allow for custom icon field instead of the default one (configured in the form as Map icon picker )
		 * @since  1.2
		 *
		 * @param $value   string Field ID used to retrieve the map entry icon
		 * @param $view_id string View ID
		 * @param $form_id string Form ID
		 */
		$custom_icon_field = apply_filters( 'gravityview/maps/markers/icon/field_id', '', $view->getViewId(), $view->getFormId() );

		if ( ! empty( $custom_icon_field ) ) {
			return $custom_icon_field;
		}

		// Retrieve default Map Icon field (if exists)
		$fields = GVCommon::get_form_fields( $view->getForm(), false, true );

		if ( ! empty( $fields ) ) {
			foreach ( $fields as $id => $field ) {
				if ( in_array( $field['type'], [ 'gvmaps_icon_picker' ] ) ) {
					return $id;
				}
			}
		}

		return false;
	}

	public function get_markers_by_entry( $entry_id ) {
		return array_filter( $this->markers, static function ( $marker ) use ( $entry_id ) {
			return $entry_id == $marker->get_entry_id();
		} );
	}
}
