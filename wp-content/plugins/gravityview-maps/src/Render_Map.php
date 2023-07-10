<?php

namespace GravityKit\GravityMaps;

use WP_Scripts;
use GravityView_View;
use GVCommon;
use GravityKitFoundation;
use GravityView_frontend;
use \GV\REST\Core as Rest_Core;

/**
 * Handles displaying the map code, including rendering the JavaScript
 */
class Render_Map extends Component {
	/**
	 * Stores a singleton of this component.
	 *
	 * @since 2.0
	 *
	 * @var $this
	 */
	protected static $_instance;

	/**
	 * Stores whether a given view has maps.
	 *
	 * @since 2.0
	 *
	 * @var array
	 */
	protected static $has_map = [];

	/**
	 * @since 1.2 Used to check whether Google Maps exists in a currently registered script
	 */
	const google_maps_regex = '/maps\.google(apis)?\.com\/maps\/api\/js/ism';

	var $service = 'google';

	public $google_script_handle = 'gv-google-maps';

	/**
	 * Holds map ID (used for multiple map canvases)
	 */
	var $map_id = 0;

	/**
	 * @since 1.7
	 * @var null|string $api_key Holds Google Maps API key
	 */
	protected $api_key = null;

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

	function load() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 200 );

		add_action( 'gravityview_map_render_div', [ $this, 'render_map_div' ], 10, 2 );

		add_action( 'gravityview_after', [ $this, 'localize_javascript' ], 100, 1 );

		add_filter( 'gravityview/template/text/no_entries', [ $this, 'include_map_no_results' ], 20 );

		add_filter( 'gravityview/widgets/wrapper_css_class', [ $this, 'add_widget_wrapper_search_css_class' ], 10, 2 );
	}

	/**
	 * Include a maps even when there are no results.
	 *
	 * @since 2.0
	 *
	 * @param $html
	 *
	 * @return string
	 */
	public function include_map_no_results( $html ) {
		ob_start();
		$map_html = ob_get_clean();

		return $map_html . $html;
	}

	/**
	 * Gets the current Google Script Handle.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_google_script_handle() {
		return $this->google_script_handle;
	}

	/**
	 * Set Google Maps API key
	 *
	 * @since 1.7
	 *
	 * @return void
	 */
	protected function set_api_key() {
		global $gravityview_maps;

		if ( ! function_exists( 'gravityview' ) ) {
			return;
		}

		$providers = $gravityview_maps->component_instances['Settings']->get_providers_api_keys();

		/**
		 * @filter `gravityview/maps/render/google_api_key` Modify the Google API key used when registering the `gv-google-maps` script
		 *
		 * @TODO   Refactor this filter (e.g., 'gk/gravitymaps/maps/render/[provider]/keys/[key]')
		 *
		 * @param string $key If the Google API key setting is set in GravityView Settings, use it. Otherwise: ''
		 */
		$key = apply_filters( 'gravityview/maps/render/google_api_key', $providers['google_maps/key'] );

		$this->api_key = $key;
	}

	/**
	 * Get Google Maps API key
	 *
	 * @since 1.7
	 *
	 * @return string|null
	 */
	function get_api_key() {
		if ( is_null( $this->api_key ) ) {
			$this->set_api_key();
		}

		return $this->api_key;
	}

	/**
	 * Get the Google Maps API JS handle
	 *
	 * @return string Handle for the Google Maps v3 API script
	 */
	protected function set_maps_script_handle( $handle ) {
		$this->google_script_handle = $handle;
	}

	/**
	 * Determines the Google JS handle internally, to avoid conflicting with other plugins.
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function determine_google_script_handle() {
		global $wp_scripts;

		if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
			$wp_scripts = new WP_Scripts();
		}

		// Default: use our own script
		$handle = $orginal_handle = $this->google_script_handle;

		/**
		 * Find other plugins that have registered Google Maps
		 *
		 * @since 1.2
		 */
		foreach ( $wp_scripts->registered as $script ) {
			if ( preg_match( static::google_maps_regex, $script->src ) ) {
				$handle = $script->handle;
				do_action( 'gravityview_log_debug', __METHOD__ . ': Using non-GravityView Maps script: ' . $handle, $script );
				break;
			}
		}

		/**
		 * @filter `gravityview_maps_google_script_handle` If your site already has Google Maps v3 API script enqueued, you can specify the handle here.
		 *
		 * @param string $script_slug Default: `gv-google-maps`
		 */
		$handle = apply_filters( 'gravityview_maps_google_script_handle', $handle );

		/**
		 * Any scripts that we add that depend on the original handle get modified.
		 */
		foreach ( $wp_scripts->registered as $key => $script ) {
			// Find Scripts that depend on this original handle.
			$index = array_search( $orginal_handle, $script->deps, true );
			if ( false === $index ) {
				continue;
			}

			// Replace with the new handle.
			$script->deps[ $index ] = $handle;

			// Overwrite the script.
			$wp_scripts->registered[ $key ] = $script;
		}

		// Deregister our Google Maps.
		if ( $orginal_handle !== $handle ) {
			wp_deregister_script( $orginal_handle );
		}

		$this->set_maps_script_handle( $handle );
	}

	/**
	 * Fetches the Google Script URL.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_google_script_url() {
		$args = [
			'libraries' => 'places',
			'key'       => $this->get_api_key(),

			/**
			 * Don't remove this piece, it prevents error on missing callback.
			 * As `callback` is required for the Maps Script but for our code it's not.
			 *
			 * @url https://developers.google.com/maps/documentation/javascript/url-params
			 */
			'callback'  => 'Function.prototype',
		];

		$url = set_url_scheme( 'https://maps.googleapis.com/maps/api/js' );
		$url = add_query_arg( $args, $url );

		return $url;
	}

	/**
	 * Enqueue statics (JS and CSS).
	 *
	 * @action wp_enqueue_scripts
	 *
	 * @since  0.1.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		add_action( 'gravityview_before', [ $this, 'enqueue_when_needed' ] );
		add_action( 'gravityview_search_widget_fields_before', [ $this, 'enqueue_when_needed' ] );
	}

	/**
	 * Register admin scripts.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function maybe_register_scripts() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';

		wp_register_script( 'gk-maps-base', plugins_url( "assets/js/base{$suffix}", $this->loader->path ), [ 'wp-hooks' ], $this->loader->plugin_version );

		// Our work here is already done.
		if ( wp_script_is( 'gv-google-maps-spiderfier', 'registered' ) ) {
			return;
		}

		$this->determine_google_script_handle();

		wp_register_script( $this->get_google_script_handle(), $this->get_google_script_url(), [], null );

		wp_register_script( 'gv-google-maps-clusterer', plugins_url( '/assets/lib/markerclusterer.min.js', $this->loader->path ), [], null );

		wp_register_script( 'gv-google-maps-spiderfier', plugins_url( '/assets/lib/oms.min.js', $this->loader->path ), [], null );

		wp_register_script( 'gk-maps-search-fields', plugins_url( "assets/js/gk-maps-search-fields{$suffix}", $this->loader->path ), [
			'jquery',
			'gravityview-maps',
			'gk-maps-base',
		], $this->loader->plugin_version );

		wp_register_script( 'gk-maps-address-auto-complete', plugins_url( "assets/js/gk-maps-address-auto-complete{$suffix}", $this->loader->path ), [
			'jquery',
			'gk-maps-base',
			$this->get_google_script_handle(),
		], $this->loader->plugin_version );

		/** @see Render_Map::enqueue_when_needed() for Google Map script JIT registration */
		wp_register_script( 'gravityview-maps', plugins_url( '/assets/js/gv-maps' . $suffix, $this->loader->path ), [
			'jquery',
			'gk-maps-base',
			$this->get_google_script_handle(),
			'gv-google-maps-clusterer',
			'gv-google-maps-spiderfier',
		], $this->loader->plugin_version, true );

		wp_register_style( 'gravityview-maps', plugins_url( '/assets/css/gv-maps.css', $this->loader->path ), [], $this->loader->plugin_version );
	}

	/**
	 * Enqueue scripts only if maps are loaded
	 *
	 * @return void
	 */
	public function enqueue_when_needed() {
		$this->determine_google_script_handle();

		$this->maybe_register_scripts();

		wp_enqueue_script( 'gk-maps-address-auto-complete' );

		// is the map templates, is there any map field or map widget
		if ( ! $this->has_maps() ) {
			return;
		}

		wp_enqueue_style( 'gravityview-maps' );
		wp_enqueue_script( 'gk-maps-search-fields' );

		wp_localize_script(
			'gk-maps-search-fields',
			'gk_maps_search_fields',
			[
				'geolocationCurrentLocationError' => esc_attr__( 'There is no location support on this device or it is disabled. Please check your settings.', 'gk-gravitymaps' ),
				'invalidGeolocationCoordinates  ' => esc_attr__( 'There was an unknown error with retrieving your coordinates, please check if you are using any AdBlockers or if you are blocking Google API requests on your Browser.', 'gk-gravitymaps' ),
				'geolocationFenceCenterImage'     => plugins_url( '/assets/img/mapicons/geolocation-fence-center.png', $this->loader->path ),
				'searchRestEndpoint'              => Rest_Core::get_url() . '/views/{view_id}/entries.html?limit={limit}',
				'currentLocationLabel'            => esc_attr__( 'Current location', 'gk-gravitymaps' ),
				'currentLocationTitle'            => esc_attr__( 'Click to use current location', 'gk-gravitymaps' ),
				'searchOnMoveLabel'               => esc_attr__( 'Search as map moves', 'gk-gravitymaps' ),
				'redoSearchLabel'                 => esc_attr__( 'Redo search in map', 'gk-gravitymaps' ),
			]
		);

		wp_enqueue_script( 'gravityview-maps' );
	}

	/**
	 * Checks if the current page load has any map to render
	 *
	 * @return bool
	 */
	public function has_maps() {
		if ( ! function_exists( 'gravityview_get_current_views' ) ) {
			return false;
		}

		$views = gravityview_get_current_views();

		foreach ( $views as $view ) {

			if ( isset( static::$has_map[ $view['view_id'] ] ) ) {
				return static::$has_map[ $view['view_id'] ];
			}

			$ms                                  = Admin::get_map_settings( $view['view_id'] );
			static::$has_map[ $view['view_id'] ] = ! empty( $ms['map_exists'] ) ? $ms['map_exists'] : false;

			if ( ! empty( $ms['map_exists'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get all the markers from the entries in the current view
	 *
	 * @param GravityView_View $view The GravityView_View instance
	 *
	 * @return array         Array of text addresses
	 */
	private function get_marker_array( $view ) {
		$Data = new Data( $view );

		return $Data::get_markers( $this->service );
	}

	/**
	 * Returns whether the requested page is a GravityView search result or not.
	 *
	 * @since 1.8
	 *
	 * @return bool
	 */
	public function is_search( $request = null ) {

		if ( (int) \GV\Utils::_REQUEST( 'is_current' ) === 1 ) {
			return true;
		}

		if ( \GV\Utils::_REQUEST( 'address_search' ) ) {
			return true;
		}

		if ( empty( $request ) ) {
			$request = gravityview()->request;
		}

		return function_exists( 'gravityview' ) ? $request->is_search() : GravityView_frontend::getInstance()->is_searching();
	}

	/**
	 * Adds '.gv-widgets-is-search' CSS class to widget container when performing a search.
	 *
	 * This helps us to show the map in the widget area when performing a search, but not when there are simply no results.
	 *
	 * @param string $css_class
	 *
	 * @return string CSS class, with "gv-widgets-is-search" added if performing a search.
	 */
	public function add_widget_wrapper_search_css_class( $css_class = '' ) {

		if ( $this->is_search() && \GV\Utils::_REQUEST( 'lat' ) && \GV\Utils::_REQUEST( 'long' ) ) {
			$css_class .= ' gv-widgets-is-search';
		}

		return $css_class;
	}


	/**
	 * Output the map placeholder HTML
	 * If entry is defined, add the entry ID to the <div> tag to allow JS logic to render the entry marker only
	 *
	 * @since 1.6.2 added $context parameter
	 *
	 * @param array|null                  $entry   Gravity Forms entry object
	 * @param string|\GV\Template_Context $context Current context, if set. Otherwise, empty string.
	 *
	 * @return void
	 */
	function render_map_div( $entry = null, $context = '' ) {
		// Call again, just in case not already enqueued
		$this->enqueue_when_needed();

		$entry_id           = empty( $entry ) ? '' : $entry['id'];
		$is_multi_entry_map = ( ! $entry_id );

		$hide_until_searched = $context && $context->view->settings->get( 'hide_until_searched' );

		if (
			! $is_multi_entry_map
			&& ! empty( $hide_until_searched )
			&& ! $this->is_search()
		) {
			return;
		}

		$map_css_class = 'gk-map-canvas-' . $entry_id;

		if ( $is_multi_entry_map ) {
			$markers       = $this->get_marker_array( GravityView_View::getInstance() );
			$map_css_class .= ' gk-multi-entry-map';
		} else {
			$markers = Data::get_instance()->get_markers_by_entry( $entry_id );
			$markers = array_values( array_map( static function ( $marker ) {
				return $marker->to_array();
			}, $markers ) );

			$markers = array_filter( $markers, [ Markers::class, 'filter_valid_marker_data' ] );
		}

		$data = [
			'view_id'            => gravityview_get_view_id(),
			'entry_id'           => $entry_id,
			'is_multi_entry_map' => $is_multi_entry_map,
			'markers_data'       => $markers,
			'markers'            => [],
		];

		if ( $is_multi_entry_map ) {
			$paging            = GravityView_View::getInstance()->getPaging();
			$data['page_size'] = $paging['page_size'];
		}

		?>
		<div
			id="gv-map-canvas-<?php echo $this->map_id; ?>"
			class="gv-map-canvas <?php echo esc_attr( $map_css_class ); ?>"
			data-entryid="<?php echo esc_attr( $entry_id ); ?>"
			data-gk-map="<?php echo esc_attr( wp_json_encode( $data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ) ); ?>"
		></div>
		<?php

		$this->map_id = $this->map_id + 1;
	}

	/**
	 * Determines if a given view can use REST API, which is important for the Maps.
	 *
	 * @since 2.0
	 *
	 * @param int|string|\WP_Post $view_id
	 *
	 * @return bool
	 */
	public function can_view_use_rest( $view_id ) {
		$view = \GV\View::from_post( get_post( $view_id ) );
		if ( ! $view ) {
			return false;
		}

		// REST
		if ( gravityview()->plugin->settings->get( 'rest_api' ) && $view->settings->get( 'rest_disable' ) === '1' ) {
			return false;
		}

		if ( ! gravityview()->plugin->settings->get( 'rest_api' ) && $view->settings->get( 'rest_enable' ) !== '1' ) {
			return false;
		}

		return true;
	}

	/**
	 * Localize and print the scripts
	 *
	 * @param int $view_id ID of the View being rendered
	 *
	 * @return void
	 */
	public function localize_javascript( $view_id ) {
		if ( $this->map_id <= 0 ) {
			return;
		}

		// Get the markers data
		$markers = $this->get_marker_array( GravityView_View::getInstance() );
		$view = \GV\View::from_post( get_post( $view_id ) );

		// get view map settings
		$ms = Admin::get_map_settings( $view_id );

		$map_options = $this->parse_map_options( $ms, $markers );

		$is_search = $this->is_search();

		$translations = [
			'display_errors'                => GVCommon::has_cap( [ 'gravityforms_edit_settings', 'gravityview_view_settings' ] ),
			'is_search'                     => $is_search,
			'google_maps_api_key_not_found' => esc_html__( 'Google Maps API key was not found. Please make sure that it is configured in GravityView settings.', 'gk-gravitymaps' ),
			'google_maps_script_not_loaded' => esc_html__( 'Google Maps script failed to load.', 'gk-gravitymaps' ),
			'google_maps_api_error'         => esc_html__( 'Google Maps API returned an error. Please check the browser console for more information.', 'gk-gravitymaps' ),
			'entries_missing_coordinates'   => esc_html__( 'None of the address fields have latitude/longitude coordinates. Please make sure that at least one address is geocoded before a map can be displayed.', 'gk-gravitymaps' ),
			'cannot_use_rest_error'         => esc_html__( 'Rest API cannot be disabled when using the Maps functionality. Please make sure this view has Rest API enabled.', 'gk-gravitymaps' ),
			'hide_until_searched'           => (bool) $view->settings->get( 'hide_until_searched' ),
			'can_use_rest'                  => $this->can_view_use_rest( $view_id ),
		];

		if ( empty( $ms['map_address_field'] ) ) {
			$translations['address_field_missing'] = esc_html__( 'The "Address Field" setting has not been configured for this View. In View Settings, click on the Maps tab, set the fields you would like to display on this map, then save the View.', 'gk-gravitymaps' );
		}

		$data = array_merge( $map_options, $translations );

		wp_localize_script( 'gravityview-maps', 'GV_MAPS', $data );

		?>
		<script data-js="gk-view-data" type="application/json">
			<?php echo wp_json_encode( $data ); ?>
		</script>
		<?php
	}

	/**
	 * Convert zoom control settings to values expected by Google Maps
	 *
	 * @see   https://developers.google.com/maps/documentation/javascript/controls#Adding_Controls_to_the_Map
	 *
	 * @since 1.4.2
	 *
	 * @param array $map_settings Array of map settings
	 *
	 * @return bool|null `TRUE`: show zoom control; `FALSE`: hide zoom control; `NULL`: let map decide
	 */
	private function parse_map_zoom_control( $map_settings ) {
		switch ( rgar( $map_settings, 'map_zoom_control' ) ) {
			// Force don't show zoom
			case 'none':
				$zoomControl = false;
				break;

			// Force zoom to display
			case 'small':
			case 'large': // Backward compatibility
				$zoomControl = true;
				break;
			// Let the map decide
			default:
				$zoomControl = null;
				break;
		}

		return $zoomControl;
	}

	/**
	 * Build the array of configurable map options used to generate the map
	 *
	 * @param array $map_settings Map settings
	 * @param array $markers_info All the markers to display on a map
	 *
	 * @return array Final options passed to
	 */
	private function parse_map_options( $map_settings, $markers_info ) {
		/**
		 * Default settings
		 */
		$map_options = [
			'MapOptions'            => [
				'zoomControl' => $this->parse_map_zoom_control( $map_settings ),

			],
			'api_key'               => $this->get_api_key(),
			'icon'                  => $map_settings['map_marker_icon'],
			'markerClusterIconPath' => plugins_url( 'assets/img/mapicons/m', $this->loader->path ),
			'markers_info'          => $markers_info,
			'map_id_prefix'         => 'gv-map-canvas',
			'layers'                => [
				'bicycling' => intval( 'bicycling' === $map_settings['map_layers'] ),
				'transit'   => intval( 'transit' === $map_settings['map_layers'] ),
				'traffic'   => intval( 'traffic' === $map_settings['map_layers'] ),
			],
			'is_single_entry'       => gravityview_is_single_entry(),
			'icon_bounce'           => true,
			// Return false to disable icon bounce
			'sticky'                => ! empty( $map_settings['map_canvas_sticky'] ),
			// todo: make sure we are running the map template
			'template_layout'       => ! empty( $map_settings['map_canvas_position'] ) ? $map_settings['map_canvas_position'] : '',
			// todo: make sure we are running the map template
			'marker_link_target'    => '_top',
			// @since 1.4 allow to specify a different marker link target
			'mobile_breakpoint'     => 600,
			// @since 1.4.2 Set the mobile breakpoint, in pixels
			'infowindow'            => [
				'no_empty'   => true,
				// @since 1.4 check if the infowindow is empty, and if yes, force a link to the single entry
				'empty_text' => __( 'View Details', 'gk-gravitymaps' ),
				//@since 1.4, If the infowindow is empty, generate a link to the single entry with this text
				'max_width'  => 300
				//@since 1.4, Max width of the infowindow (in px)
			],
		];

		/**
		 * @filter `gravityview/maps/render/options` Modify the map options used by Google. Uses same parameters as the [Google MapOptions](https://developers.google.com/maps/documentation/javascript/reference#MapOptions)
		 *
		 * @param array $map_options Map Options
		 */
		$map_options = apply_filters( 'gravityview/maps/render/options', $map_options );

		$default_MapOptions = [
			'backgroundColor'           => null,
			'center'                    => null,
			'disableDefaultUI'          => null,
			'disableDoubleClickZoom'    => empty( $map_settings['map_doubleclick_zoom'] ),
			'draggable'                 => ! empty( $map_settings['map_draggable'] ),
			'draggableCursor'           => null,
			'draggingCursor'            => null,
			'heading'                   => null,
			'keyboardShortcuts'         => null,
			'mapMaker'                  => null,
			'mapTypeControl'            => null,
			'mapTypeControlOptions'     => null,
			'mapTypeId'                 => strtoupper( $map_settings['map_type'] ),
			'maxZoom'                   => ! isset( $map_settings['map_maxzoom'] ) ? 16 : intval( $map_settings['map_maxzoom'] ),
			'minZoom'                   => ! isset( $map_settings['map_minzoom'] ) ? 3 : intval( $map_settings['map_minzoom'] ),
			'noClear'                   => null,
			'overviewMapControl'        => null,
			'overviewMapControlOptions' => null,
			'panControl'                => ! empty( $map_settings['map_pan_control'] ),
			'panControlOptions'         => null,
			'rotateControl'             => null,
			'rotateControlOptions'      => null,
			'scaleControl'              => null,
			'scaleControlOptions'       => null,
			'scrollwheel'               => ! empty( $map_settings['map_scrollwheel_zoom'] ),
			'streetView'                => null,
			'streetViewControl'         => ! empty( $map_settings['map_streetview_control'] ),
			'streetViewControlOptions'  => null,
			'styles'                    => empty( $map_settings['map_styles'] ) ? null : json_decode( $map_settings['map_styles'] ),
			'tilt'                      => null,
			'zoom'                      => ! isset( $map_settings['map_zoom'] ) ? 15 : intval( $map_settings['map_zoom'] ),
			'zoomControl'               => null,
			'zoomControlOptions'        => null,
			'markerClustering'          => ! empty( $map_settings['map_marker_clustering'] ),
			'markerClusteringMaxZoom'   => empty( $map_settings['map_marker_clustering_maxzoom'] ) ? null : (int) $map_settings['map_marker_clustering_maxzoom'],
		];

		/**
		 * Enforce specific Google-available parameters, then remove null options
		 *
		 * @uses Render_Map::is_not_null()
		 */
		$map_options['MapOptions'] = array_filter( shortcode_atts( $default_MapOptions, $map_options['MapOptions'] ), [ $this, 'is_not_null' ] );

		unset( $default_MapOptions );

		return $map_options;
	}

	/**
	 * Check whether something is NULL. Used by parse_map_options()
	 *
	 * @see   Render_Map::parse_map_options()
	 *
	 * @see   Render_Map::parse_map_options()
	 *
	 * @since 1.0.3-beta
	 *
	 * @param mixed $var Item to check against.
	 *
	 * @return bool True: Not null; False: is null
	 */
	public function is_not_null( $var = null ) {
		return ! is_null( $var );
	}
}
