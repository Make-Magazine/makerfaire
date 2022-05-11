<?php

use CustomFacebookFeed\CFF_Oembed;
use CustomFacebookFeed\CFF_GDPR_Integrations;
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Resizer;
use CustomFacebookFeed\CFF_Feed_Locator;
use CustomFacebookFeed\SB_Facebook_Data_Encryption;


function cff_menu() {
    $notice = '';
    if ( \cff_main_pro()->cff_error_reporter->are_critical_errors() ) {
        $notice = ' <span class="update-plugins cff-error-alert"><span>!</span></span>';
    }
    $cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
    $cap = apply_filters( 'cff_settings_pages_capability', $cap );

    add_menu_page(
        '',
        'Facebook Feed'. $notice,
        $cap,
        'cff-top',
        'cff_settings_page'
    );
}
add_action('admin_menu', 'cff_menu');

//Create Social Wall Page
function cff_social_wall_page() {}

function cff_register_option() {
    // creates our settings in the options table
    register_setting('cff_license', 'cff_license_key', 'cff_sanitize_license' );

    //Add hook to allow extensions to register their license setting
    do_action('cff_register_setting_license');
}
add_action('admin_init', 'cff_register_option');

function cff_sanitize_license( $new ) {
    $old = get_option( 'cff_license_key' );
    if( $old && $old != $new ) {
        delete_option( 'cff_license_status' ); // new license has been entered, so must reactivate
    }
    return $new;
}
function cff_activate_license() {
    // listen for our activate button to be clicked
    if( isset( $_POST['cff_license_activate'] ) ) {

        // run a quick security check
        if( ! check_admin_referer( 'cff_nonce', 'cff_nonce' ) )
            return; // get out if we didn't click the Activate button
        // retrieve the license from the database
        $cff_license = trim( get_option( 'cff_license_key' ) );

        // data to send in our API request
        $cff_api_params = array(
            'edd_action'=> 'activate_license',
            'license'   => $cff_license,
            'item_name' => urlencode( WPW_SL_ITEM_NAME ) // the name of our product in EDD
        );

        // Call the custom API.
        $cff_response = wp_remote_get( add_query_arg( $cff_api_params, WPW_SL_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );
        // make sure the response came back okay
        if ( is_wp_error( $cff_response ) )
            return false;
        // decode the license data
        $cff_license_data = json_decode( wp_remote_retrieve_body( $cff_response ) );

        //store the license data in an option
        update_option( 'cff_license_data', $cff_license_data );

        // $license_data->license will be either "active" or "inactive"
        update_option( 'cff_license_status', $cff_license_data->license );
    }
}
add_action('admin_init', 'cff_activate_license');

function cff_deactivate_license() {
    // listen for our activate button to be clicked
    if( isset( $_POST['cff_license_deactivate'] ) ) {
        // run a quick security check
        if( ! check_admin_referer( 'cff_nonce', 'cff_nonce' ) )
            return; // get out if we didn't click the Activate button
        // retrieve the license from the database
        $cff_license = trim( get_option( 'cff_license_key' ) );

        // data to send in our API request
        $cff_api_params = array(
            'edd_action'=> 'deactivate_license',
            'license'   => $cff_license,
            'item_name' => urlencode( WPW_SL_ITEM_NAME ) // the name of our product in EDD
        );

        // Call the custom API.
        $cff_response = wp_remote_get( add_query_arg( $cff_api_params, WPW_SL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
        // make sure the response came back okay
        if ( is_wp_error( $cff_response ) )
            return false;
        // decode the license data
        $cff_license_data = json_decode( wp_remote_retrieve_body( $cff_response ) );

        // $license_data->license will be either "deactivated" or "failed"
        if( $cff_license_data->license == 'deactivated' )
            delete_option( 'cff_license_status' );
    }
}
add_action('admin_init', 'cff_deactivate_license');

//Create Settings page
function cff_settings_page() {} //End Settings_Page
//Create Style page
function cff_style_page() {} //End Style_Page
//Enqueue admin styles
function cff_admin_style() {
        wp_register_style( 'cff_custom_wp_admin_css', plugin_dir_url( __FILE__ ) . 'admin/assets/css/cff-admin-style.css', false, CFFVER );
        wp_enqueue_style( 'cff_custom_wp_admin_css' );
        wp_enqueue_style( 'cff-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
        wp_enqueue_style( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'cff_admin_style' );
//Enqueue admin scripts
function cff_admin_scripts() {
    //Declare color-picker as a dependency
    wp_enqueue_script( 'cff_admin_script', plugin_dir_url( __FILE__ ) . 'admin/assets/js/cff-admin-scripts.js', array(), CFFVER );
    wp_localize_script( 'cff_admin_script', 'cffA', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'cff_nonce' => wp_create_nonce( 'cff_nonce' )
        )
    );
    $strings = array(
        'addon_activate'                  => esc_html__( 'Activate', 'custom-facebook-feed' ),
        'addon_activated'                 => esc_html__( 'Activated', 'custom-facebook-feed' ),
        'addon_active'                    => esc_html__( 'Active', 'custom-facebook-feed' ),
        'addon_deactivate'                => esc_html__( 'Deactivate', 'custom-facebook-feed' ),
        'addon_inactive'                  => esc_html__( 'Inactive', 'custom-facebook-feed' ),
        'addon_install'                   => esc_html__( 'Install Addon', 'custom-facebook-feed' ),
        'addon_error'                     => esc_html__( 'Could not install addon. Please download from smashballoon.com and install manually.', 'custom-facebook-feed' ),
        'plugin_error'                    => esc_html__( 'Could not install a plugin. Please download from WordPress.org and install manually.', 'custom-facebook-feed' ),
        'addon_search'                    => esc_html__( 'Searching Addons', 'custom-facebook-feed' ),
        'ajax_url'                        => admin_url( 'admin-ajax.php' ),
        'cancel'                          => esc_html__( 'Cancel', 'custom-facebook-feed' ),
        'close'                           => esc_html__( 'Close', 'custom-facebook-feed' ),
        'nonce'                           => wp_create_nonce( 'cff-admin' ),
        'almost_done'                     => esc_html__( 'Almost Done', 'custom-facebook-feed' ),
        'oops'                            => esc_html__( 'Oops!', 'custom-facebook-feed' ),
        'ok'                              => esc_html__( 'OK', 'custom-facebook-feed' ),
        'plugin_install_activate_btn'     => esc_html__( 'Install and Activate', 'custom-facebook-feed' ),
        'plugin_install_activate_confirm' => esc_html__( 'needs to be installed and activated to import its forms. Would you like us to install and activate it for you?', 'custom-facebook-feed' ),
        'plugin_activate_btn'             => esc_html__( 'Activate', 'custom-facebook-feed' ),
    );
    $strings = apply_filters( 'cff_admin_strings', $strings );

    wp_localize_script(
        'cff_admin_script',
        'cff_admin',
        $strings
    );
    if( !wp_script_is('jquery-ui-draggable') ) {
        wp_enqueue_script(
            array(
            'jquery',
            'jquery-ui-core',
            'jquery-ui-draggable'
            )
        );
    }
    wp_enqueue_script(
        array(
        'hoverIntent',
        'wp-color-picker'
        )
    );
}
add_action( 'admin_enqueue_scripts', 'cff_admin_scripts' );


function cff_expiration_notice(){

    //If the user is re-checking the license key then use the API below to recheck it
    ( isset( $_GET['cffchecklicense'] ) ) ? $cff_check_license = true : $cff_check_license = false;

    // delete_option( 'cff_license_data' );
    $cff_license = trim( get_option( 'cff_license_key' ) );

    // delete_option( 'cff_license_key' );
    // delete_option( 'cff_license_status' );

    //If there's no license key then don't do anything
    if( empty($cff_license) || !isset($cff_license) && !$cff_check_license ) return;

    //Is there already license data in the db?
    if( get_option( 'cff_license_data' ) && !$cff_check_license ){
        //Yes
        //Get license data from the db and convert the object to an array
        $cff_license_data = (array) get_option( 'cff_license_data' );
    } else {
        //No
        // data to send in our API request
        $cff_api_params = array(
            'edd_action'=> 'check_license',
            'license'   => $cff_license,
            'item_name' => urlencode( WPW_SL_ITEM_NAME ) // the name of our product in EDD
        );

        // Call the custom API.
        $cff_response = wp_remote_get( add_query_arg( $cff_api_params, WPW_SL_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );

        // decode the license data
        $cff_license_data = (array) json_decode( wp_remote_retrieve_body( $cff_response ) );

        //Store license data in db
        update_option( 'cff_license_data', $cff_license_data );
    }


    //Number of days until license expires
    $cff_license_expires_date = isset( $cff_license_data['expires'] ) ? $cff_license_data['expires'] : $cff_license_expires_date = '2036-12-31 23:59:59'; //If expires param isn't set yet then set it to be a date to avoid PHP notice
    if( $cff_license_expires_date == 'lifetime' ) $cff_license_expires_date = '2036-12-31 23:59:59';
    $cff_todays_date = date('Y-m-d');
    $cff_interval = round(abs(strtotime($cff_todays_date . ' -1 day')-strtotime($cff_license_expires_date))/86400); //-1 day to make sure auto-renewal has run before showing expired

    //Is license expired?
    if( $cff_interval == 0 || strtotime($cff_license_expires_date) < strtotime($cff_todays_date) ){

        //If we haven't checked the API again one last time before displaying the expired notice then check it to make sure the license hasn't been renewed
        if( get_option( 'cff_check_license_api_when_expires' ) == FALSE || get_option( 'cff_check_license_api_when_expires' ) == 'true' ){

            // Check the API
            $cff_api_params = array(
                'edd_action'=> 'check_license',
                'license'   => $cff_license,
                'item_name' => urlencode( WPW_SL_ITEM_NAME ) // the name of our product in EDD
            );
            $cff_response = wp_remote_get( add_query_arg( $cff_api_params, WPW_SL_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );
            $cff_license_data = (array) json_decode( wp_remote_retrieve_body( $cff_response ) );

            //Check whether it's active
            if( $cff_license_data['license'] !== 'expired' && ( strtotime( $cff_license_data['expires'] ) > strtotime($cff_todays_date) ) ){
                $cff_license_expired = false;
            } else {
                $cff_license_expired = true;
                //Set a flag so it doesn't check the API again until the next time it expires
                update_option( 'cff_check_license_api_when_expires', 'false' );
            }

            //Store license data in db
            update_option( 'cff_license_data', $cff_license_data );

        } else {
            //Display the expired notice
            $cff_license_expired = true;
        }

    } else {
        $cff_license_expired = false;

        //License is not expired so change the check_api setting to be true so the next time it expires it checks again
        update_option( 'cff_check_license_api_when_expires', 'true' );
    }

    //If expired date is returned as 1970 (or any other 20th century year) then it means that the correct expired date was not returned and so don't show the renewal notice
    if( $cff_license_expires_date[0] == '1' ) $cff_license_expired = false;

    //If there's no expired date then don't show the expired notification
    if( empty($cff_license_expires_date) || !isset($cff_license_expires_date) ) $cff_license_expired = false;

    //Is license missing - ie. on very first check
    if( isset($cff_license_data['error']) ){
        if( $cff_license_data['error'] == 'missing' ) $cff_license_expired = false;
    }

    //If license expires in less than 30 days and it isn't currently expired then show the expire countdown instead of the expiration notice
    if($cff_interval < 30 && !$cff_license_expired){
        $cff_expire_countdown = true;
    } else {
        $cff_expire_countdown = false;
    }

    //Check whether it was purchased after subscriptions were introduced
    if( isset($cff_license_data['payment_id']) && intval($cff_license_data['payment_id']) > 762729 ){
        //Is likely to be renewed on a subscription so don't show countdown
        $cff_expire_countdown = false;
    }


    global $cff_download_id;

    //Is the license expired?
    if( ($cff_license_expired || $cff_expire_countdown) || $cff_check_license ) {

        //If they've already dismissed the countdown notice then don't show it here
        global $current_user;
        $user_id = $current_user->ID;
        if( $cff_expire_countdown && get_user_meta($user_id, 'cff_ignore_notice') ) return;


        $cff_license_activation_error = false;
        if( $cff_license_data["success"] == false ) $cff_license_activation_error = true;

        $cff_expired_box_msg = '<svg style="width:16px;height:16px;" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="exclamation-triangle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-exclamation-triangle fa-w-18 fa-2x"><path fill="currentColor" d="M569.517 440.013C587.975 472.007 564.806 512 527.94 512H48.054c-36.937 0-59.999-40.055-41.577-71.987L246.423 23.985c18.467-32.009 64.72-31.951 83.154 0l239.94 416.028zM288 354c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z" class=""></path></svg>';

        //If expire countdown then add the countdown class to the notice box
        if($cff_expire_countdown){
            $cff_expired_box_classes = "cff-license-expired cff-license-countdown";
            $cff_expired_box_msg .= "<b>Important: Your Custom Facebook Feed Pro license key expires in " . $cff_interval . " days.</b>";
        } else if( $cff_license_activation_error ){
            $cff_expired_box_classes = "cff-license-expired";
            $cff_expired_box_msg .= "<b>Issue activating license.</b> <span>Please ensure that you entered your license key correctly. If you continue to have an issue please see <a href='https://smashballoon.com/my-license-key-wont-activate/' target='_blank'>here</a>.</span>";
        } else {
            $cff_expired_box_classes = "cff-license-expired";
            $cff_expired_box_msg .= "<b>Important: Your Custom Facebook Feed Pro license key has expired.</b><br /><span>You are no longer receiving updates that protect you against upcoming Facebook changes.</span>";
        }

        //Create the re-check link using the existing query string in the URL
        $cff_url = '?' . $_SERVER["QUERY_STRING"];
        //Determine the separator
        ( !empty($cff_url) && $cff_url != '' ) ? $separator = '&' : $separator = '';
        //Add the param to check license if it doesn't already exist in URL
        if( strpos($cff_url, 'cffchecklicense') === false ) $cff_url .= $separator . "cffchecklicense=true";

        //Create the notice message
        if( !$cff_license_activation_error ) $cff_expired_box_msg .= " &nbsp;<a href='https://smashballoon.com/checkout/?edd_license_key=".$cff_license."&download_id=".$cff_download_id."&utm_source=plugin-pro&utm_campaign=cff&utm_medium=expired-notice-settings' target='_blank' class='button button-primary'>Renew License</a><a href='javascript:void(0);' id='cff-why-renew-show' onclick='cffShowReasons()' class='button button-secondary'>Why renew?</a><a href='javascript:void(0);' id='cff-why-renew-hide' onclick='cffHideReasons()' class='button button-secondary' style='display: none;'>Hide text</a> <a href='".$cff_url."' class='button button-secondary'>Re-check License</a></p>
            <div id='cff-why-renew' style='display: none;'>
                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='shield-check' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-shield-check fa-w-16 fa-2x' data-ce-key='470'><path fill='currentColor' d='M466.5 83.7l-192-80a48.15 48.15 0 0 0-36.9 0l-192 80C27.7 91.1 16 108.6 16 128c0 198.5 114.5 335.7 221.5 380.3 11.8 4.9 25.1 4.9 36.9 0C360.1 472.6 496 349.3 496 128c0-19.4-11.7-36.9-29.5-44.3zm-47.2 114.2l-184 184c-6.2 6.2-16.4 6.2-22.6 0l-104-104c-6.2-6.2-6.2-16.4 0-22.6l22.6-22.6c6.2-6.2 16.4-6.2 22.6 0l70.1 70.1 150.1-150.1c6.2-6.2 16.4-6.2 22.6 0l22.6 22.6c6.3 6.3 6.3 16.4 0 22.6z' class='' data-ce-key='471'></path></svg>Protected Against All Upcoming Facebook Platform Updates and API Changes</h4>
                <p>You currently don't need to worry about your Facebook feeds breaking due to constant changes in the Facebook platform. You are currently protected by access to continual plugin updates, giving you peace of mind that the software will always be up to date.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fab' data-icon='wordpress' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-wordpress fa-w-16 fa-2x'><path fill='currentColor' d='M61.7 169.4l101.5 278C92.2 413 43.3 340.2 43.3 256c0-30.9 6.6-60.1 18.4-86.6zm337.9 75.9c0-26.3-9.4-44.5-17.5-58.7-10.8-17.5-20.9-32.4-20.9-49.9 0-19.6 14.8-37.8 35.7-37.8.9 0 1.8.1 2.8.2-37.9-34.7-88.3-55.9-143.7-55.9-74.3 0-139.7 38.1-177.8 95.9 5 .2 9.7.3 13.7.3 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l77.5 230.4L249.8 247l-33.1-90.8c-11.5-.7-22.3-2-22.3-2-11.5-.7-10.1-18.2 1.3-17.5 0 0 35.1 2.7 56 2.7 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l76.9 228.7 21.2-70.9c9-29.4 16-50.5 16-68.7zm-139.9 29.3l-63.8 185.5c19.1 5.6 39.2 8.7 60.1 8.7 24.8 0 48.5-4.3 70.6-12.1-.6-.9-1.1-1.9-1.5-2.9l-65.4-179.2zm183-120.7c.9 6.8 1.4 14 1.4 21.9 0 21.6-4 45.8-16.2 76.2l-65 187.9C426.2 403 468.7 334.5 468.7 256c0-37-9.4-71.8-26-102.1zM504 256c0 136.8-111.3 248-248 248C119.2 504 8 392.7 8 256 8 119.2 119.2 8 256 8c136.7 0 248 111.2 248 248zm-11.4 0c0-130.5-106.2-236.6-236.6-236.6C125.5 19.4 19.4 125.5 19.4 256S125.6 492.6 256 492.6c130.5 0 236.6-106.1 236.6-236.6z' class=''></path></svg>WordPress Compatibility Updates</h4>
                <p>With WordPress updates being released continually, we make sure the plugin is always compatible with the latest version so you can update WordPress without needing to worry.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='far' data-icon='life-ring' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-life-ring fa-w-16 fa-2x' data-ce-key='500'><path fill='currentColor' d='M256 504c136.967 0 248-111.033 248-248S392.967 8 256 8 8 119.033 8 256s111.033 248 248 248zm-103.398-76.72l53.411-53.411c31.806 13.506 68.128 13.522 99.974 0l53.411 53.411c-63.217 38.319-143.579 38.319-206.796 0zM336 256c0 44.112-35.888 80-80 80s-80-35.888-80-80 35.888-80 80-80 80 35.888 80 80zm91.28 103.398l-53.411-53.411c13.505-31.806 13.522-68.128 0-99.974l53.411-53.411c38.319 63.217 38.319 143.579 0 206.796zM359.397 84.72l-53.411 53.411c-31.806-13.505-68.128-13.522-99.973 0L152.602 84.72c63.217-38.319 143.579-38.319 206.795 0zM84.72 152.602l53.411 53.411c-13.506 31.806-13.522 68.128 0 99.974L84.72 359.398c-38.319-63.217-38.319-143.579 0-206.796z' class='' data-ce-key='501'></path></svg>Expert Technical Support</h4>
                <p>Without a valid license key you will no longer be able to receive updates or support for the Custom Facebook Feed plugin. A renewed license key grants you access to our top-notch, quick and effective support for another full year.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='unlock' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512' class='svg-inline--fa fa-unlock fa-w-14 fa-2x' data-ce-key='477'><path fill='currentColor' d='M400 256H152V152.9c0-39.6 31.7-72.5 71.3-72.9 40-.4 72.7 32.1 72.7 72v16c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24v-16C376 68 307.5-.3 223.5 0 139.5.3 72 69.5 72 153.5V256H48c-26.5 0-48 21.5-48 48v160c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V304c0-26.5-21.5-48-48-48z' class='' data-ce-key='478'></path></svg>All Pro Facebook Feed Features</h4>
                <p>Photos & Albums, Videos (HD, 360, Live), Facebook Events, Popup Lightbox, Likes, Shares, & Reactions, Comments and Replies, Filter Posts, Post Layouts, Load More Posts, Multi-column Grid Layout, Background Caching, and more!</p>
            </div>";

        if( $cff_check_license && !$cff_license_expired && !$cff_expire_countdown ){
            $cff_expired_box_classes = "cff-license-expired cff-license-valid";
            $cff_expired_box_msg = "Thanks ".$cff_license_data["customer_name"].", your Custom Facebook Feed Pro license key is valid.";
        }

        _e("
        <div class='".$cff_expired_box_classes."'>");
            if( $cff_expire_countdown ) _e("<a style='float:right; color: #dd3d36; text-decoration: none;' href='" .esc_url( add_query_arg( 'cff_nag_ignore', '0' ) ). "'>Dismiss</a>");
            _e("<p>".$cff_expired_box_msg."
        </div>
        <script type='text/javascript'>
        function cffShowReasons() {
            document.getElementById('cff-why-renew').style.display = 'block';
            document.getElementById('cff-why-renew-show').style.display = 'none';
            document.getElementById('cff-why-renew-hide').style.display = 'inline-block';
        }
        function cffHideReasons() {
            document.getElementById('cff-why-renew').style.display = 'none';
            document.getElementById('cff-why-renew-show').style.display = 'inline-block';
            document.getElementById('cff-why-renew-hide').style.display = 'none';
        }
        </script>
        ");
    }

}

add_action('admin_init', 'cff_nag_ignore');
function cff_nag_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['cff_nag_ignore']) && '0' == $_GET['cff_nag_ignore'] ) {
             add_user_meta($user_id, 'cff_ignore_notice', 'true', true);
    }
}


/* Display a notice regarding PPCA changes, which can be dismissed */
add_action('admin_notices', 'cff_ppca_notice');
function cff_ppca_notice() {

    global $current_user;
    $user_id = $current_user->ID;

    $cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
    $cap = apply_filters( 'cff_settings_pages_capability', $cap );
    if( !current_user_can( $cap ) ) return;

    // Use this to show notice again
    // delete_user_meta($user_id, 'cff_ignore_ppca_notice');

    /* Check whether it's an app token or if the user hasn't already clicked to ignore the message */
    if( get_user_meta($user_id, 'cff_ignore_ppca_notice') ) return;

    $page_id = get_option( 'cff_page_id' );
    $cff_access_token = get_option( 'cff_access_token' );

    if( $page_id && $cff_access_token ){

        //Make a call to the API to see whether the ID and token are for the same Facebook page.
        $cff_ppca_check_url = 'https://graph.facebook.com/v8.0/'.$page_id.'/posts?limit=1&access_token='.$cff_access_token;

        //Store the response in a transient which is deleted and then reset if the settings are saved.
        if ( ! get_transient( 'cff_ppca_admin_check' ) ) {
            //Get the contents of the API response
            $cff_ppca_admin_check_response = CFF_Utils::cff_fetchUrl($cff_ppca_check_url);
            set_transient( 'cff_ppca_admin_check', $cff_ppca_admin_check_response, YEAR_IN_SECONDS );

            $cff_ppca_admin_check_json = json_decode($cff_ppca_admin_check_response);
        } else {
            $cff_ppca_admin_check_response = get_transient( 'cff_ppca_admin_check' );
            //If we can't find the transient then fall back to just getting the json from the api
            if ($cff_ppca_admin_check_response == false) $cff_ppca_admin_check_response = CFF_Utils::cff_fetchUrl($cff_ppca_check_url);

            $cff_ppca_admin_check_json = json_decode($cff_ppca_admin_check_response);
        }

        //If there's a PPCA error or it's a multifeed then display notice
        if( ( isset($cff_ppca_admin_check_json->error->message) && strpos($cff_ppca_admin_check_json->error->message, 'Public Content Access') ) || strpos( $page_id, ',') != false ){
            _e("
            <div class='cff-license-expired'>
                <a class='cff-admin-notice-close' href='" .esc_url( add_query_arg( 'cff_nag_ppca_ignore', '0' ) ). "'>Don't show again<i class='fa fa-close' style='margin-left: 5px;'></i></a>
                <p style='min-height: 22px;'><img src='" . plugins_url( 'admin/assets/img/fb-icon.png' , __FILE__ ) . "' style='float: left; width: 22px; height: 22px; margin-right: 12px; border-radius: 5px; box-shadow: 0 0 1px 0 #BA7B7B;'>
                <b>Action required: PPCA Error.</b> <span style='margin-right: 10px;'>Due to Facebook API changes it is no longer possible to display feeds from Facebook Pages you are not an admin of. Please <a href='https://smashballoon.com/facebook-ppca-error-notice/' target='_blank'>see here</a> for more information.</span><a href='admin.php?page=cff-top' class='cff-admin-notice-button'>Go to Facebook Feed Settings</a></p>
            </div>
            ");
        }
    }

}
//If PPCA notice is dismissed then don't show again
add_action('admin_init', 'cff_nag_ppca_ignore');
function cff_nag_ppca_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['cff_nag_ppca_ignore']) && '0' == $_GET['cff_nag_ppca_ignore'] ) {
             add_user_meta($user_id, 'cff_ignore_ppca_notice', 'true', true);
    }
}


// Add a Settings link to the plugin on the Plugins page
$cff_plugin_file = 'custom-facebook-feed-pro/custom-facebook-feed.php';
add_filter( "plugin_action_links_{$cff_plugin_file}", 'cff_add_settings_link', 10, 2 );

//modify the link by unshifting the array
function cff_add_settings_link( $links, $file ) {
    $cff_settings_link = '<a href="' . admin_url( 'admin.php?page=cff-feed-builder' ) . '">' . __( 'Settings', 'custom-facebook-feed' ) . '</a>';
    array_unshift( $links, $cff_settings_link );

    return $links;
}

//Delete cache
function cff_delete_cache(){
    global $wpdb;

    $cache_table_name = $wpdb->prefix . 'cff_feed_caches';

    $sql = "
    UPDATE $cache_table_name
    SET cache_value = ''
    WHERE cache_key = 'posts';";
    $wpdb->query( $sql );

    $table_name = $wpdb->prefix . "options";
    $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_cff\_%')
        " );
    $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_cff\_ej\_%')
        " );
    $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_cff\_tle\_%')
        " );
    $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_cff\_album\_%')
        " );
    $wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_cff\_%')
        " );


    //Clear cache of major caching plugins
    if(isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')){
        $GLOBALS['wp_fastest_cache']->deleteCache();
    }
    //WP Super Cache
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
    }
    //W3 Total Cache
    if (function_exists('w3tc_flush_all')) {
        w3tc_flush_all();
    }
    if (function_exists('sg_cachepress_purge_cache')) {
        sg_cachepress_purge_cache();
    }

    // Litespeed Cache (older method)
    if ( method_exists( 'LiteSpeed_Cache_API', 'purge' ) ) {
        LiteSpeed_Cache_API::purge( 'esi.custom-facebook-feed' );
    }

    // Litespeed Cache (new method)
    if(has_action('litespeed_purge')) {
        do_action( 'litespeed_purge', 'esi.custom-facebook-feed' );
    }

	if( has_action( 'litespeed_purge_all' ) ) {
		do_action( 'litespeed_purge_all' );
	}
}

//Cron job to clear transients
add_action('cff_cron_job', 'cff_cron_clear_cache');
function cff_cron_clear_cache() {
    //Delete all transients unless it's using background caching
    if( get_option('cff_caching_type') != 'background' ) cff_delete_cache();
}

//Add custom cron schedule
add_filter( 'cron_schedules', 'cff_cron_custom_interval' );
function cff_cron_custom_interval( $schedules ) {
    $schedules['30mins'] = array(
        'interval' => 30*60,
        'display' => __( 'Every 30 minutes' )
    );
    return $schedules;
}

//Cron For The Group Posts
add_action('group_post_scheduler_cron', 'cff_group_cache_function');
function cff_group_cache_function(){
    CustomFacebookFeed\CFF_Group_Posts::cron_update_group_persistent_cache();
}

/**
 * Finds records in the cff_feed_caches table that can be updated in the background
 * then loops through them and updates them.
 *
 * If there are more feeds than a single batch can handle, subsequent background
 * updates are scheduled for additional batches using the single cron event cff_cron_additional_batch
 *
 * @since 4.0
 */
function cff_v4_do_background_updates() {
    $cron_records = CustomFacebookFeed\Builder\CFF_Db::feed_caches_query( array( 'cron_update' => true ) );

    $num = count( $cron_records );
    if ( $num === CustomFacebookFeed\Builder\CFF_Db::RESULTS_PER_CRON_UPDATE ) {
        wp_schedule_single_event( time() + 120, 'cff_cron_additional_batch' );
    }

    cff_v4_update_batch( $cron_records );
}

/**
 * If there are enough background updates for multiple batches, the
 * additional batches are processed using a one time cron event
 *
 * @since 4.0
 */
add_action( 'cff_cron_additional_batch', 'cff_v4_process_additional_batch' );
function cff_v4_process_additional_batch() {
    $args = array(
        'cron_update' => true,
        'additional_batch' => true,
    );
    $cron_records = CustomFacebookFeed\Builder\CFF_Db::feed_caches_query( $args );
    $num = count( $cron_records );

    if ( $num === CustomFacebookFeed\Builder\CFF_Db::RESULTS_PER_CRON_UPDATE ) {
        wp_schedule_single_event( time() + 120, 'cff_cron_additional_batch' );
    }

    cff_v4_update_batch( $cron_records );
}

/**
 * Takes a db query of caches and processes them for updates from the API.
 *
 * @param array $cron_records
 *
 * @since 4.0
 */
function cff_v4_update_batch( $cron_records ) {
    foreach ( $cron_records as $cron_record ) {
        $feed_id = (int)$cron_record['feed_id'];
	    $post_set = new \CustomFacebookFeed\Builder\CFF_Post_Set( $feed_id );

	    $post_set->init();

	    $feed_cache = new \CustomFacebookFeed\CFF_Cache( $feed_id );
	    $feed_cache->retrieve_and_set();

	    $cache_type = $cron_record['cache_key'];

	    if ( $cache_type === 'header' ) {
		    $settings = $post_set->get_feed_settings();
            $header_details = array();

		    if ( isset( $settings['sources'][0] ) ) {
			    $args = array(
				    'id' => $settings['sources'][0]
			    );
			    $results = \CustomFacebookFeed\Builder\CFF_Db::source_query( $args );

			    $header_details = \CustomFacebookFeed\CFF_Utils::fetch_header_data( $results[0]['account_id'], $results[0]['account_type'] === 'group', $results[0]['access_token'], 0, false, '' );
		    }
		    $feed_cache->update_or_insert( $cache_type, $header_details );

        } elseif ( $cache_type === 'posts' ) {
		    $feed_cache->clear( 'all' );
		    $feed_cache->clear( 'posts' );
		    $settings = $post_set->get_converted_settings();

			if ( ! empty( $settings['sources'] ) ) {
				$data_att_html = \CustomFacebookFeed\CFF_Shortcode::cff_get_shortcode_data_attribute_html_static( array( 'feed'=> $feed_id ) );
				$data = \CustomFacebookFeed\CFF_Shortcode::cff_get_json_data( $settings, null, $data_att_html );
			}
        }
    }
}

//Cron job to get_set_cache
add_action('cff_cache_cron', 'cff_cache_cron_function');
function cff_cache_cron_function() {
    global $wpdb;
    $table_name = $wpdb->prefix . "options";
    $cff_transients_raw = $wpdb->get_results( "
        SELECT `option_name` AS `name`, `option_value` AS `value`
        FROM  $table_name
        WHERE `option_name` LIKE ('%\_transient\_cff\_%')
        ORDER BY `option_name`
        " );
    $cff_transients = array();

    // Feed Cron
    // cff_delete_cache(); Don't clear the cache here as it should just replace the transient below instead
	$encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption;

    //Loop through the transients
    foreach ( $cff_transients_raw as $result ) {

        //Remove _transient_ prefix from the transient name as it's added automatically when the transient is set
        $result_name = $result->name;
        $prefix = '_transient_';
        if (substr($result_name, 0, strlen($prefix)) == $prefix) $result_name = substr($result_name, strlen($prefix));

        //Don't re set the meta transients as they can be fetched on page load, don't recheck the timeline events as they rarely change, and only recheck the initial feed cache (whose transient name only contains one underscore)
        if ( 0 !== strpos( $result_name, 'cff_meta' ) && substr( $result_name, 0, 8 ) !== "cff_tle_" && substr_count($result_name, '_') == 1 ) {

			if ( ! empty( $result->value ) && $encryption->decrypt( $result->value ) ) {
				$result_value = $encryption->decrypt( $result->value );
			} else {
				$result_value = maybe_unserialize( $result->value );
			}

            //Create an array of the transients
            $cff_transients[ $result_name ] = $result_value;

            //Get the API URL from the JSON array
            $existing_data = json_decode($result_value);
            $cff_posts_json_url = $existing_data->api_url;

            //Get the contents of the Facebook page
            $new_posts_json = CFF_Utils::cff_fetchUrl($cff_posts_json_url);
            $FBdata = json_decode($new_posts_json);

            //Check whether the JSON is wrapped in a "data" property as if it doesn't then it's a featured post
            $prefix_data = '{"data":';
            (substr($new_posts_json, 0, strlen($prefix_data)) == $prefix_data) ? $cff_featured_post = false : $cff_featured_post = true;

            //Add API URL to beginning of JSON array
            $prefix = '{';
            if (substr($new_posts_json, 0, strlen($prefix)) == $prefix) $new_posts_json = substr($new_posts_json, strlen($prefix));
            $new_posts_json = '{"api_url":"'.$cff_posts_json_url.'", ' . $new_posts_json;

            $encryption = new SB_Facebook_Data_Encryption();
            $new_posts_json = $encryption->maybe_encrypt( $new_posts_json );

            //If it's a featured post then it doesn't contain 'data'
            ( $cff_featured_post ) ? $FBdata = $FBdata : $FBdata = $FBdata->data;

            if( !empty($FBdata) ) {

                //Error returned by API
                if( isset($FBdata->error) ){
                    //Delete the transient. Then it will just use the backup cache on page load.
                    delete_transient( $result_name );
                } else {
                    //If there's no error then set the backup cache for 6 months
                    set_transient( '!cff_backup_' . $result_name, $new_posts_json, YEAR_IN_SECONDS );
                }
                //Cache the JSON
                set_transient( $result_name, $new_posts_json, 7 * DAY_IN_SECONDS );
            }


        } else {

            //Delete the meta transients so they can be re set when the pages loads
            delete_transient( $result_name );
            $wpdb->query( "
            DELETE
            FROM $table_name
            WHERE `option_name` LIKE ('%\_transient\_cff\_meta\_%')
            " );
        }

    } // End foreach

    // updated cache
    cff_v4_do_background_updates();

    //Clear cache of major caching plugins
    if(isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')){
        $GLOBALS['wp_fastest_cache']->deleteCache();
    }
    //WP Super Cache
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
    }
    //W3 Total Cache
    if (function_exists('w3tc_flush_all')) {
        w3tc_flush_all();
    }

}

add_action('admin_init', 'cff_disable_welcome');
function cff_disable_welcome() {
    global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['cff_disable_welcome']) && '0' == $_GET['cff_disable_welcome'] ) {
             add_user_meta($user_id, 'cff_disable_welcome', 'true', true);
    }
}

function cff_mobile_cols_tooltip() {
    ?>
        <a class="cff-tooltip-link" href="JavaScript:void(0);"><?php _e('Different number for mobile?'); ?></a>
        <p class="cff-tooltip cff-more-info"><?php _e("To change the number of columns for mobile devices, use 'colsmobile=(number)' in the shortcode. e.g. colsmobile=3"); ?></p>
<?php
        }

function cff_admin_hide_unrelated_notices() {

    // Bail if we're not on a sbi screen or page.
    if ( ! isset( $_GET['page'] ) || strpos( $_GET['page'], 'cff') === false ) {
        return;
    }

    // Extra banned classes and callbacks from third-party plugins.
    $blacklist = array(
        'classes'   => array(),
        'callbacks' => array(
            'cffdb_admin_notice', // 'Database for sbi' plugin.
        ),
    );

    global $wp_filter;

    foreach ( array( 'user_admin_notices', 'admin_notices', 'all_admin_notices' ) as $notices_type ) {
        if ( empty( $wp_filter[ $notices_type ]->callbacks ) || ! is_array( $wp_filter[ $notices_type ]->callbacks ) ) {
            continue;
        }
        foreach ( $wp_filter[ $notices_type ]->callbacks as $priority => $hooks ) {
            foreach ( $hooks as $name => $arr ) {
                if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
                    unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
                    continue;
                }
                $class = ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) ? strtolower( get_class( $arr['function'][0] ) ) : '';
                if (
                    ! empty( $class ) &&
                    strpos( $class, 'cff' ) !== false &&
                    ! in_array( $class, $blacklist['classes'], true )
                ) {
                    continue;
                }
                if (
                    ! empty( $name ) && (
                        strpos( $name, 'cff' ) === false ||
                        in_array( $class, $blacklist['classes'], true ) ||
                        in_array( $name, $blacklist['callbacks'], true )
                    )
                ) {
                    unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
                }
            }
        }
    }
}
add_action( 'admin_print_scripts', 'cff_admin_hide_unrelated_notices' );

