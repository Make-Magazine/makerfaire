<?php
/**
 * Main Geocoder class.
 *
 * @package gravityforms-geolocation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gravity Geolocation base geocoder class.
 *
 * Can be extended to work with different geocoding APIs.
 *
 * @Since 3.0
 *
 * @Author Eyal Fitoussi
 */
class GFGEO_Geocoder {

	/**
	 * Provider.
	 *
	 * @var string
	 */
	public $provider = 'google_maps';

	/**
	 * Geocode API URL.
	 *
	 * @var string
	 */
	public $geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json';

	/**
	 * Reverse geocode API URl.
	 *
	 * @var string
	 */
	public $reverse_geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json';

	/**
	 * Geocoding type.
	 *
	 * @var string
	 */
	public $type = 'geocode';

	/**
	 * Address or coords to geocode.
	 *
	 * @var string
	 */
	public $location = '';

	/**
	 * Endpoint parameters.
	 *
	 * @var array
	 */
	public $params = array();

	/**
	 * Default location fields to return.
	 *
	 * @var array
	 */
	public $location_fields = array(
		'street_number'     => '',
		'street_name'       => '',
		'street'            => '',
		'premise'           => '',
		'neighborhood'      => '',
		'city'              => '',
		'county'            => '',
		'region_name'       => '',
		'region_code'       => '',
		'postcode'          => '',
		'country_name'      => '',
		'country_code'      => '',
		'address'           => '',
		'formatted_address' => '',
		'lat'               => '', // to support older versions.
		'lng'               => '', // to support older versions.
		'latitude'          => '',
		'longitude'         => '',
		'place_id'          => '',
	);

	/**
	 * [__construct description]
	 *
	 * @param string $provider geocoding provider.
	 */
	public function __construct( $provider = '' ) {

		$this->provider = ! empty( $provider ) ? $provider : $this->provider;
		$this->params   = array(
			'region'   => 'us',
			'language' => 'en',
		);
	}

	/**
	 * Get endpoint parameters.
	 *
	 * @param array $options options to pass to the geocoder.
	 *
	 * @return options.
	 */
	public function get_endpoint_params( $options ) {
		return $options;
	}

	/**
	 * Prepare address for the geocoder. Remove unwanted characters.
	 *
	 * @param  string $raw_data string.
	 *
	 * @return [type]           [description]
	 */
	public function parse_raw_data( $raw_data ) {

		$characters = array(
			' ' => '+',
			',' => '',
			'?' => '',
			'&' => '',
			'=' => '',
			'#' => '',
		);

		// Clean up address from invalid characters.
		$invalid_chars = apply_filters(
			'gfgeo_geocoder_invalid_characters',
			$characters,
			$this,
			$raw_data,
		);

		return trim( strtolower( str_replace( array_keys( $invalid_chars ), array_values( $invalid_chars ), $raw_data ) ) );
	}

	/**
	 * Check if the location provided is address or coordiantes.
	 *
	 * We need to know if to geocode or reverse geocoder.
	 *
	 * @param  mixed $raw_data string of address || array of coords.
	 *
	 * @return [type]           [description]
	 */
	public function verify_data( $raw_data = '' ) {

		if ( empty( $raw_data ) && ! empty( $this->location ) ) {
			$raw_data = $this->location;
		}

		$raw_data = apply_filters( 'gfgeo_geocoder_raw_data', $raw_data );

		// if data is array, then it should be coordinates.
		if ( is_array( $raw_data ) ) {

			// convert to lat,lng comma separated.
			$location   = implode( ',', $raw_data );
			$this->type = 'reverse_geocode';

			// if not array, then it should be an address.
		} else {

			$location   = $this->parse_raw_data( $raw_data );
			$this->type = 'geocode';
		}

		return $location;
	}

