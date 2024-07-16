<?php
$templatePath = get_template_directory();

// Register Custom Navigation Walker include custom menu widget to use walkerclass
include_once $templatePath . '/lib/wp_bootstrap_navwalker.php';

// Load the settings field for the Applications API
//include_once dirname(__FILE__) . '/api/admin-settings.php';

// Load the functions for the Applications API
include_once dirname(__FILE__) . '/api/v2/functions.php';

include_once $templatePath . '/classes/makerfaire-helper.php';
include_once $templatePath . '/classes/gf-rmt-helper.php';
include_once $templatePath . '/classes/mf-sharing-cards.php';
include_once $templatePath . '/classes/ICS.php';

// Legacy Helper Functions replacing VIP Wordpress.com calls
include_once $templatePath . '/classes/legacy-helper.php';

//cron job
include_once $templatePath . '/classes/cronJob.php';

// Include all function files in the makerfaire/functions directory:
foreach (glob($templatePath . '/functions/*.php') as $file) {
    include_once $file;
}

//include any subfolders like 'gravity_forms'
foreach (glob($templatePath . '/functions/*/*.php') as $file) {
    include_once $file;
}

// Include all custom post type files in the /cpt directory:
foreach (glob($templatePath . '/cpt/*.php') as $file) {
    include_once $file;
}

// add post-thumbnails support to theme
add_theme_support('post-thumbnails');
add_image_size('schedule-thumb', 140, 140, true);

//turn off wordpress feature that replaces double line breaks with paragraph elements.
remove_filter('the_content', 'wpautop');

/* Turn off secure file download. This was conflicting with LargeFS on wpengine */
add_filter('gform_secure_file_download_location', '__return_false');

// Define our current Version number using the stylesheet version
function my_wp_default_styles($styles) {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    $styles->default_version = $my_version;
}

add_action("wp_default_styles", "my_wp_default_styles");

/* Disable Conflicting Code using Filters */
add_filter('jetpack_enable_opengraph', '__return_false', 99);

// Turn off the WP_GOVERNOR, as it breaks S&F 
define('WPE_GOVERNOR', false);

