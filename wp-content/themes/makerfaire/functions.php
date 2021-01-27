<?php
// FOR NOW, TURN OFF GUTENBURG
// disable for posts
//add_filter('use_block_editor_for_post', '__return_false', 10);
// disable for post types
//add_filter('use_block_editor_for_post_type', '__return_false', 10);

// Set our global Faire Variable. Use the slug of the taxonomy as the value.
define('MF_CURRENT_FAIRE', 'world-maker-faire-new-york-2014');

// include maker-faire-forms plugin
require_once( TEMPLATEPATH . '/plugins/maker-faire-forms/maker-faire-forms.php' );

// include maker-faire-forms plugin
require_once( TEMPLATEPATH . '/plugins/public-pages/makers.php' );

// include maker-faire-forms plugin
require_once( TEMPLATEPATH . '/post-types/maker.php' );

// include global-maker-faires post type
require_once( TEMPLATEPATH . '/post-types/global-maker-faire.php' );

// Markdown
require_once( TEMPLATEPATH . '/plugins/markdown/markdown.php' );

// Status Board
require_once( TEMPLATEPATH . '/plugins/status-board/status-board.php' );

// Current Faire Page
require_once( TEMPLATEPATH . '/plugins/admin-pages/current-faire/current-faire.php');

// Sponsor Carousel
include_once TEMPLATEPATH . '/plugins/public-pages/sponsor.php';

// Sponsor Carousel
include_once TEMPLATEPATH . '/plugins/instagram/instagram.php';

// Register Custom Navigation Walker include custom menu widget to use walkerclass
include_once TEMPLATEPATH . '/lib/wp_bootstrap_navwalker.php';

// Post Locker
//include_once dirname( __FILE__ ) . '/plugins/hide-post-locker/hide-post-locker.php';
// Blue Ribbons
include_once dirname(__FILE__) . '/plugins/blue-ribbons/blue-ribbons.php';

// White House
include_once dirname(__FILE__) . '/plugins/white-house/white-house.php';

// Load the settings field for the Applications API
include_once dirname(__FILE__) . '/api/admin-settings.php';

// Load the functions for the Applications API
include_once dirname(__FILE__) . '/api/v2/functions.php';

//include_once TEMPLATEPATH. '/classes/gf-entry-datatables.php';
include_once TEMPLATEPATH . '/classes/gf-helper.php';
include_once TEMPLATEPATH . '/classes/makerfaire-helper.php';
include_once TEMPLATEPATH . '/classes/gf-rmt-helper.php';
include_once TEMPLATEPATH . '/classes/mf-sharing-cards.php';
include_once TEMPLATEPATH . '/classes/ICS.php';

if (!defined('LOCAL_DEV_ENVIRONMENT') || !LOCAL_DEV_ENVIRONMENT) {
    //include_once TEMPLATEPATH . '/classes/mf-login.php';
}

// Legacy Helper Functions replacing VIP Wordpress.com calls
include_once TEMPLATEPATH . '/classes/legacy-helper.php';

//cron job
include_once TEMPLATEPATH . '/classes/cronJob.php';

//eventbrite API
if (is_admin()) {
    include_once TEMPLATEPATH . '/classes/eventbrite.class.inc';
}

require_once( 'taxonomies/type.php' );
require_once( 'taxonomies/sponsor-category.php' );
require_once( 'taxonomies/location.php' );
require_once( 'taxonomies/faire.php' );
require_once( 'taxonomies/location_category.php' );
require_once( 'taxonomies/makerfaire_category.php' );
require_once( 'taxonomies/group.php' );
/*
  require_once( 'plugins/post-types/event-items.php' );
  require_once( 'post-types/sponsor.php' );
  require_once( 'post-types/location.php' ); */
if (defined('WP_CLI') && WP_CLI)
    require_once( 'plugins/wp-cli/wp-cli.php' );

// Include all function files in the makerfaire/functions directory:
foreach (glob(TEMPLATEPATH . '/functions/*.php') as $file) {
    include_once $file;
}

//include any subfolders like 'gravity_forms'
foreach (glob(TEMPLATEPATH . '/functions/*/*.php') as $file) {
    include_once $file;
}
// add post-thumbnails support to theme
add_theme_support('post-thumbnails');
add_image_size('schedule-thumb', 140, 140, true);
remove_filter('the_content', 'wpautop');

// Define our current Version number using the stylesheet version
function my_wp_default_styles($styles) {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    $styles->default_version = $my_version;
}

add_action("wp_default_styles", "my_wp_default_styles");

/* Disable Conflicting Code using Filters */
add_filter('jetpack_enable_opengraph', '__return_false', 99);

