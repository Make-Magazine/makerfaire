<?php

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

// Gravity Forms Specific Plugins and Classes
include_once TEMPLATEPATH . '/classes/gf-limit-checkboxes.php';

//include_once TEMPLATEPATH. '/classes/gf-entry-datatables.php';
include_once TEMPLATEPATH . '/classes/gf-helper.php';
include_once TEMPLATEPATH . '/classes/makerfaire-helper.php';
include_once TEMPLATEPATH . '/classes/gf-rmt-helper.php';
include_once TEMPLATEPATH . '/classes/mf-sharing-cards.php';
if (!defined('LOCAL_DEV_ENVIRONMENT') || !LOCAL_DEV_ENVIRONMENT) {
  include_once TEMPLATEPATH . '/classes/mf-login.php';
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
require_once( 'post-types/location.php' );*/
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

// Define our current Version number using the stylesheet version
function my_wp_default_styles($styles) {
  $my_theme = wp_get_theme();
  $my_version = $my_theme->get('Version');
  $styles->default_version = $my_version;
}

add_action("wp_default_styles", "my_wp_default_styles");

/* Disable Conflicting Code using Filters */
add_filter('jetpack_enable_opengraph', '__return_false', 99);

/* Load up jQuery */

function load_scripts() {
  // Styles
  wp_enqueue_style('make-gravityforms', get_stylesheet_directory_uri() . '/css/gravityforms.css');
  wp_enqueue_style('make-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css');
  wp_enqueue_style('make-bootstrapdialog', get_stylesheet_directory_uri() . '/css/bootstrap-dialog.min.css');
  wp_enqueue_style('wpb-google-fonts', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Condensed:400', false);
  wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', false);
  wp_enqueue_style('make-styles', get_stylesheet_directory_uri() . '/css/style.css');
  wp_enqueue_style('ytv', get_stylesheet_directory_uri() . '/css/ytv.css');

  wp_enqueue_style('jquery-datetimepicker-css', get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css');
  wp_enqueue_style('mf-datatables', get_stylesheet_directory_uri() . '/css/mf-datatables.css');
  wp_enqueue_style('fancybox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css', true);
  // jquery from Wordpress core (with no-conflict mode flag enabled):
  wp_enqueue_script('jquery');
  $my_theme = wp_get_theme();
  $my_version = $my_theme->get('Version');
  // Libraries concatenated by the grunt concat task (in Gruntfile.js):
  wp_enqueue_script('built-libs', get_stylesheet_directory_uri() . '/js/built-libs.js', array('jquery'),$my_version);
  // Other libraries:
  wp_enqueue_script('jquery-datetimepicker', get_stylesheet_directory_uri() . '/js/libs/jquery.datetimepicker.js');
  
  wp_enqueue_script('thickbox', null);

  // Scripts
  wp_enqueue_script('built', get_stylesheet_directory_uri() . '/js/built.js', array('jquery'),$my_version);

  // Localize
  $translation_array = array('templateUrl' => get_stylesheet_directory_uri(), 'ajaxurl' => admin_url('admin-ajax.php'));
  wp_localize_script('built', 'object_name', $translation_array);
}

add_action('wp_enqueue_scripts', 'load_scripts');

//Load custom gravity forms js for barnes and noble forms
//Change the formid below to load for barnes and noble

function enqueue_custom_barnesandnoble_script($form, $is_ajax) {
  $my_theme = wp_get_theme();
  $my_version = $my_theme->get('Version');
  wp_enqueue_script('make-gravityformsbarnesandnoble', get_stylesheet_directory_uri() . '/js/libs/gravityformsbarnesandnoble.js', array('jquery'),$my_version);
}

add_action('gform_enqueue_scripts_108', 'enqueue_custom_barnesandnoble_script', 10, 2);

//Load custom gravity forms js for all forms

function enqueue_custom_allforms_script($form, $is_ajax) {
  $my_theme = wp_get_theme();
  $my_version = $my_theme->get('Version');
  wp_enqueue_script('make-gravityformsallforms', get_stylesheet_directory_uri() . '/js/libs/gravityformsallforms.js', array('jquery'),$my_version);
}

add_action('gform_enqueue_scripts', 'enqueue_custom_allforms_script', 10, 2);

function load_admin_scripts() {
  //scripts
  wp_enqueue_script('make-gravityforms-admin', get_stylesheet_directory_uri() . '/js/libs/gravityformsadmin.js', array('jquery', 'jquery-ui-tabs'));
  wp_enqueue_script('make-fairesigns-admin', get_stylesheet_directory_uri() . '/js/libs/mf_fairesigns.js', array('jquery'));
  wp_enqueue_script('jquery-datetimepicker', get_stylesheet_directory_uri() . '/js/libs/jquery.datetimepicker.js', array('jquery'), null);
  wp_enqueue_script('make-bootstrap', get_stylesheet_directory_uri() . '/js/built-libs.js', array('jquery'));
  wp_enqueue_script('admin-scripts', get_stylesheet_directory_uri() . '/js/built-admin-scripts.js', array('jquery'));
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
  //styles
  wp_enqueue_style('make-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css');
  wp_enqueue_style('jquery-datetimepicker-css', get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css');
  wp_enqueue_style('mf-admin-style', get_stylesheet_directory_uri() . '/css/mf-admin-style.css');
}

add_action('admin_enqueue_scripts', 'load_admin_scripts');

// Remove richedit
add_filter('user_can_richedit', create_function('', 'return false;'), 50);

//This function is used to enqueue the angularJS!!!!
function angular_scripts() {
  if (is_page('ribbons')) {
    wp_enqueue_script(
            'angularjs', get_stylesheet_directory_uri() . '/bower_components/angular/angular.min.js'
    );
    wp_enqueue_script(
            'angularjs-route', get_stylesheet_directory_uri() . '/bower_components/angular-route/angular-route.min.js'
    );
    wp_enqueue_script(
            'dirPagination', get_stylesheet_directory_uri() . '/bower_components/angularUtils-pagination/dirPagination.js', array('angularjs', 'angularjs-route')
    );
    wp_enqueue_script(
            'angular-scripts', get_stylesheet_directory_uri() . '/js/angular/ribbonApp.js', array('angularjs', 'angularjs-route')
    );
    wp_localize_script('angular-scripts', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

    wp_localize_script(
            'angular-scripts', 'angularLocalized', array(
        'partials' => trailingslashit(get_template_directory_uri()) . 'partials/'
            )
    );
  }
}

add_action('wp_enqueue_scripts', 'angular_scripts');




/**
 * This function will connect wp_mail to your authenticated
 * SMTP server. This improves reliability of wp_mail, and
 * avoids many potential problems.
 */
add_action('phpmailer_init', 'send_smtp_email');

function send_smtp_email($phpmailer) {

  // Define that we are sending with SMTP
  $phpmailer->isSMTP();

  // The hostname of the mail server
  $phpmailer->Host = "smtp.mandrillapp.com";

  // Use SMTP authentication (true|false)
  $phpmailer->SMTPAuth = true;

  // SMTP port number - likely to be 25, 465 or 587
  $phpmailer->Port = "587";

  // Username to use for SMTP authentication
  $phpmailer->Username = "webmaster@makermedia.com";

  // Password to use for SMTP authentication
  $phpmailer->Password = "VyprCy78ZO0LRNwCTMVn2Q";

  // Encryption system to use - ssl or tls
  $phpmailer->SMTPSecure = "";
}
