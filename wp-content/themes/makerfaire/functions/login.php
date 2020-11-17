<?php

function cookie_login_warning() { ?>
    <style type="text/css">
        .wp-core-ui #login { width: 80%; }
        #login::before {
            content: "We are unable to process your login as we have detected that you have cookies blocked. Please make sure cookies are enabled in your browser and try again.";
            text-align: center;
            font-size:42px;
            line-height: 46px;
        }
        #form-signin-wrapper {
            display: none;
        }
    </style>
    <?php

}

//add_action('login_enqueue_scripts', 'cookie_login_warning');

/* redirect wp-login.php to the auth0 login page */

// check if logged in
function ajax_check_user_logged_in() {
    echo is_user_logged_in()?'yes':'no';
    die();
}
add_action('wp_ajax_is_user_logged_in', 'ajax_check_user_logged_in');
add_action('wp_ajax_nopriv_is_user_logged_in', 'ajax_check_user_logged_in');

/** Set up the Ajax Logout */
add_action('wp_ajax_mm_wplogout', 'MM_wordpress_logout');
add_action('wp_ajax_nopriv_mm_wplogout', 'MM_wordpress_logout');

function MM_wordpress_logout() {
    //check_ajax_referer( 'ajax-logout-nonce', 'ajaxsecurity' );
    wp_logout();
    ob_clean(); // probably overkill for this, but good habit
    wp_send_json_success();
}

add_action('wp_ajax_mm_wplogin', 'MM_WPlogin');
add_action('wp_ajax_nopriv_mm_wplogin', 'MM_WPlogin');

/** Set up the Ajax WP Login */
function MM_WPlogin() {
    //check_ajax_referer( 'ajax-login-nonce', 'ajaxsecurity' );
    global $wpdb; // access to the database
    //use auth0 plugin to log people into wp
    $a0_plugin =  new WP_Auth0_InitialSetup( WP_Auth0_Options::Instance() );
    $a0_options = WP_Auth0_Options::Instance();
    $users_repo = new WP_Auth0_UsersRepo($a0_options);
    $login_manager = new WP_Auth0_LoginManager($users_repo, $a0_options);

    //get the user information passed from auth0
    $userinput = filter_input_array(INPUT_POST);
    $userinfo = (object) $userinput['auth0_userProfile'];
    $userinfo->email_verified = true;
    $access_token = filter_input(INPUT_POST, 'auth0_access_token', FILTER_SANITIZE_STRING);
    $id_token = filter_input(INPUT_POST, 'auth0_id_token', FILTER_SANITIZE_STRING);

    if ($login_manager->login_user($userinfo, $id_token, $access_token)) {
        wp_send_json_success();
    } else {
        error_log('Failed login');
        error_log(print_r($userinput, TRUE));
        wp_send_json_error();
    }
}

/**
 * These Functions Add and Verify the Invisible Google reCAPTCHA on Login
 * Normal users never see the wp-login.php page as they are forwarded to Auth0. 
 * this will stop spam bots from signing up
 */
add_action('login_enqueue_scripts', 'login_recaptcha_script');

function login_recaptcha_script() {
    wp_register_script('recaptcha_login', 'https://www.google.com/recaptcha/api.js');
    wp_enqueue_script('recaptcha_login');
}

add_action('login_form', 'display_recaptcha_on_login');

function display_recaptcha_on_login() {
    echo "<script>
            function onSubmit(token) {
                document.getElementById('loginform').submit();
            }
          </script>
            <button class='g-recaptcha' data-sitekey='6Lf_-kEUAAAAAHtDfGBAleSvWSynALMcgI1hc_tP' data-callback='onSubmit' data-size='invisible' style='display:none;'>Submit</button>";
}

add_filter('wp_authenticate_user', 'verify_recaptcha_on_login', 10, 2);



add_action( 'login_form_lostpassword', 'wpse45134_filter_option' );
add_action( 'login_form_retrievepassword', 'wpse45134_filter_option' );
add_action( 'login_form_register', 'wpse45134_filter_option' );
/**
 * Simple wrapper around a call to add_filter to make sure we only
 * filter an option on the login page.
 */
function wpse45134_filter_option()
{
    // use __return_zero because pre_option_{$opt} checks
    // against `false`
    add_filter( 'pre_option_users_can_register', '__return_zero' );
}