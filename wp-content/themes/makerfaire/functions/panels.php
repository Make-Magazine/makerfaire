<?php

/* * *************************************************** */
/* Determine correct layout                             */
/* * *************************************************** */

function dispLayout($row_layout) {
    $return = '';
    GLOBAL $acf_blocks;
    $activeinactive = ($acf_blocks ? get_field('activeinactive') : get_sub_field('activeinactive'));

    if ($activeinactive == 'Active') {
        switch ($row_layout) {
            case 'buy_tickets_float': //floating buy tickets banner
                $return = getBuyTixPanel($row_layout);
                break;
            case 'featured_makers_panel':                // FEATURED MAKERS (SQUARE)
            case 'featured_makers_panel_dynamic':        // FEATURED MAKERS (SQUARE) - dynamic
                $return = getFeatMkPanel($row_layout);
                break;
            case 'three_column':
            case '3_column': // 3 COLUMN LAYOUT
                $return = get3ColLayout();
                break;
            case 'six_column':
            case '6_column': // 6 column navigation panel
                $return = get6ColLayout();
                break;
            case '1_column_wysiwyg': // 1 column wysiwyg
                $return = get1ColWYSIWYG();
                break;
            case '2_column_wysiwyg': // 1 column wysiwyg
                $return = get2ColWYSIWYG();
                break;
            case 'one_column':
            case '1_column': // 1 COLUMN LAYOUT
                $return = get1ColLayout();
                break;
            case 'call_to_action_panel':  // CTA PANEL
            case 'call_to_action':  // CTA PANEL
                $return = getCTApanel();
                break;
            case 'tint_social_block_panel':  // NEWS BLOCK PANEL
                $return = getTintSocialBlockpanel();
                break;
            case 'ribbon_separator_panel':  // CTA PANEL
                $return = getRibbonSeparatorpanel();
                break;
            case 'static_or_carousel': // IMAGE CAROUSEL (RECTANGLE)
                $return = getImgCarousel();
                break;
            case 'square_image_carousel': // IMAGE CAROUSEL (SQUARE)
                $return = getImgCarouselSquare();
                break;
			case 'what_is_maker_faire': // WHAT IS MAKER FAIRE PANEL
				$return = getWhatisMF();
				break;
            case 'newsletter_panel':  // NEWSLETTER PANEL
                $return = getNewsletterPanel();
                break;
            case 'sponsors_panel':   // SPONSOR PANEL
                $return = getSponsorPanel();
                break;
            case 'social_media': //social media panel
                $return = getSocialPanel();
                break;
            case 'flag_banner_panel': //flag banner separator
                $return = getFlagBannerPanel();
                break;
            case 'two_column_video':
            case '2_column_video': // Video Panels
                $return = getVideoPanel();
                break;
            case 'two_column_image':
            case '2_column_images': // Image Panels in the same style as the Video Panels
                $return = getImagePanel();
                break;
            case 'makey_banner': // faire map link separator
                $return = getMakeyBanner();
                break;
            case 'image_slider': // this is gonna end up pretty similar to the image carousel, but we're going to have it as a panel
                $return = getSliderPanel();
                break;
			case 'rss_feed': // pull the rss feed shortcode with user inputs
				$return = getRSSFeed();
				break;
			case 'faire_list': // return display of the list of faires
			    $return = getFaireList();
			    break;
			case 'cfm_list': // return display of the list of call for maker forms
			    $return = getCFMList();
			    break;
        }
    }
    return $return;
}

/* * *********************************************** */
/*   Function to build the featured maker panel   */
/* * *********************************************** */

function getFeatMkPanel($row_layout) {
    GLOBAL $acf_blocks;
    $return = '';

    $dynamic = ($row_layout == 'featured_makers_panel_dynamic' ? true : false);

    $makers_to_show = ($acf_blocks ? get_field('makers_to_show') : get_sub_field('makers_to_show'));
    $more_makers_button = ($acf_blocks ? get_field('more_makers_button') : get_sub_field('more_makers_button'));
    $background_color = ($acf_blocks ? get_field('background_color') : get_sub_field('background_color'));
    $title = ($acf_blocks ? get_field('title') : (get_sub_field('title') ? get_sub_field('title') : ''));

    //var_dump($background_color);
    // Check if the background color selected was white
    $return .= '<section class="featured-maker-panel ' . $background_color . '"> ';

    $return .= '  <div class="panel-title title-w-border-y ' . ($background_color === "white-bg" ? ' yellow-underline' : '') . '">
                   <h2>' . $title . '</h2>
                 </div>';

    //build makers array
    $makerArr = array();
    if ($dynamic) {
        $formid = (int) ($acf_blocks ? get_field('enter_formid_here') : get_sub_field('enter_formid_here'));

        $search_criteria['status'] = 'active';
        $search_criteria['field_filters'][] = array('key' => '303', 'value' => 'Accepted');
        $search_criteria['field_filters'][] = array('key' => '304', 'value' => 'Featured Maker');

        $entries = GFAPI::get_entries($formid, $search_criteria, null, array('offset' => 0, 'page_size' => 999));

        //randomly order entries
        shuffle($entries);
        foreach ($entries as $entry) {
            $url = $entry['22'];

            $overrideImg = findOverride($entry['id'], 'makerPanel');
            if ($overrideImg != '')
                $url = $overrideImg;
            $makerArr[] = array('image' => $url,
                'name' => $entry['151'],
                'desc' => $entry['16'],
                'maker_url' => '/maker/entry/' . $entry['id']
            );
        }
    } else {
        // check if the nested repeater field has rows of data
        if (have_rows('featured_makers')) {
            // loop through the rows of data
            while (have_rows('featured_makers')) {
                the_row();
                $url = get_sub_field('maker_image')['url'];
                $makerArr[] = array('image' => $url,
                    'name' => get_sub_field('maker_name'),
                    'desc' => get_sub_field('maker_short_description'),
                    'maker_url' => get_sub_field('more_info_url')
                );
            }
        }
    }

    //limit the number returned to $makers_to_show
    $makerArr = array_slice($makerArr, 0, $makers_to_show);

    $return .= '<div id="performers" class="featured-image-grid">';

    //loop thru maker data and build the table
    foreach ($makerArr as $maker) {
        // var_dump($maker);
        // echo '<br />';
        $return .= '<div class="grid-item lazyload" data-bg="' . $maker['image'] . '">';

        if (!empty($maker['desc'])) {
            $markup = !empty($maker['maker_url']) ? 'a' : 'div';
            $href = !empty($maker['maker_url']) ? 'href="' . $maker['maker_url'] . '"' : '';
            $return .= '<' . $markup . ' ' . $href . ' class="grid-item-desc">
                     <div class="desc-body"><h4>' . $maker['name'] . '</h4>
                     <p class="desc">' . $maker['desc'] . '</p></div>';
            if (!empty($maker['maker_url'])) {
                $return .= '  <p class="btn btn-blue read-more-link">Learn More</p>'; //<a href="' . $maker['maker_url'] . '"></a>
            }
            $return .= ' </' . $markup . '>';
        }
        // the caption section
        $return .= '  <div class="grid-item-title-block hidden-sm hidden-xs">
		                 <h3>' . $maker['name'] . '</h3>
                    </div>';
        $return .= '</div>'; //close .grid-item
    }
    $return .= '</div>';  //close #performers
    //check if we should display a more maker button
    $cta_url = get_sub_field('cta_url');
    if ($cta_url) {
        $cta_text = (get_sub_field('cta_text') !== '' ? get_sub_field('cta_text') : 'More Makers');
        $return .= '<div class="row">
            <div class="col-xs-12 text-center">
              <a class="btn btn-outlined more-makers-link" href="' . $cta_url . '">' . $cta_text . '</a>
            </div>
          </div>';
    }
    $return .= '</section>';
    $return .= '<script type="text/javascript">
						function fitTextToBox(){
							jQuery(".grid-item").each(function() {
							    var availableHeight = jQuery(this).innerHeight() - 30;
								 if(jQuery(this).find(".read-more-link").length > 0){
									 availableHeight = availableHeight - jQuery(this).find(".read-more-link").innerHeight() - 30;
								 }

								 jQuery(jQuery(this).find(".desc-body")).css("mask-image", "-webkit-linear-gradient(top, rgba(0,0,0,1) 80%, rgba(0,0,0,0) 100%)");

								 if( 561 > jQuery(window).width() ) {
								   jQuery(jQuery(this).find(".desc-body")).css("mask-image", "none");
									jQuery(jQuery(this).find(".desc-body")).css("height", "auto");
								 } else {
								 	jQuery(jQuery(this).find(".desc-body")).css("height", availableHeight);
								 }
							 });
						}
	                jQuery(document).ready(function(){
						    fitTextToBox();
						 });
						 jQuery(window).resize(function(){
						 	 fitTextToBox();
						 });
					</script>';
    return $return;
}

