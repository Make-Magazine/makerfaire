<?php

function faire_maps_scripts() {
   if (is_page_template('pages/page-global-maker-faire-map.php')) {
      $my_theme = wp_get_theme();
      $my_version = $my_theme->get('Version');
      wp_enqueue_script(
              'faires-global-map-scripts', get_stylesheet_directory_uri() . '/js/angular/global-faires-map-app.js', array('angularjs', 'ordinal-filter', 'angular-utils-pagination'), $my_version
      );
      wp_enqueue_script(
              'angular-utils-pagination', get_stylesheet_directory_uri() . '/bower_components/angularUtils-pagination/dirPagination.js', array('angularjs')
      );
      wp_enqueue_script(
              'ordinal-filter', get_stylesheet_directory_uri() . '/bower_components/angularjs-ordinal-filter/ordinal-browser.js', array('angularjs')
      );
      wp_enqueue_script(
              'angularjs', get_stylesheet_directory_uri() . '/bower_components/angular/angular.min.js'
      );
   }
}

add_action('wp_enqueue_scripts', 'faire_maps_scripts');
?>
