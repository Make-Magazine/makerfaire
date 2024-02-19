<?php

use Acf_Mapifypro\Model\Mapify_Map;

include_once( 'lib/utilities.php' );
include_once( 'lib/route.php' );
include_once( 'lib/wpml-compatibility.php' );
include_once( 'options/carbon-datastore.php' );
include_once( 'options/carbon-functions.php' );
include_once( 'options/class-prettyroutes-acf.php' );

/**
 * Load frontend scripts & styles
 */
function routes_enqueue_assets() {
	if (!defined('MPFY_PRETTYROUTES_LOAD_ASSETS') || is_admin()) {
		return false;
	}

	wp_enqueue_style( 'leaflet', plugins_url('assets/vendor/leaflet-1.7.1/leaflet.css', MAPIFY_PLUGIN_FILE), array(), '1.7.1' );
	wp_enqueue_style( 'prettyroutes', plugins_url( 'assets/style.css' , __FILE__ ) );

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('leaflet', plugins_url( 'assets/vendor/leaflet-1.7.1/leaflet.js', MAPIFY_PLUGIN_FILE ), array( 'jquery' ), '1.7.1', true );
	wp_enqueue_script( 'prettyroutes', plugins_url( 'assets/js/dist/bundle.js', PRETTYROUTES_PLUGIN_FILE ), array( 'jquery' ), mpfy_get_cache_buster_version(), true );
	wp_localize_script( 'prettyroutes', 'prettyroutes_script_settings', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'routes_get_route' ),
		'strings'  => array(),
	) );
	
	// Prevent Lodash conflict with Underscore.
	wp_add_inline_script( 'prettyroutes', 'window.lodash = _.noConflict();', 'after' );
}
add_action( 'wp_footer', 'routes_enqueue_assets' );

/**
 * Load admin scripts & styles
 */
