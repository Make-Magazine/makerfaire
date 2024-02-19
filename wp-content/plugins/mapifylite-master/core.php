<?php
// Load the textdomain - we are inside the plugins_loaded hook so we just call directory
$dir = dirname( plugin_basename( MAPIFY_PLUGIN_FILE ) ) . DIRECTORY_SEPARATOR . 'languages';
load_plugin_textdomain('mpfy', false, $dir);

add_action('init', 'mpfy_plugin_init', 100);
function mpfy_plugin_init() {
	// Load the mapify post types
	include_once(MAPIFY_PLUGIN_DIR . '/options/post-types.php');

	// Flush rewrite rules after an update to make sure that the custom post types are included in rewrite rules
	if (get_option('mpfy_flush_required') === 'y') {
		update_option('mpfy_flush_required', 'n');

		if (function_exists('flush_rewrite_rules')) {
			flush_rewrite_rules();
		} else {
			add_action('wp', 'flush_rewrite_rules');
		}
	}

	// enqueue generic dependencies
	wp_enqueue_script('jquery');
}

// Enqueue front-end assets
$mpfy_footer_scripts = '';
add_action('wp_footer', 'mpfy_enqueue_assets');
function mpfy_enqueue_assets() {
	global $mpfy_footer_scripts;

	if (!defined('MPFY_LOAD_ASSETS') || is_admin()) {
		return false;
	}

	wp_enqueue_style( 'leaflet', plugins_url( 'assets/vendor/leaflet-1.7.1/leaflet.css', MAPIFY_PLUGIN_FILE ), array(), '1.7.1' );
	wp_enqueue_style( 'leaflet-markercluster', plugins_url('assets/vendor/leaflet/markercluster/MarkerCluster.css', MAPIFY_PLUGIN_FILE), array( 'leaflet' ), '1.0.5' );
	wp_enqueue_style( 'leaflet-markercluster-default', plugins_url('assets/vendor/leaflet/markercluster/MarkerCluster.Default.css', MAPIFY_PLUGIN_FILE), array( 'leaflet' ), '1.0.5' );
	wp_enqueue_style( 'leaflet-locatecontrol', plugins_url('assets/vendor/leaflet/locatecontrol/L.Control.Locate.min.css', MAPIFY_PLUGIN_FILE), array( 'leaflet' ), '0.61.0' );

	wp_enqueue_style( 'montserrat-font', '//fonts.googleapis.com/css?family=Montserrat' );
	wp_enqueue_style( 'mapify-map', plugins_url('assets/map.css', MAPIFY_PLUGIN_FILE), array(), MAPIFY_PLUGIN_VERSION . '.3' );	
	wp_enqueue_style( 'slick-slider', plugins_url('assets/vendor/slick/slick.css', MAPIFY_PLUGIN_FILE), array(), MAPIFY_PLUGIN_VERSION );
	wp_enqueue_style( 'slick-slider-theme', plugins_url('assets/vendor/slick/slick-theme.css', MAPIFY_PLUGIN_FILE), array(), MAPIFY_PLUGIN_VERSION );
	wp_enqueue_style( 'mapify-popup', plugins_url('assets/popup.css', MAPIFY_PLUGIN_FILE), array(), MAPIFY_PLUGIN_VERSION . '.3' );

	wp_enqueue_script('leaflet', plugins_url( 'assets/vendor/leaflet-1.7.1/leaflet.js', MAPIFY_PLUGIN_FILE ), array(), '1.7.1', true);
	wp_enqueue_script('leaflet-bouncemaker', plugins_url( 'assets/vendor/leaflet/bouncemarker.js', MAPIFY_PLUGIN_FILE ), array( 'leaflet' ), '1.0.0', true );
	wp_enqueue_script('leaflet-markercluster', plugins_url( 'assets/vendor/leaflet/markercluster/leaflet.markercluster.js', MAPIFY_PLUGIN_FILE ), array( 'leaflet' ), '1.0.5', true );
	wp_enqueue_script('leaflet-locatecontrol', plugins_url( 'assets/vendor/leaflet/locatecontrol/L.Control.Locate.min.js', MAPIFY_PLUGIN_FILE ), array( 'leaflet' ), '0.61.0', true );

	wp_enqueue_script('carouFredSel', plugins_url('assets/vendor/jquery.carouFredSel-6.2.1-packed.js', MAPIFY_PLUGIN_FILE), array(), false, true);
	wp_enqueue_script('slick', plugins_url('assets/vendor/slick/slick.min.js', MAPIFY_PLUGIN_FILE), array(), false, true);
	wp_enqueue_script('mousewheel', plugins_url('assets/vendor/jquery.mousewheel.js', MAPIFY_PLUGIN_FILE), array(), false, true);

	wp_enqueue_script('mapify-tooltip', plugins_url('assets/vendor/tooltip.js', MAPIFY_PLUGIN_FILE), array( 'jquery' ), false, true);
	wp_enqueue_script('mapify', plugins_url('assets/js/dist/bundle.js', MAPIFY_PLUGIN_FILE), array( 'jquery' ), MAPIFY_PLUGIN_VERSION . '.3', true);
	wp_localize_script('mapify', 'mapify_script_settings', array(
		'strings'=>array(
			'no_search_results'=>'<p>' . __('No locations were found.', 'mpfy') . '<br />' . __('Please search again.', 'mpfy') . '</p>',
			'no_search_results_with_closest'=>'<p>' . __('No locations were found within your search criteria. Please search again.', 'mpfy') . '</p><p class="mpfy-or-text">' . __('Or ...', 'mpfy') . ' <a href="#" class="mpfy-closest-pin">' . __('See the Closest Location', 'mpfy') . '</a></p>',
			'search_geolocation_failure'=>'<p>' . __('Could not find the entered address.', 'mpfy') . '<br />' . __('Please check your spelling and try again.', 'mpfy') . '</p>',
		),
	));

	// load popup html
	include_once(MAPIFY_PLUGIN_DIR . '/templates/popup.php');

	// Add WP ajax url to the global JS scope for easy access
	?>
	<script type="text/javascript">
	window.wp_ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
	</script>
	<?php echo $mpfy_footer_scripts; ?>
	<?php
}