	/**
	 * Get endpoint URL.
	 *
	 * @return [type] [description]
	 */
	public function get_endpoint_url() {

		$url_type = $this->type . '_url';

		// can modify the URL params.
		$args = apply_filters(
			'gfgeo_geocoder_endpoint_args',
			array(
				'url_base'   => $this->$url_type . '?',
				'url_params' => $this->params,
			),
			$this
		);

		// deprecated. Will be removed in the future.
		$args = apply_filters( 'gfgeo_geocoder_endpoint_url', $args, $this );

		// remove any extra spaces from parameters.
		$params = array_map( 'trim', $args['url_params'] );
		$url    = $args['url_base'];

		/**
		 * If region exists, lets place it at the beggining of the array.
		 *
		 * We do this to prevnt the &region renders as ®ion and break the URL.
		 *
		 * This solution should work until we find a less hacky one.
		 */
		if ( array_key_exists( 'region', $params ) ) {

			$url .= 'region=' . $params['region'] . '&';

			unset( $params['region'] );
		}

		return $url . http_build_query( $params );
	}

	/**
	 * Get the geolocation data using a child class.
	 *
	 * @param  array $geocoded_data the geocoded data.
	 *
	 * @return [type]                [description]
	 */
	public function get_data( $geocoded_data ) {
		return $geocoded_data;
	}

	/**
	 * Geocode function.
	 *
	 * @param  mixed   $raw_data       string of address || array of coords.
	 *
	 * @param  array   $options       geocoder options.
	 *
	 * @param  boolean $force_refresh [description].
	 *
	 * @return [type]                 [description]
	 */
	public function geocode( $raw_data = '', $options = array(), $force_refresh = false ) {

		// Verify location.
		$this->location = $this->verify_data( $raw_data );

		// abort if no location provided.
		if ( empty( $this->location ) ) {

			$status = __( 'Location is missing.', 'gfgeo' );

			return $this->failed( $status, array() );
		}

		// Get geocoder default options.
		$this->params = $this->get_endpoint_params( $this->params );

		// Merge provided options.
		if ( is_array( $options ) ) {
			$this->params = array_merge( $this->params, $options );
		}

		// look for geocoded location in cache.
		$address_hash    = md5( $this->location );
		$location_output = get_transient( 'gfgeo_geocoded_' . $address_hash );
		$location_output = apply_filters( 'gfgeo_transient_location_output', $location_output, $address_hash );
		$response        = array();

		// if no location found in cache or if forced referesh try to geocode.
		if ( true === $force_refresh || false === $location_output ) {

			// get data from the provider.
			$result = wp_remote_get( $this->get_endpoint_url() );

			// abort if remote connection failed.
			if ( is_wp_error( $result ) ) {
				return $this->failed( $result->get_error_message(), $result );
			}

			// look for geocoded data.
			$geocoded_data = wp_remote_retrieve_body( $result );

			// abort if no data found.
			if ( is_wp_error( $geocoded_data ) ) {

				$status = __( 'Geocoding failed', 'gfgeo' );

				return $this->failed( $status, $geocoded_data );
			}

			// if response successful.
			if ( 200 === wp_remote_retrieve_response_code( $result ) ) {

				// decode the data.
				$geocoded_data = json_decode( $geocoded_data );

				// if geocoding success.
				if ( ! empty( $geocoded_data ) ) {

					// get geocoded data. Return either location fields or error message.
					$response = $this->get_data( $geocoded_data );

					$response['data'] = $geocoded_data;

					// If location was found.
					if ( $response['geocoded'] ) {

						// add missing address field.
						if ( 'reverse_geocode' === $this->type ) {
							$response['result']['address'] = $response['result']['formatted_address'];
						} else {
							$response['result']['address'] = sanitize_text_field( urldecode( $this->location ) );
						}

						// hook after geocoding.
						do_action( 'gfgeo_geocoded_location', $response['result'], $response, $address_hash );

						// Modify cache expiration time.
						$expiration = apply_filters( 'gfgeo_geocoder_transient_expiration', DAY_IN_SECONDS * 7 );

						// cache location.
						set_transient( 'gfgeo_geocoded_' . $address_hash, $response['result'], $expiration );

						// we need to pass the output via $location_output.
						$location_output = $response['result'];

						// can run custom function on sucess.
						$this->success( $location_output, $response );

						// return error message.
					} else {
						return $this->failed( $response['result'], $geocoded_data );
					}

					// If geocode failed display errors.
				} else {

					$status = __( 'Location data was not found.', 'gfgeo' );

					return $this->failed( $status, $geocoded_data );
				}
			} else {

				$status = __( 'Unable to contact the API service or failed geocoding.', 'gfgeo' );

				return $this->failed( $status, $geocoded_data );
			}
		}

		return apply_filters( 'gfgeo_geocoded_location_output', $location_output, $response, $address_hash );
	}

