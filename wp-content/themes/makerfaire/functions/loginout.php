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
  return '<div class="site-content">
            <div id="logoutPrompt">
              <div>
                <img src="https://make.co/wp-content/universal-assets/v2/images/makey-spyglass.jpg" alt="Just making sure" />
              </div>
              <div>
                <p>You are attempting to log out of ' . get_bloginfo( 'name' ) . '.</p>
                <p>Do you really want to log out?</p>
                <div class="logout-buttons">
                  <a onclick="history.go(-1);" class="btn universal-btn universal-btn-red">Cancel</a>
                  <a href="' . wp_logout_url( home_url() ) . '" class="btn universal-btn">Log out</a>
              </div>
            </div>
          </div>';
}
add_shortcode( 'logout_link', 'add_logout_link' );
