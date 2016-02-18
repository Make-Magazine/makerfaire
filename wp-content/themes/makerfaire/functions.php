<?php

// Set our global Faire Variable. Use the slug of the taxonomy as the value.
define( 'MF_CURRENT_FAIRE', 'world-maker-faire-new-york-2014' );

// include maker-faire-forms plugin
require_once( TEMPLATEPATH. '/plugins/maker-faire-forms/maker-faire-forms.php' );

// include maker-faire-forms plugin
require_once( TEMPLATEPATH. '/plugins/public-pages/makers.php' );

// include maker-faire-forms plugin
require_once( TEMPLATEPATH. '/post-types/maker.php' );

// include global-maker-faires post type
require_once( TEMPLATEPATH. '/post-types/global-maker-faire.php' );

// Markdown
require_once( TEMPLATEPATH. '/plugins/markdown/markdown.php' );

// Status Board
require_once( TEMPLATEPATH. '/plugins/status-board/status-board.php' );

// Current Faire Page
require_once( TEMPLATEPATH. '/plugins/admin-pages/current-faire/current-faire.php');

// Sponsor Carousel
include_once TEMPLATEPATH. '/plugins/public-pages/sponsor.php';

// Sponsor Carousel
include_once TEMPLATEPATH. '/plugins/instagram/instagram.php';

// Post Locker
//include_once dirname( __FILE__ ) . '/plugins/hide-post-locker/hide-post-locker.php';

// Blue Ribbons
include_once dirname( __FILE__ ) . '/plugins/blue-ribbons/blue-ribbons.php';

// White House
include_once dirname( __FILE__ ) . '/plugins/white-house/white-house.php';

// Load the settings field for the Applications API
include_once dirname( __FILE__ ) . '/api/admin-settings.php';

// Load the functions for the Applications API
include_once dirname( __FILE__ ) . '/api/v2/functions.php';

// Gravity Forms Specific Plugins and Classes
include_once TEMPLATEPATH. '/classes/gf-limit-checkboxes.php';
include_once TEMPLATEPATH. '/classes/gf-entry-sidebar.php';
include_once TEMPLATEPATH. '/classes/gf-entry-summary.php';
include_once TEMPLATEPATH. '/classes/gf-entry-notifications.php';
//include_once TEMPLATEPATH. '/classes/gf-entry-datatables.php';
include_once TEMPLATEPATH. '/classes/gf-helper.php';
include_once TEMPLATEPATH. '/classes/makerfaire-helper.php';
include_once TEMPLATEPATH. '/classes/gf-rmt-helper.php';
include_once TEMPLATEPATH. '/classes/mf-sharing-cards.php';
if (!defined('LOCAL_DEV_ENVIRONMENT') || !LOCAL_DEV_ENVIRONMENT) {
  include_once TEMPLATEPATH. '/classes/mf-login.php';
}

// Legacy Helper Functions replacing VIP Wordpress.com calls
include_once TEMPLATEPATH. '/classes/legacy-helper.php';

//cron job
include_once TEMPLATEPATH. '/classes/cronJob.php';

require_once( 'taxonomies/type.php' );
require_once( 'taxonomies/sponsor-category.php' );
require_once( 'taxonomies/location.php' );
require_once( 'taxonomies/faire.php' );
require_once( 'taxonomies/location_category.php' );
require_once( 'taxonomies/makerfaire_category.php' );
require_once( 'taxonomies/group.php' );
require_once( 'plugins/post-types/event-items.php' );
require_once( 'post-types/sponsor.php' );
require_once( 'post-types/location.php' );
if ( defined( 'WP_CLI' ) && WP_CLI )
  require_once( 'plugins/wp-cli/wp-cli.php' );

// add post-thumbnails support to theme
add_theme_support( 'post-thumbnails' );
add_image_size( 'schedule-thumb', 140, 140, true );

// Define our current Version number using the stylesheet version
function my_wp_default_styles($styles) {
  $my_theme = wp_get_theme();
  $my_version=  $my_theme->get( 'Version' );
  $styles->default_version=$my_version;
}
add_action("wp_default_styles","my_wp_default_styles");

/* Favicon in Header */
add_action('wp_head','my_custom_fav_ico');
function my_custom_fav_ico() {
  echo '<link rel="shortcut icon" href="'.get_stylesheet_directory_uri(). '/images/favicon.ico'.'" />';
}
/* Disable Conflicting Code using Filters */
add_filter( 'jetpack_enable_opengraph', '__return_false', 99 );

