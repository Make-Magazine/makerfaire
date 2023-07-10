<?php

namespace GravityKit\GravityMaps;

use GF_Query_Condition;
use GF_Query_Column;
use GravityView_View;

/**
 * Register and launch search filters component.
 *
 * @link      http://gravityview.co
 * @since     TBD
 *
 * @since     TBD
 * @author    GravityView <hello@gravityview.co>
 * @package   GravityView_Maps
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @license   GPL2+
 */
class Search_Filter extends Component {

	protected $has_geolocation_condition = false;

	/**
	 * Stores a singleton of this component.
	 *
	 * @since 2.0
	 *
	 * @var $this
	 */
	protected static $_instance;

	/**
	 * Constructor that calls the component constructor.
	 *
	 * @since 2.0
	 *
	 * @param Loader $loader Instance of GravityView_Ratings_Reviews_Loader
	 *
	 * @return void
	 */
	public function __construct( Loader $loader ) {
		parent::__construct( $loader );
		static::$_instance = $this;
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @since 2.0
	 *
	 * @return $this
	 */
	public static function get_instance() {
		if ( ! isset( static::$_instance ) ) {
			static::$_instance = new static( $GLOBALS['gravityview_maps'] );
		}

		return static::$_instance;
	}

	/**
	 * Unit for Miles.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	const MILES = 'mi';

	/**
	 * Unit for Kilometers.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	const KM = 'km';

	/**
	 * @inheritDoc
	 *
	 * @since 2.0
	 */
	public function load() {

		// Filter the GravityView entries based on Geolocation Params.
		add_action( 'gravityview/view/query', [ $this, 'filter_view_query' ], 1000, 3 );
		add_action( 'gravityview/view/query', [ $this, 'filter_view_query_bounds' ], 1050, 3 );
		add_action( 'gravityview/view/query', [ $this, 'filter_view_query_remove_geolocation_on_empty' ], 1100, 3 );

		// Ensure there is something to be filtered.
		add_filter( 'gravityview_fe_search_criteria', [ $this, 'ensure_geolocation_query_filters' ], 10, 3 );

		add_filter( 'gravityview/view/get', [ $this, 'ensure_geolocation_is_always_search' ], 15 );
	}

	/**
	 * Definition of is_search on GravityView is a little funky, and there are no ways to properly filter it.
	 *
	 * The only way that is reliable due to legacy code to mark a request as a Search when you are dealing with
	 * the `hide_until_search` settings is to hook as early as possible in a GV request and set the super global
	 * $_GET['gv_search'] so that all places actually run their required queries.
	 *
	 * @since 2.0
	 *
	 * @param $view
	 *
	 * @return mixed
	 */
	public function ensure_geolocation_is_always_search( $view ) {
		if ( ! static::is_radius_request() && ! static::is_radius_request() ) {
			return $view;
		}

		$_GET['gv_search'] = 1;

		return $view;
	}

	/**
	 * When inside the REST API request we need to ensure we can run Geolocation queries, which require a search
	 * criteria to exist.
	 *
	 * @since 2.0
	 *
	 * @param array      $search_criteria
	 * @param int|string $form_id
	 * @param array      $attributes
	 *
	 * @return array
	 */
	public function ensure_geolocation_query_filters( array $search_criteria, $form_id, $attributes ) {
		$bounds = \GV\Utils::_GET( 'bounds' );
		if ( empty( $bounds ) ) {
			return $search_criteria;
		}

		$geolocation = \GV\Utils::_GET( 'filter_geolocation' );
		if ( empty( $geolocation ) ) {
			return $search_criteria;
		}

		$settings = Admin::get_map_settings( $attributes['id'], false );
		if ( empty( $settings['map_address_field'] ) ) {
			return $search_criteria;
		}

		if ( ! empty( $search_criteria['field_filters'] ) ) {
			return $search_criteria;
		}

		// Force a base value early on, so we can replace.
		$search_criteria['field_filters'] = [
			[
				'form_id' => $form_id,
				'key' => 'geolocation',
				'operator' => 'contains',
				'value' => '1',
			],
			'mode' => 'any',
		];

		return $search_criteria;
	}

	/**
	 * Calculate the longitude and latitude or a radius around a location
	 *
	 * Returns an array with the following keys: `latitude_max`, `latitude_min`, `longitude_max`, `longitude_min`, to produce a radius search
	 *
	 * @param string|float $longitude The longitude to calculate from
	 * @param string|float $latitude  The latitude to calculate from
	 * @param string|float $miles     Number of miles for the radius
	 *
	 * @return array
	 */
	public function get_radius( $longitude, $latitude, $miles ) {
		/**
		 * Latitude: degree is approximately 69.172 miles, and a minute of latitude is approximately 1.15 miles
		 * Longitude: degree ~54.6 miles, minute ~0.91 miles
		 *
		 * @see https://www.usgs.gov/faqs/how-much-distance-does-a-degree-minute-and-second-cover-your-maps
		 */
		$degrees = $miles / 69.172 / 100;
		$minutes = $miles / 1.15 / 100;

		$settings = [
			'latitude_max' => (float) $latitude + $degrees,
			'latitude_min' => (float) $latitude - $degrees,
			'longitude_max' => (float) $longitude + $minutes,
			'longitude_min' => (float) $longitude - $minutes,
		];

		return $settings;
	}

	/**
	 * Quick dirty method of converting Miles to KM.
	 *
	 * @since 2.0
	 *
	 * @param float|string $radius Which radius we are converting.
	 *
	 * @return float
	 */
	public static function convert_to_km( $radius ) {
		return (float) $radius * 1.609344;
	}

	/**
	 * Quick dirty method of converting KM to Miles.
	 *
	 * @since 2.0
	 *
	 * @param float|string $radius Which radius we are converting.
	 *
	 * @return float
	 */
	public static function convert_to_mi( $radius ) {
		return (float) $radius / 1.609344;
	}

	/**
	 * Determines which Radius Unit we should load by default.
	 *
	 * @todo  Pull from Address field used.
	 *
	 * @since 2.0
	 *
	 * @param $view_id
	 *
	 * @return mixed|string
	 */
	public function get_default_radius_unit( $view_id = null ) {
		$settings = Admin::get_map_settings( $view_id, false );

		// If we have a default setting for default radius unit and it's set to a valid value we use it.
		if (
			! empty( $settings['map_default_radius_search_unit'] )
			&& in_array( $settings['map_default_radius_search_unit'], [ static::MILES, static::KM ], true )
		) {
			return $settings['map_default_radius_search_unit'];
		}

		$miles  = [
			'en_US',
			'en_GB',
			'my_MM',
		];
		$locale = get_locale();
		if ( in_array( $locale, $miles ) ) {
			return static::MILES;
		}

		return static::KM;
	}

	/**
	 * Radius Options for the Geolocation Select for Search Widget.
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	public static function get_radius_select_options( $selected = 5 ) {
		if ( null === $selected ) {
			$selected = 5;
		}

		$default_options = [
			'value' => 0, // int|float|string
			'label' => esc_attr__( '%1$s', 'gk-gravitymaps' ),
			'selected' => false,
		];

		$options = [
			[
				'value' => 1,
			],
			[
				'value' => 5,
			],
			[
				'value' => 10,
			],
			[
				'value' => 15,
			],
			[
				'value' => 20,
			],
			[
				'value' => 25,
			],
			[
				'value' => 50,
			],
			[
				'value' => 100,
			],
		];

		/**
		 * Allow modifications to the geolocation radius options.
		 *
		 * @since 2.0
		 *
		 * @param array           $options         Which options we are passing forward.
		 * @param array           $default_options Which are the default options that will be applied afterwards.
		 * @param string|int|null $selected        Which is the selected option if any.
		 */
		$options = (array) apply_filters( 'gk/gravitymaps/geolocation_radius_options', $options, $default_options, $selected );

		return array_filter( array_map( static function ( $option ) use ( $default_options, $selected ) {
			if ( ! is_array( $option ) ) {
				return null;
			}

			$option = array_merge( $default_options, $option );

			// Select one based on the value passed.
			if ( $option['value'] == $selected ) {
				$option['selected'] = true;
			}

			return $option;
		}, $options ) );
	}

