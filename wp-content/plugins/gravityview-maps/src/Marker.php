<?php

namespace GravityKit\GravityMaps;

use Exception;
use GFCommon;
use GFFormsModel;

class Marker {
	/**
	 * @var null|Icon
	 */
	protected $icon = null;

	/**
	 * Gravity Forms entry array
	 * @var array
	 */
	protected $entry = array();

	/**
	 * Gravity Forms address field object
	 * @var array
	 */
	protected $field = null;

	/**
	 * Full address without any line breaks or spaces
	 * @var string
	 */
	protected $address = null;

	/**
	 * Marker position - set of Latitude / Longitude
	 * @var array 0 => Latitude / 1 => Longitude
	 */
	protected $position = null;

	/**
	 * Marker Entry URL
	 * @var array
	 */
	protected $entry_url = null;

	/**
	 * Marker Info Window content
	 * @var array
	 */
	protected $infowindow = null;

	/**
	 *
	 * @var Cache_Markers instance
	 */
	private $cache = null;

	/**
	 * @param array                       $entry
	 * @param GF_Field_Address|GF_Field[] $field      GF Field used to calculate the address, or array of fields with position data, used when $mode is 'coordinates'
	 * @param array                       $icon       {
	 *                                                Optional. Define custom icon data.
	 *
	 * @param string                      $url        URL of the icon
	 * @param array                       $size       Array of the size of the icon in pixels. Example: [20,30]
	 * @param array                       $origin     If using an image sprite, the start of the icon from top-left.
	 * @param array                       $anchor     Where the "pin" of the icon should be, example [0,32] for the bottom of a 32px icon
	 * @param array                       $scaledSize How large should the icon appear in px (scaling down image for Retina)
	 *                                                }
	 * @param string                      $mode       Marker position mode: 'address' or 'coordinates'
	 *
	 * @link https://developers.google.com/maps/documentation/javascript/markers Read more on Markers
	 */
	function __construct( $entry, $position_field, $icon = array(), $mode = 'address' ) {
		// get the cache markers class instance
		$this->cache = $GLOBALS['gravityview_maps']->component_instances['Cache_Markers'];

		$this->entry = $entry;

		$this->entry_url = $this->set_entry_url( $entry );

		$this->field = $position_field;

		// generate the marker position (lat/long)

		if ( 'address' === $mode ) {
			$this->address = $this->generate_address( $entry, $position_field );

			$this->position = $this->generate_position_from_address( $entry, $position_field );
		} else {
			$this->position = $this->generate_position_from_coordinates( $entry, $position_field );
		}

		if ( ! empty( $icon ) ) {
			$this->icon = new Icon( $icon[0] );
		}
	}

	/**
	 * Get array of marker data used
	 *
	 * @since 1.5
	 *
	 * @return ?array
	 */
	public function to_array() {
		if ( ! $this->is_valid() ) {
			return null;
		}
		$position = $this->get_position();

		return [
			'entry_id' => $this->get_entry_id(),
			'field_id' => $this->get_field_id(),
			'lat'      => $position[0],
			'long'     => $position[1],
			'icon'     => $this->get_icon( true ),
			'url'      => $this->get_entry_url(),
			'content'  => $this->get_infowindow_content()
		];
	}

