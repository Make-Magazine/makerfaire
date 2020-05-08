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

function load_auth0_js() {
    //auth0
    wp_enqueue_script('auth0', 'https://cdn.auth0.com/js/auth0/9.6.1/auth0.min.js', array(), false);
    wp_enqueue_script('auth0Login', get_stylesheet_directory_uri() . '/auth0/js/auth0login.js', array(), false);
}

//add_action('login_enqueue_scripts', 'load_auth0_js', 10);



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