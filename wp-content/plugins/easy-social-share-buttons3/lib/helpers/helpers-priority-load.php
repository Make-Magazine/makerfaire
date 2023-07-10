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
include_once (ESSB3_HELPERS_PATH . 'share-counters/helpers-sharecounters.php'); 
include_once (ESSB3_HELPERS_PATH . 'helpers-deprecated.php');

// Classes
include_once (ESSB3_CLASS_PATH . 'class-factory-loader.php');
include_once (ESSB3_CLASS_PATH . 'class-plugin-options.php');
include_once (ESSB3_CLASS_PATH . 'class-runtime-cache.php');
include_once (ESSB3_CLASS_PATH . 'class-plugin-loader.php');
include_once (ESSB3_CLASS_PATH . 'share-information/class-abstract-post-information.php');
include_once (ESSB3_CLASS_PATH . 'share-information/class-single-post-information.php');
include_once (ESSB3_CLASS_PATH . 'share-information/class-site-share-information.php');

// Post meta class can be loaded in advance
if (!class_exists('ESSB_Post_Meta')) {
    include_once (ESSB3_CLASS_PATH . 'class-post-meta.php');
}

// Static Resources
include_once (ESSB3_CLASS_PATH . 'assets/class-dynamic-js-builder.php');
include_once (ESSB3_CLASS_PATH . 'assets/class-dynamic-css-builder.php');
include_once (ESSB3_CLASS_PATH . 'assets/class-static-css-loader.php');
include_once (ESSB3_CLASS_PATH . 'assets/class-plugin-assets.php');
include_once (ESSB3_CLASS_PATH . 'assets/class-module-assets.php');

// AJAX Actions
include_once (ESSB3_CLASS_PATH . 'class-ajax.php');

// Block Editor Integration
if (!essb_option_bool_value('gutenberg_disable_blocks')) {
    include_once (ESSB3_MODULES_PATH . 'block-editor/block-editor-loader.php');
}

// Post loading events (running before plugin real code
ESSB_Plugin_Options::load();

// Module assets manager
ESSB_Module_Assets::load();