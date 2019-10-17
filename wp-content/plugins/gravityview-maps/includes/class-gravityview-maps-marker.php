<?php

class GravityView_Maps_Marker {

	/**
	 * @var null|GravityView_Maps_Icon
	 */
	protected $icon = NULL;

	/**
	 * Gravity Forms entry array
	 * @var array
	 */
	protected $entry = array();

	/**
	 * Full address without any line breaks or spaces
	 * @var string
	 */
	protected $address = NULL;

	/**
	 * Marker position - set of Latitude / Longitude
	 * @var array
	 */
	protected $position = NULL;

	/**
	 * Marker Entry URL
	 * @var array
	 */
	protected $entry_url = NULL;

	/**
	 * Marker Info Window content
	 * @var array
	 */
	protected $infowindow = NULL;

	/**
	 *
	 * @var GravityView_Maps_Cache_Markers instance
	 */
	private $cache = NULL;



	/**
	 * @param $entry
	 * @param $field string GF Field array - the field used to calculate the address
	 * @param array $icon {
	 *      Optional. Define custom icon data.
	 *
	 *      @link https://developers.google.com/maps/documentation/javascript/markers Read more on Markers
	 *      @param string $url URL of the icon
	 *      @param array $size Array of the size of the icon in pixels. Example: [20,30]
	 *      @param array $origin If using an image sprite, the start of the icon from top-left.
	 *      @param array $anchor Where the "pin" of the icon should be, example [0,32] for the bottom of a 32px icon
	 *      @param array $scaledSize How large should the icon appear in px (scaling down image for Retina)
	 * }
	 * @param string $mode Marker position mode: 'address' or 'coordinates'
	 *
	 */
	function __construct( $entry, $position_fields, $icon = array(), $mode = 'address' ) {

		// get the cache markers class instance
		$this->cache = $GLOBALS['gravityview_maps']->component_instances['cache-markers'];

		$this->entry = $entry;

		$this->entry_url = $this->set_entry_url( $entry );

		// generate the marker position (lat/long)

		if( 'address' === $mode ) {
			$this->address = $this->generate_address( $entry, $position_fields );
			$this->position = $this->generate_position_from_address( $entry, $position_fields );
		} else {
			$this->position = $this->generate_position_from_coordinates( $entry, $position_fields );
		}

		if( !empty( $icon ) ) {
			$this->icon = new GravityView_Maps_Icon( $icon[0] );
		}

	}

	/**
	 * @return GravityView_Maps_Icon
	 */
	public function get_icon() {
		return $this->icon;
	}

	/**
	 * @param GravityView_Maps_Icon $icon
	 */
	public function set_icon( GravityView_Maps_Icon $icon ) {
		$this->icon = $icon;
	}

	/**
	 * @return array
	 */
	public function get_entry() {
		return $this->entry;
	}

	/**
	 * @param array $entry
	 */
	public function set_entry( $entry ) {
		$this->entry = $entry;
	}

	/**
	 * @return string
	 */
	public function get_address() {
		return $this->address;
	}

	/**
	 * @param string $address
	 */
	public function set_address( $address ) {
		$this->address = $address;
	}

	/**
	 * @return array
	 */
	public function get_position() {
		return $this->position;
	}

	/**
	 * @param string $address
	 */
	public function set_position( $position ) {
		$this->position = $position;
	}

	/**
	 * @return array|string
	 */
	public function get_entry_url() {
		return $this->entry_url;
	}

	/**
	 * @param $entry
	 *
	 * @return string
	 */
	public function set_entry_url( $entry ) {

		if( !function_exists( 'gv_entry_link' ) ) {
			$url = '';
		} else {
			$url = gv_entry_link( $entry );
		}

		/**
		 * @filter `gravityview/maps/marker/url` Filter the marker single entry view url
		 * @since 1.4
		 * @param string $url Single entry view url
		 * @param array $entry Gravity Forms entry object
		 */
		$url = apply_filters( 'gravityview/maps/marker/url', $url, $entry );

		return $url;
	}

	/**
	 * @return mixed
	 */
	public function get_entry_id() {
		return $this->entry['id'];
	}


	public function set_infowindow_content( $content ) {
		$this->infowindow = $content;
	}

	public function get_infowindow_content() {
		return $this->infowindow;
	}


	/**
	 * Generate a string address with no line breaks from an address field
	 *
	 * @param array $entry GF Entry array
	 * @param array $field GF Field array
	 *
	 * @return string
	 */
	protected function generate_address( $entry, $field ) {

		// Get the address fields as an array (.3, .6, etc.)
		$value = RGFormsModel::get_lead_field_value( $entry, $field );

		// Get the text output (without map link)
		$address = GFCommon::get_lead_field_display( $field, $value, '', false, 'text' );

		// Replace the new lines with spaces
		$address = str_replace( "\n", ' ', $address );

		/**
		 * @filter `gravityview/maps/marker/address` Filter the address value
		 * @since 1.0.4
		 * @param string $address Address value
		 * @param array $entry Gravity Forms entry object
		 */
		$address = apply_filters( 'gravityview/maps/marker/address', $address, $entry );

		return $address;
	}

	/**
	 * Generate the marker position (Lat & Long) based on an address field
	 *
	 * @param array $entry GF Entry array
	 * @param array $field GF Field array
	 *
	 * @return array 0 => Latitude / 1 => Longitude
	 */
	protected function generate_position_from_address( $entry, $field ) {

		$position = $this->cache->get_cache_position( $entry['id'], $field['id'] );

		// in case position is not saved as entry meta, try to fetch it on a Geocoder service provider
		if( empty( $position ) && ! empty( $this->address ) ) {
			$position = $this->fetch_position( $this->address );
			$this->cache->set_cache_position( $entry['id'], $field['id'], $position );
		}

		return $position;
	}

	/**
	 * Geocode an Address to get the coordinates Lat/Long
	 * Uses Geocoder
	 *
	 * @param $address string
	 *
	 * @return mixed
	 */
	protected function fetch_position( $address ) {
		return $GLOBALS['gravityview_maps']->component_instances['geocoding']->geocode( $address );
	}

	/**
	 * Generate the marker position (Lat & Long) based on form fields
	 *
	 * @param array $entry GF Entry array
	 * @param array $field GF Field array
	 *
	 * @return array 0 => Latitude / 1 => Longitude
	 */
	protected function generate_position_from_coordinates( $entry, $fields ) {

		$position = array();

		if ( !empty( $fields ) && is_array( $fields ) ) {
			foreach( $fields as $field ) {
				$position[] = RGFormsModel::get_lead_field_value( $entry, $field );
			}
		}

		return $position;
	}

}