/*
  Set some CONST for universal assets (nav and footer)
  enclosed in a function for safety
  this needs to appear before the scripts/styles are enqueued
 */

function set_universal_asset_constants() {
    // Assume that we're in prod; only change if we are definitively in another
    $universal_asset_env = 'make.co';
	$universal_makehub_asset_env = 'community.make.co';
    $universal_asset_proto = 'https://';
    $host = $_SERVER['HTTP_HOST'];
    // dev environments
    if (strpos($host, 'dev.') === 0) {
        $universal_asset_env = 'dev.make.co';
		$universal_makehub_asset_env = 'devmakehub.wpengine.com';
    }
    // stage environments
    else if (strpos($host, 'stage.') === 0) {
        $universal_asset_env = 'stage.make.co';
		$universal_makehub_asset_env = 'stagemakehub.wpengine.com';
    }
    // legacy staging environments
    else if (strpos($host, '.staging.wpengine.com') > -1) {
        $universal_asset_env = 'makeco.staging.wpengine.com';
		$universal_makehub_asset_env = 'makehub.staging.wpengine.com';
    }
    // wpengine local environments
    else if (strpos($host, '.local') > -1  || strpos($host, '.test') > -1 ) {
        $universal_asset_env = 'makeco.local';
		$universal_makehub_asset_env = 'makehub.local';
		$universal_asset_proto = 'http://';
    }
    // Set the important bits as CONSTANTS that can easily be used elsewhere
    define('UNIVERSAL_ASSET_URL_PREFIX', $universal_asset_proto . $universal_asset_env);
	define('UNIVERSAL_MAKEHUB_ASSET_URL_PREFIX', $universal_asset_proto . $universal_makehub_asset_env);
}

set_universal_asset_constants();

function load_scripts() {

    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');

    // Styles
    wp_enqueue_style('make-gravityforms', get_stylesheet_directory_uri() . '/css/gravityforms.css');
    wp_enqueue_style('make-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css');
    wp_enqueue_style('make-bootstrapdialog', get_stylesheet_directory_uri() . '/css/bootstrap-dialog.min.css', true);
    // wp_enqueue_style('wpb-google-fonts', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Condensed:400', false);
    wp_enqueue_style('linearicons', 'https://cdn.linearicons.com/free/1.0.0/icon-font.min.css', '', 'all', true);
    wp_enqueue_style('make-styles', get_stylesheet_directory_uri() . '/css/style.min.css', array(), $my_version);
    // wp_enqueue_style('ytv', get_stylesheet_directory_uri() . '/css/ytv.css');

    wp_enqueue_style('jquery-datetimepicker-css', get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css', '', '', true);
    wp_enqueue_style('mf-datatables', get_stylesheet_directory_uri() . '/css/mf-datatables.css', '', '', true);
    wp_enqueue_style('fancybox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.6/css/jquery.fancybox.min.css', '', 'all');
    wp_enqueue_style('universal.css', UNIVERSAL_ASSET_URL_PREFIX . '/wp-content/themes/memberships/universal-nav/css/universal.min.css', array(), $my_version);

    // font awesome load script
	wp_enqueue_script('fontawesome5-js', 'https://kit.fontawesome.com/7c927d1b5e.js', array(), '', true ); 
    //auth0
    wp_enqueue_script('auth0', 'https://cdn.auth0.com/js/auth0/9.6.1/auth0.min.js', array(), false, true);
    // space time for timezone hijinks
    wp_enqueue_script('spacetime', 'https://unpkg.com/spacetime', array(), false, true);

    if (strpos($_SERVER['REQUEST_URI'], "authenticate-redirect") !== false) {
        wp_enqueue_script('billboard', get_stylesheet_directory_uri() . '/js/libs/billboard.js', array('jquery'), $my_version, true);
    }
    // Libraries concatenated by the grunt concat task (in Gruntfile.js):
    wp_enqueue_script('built-libs', get_stylesheet_directory_uri() . '/js/built-libs.min.js', array('jquery'), $my_version, true);
    // Other libraries:
    wp_enqueue_script('jquery-datetimepicker', get_stylesheet_directory_uri() . '/js/libs/jquery.datetimepicker.js', array('jquery'), '', true);
    // wp_enqueue_script('jquery-mark', get_stylesheet_directory_uri() . '/js/libs/jquery.mark.min.js');
    wp_enqueue_script('jquery-sticky', get_stylesheet_directory_uri() . '/js/libs/jquery.sticky.js', array('jquery'), '', true);
    wp_enqueue_script('universal', UNIVERSAL_MAKEHUB_ASSET_URL_PREFIX . '/wp-content/universal-assets/v1/js/min/universal.min.js', array(), $my_version, true);

    wp_enqueue_script('thickbox', null);

    // Scripts
    wp_enqueue_script('built', get_stylesheet_directory_uri() . '/js/built.min.js', array('jquery'), $my_version, true);

    // Localize
    $translation_array = array('templateUrl' => get_stylesheet_directory_uri(), 'ajaxurl' => admin_url('admin-ajax.php'));
    wp_localize_script('built', 'object_name', $translation_array);
    wp_localize_script('built-libs', 'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'home_url' => get_home_url(),
                'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
                'wp_user_email' => wp_get_current_user()->user_email,
            )
    );

    /* jQuery can't be moved to footer as too many inline js rely on jquery without a on load event :(
      remove_action('wp_head', 'wp_print_scripts');
      remove_action('wp_head', 'wp_print_head_scripts', 9);
      remove_action('wp_head', 'wp_enqueue_scripts', 1);

      add_action('wp_footer', 'wp_print_scripts', 1);
      add_action('wp_footer', 'wp_enqueue_scripts', 0);
      add_action('wp_footer', 'wp_print_head_scripts', 5); */
}

