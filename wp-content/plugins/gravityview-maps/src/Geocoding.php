<?php

namespace GravityKit\GravityMaps;

use Exception;
use GravityKit\GravityMaps\Foundation\Helpers\Arr;
use GravityKitFoundation;

/**
 * Class responsible for address geocoding
 *
 * @since 1.0.0
 * @uses  \GravityKit\GravityMaps\Geocoder\Geocoder
 *
 */
class Geocoding extends Component {
	/**
	 * @var \HTTP_Adapter
	 */
	protected $adapter = null;

	/**
	 * @var \GravityKit\GravityMaps\Geocoder\Geocoder
	 */
	protected $geocoder = null;

	function load() {
		try {
			$this->adapter  = $this->set_http_adapter();
			$this->geocoder = $this->set_geocoder();
			$this->set_providers();
		} catch ( Exception $e ) {
			do_action( 'gravityview_log_error', '[GravityView Maps] Failed during geocoder load. Error message:', $e->getMessage() );
		}
	}

	/**
	 * Configure the http settings used by the Geocoder
	 *
	 * @return HTTP_Adapter
	 */
	public function set_http_adapter() {
		return new HTTP_Adapter();
	}

	/**
	 * Returns the Geocoder instance
	 * @since 1.8
	 * @return \GravityKit\GravityMaps\Geocoder\Geocoder
	 */
	public function get_geocoder() {
		return $this->geocoder;
	}

	public function set_geocoder() {
		return new \GravityKit\GravityMaps\Geocoder\Geocoder();
	}

	public function set_providers() {
		/** @var Settings $settings */
		$settings = $this->loader->component_instances['Settings'];

		$keys = $settings->get_providers_api_keys();

		/**
		 * @filter `gravityview/maps/geocoding/providers/locale` Sets the locale for the geocoding provider. [Default: none; provider will decide.]
		 * @since  1.0
		 *
		 * @param null|string $locale A locale (optional). [Default: null]
		 */
		$locale = apply_filters( 'gravityview/maps/geocoding/providers/locale', null );

		/**
		 * @filter `gravityview/maps/geocoding/providers/region` Sets the region for the geocoding provider. [Default: none; provider will decide.]
		 * @since  1.0
		 *
		 * @param null|string $region Region biasing (optional). [Default: null]
		 */
		$region = apply_filters( 'gravityview/maps/geocoding/providers/region', null );

		$providers = array();

		// Google Maps for Work Provider
		if ( ! empty( $keys['google_maps/business/client_id'] ) && ! empty( $keys['google_maps/business/key'] ) ) {
			$providers[] = new \GravityKit\GravityMaps\Geocoder\Provider\GoogleMapsBusinessProvider(
				$this->adapter,
				$keys['google_maps/business/client_id'],
				$keys['google_maps/business/key'],
				$locale,
				$region,
				true
			);
		} elseif ( apply_filters( 'gravityview/maps/geocoding/providers/googlemaps', true ) ) {
			// If set, use unrestricted key for geocoding.
			$key = ! empty( Arr::get( $settings->get_providers_api_keys(), 'google_maps/key_unrestricted' ) )
				? Arr::get( $settings->get_providers_api_keys(), 'google_maps/key_unrestricted' )
				: Arr::get( $settings->get_providers_api_keys(), 'google_maps/key' );

			// Google Maps Provider (even without key)
			$providers[] = new \GravityKit\GravityMaps\Geocoder\Provider\GoogleMapsProvider(
				$this->adapter,
				$locale,
				$region,
				true,
				$key
			);

			unset( $key );
		}

		// Bing Maps Provider
		if ( ! empty( $keys['bingmaps-api-key'] ) ) {
			$providers[] = new \GravityKit\GravityMaps\Geocoder\Provider\BingMapsProvider(
				$this->adapter,
				$keys['bingmaps-api-key'],
				$locale
			);

		}

		// MapQuest Provider
		if ( ! empty( $keys['mapquest-api-key'] ) ) {
			$providers[] = new \GravityKit\GravityMaps\Geocoder\Provider\MapQuestProvider(
				$this->adapter,
				$keys['mapquest-api-key'],
				$locale,
				/**
				 * @filter `gravityview/maps/geocoding/mapquest/licensed_data`
				 *
				 * @param boolean $licensed_data True to use MapQuest's licensed endpoints, default is false to use the open endpoints (optional).
				 */
				apply_filters( 'gravityview/maps/geocoding/mapquest/licensed_data', false )
			);
		}

		// OpenStreetMap Provider
		if ( apply_filters( 'gravityview/maps/geocoding/providers/openstreetmap', true ) ) {
			$providers[] = new \GravityKit\GravityMaps\Geocoder\Provider\OpenStreetMapProvider(
				$this->adapter,
				$locale
			);
		}

		if ( empty( $providers ) ) {
			do_action( 'gravityview_log_error', '[GravityView Maps] Not possible to use Geocoding without providers' );

			return;
		}

		$this->geocoder->registerProvider(
			new \GravityKit\GravityMaps\Geocoder\Provider\ChainProvider( $providers )
		);
	}

	/**
	 * Get the position coordinates for a given address.
	 *
	 * @param string $address string Address to be geocoded
	 *
	 * @return array|Geocoder\Exception\RuntimeException
	 */
	public function geocode( $address ) {
		try {
			$result      = $this->geocoder->geocode( $address );
			$coordinates = $result->getCoordinates();
			do_action( 'gravityview_log_debug', __METHOD__ . ': Geocoded [' . $address . '] to ' . implode( ', ', $coordinates ) );

			return $coordinates;
		} catch ( Exception $e ) {
			do_action( 'gravityview_log_error', __METHOD__ . ': Trying to fetch the position of address [' . $address . ']. Error message:', $e->getMessage() );

			return $e;
		}
	}
}