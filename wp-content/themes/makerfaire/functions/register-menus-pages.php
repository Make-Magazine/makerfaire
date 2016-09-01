<?php


// Add page visible to editors
function register_my_page(){
  add_menu_page( 'Entries Management', 'My Page', 'edit_others_posts', 'my_page_slug',  get_stylesheet_directory_uri() . 'plugins/entry_list.php'  );
}
add_action( 'admin_menu', 'register_my_page' );



function isc_register_menus() {
  register_nav_menus(
  array('header-menu' => __( 'Header Menu' ),
        'footer' => __( 'footer' ),
        'mf-admin-bayarea-register-menu' => __( 'MF BayArea Admin Bar' ),
        'mf-admin-newyork-register-menu' => __( 'MF NewYork Admin Bar' ),
        'mf-admin-national-register-menu' => __( 'MF National Admin Bar' ),
        'mf-admin-barnesandnoble-register-menu' => __( 'MF Barnes And Noble Admin Bar' ),
        'mobile-nav' => __( 'Mobile Navigation' ) )
  );
}
add_action( 'init', 'isc_register_menus' );
