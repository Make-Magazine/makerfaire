<?php
function isc_register_menus() {
   register_nav_menus(
      array(
         'secondary_universal_menu' => __( 'secondary_universal_menu' ),
         'bay_area_secondary_nav' => __( 'bay_area_secondary_navn' ),
         'yearbook_secondary_nav' => __( 'yearbook_secondary_nav' ),
         'press-center-left-hand-nav' => __( 'Press Center Left Hand Page Navigation' )
      )
   );
}

add_action('init', 'isc_register_menus');
