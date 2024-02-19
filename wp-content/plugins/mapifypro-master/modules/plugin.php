<?php

// Handle map settings
include_once( 'map-settings/map-settings.php' );

// Handle WPML plugin compatibility
include_once( 'wpml-compatibility.php' );

// Auto re-activate license after upgrade
include_once( 'plugin-re-activate-after-upgrade.php' );

// Init custom image sizes
include_once( 'carbon.php' );

// Provides on-the-fly thumbnails
include_once( 'wpthumb.php' );

// Provides an ajax service for map settings
include_once( 'service-map-settings.php' );

// Overrides Open Graph meta when viewing single map locations
include_once( 'og-meta.php' );

// Overrides the comments form when viewing it in the Mapify popup
include_once( 'comments.php' );

// Groups Mapify menu items into a single menu
include_once( 'admin-menu-grouping.php' );

// Handles the plugin data update process
include_once( 'updater.php' );

// Enable location popup
include_once( 'map-location-popup.php' );

// Filter map-attrs-data
include_once( 'map-attrs.php' );

// Handle the admin UI styles
include_once( 'admin-styles.php' );

// Include PrettyRoutes
include_once( 'prettyroutes/prettyroutes.php' );

// Include CrowdMaps
include_once( 'crowdmaps/crowdmaps.php' );

// Include Advanced Custom Field (AFC) on the plugin
include_once( 'class-mapify-acf.php' );

/* MapifyPro */
include_once( 'plugin-pro.php' );

/**
 * Include Advanced Custom Field (AFC) on the plugin
 * Also include the ACF MapifyPro plugin
 */
$mapify_acf = new Mapify_ACF();