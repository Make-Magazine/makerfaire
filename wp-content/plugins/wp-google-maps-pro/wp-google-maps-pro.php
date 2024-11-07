<?php
/*
Plugin Name: WP Go Maps - Pro Add-on
Plugin URI: http://www.wpgmaps.com
Description: This is the Pro add-on for WP Go Maps. The Pro add-on enables you to add descriptions, pictures, links and custom icons to your markers as well as allows you to download your markers to a CSV file for quick editing and re-upload them when complete.
Version: 9.0.32
Author: WP Go Maps
Author URI: http://www.wpgmaps.com
Requires Plugins: wp-google-maps

@copyright 2024 Code Cabin Pty Ltd 
*/

/* 
 * Copyright (c) Code Cabin Pty Ltd 2024
*/

/*
 * 9.0.32 - 2024-10-25
 * Fixed issue where simple shortcodes would not function as expected within info-windows
 * Fixed issue where old image references were made in CSS file, which could lead to issues with preloading assets
 * 
 * 9.0.31 - 2024-10-14
 * Added a note to the Woo Commerce product editor setting explaining that it does not work with the beta Product editor (Atlas Novus)
 * Added ability to disable carousel marker listing autoplay within the settings area
 * Added datatables performance mode option which excludes some columns from the search (Performance)
 * Fixed issue where import schedule would be autoloaded, which could lead to performance issues in some cases. Will self resolve on next schedule update
 * Fixed issue with divi frontend builder loading our modules when they are not needed
 * Fixed issue with Airtable importer where array field values would not be reduced, and caused import issues
 * Fixed issue with Airtable importer where additional columns would not be ignored when invalid 
 * Fixed issue where mashup marker listings would only load markers from the primary/parent map
 * Fixed issue where grip marker listing would not function with mashups
 * Fixed issue where store locator title search placholder would not update as expected (Atlas Novus)
 * Fixed issue where marker icons were being refreshed by a second database query when loaded to the map, which leads to slower load times. Avg 35% load improvement for large datasets (Performance)
 * Fixed issue where marker custom fields would be queried twice during marker initialization, which leads to slower load times. Avg 25% load improvement for large datasets (Performance)
 * Fixed issue where batch importer would run in the background on any page, including REST and frontend, which could lead to slower load times (Performance)
 * Fixed issue where ratings module would be initialized on each marker, without checking if they were enabled. Only relevant with Gold add-on. Avg 13% load improvement for large datasets (Performance)
 * Fixed issue where store locator search would run on a map without a store locator, if another map is present on the page, with store locator enabled
 * Fixed issue where legacy V7 data migrator was running on every map load, this is handled by basic, on upgrade. (Performance)
 * Moved marker listing pagination style option to the datatables settings panel within the map editor to prevent confusion
 * 
 * 9.0.30 - 2024-06-13
 * Added ability to enable marker labels (beta)
 * Added support for AdvancedMarkerElement module, improving performance slightly (beta) (Google Maps)
 * Added ability to use excerpt for Woo Product markers, instead of full description (Atlas Novus)
 * Added ability to use excerpt for ACF markers, instead of full description (Atlas Novus)
 * 
 * 9.0.29 - 2024-05-08
 * Added ability to load most marker listings without a map on a page, using a standalone shortcode (beta) (Atlas Novus)
 * Added ability to use standalone category filter, without a map, where a standalone marker listing is present (Atlas Novus)
 * Added ability to set a custom image placeholder for marker listings (basic table) from settings area (Atlas Novus)
 * Added ability to set marker field filter query to strict mode instead of partial result
 * Added contentshift trigger to OpenLayers info-windows when gallery loads
 * Added shape creation tools to marker creator (Marker Creator V2)
 * Added ability to load additional ACF fields into the marker description within info-windows (beta) (Atlas Novus)
 * Added ability to disable auto page scrolling for marker listing items, within global settings
 * Added ability to use WooCommerce product categories as a category source (beta) (Atlas Novus)
 * Added plugin dependencies
 * Fixed issue where Thrive template builder would no longer allow blocks to added when Pro is active
 * Fixed issue where carousel marker listing would throw a nullable string warning in some PHP versions
 * Fixed issue where some marker listings would not initialize from standalone shortcodes (Atlas Novus)
 * Fixed issue where markers and ratings export options were swapped (Legacy)
 * Fixed issue where writersblock editor would not reset to visual mode between edits (Atlas Novus)
 * Fixed issue where Woo checkout map setting would throw a warning when the map no longer exists
 * Fixed issue where shape info-windows would open even without content
 * Fixed issue where image overlays would prevent map clicks, they no bubble to map
 * Fixed issue where WooCommerce product location meta box would not work with Vendor Extension
 * Fixed issue where WooCommerce product query may break due to apostrophe storage. We remove these for now, with a long term solution in the pipeline
 * Fixed issue where WooCommerce product storage would sometimes trigger when specific extensions were enabled, clearing location data
 * Removed console warning when info-windows are opened for shapes, regarding distance calculations
 * 
 * 9.0.28 - 2024-03-25
 * Fixed issue with Gutenberg focus marker background lookups
 * Fixed issue where marker description do_shortcode would run on empty descriptions, causing issues in some environments
 * Fixed issue with misnamed params variable in our DirectionsBox module. Did not effect functionality, but still worth solving
 * Fixed issue where error would be thrown when opening panel info windows, for shapes, in OpenLayers
 * Fixed issue where sharing tools would not update their marker ID correctly (Atlas Novus)
 * Fixed issue where marker sharing tools would show when viewing shape data (Atlas Novus)
 * Tested with WP 6.5
 * Archived V8 changelogs
 * 
 * 9.0.27 - 2024-03-05
 * Added performance tool which allows you to add a few DB table indexes to improve query times
 * Added basic shortcode support for polygons and other shapes
 * Added support for internal defer loading of script assets (beta)
 * Added ability to force Datatables to only search on enter, to assist users with large datasets 
 * Fixed issue where directions waypoints would be sent to directions API, even when they were unset on some installations
 * Fixed issue where basic marker shortcodes would be rendered in description editor, making management more complicated
 * Fixed issue where "&" would show up encoded in marker listings
 * Improved basic shortcode support/handling in markers
 * Improved shortcode attribute security by escaping attributes further, based on recent security reports
 * 
 * 9.0.26 - 2024-01-11
 * Added HTML structure warning when invalid HTML is detected (Atlas Novus)
 * Fixed issue where importer would not detect geojson import URL's correctly
 * Fixed issue where some non-nullable parameter were passed to internal PHP functions (Phase 1)
 * Fixed issue where OpenLayers would close info-windows when clicking inside an info-window
 * Fixed issue where marker icon editor would not correctly render some FontAwesome 5 icons
 * Updated plugin header copyright year
 * 
 * 9.0.25 - 2023-10-11
 * Added ability to import KML files where no point data is present, if an address is available instead. We will geocode the address to create point data
 * Added support for Airtable Personal Access Tokens on importer. Legacy keys still supported, but PAT preferred
 * Fixed issue where deprecation notices are shown in PHP 8.2, including dynamic property creation and function changes
 * Fixed issue where importing CSV data with special characters might result in malformed API URL's
 * Fixed issue where importing CSV data with locations on the equator (0) the row would be skipped
 * Fixed issue where deeply nested KML polygon names would not be imported correctly (ExtendedData sub structures)
 * Fixed issue where importing special characters via CSV would cause titles to be stored with HTML entities in place
 * Fixed issue where 'Do not enqueue Datatables' would not dequeue responsive datatable library
 * Improved core code base by refactoring some modules/sections
 * Improved reliability of the CSV importer
 * 
 * 9.0.24 - 2023-09-01
 * Plugin header (meta) updated top reflect copyright
 * 
 * 9.0.23 - 2023-08-15
 * Fixed issue where JSON export may fail when Gold is enabled and ratings are included, with only a single map
 * Fixed issue where Query Monitor would return a dependency error for some scripts 
 * Fixed issue where VGM email address could not be edited
 * 
 * 9.0.22 - 2023-08-08
 * Fixed various Gutenberg block definition issues, which would cause unexpected visual outputs in the block editor
 * Improved Pro Map Block (Gutenberg) to use the block.json definition (V3 Block Engine)
 * Improved Pro Store Locator Block (Gutenberg) to use the block.json definition (V3 Block Engine)
 * Improved Category Filter Block (Gutenberg) to use the block.json definition (V3 Block Engine) 
 * Improved Category Legends Block (Gutenberg) to use the block.json definition (V3 Block Engine) 
 * Improved Directions Block (Gutenberg) to use the block.json definition (V3 Block Engine) 
 * Improved Marker Listing Block (Gutenberg) to use the block.json definition (V3 Block Engine) 
 * Improved Infowindow Block (Gutenberg) to use the block.json definition (V3 Block Engine) 
 * Improved loading order of block assets
 * Updated legacy Gutenberg blocks to use the new WP Go Maps block category
 * Removed Gutenberg modules from auto-builder, allowing them to be loaded separately
 * 
 * 9.0.21 - 2023-08-03
 * Fixed issue where Gutenberg Store Locator block would cause an inspector error due to a duplicate react element key name
 * Fixed issue where Gutenberg blocks incorrectly set the classname property when creating elements
 * Fixed issue where Gutenberg blocks would not be accessible in WP 6.3 due to changes in script localization
 * Fixed issue where Gutenberg blocks inspectors would cause errors in console due to incorrect definitions 
 * Tested with WP 6.3
 * 
 * 9.0.20 - 2023-04-28
 * Fixed issue where default map theme may not be parse correctly causing a map load failure, showing only a single marker 
 * 
 * 9.0.19 - 2023-03-22
 * Fixed issue where 'enable_category' shortcode attribute would throw warning/error in some installations 
 * 
 * 9.0.18 - 2023-03-15
 * Added ability to clear a WooCommerce product marker location, removing it from the map
 * Fixed issue where KML importer would fail to identify datasets when more than one namespace schema is found
 * Fixed issue where 'enable_category' shortcode attribute would not accept "0" to disable category filter (Atlas Novus)
 * Fixed issue where parent/child category traversal would throw an error when parent ID is not identifiable
 * Fixed issue where lightbox would not open correctly for ACF and WooCommerce integration markers
 * Fixed issue where rating modules would not be displayed in panel, or standalone, info-windows (Atlas Novus)
 * Fixed issue where rating modules would not be styled correctly (Atlas Novus)
 * 
 * 9.0.17 - 2023-01-11
 * Fixed issue where update call would sometimes throw a warning due to transient data being unavailable
 * 
 * 9.0.16 - 2023-01-11
 * Fixed issue with realpath implementation for XML path variables
 * 
 * 9.0.15 - 2023-01-10
 * Fixed issue where importing (CSV) a category by name, as part of a marker import, would not assign the category to a map, leaving it unlinked for filtering 
 * 
 * 9.0.14 - 2022-12-14
 * Added ability to hide marker listing pagination
 * Added ability to set "Show X items by default" option to "1" for users wanting to show only a single marker result, based on distance for example
 * Added beta flag to WordPress category source, as it is still in development
 * Added import step indicator for batch imports
 * Improved PHP8.1 compatibility by introducing "#[\ReturnTypeWillChange]" to classes which extend without return types
 * Improved overall stability of Gutenberg modules
 * Improved file handling for remote import files
 * Fixed issue where some older themes would throw a warning in widget area due to Gutenberg integration
 * Fixed issue where marker listing would not move back to first page when filtering by category
 * Fixed issue where ViewportGroupings would not be closed when 'close on map click' was enabled (Atlas Novus)
 * Fixed issue where legacy marker listing adapter would have no min width on mobile devices (Atlas Novus)
 * Fixed issue where legacy polydata (or import/export looped) KML data would become inverted, and this altered over time
 * Fixed issue where some KML imported maps would start at lat/lng 0 when no starting point could be located 
 * Fixed issue where shortcode category would not be respected during a store locator search if category filtering was disabled 
 * Fixed issue where WordPress category source would only apply to ACF and WooCommerce markers (Post type driven)
 * Fixed issue where importer could cause issues with 3rd party plugin solutions, such as RankMath importer
 * 
 * 9.0.13 - 2022-11-01
 * Added ability to set/hide markers on the frontend with the bulk editor
 * Fixed issue where hover icons could only be set once
 * Fixed issue where normal icons would be refreshed before storage 
 * Fixed issue where backup class would throw a warning on some installs (list_files usage)
 * Fixed issue where MarkerIconPicker would throw undefined index error in some instanced
 * Fixed issue where Legacy CSV importer would reference a default variable which was not defined
 * Fixed issue where lowest level of access was not being respected for category creation
 * Fixed issue where admin pro styles were being loaded on the frontend in legacy mode
 * Fixed issue where grid layout would not apply crop to the images in Atlas Novus (ported)
 * Tested up to WordPress 6.1 
 * 
 * 9.0.12 - 2022-10-13
 * Fixed issue where link imports would encode query params making links unusability
 * Fixed issue where Mobile pagination buttons would not meet 48px touch requirement for Google Search Console
 * Fixed issue where some marker listings were too small on mobile, when place within the map (Atlas Novus)
 * 
 * 9.0.11 - 2022-09-20
 * Added ability to disable zoom on Marker Listing click
 * Added ability to change the Store Locator address placeholder text 
 * Added marker field support to Airtable importer
 * Added offset support to Airtable importer, allowing for multiple pages to be imported. Previously only imported 100 records
 * Fixed issue where Airtable importer required a specific API URL which is not easily located. We will now build this on the fly, from a standard link 
 * Fixed issue where importing markers in 'replace' mode would not remove existing marker category relationships, leading to stale data storage
 * Fixed issue where country restriction would apply incorrectly due to an issue with the country select module (ISO 3166-1 alpha-2)
 * 
 * 9.0.10 - 2022-08-24
 * Fixed issue where Nominatim country restriction would be sent incorrectly 
 * Fixed issue where User Location would not populate panel and DOM infowindows correctly 
 * Fixed issue where deleting a category would not remove marker relationships 
 * 
 * 9.0.9 - 2022-08-11
 * Improved conditional loading of tools page dependencies
 * 
 * 9.0.8 - 2022-08-03
 * Fixed issue where Import by URL would not correctly identify CSV files
 * Fixed issue where new marker field would not be created when mapped to new field value 
 * 
 * 9.0.7 - 2022-07-27 
 * Added center point marker support for auto search area in store locator 
 * Fixed issue where gallery lightbox could be opened multiple times per instance 
 * Fixed issue where lightbox settings would not fully clone from the parent feature 
 * Fixed issue where lightbox would not show in fullscreen map mode 
 * Fixed issue where duplicating a map would not duplicate point labels
 * Fixed issue where duplicating a map would not duplicate image overalays
 * Fixed issue where checkbox category filters would not apply child category selections correctly
 * Fixed issue where radius dropdown would show when auto region mode was enabled on store locator (Atlas Novus)
 * Fixed issue where auto search area would not show notice about realtime marker listing filters (Atlas Novus)
 * Fixed issue where auto search area would still show color pickers for radius area (Atlas Novus)
 * Fixed issue where distance units in info-windows were being shown before distance is calculated
 * Fixed issue where distance would not show in panel info-windows (Atlas Novus)
 * Fixed issue where approve VGM button would not show in marker list, in some installations (Legacy)
 * Fixed issue where approve VGM button would not show, at all (Atlas Novus) 
 * 
 * 9.0.6 - 2022-07-14
 * Added option to control gallery image default size. Lightboxes should still use full size in Atlas Novus, but main sliders will respect defined sizes
 * Fixed issue where OpenLayers custom map images would be misplaced on retina displays 
 * Fixed issue where store locator autocomplete would only be bound to the first store locator on the map 
 * Fixed issue where store locator user location button would be duplicated when two maps were present on the same page
 * Fixed issue where DataTables reload would be called early and cause an error to be thrown
 * Fixed issue where category shortcode attribute would not apply correctly when using Advanced Table listings
 * Fixed issue where directions panel would not initialize when legacy interface style was set to modern, and user swapped to Atlas Novus
 * Fixed issue where exporting old Polyline/Polygon data would fail due to non-object storage structure
 * Fixed issue where more details link would not be shown in panel info window (Atlas Novus)
 * Fixed issue where exif.js was being loaded on all admin pages
 * Fixed issue where import-export-page.js was being loaded on all admin pages
 * Updated DataTables bundles to 1.12.1 (Excl. Styles)
 * Updated DataTables Responsive bundles to 2.3.0
 * 
 * 9.0.5 - 2022-07-06
 * Added support for updated Google Sheets share URLs 
 * Added improved file mirror system during import
 * Improved performance for remote file imports dramatically 
 * Improved underlyig canvas handling on retina displays with OpenLayers
 * Fixed issue where remote file imports would cause crashes, based on source, or file sizes 
 * Fixed issue where route transit options are not availability
 * Removed calls to $.isNumeric and replaced them with WPGMZA.isNumeric counterpart
 * Removed $.bind calls and replaced them with standard $.on event listeners
 *  
 * 9.0.4 - 2022-06-29
 * Fixed issue with directions renderer would not correctly reset marker icons. Causing errors during resets. 
 * 
 * 9.0.3 - 2022-06-28
 * Fixed issue where de_DE translations in the tools (advanced) area were incorrectly displayed
 * Fixed issue where info window image resize options would not set initial canvas size (Atlas Novus)
 * Fixed issue where info window resize toggle was not being respected in new gallery (Atlas Novus)
 *  
 * 9.0.2 - 2022-06-24
 * Fixed issue where writersblock HTML editor would not reset when saving/populating from a feature (Atlas Novus)
 * Fixed issue where gallery would not initialize in some environmets. Further correction to 9.0.1 (Atlas Novus)
 * Fixed issue where gallery height resize animations would auto apply in native info-windows (Atlas Novus)
 * 
 * 9.0.1 - 2022-06-22
 * Added "day one" core patch support 
 * Added ability to edit HTML of any WritersBlock input (Atlas Novus)
 * Fixed issue where "Hide category field" in info-window was not available in legacy engine
 * Fixed issue where ini_set may be called even when not available, conditions added 
 * Fixed issue with trailing comma in JSON importer causing parse errors on older PHP versions 
 * Fixed issue where gallery max width/height would not apply based on global settings (Atlas Novus)
 * Fixed issue where gallery would not initialize in some optimized/cached environments (Atlas Novus)
 * Fixed issue where URL based imports with no known extension would not default to JSON
 * 
 * 9.0.0 - 2022-06-20
 * Added Atlas Novus Pro 
 * Added marker creator, allowing for simple marker creation
 * Added Batched importing for CSV files 
 * Added ability to import category by name or ID 
 * Added better supports for marker field importing
 * Added cross version compatibility checks and support
 * Improved CSV importer dramatically
 * Improved integration import stability
 * Improved ACF integration drastically, allowing additional fields to be shown in the map
 * Improved heatmap stability 
 * Improved map creation wizard
 * Improved import logger drastically
 * Improved auto backup system drastically
 * Improved scheduled import manager, still relies on WP Cron, but in theory scheduled CSV imports should be more reliable  
 * Fixed issue where auto store locator radius would fail to load bounds with very specific searches 
 * Fixed issue where map would auto scroll when marker info window is opened 
 * Renamed original Woo integration as Toolset, as this is accurate. WooCommerce now integrated natively 
 * Removed Mappity 
 * Atlas Novus
 * - Added panel display systems 
 * - Added new info window panel style
 * - Added pnale marker listing option
 * - Added category legends system 
 * - Added WooCommerce Product integration, allowing products to be added to a map automatically 
 * - Added WooCommerce Checkout map intergration, allowing people to select shipping address 
 * - Added ability to filter post type for ACF integration
 * - Added ability to remap CSV files on Import
 * - Added additional export tools 
 * - Added ability to export all data types to CSV 
 * - Added ability to export global setup options 
 * - Added ability to import global setup options 
 * - Added ability to export as KML 
 * - Added ability to show marker field names in info window 
 * - Added ability to show category names in info window 
 * - Added ability to use WordPress categories as source 
 * - Added coallation fixer, which resolves common database issues for ACF/WooCommerce integration
 * - Added ability to search nearby locations to a marker 
 * - Added ability to share locations externally 
 * - Added streetview modules, allowing the map to start in streetview mode 
 * - Added image overlay feature, allowing you to add images to maps
 * - Added shape labels, adding labels to polygons, polylines, rectangles and cirlces
 * - Added ability to set a custom map image in OpenLayers. Great for malls and custom map implementations
 * - Added Gutenberg block and shortcode for directions
 * - Added Gutenberg block and shortcode for marker listings 
 * - Added Gutenberg block and shortcode for info window
 * - Added Gutenberg block and shortcode for category legends 
 * - Added Gutenberg block and shortcode for category filters 
 * - Added info windows to shapes 
 * - Added bulk marker editor 
 * - Added improved map creation wizard 
 * - Added more granular control over info window property visibility
 * - Added writersblock to all editors, replaceing TinyMCE fully
 * - Added embedded media controller, allowing for HTML5 Video and Audio to be dropped into info windows 
 * - Added feature layer supports, allowing you to move items above/below others 
 * - Added ability to add marker hover state 
 * - Added ability to add shape hover state 
 * - Improved Category page and tool UI/UX 
 * - Improved Marker Field page UI/UX 
 * - Improved all shape drawing tools 
 * - Improved data supports for shapes 
 * - Improved Gallery systems 
 * - Improved directions system
 * - Improved "Only load markers in viewport" controller  
 * - Improved category selection system
 * - Renamed Custom Fields page to Marker Fields for clarity 
 * - Renamed Advanced page to Tools for clarity 
 * - Removed legacy importers from Tools
 * - Removed old 'modern' info windows 
 * - Removed old panel systems (Directions, info-window, and marker listing)
 *
*/

