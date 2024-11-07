<?php
/*
Plugin Name: WP Go Maps - Gold Add-on
Plugin URI: http://www.wpgmaps.com
Description: This is the Gold add-on for WP Go Maps. This enables mass-marker support (marker clustering)
Version: 5.2.6
Author: WP Go Maps
Author URI: http://www.wpgmaps.com
Requires Plugins: wp-google-maps, wp-google-maps-pro

@copyright 2024 Code Cabin Pty Ltd
*/

/* 
 * Copyright (c) Code Cabin Pty Ltd 2024
*/

/*
 * 5.2.6
 * Added menu icon to map editor for marker clustering (Atlas Novus)
 * Improved caching reliability and performance (beta) (Requires Pro update)
 * Improved error handling within the caching module to prevent unknown errors (beta)
 * Fixed issue where caching settings area would crash if caching failed outright. Division by zero error (beta)
 * Fixed issue where caching settings would show map count instead of marker count. Now shows both along with performance improvements
 * Fixed issue where update controller would not return expected data causing an index error within logs on some environments
 * 
 * 5.2.5
 * Added plugin dependencies
 * Improved tracking devices table layout, specifically when empty
 * Improved cluster and near vicinity marker systems so that they can function side-by-side
 * Fixed issue where map caching (beta) would not respect map ID's 
 * Fixed issue where map caching (beta) would not respect category icons
 * Fixed issue where map caching (beta) would not respect map icons
 * Fixed issue where location tracking marker would not be recreated for a device when manually deleted
 * Fixed issue where location tracking endpoint would sometimes throw an error 
 * 
 * 5.2.4
 * Added support for new (beta) Location Tracking app
 * Added a bridging admin stylesheet to allow us modernize some of the older UI structures
 * Added map caching core modules, UI and controllers (beta)
 * Added support for internal defer loading of script assets (beta)
 * Improved all location tracking REST endpoints
 * Improved device approval flows
 * Improved device manager
 * Improved location tracking core
 * Updated plugin header copyright year
 * 
 * 5.2.3 
 * Fixed issue where deprecation notices are shown in PHP 8.2, including dynamic property creation and function changes
 * Fixed issue where 'require-account' would not apply to ratings controller
 * Improved core code base by refactoring some modules/sections
 * 
 * 5.2.2
 * Plugin header (meta) updated top reflect copyright
 * 
 * 5.2.1
 * Added core V9.0.0 day one patch supports
 * Fixed issue where activating gold before pro would throw a warning/error on some installations 
 * Fixed issue where activating gold and pro without basic would throw a warning/error on some installations
 * Fixed issue where cluster may not be positioned correctly (Requires Basic 9.0.1)
 * 
 * 5.2.0
 * Added support for WPGMZA core V9.0.0 builds (Temporary compatibility)
 * Added support for V9.0.0 UI, but does not add core functionality changes at this stage
 * 
 * 5.1.9
 * Fixed issue where OpenLayers cluster icons would be slightly offset from expected location
 * Fixed issue where animated marker separations would throw infow-window errors
 * Fixed issue where star rating output would not display as intended
 * Updated cluster icon source to point to bundled files instead of externally hosted directory
 *
 * 5.1.8
 * Fixed issue where rating widgets would not initialize in marker supported listings
 * Fixed issue where marker separation module would show outside of search radius when used with store locator
 * Fixed issue where marker separation group would not be expanded when a marker is opened in a listing
 *
 * 5.1.7
 * Added support for marker separation module after filtering, technically a new feature but could be considered a bug
 *
 * 5.1.6
 * Fixed issue with cluster placement in OpenLayers engine
 *
 * 5.1.5
 * Fixed issue where clusters would not place accurately based on the approx. marker positions within the cluster  
 * Fixed issue where minimum cluster size was not being respected
 *
 * 5.1.4 
 * Fixed issue where WPGMZA.MarkerRating was being used instead of WPGMZA.RatingWidget
 * Fixed issue with live tracking frontend module initialization timing
 * Fixed issue with marker separator icon selector initialization in settings area
 * Fixed issue with legacy NVC module initialization timing
 * Fixed issue where clusters would be shown within the map editor
 * Fixed issue where clusters would not initialize correctly when moving between zoom levels in some cases
 * Fixed issue where store locator search would not reinit the clusters
 * Fixed issue where ratings would not be averaged on the frontend
 *
 * 5.1.3
 * Fixed an issue where the live tracking device table would not be created as part of the installation
 * Fixed an issue where device list would not be shown on some installations
 * Fixed an issue where some toggles in the global settings area could not be disabled when initially enabled
 * Fixed an issue where ratings would not be placed as the last item within the info-window
 * Fixed issues with the marker rating settings area, specifically with layout
 *
 * 5.1.2
 * Fixed issues with cluster override options not being saved correctly
 * Fixed an issue with fit to bounds functionality of cluster icons
 * Fixed an issue with OpenLayer clusters snapping to the top left corner of the map
 *
 * 5.1.1
 * Fixed - Live Tracking install function will be executed on page load and not only on installation
 * 
 * 5.1.0
 * Added Core 8.1 Compatibility
 *
 * 5.0.6
 * Fixed fatal error wpgmza_get_basic_version missing when not running Pro add-on
 * Fixed - Duplicate rating when you reopen the IW
 *
 * 5.0.5 :- 2020-04-21
 * Rebuilt out of date JavaScript
 *
 * 5.0.4 :- 2020-03-02
 * Significantly optimised marker separation grouping performance
 * Added new "Maximum group size" setting for marker separator
 *
 * 5.0.3 :- 2020-02-12
 * Fixed some Marker Rating settings strings not passed through translation functions
 * Fixed separation group icons sometimes appearing when all the groups markers are clustered
 * Fixed modern style infowindow opening for separation group placeholders
 * 
 * 5.0.2 :- 2019-10-24
 * Fixed issue with updater not working correctly
 *
 * 5.0.1 :- 2019-10-11
 * Fixed near vicinity marker separation always on, solves performance issues on sites with large numbers of markers
 *
 * 5.0.0 :- 2019-10-08
 * Added new Marker Ratings feature including a range of settings and rating widgets
 * Added new setting for Marker Separator placeholder icon
 * Added new marker separation patterns, line and grid
 * JavaScript modules are now combined and minified for lighter resource footprint and faster load time
 * Removed beta KML importer when Pro >= 7.* is running
 *
 * 4.23 :- 2019-08-20
 * Fixed an issue where separation groups would not open following clicking on a cluster representing the group
 *
 * 4.22 :- 2019-08-15
 * Fixed fatal error when CRUD class is not present (older versions of Basic, or inactive Basic and Pro)
 * Added clusterclick event for map
 *
 * 4.21 :- 2019-08-01
 * Added skipNonceCheck to devices POST REST API endpoint
 * Fixed 403 forbidden when live tracking app transmits position
 *
 * 4.20 :- 2019-07-31
 * Changed device approval REST API methods to register routes through WPGMZA\RestAPI
 * Fixed can't approve devices when running WP Go Maps >= 7.11.40
 *
 * 4.19 :- 2019-07-24
 * Fixed issues with device table when paired marker had been deleted
 * Fixed clusterer closing infowindows on marker listing item click
 *
 * 4.18 :- 2019-06-26
 * Added check for CRUD class to catch errors on installations running legacy basic
 * Removed console logging
 *
 * 4.17 :- 2019-05-29
 * Fixed an issue with device approval when devices have markers which have been deleted
 * Fixed console log being outputted even when WPGMZA.MarkerClusterer.enableDebugLog is false
 *
 * 4.16 :- 2019-05-20
 * Fixed clustering not working around 180th meridian
 *
 * 4.15 :- 2019-03-27
 * Moved live tracking settings to global settings page
 * Added new live tracking modules to support new live tracking app
 * Added new live tracking REST API endpoints
 * Moved map ID setting to live tracking app
 *
 * 4.14 :- 2019-03-15
 * Added code to auto detect plugin version from plugin comment header
 * Prevented markers from being cleared when resetting viewpoint when user is running Pro 7.11 or above
 *
 * 4.13 :- 2019-03-14
 * Fixed separation animation not working after multiple attempts where legacy global infoWindow is not defined
 *
 * 4.12 :- 2019-03-13
 * Added Cluster.prototype.setMarkerVisibility
 * Clustered markers can now only be visible if they are not in a separator group, or if the separator group is open
 * The separator group placeholders visibility is now set to match the markers visibility
 * Fixed markers 0...n set to visible when n is below minimum clustering size
 * Fixed new clustering and separator scripts enqueued without version string
 *
 * 4.11 - 2019-02-21
 * Wrapped old cluster module with native cluster modules
 * Clustering is now supported in OpenLayers
 * Clustering can now be used with Marker Separator / Near Vicinity Markers
 * Fixed cluster count wrong when using Pro >= 7.10.*
 * Fixed clustering incorrect when filters are applied when using Pro >= 7.11.*
 *
 * 4.10 - 2019-01-18
 * Fixed second map not initialising when running Gold due to new markersplaced.wpgmza event firing too soon
 *
 * 4.09 - 2019-01-17
 * Fixed setIcon is not a function for placeholder icon when running Pro < 7
 *
 * 4.08 - 2019-01-09
 * Added new modern marker separation modules for near marker vicinity feature
 * Added control to switch back to legacy module where needed
 * Added link where beta near vicinity settings were to new Marker Separation tab
 * Added zoom-independent marker separation through new module
 * Added animated marker separation through new module
 * Added backward compatibility for Pro 6 to new module
 * Moved near vicinity marker controls into "Marker Separation" tab
 * Fixed near marker vicinity not working with newer versions of Basic
 * Fixed near marker vicinity zooming to the wrong level on click
 * Dropped redundant zoom-to-fit for new Marker Separation module
 * Dropped "webbing" lines for new Marker Separation in light of zoom independence, these may be re-added
 *
 * 4.07 - 2018-10-24
 * Handed over control of API enqueue to Basic (removed JS API loading tags)
 * Fixed multiple API message when running Gold
 * Fixed polyline route feature not updating correctly
 *
 * 4.06 - 2018-09-24
 * Fix undefined index in updater code
 * 
 * 4.05 - 2018-09-06
 * Fixed incompatibilities with Pro 6.*
 *
 * 4.04 - 2018-08-22
 * Fixed NVC markers not opening on click with WPGM >= 7.10.*
 * Removed calls to deprecated jQuery load
 *
 * 4.03 - 2017-01-19
 * Added KML importer functionality (still in beta)
 * 
 * 4.02
 * Added full screen control support
 * Added Near-Vicinity Marker Control
 * Added Clustering Global Options
 * 
 * 4.01 - 2016-10-21 - Medium Priority
 * Fixed a JS error in the map editor
 * 
 * 4.00 - 2016-06-27
 * Real time location tracking enabled
 * Moved the menu into a tab within the map editor
 * Marker clustering icons fixed
 * 
 * 3.33 - 2016-04-04
 * Fixed a bug that caused the theme to not display on the front-end
 * 
 * 3.32 - 2016-01-07 - High priority
 * Fixed a bug that caused the map to not display with the new versions of the basic and pro (theme issue)
 * Removed map styling/theme functionality as it is now defaulted in the basic version
 * SSL bug fix
 * 
 * 3.31 - 2015-09-07 - High priority
 * Fixed a bug that caused the filtering of markers to not work correctly when mass marker support was enabled
 * Fixed bugs in the map editor
 * New map widget functionality
 * Refactored some of the JS code to ensure it is in line with the latest Pro version
 * 
 * 3.30 - 2015-08-20 - High priority
 * Fixed a bug that broke the map editor within WordPress 4.3
 * 
 * 3.29
 * PHP Notices fixed
 * Database option now works in Gold
 * Retina marker settings are now applied in the back end map editor
 * Retina marker custom sizes are now supported in the back end
 *
 * 3.28
 * Right click to add marker bug fixed
 * PHP Notices fixed
 * 
 * 3.27 - Low priority update
 * Changed update URL
 *
 * 3.26
 * Fixed approve marker bug
 * 
 * 3.25
 * Added support for the new marker pull method
 * 
 * 3.24
 * Approving of VGM markers bug fixed
 * 
 * 3.23
 * Fixed bug that copied one map style to another map style if there is more than one map on a page
 * Multiple category per marker support functionality added
 * 
 * 3.22
 * Code improvement
 * 
 * 3.21
 * Small bug fixes & code improvement
 * 
 * 3.20
 * Added the option to select which API version you would like to use
 * 
 * 3.19
 * Added weather, cloud and transit layer
 * 
 * 3.18
 * Small bug fix
 * 
 * 3.17
 * Compatible with basic version 6
 * 
 * 3.16
 * Fixed small bug with resetting select boxes within the add marker section
 * 
 * 3.15
 * Fixed a bug that stopped you from deleting polylines in the Gold add-on
 * All front end JS is now included in it's own file
 * 
 * 3.14
 * Added a check to see if the Google Maps API was already loaded to avoid duplicate loading
 * Fixed the mouse scroll wheel bug
 * Fixed some SSL bugs
 * Advanced marker list now updates with category drop down selection
 *
 * 3.13
 * Fixed a small bug with the categories
 *
 * 3.12
 * Added category functionality
 * Fixed a click bug with the marker listing
 *
 * 3.11
 * You can now show your visitors location on the map
 * Added polygon functionality
 * Added polyline functionality
 * Markers can now be sorted by id,title,description or address
 * Added better support for jQuery versions
 * Adjusted the KML functionality to avoid caching
 * Fixed a bug that stopped the advanced marker listing from working
 * 
 * 3.10
 * Fixed a bug that didnt allow for multiple clicks on the marker list to bring the view back to the map
 * 
 * 3.09
 * Fixed a dataTables bug
 * 
 * 3.08
 * This version allows the plugin to update itself moving forward
 * 
 * 3.07
 * Added troubleshooting support
 * 
 * 3.06
 * Fixed some small bugs    
 * 
 * 3.05
 * Fixed a IE9 display bug
 * Added support for jQuery1.9+
 * Fixed some bugs
 * Added support for one-page-style themes.
 * 
 * 3.04
 * Fixed bug whereby you couldnt disable mass marker support
 *
 * 3.03
 * Added responsive size functionality
 * You can now enable and disable mass marker support
 * Added support code for the new WP Go Maps User Generated Markers plugin
 * Added the option for a more advanced way to list your markers below your maps
 * Added support for Fusion Tables
 * 
 * 3.02
 * Fixed the bug that caused the directions box to show above the map by default
 * Fixed the bug whereby an address was already hard-coded into the "To" field of the directions box
 * Fixed the bug that caused the traffic layer to show by default
 *
 * 3.01
 * Added the functionality to list your markers below your map
 * Added more advanced directions functionality
 * Fixed small bugs
 * Fixed a bug that caused a fatal error when trying to activate the plugin on some hosts.
 *
 * 3.0
 * Plugin now supports multiple maps on one page (there is a known issue on the Gold add-on that shows another maps markers on the map your are on when using the zoom in/out function. I am working on this.
 * Bicycle directions now added
 * Walking directions now added
 * "Avoid tolls" now added to the directions functionality
 * "Avoid highways" now added to directions functionality
 * New setting: open links in a new window
 * Added functionality to reset the default marker image if required.
 *
 * 2.8
 * Fixed the bug that was causing both the bicycle layer and traffic layer to show all the time
 *
 * 2.7
 * Added traffic layer
 * Added bicycle layer
 *
 * 2.6
 * Added additional map settings
 * Added support for KML/GeoRSS layers.
 *
 * 2.5
 * Markers now automatically close when you click on another marker.
 * Russian localization added
 * The "To" field in the directions box now shows the address and not the GPS co-ords.
 *
 * 2.4
 * Added support for localization
 *
 * 2.3
 * Fixed the bug that caused slow loading times with sites that contain a high number of maps and markers
 *
 * 2.2
 * Added functionality for 'Titles' for each marker
 *
 * 2.1
 * Added functionality for WordPress MU
 *
 * 2.0
 * Added Map Alignment functionality
 * Added Map Type functionality
 * Started using the Geocoding API Version 3  instead of Version 2 - quicker results!
 * Fixed bug that didnt import animation data for CSV files
 * fixed zoom bug
 *
 * 1.1
 * Added support for advanced styling
 * Fixed a few bugs with the jQuery script
 * Fixed the shortcode bug where the map wasnt displaying when two or more short codes were one the post/page
 * Fixed a bug that wouldnt save the icon on editing a marker in some instances
 *
 *
 * 
 */