	/**
	 * Determines if a given request is a bounds request.
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public static function is_bounds_request(): bool {
		$bounds = \GV\Utils::_GET( 'bounds' );
		return ! empty( $bounds );
	}

	/**
	 * Determines if a given request is a radius request.
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public static function is_radius_request(): bool {
		$lat  = \GV\Utils::_GET( 'lat' );
		$long = \GV\Utils::_GET( 'long' );
		if ( ! $lat ) {
			return false;
		}

		if ( ! $long ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the Latitude and Longitude from a view, for Manual Coordinates fields.
	 *
	 * @since 2.2
	 *
	 * @param $view
	 *
	 * @return ?object {
	 *                     lat: string|int,
	 *                     long: string|int,
	 *                 }
	 */
	public static function get_manual_coordinates_object( $view ): ?object {
		/**
		 * We specifically are mocking a GravityView_View object to get the form, because of backwards compatibility related
		 * to how the filter was used in the past. Eventually we should deprecate this filter below, so we can use a normal
		 * view instead of the Template Bound one.
		 *
		 *
		 * @see GravityView_View::getForm()
		 */
		$mock_view = new class( $view ) {
			private $view;

			public function __construct( $view ) {
				$this->view = $view;
			}

			public function getForm() {
				return $this->view->form->form;
			}
		};

		/**
		 * @filter `gravityview/maps/markers/lat_long/fields_id` Enable marker position by feeding the latitude and longitude coordinates from form fields ids
		 * @since  1.2
		 *
		 * @param array  $lat_long_fields Array of latitude/longitude of Gravity Forms field IDs
		 * @param object $view            Current View object
		 */
		$lat_long_field_ids = apply_filters( 'gravityview/maps/markers/lat_long/fields_id', [], $mock_view );

		// When empty means there are no fields being filtered.
		if ( empty( $lat_long_field_ids ) ) {
			return null;
		}

		if ( ! is_array( $lat_long_field_ids ) ) {
			gravityview()->log->error(
				'[GravityView Maps] Invalid Marker Coordinates fields. Expected an array with two field ids.',
				[ 'ids' => $lat_long_field_ids, 'view' => $view ]
			);

			return null;
		}

		// If it changed but not 2, means we have an invalid configuration.
		if ( 2 !== count( $lat_long_field_ids ) ) {
			gravityview()->log->error(
				'[GravityView Maps] Invalid Marker Coordinates fields. Expected two items in the array.',
				[ 'ids' => $lat_long_field_ids, 'view' => $view ]
			);
			return null;
		}

		return (object) [
			'lat' => reset( $lat_long_field_ids ),
			'long' => end( $lat_long_field_ids ),
		];
	}