/* Load up jQuery */
function load_scripts() {
  // Styles
  wp_enqueue_style( 'make-gravityforms', get_stylesheet_directory_uri() . '/css/gravityforms.css' );
  wp_enqueue_style( 'make-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css' );
  wp_enqueue_style( 'make-bootstrapdialog', get_stylesheet_directory_uri() . '/css/bootstrap-dialog.min.css' );
  wp_enqueue_style( 'wpb-google-fonts', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900', false );
  wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', false );
  wp_enqueue_style( 'make-styles', get_stylesheet_directory_uri() . '/css/style.css' );
  wp_enqueue_style( 'ytv', get_stylesheet_directory_uri() . '/css/ytv.css' );
  wp_enqueue_style( 'bootgrid', get_stylesheet_directory_uri() . '/plugins/grid/jquery.bootgrid.min.css' );
  wp_enqueue_style( 'jquery-datetimepicker-css',  get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css' );
  wp_enqueue_style( 'mf-datatables', get_stylesheet_directory_uri() . '/css/mf-datatables.css' );
  wp_enqueue_style( 'fancybox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css', true );
  // jquery from Wordpress core (with no-conflict mode flag enabled):
  wp_enqueue_script( 'jquery' );
  // Libraries concatenated by the grunt concat task (in Gruntfile.js):
  wp_enqueue_script( 'built-libs', get_stylesheet_directory_uri() . '/js/built-libs.js');
  // Other libraries:
  wp_enqueue_script( 'jquery-datetimepicker', get_stylesheet_directory_uri() . '/js/libs/jquery.datetimepicker.js');
  wp_enqueue_script( 'bootgrid', get_stylesheet_directory_uri() . '/plugins/grid/jquery.bootgrid.min.js');
  wp_enqueue_script( 'thickbox', null);
  // Localize
  $translation_array = array('templateUrl' => get_stylesheet_directory_uri(),'ajaxurl' => admin_url( 'admin-ajax.php' ));
  wp_localize_script('built', 'object_name', $translation_array);
  // Scripts
  wp_enqueue_script( 'built', get_stylesheet_directory_uri() . '/js/built.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'load_scripts' );
add_action( 'gform_enqueue_scripts', 'enqueue_custom_script', 10, 2 );

function enqueue_custom_script( $form, $is_ajax ) {
  wp_enqueue_script( 'make-gravityforms',  get_stylesheet_directory_uri() . '/js/libs/gravityforms.js', array( 'jquery' ), null );
}

function load_admin_scripts() {
  //scripts
  wp_enqueue_script('make-gravityforms-admin',  get_stylesheet_directory_uri() . '/js/libs/gravityformsadmin.js', array('jquery', 'jquery-ui-tabs'));
  wp_enqueue_script( 'jquery-datetimepicker',  get_stylesheet_directory_uri() . '/js/libs/jquery.datetimepicker.js', array( 'jquery' ), null );
  wp_enqueue_script( 'make-bootstrap', get_stylesheet_directory_uri() . '/js/libs/bootstrap.min.js', array( 'jquery' ) );
  wp_enqueue_script( 'sack' );
  //styles
  wp_enqueue_style( 'make-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css' );
  wp_enqueue_style('jquery-datetimepicker-css',  get_stylesheet_directory_uri() . '/css/jquery.datetimepicker.css');
  wp_enqueue_style('wp-admin-style',  'http://makerfaire.staging.wpengine.com/wp-admin/load-styles.php?c=0&dir=ltr&load=dashicons,admin-bar,wp-admin,buttons,wp-auth-check&ver=1.35');
  wp_enqueue_style('made-admin-style',  get_stylesheet_directory_uri() . '/css/make.admin.css');
}
add_action( 'admin_enqueue_scripts', 'load_admin_scripts' );


// Add page visible to editors
function register_my_page(){
  add_menu_page( 'Entries Management', 'My Page', 'edit_others_posts', 'my_page_slug',  get_stylesheet_directory_uri() . 'plugins/entry_list.php'  );
}
add_action( 'admin_menu', 'register_my_page' );

/**
 * Allows us to clean the nasty garbled code that gets saved to the database for applications
 * Over time this function will phase out as we rebuild the form system
 * @param  String  $content The application content we want to clean. This should be looped through an array
 * @param  Boolean $report  Setting to true will remove any HTML formating we are correcting as we don't want that output to our reports
 * @return String
 *
 * @since L-Ron
 */
function mf_clean_content( $content, $report = false ) {
  $bad = array( '&#039;', "\'", '\"', '&#8217;', 'u2019', '&#38;', '&#038;', '&amp;', '&#34;', '&#034;', 'u201c', 'u201d', '&#8211;', '&lt;', '&#8230;', 'u00a0', 'u2013', '00ae', 'u016f', 'u0161', 'u00e1', 'u2122', 'u00a9' );
  $good = array( "'",     "'",  '"',  "'",       "'",     '&',     '&',      '&',     '"',     '"',      '"',     '"',     '–',       '>',    '...',     ' ',     '-',     '®',    'ů',     'š',     'á',   '™',   '©' );

  // If we are are correct HTML, let's add that here as reports we don't want HTML shown in here
  if ( $report ) {
    array_push( $bad,  '<br />\rn', '<br />rn', '<br />nn', 'rnrn', '<br />', '\rn', '.rn' );
    array_push( $good, ' ',       ' ',    ' ',      ' ',    ' ',      ' ',   ' ' );
  } else {
    array_push( $bad,  '<br />\rn',    '<br />rn',     '<br />nn',     'rnrn',         '.rn' );
    array_push( $good, '<br /><br />', '<br /><br />', '<br /><br />', '<br /><br />', '.<br /><br />' );
  }

  $cleaned = str_replace( $bad, $good, htmlspecialchars_decode( mf_convert_newlines( $content ) ) );

  return $cleaned;
}

function makerfaire_get_news() {
  $url = 'http://makezine.com/maker-faire-news/';
  $response = wp_remote_get( $url, 3, 60,  array( 'obey_cache_control_header' => false ) );
  $output = wp_remote_retrieve_body( $response );
  $cleaned_output = str_replace('?resize=130%2C130', '',$output);
  return $cleaned_output;
}

add_shortcode('news', 'makerfaire_get_news');

function makerfaire_get_beat() {
  $url = 'http://makezine.com/beat-reports/';
  $response = wp_remote_get( $url, 3, 60*60,  array( 'obey_cache_control_header' => false ) );
  $output = wp_remote_retrieve_body( $response );
  return $output;
}

add_shortcode('mf_beat_reports', 'makerfaire_get_beat');

function makerfaire_sidebar_news() {
  $url = 'http://makezine.com/maker-faire-news-sidebar/';
  $response = wp_remote_get( $url, 3, 60*60,  array( 'obey_cache_control_header' => false ) );
  $output = wp_remote_retrieve_body( $response );
  return $output;

}

function makerfaire_get_slider() {
  $url = 'http://makezine.com/maker-faire-featured-slider/';
  $response = wp_remote_get( $url, 3, 60,  array( 'obey_cache_control_header' => false ) );
  $output = wp_remote_retrieve_body( $response );
  return $output;
}
add_shortcode( 'mf-featured-slider', 'makerfaire_get_slider' );

/**
 * Make the 'accepted' status public so that forms can be shown
 *
 * @see http://vip-support.automattic.com/tickets/16382
 */
add_action( 'init', function() {
  global $wp_post_statuses;

  if ( isset( $wp_post_statuses['accepted'] ) )
    $wp_post_statuses['accepted']->public = true;

}, 400 );

function makerfaire_index_feed($n = 4) {
  $f = fetch_feed('http://makezine.com/tag/maker-faire/feed/');

  if(is_wp_error($f))
    return false;

  $max = $f->get_item_quantity($n);
  $fs  = $f->get_items(0, $max);

  $res = array();
  foreach($fs as $i)
  {
    $img = preg_match('/<img(.*?)src="(.*?)"(.*?)>/i', html_entity_decode($i->get_description()), $m);
    $res[] = array('i'=>$i, 'img'=>$m[0], 'src'=>$m[2]);
  }

  return $res;
}

function isc_register_menus() {
  register_nav_menus(
  array( 'header-menu' => __( 'Header Menu' ),
            'footer' => __( 'footer' ),
      'mf-admin-bayarea-register-menu' => __( 'MF BayArea Admin Bar' ),
                        'mf-admin-newyork-register-menu' => __( 'MF NewYork Admin Bar' ),
      'mobile-nav' => __( 'Mobile Navigation' ) )
  );
}
add_action( 'init', 'isc_register_menus' );


function makerfaire_carousel_shortcode( $atts ) {
  extract( shortcode_atts( array( 'id' => 'biggins'), $atts ) );
  return  '<a class="carousel-control left" href="#' . esc_attr( $id ) . '" data-slide="prev">
  <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span></a>
      <a class="carousel-control right" href="#' . esc_attr( $id ) . '" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span></a>';
}
add_shortcode( 'arrows', 'makerfaire_carousel_shortcode' );

function makerfaire_data_toggle() {
  return '<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#ny2013">New York 2013</a></li>
    <li><a data-toggle="tab" href="#ba2013">Bay Area 2013</a></li>
    <li><a data-toggle="tab" href="#d2013">Detroit 2013</a></li>
    <li><a data-toggle="tab" href="#r2013">Rome 2013</a></li>
    <li><a data-toggle="tab" href="#ny2012">New York 2012</a></li>
    <li><a data-toggle="tab" href="#d2012">Detroit 2012</a></li>
    <li><a data-toggle="tab" href="#ba2012">Bay Area 2012</a></li>
    <li><a data-toggle="tab" href="#ny2011">New York 2011</a></li>
    <li><a data-toggle="tab" href="#d2011">Detroit 2011</a></li>
    <li><a data-toggle="tab" href="#ba2011">Bay Area 2011</a></li>
    <li><a data-toggle="tab" href="#ny2010">New York 2010</a></li>
    <li><a data-toggle="tab" href="#d2010">Detroit 2010</a></li>
    <li><a data-toggle="tab" href="#ba2010">Bay Area 2010</a></li>
    <li><a data-toggle="tab" href="#ba2009">Bay Area 2009</a></li>
    <li><a data-toggle="tab" href="#a2008">Austin 2008</a></li>
    <li><a data-toggle="tab" href="#ba2008">Bay Area 2008</a></li>
    <li><a data-toggle="tab" href="#a2007">Austin 2007</a></li>
  </ul>';
}

add_shortcode( 'tabs', 'makerfaire_data_toggle' );

function makerfaire_newsletter_shortcode() {
  $output = '<form class="form-horizontal" action="http://whatcounts.com/bin/listctrl" method="POST">
    <input type="hidden" name="slid" value="6B5869DC547D3D46E66DEF1987C64E7A" />
    <input type="hidden" name="cmd" value="subscribe" />
    <input type="hidden" name="custom_source" value="Newsletter Shortcode" />
    <input type="hidden" name="custom_incentive" value="none" />
    <input type="hidden" name="custom_url" value="' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '" />
    <input type="hidden" id="format_mime" name="format" value="mime" />
    <input type="hidden" name="goto" value="//makerfaire.com/thanks-for-signing-up" />
    <input type="hidden" name="custom_host" value="' . $_SERVER["HTTP_HOST"] . '" />
    <input type="hidden" name="errors_to" value="" />
    <fieldset>
      <div id="legend">
        <legend>Sign up for the Maker Faire Newsletter</legend>
      </div>
      <div class="control-group">
        <!-- Name -->
        <label class="control-label" for="first">Name</label>
        <div class="controls">
          <input class="formfield input-xlarge" name="first" required="required" type="text">
        </div>
      </div>
      <div class="control-group">
        <!-- Email -->
        <label class="control-label" for="email">Email</label>
        <div class="controls">
          <input class="formfield input-xlarge" name="email" required="required" type="email">
        </div>
      </div>
      <div class="control-group">
        <!-- Button -->
        <div class="controls">
          <button class="btn-cyan" type="submit">Subscribe</button>
        </div>
      </div>
    </fieldset>
  </form>';
  return $output;
}

add_shortcode( 'newsletter', 'makerfaire_newsletter_shortcode' );

/**
 * meet the makers
 */
function makerfaire_meet_the_makers_shortcode($atts, $content = null) {
  extract( shortcode_atts( array(
      'form_id'   => '',
      'entry1_id' => '',
      'entry1_description'  => '',
      'entry2_id' => '',
      'entry2_description'  => '',
      'entry3_id' => '',
      'entry3_description'  => ''
  ), $atts ) );

  $values = array();
    if (null != (esc_attr($entry1_id))) {
      $values[0] = $entries = GFAPI::get_entry(esc_attr($entry1_id));
      $entry1_description = isset($entry1_description) ? $entry1_description : $values[0]['151'];
    }
    if (null != (esc_attr($entry2_id))) {
      $values[1] = $entries = GFAPI::get_entry(esc_attr($entry2_id));
       $entry2_description = isset($entry2_description) ? $entry2_description : $values[1]['151'];
   }
    if (null != (esc_attr($entry3_id))) {
      $values[2] = $entries = GFAPI::get_entry(esc_attr($entry3_id));
       $entry3_description = isset($entry3_description) ? $entry3_description : $values[2]['151'];
   }

$output = '<div class="row filter-container mmakers">'
          . ' <div class="col-xs-12 col-sm-8"><a href="/maker/entry/' . $values[0]['id'] . '" class="post">'
          . '   <img class="img-responsive" src="' . legacy_get_resized_remote_image_url($values[0]['22'],622,402) . '" alt="Featured Maker 1">'
          . '   <div class="text-box"><span class="section">' . $entry1_description . '</span></div></a>'
          . ' </div><div class="col-xs-12 col-sm-4">'
          . '   <a href="/maker/entry/' . $values[1]['id'] . '" class="post">'
          . '     <img class="img-responsive" src="' . legacy_get_resized_remote_image_url($values[1]['22'],622,402) . '" alt="Featured Maker 2">'
          . '     <div class="text-box"><span class="section">' . $entry2_description . '</span></div>'
          . '   </a>'
          . '   <a href="/maker/entry/' . $values[2]['id'] . '" class="post">'
          . '     <img class="img-responsive" src="' . legacy_get_resized_remote_image_url($values[2]['22'],622,402) . '" alt="Featured Maker 3">'
          . '     <div class="text-box"><span class="section">' . $entry3_description . '</span></div>'
          . '   </a>'
          . '</div></div>';

  return $output;
}

add_shortcode( 'mmakers', 'makerfaire_meet_the_makers_shortcode' );

/**
 * 3 Maker Faire tagged posts from Makezine - for homepage
 */

function get_first_image_url($html) {
  if (preg_match('/<img.+?src="(.+?)"/', $html, $matches)) {
    return $matches[1];
  }
}

function makerfaire_makezine_rss_news() {
  $url = 'http://makezine.com/tag/maker-faire/feed/';
  $rss = fetch_feed( $url);
  // Figure out how many total items there are, but limit it to 5.
  $maxitems = $rss->get_item_quantity( 3 );
  // Build an array of all the items, starting with element 0 (first element).
  $rss_items = $rss->get_items( 0, $maxitems );

  //image #2
  $description=$rss_items[1]->get_description();
  $image = get_first_image_url($description);
  $description = strip_tags($description);
  $title =esc_html( $rss_items[1]->get_title() );
  $url=esc_url( $rss_items[1]->get_permalink());
  $output = '<div class="row filter-container mf-news">'
          . '<div class="col-xs-12 col-sm-4">'
          . '  <a href="'.$url.'" class="post">'
          . '    <img class="img-responsive" src="' . legacy_get_resized_remote_image_url($image,622,402) . '" alt="Featured Maker Faire post 1">'
          . '    <div class="text-box"><span class="section">' . $title . '</span></div>'
          . '  </a>';

  //image #3
  $description=$rss_items[2]->get_description();
  $image = get_first_image_url($description);
  $description = strip_tags($description);
  $title =esc_html( $rss_items[2]->get_title() );
  $url=esc_url( $rss_items[2]->get_permalink());
  $output .= '  <a href="'.$url . '" class="post">'
          . '    <img class="img-responsive" src="' . legacy_get_resized_remote_image_url($image,622,402) . '" alt="Featured Maker Faire post 2">'
          . '    <div class="text-box"><span class="section">' . $title . '</span></div>'
          . '  </a>'
          . '</div>';

  //image #1
  $description=$rss_items[0]->get_description();
  $image = get_first_image_url($description);
  $description = strip_tags($description);
  $title =esc_html( $rss_items[0]->get_title() );
  $url=esc_url( $rss_items[0]->get_permalink());
  $output .= ' <div class="col-xs-12 col-sm-8"><a href="' . $url. '" class="post">'
          . '  <img class="img-responsive" src="' . legacy_get_resized_remote_image_url($image,622,402) . '" alt="Featured Maker Faire post 3">'
          . '  <div class="text-box"><span class="section">' . $title . '</span></div></a>'
          . '</div>'
          . '</div>';
  RETURN $output;
}

add_shortcode( 'mf-news', 'makerfaire_makezine_rss_news' );


function makerfaire_featured_makers_shortcode($atts, $content = null) {
  extract( shortcode_atts( array(
    'form_id'   => '',
    'entry1_id' => '',
    'entry2_id' => '',
    'entry3_id' => ''
  ), $atts ) );

  $criteria = array(
     'field_filters' => array(
       array('key' => '304', 'value' => 'Featured Maker')
     )
  );

  $entries = GFAPI::get_entries(esc_attr($form_id), $criteria, null, array('offset' => 0, 'page_size' => 40));
  $randEntry = array_rand($entries);

  $output = '';
  return $output;
}
add_shortcode( 'fmakers', 'makerfaire_featured_makers_shortcode' );

/**
 * Modal Window Builder
 */
function make_modal_builder( $atts, $content = null ) {
  extract( shortcode_atts( array(
    'launch'  => 'Launch Window',
    'title'   => 'Modal Title',
    'btn_class' => '',
    'embed' => ''
  ), $atts ) );

  $number = mt_rand();
  $output = '<a class="btn  ' . esc_attr( $btn_class ) . '" data-toggle="modal" href="#modal-' . $number . '">' . esc_html( $launch ) . '</a>';
  $output .= '<div id="modal-' . $number . '" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
  $output .= '  <div class="modal-header">';
  $output .= '    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
  $output .= '    <h3>' . esc_html( $title ) . '</h3>';
  $output .= '  </div>';
  $output .= '  <div class="modal-body">';
  if ( legacy_is_valid_domain( $embed,  array('fora.tv', 'ustream.com', 'ustream.tv' ) ) ) {
    $output .= '<iframe src="' . esc_url( $embed ) . '" width="530" height="320" frameborder="0"></iframe>';
  } else {
    $output .= ( !empty( $embed ) ) ? wp_oembed_get( esc_url( $embed ), array( 'width' => 530 ) ) : '';
  }
  $output .=      wp_kses_post( $content );
  $output .= '  </div>';
  $output .= '  <div class="modal-footer">';
  $output .= '    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>';
  $output .= '  </div>';
  $output .= '</div>';

  return $output;
}
add_shortcode( 'modal', 'make_modal_builder' );

function makerfaire_news_rss() { ?>
  <div class="newsies">
    <div class="news post">
      <h3 style="color: #fc040c;">
        <a href="http://makezine.com/tag/maker-faire/">Latest Maker Faire News</a>
      </h3>
      <?php
      $fs = makerfaire_index_feed();

      foreach($fs as $f) : $a = $f['i']->get_authors(); ?>
        <div class="row">
          <div class="col-md-2">
            <a href="<?php echo esc_url($f['i']->get_link()); ?>" title="<?php echo esc_attr($f['i']->get_title()); ?>"><img class="img-thumbnail faire-thumb " alt="<?php echo legacy_get_resized_remote_image_url(esc_attr($f['i']->get_title()),308,202); ?>" src="<?php echo esc_url($f['src']); ?>" /></a>
          </div>
          <div class="col-md-6">
          <h2><a href="<?php echo esc_url($f['i']->get_link()); ?>"><?php echo esc_html($f['i']->get_title()); ?></a></h2>
          <?php echo str_replace(array($f['img'], '<p><a href="'.$f['i']->get_link().'">Read the full article on MAKE</a></p>'), '', html_entity_decode(esc_html($f['i']->get_description()))); ?>

          <!-- READ FULL STORY BUTTON AND LINK
           <p class="read_more" style="margin:10px 0"><strong>
          <a class="btn btn-primary btn-xs" href="<?php /*  echo esc_url($f['i']->get_link()); */ ?>">Read full story &raquo;</a></strong></p>
          -->

          <!-- AUTHOR AND CATEGORY DESCRIPTIONS
          <ul class="unstyled">
            <li>Posted by <?php  /* echo esc_html($a[0]->name); ?> | <?php echo esc_html($f['i']->get_date()); ?></li>
            <li>Categories: <?php foreach($f['i']->get_categories() as $cat) : echo esc_html($cat->term.', '); endforeach; */ ?></li>
          </ul>
          -->
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <h4><a href="http://makezine.com/tag/maker-faire/">Read More &rarr;</a></h4>
<?php }

function makerfaire_widgets_init() {

  register_sidebar( array(
    'name' => 'Maker Faire Calendar',
    'id' => 'home_right_1',
    'before_widget' => '',
    'after_widget' => '',
    'before_title' => '<h4 class="more-faires">',
    'after_title' => '</h4>',
  ) );
}
add_action( 'widgets_init', 'makerfaire_widgets_init' );


if ( function_exists( 'vip_redirects' ) ) {
  vip_redirects( array(
    '/mini/toolkit/'  => 'https://www.dropbox.com/sh/ykbi3al0j4hatd2/hSN6Z9nXTU',
    '/alt'         => 'http://makerfaire.com/bayarea-2013/getting-to-maker-faire/'
  ) );
}

function mf_quick_links_box() {
  add_meta_box( 'quickly', 'Quick Links', 'mf_quick_links', 'mf_form' );
}
// This function echoes the content of our meta box
// TODO: investigate if this function is deprecated??
function mf_quick_links() {
  $output = '<div id="project-id-search"><label for="project-id" class="screen-reader-text">Search by Project ID</label><input type="search" name="search-proj-id" id="project-id" /><input type="submit" value="Search by ID" id="search-submit" class="button" /></div><ul class="subsubsub">
    <li class="all"><a href="edit.php?post_type=mf_form" class="current">All</a> |</li>
    <li class="trash"><a href="edit.php?post_status=trash&amp;post_type=mf_form">Trash</a> |</li>
    <li class="proposed"><a href="edit.php?post_status=proposed&amp;post_type=mf_form" title="Application proposed; waiting for acceptance.">Proposed</a> |</li>
    <li class="waiting-for-info"><a href="edit.php?post_status=more-info&amp;post_type=mf_form" title="Question has been emailed to Maker, waiting for response.">Waiting for Info</a> |</li>
    <li class="accepted"><a href="edit.php?post_status=accepted&amp;post_type=mf_form" title="Application is accepted to Maker Faire.">Accepted</a> |</li>
    <li class="cancelled"><a href="edit.php?post_status=cancelled&amp;post_type=mf_form" title="Accepted application is cancelled; This project will not attend Maker Faire after all.">Cancelled</a> |</li>
    <li class="in-progress"><a href="edit.php?post_status=in-progress&amp;post_type=mf_form" title="">In Progress</a></li>
    <li class="in-progress"><a href="edit.php?post_status=wait-list&amp;post_type=mf_form" title="">Wait List</a></li>
  </ul>
  <div class="clear"></div>';
  echo $output;
}

if (is_admin())
  add_action('admin_menu', 'mf_quick_links_box');

function mf_clean_title( $title ) {
    $title = str_replace('&nbsp;', ' ', $title);
    return $title;
}
add_filter('the_title', 'mf_clean_title', 10, 2);


function mf_release_shortcode() {
  $request_id = (!empty($_REQUEST['id']) ? $_REQUEST['id'] : null);
  $output = '<iframe src="' . esc_url( 'http://db.makerfaire.com/pa/' .  $request_id ) . '" width="620" height="2000" scrolling="auto" frameborder="0"> [Your user agent does not support frames or is currently configured not to display frames.] </iframe>';
  return $output;
}

add_shortcode( 'release', 'mf_release_shortcode' );


add_filter( 'wp_kses_allowed_html', 'mf_allow_data_atts', 10, 2 );
function mf_allow_data_atts( $allowedposttags, $context ) {
  $tags = array( 'div', 'a', 'li' );
  $new_attributes = array( 'data-toggle' => true );

  foreach ( $tags as $tag ) {
    if ( isset( $allowedposttags[ $tag ] ) && is_array( $allowedposttags[ $tag ] ) )
      $allowedposttags[ $tag ] = array_merge( $allowedposttags[ $tag ], $new_attributes );
  }

  return $allowedposttags;
}


add_filter('tiny_mce_before_init', 'mf_filter_tiny_mce_before_init');
function mf_filter_tiny_mce_before_init( $options ) {

  if ( ! isset( $options['extended_valid_elements'] ) )
    $options['extended_valid_elements'] = '';

  $options['extended_valid_elements'] .= ',a[data*|class|id|style|href]';
  $options['extended_valid_elements'] .= ',li[data*|class|id|style]';
  $options['extended_valid_elements'] .= ',div[data*|class|id|style]';

  return $options;
}

function mf_allow_my_post_types( $allowed_post_types ) {
  $allowed_post_types[] = 'mf_form';
  return $allowed_post_types;
}

add_filter( 'rest_api_allowed_post_types', 'mf_allow_my_post_types');


add_filter( 'jetpack_open_graph_tags', function( $tags ) {
  global $post;
  if ($post->post_type == 'mf_form') {
    $json = json_decode( $post->post_content );
    $tags['og:description'] = $json->public_description;
  } else {
    setup_postdata($post);
    $tags['og:description'] = get_the_excerpt();
  }

  return $tags;
}, 10 );



// show admin bar only for admins
if (!current_user_can('manage_options')) {
  add_filter('show_admin_bar', '__return_false');
}
// show admin bar only for admins and editors
if (!current_user_can('edit_posts')) {
  add_filter('show_admin_bar', '__return_false');
}
/**
 * OBSOLETE: No longer using this functionality: Hide Maker Faire applications from past faires
 *
 * In the past, CS had a method for only selecting the current
 * faire for applications. We want to do the same here, and prevent
 * all applications from showing up in the edit screen.
 *
 * Have to use slug, RE: See http://core.trac.wordpress.org/ticket/13258
 *
 * @global $query
 *
 *
function mf_hide_faires( $query ) {
  if ( is_admin() && $query->is_main_query() ) {
    $tax_query = array(
      array(
        'taxonomy'  => 'faire',
        'field'   => 'slug',
        'terms'   => MF_CURRENT_FAIRE,
        'operator'  => 'IN',
      )
    );
    $query->set( 'tax_query', $tax_query );
  }
}

// add_action( 'pre_get_posts', 'mf_hide_faires' );
 *
 */



/**
 * Counts the post numbers for the Dashboard.
 */
function mf_add_magazine_article_counts() {
    if ( !post_type_exists( 'mf_form' ) ) {
       return;
    }

    $num_posts = wp_count_posts( 'mf_form' );
    $num = number_format_i18n( $num_posts->accepted );
    $text = _n( 'Application', 'Applications', intval($num_posts->accepted) );
    if ( current_user_can( 'edit_posts' ) ) {
      $url = admin_url( 'edit.php?post_type=mf_form' );
      $num = '<a href="'.$url.'">'.$num.'</a>';
      $text = '<a href="'.$url.'">'.$text.'</a>';
    }
    echo '<td class="first b b-mf_form">' . $num . '</td>';
    echo '<td class="t mf_form">' . $text . '</td>';

    echo '</tr>';

    if ($num_posts->proposed > 0) {
      $num = number_format_i18n( $num_posts->proposed );
      $text = _n( 'Applications Pending', 'Applications Pending', intval($num_posts->proposed) );
      if ( current_user_can( 'edit_posts' ) ) {
        $url = admin_url( 'edit.php?post_status=proposed&post_type=mf_form' );
        $num = '<a href="' . $url . '">' . $num . '</a>';
        $text = '<a href="' . $url . '">' . $text . '</a>';
      }
      echo '<td class="first b b-recipes">' . $num . '</td>';
      echo '<td class="t recipes">' . $text . '</td>';

      echo '</tr>';
    }
}

add_action('right_now_content_table_end', 'mf_add_magazine_article_counts');

function mf_send_hipchat_notification( $message = 'Default Message', $from = 'MakeBot' ) {
  $base     = 'https://api.hipchat.com/v1/rooms/message';
  $auth_token = '9f4f9113e8eeb3754da520d295ca59';
  $room     = 198932;
  $notify   = 1;

  $opts = array(
    'auth_token'=> $auth_token,
    'room_id' => $room,
    'from'    => $from,
    'notify'  => $notify,
    'message' => urlencode( $message ),
    'color'   => 'green'
  );

  $url = add_query_arg( $opts, $base );
  //Move to wp_remote_get
  $request =   wp_remote_get($url);
  // Get the body of the response
  $json = wp_remote_retrieve_body( $request );
  //$json = wpcom_vip_file_get_contents( $url );
}

// Redirect mobile users on iOS or Android to their app stores if set.
function mf_page_redirect_to_app_stores() {
  if ( ! is_page( 'app' ) && function_exists( 'jetpack_is_mobile' ) )
    return;
  if ( class_exists( 'Jetpack' ) ) :

  $redirect_to = '';

  if ( Jetpack_User_Agent_Info::is_iphone_or_ipod() )
    $redirect_to = 'https://itunes.apple.com/us/app/maker-faire-the-official-app/id641794889';
  elseif ( Jetpack_User_Agent_Info::is_android() )
    $redirect_to = 'https://play.google.com/store/apps/details?id=com.xomodigital.makerfaire';

  if ( ! empty( $redirect_to ) ) {
    wp_redirect( $redirect_to, 301 );  // Permanent redirect
    exit;
  }
  endif;

}
add_action( 'template_redirect', 'mf_page_redirect_to_app_stores' );


add_action( 'admin_head', 'make_cpt_icons' );
/**
 * Adds icons for the custom post types that are in the admin.
 */
function make_cpt_icons() { ?>
  <style type="text/css" media="screen">
    .icon16.icon-event-items:before,
    #adminmenu #menu-posts-event-items div.wp-menu-image:before {
      content: '\f145';
    }
    .icon16.icon-dashboard:before,
    #adminmenu #menu-dashboard div.wp-menu-image:before {
      content: '\f226';
    }
    .icon16.icon-post:before,
    #adminmenu #menu-posts div.wp-menu-image:before {
      content: '\f109';
    }
    .icon16.icon-media:before,
    #adminmenu #menu-media div.wp-menu-image:before {
      content: '\f104';
    }
    .icon16.icon-comments:before,
    #adminmenu #menu-comments div.wp-menu-image:before {
      content: '\f101';
    }
    .icon16.icon-page:before,
    #adminmenu #menu-pages div.wp-menu-image:before {
      content: '\f105';
    }
    .icon16.icon-post:before,
    #adminmenu #menu-posts-mf_form div.wp-menu-image:before {
      content: '\f116';
    }
    .icon16.icon-post:before,
    #adminmenu #menu-posts-maker div.wp-menu-image:before {
      content: '\f307';
    }
    .icon16.icon-appearance:before,
    #adminmenu #menu-appearance div.wp-menu-image:before {
      content: '\f100';
    }
    .icon16.icon-plugins:before,
    #adminmenu #menu-plugins div.wp-menu-image:before {
      content: '\f106';
    }
    .icon16.icon-users:before,
    #adminmenu #menu-users div.wp-menu-image:before {
      content: '\f110';
    }
    .icon16.icon-tools:before,
    #adminmenu #menu-tools div.wp-menu-image:before {
      content: '\f107';
    }
    .icon16.icon-settings:before,
    #adminmenu #menu-settings div.wp-menu-image:before {
      content: '\f111';
    }
  </style>

