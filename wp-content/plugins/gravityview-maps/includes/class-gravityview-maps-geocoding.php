<?php
/**
 * GravityView Maps Extension - Geocoding
 *
 * Using the Geocoder php lib
 *
 * @package   GravityView_Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.0.0
 */

class GravityView_Maps_Geocoding extends GravityView_Maps_Component {

	/**
	 * @var \GravityView_Maps_HTTP_Adapter
	 */
	protected $adapter = null;

	/**
	 * @var \Geocoder\Geocoder
	 */
	protected $geocoder = null;


	function load() {

		// Loads the composer libs ( Geocoder, ...)
		require_once $this->loader->dir . 'vendor/autoload.php';
		require_once $this->loader->dir . 'includes/class-gravityview-maps-http-adapter.php';

		try {
			$this->adapter = $this->set_http_adapter();
			$this->geocoder = $this->set_geocoder();
			$this->set_providers();
		} catch ( Exception $e ) {
			do_action( 'gravityview_log_error', '[GravityView Maps] Failed during geocoder load. Error message:', $e->getMessage() );
		}
	}

	/**
	 * Configure the http settings used by the Geocoder
	 *
	 * @return \GravityView_Maps_HTTP_Adapter
	 */
	public function set_http_adapter() {
		return new \GravityView_Maps_HTTP_Adapter();
	}

	public function set_geocoder() {
		return new \Geocoder\Geocoder();
	}

	public function set_providers() {

		$keys = $this->get_providers_settings();

		$locale  = apply_filters( 'gravityview/maps/geocoding/providers/locale', null );
		$region  = apply_filters( 'gravityview/maps/geocoding/providers/region', null );

		$providers = array();

		// Google Maps for Work Provider
		if ( isset( $keys['googlemapsbusiness-api-clientid'] ) && isset( $keys['googlemapsbusiness-api-key'] ) ) {
			$providers[] = new \Geocoder\Provider\GoogleMapsBusinessProvider(
				$this->adapter,
				$keys['googlemapsbusiness-api-clientid'],
				$keys['googlemapsbusiness-api-key'],
				$locale,
				$region,
				true
			);
		} elseif ( apply_filters( 'gravityview/maps/geocoding/providers/googlemaps', true ) ) {

			$googlemaps_key = isset( $keys['googlemaps-api-key'] ) ? $keys['googlemaps-api-key'] : null;

			/**
			 * @filter `gravityview/maps/geocoding/providers/googlemaps/api_key` Filter the Google Maps API key used for Google Maps Geocoding API
			 * @since 1.4
			 * @param string $googlemaps_key Google Maps Geocoding API key
			 */
			$googlemaps_key = apply_filters( 'gravityview/maps/geocoding/providers/googlemaps/api_key', $googlemaps_key );

			// Google Maps Provider (even without key)
			$providers[] = new \Geocoder\Provider\GoogleMapsProvider(
				$this->adapter,
				$locale,
				$region,
				true,
				$googlemaps_key
			);

			unset( $googlemaps_key );
		}

		// Bing Maps Provider
		if ( isset( $keys['bingmaps-api-key'] ) ) {
			$providers[] = new \Geocoder\Provider\BingMapsProvider(
				$this->adapter,
				$keys['bingmaps-api-key'],
				$locale
			);
		}

		// MapQuest Provider
		if ( isset( $keys['mapquest-api-key'] ) ) {
			$providers[] = new \Geocoder\Provider\MapQuestProvider(
				$this->adapter,
				$keys['mapquest-api-key'],
				$locale,
				/**
				 * @filter `gravityview/maps/geocoding/mapquest/licensed_data`
				 * @param boolean $licensed_data True to use MapQuest's licensed endpoints, default is false to use the open endpoints (optional).
				 */
				apply_filters( 'gravityview/maps/geocoding/mapquest/licensed_data', false )
			);
		}

		// OpenStreetMap Provider
		if ( apply_filters( 'gravityview/maps/geocoding/providers/openstreetmap', true ) ) {
			$providers[] = new \Geocoder\Provider\OpenStreetMapProvider(
				$this->adapter,
				$locale
			);
		}

		if( empty( $providers ) ) {
			do_action( 'gravityview_log_error', '[GravityView Maps] Not possible to use Geocoding without providers' );
			return;
		}

		$this->geocoder->registerProvider(
			new \Geocoder\Provider\ChainProvider( $providers )
		);

	}


	/**
	 * Based on the GravityView Maps general Settings, build the array of geocoding providers' keys
	 *
	 * @param $adapter
	 */
	protected function get_providers_settings() {

		if( ! method_exists( 'GravityView_Settings', 'getSetting' ) ) {
			return false;
		}

		$providers = array();
		$keys = array( 'googlemaps-api-key', 'googlemapsbusiness-api-clientid', 'googlemapsbusiness-api-key', 'bingmaps-api-key', 'mapquest-api-key' );

		foreach( $keys as $key ) {
			$api_key = GravityView_Settings::getSetting( $key );
			if( !empty( $api_key ) ) {
				$providers[ $key ] = trim( $api_key );
			}
		}

		return $providers;
	}

	/**
	 * Get the position coordinates for a given address.
	 *
	 * @param string $address string Address to be geocoded
	 *
	 * @return array
	 */
	public function geocode( $address ) {

		try {
			$result = $this->geocoder->geocode( $address );
			$coordinates = $result->getCoordinates();
		} catch ( Exception $e ) {
			do_action( 'gravityview_log_error', __METHOD__ . ': Trying to fetch the position of address ['. $address .']. Error message:', $e->getMessage() );
			$coordinates = array();
		}

		return $coordinates;
	}

}