<?php
/* redirect wp-login.php to the auth0 login page */
add_filter( 'login_url', 'custom_login_url', 10, 2 );

function custom_login_url( $login_url='', $redirect='') {
  if (empty( $redirect ) ) {
    $redirect =get_site_url();
  }

  $state = base64_encode('makersABC123');
  $login_url = 'https://makermedia.auth0.com/authorize?
  response_type=token&
  client_id=Ya3K0wmP182DRTexd1NdoeLolgXOlqO1&
  state='.$state.'&
  redirect_uri='.get_site_url().'/authenticate-redirect/&nonce=1234';
  return $login_url;
}

/** Set up the Ajax Logout */
add_action( 'wp_ajax_mm_wplogout',        'MM_wordpress_logout' );
add_action( 'wp_ajax_nopriv_mm_wplogout', 'MM_wordpress_logout' );
function MM_wordpress_logout(){
    //check_ajax_referer( 'ajax-logout-nonce', 'ajaxsecurity' );
    wp_logout();
    //ob_clean(); // probably overkill for this, but good habit
    wp_send_json_success();
}

add_action( 'wp_ajax_mm_wplogin', 'MM_WPlogin' );
add_action( 'wp_ajax_nopriv_mm_wplogin', 'MM_WPlogin' );

/** Set up the Ajax WP Login */
function MM_WPlogin(){
  //check_ajax_referer( 'ajax-login-nonce', 'ajaxsecurity' );
  global $wpdb; // access to the database

  //use auth0 plugin to log people into wp
  $a0_plugin  = new WP_Auth0();
  $a0_options = WP_Auth0_Options::Instance();
  $users_repo = new WP_Auth0_UsersRepo( $a0_options );
  $users_repo->init();

  $login_manager = new WP_Auth0_LoginManager( $users_repo, $a0_options );
  $login_manager->init();

  //get the user information passed from auth0
  $userinput     = filter_input_array(INPUT_POST);
  $userinfo      = (object) $userinput['auth0_userProfile'];
  $userinfo->email = $userinfo->name;
  $userinfo->email_verified = true;
  $access_token = filter_input(INPUT_POST, 'auth0_access_token', FILTER_SANITIZE_STRING);
  $id_token     = filter_input(INPUT_POST, 'auth0_id_token', FILTER_SANITIZE_STRING);

  if($login_manager->login_user( $userinfo, $id_token, $access_token)) {
    wp_send_json_success();
  }else{
    wp_send_json_error();
  }
}