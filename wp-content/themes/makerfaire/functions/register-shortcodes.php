<?php

/**
 * makerfaire carousel
 * @param type $atts
 * @return string array()
 */
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


/**
 * meet the makers
 */
function makerfaire_meet_the_makers_shortcode($atts, $content = null) {
  global $wpdb;
  extract( shortcode_atts( array('faire'   => ''), $atts ) );
  $faireArr = explode(",", $faire);
  $formIDarr=array();
  foreach($faireArr as $fairelp){
    //pull form id's for selected faire(s)
    $sql = "select form_ids from wp_mf_faire where faire like '%".$fairelp."%'";
    $results = $wpdb->get_results($sql);
    foreach($results as $result){
     $formIDarr =  array_merge($formIDarr,explode(",", $result->form_ids));
    }
  }
  //MF-918 change to have this auto pull instead of having to set the entry id and description
  $search_criteria['field_filters'][] = array( 'key' => '304', 'value' => 'Featured Maker' );
  $search_criteria['field_filters'][] = array( 'key' => '303', 'value' => 'Accepted' );
  $search_criteria['field_filters']['mode'] = 'all';

  $result    = GFAPI::count_entries( $formIDarr, $search_criteria );

  $offset= rand(0,$result); //randomly choose where to pick 3 entries from
  $entries   = GFAPI::get_entries( $formIDarr, $search_criteria, null, array('offset' => $offset, 'page_size' => 3));

$output = '<div class="row filter-container mmakers">'
          . ' <div class="col-xs-12 col-sm-8"><a href="/maker/entry/' . $entries[0]['id'] . '" class="post">'
          . '   <img class="img-responsive" src="' . legacy_get_resized_remote_image_url($entries[0]['22'],622,402) . '" alt="Featured Maker 1">'
          . '   <div class="text-box"><span class="section">' . $entries[0]['16'] . '</span></div></a>'
          . ' </div><div class="col-xs-12 col-sm-4">'
          . '   <a href="/maker/entry/' . $entries[1]['id'] . '" class="post">'
          . '     <img class="img-responsive" src="' . legacy_get_resized_remote_image_url($entries[1]['22'],622,402) . '" alt="Featured Maker 2">'
          . '     <div class="text-box"><span class="section">' . substr($entries[1]['151'],0,48) . '</span></div>'
          . '   </a>'
          . '   <a href="/maker/entry/' . $entries[2]['id'] . '" class="post">'
          . '     <img class="img-responsive" src="' . legacy_get_resized_remote_image_url($entries[2]['22'],622,402) . '" alt="Featured Maker 3">'
          . '     <div class="text-box"><span class="section">' . substr($entries[2]['151'],0,48) . '</span></div>'
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

/*
 * add new shortcode to generate a export entries look
 */

function  createExportLink($atts){
  extract( shortcode_atts( array(
    'formid'  => '',
    'title'   => ''
  ), $atts ) );
  $link = '';
  if($formid != ''){
    //create a crypt key to pass to entriesExport.php to avoid outside from accessing
    $date  = date('mdY');
    $crypt = crypt($date, AUTH_SALT);
    $forms = RGFormsModel::get_forms( null, 'title' );
    $form = GFAPI::get_form($formid);
    $link = '<a href="/wp-content/themes/makerfaire/devScripts/entriesExport.php?formID='. absint( $formid ).'&auth='.$crypt.'">Export Entries</a>';
  }
  return $link;
}

add_shortcode( 'mfExportLink', 'createExportLink' );