<?php
  wp_enqueue_script('thickbox');
  wp_enqueue_style('thickbox');
 }

/**
 * Adds footer copyright information
 */
function make_copyright_footer() { ?>
  <div class="col-xs-12 footer_copyright">
    <div class="text-center">
      <p class="muted"><small>Make: and Maker Faire are registered trademarks of <a href="//makermedia.com">Maker Media, Inc.</small></p>
      <p class="muted"><small>Copyright &copy; 2004-<?php echo date("Y") ?> Maker Media, Inc.  All rights reserved</small></p>
    </div>
  </div>
<?php }


/**
 * Redirects to the "Current Faire" page after an application is trashed
 * @return void
 *
 * @since Mechani-Kong
 */
function maker_faire_trashed_application_redirect() {
    $screen = get_current_screen();

    if ( 'edit-mf_form' == $screen->id && isset( $_GET['trashed'] ) && intval( $_GET['trashed'] ) > 0 ) {
        $redirect = add_query_arg( array(
          'post_type' => 'mf_form',
          'page' => 'current_faire'
        ) );

        wp_redirect( $redirect );
        exit();
    }
}
add_action( 'load-edit.php','maker_faire_trashed_application_redirect' );

function scriptLoaded(){
   document.write( myValue );
}

/**
 *  Check if input string is a valid YouTube URL
 *  and try to extract the YouTube Video ID from it.
 *  @author  Stephan Schmitz <eyecatchup@gmail.com>
 *  @param   $url   string   The string that shall be checked.
 *  @return  mixed           Returns YouTube Video ID, or (boolean) false.
 */