	/**
	 * Filters the \GF_Query with advanced logic.
	 *
	 * @param \GF_Query   $query   The current query object reference
	 * @param \GV\View    $view    The current view object
	 * @param \GV\Request $request The request object
	 */
	public function filter_view_query_bounds( &$query, $view, $request ) {
		if ( ! static::is_bounds_request() ) {
			return;
		}

		$view_id = $view->settings->get( 'id' );
		if ( empty( $view_id ) ) {
			return;
		}

		$settings              = Admin::get_map_settings( $view->settings->get( 'id' ), false );
		$gf_geolocation_fields = Form_Fields::get_gf_geolocation_form_fields( $view->form->ID );
		$lat_long_field        = static::get_manual_coordinates_object( $view );

		if ( empty( $settings['map_address_field'] ) && empty( $gf_geolocation_fields ) && empty( $lat_long_field ) ) {
			return;
		}

		$bounds = \GV\Utils::_GET( 'bounds' );

		// Bail on Null island bounds.
		if ( empty( $bounds['max_lat'] ) && empty( $bounds['min_lat'] ) && empty( $bounds['min_lng'] ) && empty( $bounds['max_lng'] ) ) {
			return;
		}

		/**
		 * If we got to this point it's safe to say this is a search, so we flag so that Hide Entries before search
		 * works as intended, there is no filter to \GV\Request::is_search()
		 */
		$_GET['gv_search'] = true;

		$bounds_condition = new Search_GF_Query_Bounds_Condition();

		if ( ! empty( $settings['map_address_field'] ) ) {
			$bounds_condition->set_fields( $settings['map_address_field'], 'internal' );
		}

		if ( ! empty( $gf_geolocation_fields ) ) {
			$bounds_condition->set_fields( $gf_geolocation_fields, 'gravityforms' );
		}

		if ( ! empty( $lat_long_field ) ) {
			$bounds_condition->set_fields( [ $lat_long_field ], 'manual_coordinates' );
		}
		$bounds_condition->set_bounds( $bounds );

		$query_parts   = $query->_introspect();
		$expressions   = $query_parts['where']->expressions;
		$expressions[] = $bounds_condition;

		$where = GF_Query_Condition::_and( ...$expressions );
		$query->where( $where );

		static::replace_geolocation_with_empty( $query );

		$this->set_has_geolocation_condition( true );
	}