function load_scripts() {
    wp_enqueue_script("jquery");
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');

    // Styles
    wp_enqueue_style('make-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css');
    wp_enqueue_style('make-bootstrapdialog', get_stylesheet_directory_uri() . '/css/bootstrap-dialog.min.css', true);
    wp_enqueue_style('make-styles', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version);
    if (is_admin()) {
        wp_enqueue_style('mf-datatables', get_stylesheet_directory_uri() . '/css/mf-datatables.css', '', '', true);
    }
    wp_enqueue_style('fancybox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/css/jquery.fancybox.min.css', '', 'all');
    wp_enqueue_style('universal-firstload.css', UNIVERSAL_MAKEHUB_ASSET_URL_PREFIX . 'wp-content/universal-assets/v2/css/universal-firstload.min.css', array(), $my_version);
    wp_enqueue_style('universal.css', UNIVERSAL_MAKEHUB_ASSET_URL_PREFIX . 'wp-content/universal-assets/v2/css/universal.min.css', array(), $my_version);

    // space time for timezone hijinks
    wp_enqueue_script('spacetime', 'https://unpkg.com/spacetime', array(), false, true);
    // select 2 for styling select
    wp_enqueue_script('select2', WP_PLUGIN_URL . '/search-filter-pro/public/assets/js/select2.min.js', array(), false, true);

    // Libraries concatenated by our npm build
    wp_enqueue_script('make-js', get_stylesheet_directory_uri() . '/js/built.min.js', array('jquery'), $my_version, true);
    // Universasl libraries:
    wp_enqueue_script('universal-auth0', UNIVERSAL_MAKEHUB_ASSET_URL_PREFIX . 'wp-content/universal-assets/v2/js/min/universal-auth0.min.js', array(), $my_version, true);
    wp_enqueue_script('universal', UNIVERSAL_MAKEHUB_ASSET_URL_PREFIX . 'wp-content/universal-assets/v2/js/min/universal.min.js', array(), $my_version, true);

    // Localize
    $user = wp_get_current_user();
    $auth0_user_data = null;
    // if user is logged in 
    if (isset($user->ID) && $user->ID != 0) {
        $user_meta = get_user_meta($user->ID);
        // wp_auth0_obj stores auth0 data for user in a json string. Not all users have user_metadata set in this string on first login, so let's test for that before setting auth0_user_data
        if (isset($user_meta['wp_auth0_obj'])) {
            if (str_contains($user_meta['wp_auth0_obj'][0], "user_metadata")) {
                $auth0_user_data = json_decode($user_meta['wp_auth0_obj'][0])->user_metadata;
            }
        }
    }
    wp_localize_script(
        'make-js',
        'ajax_object',
        array(
            'templateUrl' => get_stylesheet_directory_uri(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'home_url' => get_home_url(),
            'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
            'wp_user_email' => $user->user_email,
            'wp_user_nicename' => isset($auth0_user_data->first_name) && isset($auth0_user_data->last_name) ? $auth0_user_data->first_name . " " . $auth0_user_data->last_name : $user->display_name,
            'wp_user_avatar' => isset($auth0_user_data->picture) ? $auth0_user_data->picture : esc_url(get_avatar_url($user->user_email)),
            'wp_user_memlevel' => isset($auth0_user_data->membership_level) ? $auth0_user_data->membership_level : "",
        )
    );
    //VUE files for Maker Portal
    if (is_page('maker-portal')) {
        //the rest of the site uses bootstrap 3, for this page to work we need bootstrap 4
        wp_dequeue_style('make-bootstrap');
        wp_dequeue_style('make-bootstrapdialog');

        //<!-- Load required Bootstrap and BootstrapVue CSS -->
        wp_enqueue_style('vue-style', "https://unpkg.com/bootstrap/dist/css/bootstrap.min.css");
        wp_enqueue_style('bs-vue-style', "https://unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.css");

        //<!-- Load polyfills to support older browsers -->
        wp_enqueue_script('polyfill', "https://polyfill.io/v3/polyfill.min.js?features=es2015%2CIntersectionObserver");

        //<!-- Load Vue followed by BootstrapVue -->
        wp_enqueue_script('bootstrap', "https://unpkg.com/bootstrap@4.6.1/dist/js/bootstrap.min.js", array(), '', true);
        wp_enqueue_script('vue-js', "https://unpkg.com/vue@2.6.12/dist/vue.min.js", array(), '', true);
        wp_enqueue_script('bs-vue-js', "https://unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.js", array('vue-js', 'bootstrap'), '', true);
        wp_enqueue_script('axios', "https://unpkg.com/axios@1.6.8/dist/axios.min.js", array('vue-js'), '', true);
        wp_enqueue_script('maker-portal', get_stylesheet_directory_uri() . "/js/min/maker-portal.min.js", array('axios'), '', true);
        wp_localize_script('maker-portal', 'vueAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
}

add_action('wp_enqueue_scripts', 'load_scripts');

function remove_unnecessary_scripts() {
    if (is_admin()) {
        if (is_plugin_active('elementor/elementor.php')) {
            wp_deregister_script('elementor-ai');
            wp_dequeue_script('elementor-ai');
        }
    }
    if(is_page_template('page-entry.php')) {
        wp_deregister_script('spacetime');
        wp_dequeue_script('spacetime');
        wp_deregister_script('events-manager');
        wp_dequeue_script('events-manager');
    }
}
add_action('wp_print_scripts', 'remove_unnecessary_scripts', PHP_INT_MAX); // we want this to happen absolutely last

function remove_unnecessary_styles() {
    if (is_admin()) {
        wp_deregister_style('elementor-ai');
        wp_dequeue_style('elementor-ai');
    }
    if( is_page_template('page-entry.php') || is_page_template('page-schedule.php') || is_page_template('page-meet-the-makers.php') ) {
        wp_deregister_style('elementor-pro');
        wp_dequeue_style('elementor-pro');
        wp_deregister_style('elementor-frontend');
        wp_dequeue_style('elementor-frontend');
        wp_deregister_style('search-filter-plugin-styles');
        wp_dequeue_style('search-filter-plugin-styles');
        wp_deregister_style('e-animations');
        wp_dequeue_style('e-animations');
        wp_deregister_style('swiper');
        wp_dequeue_style('swiper');
        wp_deregister_style('fancybox');
        wp_dequeue_style('fancybox');
        wp_deregister_style('remodal');
        wp_dequeue_style('remodal');
        wp_deregister_style('remodal-default-them');
        wp_dequeue_style('remodal-default-them');
    }
}
add_action('wp_print_styles', 'remove_unnecessary_styles', PHP_INT_MAX); // we want this to happen absolutely last

// Load custom gravity forms js and css for all forms
function gravity_scripts($form, $is_ajax) {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    wp_enqueue_script('make-gravityformsallforms', get_stylesheet_directory_uri() . '/js/standalone/gravityformsallforms.js', array('jquery'), $my_version);
    wp_enqueue_style('gravity-styles', get_stylesheet_directory_uri() . '/css/gravity-style.min.css', array(), $my_version);
    wp_enqueue_style('make-gravityforms', get_stylesheet_directory_uri() . '/css/gravityforms.css');
    wp_enqueue_style('jquery-datetimepicker-css', get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css', '', '', true);
    wp_enqueue_script('jquery-datetimepicker', get_stylesheet_directory_uri() . '/js/standalone/jquery.datetimepicker.js', array('jquery'), '', true);
}

add_action('gform_enqueue_scripts', 'gravity_scripts', 10, 2);


function load_admin_scripts() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    //scripts
    wp_enqueue_script('make-gravityforms-admin', get_stylesheet_directory_uri() . '/js/standalone/gravityformsadmin.js', array('jquery', 'jquery-ui-tabs'), $my_version);
    wp_enqueue_script('make-fairesigns-admin', get_stylesheet_directory_uri() . '/js/standalone/mf_fairesigns.js', array('jquery'), $my_version);
    wp_enqueue_script('jquery-datetimepicker', get_stylesheet_directory_uri() . '/js/standalone/jquery.datetimepicker.js', array('jquery'), null);

    //wp_enqueue_script('make-bootstrap', get_stylesheet_directory_uri() . '/js/built-libs.min.js', array('jquery'));
    wp_enqueue_script('fontawesome5-js', 'https://kit.fontawesome.com/7c927d1b5e.js', array(), '', true);
    wp_enqueue_script('admin-scripts', get_stylesheet_directory_uri() . '/js/built-admin-scripts.min.js', array('jquery'), $my_version);

    //styles
    wp_enqueue_style('make-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css');
    wp_enqueue_style('jquery-datetimepicker-css', get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css');
    wp_enqueue_style('mf-admin-style', get_stylesheet_directory_uri() . '/css/mf-admin-style.min.css', array(), $my_version);

    wp_enqueue_script('sack');
    wp_enqueue_script('thickbox', null);
}

add_action('admin_enqueue_scripts', 'load_admin_scripts', 999);


//this function is used to enqueue the VUE map
function mf_map() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    // Map page only
    if (is_page_template('page-makerfaire-map.php')) {
        wp_enqueue_script('google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDtWsCdftU2vI9bkZcwLxGQwlYmNRnT2VM', false, false, true);
        wp_enqueue_script('google-markers', 'https://cdnjs.cloudflare.com/ajax/libs/js-marker-clusterer/1.0.0/markerclusterer_compiled.js', array('google-map'), false, true);
        wp_enqueue_script('vue', get_stylesheet_directory_uri() . '/js/mf-map/vue.min.js', false, $my_version, true);
        wp_enqueue_script('axios', get_stylesheet_directory_uri() . '/js/mf-map/axios.min.js', array('vue'), $my_version, true);
        wp_enqueue_script('vue-table-2', get_stylesheet_directory_uri() . '/js/mf-map/vue-tables-2.min.js', array('vue'), $my_version, true);
        wp_enqueue_script('vue-map', get_stylesheet_directory_uri() . '/js/mf-map/min/mf-map.min.js', array('vue'), $my_version, true);
    }
}

add_action('wp_enqueue_scripts', 'mf_map');

//This function is used to enqueue the angularJS!!!!
function angular_scripts() {
    if (is_page('ribbons') || is_page_template('page-schedule.php') || is_page_template('page-meet-the-makers.php')) {
        $my_theme = wp_get_theme();
        $my_version = $my_theme->get('Version');
        wp_enqueue_script('angularjs', get_stylesheet_directory_uri() . '/js/built-angular-libs.min.js', array('make-js'), $my_version, true);
        if (is_page('ribbons')) {
            wp_enqueue_script('angular-scripts', get_stylesheet_directory_uri() . '/js/angular/ribbonApp.js', array('angularjs'), $my_version, true);
            //localize
            wp_localize_script('angular-scripts', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
            wp_localize_script('angular-scripts', 'angularLocalized', array('partials' => trailingslashit(get_template_directory_uri()) . 'partials/'));
        } elseif (is_page_template('page-schedule.php')) {
            //angular ui-bootstrap style
            wp_enqueue_style('ui-bootstrap', get_stylesheet_directory_uri() . '/css/angular/angular-ui-bootstrap/ui-bootstrap-csp.css', array(), null, 'all');
        } elseif (is_page_template('page-meet-the-makers.php')) {
            //angular ui-bootstrap style
            wp_enqueue_style('ui-bootstrap', get_stylesheet_directory_uri() . '/css/angular/angular-ui-bootstrap/ui-bootstrap-csp.css', array(), null, 'all');
        }
    }
}

add_action('wp_enqueue_scripts', 'angular_scripts');

add_filter('gform_enable_field_label_visibility_settings', '__return_true');

// Making error logs for ajax to call
add_action('wp_ajax_make_error_log', 'make_error_log');
add_action('wp_ajax_nopriv_make_error_log', 'make_error_log');

// Write to the php error log by request
function make_error_log() {
    $error = filter_input(INPUT_POST, 'make_error', FILTER_SANITIZE_SPECIAL_CHARS);
    error_log(print_r($error, true));
}

function my_acf_flexible_content_layout_title($title, $field, $layout, $i) {
    $newTitle = '';
    if ($activeInactive = get_sub_field('activeinactive')) {
        $style = ($activeInactive === 'Active') ? 'style="color: green"' : 'style="color: red"';
        $newTitle .= ' <span ' . $style . '>(' . $activeInactive . ')</span>';
    }
    if ($butTixText = get_sub_field('buy_ticket_text')) {
        $newTitle .= ' ' . $butTixText . ' ';
    }
    // 1-col WYSIWYG
    if ($customTitle = get_sub_field('title')) {
        $newTitle .= ' ' . $customTitle . ' ';
    }
    // 3-col
    if ($panelTitle = get_sub_field('panel_title')) {
        $newTitle .= ' ' . $panelTitle . ' ';
    }
    // Star Ribbon
    if ($starRibbonText = get_sub_field('text')) {
        $newTitle .= ' ' . $starRibbonText . ' ';
    }
    // Hero Title
    if ($columnTitle = get_sub_field('column_title')) {
        $newTitle .= ' ' . strip_tags($columnTitle) . ' ';
    }
    if ($sponsorsURL = get_sub_field('sponsors_page_url')) {
        $newTitle .= ' Sponsors: ' . $sponsorsURL . ' ';
    }
    if ($featureFairesTitle = get_sub_field('featured_faires_title')) {
        $newTitle .= ' ' . $featureFairesTitle . ' ';
    }
    $newTitle .= '<div style="font-size: 12px; margin-right: 2em;">' . $title . '</div>';
    // Header Text - Maker Toolkit
    if ($headerText = get_sub_field('header_text')) {
        $newTitle = strip_tags($headerText);
    }
    return $newTitle;
}

// name
add_filter('acf/fields/flexible_content/layout_title', 'my_acf_flexible_content_layout_title', 10, 4);

// set the fields that should cause the flexible content fields to collapse
function ACF_flexible_content_collapse() {
    if (get_field('sections')) { // Maker toolkit
?>
        <style id="acf-flexible-content-collapse">
            .acf-flexible-content .acf-fields {
                display: none;
            }
        </style>
        <script type="text/javascript">
            jQuery(function($) {
                $('.acf-flexible-content .layout').addClass('-collapsed');
                $('#acf-flexible-content-collapse').detach();
            });
        </script>
<?php

    }
}

add_action('acf/input/admin_head', 'ACF_flexible_content_collapse');


/* Remove items from the top black nav bar in admin
 */
function mf_remove_toolbar_node($wp_admin_bar) {
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('customize');
    $wp_admin_bar->remove_node('updates');
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('autoptimize');
    $wp_admin_bar->remove_node('stats');
}

add_action('admin_bar_menu', 'mf_remove_toolbar_node', 999);

// keep the old style widget page
function switch_widget_editor() {
    remove_theme_support('widgets-block-editor');
}
add_action('after_setup_theme', 'switch_widget_editor');

// Never expire orphaned entries.
// Set the expiration date to 100 years in the future effectively never expiring the entries.
add_filter('gpnf_expiration_modifier', function () {
    return 100 * YEAR_IN_SECONDS;
});

add_filter('gform_incomplete_submissions_expiration_days', 'keep_incomplete_submissions', 10, 1);

function keep_incomplete_submissions($expiration_days) {
    $expiration_days = 365;
    return $expiration_days;
}

function select_Timezone($selected = '') {
    //$selected = "US/Pacific";
    $timeZone = array(
        'Africa/Cairo' => 'Africa/Cairo',
        'America/Argentina/Buenos_Aires' => 'America/Buenos_Aires',
        'America/Caracas' => 'America/Caracas',
        'Asia/Almaty' => 'Asia/Almaty',
        'Asia/Baku' => 'Asia/Baku',
        'Asia/Bangkok' => 'Asia/Bangkok',
        'Asia/Hong_Kong' => 'Asia/Hong_Kong',
        'Asia/Kabul' => 'Asia/Kabul',
        'Asia/Kathmandu' => 'Asia/Kathmandu',
        'Asia/Kolkata' => 'Asia/Kolkata',
        'Asia/Tashkent' => 'Asia/Tashkent',
        'Asia/Tehran' => 'Asia/Tehran',
        'Asia/Tokyo' => 'Asia/Tokyo',
        'Asia/Vladivostok' => 'Asia/Vladivostok',
        'Atlantic/Cape_Verde' => 'Atlantic/Cape_Verde',
        'Atlantic/Stanley' => 'Atlantic/Stanley',
        'Australia/Darwin' => 'Australia/Darwin',
        'Australia/Sydney' => 'Australia/Sydney',
        'America/Lower_Princes' => 'Canada/Atlantic',
        'America/St_Johns' => 'Canada/Newfoundland',
        'Europe/Brussels' => 'Europe/Brussels',
        'Europe/London' => 'Europe/London',
        'Europe/Moscow' => 'Europe/Moscow',
        'Pacific/Fiji' => 'Pacific/Fiji',
        'Pacific/Midway' => 'Pacific/Midway',
        'America/Anchorage' => 'US/Alaska',
        'America/Phoenix' => 'US/Arizona',
        'America/Chicago' => 'US/Central',
        'America/New_York' => 'US/Eastern',
        'Pacific/Honolulu' => 'US/Hawaii',
        'America/Denver' => 'US/Mountain',
        'America/Los_Angeles' => 'US/Pacific',
    );
    $select = '<select class="timeZoneSelect">';
    foreach ($timeZone as $key => $row) {
        $select .= '<option value="' . $key . '"';
        $select .= ($key === $selected ? ' selected' : '');
        $select .= '>' . $row . '</option>';
    }
    $select .= '</select>';
    return $select;
}

function basicCurl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //for local server only!
    $host = $_SERVER['HTTP_HOST'];
    if (strpos($host, '.local') > -1  || strpos($host, '.test') > -1) { // wpengine local environments
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function smartTruncate($string, $limit, $break = ".", $pad = "...") {
    // return with no change if string is shorter than $limit
    if (strlen($string) <= $limit) {
        return $string;
    }
    // is $break present between $limit and the end of the string?
    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            $string = trim(substr($string, 0, $breakpoint), ",") . $pad;
        }
    }
    return $string;
}

function validate_url($url) {
    if (preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i', $url)) {
        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);

        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    } else {
        return false;
    }

    return true;
}

//pull in custom elementor widgets
require_once('elementor/make-widgets.php');

//extend wp login to 90 days
add_filter('auth_cookie_expiration', 'extend_login_session');

function extend_login_session($expire) {
    return 7776000; // seconds for 90 day time period
}

//kill comments completely on the site
add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;

    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }

    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});


