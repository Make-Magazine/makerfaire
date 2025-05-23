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
     $formIDarr = array_merge($formIDarr,explode(",", $result->form_ids));
    }
  }
  //MF-918 change to have this auto pull instead of having to set the entry id and description
  $search_criteria['field_filters'][] = array( 'key' => '304', 'value' => 'Featured Maker' );
  $search_criteria['field_filters'][] = array( 'key' => '303', 'value' => 'Accepted' );
  $search_criteria['field_filters']['mode'] = 'all';

  $result = GFAPI::count_entries( $formIDarr, $search_criteria );

  $offset = rand(0,$result-3); //randomly choose where to pick 3 entries from (starting from 3 to the total number of entries - 3
  $entries = GFAPI::get_entries( $formIDarr, $search_criteria, null, array('offset' => $offset, 'page_size' => 3));

  $image1 = ($entries[0]['22']!=''?$entries[0]['22']:'/wp-content/themes/makerfaire/images/grey-makey.png');
  $image2 = ($entries[1]['22']!=''?$entries[1]['22']:'/wp-content/themes/makerfaire/images/grey-makey.png');
  $image3 = ($entries[2]['22']!=''?$entries[2]['22']:'/wp-content/themes/makerfaire/images/grey-makey.png');

  $description0 = $entries[0]['16'];
  $desc_length0 = iconv_strlen($description0, 'UTF-8');
  if ($desc_length0 > 200) {
    $description0 = substr($description0, 0, 200) . '...';
  }
  $description1 = $entries[1]['16'];
  $desc_length1 = iconv_strlen($description1, 'UTF-8');
  if ($desc_length1 > 200) {
    $description1 = substr($description1, 0, 200) . '...';
  }
  $description2 = $entries[2]['16'];
  $desc_length2 = iconv_strlen($description2, 'UTF-8');
  if ($desc_length2 > 200) {
    $description2 = substr($description2, 0, 200) . '...';
  }

  $output = '<div class="mtm-cont">'
          . ''
          . '   <a class="mtm-big-img" href="/maker/entry/' . $entries[0]['id'] . '" style="background: url(' . legacy_get_resized_remote_image_url($image1,622,402) . ');">'
          . '     <div class="mtm-text-box">'
          . '       <h2>' . substr($entries[0]['151'],0,48) . '</h2>'
          . '       <p>' . $description0 . '</p>'
          . '     </div>'
          . '   </a>'
          . ' <div class="mtm-sm-img">'
          . '   <a href="/maker/entry/' . $entries[1]['id'] . '" style="background: url(' . legacy_get_resized_remote_image_url($image2,622,402) . ');">'
          . '     <div class="mtm-text-box mtm-text-box-sm">'
          . '       <h2>' . substr($entries[1]['151'],0,48) . '</h2>'
          . '       <p>' . $description1 . '</p>'
          . '     </div>'
          . '   </a>'
          . '   <a href="/maker/entry/' . $entries[2]['id'] . '" style="background: url(' . legacy_get_resized_remote_image_url($image3,622,402) . ');">'
          . '     <div class="mtm-text-box mtm-text-box-sm">'
          . '       <h2>' . substr($entries[2]['151'],0,48) . '</h2>'
          . '       <p>' . $description2 . '</p>'
          . '     </div>'
          . '   </a>'
          . ' </div>'
          . '</div>';

  return $output;
}

add_shortcode( 'mmakers', 'makerfaire_meet_the_makers_shortcode' );


/** DELETE THIS (find on old pages first)
 * 3 arbitrarily tagged posts from Makezine - for homepage, but also available as generic shortcode
 */