	/**
	 * Success call back function.
	 *
	 * Can be used in class child.
	 *
	 * @param  [type] $location_output [description].
	 *
	 * @param  [type] $response        [description].
	 */
	public function success( $location_output, $response ) {}

	/**
	 * Location failed function.
	 *
	 * @param  string $status error message.
	 *
	 * @param  array  $data   geocoder data.
	 *
	 * @return [type]         [description]
	 */
	public function failed( $status, $data ) {

		// generate warning showing the error message when geocoder fails.
		if ( 'ZERO_RESULTS' !== $status ) {

			$message = $status;

			if ( ! empty( $data->error_message ) ) {
				$message .= ' - ' . $data->error_message;
			}

			trigger_error( 'Gravity Geolocation geocoder failed. Error : ' . $message, E_USER_NOTICE );
		}

		return array(
			'error' => $status,
			'data'  => $data,
		);
	}
}

/**
 * Google Maps Geocoder.
 *
 * @since 3.1.
 *
 * @author Eyal Fitoussi.
 */
class GFGEO_Google_Maps_Geocoder extends GFGEO_Geocoder {

	/**
	 * Provider.
	 *
	 * @var string
	 */
	public $provider = 'google_maps';

	/**
	 * Geocode API URL.
	 *
	 * @var string
	 */
	public $geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json';

	/**
	 * Reverse geocode API URl.
	 *
	 * @var string
	 */
	public $reverse_geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json';

	/**
	 * Get endpoint parameters.
	 *
	 * @param array $options geocoder options.
	 *
	 * @return [type] [description]
	 */
	public function get_endpoint_params( $options ) {

		$location = ( 'reverse_geocode' === $this->type ) ? 'latlng' : 'address';
		$params   = array(
			$location  => $this->location,
			'region'   => trim( GFGEO_GOOGLE_MAPS_COUNTRY ),
			'language' => trim( GFGEO_GOOGLE_MAPS_LANGUAGE ),
			'key'      => trim( GFGEO_GOOGLE_MAPS_SERVER_API_KEY ),
		);

		return $params;
	}

	/**
	 * Get endpoint URL.
	 *
	 * @return [type] [description]
	 */
	public function get_endpoint_url() {

		$china = '';

		if ( '' !== $china ) {
			$this->geocode_url = 'https://maps.google.cn/maps/api/geocode/json';
		}
		return parent::get_endpoint_url();
	}

	/**
	 * Get result data.
	 *
	 * @param  object $geocoded_data geocolocation data.
	 *
	 * @return [type]                [description]
	 */
	public function get_data( $geocoded_data ) {

		// if failed geocoding return error message.
		if ( 'OK' !== $geocoded_data->status ) {
			return array(
				'geocoded' => false,
				'result'   => $geocoded_data->status,
			);

			// Otherwise, return location data.
		} else {
			return array(
				'geocoded' => true,
				'result'   => $this->get_location_fields( $geocoded_data, $this->location_fields ),
			);
		}
	}

