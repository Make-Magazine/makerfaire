<?php

namespace WPGMZA;

global $wpdb;
global $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES;
global $WPGMZA_TABLE_NAME_RATINGS;
global $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS;

define('WPGMZA_GOLD_DIR_PATH', plugin_dir_path(__FILE__));
define('WPGMZA_GOLD_DIR_URL', plugin_dir_url(__FILE__));

$WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES	= $wpdb->prefix . 'wpgmza_live_tracking_devices';
$WPGMZA_TABLE_NAME_RATINGS					= $wpdb->prefix . 'wpgmza_ratings';
$WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS		= $wpdb->prefix . 'wpgmza_markers_has_ratings';

define('WPGMZA_CACHE_DIR', plugin_dir_path(__FILE__) . 'cache/');
define('WPGMZA_CACHE_URL', plugin_dir_url(__FILE__) . 'cache/');