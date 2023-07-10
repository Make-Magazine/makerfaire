<?php

namespace GravityKit\GravityMaps;

use Exception;
use GFCommon;
use GFFormsModel;
use GF_Field_Address;
use GF_Field;

class Marker {
	/**
	 * Store the address mode slug.
	 *
	 * @since 2.2
	 *
	 * @var string
	 */
	public const MODE_ADDRESS = 'address';

	/**
	 * Store the coordinates mode slug.
	 *
	 * @since 2.2
	 *
	 * @var string
	 */
	public const MODE_COORDINATES = 'coordinates';

	/**
	 * Marker mode. Can be 'address' or 'coordinates'.
	 *
	 * @since 2.2
	 *
	 * @var string
	 */
	protected $mode = self::MODE_ADDRESS;

	/**
	 * @var null|Icon
	 */
	protected $icon = null;

	/**
	 * Gravity Forms entry array
	 *
	 * @var array
	 */
	protected $entry = [];

	/**
	 * Which field is used to calculate the address.
	 *
	 * @since 2.2
	 *
	 * @var ?GF_Field_Address
	 */
	protected $address_field = null;

	/**
	 * Which field is used to get the latitude.
	 *
	 * @since 2.2
	 *
	 * @var ?GF_Field
	 */
	protected $lat_field = null;

	/**
	 * Which field is used to get the longitude.
	 *
	 * @since 2.2
	 *
	 * @var ?GF_Field
	 */
	protected $long_field = null;

	/**
	 * Full address without any line breaks or spaces
	 *
	 * @var string
	 */
	protected $address = null;

	/**
	 * Marker position - set of Latitude / Longitude
	 *
	 * @var ?array 0 => Latitude / 1 => Longitude
	 */
	protected $position = null;

	/**
	 * Marker Entry URL
	 *
	 * @var array
	 */
	protected $entry_url = null;

	/**
	 * Marker Info Window content
	 *
	 * @since 1.6
	 *
	 * @var string
	 */
	protected $infowindow = null;

	/**
	 *
	 * @var Cache_Markers instance
	 */
	protected $cache = null;

	/**
	 * Gravity Forms address field object
	 *
	 * @deprecated TBD
	 *
	 * @var array
	 */
	protected $field = null;

	/**
	 * @link https://developers.google.com/maps/documentation/javascript/markers Read more on Markers
	 *
	 * @param array                       $entry
	 * @param GF_Field_Address|GF_Field[] $position_field GF Field used to calculate the address, or array of fields with position data, used when $mode is 'coordinates'
	 * @param array                       $icon           {
	 *                                                    Optional. Define custom icon data.
	 *
	 * @type string                       $url            URL of the icon
	 * @type array                        $size           Array of the size of the icon in pixels. Example: [20,30]
	 * @type array                        $origin         If using an image sprite, the start of the icon from top-left.
	 * @type array                        $anchor         Where the "pin" of the icon should be, example [0,32] for the bottom of a 32px icon
	 * @type array                        $scaledSize     How large should the icon appear in px (scaling down image for Retina)
	 *                                                    }
	 *
	 *
	 */
	public function __construct( $__deprecated_one = null, $__deprecated_two = null, $__deprecated_three = null, $__deprecated_four = null ) {
		if ( ! empty( $__deprecated_one ) ) {
			_deprecated_argument( __METHOD__, 'TBD', '$entry was deprecated in please use the factory methods `from_address_field` and `from_coordinate_fields`' );
		}

		if ( ! empty( $__deprecated_two ) ) {
			_deprecated_argument( __METHOD__, 'TBD', '$position_field was deprecated in please use the factory methods `from_address_field` and `from_coordinate_fields`' );
		}

		if ( ! empty( $__deprecated_three ) ) {
			_deprecated_argument( __METHOD__, 'TBD', '$icon was deprecated in please use the factory methods `from_address_field` and `from_coordinate_fields`' );
		}

		if ( ! empty( $__deprecated_four ) ) {
			_deprecated_argument( __METHOD__, 'TBD', '$mode was deprecated in please use the factory methods `from_address_field` and `from_coordinate_fields`' );
		}
		// Here just to prevent backwards compatibility issues. No longer used.
	}

