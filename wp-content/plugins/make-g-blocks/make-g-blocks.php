<?php
/**
 * Plugin Name: Make G Blocks
 * Description: This plugin uses ACF to create the Make: panels used across various Make: websites and allow their use as Gutenberg blocks.
 * Version: 1.2
 * Author: Make: Community engineering
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
$acf_blocks = FALSE;

// Include all function files in the functions directory:
foreach (glob(plugin_dir_path(__FILE__) . '/functions/*.php') as $file) {
    include_once $file;
}

add_action('wp_enqueue_scripts', 'makegblocks_enqueue_scripts');

function makegblocks_enqueue_scripts() {
    //add scripts here
}

add_action('wp_enqueue_scripts', 'makegblocks_enqueue_styles');

function makegblocks_enqueue_styles() {
    //$my_version = '1.0.24';
    //wp_enqueue_style('makehub-style', plugins_url( '/css/style.min.css', __FILE__ ), array(), $my_version );
}

// Plugin styles, add bootstrap and panels.less for easy previewing
function wpdocs_enqueue_custom_admin_styles() {
    wp_enqueue_style('bootstrap-css', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css', array(), null, 'all');
    // in the package json, we've compiled the css necessary for the panels/blocks here
    wp_enqueue_style('admin-style-css', get_stylesheet_directory_uri() . '/css/admin-style.min.css', array(), null, 'all');
    wp_enqueue_style('admin-preview-css', plugins_url('css/admin-preview.css', __FILE__), array(), null, 'all');
}

add_action('admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_styles');