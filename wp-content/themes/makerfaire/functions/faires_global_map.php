<?php
// Faires data API
function get_faires_data() {
  $data = TEMPLATEPATH . '/functions/faires_global_map/faires.json';
  header('Content-Type: application/json');
  include_once $data;
  exit;
}
add_action('wp_ajax_get_faires_data', 'get_faires_data');
add_action('wp_ajax_nopriv_get_faires_data', 'get_faires_data');

// Faires data API
function get_faires_data_kml() {
  $data = TEMPLATEPATH . '/functions/faires_global_map/faires.kml';
  include_once $data;
  exit;
}
add_action('wp_ajax_get_faires_data_kml', 'get_faires_data_kml');
add_action('wp_ajax_nopriv_get_faires_data_kml', 'get_faires_data_kml');

// Faires data API
function get_faires_map_data() {
  $data = TEMPLATEPATH . '/functions/faires_global_map/faires_map_data.json';
  include_once $data;
  exit;
}
add_action('wp_ajax_get_faires_map_data', 'get_faires_map_data');
add_action('wp_ajax_nopriv_get_faires_map_data', 'get_faires_map_data');

function faire_maps_scripts() {
  if (is_page('maps')) {
    wp_enqueue_script(
      'faires-global-map-scripts',
      get_stylesheet_directory_uri() . '/pages/map/map.js',
      array('angularjs', 'ngTasty')
    );
    wp_enqueue_script(
      'ngTasty',
      get_stylesheet_directory_uri() . '/js/angular/ng-tasty-tpls.min.js',
      array('angularjs')
    );
    wp_enqueue_script(
      'angularjs',
      get_stylesheet_directory_uri() . '/js/angular/angular.min.js'
    );
  }
}
add_action( 'wp_enqueue_scripts', 'faire_maps_scripts' );
?>