function makerfaire_makezine_rss_news($atts) {   
   return '';
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
		  if (typeof googletag === "undefined" || googletag === null) {
          	googletag.pubads().refresh();
          	ga(\'send\', \'pageview\');
		  }
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

/*
 * Used to create the sponsor slideshow
 */
function createSponsSlide($atts) {
  extract( shortcode_atts( array(
    'faire_id'  => '',
    'url'   => ''
  ), $atts ) );

  //if($faire_id=='')  // required field - quit there was an error
  //if($url=='')    // required field - quit there was an error

  $faireData = get_faire_by_shortid($faire_id);
  $postid  = url_to_postid( $url );

  $sponsorTypes = array(
      array('type'=>'goldsmith_sponsors', 'name'=>'Goldsmith Sponsors'),
      array('type'=>'silversmith_sponsors', 'name'=>'Silversmith Sponsors'),
      array('type'=>'coppersmith_sponsors', 'name'=>'Coppersmith Sponsors'),
      array('type'=>'media_sponsors', 'name'=>'Media and Community Sponsors'),
  );
  $return = '';
  //IF CUSTOM FIELD FOR SPONSOR SLIDER HAS A URL THEN SHOW THAT URL'S SPONSORS
  if( have_rows('goldsmith_sponsors', $postid) || have_rows('silversmith_sponsors', $postid) || have_rows('coppersmith_sponsors', $postid) || have_rows('media_sponsors', $postid) ) {
    $return .= '  <div class="sponsor-slide sponsor-slide-shortcode">
                    <div class="row">
                      <div class="col-sm-7">
                        <h4 class="sponsor-slide-title">'. $faireData['faire_name'].' Sponsors: <span class="sponsor-slide-cat"></span></h4>
                      </div>
                      <div class="col-sm-5">
                        <h5><a href="/sponsors">Become a sponsor</a></h5>
                        <h5><a href="' . $url .'">All sponsors</a></h5>
                      </div>
                    </div> <!-- end .row -->
                    <div class="row">
                      <div class="col-xs-12">
                        <div id="carousel-sponsors-slider" class="carousel slide" data-ride="carousel">
                          <!-- Wrapper for slides -->
                          <div class="carousel-inner" role="listbox">';
                          foreach($sponsorTypes as $sponsor) {
                            if( have_rows($sponsor['type'], $postid) ){
                              $return.= '<div class="item">
                                  <div class="row sponsors-row">
                                    <div class="col-xs-12">
                                      <h3 class="sponsors-type text-center">'.$sponsor['name'].'</h3>
                                      <div class="faire-sponsors-box">';
                                        while( have_rows($sponsor['type'], $postid) ) {
                                          the_row();
                                          $sub_field_1 = get_sub_field('image'); //Photo
                                          $sub_field_2 = get_sub_field('url'); //URL

                                          $return.=  '<div class="sponsors-box-md">';
                                          if( get_sub_field('url') ) {
                                            $return.=  '<a href="' . $sub_field_2 . '" target="_blank">';
                                          }
                                          $return.=  '<img src="' . $sub_field_1 . '" alt="Maker Faire sponsor logo" class="img-responsive" />';
                                          if( get_sub_field('url') ) {
                                            $return.=  '</a>';
                                          }
                                          $return.=  '</div> <!-- end .sponsors-box-md-->';
                                        }
                                    $return.= '</div> <!-- end faire-sponsors-box-->
                                    </div> <!-- end col-xs-12 -->
                                  </div> <!-- end sponsors-row -->
                                </div> <!-- end item-->';
                            }
                          }


              $return.= '
            </div> <!-- end .carousel-inner-->
          </div> <!-- end #carousel-sponsors-slider -->
        </div> <!-- end .col-xs-12 -->
      </div> <!-- end .row -->
  </div> <!-- end .sponsor-slide -->';

  $return.= "<script>
    // Update the sponsor slide title each time the slide changes
    jQuery('.carousel-inner .item:first-child').addClass('active');
    jQuery(function() {
      var title = jQuery('.item.active .sponsors-type').html();
      jQuery('.sponsor-slide-cat').text(title);
      jQuery('#carousel-sponsors-slider').on('slid.bs.carousel', function () {
        var title = jQuery('.item.active .sponsors-type').html();
        jQuery('.sponsor-slide-cat').text(title);
      })
    });
    </script>";
  }
  return $return;
}
add_shortcode('sponsor_slideshow', 'createSponsSlide');

/**
 * * Adds a campaign monitor form to the post content
 * @param  Array  $atts    The array of attributes passed through the shortcode
 * @param  String $content The string of content wrapped inside the shortcode
 * @param  Shortcode attributes:
 *         url           Insert the full URL as per the Campaign Monitor source code (IE http://newsletter.makezine.com/t/r/s/jkdduth/)
 *         class         Insert any classes you wish to enter. Separate each class with spaces. EG button btn-primary
 *         id            Insert any ID you want to use. By default this is set to 'subForm'.
 *         title         Want to display a title? Enter one then! :)
 *         name_id       This field will allow you to customize the ID field in the name and label tags for the name fields. Defaults to 'name'
 *         email_id      This field will allow you to customize the ID field in the name and label tags for the email fields.
 *         name_class    Sometime we want to add classes to the input fields. Use this attribute
 *         email_class   As with the name_class above, apply custom classes to the email input field.
 *         name          You can customize the default "Name" text in label with this.
 *         email         You can customize the default "Email" text in label with this.
 *         submit_class  Add a custom class to the submit button
 *         submit        Change the default text of the submit button
 *
 * 		   EG of all fields in use [make-compagin-monitor url="http://newsletter.makezine.com/t/r/s/jkdduth/" class="my-form-class" id="my-form-id" title="My CM Title" name_id="name" email_id="jkdduth-jkdduth" name_class="input-class" email_class="input-class" name="Your Name" email="Your Email" submit_class="btn btn-primary" submit="Submit Your Application"]
 * @return String
 */
function make_campaign_monitor_form( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'url' 		   => '',
		'class' 	   => '',
		'id'		   => 'subForm',
		'title'        => '',
		'name_id'	   => 'name',
		'email_id'	   => '',
		'name_class'   => '',
		'email_class'  => '',
		'name'		   => 'Name',
		'email'		   => 'Email',
		'submit_class' => '',
		'submit'       => 'Subscribe',
	), $atts ) );
	$output = '';
	if ( ! empty( $class ) ) {
		$output .= '<form action="' . esc_url( $url ) . '" method="post" id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '">';
	} else {
		$output .= '<form action="' . esc_url( $url ) . '" method="post" id="' . esc_attr( $id ) . '">';
	}
		$output .=	'<fieldset>';

			// Add a title to the CM form
			if ( ! empty( $title ) )
				$output .= '<legend>' . esc_attr( $title ) . '</legend>';

			// Start our Name label and input fields
			$output .= '<div class="clearfix">
				<label for="' . esc_attr( $name_id ) . '">' . esc_attr( $name ) . ':</label>
				<div class="input">
					<input type="text" name="cm-' . esc_attr( $name_id ) . '" id="' . esc_attr( $name_id ) . '"';

					// Add a class to the name field if needed
					if ( ! empty( $name_class ) )
						$output .= ' ' . esc_attr( $name_class );

					$output .= ' />';
				$output .= '</div>
			</div>';

			// Start our Email label and input fields
			$output .= '<div class="clearfix">
				<label for="' . esc_attr( $email_id ) . '">' . esc_attr( $email ) . ':</label>
				<div class="input">
					<input type="text" name="cm-' . esc_attr( $email_id ) . '" id="' . esc_attr( $email_id ) . '"';

					// Add a class to the email field if needed
					if ( ! empty( $email_class ) )
						$output .= ' ' . esc_attr( $email_class );

					$output .= ' />';
				$output .= '</div>
			</div>
		</fieldset>
		<div class="actions">
			<input type="submit" value="' . esc_attr( $submit ) . '" ';

				// Add a class to the submit field if needed
				if ( ! empty( $submit_class ) )
					$output .= 'class="' . esc_attr( $submit_class ) . '" ';

			$output .= '/>
		</div>
	</form>';

	return $output;
}
add_shortcode( 'make-compagin-monitor', 'make_campaign_monitor_form' );