	/**
	 * Create a new Marker instance from an address field.
	 *
	 * @since 2.2
	 *
	 * @param array    $entry
	 * @param GF_Field_Address $field
	 * @param array    $icon
	 *
	 * @return self
	 */
	public static function from_address_field( $entry, GF_Field_Address $field, array $icon = [] ): self {
		$marker       = new self;
		$marker->mode = static::MODE_ADDRESS;

		$marker->set_cache();
		$marker->set_entry( $entry );
		$marker->set_address_field( $field );
		$marker->generate_address();
		$marker->generate_position_from_address();

		if ( ! empty( $icon ) ) {
			$marker->set_icon( new Icon( $icon[0] ) );
		}

		return $marker;
	}

	/**
	 * Create a new Marker instance from a set of coordinates fields.
	 *
	 * @since 2.2
	 *
	 * @param array    $entry
	 * @param GF_Field $lat_field
	 * @param GF_Field $long_field
	 * @param array    $icon
	 *
	 * @return self
	 */
	public static function from_coordinate_fields( $entry, GF_Field $lat_field, GF_Field $long_field, array $icon = [] ): self {
		$marker       = new self;
		$marker->mode = static::MODE_COORDINATES;

		$marker->set_cache();
		$marker->set_entry( $entry );
		$marker->set_lat_field( $lat_field );
		$marker->set_long_field( $long_field );

		$marker->generate_position_from_coordinates();

		if ( ! empty( $icon ) ) {
			$marker->set_icon( new Icon( $icon[0] ) );
		}

		return $marker;
	}

	/**
	 * Configures the Cache for this marker.
	 *
	 * @since 2.2
	 *
	 * @param mixed $cache
	 */
	protected function set_cache( $cache = null ): void {
		if ( null === $cache ) {
			$cache = $GLOBALS['gravityview_maps']->component_instances['Cache_Markers'];
		}

		// get the cache markers class instance
		$this->cache = $cache;
	}

	/**
	 * Configure the address field used to calculate the coordinates of this marker.
	 *
	 * @since 2.2
	 *
	 * @param GF_Field_Address $field
	 */
	public function set_address_field( GF_Field_Address $field ): void {
		$this->address_field = $field;
	}

	/**
	 * Get the address field used to calculate the coordinates of this marker.
	 *
	 * @since 2.2
	 *
	 * @return GF_Field_Address
	 */
	public function get_address_field(): GF_Field_Address {
		return $this->address_field;
	}

	/**
	 * Set the Latitude field used to calculate the coordinates of this marker.
	 *
	 * @since 2.2
	 *
	 * @param GF_Field $field
	 */
	public function set_lat_field( GF_Field $field ): void {
		$this->lat_field = $field;
	}

	/**
	 * Gets the Latitude field used to calculate the coordinates of this marker.
	 *
	 * @since 2.2
	 *
	 * @return GF_Field
	 */
	public function get_lat_field(): GF_Field {
		return $this->lat_field;
	}

	/**
	 * Set the Longitude field used to calculate the coordinates of this marker.
	 *
	 * @since 2.2
	 *
	 * @param GF_Field $field
	 */
	public function set_long_field( GF_Field $field ): void {
		$this->long_field = $field;
	}

	/**
	 * Gets the Longitude field used to calculate the coordinates of this marker.
	 *
	 * @since 2.2
	 *
	 * @return GF_Field
	 */
	public function get_long_field(): GF_Field {
		return $this->long_field;
	}

	/**
	 * Get array of marker data used
	 *
	 * @since 1.5
	 *
	 * @return ?array
	 */
	public function to_array(): ?array {
		if ( ! $this->is_valid() ) {
			return null;
		}
		$position = $this->get_position();

		$data = [
			'mode' => $this->mode,
			'entry_id' => $this->get_entry_id(),
			'address_field_id' => null,
			'lat_field_id' => null,
			'long_field_id' => null,
			'lat' => $position[0],
			'long' => $position[1],
			'icon' => $this->get_icon( true ),
			'url' => $this->get_entry_url(),
			'content' => $this->get_infowindow_content(),
		];

		if ( static::MODE_ADDRESS === $this->mode ) {
			$data['address_field_id'] = $this->get_address_field()->id;
		}

		if ( static::MODE_COORDINATES === $this->mode ) {
			$data['lat_field_id']  = $this->get_lat_field()->id;
			$data['long_field_id'] = $this->get_long_field()->id;
		}

		return $data;
	}