	/**
	 * Collect location fields.
	 *
	 * @param  object $geocoded_data geolocation data.
	 *
	 * @param  array  $location      $location data.
	 *
	 * @return [type]                [description]
	 */
	public function get_location_fields( $geocoded_data = array(), $location = array() ) {

		// default values.
		$location['formatted_address'] = sanitize_text_field( $geocoded_data->results[0]->formatted_address );
		$location['lat']               = sanitize_text_field( $geocoded_data->results[0]->geometry->location->lat );
		$location['lng']               = sanitize_text_field( $geocoded_data->results[0]->geometry->location->lng );
		$location['latitude']          = sanitize_text_field( $geocoded_data->results[0]->geometry->location->lat );
		$location['longitude']         = sanitize_text_field( $geocoded_data->results[0]->geometry->location->lng );
		$location['place_id']          = ! empty( $geocoded_data->results[0]->place_id ) ? sanitize_text_field( $geocoded_data->results[0]->place_id ) : '';

		$address_componenets = $geocoded_data->results[0]->address_components;

		// loop through address fields and collect data.
		foreach ( $address_componenets as $geocoded_data ) {

			switch ( $geocoded_data->types[0] ) {

				// street number.
				case 'street_number':
					$location['street_number'] = sanitize_text_field( $geocoded_data->long_name );
					$location['street']        = sanitize_text_field( $geocoded_data->long_name );
					break;

				// street name and street.
				case 'route':
					// street name.
					$location['street_name'] = sanitize_text_field( $geocoded_data->long_name );

					// street ( number + name ).
					if ( ! empty( $location['street_number'] ) ) {

						$location['street'] = $location['street_number'] . ' ' . $location['street_name'];

					} else {

						$location['street'] = $location['street_name'];
					}
					break;

				// premise.
				case 'subpremise':
					$location['premise'] = sanitize_text_field( $geocoded_data->long_name );
					break;

				// neigborhood.
				case 'neighborhood':
					$location['neighborhood'] = sanitize_text_field( $geocoded_data->long_name );
					break;

				// city.
				case 'sublocality_level_1':
				case 'locality':
				case 'postal_town':
					$location['city'] = sanitize_text_field( $geocoded_data->long_name );
					break;

				// county.
				case 'administrative_area_level_2':
				case 'political':
					$location['county'] = sanitize_text_field( $geocoded_data->long_name );
					break;

				// region / state.
				case 'administrative_area_level_1':
					$location['region_code'] = sanitize_text_field( $geocoded_data->short_name );
					$location['region_name'] = sanitize_text_field( $geocoded_data->long_name );
					break;

				// postal code.
				case 'postal_code':
					$location['postcode'] = sanitize_text_field( $geocoded_data->long_name );
					break;

				// country.
				case 'country':
					$location['country_code'] = sanitize_text_field( $geocoded_data->short_name );
					$location['country_name'] = sanitize_text_field( $geocoded_data->long_name );
					break;
			}
		}

		return $location;
	}

	/**
	 * Return error message.
	 *
	 * @param  string $status status code.
	 *
	 * @return [type]         [description]
	 */
	public function get_error_message( $status ) {

		if ( 'ZERO_RESULTS' === $status ) {
			return array(
				'geocoded' => false,
				'error'    => __( 'The data entered could not be geocoded.', 'gfgeo' ),
			);
		} elseif ( 'INVALID_REQUEST' === $status ) {
			return array(
				'geocoded' => false,
				'error'    => __( 'Invalid request. Did you enter an address?', 'gfgeo' ),
			);
		} elseif ( 'OVER_QUERY_LIMIT' === $status ) {
			return array(
				'geocoded' => false,
				'error'    => __( 'Something went wrong while retrieving your location.', 'gfgeo' ) . '<span style="display:none">OVER_QUERY_LIMIT</span>',
			);
		} else {
			return array(
				'geocoded' => false,
				'error'    => __( 'Something went wrong while retrieving your location.', 'gfgeo' ),
			);
		}
	}
}

/**
 *
 * Gravity Geolocation Geocoder.
 *
 * @since 3.0
 *
 * @author Eyal Fitoussi
 *
 * @param  string||array $raw_data can be address as a string or coords as of array( 'latitude','longitude' ).
 *
 * @param  boolean       $force_refresh true to ignore data saved in cache.
 *
 * @return array geocoded data.
 */
function gfgeo_geocoder( $raw_data = '', $force_refresh = false ) {

	// get provider.
	$provider = 'google_maps';

	// Generate class name.
	$class_name = 'GFGEO_' . $provider . '_Geocoder';

	// verify that provider geocoding exists. Otherwise, use Google Maps as default.
	if ( ! class_exists( 'GFGEO_' . $provider . '_Geocoder' ) ) {
		$class_name = 'GFGEO_Google_Maps_Geoocoder';
	}

	$geocoder = new $class_name( $provider );

	return $geocoder->geocode( $raw_data, array(), $force_refresh );
}
