<?php

/**
 * Handle displaying the map code, including rendering the Javascript
 */
class GravityView_Maps_Render_Map extends GravityView_Maps_Component {

	/**
	 * @since 1.2 Used to check whether Google Maps exists in a currently registered script
	 */
	const google_maps_regex = '/maps\.google(apis)?\.com\/maps\/api\/js/ism';

	var $service = 'google';

	var $google_script_handle = 'gv-google-maps';

	// holds the map id (used for multiple map canvas)
	var $map_id = 0;


	function load() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts'), 200 );

		add_action( 'gravityview_map_render_div', array( $this, 'render_map_div' ), 10, 1 );

		add_action( 'gravityview_after', array( $this, 'localize_javascript' ), 100, 1 );

	}

	/**
	 * Get the Google Maps API JS handle
	 *
	 * @return string Handle for the Google Maps v3 API script
	 */
	private function set_maps_script_handle() {
		global $wp_scripts;

		if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
			$wp_scripts = new WP_Scripts();
		}

		// Default: use our own script
		$handle = $this->google_script_handle;

		/**
		 * Find other plugins that have registered Google Maps
		 *
		 * @since 1.2
		 */
		foreach ( $wp_scripts->registered as $script ) {
			if ( preg_match( self::google_maps_regex, $script->src ) ) {
				$handle = $script->handle;
				do_action( 'gravityview_log_debug', __METHOD__ . ': Using non-GravityView Maps script: ' . $handle, $script );
				break;
			}
		}

		/**
		 * @filter `gravityview_maps_google_script_handle` If your site already has Google Maps v3 API script enqueued, you can specify the handle here.
		 * @param[in,out] string $script_slug Default: `gv-google-maps`
		 */
		$this->google_script_handle = apply_filters( 'gravityview_maps_google_script_handle', $handle );

	}

	/**
	 * Enqueue statics (JS and CSS).
	 *
	 * @action wp_enqueue_scripts
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->register_scripts();
		$this->enqueue_when_needed();
	}

	/**
	 * Register admin scripts.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function register_scripts() {

		$this->set_maps_script_handle();

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';
		$protocol = is_ssl() ? 'https' : 'http';

		// Is there any API KEY
		$api_key = method_exists( 'GravityView_Settings', 'getSetting' ) ? GravityView_Settings::getSetting( 'googlemaps-api-key' ) : '';

		/**
		 * @filter `gravityview/maps/render/google_api_key` Modify the Google API key used when registering the `gv-google-maps` script
		 * @param string $api_key If the Google API key setting is set in GravityView Settings, use it. Otherwise: ''
		 */
		$api_key = apply_filters( 'gravityview/maps/render/google_api_key', $api_key );

		$api_key =  !empty( $api_key ) ? '?key=' . $api_key : '';

		wp_register_script( 'gv-google-maps', $protocol .'://maps.googleapis.com/maps/api/js'. $api_key, array(), null );

		wp_register_script( 'gravityview-maps', plugins_url( '/assets/js/gv-maps'.$suffix, $this->loader->_path ), array( 'jquery', $this->google_script_handle ), $this->loader->plugin_version, true );

		wp_register_style( 'gravityview-maps', plugins_url( '/assets/css/gv-maps.css', $this->loader->_path ), array(), $this->loader->plugin_version );
	}



	/**
	 * Enqueue
	 *
	 * @return void
	 */
	public function enqueue_when_needed() {

		// is the map templates, is there any map field or map widget
		if( ! $this->has_maps() ) {
			return;
		}

		wp_enqueue_style( 'gravityview-maps' );

		wp_enqueue_script( 'gravityview-maps' );

	}

	/**
	 * Checks if the current page load has any map to render
	 *
	 * @return bool
	 */
	public function has_maps() {

		if( ! function_exists( 'gravityview_get_current_views') ) {
			return false;
		}

		$views = gravityview_get_current_views();

		foreach( $views as $view ) {

			$ms = GravityView_Maps_Admin::get_map_settings( $view['view_id'] );

			if( !empty( $ms['map_exists'] ) ) {
				return true;
			}
		}

		return false;
	}




	/**
	 * Get all the markers from the entries in the current view
	 *
	 * @param  GravityView_View $this The GravityView_View instance
	 * @return array         Array of text addresses
	 */
	private function get_marker_array( $View ) {

		$Data = new GravityView_Maps_Data( $View );

		$markers = $Data::get_markers( $this->service );

		return $markers;
	}

	/**
	 * Output the map placeholder HTML
	 * If entry is defined, add the entry ID to the <div> tag to allow JS logic to render the entry marker only
	 * @param array|null $entry Gravity Forms entry object
	 * @return void
	 */
	function render_map_div( $entry = null ) {

		$entry_id = empty( $entry ) ? '' : $entry['id'];

		?>
		<div id="gv-map-canvas-<?php echo $this->map_id; ?>" class="gv-map-canvas" data-entryid="<?php echo esc_attr( $entry_id ); ?>"></div>
		<?php

		$this->map_id = $this->map_id + 1;
	}

	/**
	 * Localize and print the scripts
	 *
	 * @param  int $view_id ID of the View being rendered
	 *
	 * @return void
	 */
	public function localize_javascript( $view_id ) {

		if( $this->map_id <= 0 ) {
			return;
		}

		// Get the markers data
		$markers_info = $this->get_marker_array( GravityView_View::getInstance() );

		// get view map settings
		$ms = GravityView_Maps_Admin::get_map_settings( $view_id );

		$map_options = $this->parse_map_options( $ms, $markers_info );

		wp_localize_script( 'gravityview-maps', 'GV_MAPS', $map_options );

	}

	/**
	 * Convert zoom control settings to values expected by Google Maps
	 *
	 * @see https://developers.google.com/maps/documentation/javascript/controls#Adding_Controls_to_the_Map
	 *
	 * @since 1.4.2
	 *
	 * @param array $map_settings Array of map settings
	 *
	 * @return bool|null `TRUE`: show zoom control; `FALSE`: hide zoom control; `NULL`: let map decide
	 */
	private function parse_map_zoom_control( $map_settings ) {

		switch( rgar( $map_settings, 'map_zoom_control' ) ) {

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
				$zoomControl = NULL;
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
		$map_options = array(
			'MapOptions' => array(
				'zoomControl' => $this->parse_map_zoom_control( $map_settings ),
			),
			'icon' => $map_settings['map_marker_icon'],
			'markers_info' => $markers_info,
			'map_id_prefix' => 'gv-map-canvas',
			'layers' => array(
				'bicycling' => intval( 'bicycling' === $map_settings['map_layers'] ),
				'transit' => intval( 'transit' === $map_settings['map_layers'] ),
				'traffic' => intval( 'traffic' === $map_settings['map_layers'] ),
			),
			'is_single_entry' => gravityview_is_single_entry(),
			'multiple_maps' => $this->map_id - 1,
			'icon_bounce' => true, // Return false to disable icon bounce
			'sticky' => !empty( $map_settings['map_canvas_sticky'] ), // todo: make sure we are running the map template
			'template_layout' => !empty( $map_settings['map_canvas_position'] ) ? $map_settings['map_canvas_position'] : '', // todo: make sure we are running the map template
			'marker_link_target' => '_top', // @since 1.4 allow to specify a different marker link target
			'mobile_breakpoint' => 600, // @since 1.4.2 Set the mobile breakpoint, in pixels
			'infowindow' => array(
				'no_empty' => true, // @since 1.4 check if the infowindow is empty, and if yes, force a link to the single entry
				'empty_text' => __( 'View Details', 'gravityview-maps' ), //@since 1.4, If the infowindow is empty, generate a link to the single entry with this text
				'max_width' => 300 //@since 1.4, Max width of the infowindow (in px)
			),
		);

		/**
		 * @filter `gravityview/maps/render/options` Modify the map options used by Google. Uses same parameters as the [Google MapOptions](https://developers.google.com/maps/documentation/javascript/reference#MapOptions)
		 * @param array $map_options Map Options
		 */
		$map_options = apply_filters( 'gravityview/maps/render/options', $map_options );

		$default_MapOptions = array(
			'backgroundColor' => NULL,
			'center' => NULL,
			'disableDefaultUI' => NULL,
			'disableDoubleClickZoom' => empty( $map_settings['map_doubleclick_zoom'] ),
			'draggable' => !empty( $map_settings['map_draggable'] ),
			'draggableCursor' => NULL,
			'draggingCursor' => NULL,
			'heading' => NULL,
			'keyboardShortcuts' => NULL,
			'mapMaker' => NULL,
			'mapTypeControl' => NULL,
			'mapTypeControlOptions' => NULL,
			'mapTypeId' => strtoupper( $map_settings['map_type'] ),
			'maxZoom' => ! isset( $map_settings['map_maxzoom'] ) ? 16 : intval( $map_settings['map_maxzoom'] ),
			'minZoom' => ! isset( $map_settings['map_minzoom'] ) ? 3 : intval( $map_settings['map_minzoom'] ),
			'noClear' => NULL,
			'overviewMapControl' => NULL,
			'overviewMapControlOptions' => NULL,
			'panControl' => !empty( $map_settings['map_pan_control'] ),
			'panControlOptions' => NULL,
			'rotateControl' => NULL,
			'rotateControlOptions' => NULL,
			'scaleControl' => NULL,
			'scaleControlOptions' => NULL,
			'scrollwheel' => !empty( $map_settings['map_scrollwheel_zoom'] ),
			'streetView' =>  NULL,
			'streetViewControl' => !empty( $map_settings['map_streetview_control'] ),
			'streetViewControlOptions' => NULL,
			'styles' => empty( $map_settings['map_styles'] ) ? NULL : json_decode( $map_settings['map_styles'] ),
			'tilt' => NULL,
			'zoom' => ! isset( $map_settings['map_zoom'] ) ? 15 : intval( $map_settings['map_zoom'] ),
			'zoomControl' => NULL,
			'zoomControlOptions' => NULL,
		);

		/**
		 * Enforce specific Google-available parameters, then remove null options
		 * @uses GravityView_Maps_Render_Map::is_not_null()
		 */
		$map_options['MapOptions'] = array_filter( shortcode_atts( $default_MapOptions, $map_options['MapOptions'] ), array( $this, 'is_not_null' ) );

		unset( $default_MapOptions );

		return $map_options;
	}

	/**
	 * Check whether something is NULL. Used by parse_map_options()
	 *
	 * @see GravityView_Maps_Render_Map::parse_map_options()
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