global $wpgmza_gold_version;
$wpgmza_gold_version = null;
$subject = file_get_contents(__FILE__);
if(preg_match('/Version:\s*(.+)/', $subject, $m))
	$wpgmza_gold_version = trim($m[1]);

define('WPGMZA_GOLD_VERSION', $wpgmza_gold_version);

function wpgmza_gold_php_version_notice()
{
	?>
	<div class="notice notice-error">
		<p>
			<?php
			_e('<strong>WP Go Maps Gold Add-on:</strong> You are running PHP version 5.2 or below, which is no longer supported by WP Go Maps and WP Go Maps Gold Add-on. Please switch to version 5.3 or above. Please speak to your host if you are unsure how to switch PHP versions.', 'wp-google-maps');
			?>
		</p>
	</div>
	<?php
}

function wpgmza_gold_basic_version_notice()
{
	?>
	<div class="notice notice-error">
		<p>
			<?php
			_e('<strong>WP Go Maps Gold Add-on:</strong> This add-on requires WP Go Maps 7.0 or above. Please update WP Go Maps to use the Gold add-on. You can force a check for updates by going to Updates in your Dashboard menu, and clicking "Check Again".', 'wp-google-maps');
			?>
		</p>
	</div>
	<?php
}

if(!function_exists('wpgmza_require_once'))
{
	function wpgmza_require_once($filename)
	{
		if(!file_exists($filename))
			throw new Exception("Fatal error: wpgmza_require_once(): Failed opening required '$filename'");
		
		require_once($filename);
	}
}