function parse_yturl($url)
{
    $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
    preg_match($pattern, $url, $matches);
    return (isset($matches[1])) ? $matches[1] : false;
}

add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'all',
  array(
  'labels' => array(
  'name' => __( 'All' ),
  'singular_name' => __( 'All' )
  ),
  'public' => true,
  'has_archive' => true,
  )
  );
  register_post_type( 'in-progress',
  array(
  'labels' => array(
  'name' => __( 'In Progress' ),
  'singular_name' => __( 'In Progress' )
  ),
  'public' => true,
  'has_archive' => true,
  )
  );
        register_post_type( 'maker-entry-archive',
            array(
            'labels' => array(
            'name' => __( 'Maker Entry Archives' ),
            'singular_name' => __( 'Maker Entry Archive' )
            ),
            'taxonomies' => array('category'),
            'show_ui' => true,

            'public' => true,

            'rewrite'            => array( 'slug' => 'mfarchives', 'with_front' => true ),
            )
  );
}


/**
 * Allow HTML in WordPress Custom Menu Descriptions
 *
 * Create HTML list of nav menu items and allow HTML tags.
 * Replacement for the native menu Walker, echoing the description.
 * This is the ONLY known way to display the Description field.
 *
 * @see http://wordpress.stackexchange.com/questions/51609/
 *
 */
    class Description_Walker extends Walker_Nav_Menu {

        function start_el(&$output, $item, $depth=0,$args = Array(), $id = 0)
        {
            $classes     = empty ( $item->classes ) ? array () : (array) $item->classes;

            $class_names = join(
                ' '
                ,   apply_filters(
                    'nav_menu_css_class'
                    ,   array_filter( $classes ), $item
                )
            );

            ! empty ( $class_names )
            and $class_names = ' class="'. esc_attr( $class_names ) . '"';

            // Build default menu items
            $output .= "<li id='menu-item-$item->ID' $class_names>";

            $attributes  = '';

            ! empty( $item->attr_title )
            and $attributes .= ' title="'  . esc_attr( $item->attr_title ) .'"';
            ! empty( $item->target )
            and $attributes .= ' target="' . esc_attr( $item->target     ) .'"';
            ! empty( $item->xfn )
            and $attributes .= ' rel="'    . esc_attr( $item->xfn        ) .'"';
            ! empty( $item->url )
            and $attributes .= ' href="'   . esc_attr( $item->url        ) .'"';

            // Build the description (you may need to change the depth to 0, 1, or 2)
            $description = ( ! empty ( $item->description ) and 1 == $depth )
                ? '<span class="nav_desc">'.  $item->description . '</span>' : '';

            $title = apply_filters( 'the_title', $item->title, $item->ID );

            $item_output = $args->before
                . "<a $attributes>"
                . $args->link_before
                . $title
                . '</a> '
                . $args->link_after
                . $description
                . $args->after;

            // Since $output is called by reference we don't need to return anything.
            $output .= apply_filters(
                'walker_nav_menu_start_el'
                ,   $item_output
                ,   $item
                ,   $depth
                ,   $args
            );
        }
    }
    // Allow HTML descriptions in WordPress Menu
    remove_filter( 'nav_menu_description', 'strip_tags' );
    add_filter( 'wp_setup_nav_menu_item', 'cus_wp_setup_nav_menu_item' );
    function cus_wp_setup_nav_menu_item( $menu_item ) {
        $menu_item->description = apply_filters( 'nav_menu_description', $menu_item->post_content );
        return $menu_item;
    }
    //and then use this in your template:
    //wp_nav_menu( array( 'walker' => new Description_Walker ));




    /**
     * Fake KLogger class.
     *
     * @package
     **/
    class KLogger {

      /**
       * Some constants relating to logging, used by GF.
       **/
      CONST EMERGENCY = 'EMERGENCY';
      CONST ALERT = 'ALERT';
      CONST CRITICAL = 'CRITICAL';
      CONST ERROR = 'ERROR';
      CONST WARNING = 'WARNING';
      CONST NOTICE = 'NOTICE';
      CONST INFO = 'INFO';
      CONST DEBUG = 'DEBUG';
    }
    /**
     * Kinda fake GFLogging class
     *
     * @package
     **/
    class GFLogging {

      /**
       * A version integer.
       *
       * @var int
       **/
      var $version;

      /**
       * @access @static
       *
       * @return null
       */
      static public function include_logger() {
        // Not much happens here
      }

      /**
       * Log to PHP error log
       *
       * @return null
       */
      static public function log_message( $slug, $message, $debug_level ) {
            //don't log debug messages
            //if($debug_level==KLogger::DEBUG){
            if($debug_level==KLogger::ERROR){
                error_log( "GF LOG: $slug, $message, $debug_level" );
            }
      }
    }


/**
 * The Better Gallery shortcode, courtesy of WordPress Core
 *
 * Wanted to extend our Bootstrap Slideshow so that you could put in Post IDs and get back a slideshow.
 * Basically the same thing that the default slideshow does, so why not use that! Updated for Bootstrap 3!
 *
 * @since 1.0
 *
 * @param array $attr Attributes of the shortcode.
 * @return string HTML content to display gallery.
 */
function make_new_gallery_shortcode($attr) {
  $post = get_post();

  static $instance = 0;
  $instance++;

  if ( ! empty( $attr['ids'] ) ) {
    // 'ids' is explicitly ordered, unless you specify otherwise.
    if ( empty( $attr['orderby'] ) )
      $attr['orderby'] = 'post__in';
    $attr['include'] = $attr['ids'];
  }

  // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
  if ( isset( $attr['orderby'] ) ) {
    $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
    if ( !$attr['orderby'] )
      unset( $attr['orderby'] );
  }

  extract(shortcode_atts(array(
    'order'      => 'ASC',
    'orderby'    => 'menu_order ID',
    'id'         => $post->ID,
    'itemtag'    => 'dl',
    'icontag'    => 'dt',
    'captiontag' => 'dd',
    'columns'    => 3,
    'size'       => 'medium',
    'include'    => '',
    'exclude'    => ''
  ), $attr));


  $rand = mt_rand( 0, $id );

  $id = intval($id);
  if ( 'RAND' == $order )
    $orderby = 'none';

  if ( !empty($include) ) {
    $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

    $attachments = array();
    foreach ( $_attachments as $key => $val ) {
      $attachments[$val->ID] = $_attachments[$key];
    }
  } elseif ( !empty($exclude) ) {
    $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
  } else {
    $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
  }

  if ( empty($attachments) )
    return '';

  $output = '<div id="carousel-example-generic-' . $rand . '" class="carousel slide" data-interval="" data-ride="carousel"><div class="carousel-inner">';

  $i = 0;
  foreach( $attachments as $id => $attachment ) {
    $i++;
    if ($i == 1) {
      $output .= '<div class="item active">';
    } else {
      $output .= '<div class="item">';
    }
    $output .= wp_get_attachment_link( $attachment->ID, sanitize_title_for_query( $size ) );
    if ( isset( $attachment->post_excerpt ) && ! empty( $attachment->post_excerpt ) ) {
      $attachment_caption = $attachment->post_excerpt;
    } elseif ( isset( $attachment->post_title ) && ! empty( $attachment->post_title ) ) {
      $attachment_caption = $attachment->post_title;
    } else {
      $attachment_caption = '';
    }
    if ( isset( $attachment_caption ) && ! empty( $attachment_caption ) ) {
      $output .= '<div class="carousel-caption">';
      $output .= '<h4>' . Markdown( wp_kses_post( $attachment_caption ) ) . '</h4>';
      $output .= '</div>';

    }
    $output .= '</div>';

  } //foreach
  $output .= '</div>
    <a class="left carousel-control" href="#carousel-example-generic-' . $rand . '" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#carousel-example-generic-' . $rand . '" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>';
  $output .= '<p class="pull-right"><span class="label viewall" style="cursor:pointer">View All</span></p>';
  $output .= '
    <script>
      jQuery(document).ready(function(){
        jQuery(".viewall").click(function() {
          jQuery(".carousel-inner").removeClass("carousel-inner");
          jQuery(".carousel-control").hide();
          googletag.pubads().refresh();
          ga(\'send\', \'pageview\');
          urlref = location.href;
          PARSELY.beacon.trackPageView({
            url: urlref,
            urlref: urlref,
            js: 1,
            action_name: "Next Slide"
          });
          jQuery(this).addClass(\'hide\');
          return true;
        })
      });
    </script>
  ';
  $output .= '<div class="clearfix"></div>';
  return $output;
}

add_shortcode( 'new_gallery', 'make_new_gallery_shortcode' );

//add jquery for gravity forms
add_filter('gform_register_init_scripts', 'gform_addScript');
function gform_addScript($form) {
    $script = '(function(){' .
        'jQuery("input[type=radio][name=input_1]").change(function(){
            if (jQuery(this).val().indexOf("Standard Presentation") > -1) {
                //disable "45 minutes" option
                jQuery("input[name=\'input_2.3\']").attr("disabled",true);
                //if option is already checked, uncheck it
                jQuery("input[name=\'input_2.3\']").attr("checked",false);
            }else{
                jQuery("input[name=\'input_2.3\']").attr("disabled",false);
            }
        });' .
    '})(jQuery);';

    GFFormDisplay::add_init_script($form['id'], 'formScript', GFFormDisplay::ON_PAGE_RENDER, $script);

    return $form;
}

/* This function checks if the entry-id set on the form is valid
 * If it is, then it compares the entered email to see if it matches the previous
 * one used on the entry. if it all passes, then they can move to the next step
 * otherwise it returns errors
 */