/* * *********************************************** */
/*   Function to build the featured event panel      */
/* * *********************************************** */

function getFeatEvPanel($row_layout) {
    global $wpdb;
    $return = '';
    $dynamic = ($row_layout == 'featured_events_dynamic' ? true : false);
    $return .= '<section class="featured-events-panel">
          <div class="container">';
    if (get_sub_field('title')) {
        $return .= '<div class="row padtop text-center">
            <div class="title-w-border-r">
              <h2>' . get_sub_field('title') . '</h2>
            </div>
          </div>';
    }

    //gutenburg blocks use get_field, ACF panels use get_sub_field
    GLOBAL $acf_blocks;
    $title = ($acf_blocks ? get_field('title') : get_sub_field('title'));

    if ($title) {
        $return .= '<div class="row padtop text-center">
            <div class="title-w-border-r">
              <h2>' . $title . '</h2>
            </div>
          </div>';
    }

    $return .= '<div class="row padbottom">';

    //build event array
    $eventArr = array();
    if ($dynamic) {
        $formid = ($acf_blocks ? get_field('enter_formid_here') : get_sub_field('enter_formid_here'));
        $query = "SELECT schedule.entry_id, schedule.start_dt as time_start, schedule.end_dt as time_end, schedule.type,
                       lead_detail.value as entry_status, DAYNAME(schedule.start_dt) as day,location.location,
                       (SELECT value FROM {$wpdb->prefix}rg_lead_detail WHERE lead_id = schedule.entry_id AND field_number like '304.3'
                           AND value like 'Featured Maker')  as flag,
                       (SELECT value FROM {$wpdb->prefix}rg_lead_detail WHERE lead_id = schedule.entry_id AND field_number like '22')  as photo,
                       (SELECT value FROM {$wpdb->prefix}rg_lead_detail WHERE lead_id = schedule.entry_id AND field_number like '151') as name,
                       (SELECT value FROM {$wpdb->prefix}rg_lead_detail WHERE lead_id = schedule.entry_id AND field_number like '16')  as short_desc
                  FROM {$wpdb->prefix}mf_schedule as schedule
                       left outer join {$wpdb->prefix}mf_location as location on location_id = location.id
                       left outer join {$wpdb->prefix}rg_lead as lead on schedule.entry_id = lead.id
                       left outer join {$wpdb->prefix}rg_lead_detail as lead_detail on
                       schedule.entry_id = lead_detail.lead_id AND field_number = 303
                 WHERE lead.status = 'active' AND lead_detail.value='Accepted'";

        foreach ($wpdb->get_results($query) as $row) {
            //only write schedule for featured events
            if ($row->flag != NULL) {
                $startDate = date_create($row->time_start);
                $startDate = date_format($startDate, 'g:i a');

                $endDate = date_create($row->time_end);
                $endDate = date_format($endDate, 'g:i a');

                $projPhoto = $row->photo;
                $args = array(
                    'resize' => '300,300',
                    'quality' => '80',
                    'strip' => 'all',
                );
                $photon = jetpack_photon_url($projPhoto, $args);
                $eventArr[] = array(
                    'image' => $photon,
                    'event' => $row->name,
                    'description' => $row->short_desc,
                    'day' => $row->day,
                    'time' => $startDate . ' - ' . $endDate,
                    'location' => $row->location,
                    'maker_url' => '/maker/entry/' . $row->entry_id
                );
            }
        }
    } else {
        // check if the nested repeater field has rows of data
        if (have_rows('featured_events')) {
            // loop through the rows of data
            while (have_rows('featured_events')) {
                the_row();
                $url = get_sub_field('event_image');
                $args = array(
                    'resize' => '300,300',
                    'quality' => '80',
                    'strip' => 'all',
                );
                $photon = jetpack_photon_url($url['url'], $args);
                $eventArr[] = array(
                    'image' => $photon,
                    'event' => get_sub_field('event_name'),
                    'description' => get_sub_field('event_short_description'),
                    'day' => get_sub_field('day'),
                    'time' => get_sub_field('time'),
                    'location' => get_sub_field('location'),
                    'maker_url' => ''
                );
            }
        }
    }

    //build event display
    foreach ($eventArr as $event) {
        $return .= '<div class="featured-event col-xs-6">' .
                ($event['maker_url'] != '' ? '<a href="' . $event['maker_url'] . '">' : '') .
                '<div class="col-xs-12 col-sm-4 nopad">
              <div class="event-img lazyload" data-bg="' . $event['image'] . '"></div>
            </div>
            <div class="col-xs-12 col-sm-8">
              <div class="event-description">
                <h4>' . $event['event'] . '</h4>
                <p class="event-desc">' . $event['description'] . '</p>
              </div>
              <div class="event-details">
                <p class="event-day">' . $event['day'] . ' ' . $event['time'] . '</p>
                <p class="event-location">' . $event['location'] . '</p>
              </div>
            </div>' .
                ($event['maker_url'] != '' ? '</a>' : '') .
                '</div>';
    }

    $return .= '</div>'; //end div.row

    $all_events_button = ($acf_blocks ? get_field('all_events_button') : get_sub_field('all_events_button'));
    if ($all_events_button) {
        $return .= '<div class="row padbottom">
            <div class="col-xs-12 padbottom text-center">
              <a class="btn btn-b-ghost" href="' . $all_events_button . '">All Events</a>
            </div>
          </div>';
    }

    $return .= '</div>'; //end div.container
    $return .= '</section>';
    return $return;
}

/* * *************************************************** */
/*  Function to return 3_column_photo_and_text_panel     */
/* * *************************************************** */