add_action('admin_enqueue_scripts', 'mpfy_plugin_admin_assets', 100);
function mpfy_plugin_admin_assets() {
	wp_enqueue_style( 'mapify-admin-css', plugins_url( 'assets/admin.css' , MAPIFY_PLUGIN_FILE ) );
}

// Add general Mapify shortcode
add_shortcode('custom-mapping', 'mpfy_shortcode_custom_mapping');
function mpfy_shortcode_custom_mapping($atts, $content) {
	global $mpfy_footer_scripts;
	static $mpfy_instances = -1;
	$mpfy_instances ++;

	if (!defined('MPFY_LOAD_ASSETS')) {
		define('MPFY_LOAD_ASSETS', true);
	}

	extract( shortcode_atts( array(
		'width'=>0,
		'height'=>300,
		'map_id'=>0,
	), $atts));

	if (!stristr($width, '%')) {
		$width = intval($width);
		$width = ($width < 1) ? 0 : $width . 'px';
	}

	if (!stristr($height, '%')) {
		$height = intval($height);
		$height = ($height < 1) ? 300 : $height . 'px';
	}

	if ($map_id == 0) {
		$map_id = Mpfy_Map::get_first_map_id();
	}

	$map = get_post(intval($map_id));
	if (!$map || is_wp_error($map) || $map->post_type != 'map') {
		return 'Invalid or no map_id specified.';
	}

	$map = new Mpfy_Map($map->ID);

	$template = include('templates/map.php');
	$mpfy_footer_scripts .= $template['script'];
	return $template['html'];
}

// Add compatibility with previous version of the plugin
if ( !function_exists('cm_shortcode_custom_mapping') ) {
	function cm_shortcode_custom_mapping() {
		return call_user_func_array('mpfy_shortcode_custom_mapping', func_get_args() );
	}
}

function mpfy_get_single_template( $post ) {
	$template = '';

	if ( ! isset( $post->post_type ) ) {
		return $template;
	}

	if ($post->post_type == 'map-location') {
		$template = MAPIFY_PLUGIN_DIR . '/templates/single-map-location.php';
	}

	if (MPFY_IS_AJAX && in_array($post->post_type, mpfy_get_supported_post_types())) {
		$map_location = new Mpfy_Map_Location($post->ID);
		if ($map_location->get_maps()) {
			$template = MAPIFY_PLUGIN_DIR . '/templates/single-map-location.php';
		}
	}

	return $template;
}

// Apply proper template to valid location post types so that WP does not take a generic template from the theme
add_filter('template_include', 'mpfy_filter_single_template', 1000);
function mpfy_filter_single_template($template) {
	global $post;

	$mpfy_template = mpfy_get_single_template( $post );
	if ( $mpfy_template ) {
		return $mpfy_template;
	}

	return $template;
}

// Same sa mpfy_filter_single_template() but override template loading in case thesis is the site theme
add_filter('template_include', 'mpfy_filter_single_template_thesis', 9);
function mpfy_filter_single_template_thesis($template) {
	global $post, $thesis;
	$mpfy_template = mpfy_get_single_template( $post );
	if ( $thesis && $mpfy_template ) {
		include( $mpfy_template );
		exit;
	}
	return $template;
}

// Duplicates location meta so it's easier to query
add_action('save_post', 'mpfy_action_duplicate_location_map_meta', 11);
function mpfy_action_duplicate_location_map_meta($post_id) {
	$post = get_post($post_id);

	if (!in_array($post->post_type, mpfy_get_supported_post_types())) {
		return;
	}

	mpfy_duplicate_location_map_meta($post_id);
}

add_action('added_post_meta', 'mpfy_action_duplicate_location_map_meta_on_meta_update', 10, 4);
add_action('updated_post_meta', 'mpfy_action_duplicate_location_map_meta_on_meta_update', 10, 4);
function mpfy_action_duplicate_location_map_meta_on_meta_update($meta_id, $object_id, $meta_key, $_meta_value) {
	if ( $meta_key != '_map_location_map' ) {
		return;
	}
	mpfy_duplicate_location_map_meta($object_id);
}

// allows proper and fast location queries at the cost of data duplication through meta. Not ideal but a needed compromise to avoid having to deal with writing custom field, datastores and complex field implementations etc. for this specific scenario
function mpfy_duplicate_location_map_meta($post_id) {
	$location = new Mpfy_Map_Location($post_id);
	$maps = $location->get_maps();

	delete_post_meta($post_id, '_map_location_map_id');
	foreach ($maps as $map_id) {
		add_post_meta($post_id, '_map_location_map_id', $map_id);
	}
}