add_filter( 'gform_validation', 'custom_validation' );
function custom_validation( $validation_result ) {
    $form = $validation_result['form'];

    // determine if entry-id and contact-email id's are in the submitted form
    // and what their field id's are
    $entryID = get_value_by_label('entry-id', $form);
    $contact_email   = get_value_by_label('contact-email', $form);

    //make sure we are in the right form
    if(!empty($entryID) && !empty($contact_email)){
        $entryid        = rgpost( 'input_'. $entryID['id']) ;
        $sub_email      = rgpost( 'input_'. $contact_email['id'] ) ;

        //check if entry-id is valid
        $entry = GFAPI::get_entry( $entryid );
        if(is_array($entry)){
            //finding Field with ID of 1 and marking it as failed validation
            foreach( $form['fields'] as &$field ) {
               if ( $field->id == $contact_email['id']) {     //contact_email
                    $entryForm = GFAPI::get_form( $entry['form_id']);
                    $ef_email = get_value_by_label('contact-email', $entryForm,$entry);
                    $contactEmail = $ef_email['value'];

                    if(strtolower($sub_email) != strtolower($contactEmail)){
                        // set the form validation to false
                        $validation_result['is_valid'] = false;
                        $field->failed_validation = true;
                        $field->validation_message = 'Email does not match contact email on the project';
                    }
                }
            }
        }else{
            // set the form validation to false
            $validation_result['is_valid'] = false;
            //finding Field with ID of 1 and marking it as failed validation
            foreach( $form['fields'] as &$field ) {
                if ( $field->id == $entryID['id']) {
                    // set the form validation to false
                    $validation_result['is_valid'] = false;
                    $field->failed_validation = true;
                    $field->validation_message = 'Invalid Project ID';
                    break;
                }
            }
        }
    }
     //Assign modified $form object back to the validation result
    $validation_result['form'] = $form;
    return $validation_result;
};
add_filter( 'gform_pre_render_35', 'populate_html' );
add_filter( 'gform_pre_render_36', 'populate_html' );
add_filter( 'gform_pre_render_37', 'populate_html' );
add_filter( 'gform_pre_render_38', 'populate_html' );
add_filter( 'gform_pre_render_39', 'populate_html' );
add_filter( 'gform_pre_render_52', 'populate_html' );

/*
 * this logic is for page 2 of 'linked forms'
 * It will take the entry id submitted on page one and use that to pull in various data from the original form submission
 */
function populate_html( $form ) {
    //this is a 2-page form with the data from page one being displayed in an html field on page 2
    $current_page = GFFormDisplay::get_current_page( $form['id'] );
    $html_content = "The information you have submitted is as follows:<br/><ul>";
    if ( $current_page == 2 ) {
       foreach ( $form['fields'] as &$field ) {
           if($field->inputName=='entry-id'){
               $entry_id = rgpost( 'input_' . $field->id );
           }
       }

       $fieldIDarr['project-name']          = 151;
       $fieldIDarr['short-project-desc']    = 16;
       $fieldIDarr['exhibit-contain-fire']  = 83;
       $fieldIDarr['interactive-exhibit']   = 84;
       $fieldIDarr['fire-safety-issues']    = 85;
       $fieldIDarr['serving-food']    = 44;
       $fieldIDarr['you-are-entity']    = 45;
       $fieldIDarr['plans-type']    = "55";
       
       //find the project name for submitted entry-id
       $entry = GFAPI::get_entry( $entry_id );
       foreach ( $form['fields'] as &$field ) {
           if(isset($fieldIDarr[$field->inputName])){
             if ($field->inputName == 'plans-type')
              {   
                $planstypevalues = array();
                for ($i = 1; $i <= 6; $i++) 
                {
                  if (isset( $entry['55.'.$i])  && !empty($entry['55.'.$i]))
                  {
                    $planstypevalues[] = $entry['55.'.$i];
                  }
                }
                $field->defaultValue = implode(',',$planstypevalues);
              }
              else
               $field->defaultValue = $entry[$fieldIDarr[$field->inputName]];
           }
       }
    }

    return($form);
}

//for the Barnes and Noble forms the store preference is set by JS.
//If the back button is selected we need to populate these fields
add_filter( 'gform_pre_render_43', 'BN_storeSelect' );
function BN_storeSelect( $form ) {
    if(isset($_POST["input_341"]) || isset($_POST["input_342"]) || isset($_POST["input_343"])){
        //add selected values to form
        foreach ( $form['fields'] as &$field ) {
            if($field->id==341 || $field->id==342 || $field->id==343){
                $choices = array();
                $storeSel = rgpost("input_".$field->id);
                $choices[] = array( 'text' => $storeSel, 'value' => $storeSel);
                $field->choices = $choices;
            }
        }
    }
    return($form);
}

//when a linked form is submitted, find the initial formid based on entry id
// and add the fields from the linked form to that original entry
add_action( 'gform_after_submission', 'GSP_after_submission', 10, 2 );
function GSP_after_submission($entry, $form ){
    // update meta
    $updateEntryID = get_value_by_label('entry-id', $form, $entry);
    gform_update_meta( $entry['id'], 'entry_id', $updateEntryID['value'] );
}

//=============================================
// Return field ID number based on the
// the Parameter Name for a specific form
//=============================================
function get_value_by_label($key, $form, $entry=array()) {
    $return = array();
    foreach ($form['fields'] as &$field) {
        $lead_key = $field['inputName'];
        if ($lead_key == $key) {
            $return['id']    = $field['id'];
            if(!empty($entry)){
                $return['value'] = $entry[$field['id']];
            }else{
                $return['value']='';
            }
            return $return;
        }
    }
    return false;
}

/*
 * function to allow easier testing of forms by skipping pages and going
 * directly to the page of the form you want to test
 * To skip a page, simply append the ?form_page=2 parameter to the URL of any
 * page on which you are displaying a Gravity Form
 */
add_filter("gform_pre_render", "gform_skip_page");
function gform_skip_page($form) {
    if(!rgpost("is_submit_{$form['id']}") && rgget('form_page') && is_user_logged_in())
        GFFormDisplay::$submission[$form['id']]["page_number"] = rgget('form_page');
    return $form;
}

/* This filter is triggered before the entry detail page is displayed in admin
 * Using this to add class of entryStandout to specific fields
 */
add_filter( 'gform_entry_field_value', 'entry_field_standout', 10, 4 );
function entry_field_standout( $value, $field, $lead, $form ) {
    //topics/category fields
    if (isset($field['id']) && $field['id'] != 64)
        return $value;

    $value = '<span class="entryStandout">'.$value.'</span>';
    return $value;

}

//ajax functionality to update the entry rating
function myajax_update_entry_rating() {
    global $wpdb;
    $entry_id = $_POST['rating_entry_id'];
    $rating   = $_POST['rating'];
    $user     = $_POST['rating_user'];

    //update user rating

    //if there is already a record for this user, update it.
    //else add it.
    $sql = "Insert into wp_rg_lead_rating (entry_id, user_id, rating) "
         . " values (".$entry_id.','.$user.','.$rating.")"
         . " on duplicate key update rating=".$rating.", ratingDate=now()";

    $wpdb->get_results($sql);

    //update the meta with the average rating
    $sql = "SELECT avg(rating) as rating FROM `wp_rg_lead_rating` where entry_id = ".$entry_id;
    $results = $wpdb->get_results($sql);
    $rating = round($results[0]->rating);

    gform_update_meta( $entry_id, 'entryRating', $rating );
    echo 'Your Rating Has Been Saved';
    // IMPORTANT: don't forget to "exit"
    exit;
}

add_action( 'wp_ajax_update-entry-rating', 'myajax_update_entry_rating' );

//adding new meta field to store the average rating
add_filter( 'gform_entry_meta', 'custom_entry_meta', 10, 2);
function custom_entry_meta($entry_meta, $form_id){
    //data will be stored with the meta key named score
    //label - entry list will use Score as the column header
    //is_numeric - used when sorting the entry list, indicates whether the data should be treated as numeric when sorting
    //is_default_column - when set to true automatically adds the column to the entry list, without having to edit and add the column for display
    //update_entry_meta_callback - indicates what function to call to update the entry meta upon form submission or editing an entry
    $entry_meta['entryRating'] = array(
        'label' => 'Rating',
        'is_numeric' => true,
        'update_entry_meta_callback' => 'update_entry_meta',
        'is_default_column' => true,
        'filter'    => array(
        'operators' => array( 'is', 'isnot','<','>' ),
        'choices'   => array(
          array( 'value' => '0', 'text' => 'Unrated' ),
          array( 'value' => '1', 'text' => '1 Stars' ),
          array( 'value' => '2', 'text' => '2 Stars' ),
          array( 'value' => '3', 'text' => '3 Stars' ),
                            array( 'value' => '4', 'text' => '4 Stars' ),
                            array( 'value' => '5', 'text' => '5 Stars' ),
        )
      )
    );

    //create new meta field to hold original entry id
    $entry_meta['entry_id'] = array(
        'label' => 'Original Entry ID',
        'is_numeric' => true,

        'is_default_column' => false
    );
    return $entry_meta;
}

//set the default value for entry rating
function update_entry_ID_meta( $key, $lead, $form ){
    //default entry_-_id
    //$value = '';
    return $value;
}

//set the default value for entry rating
function update_entry_meta( $key, $lead, $form ){
    //default rating
    $value = '0';
    return $value;
}

//formats the ratings field that are displayed in the entries list
add_filter( 'gform_entries_field_value', 'format_ratings', 10, 3 );
function format_ratings( $value, $form_id, $field_id ) {

    if($field_id=='entryRating'){
        if($value==0){
            return 'No Rating';
        }else{
            return $value .' stars';
        }
    }
    return $value;
}

add_filter( 'gform_entries_column_filter', 'change_column_data', 10, 5 );
function change_column_data( $value, $form_id, $field_id, $entry, $query_string ) {
    //only change the data when form id is 1 and field id is 2
    if ( $form_id != 9) {
        return $value;
    }
    if($field_id == 'source_url'){
        $form = GFAPI::get_form( $entry['form_id'] );
        return $form['title'];
    }
    return $value;
}

//add new submenu for our custom built list page
add_filter( 'gform_addon_navigation', 'add_menu_item' );
function add_menu_item( $menu_items ) {
    $menu_items[] = array( "name" => "mf_entries", "label" => "Entries", "callback" => "entries_list", "permission" => "edit_posts" );
    $menu_items[] = array( "name" => "mf_fsp", "label" => "Download FSP", "callback" => "build_pdf_fsp","permission" => "edit_posts" );
    $menu_items[] = array( "name" => "mf_gsp", "label" => "Download GSP", "callback" => "build_pdf_gsp","permission" => "edit_posts" );
    $menu_items[] = array( "name" => "mf_export", "label" => "MF Export", "callback" => "build_mf_export","permission" => "edit_posts" );
    $menu_items[] = array( "name" => "mf_fairesign", "label" => "Faire Signs", "callback" => "build_faire_signs","permission" => "edit_posts" );

    return $menu_items;
}
add_action( 'wp_ajax_createCSVfile', 'createCSVfile' );
add_action( 'admin_post_export_MFform', 'createCSVfile' );
add_action( 'admin_post_createCSVfile', 'createCSVfile' );
function build_faire_signs(){
    require_once( TEMPLATEPATH.'/classes/faire_signs.php' );
}


function build_mf_export(){
    ?>
<h2>Export MakerFaire Forms</h2>
    <h3>Please select the form you want to export:</h3>
     <form method="post" action="admin-post.php">
        <input type="hidden" name="action" value="export_MFform">

        <select id="exportForm" name="exportForm">
                <option value=""><?php _e( 'Select a form', 'gravityforms' ); ?></option>
                <?php
                $forms = RGFormsModel::get_forms( null, 'title' );
                foreach ( $forms as $form ) {
                        ?>
                        <option value="<?php echo absint( $form->id ) ?>"><?php echo esc_html( $form->title ); ?></option>
                <?php
                }
                ?>
        </select>
        <input type="submit" value="Download Export File" class="button button-large button-primary" />
    </form>
    <?php
}