/*
 * NOTICE:
 *
 * Core code moved to legacy-core.php. This file checks two things:
 *
 * 1) PHP version >= 5.3 - needed for namespace and anonymous functions
 * 2) DOMDocument, increasingly used throughout the plugin
 *
 * The following checks will cause the script to return rather than loading legacy-core.php,
 * which would cause syntax errors in case of 1) and fatal errors in case of 2)
 *
 */

define('WPGMZA_PRO_FILE', __FILE__);

$fromVersion = get_option('wpgmza_db_version');
if ($fromVersion == NULL) {
	$fromVersion = get_option('wpgmaps_current_version');
}

/* Check Basic Compat - V8.1.0 */
if(!empty($fromVersion) && version_compare($fromVersion, '8.1.0', '<')){
	add_action('admin_notices', 'wpgmaps_pro_81_notice');
	return;
}
function wpgmaps_pro_81_notice() {
	$fromVersion = get_option('wpgmza_db_version');
	if(version_compare($fromVersion, '8.1.0', '<')){
		?>

		<div class="notice notice-error">
			<h1><?php _e('Urgent notice', 'wp-google-maps'); ?></h1>
			<h3><?php _e('WP Go Maps', 'wp-google-maps'); ?></h3>
			<p><?php
				echo sprintf(__('In order to use WP Go Maps Pro 8.1, you need to <a href="%s">update your basic version</a> to the latest version (8.1*).', 'wp-google-maps'),'update-core.php');
			?></p><br />
			<p>&nbsp;</p>
		</div><br />

		<?php
	}
}