function get3ColLayout() {
    $return = '';

    $return .= '<section class="content-panel three-column">
                <div class="container">';

    //gutenburg blocks use get_field, ACF panels use get_sub_field
    GLOBAL $acf_blocks;
    $panelTitle = ($acf_blocks ? get_field('panel_title') : get_sub_field('panel_title'));

    if ($panelTitle) {
        $return .= ' <div class="row">
                    <div class="col-xs-12 text-center padbottom">
                      <h2 class="panel-title yellow-underline">' . $panelTitle . '</h2>
                    </div>
                  </div>';
    }

    $return .= '   <div class="row">'; //start row
    //get requested data for each column
    $columns = ($acf_blocks ? get_field('column') : get_sub_field('column'));
    foreach ($columns as $column) {
        $return .= '   <div class="col-sm-4">'; //start column
        $data = $column['data'];
        $columnInfo = '';
        switch ($column['column_type']) {
            case 'image':     // Image with optional link
                $alignment = $data['column_list_alignment'];
                $imageArr = $data['column_image_field'];
                $image = '<img alt="' . $imageArr['alt'] . '" class="img-responsive lazyload" src="' . $imageArr['url'] . '" />';

                $cta_link = $data['image_cta'];
                $ctaText = $data['image_cta_text'];

                if (!empty($cta_link)) {
                    $columnInfo = '<a href="' . $cta_link . '">' . $image . '</a>';
                    if (!empty($ctaText)) {
                        $columnInfo .= '<p class="text-' . $alignment . ' sub-caption-dark"><a href="' . $cta_link . '" target="_blank">' . $ctaText . '</a></p>';
                    }
                } else {
                    $columnInfo = $image;
                }
                break;
            case 'paragraph': // Paragraph text
                $columnInfo = '<p>' . $data['column_paragraph'] . '</p>';
                break;
            case 'list':      // List of items with optional links
                $columnInfo = '<div class="flagship-faire-wrp">';
                if (!empty($data['list_title'])) {
                    $columnInfo .= '<p class="line-item list-title">' . $data['list_title'] . '</p>';
                }
                $columnInfo .= '  <ul>';
                foreach ($data['column_list_fields'] as $list_fields) {
                    $list_text = $list_fields['list_text'];
                    $list_link = $list_fields['list_link'];
                    $columnInfo .= '<li>' . (!empty($list_link) ? '<a class="" href="' . $list_link . '">' . $list_text . '</a>' : $list_text) . '</li>';
                }
                $columnInfo .= '  </ul>';
                $columnInfo .= '</div>';
                break;
        }
        $return .= $columnInfo;
        $return .= '</div>'; //end column
    }

    $return .= '</div>'; //end row

    $return .= ' </div>
              </section>'; // end div.container and section.content-panel
    return $return;
}

/* * *************************************************** */
/*  Function to return 6_column_photo_and_text_panel     */
/* * *************************************************** */

function get6ColLayout() {
    $return = '';

    GLOBAL $acf_blocks;
    $panelTitle = ($acf_blocks ? get_field('panel_title') : get_sub_field('panel_title'));
    $imageHeight = ($acf_blocks ? get_field('image_height') : get_sub_field('image_height'));
    $padding = ($acf_blocks ? get_field('padding') : get_sub_field('padding'));
    $linkPosition = ($acf_blocks ? get_field('link_position') : get_sub_field('link_position'));

    if ($padding == TRUE) {
        $return .= '<section class="content-panel six-column with-padding">';
    } else {
        $return .= '<section class="content-panel six-column">';
    }

    if ($panelTitle) {
        $return .= ' <div class="row">
                    <div class="col-xs-12 text-center padbottom">
                      <h2 class="panel-title yellow-underline">' . $panelTitle . '</h2>
                    </div>
                  </div>';
    }

    $return .= '   <div class="image-grid-row">'; //start row
    //get requested data for each column
    $columns = ($acf_blocks ? get_field('column') : get_sub_field('column'));
    foreach ($columns as $column) {
        $return .= '   <div class="image-grid-col" style="flex-direction:' . $linkPosition . ';">'; //start column
        $data = $column['data'];

        $imageArr = $data['column_image_field'];
        //var_dump($imageArr['alt']);

        $columnInfo = '';
        //$image = '<img height="" width="" alt="'.$imageArr['alt'].'" class="ximg-responsive" src="' . $imageArr['url'] . '" />';
        //echo $imageArr['url'];

        $imgStyle = 'style="background-image:url(' . (isset($imageArr['url'])?$imageArr['url']:'') . '); height:' . $imageHeight . ';"';

        $cta_link = $data['image_cta'];
        $ctaText = $data['image_cta_text'];

        $target = "_self";
        if (isset($data['open_link_in_new_tab']) && $data['open_link_in_new_tab'] == "true") {
            $target = "_blank";
        }

        $bgColor = $data['button_color'];

        if (!empty($cta_link)) {
            if (!empty($imageArr['url'])) {
                $columnInfo = '<a class="six-col-img lazyload" href="' . $cta_link . '" ' . $imgStyle . ' target="' . $target . '"></a>';
            }
            if (!empty($ctaText)) {
                $columnInfo .= '<h4 class="text-center sub-caption-bottom ' . $bgColor . '"><a href="' . $cta_link . '" target="' . $target . '">' . $ctaText . '</a></h4>';
            }
        } else {
            $columnInfo = '<div class="six-col-img" ' . $imgStyle . '></div>';
        }

        $return .= $columnInfo;
        $return .= '</div>'; //end column
    }

    $return .= '</div>'; //end row

    $return .= ' </section>'; // end div.container and section.content-panel
    return $return;
}

/* Function to return one column wysiwyg */

function get1ColWYSIWYG() {
    $return = '';
    $column_1 = get_sub_field('column_1');
    $cta_button = get_sub_field('cta_button');
    $cta_button_url = get_sub_field('cta_button_url');
    $return .= '<section class="content-panel single-block">
          <div class="container">';

    if (get_sub_field('title')) {
        $return .= '  <div class="row">
              <div class="col-xs-12 text-center padbottom">
                <h2 class="panel-title yellow-underline">' . get_sub_field('title') . '</h2>
              </div>
            </div>';
    }

    $return .= '    <div class="row">
              <div class="col-xs-12">' . $column_1 . '</div>
            </div>';

    if (get_sub_field('cta_button')) {
        $return .= '  <div class="row text-center padtop">
              <a class="btn btn-b-ghost" href="' . $cta_button_url . '">' . $cta_button . '</a>
            </div>';
    }

    $return .= '  </div>

        </section>';
    return $return;
}

/* Function to return two column wysiwyg */

function get2ColWYSIWYG() {
    $return = '<section class="content-panel double-block">
          		<div class="container">
					   <div class="row">';
    //get requested data for each column
    $column_rows = get_sub_field('columns');
    foreach ($column_rows as $column) {
        $return .= '<div class="col-md-6 sm-12">';
        if ($column['title']) {
            $return .= '<h2 class="panel-title yellow-underline text-center">' . $column['title'] . '</h2>';
        }
        $return .= '<div class="container-fluid">' . $column['column'] . '</div>';
        if ($column['cta_button']) {
            $return .= '<a class="btn btn-b-ghost" href="' . $column['cta_button_url'] . '">' . $column['cta_button'] . '</a>';
        }
        $return .= '</div>';
    }

    $return .= '  </div>
  					</div>
             </section>';
    return $return;
}

/* * *************************************************** */
/*   Function to return Hero panel */
/* * *************************************************** */

function get1ColLayout() {
    GLOBAL $acf_blocks;
    //get data submitted on admin page
    //loop thru and randomly select an image.
    $hero_array = array();
    if (have_rows('hero_image_repeater')) {
        // loop through the rows of data

        while (have_rows('hero_image_repeater')) {
            the_row();
            // TODO add the URL wrapper
            $hero_image_random = get_sub_field('hero_image_random');

            $hero_image_url = (isset($hero_image_random["url"])?$hero_image_random["url"]:'');

            $image = '<div class="hero-img lazyload" data-bg="' . $hero_image_url . '"></div>';
            $cta_link = get_sub_field('image_cta');

            if (!empty($cta_link)) {
                $columnInfo = '<a href="' . $cta_link . '">' . $image . '</a>';
            } else {
                $columnInfo = $image;
            }
            $hero_array[] = $columnInfo;
        }
        $randKey = array_rand($hero_array, 1);
        $hero_image = $hero_array[$randKey];
    }

    $hero_text      = ($acf_blocks ? get_field('column_title') : get_sub_field('column_title'));

    //build output
    $return = '';
    $return .= '<section class="hero-panel">';    // create content-panel section

    $return .= '   <div class="row">
                    <div class="col-xs-12">';
    if ($hero_text) {
        $return .= '<div class="top_left"><img src="/wp-content/themes/makerfaire/img/TopLeftCorner.png"></div>'
                . '<div class="panel_title">'
                . '   <div class="panel_text">' . $hero_text . '</div>'
                . '   <div class="bottom_right"><img src="/wp-content/themes/makerfaire/img/BottomRightCorner.png"></div>'
                . '</div>';
    }
    $return .= '        ' . $hero_image .
            '     </div>' .
            '   </div>';


    // Because of the aggressive caching on prod, it makes more sense to shuffle the array in javascript
    $return .= '</section><script type="text/javascript">var heroArray = ' . json_encode($hero_array) . ';heroArray.sort(function(a, b){return 0.5 - Math.random()});jQuery(document).ready(function(){jQuery(".hero-img").replaceWith(heroArray[0]);});</script>';
    // this was removed from above function, since the background hero is no longer an image but a background image
    return $return;
}