function routes_enqueue_admin_assets() {
	wp_enqueue_style( 'leaflet', plugins_url('assets/vendor/leaflet-1.7.1/leaflet.css', MAPIFY_PLUGIN_FILE), array(), '1.7.1' );
	wp_enqueue_style( 'leaflet-routing-machine', plugins_url( 'assets/vendor/leaflet-routing-machine/leaflet-routing-machine.css' , PRETTYROUTES_PLUGIN_FILE ), array( 'leaflet' ), '3.2.5' );
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('leaflet', plugins_url( 'assets/vendor/leaflet-1.7.1/leaflet.js', MAPIFY_PLUGIN_FILE ), array( 'jquery' ), '1.7.1', true );
	wp_register_script( 'prettyroutes-admin', plugins_url( 'assets/js/dist/bundle-admin.js', PRETTYROUTES_PLUGIN_FILE ), array( 'leaflet' ), PRETTYROUTES_PLUGIN_VERSION, true );
	wp_localize_script( 'prettyroutes-admin', 'wp_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'prettyroutes-admin' );
}
add_action( 'admin_enqueue_scripts', 'routes_enqueue_admin_assets', 100 );

/**
 * Register post type
 */
function routes_attach_custom_fields() {
	include_once( 'options/post-types.php' );
}
add_action( 'init', 'routes_attach_custom_fields', 1000 );

/**
 * Register shortcode [route-map]
 */
function routes_shortcode_route_map($atts, $content) {
	static $routes_instances = -1;
	$routes_instances ++;

	if (!defined('MPFY_PRETTYROUTES_LOAD_ASSETS')) {
		define('MPFY_PRETTYROUTES_LOAD_ASSETS', true);
	}

	extract( shortcode_atts( array(
		'width'  => 0,
		'height' => 300,
		'map_id' => 0,
	), $atts ) );

	$width  = intval( $width );
	$width  = ( $width < 1 ) ? 0 : $width;
	$height = intval( $height );
	$height = ( $height < 1 ) ? 300 : $height;
	$map    = get_post( intval( $map_id ) );

	if ( ! $map || is_wp_error( $map ) || $map->post_type != 'route_map' ) {
		return 'Invalid or no map_id specified.';
	}

	ob_start();
	include('templates/map.php');
	$cnt = ob_get_clean();
	$cnt = preg_replace( '~>\s*<~s', '><', $cnt );
	$cnt = preg_replace( '~\s*<br \/>\s*~i', '<br />', $cnt );

	return $cnt;
}
add_shortcode( 'route-map', 'routes_shortcode_route_map' );

/**
 * Load routes
 */
function routes_filter_load_routes( $routes, $map_id ) {
	$mapify_map = new Mapify_Map( $map_id );
	$route_ids  = $mapify_map->get_route_ids();
	$routes     = array();

	if ( $route_ids ) {
		foreach ( $route_ids as $route_id ) {
			$routes[] = PrettyRoutes_Route::load( $route_id );
		}
	}

	return $routes;
}
add_filter( 'pretty_routes_load_routes', 'routes_filter_load_routes', 10, 2 );

/**
 * Admin menu
 */
function routes_hide_add_new() {
    global $submenu;

	if ( isset( $submenu['edit.php?post_type=route'][5] ) ) {
		$submenu['edit.php?post_type=route'][5][0] = mpfy_get_icon( 'routes' ) . 'Routes';
		unset($submenu['edit.php?post_type=route'][10]);
	}
	
	if ( isset( $submenu['edit.php?post_type=route'][15] ) && 'Location Tags' === $submenu['edit.php?post_type=route'][15][0] ) {
		add_submenu_page( 'edit.php?post_type=route', 'Location Tags', mpfy_get_icon( 'location-tags' ) . 'Location Tags', 'manage_categories', 'edit-tags.php?taxonomy=location-tag' );
		unset($submenu['edit.php?post_type=route'][15]);
	}
}
add_action('admin_menu', 'routes_hide_add_new');

/**
 * Get cached route
 */
function routes_get_cached_route() {
	$nonce = ! empty( $_POST['nonce'] ) ? $_POST['nonce'] : '';
	if ( ! wp_verify_nonce( $nonce, 'routes_get_route' ) ) {
		return wp_send_json_error( [ 'error' => __( 'Invalid nonce.' ) ] );
	}

	$waypoints = ! empty( $_POST['waypoints'] ) ? $_POST['waypoints'] : [];
	if ( empty( $waypoints ) ) {
		return wp_send_json_error( [ 'error' => __( 'Invalid route waypoints.' ) ] );
	}

	$coords = routes_serialize_waypoints( $waypoints );
	$transient_name = 'routes_mapbox_' . md5( $coords );

	$routes = get_transient( $transient_name );
	if ( $routes === false ) {
		$routes = routes_get_mapbox_route( $waypoints );
		if ( empty( $routes ) ) {
			return wp_send_json_error( [ 'error' => __('Error while fetching routes.') ]);
		}
		set_transient( $transient_name, $routes, WEEK_IN_SECONDS );
	}

	return wp_send_json_success($routes);
}
add_action( 'wp_ajax_routes_get_route', 'routes_get_cached_route' );
add_action( 'wp_ajax_nopriv_routes_get_route', 'routes_get_cached_route' );

function routes_serialize_waypoints( $waypoints ) {
	$coords = array_map( function( $waypoint ) {
		// reverse lat and lng, because the API expects them in the order [lng, lat]
		return implode( ',', array_reverse( $waypoint ) );
	}, $waypoints );
	return implode( ';', $coords );
}

function routes_get_mapbox_route( $waypoints ) {
	$coords = routes_serialize_waypoints( $waypoints );
	$url    = sprintf( 'https://api.mapbox.com/directions/v5/mapbox/driving/%s', $coords );
	$url    = add_query_arg( array(
		'overview'     => 'false',
		'alternatives' => 'true',
		'steps'        => 'true',
		'access_token' => 'pk.eyJ1IjoianNlYXJzMzEiLCJhIjoiY2o3bG5obHZqMmdvcDJxcW15bzFpdTB5NSJ9.lfom0YaF2Siy0-1T0y-EJw'
	), $url );

	$response = wp_remote_get( $url );
	if ( is_wp_error( $response ) ) {
		return null;
	}

	$result = json_decode( $response['body'], true );
	return ! empty( $result ) ? $result : null;
}

/**
 * Route maps legacy notification message
 */
function routes_legacy_admin_notices() {
	$screen       = function_exists( 'get_current_screen' ) ? get_current_screen() : array();
	$is_route_map = $screen && isset( $screen->post_type ) && 'route_map' === $screen->post_type;

	if ( ! $is_route_map ) {
		return;
	}

	?>
	<div class="notice notice-info mapifypro-notice">
		<p>
		<?php
			printf(
				esc_html__( '%sMapifyPro:%s We recommend you use %sMapifyPro Maps%s for PrettyRoutes for a wider range of features.', 'mpfy' ),
				'<strong>',
				'</strong>',
				"<a href='" . esc_url( get_admin_url() . 'edit.php?post_type=map' ) . "'>",
				'</a>'
			);
		?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'routes_legacy_admin_notices' );