add_action('wp_enqueue_scripts', 'load_scripts');

//Load custom gravity forms js for barnes and noble forms
//Change the formid below to load for barnes and noble

function enqueue_custom_barnesandnoble_script($form, $is_ajax) {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    wp_enqueue_script('make-gravityformsbarnesandnoble', get_stylesheet_directory_uri() . '/js/libs/gravityformsbarnesandnoble.js', array('jquery'), $my_version);
}

add_action('gform_enqueue_scripts_108', 'enqueue_custom_barnesandnoble_script', 10, 2);

//Load custom gravity forms js for all forms

function enqueue_custom_allforms_script($form, $is_ajax) {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    wp_enqueue_script('make-gravityformsallforms', get_stylesheet_directory_uri() . '/js/libs/gravityformsallforms.js', array('jquery'), $my_version);
}

add_action('gform_enqueue_scripts', 'enqueue_custom_allforms_script', 10, 2);

function load_admin_scripts() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    //scripts
    wp_enqueue_script('make-gravityforms-admin', get_stylesheet_directory_uri() . '/js/libs/gravityformsadmin.js', array('jquery', 'jquery-ui-tabs'), $my_version);
    wp_enqueue_script('make-fairesigns-admin', get_stylesheet_directory_uri() . '/js/libs/mf_fairesigns.js', array('jquery'), $my_version);
    wp_enqueue_script('jquery-datetimepicker', get_stylesheet_directory_uri() . '/js/libs/jquery.datetimepicker.js', array('jquery'), null);

    //wp_enqueue_script('make-bootstrap', get_stylesheet_directory_uri() . '/js/built-libs.min.js', array('jquery'));
    wp_enqueue_script('admin-scripts', get_stylesheet_directory_uri() . '/js/built-admin-scripts.min.js', array('jquery'), $my_version);

    //styles
    wp_enqueue_style('make-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css');
    wp_enqueue_style('jquery-datetimepicker-css', get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css');
    wp_enqueue_style('mf-admin-style', get_stylesheet_directory_uri() . '/css/mf-admin-style.min.css', array(), $my_version);

    wp_enqueue_script('sack');
    //custom scripts for national
    $user = wp_get_current_user();
    $is_national = ( in_array('national', (array) $user->roles) );
    if ($is_national) {
        wp_enqueue_script('make-gravityforms', get_stylesheet_directory_uri() . '/js/libs/gravityformsnationaladmin.js', array('jquery'), null);
    }

    $is_barnesandnoble = ( in_array('barnes__noble', (array) $user->roles) );
    if ($is_barnesandnoble) {
        wp_enqueue_script('make-gravityforms', get_stylesheet_directory_uri() . '/js/libs/gravityformsbarnesandnobleadmin.js', array('jquery'), null);
    }
}

add_action('admin_enqueue_scripts', 'load_admin_scripts');


//this function is used to enqueue the VUE map
function mf_map() {
    $my_theme = wp_get_theme();
    $my_version = $my_theme->get('Version');
    // Map page only
    if (is_page_template('page-makerfaire-map.php')) {
        wp_enqueue_script('google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDtWsCdftU2vI9bkZcwLxGQwlYmNRnT2VM', false, false, true);
        wp_enqueue_script('google-markers', 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js', array('google-map'), false, true);
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

        wp_enqueue_script('angularjs', get_stylesheet_directory_uri() . '/js/built-angular-libs.min.js', array('built-libs'), $my_version, true);

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
            wp_enqueue_style('owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css', array(), null, 'all');
            wp_enqueue_script('carousel', get_stylesheet_directory_uri() . '/js/scripts/owl.carousel.js', array(), false, true);
        }
    }
}

