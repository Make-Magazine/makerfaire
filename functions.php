<?php

// Set our global Faire Variable. Use the slug of the taxonomy as the value.
define( 'MF_CURRENT_FAIRE', 'world-maker-faire-new-york-2014' );


require_once( WP_CONTENT_DIR . '/themes/vip/plugins/vip-init.php' );

// Include Gigya plugin
require_once( __DIR__ . '/plugins/gigya/gigya.php' );

// include maker-faire-forms plugin
require_once( __DIR__ . '/plugins/maker-faire-forms/maker-faire-forms.php' );

// include maker-faire-forms plugin
require_once( __DIR__ . '/plugins/public-pages/makers.php' );

// include maker-faire-forms plugin
require_once( __DIR__ . '/post-types/maker.php' );

// Markdown
include_once dirname(__file__) . '/plugins/markdown/markdown.php';

// Status Board
include_once dirname(__file__) . '/plugins/status-board/status-board.php';

// Current Faire Page
include_once dirname( __FILE__ ) . '/plugins/admin-pages/current-faire/current-faire.php';

// Sponsor Carousel
include_once dirname( __FILE__ ) . '/plugins/public-pages/sponsor.php';

// Sponsor Carousel
include_once dirname( __FILE__ ) . '/plugins/instagram/instagram.php';

// Post Locker
include_once dirname( __FILE__ ) . '/plugins/hide-post-locker/hide-post-locker.php';

// Blue Ribbons
include_once dirname( __FILE__ ) . '/plugins/blue-ribbons/blue-ribbons.php';

// White House
include_once dirname( __FILE__ ) . '/plugins/white-house/white-house.php';

// Load the settings field for the Applications API
include_once dirname( __FILE__ ) . '/api/admin-settings.php';

// Load the functions for the Applications API
include_once dirname( __FILE__ ) . '/api/v2/functions.php';

require_once( 'taxonomies/type.php' );
require_once( 'taxonomies/sponsor-category.php' );
require_once( 'taxonomies/location.php' );
require_once( 'taxonomies/faire.php' );
require_once( 'taxonomies/location_category.php' );
require_once( 'taxonomies/group.php' );
require_once( 'plugins/post-types/event-items.php' );
require_once( 'post-types/sponsor.php' );
require_once( 'post-types/location.php' );
if ( defined( 'WP_CLI' ) && WP_CLI )
	require_once( 'plugins/wp-cli/wp-cli.php' );

if ( function_exists( 'wpcom_vip_load_plugin' ) ) {
	wpcom_vip_load_plugin( 'easy-custom-fields' );
}

// load edit-flow plugin
if ( function_exists( 'wpcom_vip_load_plugin' ) ) {
	wpcom_vip_load_plugin( 'edit-flow' );
}

// add post-thumbnails support to theme
add_theme_support( 'post-thumbnails' );
add_image_size( 'schedule-thumb', 140, 140, true );

if ( function_exists( 'wpcom_vip_enable_opengraph' ) ) {
	wpcom_vip_enable_opengraph();
}

if ( function_exists( 'vip_contrib_add_upload_cap' ) ) {
	vip_contrib_add_upload_cap();
}

if ( function_exists( 'wpcom_vip_sharing_twitter_via' ) ) {
	wpcom_vip_sharing_twitter_via( 'make' );
}

function make_enqueue_jquery() {
	// Styles
	wp_enqueue_style( 'make-bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.css' );
	wp_enqueue_style( 'make-styles', get_stylesheet_directory_uri() . '/css/style.css' );
	wp_enqueue_style( 'ytv', get_stylesheet_directory_uri() . '/css/ytv.css' );

	// Scripts
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'make-bootstrap', get_stylesheet_directory_uri() . '/js/bootstrap.js', array( 'jquery' ) );
	wp_enqueue_script( 'make-countdown', get_stylesheet_directory_uri() . '/js/jquery.countdown.js', array( 'jquery' ) );
	wp_enqueue_script( 'jquery_cookie',  get_stylesheet_directory_uri() . '/js/jquery.cookie.js', array( 'jquery' ), null );
	wp_enqueue_script( 'ytv', get_stylesheet_directory_uri() . '/js/ytv.js', array( 'jquery' ) );

}
add_action( 'wp_enqueue_scripts', 'make_enqueue_jquery' );


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
	$good = array( "'",     "'",  '"',  "'",       "'",     '&',     '&',      '&',     '"',     '"',      '"',     '"',     '–',       '>',    '...',     ' ',     '-',     '®',    'ů',     'š',     'á',		'™',	 '©' );

	// If we are are correct HTML, let's add that here as reports we don't want HTML shown in here
	if ( $report ) {
		array_push( $bad,  '<br />\rn', '<br />rn', '<br />nn', 'rnrn', '<br />', '\rn', '.rn' );
		array_push( $good, ' ',		    ' ',		' ',	    ' ',    ' ',      ' ',   ' ' );
	} else {
		array_push( $bad,  '<br />\rn',    '<br />rn',     '<br />nn',     'rnrn',         '.rn' );
		array_push( $good, '<br /><br />', '<br /><br />', '<br /><br />', '<br /><br />', '.<br /><br />' );
	}

	$cleaned = str_replace( $bad, $good, htmlspecialchars_decode( mf_convert_newlines( $content ) ) );

	return $cleaned;
}