function cff_add_caps() {
    global $wp_roles;

    $wp_roles->add_cap( 'administrator', 'manage_custom_facebook_feed_options' );

}
add_action( 'admin_init', 'cff_add_caps', 90 );

function cff_reset_resized() {
    check_ajax_referer( 'cff_nonce' , 'cff_nonce');

    $cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
    $cap = apply_filters( 'cff_settings_pages_capability', $cap );
    if ( ! current_user_can( $cap ) ) {
        wp_send_json_error(); // This auto-dies.
    }

    CFF_Resizer::delete_resizing_table_and_images();
    \cff_main_pro()->cff_error_reporter->add_action_log( 'Reset resizing tables.' );
    echo CFF_Resizer::create_resizing_table_and_uploads_folder();

    die();
}
add_action( 'wp_ajax_cff_reset_resized', 'cff_reset_resized' );

//PPCA token checks
function cff_ppca_token_check_flag() {
    check_ajax_referer( 'cff_nonce' , 'cff_nonce');

    $cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
    $cap = apply_filters( 'cff_settings_pages_capability', $cap );
    if ( ! current_user_can( $cap ) ) {
        wp_send_json_error(); // This auto-dies.
    }
    if( get_transient('cff_ppca_access_token_invalid') ){
        print_r(true);
    } else {
        print_r(false);
    }

    die();
}
add_action( 'wp_ajax_cff_ppca_token_check_flag', 'cff_ppca_token_check_flag' );