	/**
	 * Determines if the marker is valid.
	 * To be valid it needs to have a field, entry and valid position.
	 * Position 0,0 is also invalid, Null Island.
	 *
	 * @since 2.2
	 *
	 * @return bool
	 */
	public function is_valid(): bool {
		if ( in_array( $this->mode, [] ) )

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

		if ( empty( $position[0] ) ) {
			return false;
		}

		if ( empty( $position[1] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Gets a unique ID for this marker.
	 *
	 * @since 2.2
	 *
	 * @return string
	 */
	public function get_id(): string {
		if ( $this->mode === static::MODE_ADDRESS ) {
			$id = $this->get_address_field()->id;
		} elseif ( $this->mode === static::MODE_COORDINATES ) {
			$id = $this->get_lat_field()->id . '-' . $this->get_long_field()->id;
		} else {
			$id = substr( md5( wp_generate_uuid4() ), 0, 8 );
		}
		return sprintf( '%s-%s', $this->get_entry_id(), $id );
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
		$this->set_entry_url();
	}

	/**
	 * Gets the Entry ID of the marker.
	 *
	 * @since 2.2
	 *
	 * @return mixed
	 */
	public function get_entry_id() {
		return $this->entry['id'] ?? null;
	}

	/**
	 * @return string
	 */
	public function get_address() {
		return $this->address;
	}

	/**
	 *
	 * @since 1.0.4
	 * @since 2.2 Actually being used and now uses the filter.
	 *
	 * @param string $address
	 */
	public function set_address( string $address ): void {
		/**
		 * @filter `gravityview/maps/marker/address` Filter the address value.
		 *
		 * @since  1.0.4
		 * @since  1.6
		 * @since  TBD Pass $marker object as the fourth parameter.
		 *
		 * @param string           $address Address value.
		 * @param array            $entry   Gravity Forms entry object.
		 * @param GF_Field_Address $field   GF Field array.
		 * @param Marker           $marker  Marker object.
		 */
		$this->address = apply_filters( 'gravityview/maps/marker/address', $address, $this->get_entry(), $this->get_address_field(), $this );
	}

	/**
	 * @return ?array
	 */
	public function get_position(): ?array {
		return $this->position;
	}

	/**
	 * @param ?array $position
	 */
	public function set_position( ?array $position ): void {
		$this->position = $position;
	}

	/**
	 * @return array|string
	 */
	public function get_entry_url() {
		return $this->entry_url;
	}

	/**
	 * Set the entry url.
	 *
	 * @since 2.2 Removed $entry parameter, uses the current entry.
	 *
	 * @param null $__deprecated Deprecated parameter.
	 *
	 * @return void
	 */
	public function set_entry_url( $__deprecated = null ): void {
		if ( ! empty( $__deprecated ) ) {
			_deprecated_argument( __METHOD__, 'TBD', 'The entry url is now based on the current entry.' );
		}

		if ( ! function_exists( 'gv_entry_link' ) ) {
			$url = '';
		} else {
			$url = gv_entry_link( $this->get_entry() );
		}

		/**
		 * @filter `gravityview/maps/marker/url` Filter the marker single entry view url
		 *
		 * @since  1.4
		 * @since  TBD Added `$marker` parameter.
		 *
		 * @param string $url    Single entry view url
		 * @param array  $entry  Gravity Forms entry object
		 * @param Marker $marker Marker object
		 */
		$url = apply_filters( 'gravityview/maps/marker/url', $url, $this->get_entry(), $this );

		if ( is_array( $url ) ) {
			$url = reset( $url );
		}

		// Cast as string to avoid possible errors.
		$this->entry_url = (string) $url;
	}

	/**
	 * Return ID of the field that's used to generate the marker
	 *
	 * @since      1.6
	 * @deprecated TBD Deprecated in favor of `get_id()`
	 *
	 * @return integer
	 */
	public function get_field_id() {
		return $this->get_id();
	}

	/**
	 * Sets the infowindow HTML Content to be used by this marker.
	 *
	 * @since 1.6
	 *
	 * @param ?string $content
	 *
	 * @return void
	 */
	public function set_infowindow_content( ?string $content ): void {
		$this->infowindow = $content;
	}

	/**
	 * Gets the infowindow HTML Content to be used by this marker.
	 *
	 * @since 1.6
	 *
	 * @return ?string
	 */
	public function get_infowindow_content(): ?string {
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

		$available_defaults = [
			$field->id . '.4' => [ rgobj( $field, 'defaultState' ), rgobj( $field, 'defaultProvince' ) ],
			$field->id . '.6' => [ rgobj( $field, 'defaultCountry' ) ],
		];

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
	 * @since 2.2 Deprecated both params;
	 *
	 * @param null $__deprecated_one Deprecated.
	 * @param null $__deprecated_two Deprecated.
	 *
	 * @return void
	 */
	protected function generate_address( $__deprecated_one = null, $__deprecated_two = null ): void {
		if ( ! empty( $__deprecated_one ) ) {
			_deprecated_argument( __METHOD__, 'TBD', '$entry was deprecated in please use the factory methods `from_address_field` and `from_coordinate_fields`' );
		}

		if ( ! empty( $__deprecated_two ) ) {
			_deprecated_argument( __METHOD__, 'TBD', '$position_field was deprecated in please use the factory methods `from_address_field` and `from_coordinate_fields`' );
		}

		$entry = $this->get_entry();
		$field = $this->get_address_field();

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
		 * @param mixed            $field_value The address field value.
		 * @param array            $entry       Gravity Forms entry used for the marker
		 * @param GF_Field_Address $field       Gravity Forms Address field object used for the marker
		 */
		$field_value = apply_filters( 'gravityview/maps/marker/field-value', $field_value, $entry, $field );

		if ( empty( $field_value ) ) {
			return;
		}

		// Further processing is only required for fields with address type
		if ( 'address' !== $field->type ) {
			$this->set_address( $field_value );
			return;
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

		$this->set_address( $address );
	}

	/**
	 * Generate the marker position (Lat & Long) based on an address field
	 *
	 * @since 2.2 Deprecated both params;
	 *
	 * @param null $__deprecated_one Deprecated.
	 * @param null $__deprecated_two Deprecated.
	 *
	 * @return void
	 */
	protected function generate_position_from_address( $__deprecated_one = null, $__deprecated_two = null ): void {
		if ( ! empty( $__deprecated_one ) ) {
			_deprecated_argument( __METHOD__, 'TBD', '$entry was deprecated in please use the factory methods `from_address_field` and `from_coordinate_fields`' );
		}

		if ( ! empty( $__deprecated_two ) ) {
			_deprecated_argument( __METHOD__, 'TBD', '$field was deprecated in please use the factory methods `from_address_field` and `from_coordinate_fields`' );
		}

		$entry   = $this->get_entry();
		$field   = $this->get_address_field();
		$address = $this->get_address();

		if ( empty( $address ) ) {
			return;
		}

		$position = $this->cache->get_cache_position( $entry['id'], $field->id );

		// in case position is not saved as entry meta, try to fetch it on a Geocoder service provider
		if ( empty( $position ) ) {

			if ( $has_error = $this->cache->get_cache_error( $entry['id'], $field['id'] ) ) {
				return;
			}

			$position = $this->fetch_position( $address );

			if ( $position instanceof \GravityKit\GravityMaps\Geocoder\Exception\ExceptionInterface ) {
				$this->cache->set_cache_error( $entry['id'], $field['id'], $position, $entry['form_id'] );
				return;
			}

			$this->cache->set_cache_position( $entry['id'], $field['id'], $position, $entry['form_id'] );
		}

		$this->set_position( $position );
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
	 * E.g.: $position = [ 0 => Latitude, 1 => Longitude ];
	 *
	 *
	 * @since 2.2 Deprecated both params, use the entry object instead and the get_lat_field() and get_long_field() methods.
	 *
	 * @param null $__deprecated_one Deprecated.
	 * @param null $__deprecated_two Deprecated.
	 *
	 * @return void
	 */
	protected function generate_position_from_coordinates( $__deprecated_one = null, $__deprecated_two = null ): void {
		if ( ! empty( $__deprecated_one ) ) {
			_deprecated_argument( __METHOD__, 'TBD', '$entry was deprecated in please use the factory methods `from_address_field` and `from_coordinate_fields`' );
		}

		if ( ! empty( $__deprecated_two ) ) {
			_deprecated_argument( __METHOD__, 'TBD', '$field was deprecated in please use the factory methods `from_address_field` and `from_coordinate_fields`' );
		}

		$this->position = [
			GFFormsModel::get_lead_field_value( $this->get_entry(), $this->get_lat_field() ),
			GFFormsModel::get_lead_field_value( $this->get_entry(), $this->get_long_field() ),
		];
	}
}
