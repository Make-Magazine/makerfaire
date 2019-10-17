<?php

/**
 * Hold map markers and generate the output for the Maps
 */
class GravityView_Maps_Data {

	/**
	 * @var array of GravityView_Maps_Marker
	 */
	var $markers = array();

	/**
	 * Whether the marker position is defined by an address field or a pair of fields with the 'coordinates' (lat/long)
	 * @var string
	 */
	var $position_mode = 'address';

	/**
	 * @var GravityView_Maps_Data
	 */
	static $instance = NULL;

	function __wakeup() {}
	function __sleep() {}
	function __clone() {}


	function __construct( GravityView_View $view ) {

		self::$instance = &$this;

		$this->process_view( $view );

	}

	/**
	 * Take the View and create Markers based on the entries
	 *
	 * @param $view GravityView_View
	 */
	function process_view( $view ) {

		// get view map settings
		$ms = GravityView_Maps_Admin::get_map_settings( $view->view_id );

		$position_fields = $this->get_position_fields( $ms, $view );

		if ( empty( $position_fields ) ) {
			return;
		}

		// Prepare marker info window if enabled
		$infowindow = empty( $ms['map_info_enable'] ) ? false : new GravityView_Maps_InfoWindow( $ms );

		// get icon picker field
		$icon_field_id = $this->get_icon_field( $view );
		$icon_field = empty( $icon_field_id ) ? false : RGFormsModel::get_field( $view->getForm(), $icon_field_id );

		foreach ( (array)$view->entries as $entry ) {

			$icon_url = empty( $icon_field ) ? null : RGFormsModel::get_lead_field_value( $entry, $icon_field );
			$icon_data = empty( $icon_url ) ? array() : array( $icon_url );

			$marker = new GravityView_Maps_Marker( $entry, $position_fields, $icon_data, $this->position_mode );

			if( $infowindow ) {
				$marker->set_infowindow_content( $infowindow->get_marker_content( $view, $entry, $marker->get_entry_url() ) );
			}

			$this->add_marker( $marker );

		}

	}

	/**
	 * @param $map_settings array View Map settings
	 * @param $view object GravityView_View
	 *
	 * @return array|GF_Field|string
	 */
	function get_position_fields( $map_settings, $view ) {

		/**
		 * @filter `gravityview/maps/markers/lat_long/fields_id` Enable marker position by feeding the latitude and longitude coordinates from form fields ids
		 * @since 1.2
		 * @param array $lat_long_fields Array of latitude/longitude of Gravity Forms field IDs
		 * @param GravityView_View object $view Current View object
		 */
		$lat_long_field_ids = apply_filters( 'gravityview/maps/markers/lat_long/fields_id', array(), $view );

		$this->position_mode = empty( $lat_long_field_ids ) || !is_array( $lat_long_field_ids ) ? 'address' : 'coordinates';

		$position_fields = '';

		if( 'coordinates' === $this->position_mode ) {

			foreach ( $lat_long_field_ids as $field_id ) {
				$position_fields[] = RGFormsModel::get_field( $view->getForm(), $field_id );
			}

		} else {

			// Address mode
			$address_field_id = !empty( $map_settings['map_address_field'] ) ? $map_settings['map_address_field'] : '';

			/**
			 * @filter `gravityview/maps/markers/address/field_id` Customise the Address Field ID (to be used when address is in a different field than the GF Address field)
			 * @since 1.2
			 * @param mixed $address_field_id Gravity Forms field ID
			 * @param GravityView_View object $view Current View object
			 */
			$address_field_id = apply_filters( 'gravityview/maps/markers/address/field_id', $address_field_id, $view );

			$position_fields = ! empty( $address_field_id ) ? RGFormsModel::get_field( $view->getForm(), $address_field_id ) : '';
		}

		return $position_fields;
	}


	/**
	 * Get markers for map service.
	 *
	 * @param string $service The name of the service to use, if implemented
	 *
	 * @return array
	 */
	static function get_markers( $service = 'Google' ) {

		// If the method has been defined (like get_markers_google ), return it.
		if( method_exists( 'GravityView_Maps_Data', "get_markers_{$service}") ) {

			return call_user_func( array( __CLASS__, "get_markers_{$service}" ) );

		} else {
			_doing_it_wrong( 'get_markers', 'The service you requested has not been implemented: '. $service, '1.0.0' );
		}
	}

	/**
	 * Generate marker array for Google
	 * @return array
	 */
	protected static function get_markers_google() {

		$markers = self::get_instance()->markers;

		$coordinates = array();

		/** @var GravityView_Maps_Marker $marker */
		foreach( $markers as $marker ) {

			$position = $marker->get_position();

			/**
			 * Make sure there's lat & long defined.
			 * @since 1.3
			 */
			if( empty( $position ) || empty( $position[0] ) || empty( $position[1] ) ) {
				continue;
			}

			$icon = $marker->get_icon();
			$icon_url = !empty( $icon ) ? $icon->url : null;
			$coordinates[] = array(
				'entry_id' => $marker->get_entry_id(),
				'lat' => $position[0],
				'long' => $position[1],
				'icon_url' => $icon_url,
				'url' => $marker->get_entry_url(),
				'content' => $marker->get_infowindow_content()
			);
		}

		return $coordinates;
	}

	/**
	 * Get the one true instance.
	 *
	 * @return GravityView_Maps_Data
	 */
	static function get_instance() {

		if ( empty( self::$instance ) ) {
			self::$instance = new GravityView_Maps_Data( null );
		}

		return self::$instance;
	}

	/**
	 * Create a marker and add it to the $markers array
	 * 
	 * @param GravityView_Maps_Marker $marker
	 */
	public function add_marker( GravityView_Maps_Marker $marker ) {

		array_push( self::get_instance()->markers, $marker );

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
		 * @since 1.2
		 * @param $value string Field ID used to retrieve the map entry icon
		 * @param $view_id string View ID
		 * @param $form_id string Form ID
		 */
		$custom_icon_field = apply_filters( 'gravityview/maps/markers/icon/field_id', '', $view->getViewId(), $view->getFormId() );

		if( !empty( $custom_icon_field ) ) {
			return $custom_icon_field;
		}

		// Retrieve default Map Icon field (if exists)
		$fields = GVCommon::get_form_fields( $view->getForm(), false, true );

		if( !empty( $fields ) ) {

			foreach( $fields as $id => $field ) {

				if( in_array( $field['type'], array( 'gvmaps_icon_picker' ) ) ) {
					return $id;
				}

			}
		}

		return false;
	}

}
