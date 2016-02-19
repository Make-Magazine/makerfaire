<?php
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
      get_stylesheet_directory_uri() . '/js/angular/global-faires-map-app.js',
      array('angularjs', 'angular-utils-pagination')
    );
    wp_enqueue_script(
      'angular-utils-pagination',
      get_stylesheet_directory_uri() . '/bower_components/angularUtils-pagination/dirPagination.js',
      array('angularjs')
    );
    wp_enqueue_script(
      'angularjs',
      get_stylesheet_directory_uri() . '/bower_components/angular/angular.min.js'
    );
  }
}
add_action( 'wp_enqueue_scripts', 'faire_maps_scripts' );
?>