wpgmza_require_once(plugin_dir_path(__FILE__) . 'constants.php');

if(!function_exists('wpgmza_show_rest_api_missing_error'))
{
	function wpgmza_show_rest_api_missing_error()
	{
		?>
		<div class="notice notice-error">
				<p>
					<?php
					_e('<strong>WP Go Maps:</strong> This plugin requires the WordPress REST API, which does not appear to be present on this installation. Please update WordPress to version 4.7 or above.', 'wp-google-maps');
					?>
				</p>
			</div>
		<?php
	}

	if(!function_exists('get_rest_url'))
	{
		add_action('admin_notices', 'wpgmza_show_rest_api_missing_error');
		return;
	}
}

function wpgmza_gold_on_init()
{
	if(!class_exists('WPGMZA\\DOMDocument') || !class_exists('WPGMZA\\Crud') || !class_exists('WPGMZA\\DOMQueryResults')){
		add_action('admin_notices', 'wpgmza_gold_basic_version_notice');
	} else {
		require_once( plugin_dir_path(__FILE__) . 'includes/class.marker-separator-settings.php' );
	}
}

add_action('init', 'wpgmza_gold_on_init');

global $wpgmza_gold_cached_basic_dir;

function wpgmza_gold_get_basic_dir()
{
	global $wpgmza_gold_cached_basic_dir;
	
	if($wpgmza_gold_cached_basic_dir)
		return $wpgmza_gold_cached_basic_dir;
	
	if(defined('WPGMZA_DIR_PATH'))
		return WPGMZA_DIR_PATH;
	
	$plugin_dir = plugin_dir_path(__DIR__);
	
	// Try default folder name first
	$file = $plugin_dir . 'wp-google-maps/wpGoogleMaps.php';
	
	if(file_exists($file))
	{
		$wpgmza_gold_cached_basic_dir = plugin_dir_path($file);
		return $wpgmza_gold_cached_basic_dir;
	}
	
	// Scan plugins
	$plugins = get_option('active_plugins');
	foreach($plugins as $slug)
	{
		if(preg_match('/wpGoogleMaps\.php$/', $slug))
		{
			$file = $plugin_dir . $slug;
			
			if(!file_exists($file))
				return null;
			
			$wpgmza_gold_cached_basic_dir = plugin_dir_path($file);
			return $wpgmza_gold_cached_basic_dir;
		}
	}
	
	return null;
}