/* * *************************************************** */
/*   Function to return 2_column_video panel           */
/* * *************************************************** */

function getVideoPanel() {
    //get data submitted on admin page
    GLOBAL $acf_blocks;

    $return = '';
    $return .= '<section class="video-panel container-fluid">';    // create content-panel section
    //get requested data for each column
    $video_rows = ($acf_blocks ? get_field('video_row') : get_sub_field('video_row'));

    $videoRowNum = 0;
    foreach ($video_rows as $video) {
        $videoRowNum += 1;
        if ($videoRowNum % 2 != 0) {
            $return .= '<div class="row">';
            $return .= '  <div class="col-sm-4 col-xs-12">
			                <h4>' . $video['video_title'] . '</h4>
								 <p>' . $video['video_text'] . '</p>';
            if ($video['video_button_link']) {
                $return .= '  <a href="' . $video['video_button_link'] . '">' . $video['video_button_text'] . '</a>';
            }
            $return .= '  </div>';
            $return .= '  <div class="col-sm-8 col-xs-12">
			                 <div class="embed-youtube">
									 <iframe class="lazyload" src="https://www.youtube.com/embed/' . $video['video_code'] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
								  </div>
			              </div>';
            $return .= '</div>';
        } else {
            $return .= '<div class="row">';
            $return .= '  <div class="col-sm-8 col-xs-12">
								  <div class="embed-youtube">
									 <iframe class="lazyload" src="https://www.youtube.com/embed/' . $video['video_code'] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
								  </div>
							  </div>';
            $return .= '  <div class="col-sm-4 col-xs-12">
								 <h4>' . $video['video_title'] . '</h4>
								 <p>' . $video['video_text'] . '</p>';
            if ($video['video_button_link']) {
                $return .= '  <a href="' . $video['video_button_link'] . '">' . $video['video_button_text'] . '</a>';
            }
            $return .= '  </div>';
            $return .= '</div>';
        }
    }
    $return .= '</section>'; // end section/container
    return $return;
}

/* * *************************************************** */
/*   Function to return 2_column_image panel           */
/* * *************************************************** */

function getImagePanel() {
    //get data submitted on admin page
    GLOBAL $acf_blocks;

    $return = '';
    $return .= '<section class="image-panel container-fluid">';    // create content-panel section
    //get requested data for each column
    $image_rows = ($acf_blocks ? get_field('image_row') : get_sub_field('image_row'));

    $imageRowNum = 0;
    foreach ($image_rows as $image) {
        $imageRowNum += 1;
        $imageObj = $image['image'];

        if ($imageRowNum % 2 != 0) {
            $return .= '<div class="row ' . $image['background_color'] . '">';
            $return .= '  <div class="col-sm-4 col-xs-12">
                            <h4>' . $image['image_title'] . '</h4>
                            <p>' . $image['image_text'] . '</p>';
            if ($image['image_links']) {
                foreach ($image['image_links'] as $image_link) {
                    $return .= '  	    <a href="' . $image_link['image_link_url'] . '">' . $image_link['image_link_text'] . '</a>';
                }
            }
            $return .= '  </div>';
            $return .= '  <div class="col-sm-8 col-xs-12">
			                 <div class="image-display">';
            if (isset($image['image_overlay']['image_overlay_link'])) {
                $return .= ' 		  <a href="' . $image['image_overlay']['image_overlay_link'] . '">';
            }
            $return .= '			 <img class="img-responsive lazyload" src="' . $imageObj['url'] . '" alt="' . $imageObj['alt'] . '" />';
            if (isset($image['image_overlay']['image_overlay_text'])) {
                $return .= '  <div class="image-overlay-text">' . $image['image_overlay']['image_overlay_text'] . '</div>';
            }
            if (isset($image['image_overlay']['image_overlay_link'])) {
                $return .= '        </a>';
            }
            $return .= '		</div>
			              </div>';
            $return .= '</div>';
        } else {
            $return .= '<div class="row ' . $image['background_color'] . '">';
            $return .= '  <div class="col-sm-8 col-xs-12">
			                 <div class="image-display">';
            if (isset($image['image_overlay']['image_overlay_link'])) {
                $return .= ' 		  <a href="' . $image['image_overlay']['image_overlay_link'] . '">';
            }
            $return .= '			 <img class="img-responsive lazyload" src="' . $imageObj['url'] . '" alt="' . $imageObj['alt'] . '" />';
            if (isset($image['image_overlay']['image_overlay_text'])) {
                $return .= '  <div class="image-overlay-text">' . $image['image_overlay']['image_overlay_text'] . '</div>';
                ;
            }
            if (isset($image['image_overlay']['image_overlay_link'])) {
                $return .= '        </a>';
            }
            $return .= '  </div>';
            $return .= '</div>';
            $return .= '  <div class="col-sm-4 col-xs-12">
								 <h4>' . $image['image_title'] . '</h4>
								 <p>' . $image['image_text'] . '</p>';
            if (isset($image['image_links'])) {
                foreach ($image['image_links'] as $image_link) {
                    $return .= '  	    <a href="' . $image_link['image_link_url'] . '">' . $image_link['image_link_text'] . '</a>';
                }
            }
            $return .= '  </div>';
            $return .= '</div>';
        }
    }
    $return .= '</section>'; // end section/container
    return $return;
}

/* * *************************************************** */
/* Function to return Buy Tickets Floating Banner       */
/* * *************************************************** */

function getBuyTixPanel() {
    //gutenburg blocks use get_field, ACF panels use get_sub_field
    GLOBAL $acf_blocks;
    $buy_ticket_url = ($acf_blocks ? get_field('buy_ticket_url') : get_sub_field('buy_ticket_url'));
    $buy_ticket_text = ($acf_blocks ? get_field('buy_ticket_text') : get_sub_field('buy_ticket_text'));

    return '<a href="' . $buy_ticket_url . '" target="_blank"><div class="floatBuyTix">' . $buy_ticket_text . '</div></a>';
}

/* * *************************************************** */
/* Function to return Call to Action panel              */
/* * *************************************************** */

function getCTApanel() {
    GLOBAL $acf_blocks;

    $return = '';
    $cta_title = ($acf_blocks ? get_field('text') : get_sub_field('text'));
    $cta_url = ($acf_blocks ? get_field('url') : get_sub_field('url'));
    $background_color = ($acf_blocks ? get_field('background_color') : get_sub_field('background_color'));

    $bg_color_class_map = array(
        "Blue" => '',
        "Light Blue" => ' light-blue-ribbon',
        "Red" => ' red-ribbon',
        "Orange" => ' orange-ribbon',
        "Navy" => ' navy-ribbon',
        "Grey" => ' grey-ribbon'
    );
    $return .= '<a href="' . $cta_url . '">';
    $return .= '<section class="cta-panel' . $bg_color_class_map[$background_color] . '">';
    // $return .= '   <div class="arrow-left"></div>'
    //    . '   <div class="arrow-right"></div>';
    $return .= '   <div class="container">
                     <div class="row text-center">
                        <div class="col-xs-12">
                           <h3>
                              <span>' . $cta_title . '</span>
                           </h3>
                        </div>
                     </div>
                  </div>
               </section></a>';
    return $return;
}