function makerfaire_get_news() {
	$url = 'http://makezine.com/maker-faire-news/';
	$output = wpcom_vip_file_get_contents( $url, 3, 60,  array( 'obey_cache_control_header' => false ) );
	return $output;
}

add_shortcode('news', 'makerfaire_get_news');

function makerfaire_get_beat() {
	$url = 'http://makezine.com/beat-reports/';
	$output = wpcom_vip_file_get_contents( $url, 3, 60*60,  array( 'obey_cache_control_header' => false ) );
	return $output;
}

add_shortcode('mf_beat_reports', 'makerfaire_get_beat');

function makerfaire_sidebar_news() {

	$url = 'http://makezine.com/maker-faire-news-sidebar/';
	$output = wpcom_vip_file_get_contents( $url, 3, 60*60,  array( 'obey_cache_control_header' => false ) );
	return $output;

}

function makerfaire_get_slider() {
	$url = 'http://makezine.com/maker-faire-featured-slider/';
	$output = wpcom_vip_file_get_contents( $url, 3, 60,  array( 'obey_cache_control_header' => false ) );
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
	array( 'header-menu' => __( 'Header Menu' ) )
  );
}
add_action( 'init', 'isc_register_menus' );


function makerfaire_carousel_shortcode( $atts ) {
	extract( shortcode_atts( array( 'id' => 'biggins'), $atts ) );

	return 	'<a class="carousel-control left" href="#' . esc_attr( $id ) . '" data-slide="prev">&lsaquo;</a>
			<a class="carousel-control right" href="#' . esc_attr( $id ) . '" data-slide="next">&rsaquo;</a>';
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

		$output = '<form action="http://makermedia.createsend.com/t/r/s/jjuruj/" method="post" class="form-horizontal" id="subForm">
			<fieldset>

			<legend>Sign up for the Maker Faire Newsletter</legend>

			<div class="control-group">
				<label for="name" class="control-label">Your Name:</label>
				<div class="controls">
					<input type="text" class="input-xlarge" name="cm-name" id="name" size="35" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="jjuruj-jjuruj">Your Email:</label>
				<div class="controls">
					<input type="text" class="input-xlarge" name="cm-jjuruj-jjuruj" id="jjuruj-jjuruj" size="35" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="cm621683"></label>
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" name="cm-fo-dduult" id="cm621683" value="621683" />
						Please let me know when the Call for Makers goes out!
					</label>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="optionsCheckbox">Any chance we could interest you in...</label>
				<div class="controls">
				</div>
				<div class="controls">
					<label for="MAKENewsletter" class="checkbox">
						<input type="checkbox" name="cm-ol-jjuylk" id="MAKENewsletter" />
						The MAKE Newsletter?
					</label>
				</div>
			</div>

			<div class="form-actions">
				<input type="submit" value="Subscribe" class="btn btn-primary" />
			</div>

			</fieldset>
		</form>';

	return $output;
}

add_shortcode( 'newsletter', 'makerfaire_newsletter_shortcode' );

/**
 * Modal Window Builder
 */
function make_modal_builder( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'launch' 	=> 'Launch Window',
		'title' 	=> 'Modal Title',
		'btn_class'	=> '',
		'embed'	=> ''
	), $atts ) );

	$number = mt_rand();
	$output = '<a class="btn  ' . esc_attr( $btn_class ) . '" data-toggle="modal" href="#modal-' . $number . '">' . esc_html( $launch ) . '</a>';
	$output .= '<div id="modal-' . $number . '" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
	$output .= '	<div class="modal-header">';
	$output .= '		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
	$output .= '		<h3>' . esc_html( $title ) . '</h3>';
	$output .= '	</div>';
	$output .= '	<div class="modal-body">';
	if ( wpcom_vip_is_valid_domain( $embed,  array('fora.tv', 'ustream.com', 'ustream.tv' ) ) ) {
		$output .= '<iframe src="' . esc_url( $embed ) . '" width="530" height="320" frameborder="0"></iframe>';
	} else {
		$output .= ( !empty( $embed ) ) ? wpcom_vip_wp_oembed_get( esc_url( $embed ), array( 'width' => 530 ) ) : '';
	}
	$output .= 			wp_kses_post( $content );
	$output .= '	</div>';
	$output .= '	<div class="modal-footer">';
	$output .= '		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>';
	$output .= '	</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'modal', 'make_modal_builder' );

