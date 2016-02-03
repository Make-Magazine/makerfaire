<?php
// Faires data API
function get_faires_data() {
  $data = dirname(__FILE__) . '/faires.json';
  header('Content-Type: application/json');
  include_once $data;
  die();
}
add_action('wp_ajax_get_faires_data', 'get_faires_data');
add_action('wp_ajax_nopriv_get_faires_data', 'get_faires_data');
?>