function wpgmza_gold_get_basic_version()
{
	global $wpgmza_version;
	
	// Try already loaded
	if($wpgmza_version)
		return trim($wpgmza_version);
	
	if(defined('WPGMZA_VERSION'))
		return trim(WPGMZA_VERSION);
	
	$dir = wpgmza_gold_get_basic_dir();
	
	if(!$dir)
		return null;
	
	$file = $dir . 'wpGoogleMaps.php';
	
	if(!file_exists($file))
		return null;
	
	// Read version strintg
	$contents = file_get_contents($file);
		
	if(preg_match('/Version:\s*(.+)/', $contents, $m))
		return trim($m[1]);
	
	return null;
}

function wpgmza_gold_get_required_basic_version()
{
	return '8.0.0';
}

function wpgmza_gold_is_basic_compatible()
{
	$basic_version = wpgmza_gold_get_basic_version();
	$required_version = wpgmza_gold_get_required_basic_version();
	
	return version_compare($basic_version, $required_version, '>=');
}

function wpgmza_gold_show_basic_incompatible_notice()
{
	$basic_version = wpgmza_gold_get_basic_version();
	$required_version = wpgmza_get_required_basic_version();
	$gold_version = WPGMZA_GOLD_VERSION;
	
	$notice = '
	<div class="notice notice-error">
		<p>
			' .
			__(
				sprintf(
					'<strong>WP Go Maps Gold:</strong> Gold add-on %s requires WP Go Maps to be activated, the minimum required version of WP Go Maps is version %s. Please update the basic plugin to version %s to use WP Go Maps Gold %s', 
					$gold_version,
					$required_version,
					$required_version,
					$gold_version
					),
				'wp-google-maps'
			) . '
		</p>
	</div>
	';
	
	echo $notice;
}

if(version_compare(PHP_VERSION, '5.3', '<'))
{
	add_action('admin_notices', 'wpgmza_gold_php_version_notice');
	
	return;
}

if(!wpgmza_gold_is_basic_compatible())
{
	add_action('admin_notices', 'wpgmza_gold_show_basic_incompatible_notice');
	return;
}

if(!file_exists(wpgmza_gold_get_basic_dir() . 'includes/class.crud.php'))
	return;

require_once(plugin_dir_path(__FILE__) . 'legacy-core.php');