function createCSVfile() {
    //create CSV for individual entries come as a GET request, the mass entry list is a POST request
    $form_id = (isset($_POST['exportForm']) && $_POST['exportForm']!=''?$_POST['exportForm']:'');

    //if the form_id is not set in the post fields, let check the get fields
    if($form_id==''){
        $form_id = (isset($_GET['exForm']) && $_GET['exForm']!=''?$_GET['exForm']:'');
    }
    if($form_id==''){
        die('please select a form');
    }

    $entry_id = (isset($_GET['exEntry']) && $_GET['exEntry']!='' ? $_GET['exEntry']:'');

    //create CSV file
    $form = GFAPI::get_form( $form_id );
    $fieldData = array();

    //put fieldData in a usable array
    foreach($form['fields'] as $field){
        if($field->type!='section' && $field->type!='html' && $field->type!='page')
            $fieldData[$field['id']] = $field;
    }
    $search_criteria['status'] = 'active';
    $entries = array();
    if($entry_id==''){
        $entries = GFAPI::get_entries( $form_id, $search_criteria, null, array('offset' => 0, 'page_size' => 9999) );
    }else{
        //use the submitted entry
        $entries[] = GFAPI::get_entry( $entry_id );
    }

    $output = array('Entry ID','FormID');
    $list = array();
    foreach($fieldData as $field){
        $output[] = $field['label'];
    }
    $list[] = $output;

    foreach($entries as $entry){
        $fieldArray = array($entry['id'],$form_id);
        foreach($fieldData as $field){
            if($field->id==320 || $field->id==321){
                if( in_array( $field->type, array('checkbox', 'select', 'radio') ) ){
                    $currency = GFCommon::get_currency();
                    $value = RGFormsModel::get_lead_field_value( $entry, $field );
                    array_push($fieldArray, GFCommon::get_lead_field_display( $field, $value, $currency, true ));
                }
            }else{
                array_push($fieldArray, (isset($entry[$field->id])?$entry[$field->id]:""));
            }
        }
        $list[] = $fieldArray;
    }

    //write CSV file
    // output headers so that the file is downloaded rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=form-'.$form_id.($entry_id!=''?'-'.$entry_id:'').'.csv');

    $file = fopen('php://output','w');

    foreach ($list as $line){
      fputcsv($file,$line);
    }

    fclose($file);
    //wp_redirect(  admin_url( 'admin.php?page=mf_export'));
    die();

    exit();
}

function build_pdf_fsp(){
     require_once( TEMPLATEPATH.'/fpdi/FSP.php' );
}

function build_pdf_gsp(){
    require_once( TEMPLATEPATH.'/fpdi/GSP.php' );
 }



function entries_list(){
    $view    = rgget( 'view' );
    $lead_id = rgget( 'lid' );

    if ( $view == 'mfentry' && ( rgget( 'lid' ) || ! rgblank( rgget( 'pos' ) ) ) ) {
            //require_once( GFCommon::get_base_path() . '/entry_detail.php' );
            include_once TEMPLATEPATH. '/classes/entry_detail_makerfaire.php';
            GFEntryDetail::lead_detail_page();
    } else if ( $view == 'entries' || empty( $view ) ) {
            include_once TEMPLATEPATH. '/classes/entry_list_makerfaire.php';
            if( !class_exists('GFEntryList')) { require_once(GFCommon::get_base_path() . "/entry_list.php"); }
            GFEntryList::all_leads_page();
    } else {
            $form_id = rgget( 'id' );
            do_action( 'gform_entries_view', $view, $form_id, $lead_id );
    }
}

//remove old entries navigation
function remove_menu_links() {
    global $submenu;
    foreach($submenu['gf_edit_forms'] as $key=>$item){
        if(in_array('gf_entries',$item)){
            unset($submenu['gf_edit_forms'][$key]);
        }
    }
}
add_action( 'admin_menu', 'remove_menu_links', 9999 );

/**
 * Redirect gravity form admin pages to the new makerfaire specific admin pages
 */
function redirect_gf_admin_pages(){
    global $pagenow;

    /* Check current admin page. */
    if($pagenow == 'admin.php'){
        if(isset($_GET['page'])&&$_GET['page']=='gf_entries'){
            //include any parameters in the return URL
            $returnURL = '';
            foreach($_GET as $key=>$param){
                if($key!='page'){
                    if($key=='view' && $param == 'entry')  $param = 'mfentry';
                    $returnURL .= '&'.$key.'='.$param;
                }
            }
            wp_redirect(admin_url( 'admin.php' ) . "?page=mf_entries".$returnURL);
            exit;
        }
    }
}

add_action('admin_menu', 'redirect_gf_admin_pages');

//add new merge tag: user-schedule
add_filter('gform_custom_merge_tags', 'entry_schedule_custom_merge_tags', 10, 4);
add_filter('gform_replace_merge_tags', 'entry_schedule_replace_merge_tags', 10, 7);
add_filter('gform_field_content', 'entry_schedule_field_content', 10, 5);

/**
* add custom merge tags
* @param array $merge_tags
* @param int $form_id
* @param array $fields
* @param int $element_id
* @return array
*/
function entry_schedule_custom_merge_tags($merge_tags, $form_id, $fields, $element_id) {
    $merge_tags[] = array('label' => 'Entry Schedule', 'tag' => '{entry_schedule}');

    return $merge_tags;
}

/**
* replace custom merge tags in notifications
* @param string $text
* @param array $form
* @param array $lead
* @param bool $url_encode
* @param bool $esc_html
* @param bool $nl2br
* @param string $format
* @return string
*/
function entry_schedule_replace_merge_tags($text, $form, $lead, $url_encode, $esc_html, $nl2br, $format) {
    $schedule = get_schedule($lead);
    $text = str_replace('{entry_schedule}', $schedule, $text);

    return $text;
}

/**
* replace custom merge tags in field content
* @param string $field_content
* @param array $field
* @param string $value
* @param int $lead_id
* @param int $form_id
* @return string
*/
function entry_schedule_field_content($field_content, $field, $value, $lead_id, $form_id) {
    if (strpos($field_content, '{entry_schedule}') !== false) {
        $lead = GFAPI::get_entry( $lead_id );
        $schedule = get_schedule($lead);

        $field_content = str_replace('{entry_schedule}', $schedule, $field_content);
    }

    return $field_content;
}

/* Return schedule for lead */
function get_schedule($lead){
    global $wpdb;
    $schedule = '';
    $entry_id = (isset($lead['id'])?$lead['id']:'');

    if($entry_id!=''){
        //get scheduling information for this lead
        $sql = "SELECT  area.area,subarea.subarea,subarea.nicename,
                        schedule.start_dt, schedule.end_dt
                FROM    wp_mf_schedule schedule,
                        wp_mf_location location,
                        wp_mf_faire_subarea subarea,
                        wp_mf_faire_area area

                where       schedule.entry_id   = $entry_id
                        and schedule.location_id=location.ID
                        and location.entry_id   = schedule.entry_id
                        and subarea.id          = location.subarea_id
                        and area.id             = subarea.area_id";

        $results = $wpdb->get_results($sql);
        if($wpdb->num_rows > 0){
            foreach($results as $row){
                $subarea = ($row->nicename!=''&&$row->nicename!=''?$row->nicename:$row->subarea);
                $start_dt = strtotime($row->start_dt);
                $end_dt = strtotime($row->end_dt);
                $schedule .= $row->area.' '.$subarea;
                $schedule .= '<br/>';
                $schedule .= '<span>'.date("l, n/j/y, g:i A",$start_dt).' to '.date("l, n/j/y, g:i A",$end_dt).'</span><br/>';
            }
        }
    }
    return $schedule;
}

//add new Notification event of - send confirmation letter and maker cancelled exhibit
add_filter( 'gform_notification_events', 'add_event' );
function add_event( $notification_events ) {
    $notification_events['confirmation_letter']   = __( 'Confirmation Letter', 'gravityforms' );
    $notification_events['maker_cancel_exhibit']  = __( 'Maker Cancelled Exhibit', 'gravityforms' );
    $notification_events['maker_delete_exhibit']  = __( 'Maker Deleted Exhibit', 'gravityforms' );
    $notification_events['maker_updated_exhibit'] = __( 'Maker Updated Entry', 'gravityforms' );
    return $notification_events;
}

/* This function searches the database to see if any of the image overrides are set in the gravity form
 * If it is, it retrieves the value set for that override
 * Field ID         Description
 * 324              Image Override 1
 * 334              Image Override 1 place
 * 326              Image Override 2
 * 338              Image Override 2 place
 * 333              Image Override 2
 * 337              Image Override 3 place
 * 332              Image Override 4
 * 336              Image Override 4 place
 * 331              Image Override 5
 * 335              Image Override 5 place
 */
function findOverride($entry_id, $type){
    global $wpdb;
    if($entry_id!=''){
        $sql = "select * from wp_rg_lead_detail as detail join "
                . "             (SELECT lead_id,field_number FROM `wp_rg_lead_detail` "
                . "                 WHERE `lead_id` = $entry_id AND `field_number` BETWEEN 334.0 and 338.9 AND `value` = '$type' "
                . "                 ORDER BY `wp_rg_lead_detail`.`field_number` ASC limit 1) "
                . "             as override on detail.lead_id = override.lead_id "
                . "         where   (detail.field_number = 331 and override.field_number between 335.0 and 335.9999) or "
                . "                 (detail.field_number = 332 and override.field_number between 336.0 and 336.9999) or "
                . "                 (detail.field_number = 333 and override.field_number between 337.0 and 337.9999) or "
                . "                 (detail.field_number = 330 and override.field_number between 338.0 and 338.9999) or "
                . "                 (detail.field_number = 329 and override.field_number between 334.0 and 334.9999)";
        $results = $wpdb->get_results($sql);
        if($wpdb->num_rows > 0){

            return $results[0]->value;
        }
    }
    return '';
}

/* Specific logic for the Barnes and Noble forms */
add_filter( 'gform_pre_validation_43', 'update_bn_fields', 10, 2 );

function update_bn_fields($form ) {
    //add the selected value to the form
    foreach ( $form['fields'] as &$field ) {
        if ( $field->id != 341 && $field->id != 342 && $field->id != 343   ) {
            continue;
        }

        $choices = array();
        $choices[] = array( 'text' => $_POST['input_'.$field->id], 'value' => $_POST['input_'.$field->id] );
        $field->choices = $choices;
    }
    return $form;
}


/**
 * Adds the subscribe header return path overlay
 */
function subscribe_return_path_overlay() { ?>
  <div class="overlay-div overlay-slidedown hidden-xs">
    <div class="container-fluid-overlay">
      <div class="container">
        <div class="row">
          <div class="col-sm-4 overlay-1">
            <img class="img-responsive" src="//makezine.com/wp-content/uploads/2015/10/Magazine-cover-44-for-overlay.jpg" alt="Make Magazine Volume 44 Cover" />
          </div>
          <div class="col-sm-4 overlay-2">
            <h2>Get the Magazine</h2>
            <p>Make: is the voice of the Maker Movement, empowering, inspiring, and connecting Makers worldwide to tinker and hack. Subscribe to Make Magazine Today!</p>
            <a class="black-overlay-btn" target="_blank" href="https://readerservices.makezine.com/mk/default.aspx?utm_source=makerfaire.com&utm_medium=brand+bar&utm_campaign=mag+sub&pc=MK&pk=M5BMFR">SUBSCRIBE</a>
          </div>
          <div class="col-sm-4 overlay-3">
            <h2>Sign Up for the Maker Faire Newsletter</h2>
            <p>Keep informed, stay inspired.</p>
            <form class="sub-form whatcounts-signup1o" action="http://whatcounts.com/bin/listctrl" method="POST">
              <input type="hidden" name="slid_1" value="6B5869DC547D3D46E66DEF1987C64E7A"/>
              <input type="hidden" name="slid_2" value="6B5869DC547D3D46941051CC68679543" /><!-- Maker Media Newsletter -->
              <input type="hidden" name="multiadd" value="1" />
              <input type="hidden" name="cmd" value="subscribe"/>
              <input type="hidden" name="custom_source" value="Subscribe return path overlay"/>
              <input type="hidden" name="custom_incentive" value="none"/>
              <input type="hidden" name="custom_url" value=""/>
              <input type="hidden" id="format_mime" name="format" value="mime"/>
              <input type="hidden" name="goto" value="//makerfaire.com/thanks-for-signing-up"/>
              <input type="hidden" name="custom_host" value="makerfaire.com" />
              <input type="hidden" name="errors_to" value=""/>
              <input name="email" id="wc-email-o" class="overlay-input" placeholder="Enter your email" required type="email"><br>
              <input value="GO" class="black-overlay-btn" type="submit">
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    jQuery('#trigger-overlay, .overlay-div').hover(
      function () {
          jQuery('.overlay-div').stop().addClass( 'open' );
          jQuery( 'body' ).addClass( 'modal-open' );
      },
      function () {
          jQuery('.overlay-div').stop().removeClass( 'open' );
          jQuery( 'body' ).removeClass( 'modal-open' );
      }
    );
  </script>
<?php }