function child_remove_page_templates($page_templates) {
    unset($page_templates['page-maker-portal.php']);
    unset($page_templates['page-entry.php']);

    unset($page_templates['404.php']);
    unset($page_templates['flagship-faire-landing-page.php']);

    unset($page_templates['page-press-center-leftnav.php']);
    unset($page_templates['page-mfscheduler.php']);
    unset($page_templates['page-mfscheduler-tasks.php']);
    unset($page_templates['page-video-ba15.php']);
    unset($page_templates['page-white-house.php']);
    unset($page_templates['signage-detail.php']);
    unset($page_templates['signage-list.php']);
    unset($page_templates['page-query.php']);

    unset($page_templates['page-wide-leftnav.php']);
    unset($page_templates['page-wide-image-grid-leftnav.php']);
    unset($page_templates['page-wide.php']);
    unset($page_templates['page-api.php']);
    unset($page_templates['MTM-page-template.php']);
    unset($page_templates['page-topics.php']);
    unset($page_templates['pages/page-maker-week.php']);

    return $page_templates;
}
add_filter('theme_page_templates', 'child_remove_page_templates');



add_action('members_register_caps', 'th_register_caps');
function th_register_caps() {
    members_register_cap('admin_review', array('label' => __('Admin Review', 'makerfaire'), 'group' => 'makerfaire'));
    
    //notes
    members_register_cap('notes_view', array('label' => __('View Notes', 'makerfaire'), 'group' => 'makerfaire'));
    members_register_cap('notes_send', array('label' => __('Send Notes', 'makerfaire'), 'group' => 'makerfaire'));
    
    //notifications
    members_register_cap('view_notifications', array('label' => __('View Sent Notifications', 'makerfaire'), 'group' => 'makerfaire'));    
    members_register_cap('notifications_resend', array('label' => __('Resend Notifications', 'makerfaire'), 'group' => 'makerfaire'));    
    
    //manage tab
    members_register_cap('edit_flags', array('label' => __('Edit Flags', 'makerfaire'), 'group' => 'makerfaire'));
    members_register_cap('edit_prelim_loc', array('label' => __('Edit Preliminary Location', 'makerfaire'), 'group' => 'makerfaire'));
    members_register_cap('edit_entry_type', array('label' => __('Edit Entry Type', 'makerfaire'), 'group' => 'makerfaire'));
    members_register_cap('edit_fee_mgmt', array('label' => __('Edit Fee Management', 'makerfaire'), 'group' => 'makerfaire'));
    members_register_cap('edit_status', array('label' => __('Edit Status', 'makerfaire'), 'group' => 'makerfaire'));
    
    //other
    members_register_cap('edit_rmt', array('label' => __('Edit RMT', 'makerfaire'), 'group' => 'makerfaire'));
    members_register_cap('view_rmt', array('label' => __('View RMT', 'makerfaire'), 'group' => 'makerfaire'));
    members_register_cap('edit_public_info', array('label' => __('Edit Public Info', 'makerfaire'), 'group' => 'makerfaire'));
}
add_action('members_register_cap_groups', 'th_register_cap_groups');
function th_register_cap_groups() {
    members_register_cap_group(
        'makerfaire',
        array(
            'label' => __('Makerfaire', 'makerfaire'),
            'caps' => array(),
            'icon' => 'dashicons-admin-generic',
            'priority' => 10
        )
    );
}

// turn off caching on gravity form pages
function gf_wprocket()  {
    add_filter( 'pre_get_rocket_option_delay_js', '__return_zero' );
    add_filter( 'pre_get_rocket_option_defer_all_js', '__return_zero' );
}
add_action( 'gform_register_init_scripts', 'gf_wprocket', 9999, 0 );
