<?php
// add the global header to the login.php page
function mf_header_login_page() {    
  get_header();    
}
add_action('login_head', 'mf_header_login_page');

// add the global footer to the login.php page
function login_add_footer() {    
  get_footer();    
} 
add_action('login_footer', 'login_add_footer');

function add_logout_link( $atts ){
  return '<a href="' . wp_logout_url( home_url() ) . '" id="logoutPrompt">Do you really want to Logout?</a>';
}
add_shortcode( 'logout_link', 'add_logout_link' );