	/**
	 * Determines if the marker is valid.
	 * To be valid it needs to have a field, entry and valid position.
	 * Position 0,0 is also invalid, Null Island.
	 *
	 * @since TBD
	 *
	 * @return bool
	 */
	public function is_valid(): bool {
		if ( empty( $this->field ) ) {
			return false;
		}

		if ( empty( $this->entry ) ) {
			return false;
		}

		$position = $this->get_position();

		if ( empty( $position ) ) {
			return false;
		}

		if ( $position instanceof \GravityKit\GravityMaps\Geocoder\Exception\ExceptionInterface ) {
			return false;
		}

		if (  empty( $position[0] ) ) {
			return false;
		}

		if ( empty( $position[1] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the icon attached to the marker.
	 *
	 * @since 1.9 Added $as_array parameter.
	 *
	 * @param bool $as_array Whether to return the icon as an array or as an object.
	 *
	 * @return Icon|array The icon attached to the marker. If $as_array is true, returns an array of the icon data.
	 */
	public function get_icon( $as_array = false ) {
		return ( $as_array && $this->icon ) ? $this->icon->to_array() : $this->icon;
	}

	/**
	 * @param Icon $icon
	 */
	public function set_icon( Icon $icon ) {
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
		if ( ! function_exists( 'gv_entry_link' ) ) {
			$url = '';
		} else {
			$url = gv_entry_link( $entry );
		}

		/**
		 * @filter `gravityview/maps/marker/url` Filter the marker single entry view url
		 * @since  1.4
		 *
		 * @param string $url   Single entry view url
		 * @param array  $entry Gravity Forms entry object
		 */
		$url = apply_filters( 'gravityview/maps/marker/url', $url, $entry );

		return $url;
	}

	/**
	 * Return ID of the field that's used to generate the marker
	 *
	 * @since 1.6
	 *
	 * @return integer
	 */
	public function get_field_id() {
		if ( empty( $this->field ) ) {
			return null;
		}

		if ( is_array( $this->field ) && ! empty( $this->field['id'] ) ) {
			return $this->field['id'];
		}

		if ( ! is_object( $this->field ) ) {
			return null;
		}

		return $this->field->id;
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
	 * Removes default field values from the address array
	 *
	 * @since 1.7
	 *
	 * @param array            $field_value Array of address; [ 1.1, 1.2, 1.3 ... ]
	 * @param GF_Field_Address $field       The current address field
	 *
	 * @return array Address values array with defaults unset
	 */
	private function remove_default_address_inputs( $field_value, $field ) {
		$return_value = (array) $field_value;

		$available_defaults = array(
			$field->id . '.4' => array( rgobj( $field, 'defaultState' ), rgobj( $field, 'defaultProvince' ) ),
			$field->id . '.6' => array( rgobj( $field, 'defaultCountry' ) ),
		);

		foreach ( $available_defaults as $input_id => $defaults ) {

			$input_value = rgar( $field_value, $input_id );

			// In case the defaults aren't set
			$defaults = array_filter( $defaults );

			if ( $defaults && in_array( $input_value, $defaults, true ) ) {
				unset( $return_value[ $input_id ] );
			}
		}

		return $return_value;
	}

	/**
	 * Generate a string address with no line breaks from field
	 *
	 * @param array            $entry GF Entry array
	 * @param GF_Field_Address $field GF Field array
	 *
	 * @return string|array|null Null if field value is empty. Single line address otherwise (Eg: "123 Pleasant Street Example NM 12345 USA"). Could be array if users use non-Address fields to override output.
	 */
	protected function generate_address( $entry, $field ) {
		// Get the address fields as an array (1.3, 1.6, etc.)
		$field_value = GFFormsModel::get_lead_field_value( $entry, $field );

		/**
		 * @filter `gravityview/maps/marker/use-address-defaults`
		 * @since  1.7
		 *
		 * @param bool             $use_default_values Whether to use default values when generating a marker address
		 * @param GF_Field_Address $field              The current Address field
		 */
		$use_default_values = apply_filters( 'gravityview/maps/marker/use-address-default-values', false, $field );

		if ( ! $use_default_values ) {
			$field_value = $this->remove_default_address_inputs( $field_value, $field );
		}

		/**
		 * @filter `gravityview/maps/marker/field-value` Modify the address field value before processing
		 * Useful if you want to prevent
		 *
		 * @param array $entry Gravity Forms entry used for the marker
		 * @param GF_Field_Address Gravity Forms Address field object used for the marker
		 */
		$field_value = apply_filters( 'gravityview/maps/marker/field-value', $field_value, $entry, $field );

		if ( empty( $field_value ) ) {
			return null;
		}

		// Further processing is only required for fields with address type
		if ( 'address' !== $field->type ) {
			return $field_value;
		}

		// Get the text output (without map link)
		$address = GFCommon::get_lead_field_display( $field, $field_value, '', false, 'text' );

		// Replace the new lines with spaces
		$address = str_replace( "\n", ' ', $address );

		// If no address, but defaults are set, use them.
		if ( $use_default_values && '' === $address ) {

			if ( ! empty ( $field->defaultProvince ) ) {
				$address .= $field->defaultProvince;
			} elseif ( ! empty ( $field->defaultState ) ) {
				$address = $field->defaultState;
			}

			if ( ! empty ( $field->defaultCountry ) ) {
				$address .= ' ' . $field->defaultCountry;
			}
		}

		$address = trim( $address );

		/**
		 * @filter `gravityview/maps/marker/address` Filter the address value
		 * @since  1.0.4
		 * @since  1.6
		 *
		 * @param string           $address Address value
		 * @param array            $entry   Gravity Forms entry object
		 * @param GF_Field_Address $field   GF Field array
		 */
		$address = apply_filters( 'gravityview/maps/marker/address', $address, $entry, $field );

		return $address;
	}

	/**
	 * Generate the marker position (Lat & Long) based on an address field
	 *
	 * @param array $entry GF Entry array
	 * @param array $field GF Field array
	 *
	 * @return array|\Geocoder\Exception\ExceptionInterface 0 => Latitude / 1 => Longitude or Exception, if failure
	 */
	protected function generate_position_from_address( $entry, $field ) {
		if ( empty( $this->address ) ) {
			return array();
		}

		$position = $this->cache->get_cache_position( $entry['id'], $field->id );

		// in case position is not saved as entry meta, try to fetch it on a Geocoder service provider
		if ( empty( $position ) ) {

			if ( $has_error = $this->cache->get_cache_error( $entry['id'], $field['id'] ) ) {
				return array();
			}

			$position = $this->fetch_position( $this->address );

			if ( $position instanceof \GravityKit\GravityMaps\Geocoder\Exception\ExceptionInterface ) {
				$this->cache->set_cache_error( $entry['id'], $field['id'], $position, $entry['form_id'] );

				return array();
			}

			$this->cache->set_cache_position( $entry['id'], $field['id'], $position, $entry['form_id'] );
		}

		return $position;
	}

	/**
	 * Geocode an Address to get the coordinates Lat/Long
	 * Uses Geocoder
	 *
	 * @param string|array $address Expect a string of an address, but if users use non-address fields, could be array
	 *
	 * @return array|\Geocoder\Exception\ExceptionInterface
	 */
	protected function fetch_position( $address = '' ) {
		if ( is_array( $address ) ) {
			$address = implode( ' ', $address );
			$address = trim( $address );
		}

		try {
			/** @see Geocoding::geocode() */
			return $GLOBALS['gravityview_maps']->component_instances['Geocoding']->geocode( $address );
		} catch ( Exception $exception ) {
			return $exception;
		}
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

		if ( ! empty( $fields ) && is_array( $fields ) ) {
			foreach ( $fields as $field ) {
				$position[] = GFFormsModel::get_lead_field_value( $entry, $field );
			}
		}

		return $position;
	}
}
