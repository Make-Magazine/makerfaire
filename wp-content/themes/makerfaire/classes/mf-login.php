<?php

/**
 * Custom Login Page Actions
 */
// Change the login url sitewide to the custom login page
add_filter( 'login_url', 'custom_login_url', 10, 2 );
// Redirects wp-login to custom login with some custom error query vars when needed
add_action( 'login_head', 'custom_redirect_login', 10, 2 );
// Updates login failed to send user back to the custom form with a query var
add_action( 'wp_login_failed', 'custom_login_failed', 10, 2 );
// Updates authentication to return an error when one field or both are blank
add_filter( 'authenticate', 'custom_authenticate_username_password', 30, 3);
// Automatically adds the login form to "login" page
add_filter( 'the_content', 'custom_login_form_to_login_page' );

/**
 * Custom Login Page Functions
 */
function custom_login_url( $login_url='', $redirect='' )
{
    $page = get_page_by_path('login');
    if ( $page )
    {
        $login_url = get_permalink($page->ID);

        if (! empty($redirect) )
            $login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
    }
    return $login_url;
}
function custom_redirect_login( $redirect_to='', $request='' )
{
    if ( 'wp-login.php' == $GLOBALS['pagenow'] )
    {
        $redirect_url = custom_login_url();

        if (! empty($_GET['action']) )
        {
            if ( 'lostpassword' == $_GET['action'] )
            {
                return;
            }
            elseif ( 'register' == $_GET['action'] )
            {
                $register_page = get_page_by_path('register');
                $redirect_url = get_permalink($register_page->ID);
            }
        }
        elseif (! empty($_GET['loggedout'])  )
        {
            $redirect_url = add_query_arg('action', 'loggedout', custom_login_url());
        }

        wp_redirect( $redirect_url );
        exit;
    }
}
function custom_login_failed( $username )
{
    $referrer = wp_get_referer();

    if ( $referrer && ! strstr($referrer, 'wp-login') && ! strstr($referrer, 'wp-admin') )
    {
        if ( empty($_GET['loggedout']) )
        wp_redirect( add_query_arg('action', 'failed', custom_login_url()) );
        else
        wp_redirect( add_query_arg('action', 'loggedout', custom_login_url()) );
        exit;
    }
}
function custom_authenticate_username_password( $user, $username, $password )
{
    if ( is_a($user, 'WP_User') ) { return $user; }

    if ( empty($username) || empty($password) )
    {
        $error = new WP_Error();
        $user  = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));

        return $error;
    }
}
function custom_login_form_to_login_page( $content )
{
    if ( is_page('login') && in_the_loop() )
    {
        $output = $message = "";
        if (! empty($_GET['action']) )
        {
            if ( 'failed' == $_GET['action'] )
                $message = "There was a problem with your username or password.";
            elseif ( 'loggedout' == $_GET['action'] )
                $message = "You are now logged out.";
            elseif ( 'recovered' == $_GET['action'] )
                $message = "Check your e-mail for the confirmation link.";
        }

        if ( $message ) $output .= '<div class="message"><p>'. $message .'</p></div>';
        $output .= wp_login_form('echo=0&redirect='. site_url());
        $output .= '<a href="'. wp_lostpassword_url( add_query_arg('action', 'recovered', get_permalink()) ) .'" title="Recover Lost Password">Lost Password?</a>';

        $content .= $output;
    }
    
    return $content;
}