function makerfaire_news_rss() { ?>
	<div class="newsies">
		<div class="news post">
			<h3 style="color: #fc040c;"><a href="http://makezine.com/tag/maker-faire/">Latest Maker Faire News</a></h3>
			<?php
			$fs = makerfaire_index_feed();

			foreach($fs as $f) : $a = $f['i']->get_authors(); ?>
				<h4><a href="<?php echo esc_url($f['i']->get_link()); ?>"><?php echo esc_html($f['i']->get_title()); ?></a></h4>
				<div class="row">
					<div class="span2">
						<a href="<?php echo esc_url($f['i']->get_link()); ?>" title="<?php echo esc_attr($f['i']->get_title()); ?>"><img class="thumbnail faire-thumb " alt="<?php echo esc_attr($f['i']->get_title()); ?>" src="<?php echo esc_url($f['src']); ?>" /></a>
					</div>
					<div class="span6">
					<?php echo str_replace(array($f['img'], '<p><a href="'.$f['i']->get_link().'">Read the full article on MAKE</a></p>'), '', html_entity_decode(esc_html($f['i']->get_description()))); ?>
					<p class="read_more" style="margin:10px 0"><strong>
					<a class="btn btn-primary btn-mini" href="<?php echo esc_url($f['i']->get_link()); ?>">Read full story &raquo;</a></strong></p>

						<ul class="unstyled">
							<li>Posted by <?php echo esc_html($a[0]->name); ?> | <?php echo esc_html($f['i']->get_date()); ?></li>
							<li>Categories: <?php foreach($f['i']->get_categories() as $cat) : echo esc_html($cat->term.', '); endforeach; ?></li>
						</ul>
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
		'/mini/toolkit/'	=> 'https://www.dropbox.com/sh/ykbi3al0j4hatd2/hSN6Z9nXTU',
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


/**
 * Hide Maker Faire applications from past faires
 *
 * In the past, CS had a method for only selecting the current
 * faire for applications. We want to do the same here, and prevent
 * all applications from showing up in the edit screen.
 *
 * Have to use slug, RE: See http://core.trac.wordpress.org/ticket/13258
 *
 * @global $query
 *
 */
function mf_hide_faires( $query ) {
	if ( is_admin() && $query->is_main_query() ) {
		$tax_query = array(
			array(
				'taxonomy'	=> 'faire',
				'field'		=> 'slug',
				'terms'		=> MF_CURRENT_FAIRE,
				'operator'	=> 'IN',
			)
		);
		$query->set( 'tax_query', $tax_query );
	}
}

// add_action( 'pre_get_posts', 'mf_hide_faires' );



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
	$base 		= 'https://api.hipchat.com/v1/rooms/message';
	$auth_token = '9f4f9113e8eeb3754da520d295ca59';
	$room 		= 198932;
	$notify 	= 1;

	$opts = array(
		'auth_token'=> $auth_token,
		'room_id'	=> $room,
		'from' 		=> $from,
		'notify' 	=> $notify,
		'message'	=> urlencode( $message ),
		'color'		=> 'green'
	);

	$url = add_query_arg( $opts, $base );
	$json = wpcom_vip_file_get_contents( $url );
}

// Redirect mobile users on iOS or Android to their app stores if set.
function mf_page_redirect_to_app_stores() {
	if ( ! is_page( 'app' ) && function_exists( 'jetpack_is_mobile' ) )
		return;

	$redirect_to = '';

 	if ( Jetpack_User_Agent_Info::is_iphone_or_ipod() )
		$redirect_to = 'https://itunes.apple.com/us/app/maker-faire-the-official-app/id641794889';
	elseif ( Jetpack_User_Agent_Info::is_android() )
		$redirect_to = 'https://play.google.com/store/apps/details?id=com.xomodigital.makerfaire';

	if ( ! empty( $redirect_to ) ) {
		wp_redirect( $redirect_to, 301 );  // Permanent redirect
		exit;
	}
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
<?php }

/**
 * Adds footer copyright information
 */
function make_copyright_footer() { ?>
	<div class="row">
		<div class="span12">
			<p class="footer_copyright text-center"><a href="http://makezine.com/">Make:</a> and <a href="http://makerfaire.com/">Maker Faire</a> are registered trademarks of <a href="http://makermedia.com/">Maker Media, Inc.</a><br>
			Copyright &copy; 2004-<?php echo date("Y") ?> Maker Media, Inc.  All rights reserved</p>
			<?php if ( function_exists('vip_powered_wpcom') ) { echo vip_powered_wpcom(4); } ?>
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