/* * *************************************************** */
/* Function to return Ribbon Separator panel            */
/* * *************************************************** */

function getRibbonSeparatorpanel() {
    GLOBAL $acf_blocks;
    $return = '';
    $background_color = ($acf_blocks ? get_field('background_color') : get_sub_field('background_color'));
    $bg_color_class_map = array(
        "Blue" => '',
        "Light Blue" => ' light-blue-ribbon',
        "Red" => ' red-ribbon',
        "Orange" => ' orange-ribbon'
    );
    $return .= '<section class="ribbon-separator-panel' . $bg_color_class_map[$background_color] . '">';
    $return .= '   <div class="arrow-left-sm"></div>'
            . '   <div class="arrow-right-sm"></div>';
    $return .= '</section>';
    return $return;
}

/* * *************************************************** */
/* Function to return Tint Social Block panel           */
/* * *************************************************** */

function getTintSocialBlockpanel() {
    GLOBAL $acf_blocks;
    $args = [
        'personalization_id' => ($acf_blocks ? get_field('personalization_id') : get_sub_field('personalization_id')),
        'title' => ($acf_blocks ? get_field('title') : get_sub_field('title')),
        'hashtags' => ($acf_blocks ? get_field('hashtags') : get_sub_field('hashtags'))
    ];
    require_once 'MF-Social-Block.php';
    return do_social_block($args);
}

/* * *************************************************** */
/* Function to return IMAGE CAROUSEL (RECTANGLE)        */
/* * *************************************************** */

function getImgCarousel() {
    $return = '';
    // IMAGE CAROUSEL (RECTANGLE)
    GLOBAL $acf_blocks;
    $width = ($acf_blocks ? get_field('width') : get_sub_field('width'));

    // check if the nested repeater field has rows of data
    if (have_rows('images')) {

        $return .= '<section class="rectangle-image-carousel ';
        if ($width == 'Content Width') {
            $return .= 'container">';
        } else {
            $return .= 'full-width">';
        }
        $return .= '<div id="carouselPanel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner" role="listbox">';
        $i = 0;

        // loop through the rows of data
        while (have_rows('images')) {
            the_row();

            $text = ($acf_blocks ? get_field('text') : get_sub_field('text'));
            $url = ($acf_blocks ? get_field('url') : get_sub_field('url'));
            $image = get_sub_field('image');

            if ($i == 0) {
                $return .= '
        <div class="item active">';
                if ($url) {
                    $return .= '<a href="' . $url . '">';
                }
                $return .= '
            <img class="lazyload" src="' . $image['url'] . '" alt="' . $image['alt'] . '" />';
                if ($text) {
                    $return .= '
              <div class="carousel-caption">
                <h3>' . $text . '</h3>
              </div>';
                }
                if ($url) {
                    $return .= '</a>';
                }
                $return .= '
        </div>';
            } else {
                $return .= '<div class="item">
          <img class="lazyload" src="' . $image['url'] . '" alt="' . $image['alt'] . '" />';
		  if ($text) {
                    $return .= '
          <div class="carousel-caption">
            <h3>' . $text . '</h3>
		  </div>';
		  }
		  $return .= '
		  </div>';
            }
            $i++;
        }
        $return .= '</div> <!-- close carousel-inner-->';

        if ($i > 1) {
            $return .= '<a class="left carousel-control" href="#carouselPanel" role="button" data-slide="prev">
        <img class="glyphicon-chevron-right" src="' . get_bloginfo('template_directory') . '/img/arrow_left.png" alt="Image Carousel button left" />
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#carouselPanel" role="button" data-slide="next">
        <img class="glyphicon-chevron-right" src="' . get_bloginfo('template_directory') . '/img/arrow_right.png" alt="Image Carousel button right" />
        <span class="sr-only">Next</span>
      </a>';
        }
        $return .= '
          </div> <!-- close carouselPanel-->
        </section>';
    }
    return $return;
}

/* * *************************************************** */
/* Function to return IMAGE CAROUSEL (SQUARE)           */
/* * *************************************************** */

function getImgCarouselSquare() {
    $return = '';
    // IMAGE CAROUSEL (SQUARE)
    GLOBAL $acf_blocks;
    $width = ($acf_blocks ? get_field('width') : get_sub_field('width'));

    if (have_rows('images')) {
        $return .= '<section class="square-image-carousel ' . ($width == 'Content Width' ? 'container nopad' : '') . '">';
        $return .= '<div class="mtm-carousel owl-carousel">';
        while (have_rows('images')) {
            the_row();

            $text = get_sub_field('text');
            $url = get_sub_field('url');
            $image = get_sub_field('image');
            $return .= '<div class="mtm-car-image lazyload" data-bg="' . $image["url"] . '" style="background-repeat: no-repeat; background-position: center center;background-size: cover;"></div>';
        }
        $return .= '
    </div>

    <a id="left-trigger" class="left carousel-control" href="#" role="button" data-slide="prev">
      <img class="glyphicon-chevron-right" src="' . get_bloginfo('template_directory') . '/img/arrow_left.png" alt="Image Carousel button left" />
      <span class="sr-only">' . __('Previous', 'MiniMakerFaire') . '</span>
    </a>
    <a id="right-trigger" class="right carousel-control" href="#" role="button" data-slide="next">
      <img class="glyphicon-chevron-right" src="' . get_bloginfo('template_directory') . '/img/arrow_right.png" alt="Image Carousel button right" />
      <span class="sr-only">' . __('Next', 'MiniMakerFaire') . '</span>
    </a>
    </section>

    <script>
    jQuery( document ).ready(function() {
      // Carousel init
      jQuery(\'.square-image-carousel .mtm-carousel\').owlCarousel({
        center: true,
        autoWidth:true,
        items:2,
        loop:true,
        margin:0,
        nav:true,
        //navContainer:true,
        autoplay:true,
        autoplayHoverPause:true,
        responsive:{
          600:{
            items:3
          }
        }
      });
      // Carousel left right
      jQuery( ".square-image-carousel #right-trigger" ).click(function( event ) {
        event.preventDefault();
        jQuery( ".square-image-carousel .owl-next" ).click();
      });
      jQuery( ".square-image-carousel #left-trigger" ).click(function( event ) {
        event.preventDefault();
        jQuery( ".square-image-carousel .owl-prev" ).click();
      });
    });
    </script>';
    }
    return $return;
}

/* * *************************************************** */
/* Function to return slider panel                      */
/* * *************************************************** */

