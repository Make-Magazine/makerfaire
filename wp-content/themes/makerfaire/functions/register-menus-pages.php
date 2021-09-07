<?php
function isc_register_menus() {
   register_nav_menus(
      array('header-menu' => __( 'Header Menu' ),
      //'footer' => __( 'footer' ),
      'mf-admin-bayarea-register-menu' => __( 'MF BayArea Admin Bar' ),
      'mf-admin-newyork-register-menu' => __( 'MF NewYork Admin Bar' ),
      //'mf-admin-national-register-menu' => __( 'MF National Admin Bar' ),
      //'mf-admin-chicago-register-menu' => __( 'MF Chicago Admin Bar' ),
      //'mf-admin-barnesandnoble-register-menu' => __( 'MF Barnes And Noble Admin Bar' ),
      //'mobile-nav' => __( 'Mobile Navigation' ),
      'NY-left-hand-nav' => __( 'NY Left Hand Page Navigation' ),
      'BA-left-hand-nav' => __( 'BA Left Hand Page Navigation' ),
      'press-center-left-hand-nav' => __( 'Press Center Left Hand Page Navigation' ))
   );
}

add_action('init', 'isc_register_menus');