function cff_after_access_token_retrieved( $page_id_val, $cff_reviews_active ) {
if( $_GET['cff_final_response'] == 'true' ){

        \cff_main_pro()->cff_error_reporter->remove_error( 'accesstoken' );

        $access_token = preg_replace( '/[^A-Za-z0-9 ]/', '', sanitize_text_field( wp_unslash( $_GET['cff_access_token'] ) ) );
        $cff_is_groups = false;
        $pages_data_arr = '';
        $groups_data_arr = '';

        if( isset($_GET['cff_group']) ){
            //Get Groups

            $cff_is_groups = true;
            $groups_data_arr = '';

            //Extend the user token by making a call to /me/accounts. User must be an admin of a page for this to work as won't work if the response is empty.
            $url = 'https://graph.facebook.com/me/accounts?limit=500&access_token='.$access_token;

            $accounts_data = CFF_Utils::cff_fetchUrl($url);
            $accounts_data_arr = json_decode($accounts_data);
            $cff_token_expiration = 'never';
            if( empty($accounts_data_arr->data) ){
                $cff_token_expiration = '60 days';
            }

            //Get User ID
            $user_url = 'https://graph.facebook.com/me?fields=id&access_token='.$access_token;
            $user_id_data = CFF_Utils::cff_fetchUrl($user_url);

            if( !empty($user_id_data) ){
                $user_id_data_arr = json_decode($user_id_data);
                $user_id = $user_id_data_arr->id;

                //Get groups they're admin of
                $groups_admin_url = 'https://graph.facebook.com/'.$user_id.'/groups?admin_only=true&fields=name,id,picture&access_token='.$access_token;
                $groups_admin_data = CFF_Utils::cff_fetchUrl($groups_admin_url);
                $groups_admin_data_arr = json_decode($groups_admin_data);

                //Get member groups
                $groups_url = 'https://graph.facebook.com/'.$user_id.'/groups?admin_only=false&fields=name,id,picture&access_token='.$access_token;
                $groups_data = CFF_Utils::cff_fetchUrl($groups_url);
                $groups_data_arr = json_decode($groups_data);

                // $pages_data_arr = $groups_data_arr;
            }
        } else {
            //Get Pages

            $url = 'https://graph.facebook.com/me/accounts?fields=access_token,name,id&limit=500&access_token='.$access_token;

            $pages_data = CFF_Utils::cff_fetchUrl($url);
            $pages_data_arr = json_decode($pages_data);

            if( empty($pages_data_arr->data) ){
            //If they don't manage any pages then just use the user token instead
            ?>
                <script type='text/javascript'>
                jQuery(document).ready(function($) {
                    $('#cff_access_token').val('<?php echo esc_attr( $access_token ) ?>').addClass('cff-success');
                    //Check the own access token setting so it reveals token field
                    if( $('#cff_show_access_token:checked').length < 1 ){
                        $("#cff_show_access_token").trigger("change").prop( "checked", true );
                    }
                });
                </script>
            <?php
            }

        }


        if( !empty($pages_data_arr->data) || $cff_is_groups ){
        //Show the pages they manage
            echo '<div id="cff_fb_login_modal" class="cff_modal_tokens cffnomodal">';
            echo '<div class="cff_modal_box">';
            echo '<div class="cff-managed-pages">';

            if( $cff_is_groups ){
                //GROUPS

                if( empty($groups_data_arr->data) && empty($groups_admin_data_arr->data) ){
                    echo '<h3>No Groups Returned</h3>';
                    echo "<p>Facebook has not returned any groups for your user. It is only possible to display a feed from a group which you are either an admin or a member. Please note, if you are not an admin of the group then it is required that an admin add our app in the group settings in order to display a feed.</p><p>Please either create or join a Facebook group and then follow the directions when connecting your account on this page.</p>";
                    echo '<a href="JavaScript:void(0);" class="button button-primary" id="cff-close-modal-primary-button">Close</a>';
                } else {

                    \cff_main_pro()->cff_error_reporter->remove_error( 'accesstoken' );

                    echo '<div class="cff-groups-list">';
                        echo '<p style="margin-top: 0;"><i class="fa fa-check-circle" aria-hidden="true" style="font-size: 15px; margin: 0 8px 0 2px;"></i>Select a Facebook group below to get an Access Token.</p>';

                        echo '<div class="cff-pages-wrap">';
                        //Admin groups
                        foreach ( $groups_admin_data_arr->data as $page => $group_data ) {
                            echo '<div class="cff-managed-page cff-group-admin';
                            if( $group_data->id == $page_id_val ) echo ' cff-page-selected';
                            echo '" data-token="'. esc_attr( $access_token ) .'" data-page-id="'.$group_data->id.'" id="cff_'.$group_data->id.'" data-pagetype="group">';
                            echo '<p>';
                            if( isset( $group_data->picture->data->url ) ) echo '<img class="cff-page-avatar" border="0" height="50" width="50" src="'.$group_data->picture->data->url.'">';
                            echo '<b class="cff-page-info-name">'.$group_data->name.'</b><span class="cff-page-info">(Group ID: '.$group_data->id.')</span></p>';
                            echo '<div class="cff-group-admin-icon"><i class="fa fa-user" aria-hidden="true"></i> Admin</div>';
                            echo '</div>';
                        }
                        //Member groups
                        foreach ( $groups_data_arr->data as $page => $group_data ) {
                            echo '<div class="cff-managed-page';
                            if( $group_data->id == $page_id_val ) echo ' cff-page-selected';
                            echo '" data-token="'. esc_attr( $access_token ) .'" data-page-id="'.$group_data->id.'" id="cff_'.$group_data->id.'" data-pagetype="group">';
                            echo '<p>';
                            if( isset( $group_data->picture->data->url ) ) echo '<img class="cff-page-avatar" border="0" height="50" width="50" src="'.$group_data->picture->data->url.'">';
                            echo '<b class="cff-page-info-name">'.$group_data->name.'</b><span class="cff-page-info">(Group ID: '.$group_data->id.')</span></p>';
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '<a href="JavaScript:void(0);" class="button button-primary cff-group-btn" id="cff-insert-token" disabled="disabled">Use token for this Group</a>';
                        $date_group_note = date("Y-m-d") >= '2021-05-24';
                        if($date_group_note) echo '<div class="cff-note-notice cff-note-group-notice"><strong>' . esc_html__('Please note: ','custom-facebook-feed') . '</strong>' . esc_html__('Due to a Facebook API limitation, only content posted to this group in the past 90 days can be displayed.','custom-facebook-feed') . ' <a href="'.esc_url('https://smashballoon.com/doc/facebook-api-change-limits-groups-to-90-days/').'" target="_blank">'. esc_html__('See here','custom-facebook-feed') .'</a> ' . esc_html__('or more information.','custom-facebook-feed') . '</div>';
                        if( $cff_token_expiration == "60 days" ) echo '<div id="cff_token_expiration_note" class="cff-error"><b>Important:</b> This token will expire in 60 days.<br /><a href="https://smashballoon.com/extending-a-group-access-token-so-it-never-expires/" target="_blank">Extend token so it never expires</a></div>';
                    echo '</div>';

                    echo '<div id="cff-group-installation">';
                        echo '<h3>Important</h3>';

                        echo '<div id="cff-group-admin-directions">';
                            echo '<p>To display a feed from your group you need to add our app in your Facebook group settings:</p>';
                            echo '<ul>';
                            echo '<li><b>1)</b> Go to your group settings page by clicking <a id="cff-group-edit" href="https://www.facebook.com/groups/" target="_blank">here<i class="fa fa-external-link" aria-hidden="true" style="font-size: 13px; position: relative; top: 2px; margin-left: 5px;"></i></a></li>';
                            echo '<li><b>2)</b> In the "Apps" section click "Add Apps".</li>';
                            echo '<li><b>3)</b> Search for "Smash Balloon" and select our app (<a id="cff-group-app-tooltip">screenshot</a>).<img id="cff-group-app-screenshot" src="'. plugins_url( "admin/assets/img/group-app.png" , __FILE__ ) .'" alt="Thumbnail Layout" /></li>';
                            echo '<li><b>4</b>) Click "Add".</li>';
                            echo '</ul>';

                            echo '<p style="margin-bottom: 10px;">You can now use the plugin to display a feed from your group.</p>';
                        echo '</div>';

                        echo '<div id="cff-group-member-directions">';
                            echo '<p>To display a feed from this group an admin needs to first add our app in the group settings. Please ask an admin to follow the directions <a href="https://smashballoon.com/adding-our-app-to-a-facebook-group/" target="_blank">here</a> to add our app.</p>';
                            echo '<p>Once this is done you will then be able to display a feed from this group.</p>';
                        echo '</div>';

                        echo '<a href="JavaScript:void(0);" class="button button-primary" id="cff-close-modal-primary-button">Done</a>';
                        echo '<a href="https://smashballoon.com/display-facebook-group-feed/" target="_blank" class="button button-secondary"><i class="fa fa-life-ring"></i> Help</a>';
                    echo '</div>';

                }

            } else {
                //PAGES
                \cff_main_pro()->cff_error_reporter->remove_error( 'accesstoken' );

                echo '<p class="cff-tokens-note"><i class="fa fa-check-circle" aria-hidden="true" style="font-size: 15px; margin: 0 8px 0 2px;"></i> Select a Facebook page below to connect it.</p>';

                echo '<div class="cff-pages-wrap">';
                foreach ( $pages_data_arr->data as $page => $page_data ) {
                    echo '<div class="cff-managed-page ';
                    if( $page_data->id == $page_id_val ) echo 'cff-page-selected';
                    echo '" data-token="'.$page_data->access_token.'" data-page-id="'.$page_data->id.'" data-pagetype="page">';
                    echo '<p><img class="cff-page-avatar" border="0" height="50" width="50" src="https://graph.facebook.com/'.$page_data->id.'/picture"><b class="cff-page-info-name">'.$page_data->name.'</b><span class="cff-page-info">(Page ID: '.$page_data->id.')</span></p>';
                    echo '</div>';
                }
                echo '</div>';

                $cff_use_token_text = 'Connect this page';
                // if( $cff_reviews_active ) $cff_use_token_text = 'Connect for Regular Feeds';
                echo '<a href="JavaScript:void(0);" id="cff-insert-token" class="button button-primary" disabled="disabled">'.$cff_use_token_text.'</a>';
                if( !$cff_reviews_active ) echo '<a href="JavaScript:void(0);" id="cff-insert-all-tokens" class="button button-secondary cff_connect_all">Connect All</a>';
                echo "<a href='https://smashballoon.com/facebook-pages-im-admin-of-arent-listed-after-authorizing-plugin/' target='_blank' class='cff-connection-note'>One of my pages isn't listed</a>";

                // if( $cff_reviews_active ){
                //     echo '<a href="JavaScript:void(0);" class="button button-secondary cff-insert-reviews-token" disabled="disabled">Connect for Reviews Feed</a>';
                //     echo '<a href="JavaScript:void(0);" class="button button-secondary cff-insert-both-tokens" disabled="disabled">Connect for both</a>';
                // }

            }

            echo '</div>';
            echo '<a href="JavaScript:void(0);" class="cff-modal-close"><i class="fa fa-times"></i></a>';
            echo '</div>';
            echo '</div>';

            echo '<a href="JavaScript:void(0);" class="cff_admin_btn" id="cff_fb_show_tokens"><i class="fa fa-th-list" aria-hidden="true" style="font-size: 14px; margin-right: 8px;"></i>';
            $cff_is_groups ? _e( "Show Available Groups", "custom-facebook-feed" ) : _e( "Show Available Pages", "custom-facebook-feed" );
            echo '</a>';

        }

    }
}

function cff_get_current_time() {
    $current_time = time();

    // where to do tests
     //$current_time = strtotime( 'November 25, 2020' );

    return $current_time;
}

function cff_admin_modal( $admin_url_state ) {
    ?>
    <div id="cff_fb_login_modal">
                <div class="cff_modal_box">

                    <p>Log into your Facebook account using the button below and approve the plugin to connect your account.</p>


                    <div class="cff-login-options">
                        <label for="cff_login_type">Would you like to display a Facebook Page or Group?</label>
                        <select id="cff_login_type">
                            <option value="page">Facebook Page</option>
                            <option value="group">Facebook Group</option>
                        </select>

                        <p>
                            <a href="javascript:void(0);" id="cff_admin_cancel_btn" class="cff-admin-cancel-btn">Cancel</a>


        <a href="https://api.smashballoon.com/v2/facebook-login.php?state=<?php echo $admin_url_state; ?>" class="cff_admin_btn" id="cff_page_app"><i class="fa fa-facebook-square"></i> <?php _e( 'Continue', 'custom-facebook-feed' ); ?></a>

        <a href="https://api.smashballoon.com/v2/facebook-group-login.php?state=<?php echo $admin_url_state; ?>" class="cff_admin_btn" id="cff_group_app"><i class="fa fa-facebook-square"></i> <?php _e( 'Continue', 'custom-facebook-feed' ); ?></a>

        </p>
    </div>

        <p style="font-size: 11px; margin-top: 25px;"><b>Please note:</b> this does not give us permission to manage your Facebook pages or groups, it simply allows the plugin to see a list that you manage and retrieve an Access Token.</p>

    </div>
</div>
<?php
}


function cff_oembed_disable() {
     check_ajax_referer( 'cff_nonce' , 'cff_nonce');

    $cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
    $cap = apply_filters( 'cff_settings_pages_capability', $cap );
    if ( ! current_user_can( $cap ) ) {
        wp_send_json_error(); // This auto-dies.
    }

    $oembed_settings = get_option( 'cff_oembed_token', array() );
    $oembed_settings['access_token'] = '';
    $oembed_settings['disabled'] = true;
    echo '<strong>';
    if ( update_option( 'cff_oembed_token', $oembed_settings ) ) {
        _e( 'Facebook oEmbeds will no longer be handled by Custom Facebook Feed.', 'custom-facebook-feed' );
    } else {
        _e( 'An error occurred when trying to disable your oEmbed token.', 'custom-facebook-feed' );
    }
    echo '</strong>';

    die();
}
add_action( 'wp_ajax_cff_oembed_disable', 'cff_oembed_disable' );


/**
 * Adds CSS to the end of the customizer "Additonal CSS" setting
 *
 * @param $custom_css
 *
 * @return bool|int
 *
 * @since 4.0.2/4.0.7
 */
function cff_transfer_css( $custom_css ) {
    $value   = '';
    $post    = wp_get_custom_css_post( get_stylesheet() );
    if ( $post ) {
        $value = $post->post_content;
    }
    $value .= "\n\n/* Custom Facebook Feed */\n" . $custom_css . "\n/* Custom Facebook Feed - End */";

    $r = wp_update_custom_css_post(
        $value,
        array(
            'stylesheet' => get_stylesheet(),
        )
    );

    if ( $r instanceof WP_Error ) {
        return false;
    }
    $post_id = $r->ID;

    return $post_id;
}

/**
 * Validates CSS to detect anything that might be harmful
 *
 * @param $css
 *
 * @return bool|WP_Error
 *
 * @since 4.0.2/4.0.7
 */
function cff_validate_css( $css ) {
    $validity = new WP_Error();

    if ( preg_match( '#</?\w+#', $css ) ) {
        $validity->add( 'illegal_markup', __( 'Markup is not allowed in CSS.' ) );
    }

    if ( ! $validity->has_errors() ) {
        $validity = true;
    }
    return $validity;
}

/**
 * Check to see if CSS has been transferred
 *
 * @since 4.0.2/4.0.7
 */
function cff_check_custom_css() {
    $cff_style_settings = get_option( 'cff_style_settings', array() );
    $custom_css = isset( $cff_style_settings['cff_custom_css'] ) ? stripslashes( trim( $cff_style_settings['cff_custom_css'] ) ) : '';

    // only try once
    if ( empty( $custom_css ) ) {
        return;
    }

    // custom css set to nothing after trying the update once
    $cff_style_settings['cff_custom_css_read_only'] = $custom_css;
    $cff_style_settings['cff_custom_css'] = '';
    update_option( 'cff_style_settings', $cff_style_settings );
    if ( ! function_exists( 'wp_get_custom_css_post' )
        || ! function_exists( 'wp_update_custom_css_post' ) ) {
        return;
    }

    // make sure this is valid CSS or don't transfer
    if ( is_wp_error( cff_validate_css( $custom_css ) ) ) {
        return;
    }

    cff_transfer_css( $custom_css );
}
add_action( 'init', 'cff_check_custom_css' );