function getSliderPanel() {
    GLOBAL $acf_blocks;
    $background_color = ($acf_blocks ? get_field('background_color') : get_sub_field('background_color'));
    $text_position = ($acf_blocks ? get_field('text_position') : get_sub_field('text_position'));
    $slideshow_title = ($acf_blocks ? get_field('slideshow_title') : get_sub_field('slideshow_title'));
    $slideshow_name = ($acf_blocks ? get_field('slideshow_name') : get_sub_field('slideshow_name'));
    $column_number = ($acf_blocks ? get_field('column_number') : get_sub_field('column_number'));
    $slides = ($acf_blocks ? get_field('slide') : get_sub_field('slide'));

    $return = '';
    $return .= '<section class="slider-panel container-fluid ' . $background_color . ' position-' . $text_position . '">';
    if (!empty($slideshow_title)) {
        $return .= '<div class="slideshow-title"><h2>' . $slideshow_title . '</h2></div>';
    }
    $return .= '   <div class="' . $slideshow_name . '-carousel owl-carousel columns-' . $column_number . '">';
    //get requested data for each column
    foreach ($slides as $slide) {
        $imageObj = $slide['image'];
        if (empty($slide['slide_button_text']) && !empty($slide['slide_link'])) {
            $return .= '<a href="' . $slide['slide_link'] . '">';
        }
        $return .= '     <div class="item slide">
        		                   <div class="slide-image-section lazyload" data-bg="' . $imageObj['url'] . '">';
        if (!empty($slide['slide_title']) && get_sub_field("column_number") > 1) {
            $return .= '     <p class="slide-title">' . $slide['slide_title'] . '</p>';
        }
        if (!empty($slide['slide_button_text']) && get_sub_field("column_number") > 1) {
            if (!empty($slide['slide_link'])) {
                $return .= '      <a href="' . $slide['slide_link'] . '">';
            }
            $return .= '          <button class="btn slide-btn ' . $slide['slide_button_color'] . '">' . $slide['slide_button_text'] . '</button>';
            if (!empty($slide['slide_link'])) {
                $return .= '      </a>';
            }
        }
        // This section is only for one column slideshows that have description text
        if (get_sub_field("column_number") == 1) {
            $return .= '    </div>
			                <div class="slide-info-section">';
            if (!empty($slide['slide_title'])) {
                $return .= '     <p class="slide-title">' . $slide['slide_title'] . '</p>';
            }
            if (!empty($slide['slide_text'])) {
                $return .= '     <p class="slide-text">' . $slide['slide_text'] . '</p>';
            }
            if (!empty($slide['slide_button_text'])) {
                if (!empty($slide['slide_link'])) {
                    $return .= '   <a href="' . $slide['slide_link'] . '">';
                }
                $return .= '         <button class="btn slide-btn ' . $slide['slide_button_color'] . '">' . $slide['slide_button_text'] . '</button>';
                if (!empty($slide['slide_link'])) {
                    $return .= '   </a>';
                }
            }
        }
        $return .= '       </div>
		                 </div>';
        if (!empty($slide['slide_link']) && empty($slide['slide_button_text'])) {
            $return .= '</a>';
        }
    }
    $tabletSlides = 1;
    if ($column_number > 1) {
        $tabletSlides = 2;
    }
    $return .= '   </div>
	            </section>

					<script type="text/javascript">
					   jQuery(document).ready(function() {
					   	// slideshow carousel
							jQuery(".' . $slideshow_name . '-carousel.owl-carousel").owlCarousel({
							  loop: true,
							  margin: 15,
							  nav: true,
							  navText: [
								 "<i class=\'fas fa-caret-left\'></i>",
								 "<i class=\'fas fa-caret-right\'></i>"
							  ],
							  autoplay: true,
							  autoplayHoverPause: true,
							  responsive: {
								 0: {
									items: 1
								 },
								 600: {
								   items: ' . $tabletSlides . '
								 },
								 1000: {
									items: ' . $column_number . '
								 }
							  }
							})
						});
					</script>
					';
    return $return;
}


function getWhatisMF() {
    $return = '';

    $widget_radio = get_field('show_what_is_maker_faire'); //gutenburg block
    if ($widget_radio == '')
        $widget_radio = get_sub_field('show_what_is_maker_faire'); //regular panel

    if ($widget_radio == 'show') {
        $return .= '<section class="what-is-maker-faire">
            <div class="container">
              <div class="row text-center">
                <div class="title-w-border-y">
                  <h2>' . pl__('What is Maker Faire?', 'MF_theme') . '</h2>
                </div>
              </div>
              <div class="row">
                <div class="col-md-10 col-md-offset-1">
                  <p class="text-center">' .
                pl__('Maker Faire is a gathering of fascinating, curious people who enjoy learning and who love sharing what they can do. From engineers to artists to scientists to crafters, Maker Faire is a venue for these "makers" to show hobbies, experiments, projects.', 'MF_theme') .
                '</p>' .
                '<p class="text-center">' .
                pl__('We call it the Greatest Show (& Tell) on Earth - a family-friendly showcase of invention, creativity, and resourcefulness.', 'MF_theme') .
                '</p>' .
                '<p class="text-center">' .
                pl__('Glimpse the future and get inspired!', 'MF_theme') .
                '</p>' .
// .get_site_option( 'what-is-makerfaire' ).
                '</div>
              </div>
            </div>
            <div class="wimf-border">
              <div class="wimf-triangle"></div>
            </div>
            <img src="' . get_bloginfo('template_directory') . '/img/makey.png" alt="Maker Faire information Makey icon" />
          </section>';
    }

    return $return;
}


/* * *************************************************** */
/* Function to return News Letter Panel                 */
/* * *************************************************** */

function getNewsletterPanel() {
    $return = 'This newsletter signup is no longer valid';
    $return .= '
      <section class="newsletter-panel">
         <div class="container">


            <form class="form-inline sub-form whatcounts-signup1" action="https://secure.whatcounts.com/bin/listctrl" method="POST">
               <!-- List ID 28-->
               <input type="hidden" id="list_6B5869DC547D3D46E66DEF1987C64E7A_yes" name="slid_1" value="6B5869DC547D3D46E66DEF1987C64E7A" />
               <input type="hidden" name="cmd" value="subscribe" />
               <input type="hidden" name="custom_source" value="Panel" />
               <input type="hidden" name="custom_incentive" value="none" />
               <input type="hidden" name="custom_url" value="' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '" />
               <input type="hidden" id="format_mime" name="format" value="mime" />
               <input type="hidden" name="custom_host" value="' . $_SERVER["HTTP_HOST"] . '" />
               <div id="recapcha-panel" class="g-recaptcha" data-size="invisible"></div>
               <input type="hidden" name="multiadd" value="1" />

               <div class="row row-eq-height">
                  <div class="col-xs-12 col-sm-6 align-middle">
                     <h3>' . get_sub_field('newsletter_panel_text') . '</h3>
                     <p class="more-details">Also get details on:</p>
                     <div class="row" style="width:100%">
                        <div class="col-xs-12 col-sm-5  align-middle">
                           <label class="sel-container">
                              <h5>Maker Faire Bay Area</h5>
                              <!-- List ID 65 -->
                              <input type="checkbox" id="list_6B5869DC547D3D461285274DDB064BAC_yes" name="slid_2" value="6B5869DC547D3D461285274DDB064BAC" />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                        <div class="col-xs-12 col-sm-7 align-middle">
                           <label class="sel-container">
                              <h5>World Maker Faire New York</h5>
                              <!-- List ID 64 -->
                              <input type="checkbox" id="list_6B5869DC547D3D4641ADFD288D8C7739_yes" name="slid_3" value="6B5869DC547D3D4641ADFD288D8C7739" />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>
                  </div>

                  <div class="col-xs-12 col-sm-6 align-middle">
                     <div class="row row-eq-height" style="width:100%">
                        <div class="col-xs-12 col-sm-2 align-middle">
                   <!--        <img class="img-responsive lazyload" src="/wp-content/themes/makerfaire/img/makey_outlined.svg" />-->
                        </div>
                        <div class="col-xs-12 col-sm-10 align-middle">
                           <input id="wc-email" class="form-control nl-panel-input" name="email" placeholder="' . __('Enter your Email', 'MiniMakerFaire') . '" required type="email">
                           <input class="form-control btn-w-ghost" value="' . __('Go', 'MiniMakerFaire') . '" type="submit">
                        </div>
                     </div>
                  </div>
               </div>
            </form>

         </div>
      </section>

    <div class="fancybox-thx" style="display:none;">
      <div class="col-sm-4 hidden-xs nl-modal">
        <span class="fa-stack fa-4x">
        <i class="far fa-circle fa-stack-2x"></i>
        <i class="far fa-thumbs-up fa-stack-1x"></i>
        </span>
      </div>
      <div class="col-sm-8 col-xs-12 nl-modal">
        <h3>' . __('Awesome!', 'MiniMakerFaire') . '</h3>
        <p>' . __('Thanks for signing up.', 'MiniMakerFaire') . '</p>
      </div>
      <div class="clearfix"></div>
    </div>

    <div class="nl-modal-error" style="display:none;">
        <div class="col-xs-12 nl-modal padtop">
            <p class="lead">The reCAPTCHA box was not checked. Please try again.</p>
        </div>
        <div class="clearfix"></div>
    </div>

    <script>
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
        jQuery(".nl-modal-error").fancybox({
          autoSize : false,
          width  : 250,
          autoHeight : true,
          padding : 0,
          afterLoad   : function() {
            this.content = this.content.html();
          }
        });
      });
      var onSubmitPanel = function(token) {
        var bla = jQuery("#wc-email").val();
        globalNewsletterSignup(bla);
        jQuery.post("https://secure.whatcounts.com/bin/listctrl", jQuery(".whatcounts-signup1").serialize());
        jQuery(".fancybox-thx").trigger("click");
        //jQuery(".nl-modal-email-address").text(bla);
        //jQuery(".whatcounts-signup2 #email").val(bla);
      }
      jQuery(document).on("submit", ".whatcounts-signup1", function (e) {
        e.preventDefault();
        onSubmitPanel();
      });
      var recaptchaKey = "6Lf_-kEUAAAAAHtDfGBAleSvWSynALMcgI1hc_tP";
      onloadCallback = function() {
        if ( jQuery("#recapcha-panel").length ) {
          grecaptcha.render("recapcha-panel", {
            "sitekey" : recaptchaKey,
            "callback" : onSubmitPanel
          });
        }
      };
    </script>';
    return $return;
}

