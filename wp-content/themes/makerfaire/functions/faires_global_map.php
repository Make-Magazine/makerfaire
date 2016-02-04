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

// // Enqueue maps JS (only if on maps page)
// function map_scripts() {
//   if (is_page('Global Maker Faire Map')) {
//     wp_enqueue_script(
//       'angularjs',
//       TEMPLATEPATH . '/pages/map/map.js'
//     );
//   }
// }
// add_action( 'wp_enqueue_scripts', 'map_scripts' );
?>