add_action('wp_enqueue_scripts', 'angular_scripts');

/**
 * This function will connect wp_mail to your authenticated
 * SMTP server. This improves reliability of wp_mail, and
 * avoids many potential problems.
 */
/* add_action('phpmailer_init', 'send_smtp_email');
  function send_smtp_email($phpmailer) {
  // Define that we are sending with SMTP
  $phpmailer->isSMTP();
  // The hostname of the mail server
  $phpmailer->Host = "smtp.mandrillapp.com";
  // Use SMTP authentication (true|false)
  $phpmailer->SMTPAuth = true;
  // SMTP port number - likely to be 25, 465 or 587
  $phpmailer->Port = "2525";
  // Username to use for SMTP authentication
  $phpmailer->Username = "webmaster@makermedia.com";
  // Password to use for SMTP authentication
  $phpmailer->Password = "VyprCy78ZO0LRNwCTMVn2Q";
  // Encryption system to use - ssl or tls
  $phpmailer->SMTPSecure = "";
  } */
add_filter('gform_enable_field_label_visibility_settings', '__return_true');


// Making error logs for ajax to call
add_action('wp_ajax_make_error_log', 'make_error_log');
add_action('wp_ajax_nopriv_make_error_log', 'make_error_log');

// Write to the php error log by request
function make_error_log() {
    $error = filter_input(INPUT_POST, 'make_error', FILTER_SANITIZE_STRING);
    error_log(print_r($error, TRUE));
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
        <style id="acf-flexible-content-collapse">.acf-flexible-content .acf-fields { display: none; }</style>
        <script type="text/javascript">
            jQuery(function ($) {
                $('.acf-flexible-content .layout').addClass('-collapsed');
                $('#acf-flexible-content-collapse').detach();
            });
        </script>
        <?php

    }
}

add_action('acf/input/admin_head', 'ACF_flexible_content_collapse');

function custom_acf_repeater_colors() {
    echo '<style type="text/css">
        /* nth field background */
        .acf-repeater.-row .acf-row:nth-of-type(2n) .acf-table {
            background:#fafafa;
        }
        /* left field label td */
        .acf-repeater.-row .acf-row:nth-of-type(2n) td.acf-label {
            background:#eee;
            border-color:#ddd;
        }
        /* field td */
        .acf-repeater.-row .acf-row:nth-of-type(2n) td.acf-input {
            border-color:#ddd;
        }
        /* left and right side - order and delete td */
        .acf-repeater.-row .acf-row:nth-of-type(2n) td.order,
        .acf-repeater.-row .acf-row:nth-of-type(2n) td.remove {
            background:#e3e3e3;
        }
        /* space between row - only border works */
        .acf-repeater.-row > tbody > tr > td {
            border:0;
            border-bottom:3px solid #DFDFDF;
        }
         </style>';
}

add_action('admin_head', 'custom_acf_repeater_colors');

function shapeSpace_remove_toolbar_node($wp_admin_bar) {
    // replace 'updraft_admin_node' with your node id
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('customize');
    $wp_admin_bar->remove_node('updates');
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('essb');
    $wp_admin_bar->remove_node('autoptimize');
    $wp_admin_bar->remove_node('stats');
}

add_action('admin_bar_menu', 'shapeSpace_remove_toolbar_node', 999);

// Never expire orphaned entries.
// Set the expiration date to 100 years in the future effectively never expiring the entries.
add_filter('gpnf_expiration_modifier', function() {
    return 100 * YEAR_IN_SECONDS;
});

add_filter('gform_incomplete_submissions_expiration_days', 'keep_incomplete_submissions', 10, 1);

function keep_incomplete_submissions($expiration_days) {
    $expiration_days = 365;
    return $expiration_days;
}

function select_Timezone($selected = '') {
    //$selected = "US/Pacific";
    $timeZone = array('Africa/Cairo' => 'Africa/Cairo',
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
        'America/Los_Angeles' => 'US/Pacific',);
    $select = '<select class="timeZoneSelect">';
    foreach ($timeZone as $key=>$row) {
        $select .= '<option value="' . $key . '"';
        $select .= ($key === $selected ? ' selected' : '');
        $select .= '>' . $row . '</option>';
    }
    $select .= '</select>';
    return $select;
}

function basicCurl($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function smartTruncate($string, $limit, $break = ".", $pad = "...") {
    // return with no change if string is shorter than $limit
    if (strlen($string) <= $limit)
        return $string;
    // is $break present between $limit and the end of the string?
    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            $string = trim(substr($string, 0, $breakpoint), ",") . $pad;
        }
    }
    return $string;
}