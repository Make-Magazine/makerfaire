<?php
/**
 * Pre-loader
 * 
 * @package EasySocialShareButtons
 */

// Helper functionality
include_once (ESSB3_HELPERS_PATH . 'helpers-source-map.php');
include_once (ESSB3_HELPERS_PATH . 'helpers-utilities.php');
include_once (ESSB3_HELPERS_PATH . 'helpers-disabled-modules.php'); 

// Classes
include_once (ESSB3_CLASS_PATH . 'class-factory-loader.php');
include_once (ESSB3_CLASS_PATH . 'class-plugin-options.php');
include_once (ESSB3_CLASS_PATH . 'class-runtime-cache.php');
include_once (ESSB3_CLASS_PATH . 'share-information/class-abstract-post-information.php');
include_once (ESSB3_CLASS_PATH . 'share-information/class-single-post-information.php');
include_once (ESSB3_CLASS_PATH . 'share-information/class-site-share-information.php');

// Static Resources
include_once (ESSB3_CLASS_PATH . 'assets/class-dynamic-css-builder.php');
include_once (ESSB3_CLASS_PATH . 'assets/class-static-css-loader.php');
include_once (ESSB3_CLASS_PATH . 'assets/class-plugin-assets.php');

// Post loading events (running before plugin real code
ESSB_Plugin_Options::load();