/* Check Basic Compat - V9.0.0 */
if(version_compare((string) $fromVersion, '9.0.0', '<')){
	add_action('admin_notices', 'wpgmaps_pro_90_notice');
	return;
}

function wpgmaps_pro_90_notice() {
	$fromVersion = get_option('wpgmza_db_version');
	if(version_compare($fromVersion, '9.0.0', '<')){
		?>

		<div class="notice notice-error">
			<h3><?php _e('WP Go Maps', 'wp-google-maps'); ?> - <?php _e('Urgent notice', 'wp-google-maps'); ?></h3>
			<p><?php
				echo sprintf(__('In order to use WP Go Maps Pro 9.0.0, you need to <a href="%s">update your basic version</a> to the latest version (9.*).', 'wp-google-maps'),'update-core.php');
			?></p>
			<p>&nbsp;</p>
		</div><br />

		<?php
	}
}


if(version_compare((string) $fromVersion, '9.0.0', '<')){ } else {

	/* Thrive Patch - When in Thrive editor, we stop loading Pro as it conflicts in a strange, and difficult to trace way */
	if(!empty($_GET['tve']) || !empty($_GET['et_fb'])){
		return;
	}

	global $wpgmza_pro_version;
	$wpgmza_pro_version = null;
	$subject = file_get_contents(plugin_dir_path(__FILE__) . 'wp-google-maps-pro.php');
	if(preg_match('/Version:\s*(.+)/', $subject, $m))
		$wpgmza_pro_version = trim($m[1]);

	define('WPGMZA_PRO_VERSION', $wpgmza_pro_version);

	require_once(plugin_dir_path(__FILE__) . 'constants.php');

    /*
     * We are now PHP8 compatible, so we don't need t stop anything 
    */
    /*
	if(version_compare(phpversion(), '8.0', '>=')){
		return;
	}
    */
	 
	// Pro MUST load before Basic or the plugin will break. This will change in future versions as the initialization code is altered to use the appropriate hooks
	function wpgmza_load_order_notice()
	{
		?>
		<div class="notice notice-error">
			<p>
				<?php
				_e('<strong>WP Go Maps:</strong> The plugin and Pro add-on did not load in the correct order. Please ensure you use the correct folder names for the plugin and Pro add-on, which are /wp-google-maps and /wp-google-maps-pro respectively.', 'wp-google-maps');
				?>
			</p>
		</div>
		<?php
	}

	function wpgmza_check_load_order()
	{
		global $wpgmza_version;
		
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		
		$apl = get_option('active_plugins');
		$plugins = get_plugins();
		$activated_plugins = array();
		
		foreach ($apl as $p)
		{
			if(isset($plugins[$p]))
				array_push($activated_plugins, $plugins[$p]['Name']);
		}
		
		$basic_index	= array_search('WP Go Maps', $activated_plugins);
		$pro_index		= array_search('WP Go Maps - Pro Add-on', $activated_plugins);
		
		if($basic_index === false || $pro_index === false)
			return;
		
		if($basic_index < $pro_index)
			add_action('admin_notices', 'wpgmza_load_order_notice');
	}

	if(is_admin())
		add_action('init', 'wpgmza_check_load_order');

	if(!function_exists('wpgmza_show_php_version_error'))
	{
		function wpgmza_show_php_version_error()
		{
			?>
			<div class="notice notice-error">
				<p>
					<?php
					_e('<strong>WP Go Maps:</strong> This plugin does not support PHP version 5.2 or below. Please use your cPanel or contact your host to switch version.', 'wp-google-maps');
					?>
				</p>
			</div>
			<?php
		}
	}

	if(!function_exists('wpgmza_show_dom_document_error'))
	{
		function wpgmza_show_dom_document_error()
		{
			?>
			<div class="notice notice-error">
				<p>
					<?php
					_e('<strong>WP Go Maps:</strong> This plugin uses the DOMDocument class, which is unavailable on this server. Please contact your host to request they enable this library.', 'wp-google-maps');
					?>
				</p>
			</div>
			<?php
		}
	}

	function wpgmza_show_php_5_4_45_error()
	{
		?>
		<div class="notice notice-error">
			<p>
				<?php
				_e('<strong>WP Go Maps:</strong> Due to a known issue with PHP 5.4.45 and JSON serialization, the Pro add-on cannot function correctly. We strongly recommend you switch to more up to date version of PHP.', 'wp-google-maps');
				?>
			</p>
		</div>
		<?php
	}

	global $wpgmza_cached_basic_dir;

	function wpgmza_get_basic_dir()
	{
		global $wpgmza_cached_basic_dir;
		
		if($wpgmza_cached_basic_dir)
			return $wpgmza_cached_basic_dir;
		
		if(defined('WPGMZA_PLUGIN_DIR_PATH'))
			return WPGMZA_PLUGIN_DIR_PATH;
		
		$plugin_dir = plugin_dir_path(__DIR__);
		
		// Try default folder name first
		$file = $plugin_dir . 'wp-google-maps/wpGoogleMaps.php';
		
		if(file_exists($file))
		{
			$wpgmza_cached_basic_dir = plugin_dir_path($file);
			return $wpgmza_cached_basic_dir;
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
				
				$wpgmza_cached_basic_dir = plugin_dir_path($file);
				return $wpgmza_cached_basic_dir;
			}
		}
		
		return null;
	}

	function wpgmza_get_basic_version()
	{
		global $wpgmza_version;
		
		// Try already loaded
		if($wpgmza_version)
			return trim($wpgmza_version);
		
		if(defined('WPGMZA_VERSION'))
			return trim(WPGMZA_VERSION);
		
		$dir = wpgmza_get_basic_dir();
		
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

	function wpgmza_get_required_basic_version()
	{
		return '9.0.0';
	}

	function wpgmza_is_basic_compatible()
	{
		$basic_version = wpgmza_get_basic_version();
		$required_version = wpgmza_get_required_basic_version();
		
		return version_compare($basic_version, $required_version, '>=');
	}

	function wpgmza_show_basic_incompatible_notice()
	{
		$basic_version = wpgmza_get_basic_version();
		$required_version = wpgmza_get_required_basic_version();
		$pro_version = WPGMZA_PRO_VERSION;
		
		$notice = '
		<div class="notice notice-error">
			<p>
				' .
				__(
					sprintf(
						'<strong>WP Go Maps Pro:</strong> Pro add-on %s requires WP Go Maps to be activated, the minimum required version of WP Go Maps is version %s. Please update the basic plugin to version %s to use WP Go Maps Pro %s', 
						$pro_version,
						$required_version,
						$required_version,
						$pro_version
						),
					'wp-google-maps'
				) . '
			</p>
		</div>
		';
		
		echo $notice;
	}
	 
	function wpgmza_pro_preload_is_in_developer_mode()
	{
		$globalSettings = get_option('wpgmza_global_settings');
			
		if(empty($globalSettings))
			return !empty($_COOKIE['wpgmza-developer-mode']);
		
		if(!($globalSettings = json_decode($globalSettings)))
			return false;
		
		return isset($globalSettings->developer_mode) && $globalSettings->developer_mode == true;
	}
	 
	if(version_compare(phpversion(), '5.3', '<'))
	{
		add_action('admin_notices', 'wpgmza_show_php_version_error');
		return;
	}

	if(version_compare(phpversion(), '5.4.45', '=='))
	{
		add_action('admin_notices', 'wpgmza_show_php_5_4_45_error');
		return;
	}

	if(!class_exists('DOMDocument'))
	{
		add_action('admin_notices', 'wpgmza_show_dom_document_error');
		return;
	}

	if(!wpgmza_is_basic_compatible())
	{
		add_action('admin_notices', 'wpgmza_show_basic_incompatible_notice');
		return;
	}

	if(wpgmza_pro_preload_is_in_developer_mode())
		require_once(plugin_dir_path(__FILE__) . 'legacy-core.php');
	else
	{
		try{
			require_once(plugin_dir_path(__FILE__) . 'legacy-core.php');
		}catch(Exception $e) {
			add_action('admin_notices', function() use ($e) {
				
				?>
				<div class="notice notice-error is-dismissible">
					<p>
						<strong>
						<?php
						_e('WP Go Maps', 'wp-google-maps');
						?>:</strong>
						
						<?php
						_e('The Pro add-on cannot be loaded due to a fatal error. This is usually due to missing files. Please re-install the Pro add-on. Technical details are as follows: ', 'wp-google-maps');
						echo $e->getMessage();
						?>
					</p>
				</div>
				<?php
				
			});
		}
	}

	// Adds filter to stop loading datatables from class.script-loader.php line 106
	add_filter('wpgmza-get-library-dependencies', 'wpgmza_do_not_load_datatables', 10, 1);
			
	function wpgmza_do_not_load_datatables($dep){
		$wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
		 if (!empty($wpgmza_settings['wpgmza_do_not_enqueue_datatables']) && !is_admin()) {
			$dequeue = array('datatables', 'datatables-responsive');

			foreach($dequeue as $tag){
				if (isset($dep[$tag])) {
					unset($dep[$tag]);
				}
			}

			
		}
		
		return $dep;
	}


	/**
	 * Localized strings to pass to page
	 *
	 * TODO: Rebuild into proper architecture spec
	 * 		 - This solution is temporary as info-windows are non-functional presently
	 */
	add_filter('wpgmza_localized_strings', function($arr) {
		$wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");

		if (isset($wpgmza_settings['wpgmza_settings_infowindow_link_text'])) { $wpgmza_settings_infowindow_link_text = $wpgmza_settings['wpgmza_settings_infowindow_link_text']; } else { $wpgmza_settings_infowindow_link_text = false; }
		if (!$wpgmza_settings_infowindow_link_text) { $wpgmza_settings_infowindow_link_text = __("More details","wp-google-maps"); }

		return array_merge($arr, array(
			'directions' => __('Directions', 'wp-google-maps'),
			'get_directions' => __('Get Directions', 'wp-google-maps'),
			'more_info' => $wpgmza_settings_infowindow_link_text
		));
		
	});

	add_action('admin_notices', 'wpgmza_81_pro_extension_notices');

	function wpgmza_81_pro_extension_notices(){
		global $wpgmza_ugm_version;

		if(defined("WPGMZA_GOLD_VERSION")){
			if(version_compare(WPGMZA_GOLD_VERSION, '5.1.0', '<')){
			?>

				<div class="notice notice-error">
					<h1><?php _e('Urgent notice', 'wp-google-maps'); ?></h1>
					<h3><?php _e('WP Go Maps', 'wp-google-maps'); ?></h3>
					<p><?php
						echo sprintf(__('In order to use WP Go Maps Gold with your current Pro version, you need to <a href="%s">update your gold version</a> to the latest version (5.1*).', 'wp-google-maps'),'update-core.php');
					?></p><br />
					<p>&nbsp;</p>
				</div><br />

			<?php
			}
		}

		if(!empty($wpgmza_ugm_version)){
			if(version_compare($wpgmza_ugm_version, '3.30', '<')){
			?>

				<div class="notice notice-error">
					<h1><?php _e('Urgent notice', 'wp-google-maps'); ?></h1>
					<h3><?php _e('WP Go Maps', 'wp-google-maps'); ?></h3>
					<p><?php
						echo sprintf(__('In order to use WP Go Maps Visitor Generated Markers with your current Pro version, you need to <a href="%s">update your VGM version</a> to the latest version (3.30*).', 'wp-google-maps'),'update-core.php');
					?></p><br />
					<p>&nbsp;</p>
				</div><br />

			<?php
			}
		}
	} 
}