/* * *************************************************** */
/* Function to return Sponser Panel                     */
/* * *************************************************** */

function getSponsorPanel() {
    $return = '';

    GLOBAL $acf_blocks;
    $url = ($acf_blocks ? get_field('sponsors_page_url') : get_sub_field('sponsors_page_url'));
    $year = ($acf_blocks ? get_field('sponsors_page_year') : get_sub_field('sponsors_page_year'));
    $id = url_to_postid($url);

    $title = ($acf_blocks ? get_field('title_sponsor_panel') : get_sub_field('title_sponsor_panel'));
    if($title=='')  $title = 'Thank you to our sponsors';

    // IF CUSTOM FIELD FOR SPONSOR SLIDER HAS A URL THEN SHOW THAT URL'S SPONSORS
    if (have_rows('goldsmith_sponsors', $id) || have_rows('silversmith_sponsors', $id) || have_rows('coppersmith_sponsors', $id) || have_rows('media_sponsors', $id)) {
        $return .= '
   <div class="sponsor-slide">
      <div class="container">
         <div class="row">
            <div class="col-xs-12 text-center padbottom">
               <h2 class="sponsor-slide-title">' . $title . '</h2>
            </div>
         </div>
         <div class="row">
            <div class="col-sm-12">
               <h4 class="sponsor-slide-title">' . ($year ? $year . ' ' : '') . 'Maker Faire Sponsors: <br /> <span class="sponsor-slide-cat"></span></h4>
            </div>
         </div>
         <div class="row">
            <div class="col-xs-12">
               <div id="carousel-sponsors-slider" class="carousel slide" data-ride="carousel">
                  <!-- Wrapper for slides -->
                  <div class="carousel-inner" role="listbox">';
        $sponsorArray = array(
            array('goldsmith_sponsors', 'GOLDSMITH'),
            array('silversmith_sponsors', 'SILVERSMITH'),
            array('coppersmith_sponsors', 'COPPERSMITH'),
            array('media_sponsors', 'MEDIA AND COMMUNITY'),
        );
        foreach ($sponsorArray as $sponsor) {
            if (have_rows($sponsor[0], $id)) {

                $sponsorCount = get_post_meta($id, $sponsor[0], true);

                $return .= '
                     <div class="item">
                        <div class="row sponsors-row sponsors-' . $sponsorCount . '">
                           <div class="col-xs-12">
                              <h3 class="sponsors-type text-center">' . $sponsor[1] . '</h3>
                              <div class="faire-sponsors-box">';

                while (have_rows($sponsor[0], $id)) {
                    the_row();
                    $sub_field_1 = get_sub_field('image'); //Photo
                    $sub_field_2 = get_sub_field('url'); //URL

                    $return .= '      <div class="sponsors-box-md">';
                    if (get_sub_field('url')) {
                        $return .= '      <a href="' . $sub_field_2 . '" target="_blank">';
                    }
                    $return .= '            <img class="lazyload" src="' . $sub_field_1 . '" alt="Maker Faire sponsor logo" />';
                    if (get_sub_field('url')) {
                        $return .= '      </a>';
                    }
                    $return .= '      </div><!-- close .sponsors-box-md -->';
                }
                $return .= '
                              </div> <!-- close .faire-sponsors-box -->
                           </div> <!-- close .col-xs-12 -->
                        </div> <!-- close .row sponsors-row -->
                     </div> <!-- close .item -->';
            }
        }

        $return .= '
                  </div> <!-- close .carousel-inner-->
               </div> <!-- close #carousel-sponsors-slider -->
            </div> <!-- close .col-xs-12 -->
         </div> <!-- close .row -->
         <div class="row">
            <div class="col-xs-12 text-center">
               <a class="btn btn-white more-makers-link" href="' . $url . '">Meet The Sponsors</a>
            </div>
         </div>
      </div> <!-- close .container -->
   </div> <!-- close .sponsor-slide -->';

        $return .= '<script>
                     // Update the sponsor slide title each time the slide changes
                     jQuery(".carousel-inner .item:first-child").addClass("active");
                     jQuery(function() {
                       var title = jQuery(".item.active .sponsors-type").html();
                       jQuery(".sponsor-slide-cat").text(title);
                       jQuery("#carousel-sponsors-slider").on("slid.bs.carousel", function () {
                         var title = jQuery(".item.active .sponsors-type").html();
                         jQuery(".sponsor-slide-cat").text(title);
                       });
                       if (jQuery(window).width() < 767) {
                         jQuery( ".maker-slider-btn" ).html("Learn More");
                       }
                     });
                     </script>';
    }

    return $return;
}


/* * ************************************************ */
/*  Function to return Social Media Panel           */
/* * ************************************************ */