	/**
	 * Filters the \GF_Query with advanced logic.
	 *
	 * @param \GF_Query   $query   The current query object reference
	 * @param \GV\View    $view    The current view object
	 * @param \GV\Request $request The request object
	 */
	public function filter_view_query( &$query, $view, $request ) {
		if ( ! static::is_radius_request() ) {
			return;
		}

		$maps_object = Render_Map::get_instance();

		if ( ! $maps_object->is_search( $request ) ) {
			return;
		}

		$view_id = $view->settings->get( 'id' );
		if ( empty( $view_id ) ) {
			return;
		}

		$lat  = \GV\Utils::_GET( 'lat' );
		$long = \GV\Utils::_GET( 'long' );

		// Bail on Null island bounds.
		if ( empty( $lat ) && empty( $long ) ) {
			return;
		}

		$radius = (float) \GV\Utils::_GET( 'filter_geolocation', 0 );
		$unit   = \GV\Utils::_GET( 'unit', $this->get_default_radius_unit( $view_id ) );

		if ( static::MILES === $unit ) {
			$radius = static::convert_to_km( $radius );
		}

		if ( $radius <= 0 ) {
			return;
		}

		$settings              = Admin::get_map_settings( $view->settings->get( 'id' ), false );
		$gf_geolocation_fields = Form_Fields::get_gf_geolocation_form_fields( $view->form->ID );
		$lat_long_field        = static::get_manual_coordinates_object( $view );

		if ( empty( $settings['map_address_field'] ) && empty( $gf_geolocation_fields ) && empty( $lat_long_field ) ) {
			return;
		}

		/**
		 * If we got to this point it's safe to say this is a search, so we flag so that Hide Entries before search
		 * works as intended, there is no filter to \GV\Request::is_search()
		 */
		$_GET['gv_search'] = true;

		$radius_condition = new Search_GF_Query_Radius_Condition();

		if ( ! empty( $settings['map_address_field'] ) ) {
			$radius_condition->set_fields( $settings['map_address_field'] );
		}

		if ( ! empty( $gf_geolocation_fields ) ) {
			$radius_condition->set_fields( $gf_geolocation_fields, 'gravityforms' );
		}

		if ( ! empty( $lat_long_field ) ) {
			$radius_condition->set_fields( [ $lat_long_field ], 'manual_coordinates' );
		}

		$radius_condition->set_latitude( $lat );
		$radius_condition->set_longitude( $long );
		$radius_condition->set_radius( $radius );

		$query_parts   = $query->_introspect();
		$expressions   = $query_parts['where']->expressions;
		$expressions[] = $radius_condition;

		$where = GF_Query_Condition::_and( ...$expressions );
		$query->where( $where );

		static::replace_geolocation_with_empty( $query );

		$this->set_has_geolocation_condition( true );
	}

	/**
	 * When there was no geolocation condition it just replaces with empty.
	 *
	 * @since 2.0
	 *
	 * @param \GF_Query   $query   The current query object reference
	 * @param \GV\View    $view    The current view object
	 * @param \GV\Request $request The request object
	 */
	public function filter_view_query_remove_geolocation_on_empty( &$query, $view, $request ) {
		if ( $this->has_geolocation_condition() ) {
			return;
		}

		if ( \GravityView_frontend::is_single_entry() ) {
			return;
		}

		static::replace_geolocation_with_empty( $query );
	}

	/**
	 * Sets the value for a flag determining if we have a geolocation condition that got replaced.
	 *
	 * @since 2.0
	 *
	 * @param bool $value
	 */
	public function set_has_geolocation_condition( $value ) {
		$this->has_geolocation_condition = (bool) $value;
	}

	/**
	 * Gets the value for a flag determining if we have a geolocation condition that got replaced.
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function has_geolocation_condition() {
		return (bool) $this->has_geolocation_condition;
	}

	/**
	 * Given a GF Query condition we search for a particular field and replace it with the new condition passed in.
	 *
	 * Note that this method is recursive, as GF Conditions can be recursive too.
	 *
	 * @since 2.0
	 *
	 * @param GF_Query_Condition|null $condition    Search on this and child expressions.
	 * @param string                  $find         A particular Column we are looking for.
	 * @param GF_Query_Condition      $replace_with What the particular column conditional should be replaced with.
	 *
	 * @return GF_Query_Condition
	 */
	public static function replace_condition( $condition, $find, GF_Query_Condition $replace_with ) {
		if ( ! $condition instanceof GF_Query_Condition ) {
			return $condition;
		}

		// Actually does the replacing.
		if ( $condition->left instanceof GF_Query_Column ) {
			if ( $condition->left->field_id === $find ) {
				$condition = $replace_with;
			}
		}

		// This happens after since we don't know what kind of condition was passed.
		if ( 0 !== count( $condition->expressions ) ) {
			$expressions = array_map( static function ( $condition ) use ( $replace_with, $find ) {
				return static::replace_condition( $condition, $find, $replace_with );
			}, $condition->expressions );

			// Conditions cannot be modified only re-constructed.
			if ( GF_Query_Condition::_AND === $condition->operator ) {
				$condition = GF_Query_Condition::_and( ...$expressions );
			} elseif ( GF_Query_Condition::_OR === $condition->operator ) {
				$condition = GF_Query_Condition::_or( ...$expressions );
			}
		}

		return $condition;
	}

	/**
	 * Given a GF Query, we introspect and replace the geolocation condition with an empty one.
	 *
	 * @since 2.2
	 *
	 * @param \GF_Query $query The current query object reference.
	 */
	public static function replace_geolocation_with_empty( &$query ) {
		$empty_condition = new GF_Query_Condition(
			1,
			GF_Query_Condition::EQ,
			1
		);
		$query_parts     = $query->_introspect();
		$where           = static::replace_condition( $query_parts['where'], 'geolocation', $empty_condition );
		$query->where( $where );
	}

}