/**
 * Adds the newsletter subscribe modal html to the end of the body
 */
function display_thank_you_modal_if_signed_up() { ?>
  <div class="fancybox-thx" style="display:none;">
    <div class="nl-modal-extra-cont nl-thx-p1">
      <div class="nl-modal-div1">
        <div class="col-sm-8 col-xs-12">
          <h4>Welcome to the Make Faire Community!</h4>
          <p><span class="nl-modal-email-address"></span> you are now signed up to the Maker Faire newsletter.</p>
        </div>
        <div class="col-sm-4 hidden-xs text-center">
          <i class="fa fa-check-square-o fa-5x"></i>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="nl-modal-div2">
        <div class="col-xs-12">
          <?php
            $isSecure = "http://";
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $isSecure = "https://";
            }
            ?>
          <h4>You might also like these Newsletters:</h4>
          <form class="whatcounts-signup2" action="http://whatcounts.com/bin/listctrl" method="POST">
            <input type="hidden" name="cmd" value="subscribe" />
            <input type="hidden" name="multiadd" value="1" />
            <input type="hidden" id="email" name="email" value="" />
            <input type="hidden" id="format_mime" name="format" value="mime" />
            <input type="hidden" name="goto" value="" />
            <input type="hidden" name="custom_source" value="footer" />
            <input type="hidden" name="custom_incentive" value="none" />
            <input type="hidden" name="custom_url" value="<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" />
            <input type="hidden" id="format_mime" name="format" value="mime" />
            <input type="hidden" name="goto" value="" />
            <input type="hidden" name="custom_host" value="<?php echo $_SERVER["HTTP_HOST"]; ?>" />
            <label class="list-radio pull-right">
              <input type="checkbox" id="list_6B5869DC547D3D46B52F3516A785F101_yes" name="slid_1" value="6B5869DC547D3D46B52F3516A785F101" />
              <span for="list_6B5869DC547D3D46B52F3516A785F101_yes" class="newcheckbox"></span>
            </label>
            <h4>Make: Weekly Digest</h4>
            <p>The best stuff every week.</p>
            <hr />
            <label class="list-radio pull-right">
              <input type="checkbox" id="list_6B5869DC547D3D46510F6AB3E701BA0A_yes" name="slid_2" value="6B5869DC547D3D46510F6AB3E701BA0A" />
              <span for="list_6B5869DC547D3D46510F6AB3E701BA0A_yes" class="newcheckbox"></span>
            </label>
            <h4>Maker Shed</h4>
            <p>Be the first to learn about new products, plus exclusive discounts.</p>
            <hr />
            <label class="list-radio pull-right">
              <input type="checkbox" id="list_6B5869DC547D3D467B33E192ADD9BE4B_yes" name="slid_3" value="6B5869DC547D3D467B33E192ADD9BE4B" />
              <span for="list_6B5869DC547D3D467B33E192ADD9BE4B_yes" class="newcheckbox"></span>
            </label>
            <h4>Maker Pro</h4>
            <p>The latest news about startups, products, incubators, and innovators.</p>
            <hr />
            <input class="ghost-button-black pull-right" type="submit" value="Submit" />
            <div class="clearfix"></div>
          </form>
        </div>
      </div>
    </div>
    <div class="nl-modal-cont nl-thx-p2" style="display:none;">
      <div class="col-sm-4 hidden-xs nl-modal">
        <span class="fa-stack fa-4x">
        <i class="fa fa-circle-thin fa-stack-2x"></i>
        <i class="fa fa-thumbs-o-up fa-stack-1x"></i>
        </span>
      </div>
      <div class="col-sm-8 col-xs-12 nl-modal">
        <h3>Awesome!</h3>
        <p>Thanks for signing up.</p>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
  <script>
  // Footer newsletter sign up form and modal
  jQuery(document).ready(function(){
    jQuery(".fancybox-thx").fancybox({
      autoSize : false,
      width  : 400,
      autoHeight : true,
      padding : 0,
      afterLoad   : function() {
          this.content = this.content.html();
      }
    });
    // Desktop
    jQuery(document).on('submit', '.whatcounts-signup1', function (e) {
      e.preventDefault();
      var bla = jQuery('#wc-email').val();
      jQuery.post('http://whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup1').serialize());
      jQuery('.fancybox-thx').trigger('click');
      jQuery('.nl-modal-email-address').text(bla);
      jQuery('.whatcounts-signup2 #email').val(bla);
    });
    // Mobile
    jQuery(document).on('submit', '.whatcounts-signup1m', function (e) {
      e.preventDefault();
      var bla = jQuery('#wc-email-m').val();
      jQuery.post('http://whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup1m').serialize());
      jQuery('.fancybox-thx').trigger('click');
      jQuery('.nl-modal-email-address').text(bla);
      jQuery('.whatcounts-signup2 #email').val(bla);
    });
    // Header Overlay
    jQuery(document).on('submit', '.whatcounts-signup1o', function (e) {
      e.preventDefault();
      var bla = jQuery('#wc-email-o').val();
      jQuery.cookie('Newsletter-signup', '', { path: '/', expires: 365 });
      jQuery.post('http://whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup1o').serialize());
      jQuery('.fancybox-thx').trigger('click');
      jQuery('.nl-modal-email-address').text(bla);
      jQuery('.whatcounts-signup2 #email').val(bla);
    });
    jQuery(document).on('submit', '.whatcounts-signup2', function (e) {
      e.preventDefault();
      jQuery.post('http://whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup2').serialize());
      jQuery('.nl-thx-p1').hide();
      jQuery('.nl-thx-p2').show();
    });
    jQuery('input[type="checkbox"]').click(function(e){
      e.stopPropagation();
    });
  });
  </script>
<?php }