function getSocialPanel() {
    $return = '';
    GLOBAL $acf_blocks;
    $panel_title = ($acf_blocks ? get_field('panel_title') : get_sub_field('panel_title'));

    if (have_rows('active_feeds')) {
        $return .= '
    <section class="social-feeds-panel">
      <div class="container">';
        if ($panel_title != '') {
            $return .= '
          <div class="row">
            <div class="col-xs-12 text-center">
              <div class="title-w-border-r">
                <h2>' . $panel_title . '</h2>
              </div>
            </div>
          </div>';
        }
        $return .= '
        <div class="social-row">';
        while (have_rows('active_feeds')) {
            the_row();

            if (get_row_layout() == 'facebook') {
                $facebook_title = get_sub_field('fb_title');
                $facebook_url = get_sub_field('facebook_url');
                $facebook_url_2 = rawurlencode($facebook_url);
                $return .= '
              <div class="social-panel-fb social-panel-feed">
                <h5>' . $facebook_title . '</h5>
                <iframe src="https://www.facebook.com/plugins/page.php?href=' . $facebook_url_2 . '&tabs=timeline&height=468&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId" width="100%" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
              </div>';
            } elseif (get_row_layout() == 'twitter') {
                $twitter_title = get_sub_field('tw_title');
                $twitter_id = get_sub_field('twitter_id');
                $return .= '
              <div class="social-panel-tw social-panel-feed">
                <div class="twitter-feed-parent">
                  <h5>' . $twitter_title . '</h5>
                  <script type="text/javascript" src="' . get_bloginfo('template_directory') . '/js/twitterFetcher.min.js"></script>
                  <h4>Tweets <span>by <a href="https://twitter.com/' . $twitter_id . '" target="_bank">@' . $twitter_id . '</a></span></h4>
                  <hr />
                  <div id="twitter-feed-body"></div>
                  <script>
                    var twitter_handle = "' . $twitter_id . '";
                    var configProfile = {
                      "profile": {"screenName": twitter_handle},
                      "domId": "twitter-feed-body",
                      "maxTweets": 10,
                      "enableLinks": true,
                      "showUser": true,
                      "showTime": true,
                      "showImages": true,
                      "lang": "en"
                    };
                    twitterFetcher.fetch(configProfile);
                  </script>
                </div>
              </div>';
            } elseif (get_row_layout() == 'instagram') {
                $instagram_title = get_sub_field('ig_title');
                $instagram_iframe = get_sub_field('instagram_iframe');
                $return .= '
              <div class="social-panel-ig social-panel-feed">
                <h5>' . $instagram_title . '</h5>
                ' . $instagram_iframe . '
              </div>';
            }
        }
        $return .= '
        </div>
      </div>
    </section>';
    }
    return $return;
}

/* * *************************************************** */
/* Function to get Faire backlink                       */
/* * *************************************************** */

function get_faire_backlink() {
    $back_link = get_field('back_link');
    $back_link_url = $back_link['back_link_url'];
    $back_link_text = $back_link['back_link_text'];
    $back_link_html = '';
    if ($back_link_url != '' && $back_link_text != '') {
        $back_link_html = '<div class="faire-backlink">
         <i class="far fa-chevron-left"></i>
         <a href="' . $back_link_url . '">' . $back_link_text . '</a>
      </div>';
    }
    return $back_link_html;
}

/* * *************************************************** */
/* Function to return flag banner panel                 */
/* * *************************************************** */

function getFlagBannerPanel() {
    return '<div class="flag-banner"></div>';
}

/* * *************************************************** */
/* Function to return a banner featuring Makey          */
/* * *************************************************** */

function getMakeyBanner() {
    GLOBAL $acf_blocks;

    $title  = ($acf_blocks ? get_field('title_link_text') : get_sub_field('title_link_text'));
    $URL    = ($acf_blocks ? get_field('link_url') : get_sub_field('link_url'));

    $content = '<div class="makey-banner ' . ($acf_blocks ? get_field('background-color') : get_sub_field('background-color')) . '">';
    $content .= '   <div class="container">';
    $content .= '      <div class="picture-holder">';
    $content .= '         <img alt="Maker Robot" height="74" class="lazyload" src="/wp-content/uploads/2015/04/maker-robot.png" width="53">';
    $content .= '      </div>';
    $content .= '      <a href="' . $URL . '">' . $title . ' <i class="icon-arrow-right"></i></a>';
    $content .= '   </div>';
    $content .= '</div>';

    return $content;
}

/* **************************************************** */
/* Function to return the rss feed from user input      */
/* **************************************************** */
function getRSSFeed() {
	GLOBAL $acf_blocks;
	$title = ($acf_blocks ? get_field('title') : get_sub_field('title'));
	$feed_tag = ($acf_blocks ? get_field('feed_tag') : get_sub_field('feed_tag'));
	$more_link = ($acf_blocks ? get_field('more_link') : get_sub_field('more_link'));
	$number = ($acf_blocks ? get_field('number') : get_sub_field('number'));

	$rss_shortcode = '[make_rss title="' . urlencode($title) . '" feed="' . $feed_tag . '" moreLink="' . $more_link . '" number=' . $number . ']';
	echo do_shortcode($rss_shortcode);
}



/* ********************************************************* */
/* Function to show a list of faires of the type entered     */
/* ********************************************************* */
function getFaireList() {
    GLOBAL $acf_blocks;
    GLOBAL $wpdb;

    $date_start = date('Y-m-d H:i:s', time());

    $faire_type = ($acf_blocks ? implode(",", get_field('type')) : implode(",", get_sub_field('type')));
    $past_or_future_value = ($acf_blocks ? get_field('past_or_future') : get_sub_field('past_or_future'));

    $past_or_future = "";
    if($past_or_future_value == '>') {
        $past_or_future = " AND event_start_dt > '" . $date_start . "'";
    } else if($past_or_future_value == '<') {
        $past_or_future = " AND event_start_dt < '" . $date_start . "'";
    }
    $limit = ($acf_blocks ? get_field('number') : get_sub_field('number'));

    $output = "<ul class='flex-list faire-list'>";
    $rows = $wpdb->get_results( "SELECT faire_name, faire_nicename, event_type, event_dt, event_start_dt, event_end_dt, faire_url, cfm_url, faire_image, cfm_image FROM {$wpdb->prefix}mf_global_faire WHERE event_type in({$faire_type}){$past_or_future} ORDER BY event_start_dt", OBJECT );
    $i = 0;
    foreach($rows as $row){
        if($row->faire_image) {

            $name = isset($row->faire_nicename) ? $row->faire_nicename : $row->faire_name;
            $output .= "<li><a href='$row->faire_url'>";
            $output .=      "<img src='$row->faire_image'>";
            $output .=      "<p>$row->event_dt</p>";
            $output .=      "<h3>$name</h3>";
            $output .= "</a></li>";
            if (++$i == $limit) break;
        }
    }
    $output .= "</ul>";
    echo($output);
}

/* **************************************************** */
/* Function to show a list of call for makers forms     */
/* **************************************************** */
function getCFMList() {
    GLOBAL $acf_blocks;
    GLOBAL $wpdb;

    $featured_faire_limit = ($acf_blocks ? get_field('featured_faires_number') : get_sub_field('featured_faires_number'));
    $community_faire_limit = ($acf_blocks ? get_field('community_faires_number') : get_sub_field('community_faires_number'));

    $output = "<div class='cfm-list'>";
    $output .=   "<ul class='flex-list featured-cfm-list'>";
    $featuredRows = $wpdb->get_results( "SELECT event_start_dt, cfm_start_dt, cfm_end_dt, event_type, cfm_url, faire_image, cfm_image FROM {$wpdb->prefix}mf_global_faire WHERE event_type = 'Featured' AND cfm_start_dt < CURRENT_DATE() AND cfm_end_dt > CURRENT_DATE() ORDER BY event_start_dt", OBJECT );
    $i = 0;
    foreach($featuredRows as $row){
        if($row->cfm_image) {
            $output .= "<li><a href='$row->cfm_url'>";
            $output .=      "<img src='$row->cfm_image'>";
            $output .= "</a></li>";
            if (++$i == $featured_faire_limit) break;
        }
    }
    $output .=   "</ul>";
    $output .=   "<ul class='community-cfm-list'>";
    $communityRows = $wpdb->get_results( "SELECT faire_name, event_start_dt, cfm_start_dt, cfm_end_dt, event_type, event_dt, cfm_url FROM {$wpdb->prefix}mf_global_faire WHERE event_type = 'Mini' AND cfm_start_dt < CURRENT_DATE() AND cfm_end_dt > CURRENT_DATE() ORDER BY event_start_dt", OBJECT );
    $j = 0;
    foreach($communityRows as $row){
        $output .= "<li><a href='$row->cfm_url'>";
        $output .=      $row->faire_name . "<br />(" . $row->event_dt . ")";
        $output .= "</a></li>";
        if (++$j == $community_faire_limit) break;
    }
    $output .=   "</ul>";
    echo($output);
}