//This function is used to enqueue the angularJS!!!!
function angular_scripts() {
  if (is_page('ribbons')) {
    wp_enqueue_script(
      'angularjs',
      get_stylesheet_directory_uri() . '/bower_components/angular/angular.min.js'
    );
    wp_enqueue_script(
      'angularjs-route',
      get_stylesheet_directory_uri() . '/bower_components/angular-route/angular-route.min.js'
    );
    wp_enqueue_script(
      'dirPagination',
      get_stylesheet_directory_uri() . '/bower_components/angularUtils-pagination/dirPagination.js',
      array( 'angularjs', 'angularjs-route' )
    );
    wp_enqueue_script(
      'angular-scripts',
      get_stylesheet_directory_uri() . '/js/angular/scripts.js',
      array( 'angularjs', 'angularjs-route' )
    );
    wp_localize_script('angular-scripts',  'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

    wp_localize_script(
      'angular-scripts',
      'angularLocalized',
      array(
        'partials' => trailingslashit( get_template_directory_uri() ) . 'partials/'
        )
    );
  }
}
add_action( 'wp_enqueue_scripts', 'angular_scripts' );

// This function is called via ajax to retriev the blue ribbon data
function retrieveRibbonData() {
   global $wpdb;
   require_once( TEMPLATEPATH. '/partials/ribbonJSON.php' );
    // IMPORTANT: don't forget to "exit"
    exit;
}
add_action( 'wp_ajax_nopriv_getRibbonData', 'retrieveRibbonData' );
add_action( 'wp_ajax_getRibbonData', 'retrieveRibbonData' );

/* Changes to gravity view for maker admin tool */

//Maker Admin - add new fields in gravity view
add_filter('gravityview_additional_fields','gv_add_faire',10,2);
function gv_add_faire($additional_fields){
  $additional_fields[] = array("label_text" => "Faire",        "desc"          => "Display Faire Name",
                               "field_id"   => "faire_name",   "label_type"    => "field",
                               "input_type" => "text",         "field_options" => NULL, "settings_html"=> NULL);
  $additional_fields[] = array("label_text" => "Maker Cancel Entry", "desc"          => "Maker Cancel Entry Link",
                               "field_id"   => "cancel_link",  "label_type"    => "field",
                               "input_type" => "text",         "field_options" => NULL, "settings_html"=> NULL);
  $additional_fields[] = array("label_text" => "Maker Copy Entry",   "desc"          => "Maker Copy Entry Link",
                               "field_id"   => "copy_entry",   "label_type"    => "field",
                               "input_type" => "text",         "field_options" => NULL, "settings_html"=> NULL);
  $additional_fields[] = array("label_text" => "Maker Delete Entry",   "desc"          => "Maker Delete Entry Link",
                               "field_id"   => "delete_entry",   "label_type"    => "field",
                               "input_type" => "text",         "field_options" => NULL, "settings_html"=> NULL);
  return $additional_fields;
}

//Maker Admin - populate new fields in gravity view
add_filter('gform_entry_field_value','gv_faire_name',10,4);
function gv_faire_name($display_value, $field, $entry, $form){
    if($field["type"]=='faire_name'){
        global $wpdb;

        $form_id = $entry['form_id'];
        $sql = "select faire_name from wp_mf_faire where FIND_IN_SET ($form_id,wp_mf_faire.form_ids)> 0";
        $faire = $wpdb->get_results($sql);

        $faire_name = (isset($faire[0]->faire_name) ? $faire[0]->faire_name:$sql);
        $display_value = $faire_name;
    }elseif($field["type"]=='cancel_link'){
        $display_value = '<a href="#cancelEntry" data-toggle="modal" data-projName="'.$entry['151'].'" data-entry-id="'.$entry['id'].'">Cancel</a>';
    }elseif($field["type"]=='copy_entry'){
        $display_value = '<a href="#copy_entry" data-toggle="modal" data-entry-id="'.$entry['id'].'">Copy</a>';
    }elseif($field["type"]=='delete_entry'){
        $display_value = '<a href="#deleteEntry" data-toggle="modal" data-projName="'.$entry['151'].'" data-entry-id="'.$entry['id'].'">Delete</a>';
    }

    return $display_value;
}

/* Maker Admin - After the user has updated their entry
 * Change the returned success message, including the link
 */
function gv_my_update_message( $message, $view_id, $entry, $back_link ) {
    $link = str_replace( 'entry/'.$entry['id'].'/', '', $back_link );
    return 'Entry Updated. <a href="'.esc_url($link).'">Return to your entry list</a>';
}
add_filter( 'gravityview/edit_entry/success', 'gv_my_update_message', 10, 4 );

/* Maker Admin -
 * Customise the cancel(back) button link
 */
function gv_my_edit_cancel_link( $back_link, $form, $entry, $view_id ) {
    return str_replace( 'entry/'.$entry['id'].'/', '', $back_link );
}
add_filter( 'gravityview/edit_entry/cancel_link', 'gv_my_edit_cancel_link', 10, 4 );

/* Used in maker admin, this function is called by ajax to allow a user to cancel
 * an entry and to send a notification */
function makerCancelEntry(){
  $entryID = (isset($_POST['cancel_entry_id']) ? $_POST['cancel_entry_id']:0);
  $reason  = (isset($_POST['cancel_reason'])   ? $_POST['cancel_reason']  :'');
  if($entryID!=0){
    //get entry data and form data
    $lead = GFAPI::get_entry(esc_attr($entryID));
    $form = GFAPI::get_form( $lead['form_id']);

    //Update Status to Cancelled
    mf_update_entry_field($entryID,'303','Cancelled');

    //Make a note of the cancellation
    $cancelText = "The Exhibit has been cancelled by the maker.  Reason given is: ".stripslashes($reason);
    mf_add_note($entryID,$cancelText);

    //Handle notifications for acceptance
    $notifications_to_send = GFCommon::get_notifications_to_send( 'maker_cancel_exhibit', $form, $lead );
    foreach ( $notifications_to_send as $notification ) {
            if($notification['isActive']){
                GFCommon::send_notification( $notification, $form, $lead );
            }

    }

    //GFJDBHELPER::gravityforms_sync_status_jdb($entry_info_entry_id,$acceptance_status_change);

    echo $lead['151'].', Exhibit ID '.$entryID.' has been cancelled';

  }else{
    echo 'Error in cancelling this exhibit.';
  }

  exit;
}
add_action( 'wp_ajax_maker-cancel-entry', 'makerCancelEntry' );

/* Used in maker admin, this function is called by ajax to allow a user to delete
 * an entry and to send a notification */
function makerDeleteEntry(){
  $entryID = (isset($_POST['delete_entry_id']) ? $_POST['delete_entry_id']:0);
  if($entryID!=0){
    //get entry data and form data
    $lead = GFAPI::get_entry(esc_attr($entryID));
    $form = GFAPI::get_form( $lead['form_id']);

    $trashed = GFAPI::update_entry_property( $entryID, 'status', 'trash' );
    new GravityView_Cache;

    if( ! $trashed ) {
        echo new WP_Error( 'trash_entry_failed', __('Moving the entry to the trash failed.', 'gravityview' ) );
    }

    //Make a note of the delete
    mf_add_note($entryID,"The Exhibit has been deleted by the maker.");

    //Handle notifications for acceptance
    $notifications_to_send = GFCommon::get_notifications_to_send( 'maker_delete_exhibit', $form, $lead );
    foreach ( $notifications_to_send as $notification ) {
        if($notification['isActive']){
            GFCommon::send_notification( $notification, $form, $lead );
        }
    }
    echo $lead['151'].', Exhibit ID '.$entryID.' has been deleted';
  }else{
    echo 'Error in deleting this entry.';
  }
  exit;
}
add_action( 'wp_ajax_maker-delete-entry', 'makerDeleteEntry' );

//disable gravity view cache
add_filter('gravityview_use_cache', '__return_false');

/* Used in maker admin this function is called via
 * ajax to copy an entry into a new form
 */
function makeAdminCopyEntry(){
  $entryID    = (isset($_POST['copy_entry_id']) ? $_POST['copy_entry_id']:0);
  $copy2Form  = (isset($_POST['copy2Form'])   ? $_POST['copy2Form']  :'');
  $view_id    = (isset($_POST['view_id'])?$_POST['view_id']:0);

  if($entryID!=0 and $copy2Form != '' && $view_id!=0){
    //get entry data
    $lead = GFAPI::get_entry(esc_attr($entryID));

    //get new form field ID's
    $form = GFAPI::get_form( $copy2Form);

    /*The following fields will not be copied from one entry to another
     * Page 4 review fields:
     * 295 - Are you 18 years or older
     * 114 - Full Name
     * 297 - I am the parent and/or legal guardian of
     * 115 - Date
     * 117 - Release and consent
     * all admin only fields
     */
    $doNotCopy = array(295,114,297,115,117);

    /*loop thru fields in existing entry and if they are in the new form copy them */
    $newEntry = array();
    $newEntry['form_id'] = $copy2Form;
    foreach($form['fields'] as $field){
        //skip doNotCopy fields
        if(!in_array($field['id'], $doNotCopy)){
            //do not copy admin only fields
            $adminOnly = (isset($field['adminOnly']) ? $field['adminOnly'] : FALSE);
            if(!$adminOnly){
                if(is_array($field['inputs'])){
                    foreach($field['inputs'] as $inputs){
                        $fieldID = $inputs['id'];
                        if(isset($lead[$fieldID])){
                            $newEntry[$fieldID] = $lead[$fieldID];
                        }
                    }
                }
                if(isset($lead[$field['id']])){
                    $newEntry[$field['id']] = $lead[$field['id']];
                }
            }
        }
    }
    $newEntry['303'] = 'In Progress'; //in-progress
    $newEntry_id = GFAPI::add_entry( $newEntry );
    $entry = GFAPI::get_entry($newEntry_id);
    $href = GravityView_Edit_Entry::get_edit_link( $entry, $view_id );

    echo 'New Entry created:'.$newEntry_id.'. Please click <a href="entry/'.$newEntry_id.'/'.$href.'">here</a> to finish the submission process';
  }else{
    echo 'Error in creating a new entry. Proper data was not received.';
  }

  exit;
}
add_action( 'wp_ajax_make-admin-copy-entry', 'makeAdminCopyEntry' );

/* This adds new form types for the users to select when creating new gravity forms */
add_filter( 'gform_form_settings', 'my_custom_form_setting', 10, 2 );
function my_custom_form_setting( $settings, $form ) {
    //var_dump($settings['Form Basics']);
    $form_type = rgar($form, 'form_type');
    $settings['Form Basics']['form_type'] = '
        <tr>
            <th><label for="my_custom_setting">Form Type</label></th>
            <td><select name="form_type">
                <option value="Exhibit" '.($form_type=='Exhibit'?'selected':'').'>Exhibit</option>
                <option value="Presentation" '.($form_type=='Presentation'?'selected':'').'>Presentation</option>
                <option value="Performance" '.($form_type=='Performance'?'selected':'').'>Performance</option>
                <option value="Startup Sponsor" '.($form_type=='Startup Sponsor'?'selected':'').'>Startup Sponsor</option>
                <option value="Sponsor" '.($form_type=='Sponsor'?'selected':'').'>Sponsor</option>
                <option value="Other" '.($form_type=='Other' || $form_type==''?'selected':'').'>Other</option>
            </select></td>
        </tr>';

    return $settings;
}

/* This will save the form type selected by admin users */
add_filter( 'gform_pre_form_settings_save', 'save_form_type_form_setting' );
function save_form_type_form_setting($form) {
    $form['form_type'] = rgpost( 'form_type' );
    return $form;
}

/* Allows a user to delete entries in gravity view. used in maker admin */
add_filter('gravityview/delete-entry/mode','gView_trash_entry');
function gView_trash_entry(){
    return 'trash';
}

/* This function will write all user changes to entries to a database table to create a change report */
add_action('gravityview/edit_entry/after_update','GVupdate_notification',10,3);
function GVupdate_notification($form,$entry_id,$orig_entry){
    //get updated entry
    $updatedEntry = GFAPI::get_entry(esc_attr($entry_id));
    $updates = array();

    foreach($form['fields'] as $field){
        //send notification after entry is updated in maker admin
        $input_id = $field->id;

        //if field type is checkbox we need to compare each of the inputs for changes
        $inputs = $field->get_entry_inputs();
        if ( is_array( $inputs ) ) {
            foreach ( $inputs as $input ) {
                $input_id = $input['id'];
                $origField    = (isset($orig_entry[$input_id])   ?  $orig_entry[$input_id ] : '');
                $updatedField = (isset($updatedEntry[$input_id]) ?  $updatedEntry[$input_id ] : '');
                $fieldLabel   = ($field['adminLabel']!=''?$field['adminLabel']:$field['label']);
                if($origField!=$updatedField){
                    //update field id
                    $updates[] = array('lead_id'=>$entry_id,'field_id'=>$input_id,'field_before'=>$origField,'field_after'=>$updatedField,'fieldLabel'=>$fieldLabel);
                }
            }
        } else {
            $origField    = (isset($orig_entry[$input_id])   ?  $orig_entry[$input_id ] : '');
            $updatedField = (isset($updatedEntry[$input_id]) ?  $updatedEntry[$input_id ] : '');
            $fieldLabel   = ($field['adminLabel']!=''?$field['adminLabel']:$field['label']);
            if($origField!=$updatedField){
                //update field id
                $updates[] = array('lead_id'=>$entry_id,'field_id'=>$input_id,'field_before'=>$origField,'field_after'=>$updatedField,'fieldLabel'=>$fieldLabel);
            }
        }

    }

    //check if there are any updates to process
    if(!empty($updates)){
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;//current user id
        $inserts = '';
        //field name

        //update database with this information
        foreach($updates as $update){
            if($inserts !='') $inserts.= ',';
            $inserts .= '('.$user_id.','.$update['lead_id'].','.$form['id'].','.$update['field_id'].',"'.$update['field_before'].'","'.$update['field_after'].'","'.$update['fieldLabel'].'")';
        }

        $sql = "insert into wp_rg_lead_detail_changes (user_id, lead_id, form_id, field_id, field_before, field_after,fieldLabel) values " .$inserts;

        global $wpdb;
        $wpdb->get_results($sql);
    }
}

/* This function is used by the individual entry pages to display if this entry one any ribbons */
function checkForRibbons($postID=0,$entryID=0){
    global $wpdb;
    if($postID != 0){
        $sql = "select * from wp_mf_ribbons where post_id = ".$postID." order by ribbonType";
    }else{
        $sql = "select * from wp_mf_ribbons where entry_id = ".$entryID." order by ribbonType";
    }
    $ribbons = $wpdb->get_results($sql);
    $return = "";
    //check for 0??
    $blueCount = $redCount = 0;
    foreach($ribbons as $ribbon){
      if($ribbon->ribbonType==0){
        for($i=0; $i< $ribbon->numRibbons;$i++){
          $return .= '<div class="blueMakey"></div>';
        }
      }

      if($ribbon->ribbonType==1){
        for($i=0; $i< $ribbon->numRibbons;$i++){
          $return .= '<div class="redMakey"></div>';
        }
      }
    }
    return $return;
}

/* This function is used to update entry resources and entry attributes via AJAX */
function update_entry_resatt() {
  global $wpdb;
  $ID        = $_POST['ID'];
  $table     = $_POST['table'];
  //set who is updating the record
  $current_user = wp_get_current_user();

  if($ID==0){ //add new record
    $insertArr = $_POST['insertArr'];
    foreach($insertArr as $key=>$value){
      $fields[] =$key;
      $values[] =$value;
    }
      $sql = "insert into ".$table.' ('.implode(',',$fields).',user) VALUES ("'.implode('","',$values).'",'.$current_user->ID.')';
  }else{ //update existing record
    $newValue  = $_POST['newValue'];
    $fieldName = $_POST['fieldName'];
    $sql = "update ".$table.' set '.$fieldName .'="'.$newValue.'",user= '.$current_user->ID.' where ID='.$ID;
  }

  $wpdb->get_results($sql);
  //return the ID
  if($ID==0)  $ID = $wpdb->insert_id;
  $response = array('message'=>'Saved','ID'=>$ID,'user'=>$current_user->display_name,'dateupdate'=>current_time('m/d/y h:i a'));
  wp_send_json( $response );

  // IMPORTANT: don't forget to "exit"
  exit;
}
add_action( 'wp_ajax_update-entry-resAtt', 'update_entry_resatt' );

/* This function is used to delete entry resources and entry attributes via AJAX */
function delete_entry_resatt() {
  global $wpdb;
  $table = (isset($_POST['table']) ? $_POST['table']:'');
  $ID    = (isset($_POST['ID'])    ? $_POST['ID']:0);
  $response = array('table'=>$table,'ID'=>$ID);
  if($ID != 0 && $table != ''){
    $sql = "DELETE from ".$table ." where ID =".$ID;
    $wpdb->get_results($sql);
    $response = array('message'=>'Deleted','ID'=>$ID);
  }
  wp_send_json( $response );
  // IMPORTANT: don't forget to "exit"
  exit;
}
add_action( 'wp_ajax_delete-entry-resAtt', 'delete_entry_resatt' );