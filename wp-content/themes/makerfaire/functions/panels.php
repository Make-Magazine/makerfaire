<?php

/* **************************************************** */
/* Determine correct layout                             */
/* **************************************************** */
function dispLayout($row_layout) {
   $return = '';
   $activeinactive = get_sub_field('activeinactive');
   if ($activeinactive == 'Active') {
      switch ($row_layout) {
         case 'buy_tickets_float': //floating buy tickets banner
            $return = getBuyTixPanel($row_layout);            
            break;
         case 'featured_makers_panel':                // FEATURED MAKERS (SQUARE)
         case 'featured_makers_panel_dynamic':        // FEATURED MAKERS (SQUARE) - dynamic
            $return = getFeatMkPanel($row_layout);            
            break;
         case '3_column': // 3 COLUMN LAYOUT
            $return = get3ColLayout();            
            break;
         case '6_column': // 6 column navigation panel
            $return = get6ColLayout();            
            break;
         case '1_column_wysiwyg': // 1 column wysiwyg
            $return = get1ColWYSIWYG();            
            break;
         case '1_column': // 1 COLUMN LAYOUT
            $return = get1ColLayout();            
            break;
         case 'call_to_action_panel':  // CTA PANEL
         case 'call_to_action':  // CTA PANEL
            $return = getCTApanel();            
            break;
         case 'news_block_panel':  // NEWS BLOCK PANEL
            $return = getNewsBlockpanel();            
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
         case 'newsletter_panel':  // NEWSLETTER PANEL
            $return = getNewsletterPanel();            
            break;
         case 'sponsors_panel':   // SPONSOR PANEL
            $return = getSponsorPanel();            
            break;
         case 'featured_faires_panel':   // FEATURED FAIRES PANEL
            $return = getFeatFairePanel();            
            break;   
         case 'social_media': //social media panel
            $return = getSocialPanel();            
            break;
         case 'flag_banner_panel': //flag banner separator
            $return = getFlagBannerPanel();
            break;
			case '2_column_video': // Video Panels
            $return = getVideoPanel();
            break;
			case '2_column_images': // Image Panels in the same style as the Video Panels
            $return = getImagePanel();
				break;
         case 'maker_faire_map_cta': // faire map link separator
            $return = getMFMapCTAPanel();
            break;
      }
   }
   return $return;
}

/* * *********************************************** */
/*   Function to build the featured maker panel   */
/* * *********************************************** */

function getFeatMkPanel($row_layout) {
   $return = '';
   $dynamic = ($row_layout == 'featured_makers_panel_dynamic' ? true : false);

   $makers_to_show = get_sub_field('makers_to_show');
   $more_makers_button = get_sub_field('more_makers_button');
   $background_color = get_sub_field('background_color');
	
   $title = (get_sub_field('title') ? get_sub_field('title') : '');

   //var_dump($background_color);

   // Check if the background color selected was white
   $return .= '<section class="featured-maker-panel ' . $background_color . '"> ';

   //build the container div
   $return .= '<div class="container">';
   //do not return yellow underline whenbackground is white

   $return .= '<div class="row text-center">
            <div class="panel-title title-w-border-y '.($background_color === "white-bg" ? ' yellow-underline' : '') .'">
              <h2>' . $title . '</h2>
            </div>
          </div>';

   //build makers array
   $makerArr = array();
   if ($dynamic) {
      $formid = (int) get_sub_field('enter_formid_here');

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
         
         $args = array(
            'resize' => '300,300',
            'quality' => '80',
            'strip' => 'all',
         );
         $photon = jetpack_photon_url($url, $args);

         $makerArr[] = array('image' => $photon,
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
            $url = get_sub_field('maker_image');
            $args = array(
               'resize' => '300,300',
               'quality' => '80',
               'strip' => 'all',
            );
            $photon = jetpack_photon_url($url['url'], $args);
            $makerArr[] = array('image' => $photon,
               'name' => get_sub_field('maker_name'),
               'desc' => get_sub_field('maker_short_description'),
               'maker_url' => get_sub_field('more_info_url')
            );
         }
      }
   }

   //limit the number returned to $makers_to_show
   $makerArr = array_slice($makerArr, 0, $makers_to_show);

   $return .= '<div id="performers" class="row padbottom">';

   //loop thru maker data and build the table
   foreach ($makerArr as $maker) {
      // var_dump($maker);
      // echo '<br />';
      $return .= '<div class="col-lg-4 col-md-6 col-sm-12">'
         . ' <div class="thumbnail">';
         $desc = $maker['desc'];
         if(strlen($desc) > 300) {
            $breakpoint = strpos($desc, ' ', 290);
            $desc = substr($desc, 0, $breakpoint) . '&hellip;';
         }
      if (!empty($maker['desc'])) {
         $markup = !empty($maker['maker_url']) ? 'a' : 'div';
         $href= !empty($maker['maker_url']) ? 'href="' . $maker['maker_url'] . '"' : '';
         $return .= '<'.$markup.' '.$href.' class="caption" style="display: none;">
                     <h4>' . $maker['name'] . '</h4>
                     <div class="desc-box"><p class="desc">' . $desc . '</p></div>';
         if (!empty($maker['maker_url'])) {
            $return .= '  <p class="btn btn-w-ghost learn-more">Learn More</p>'; //<a href="' . $maker['maker_url'] . '"></a>
         }
         $return .= ' </'.$markup.'>'; // close .caption
      }
      $return .= '<img class="img-responsive aligncenter size-full" src="' . $maker['image'] . '" alt="' . $maker['name'] . '" width="350" height="304" />';
      $return .= '<div class="sub-caption">
                  <h4>' . $maker['name'] . '</h4>
                </div>'; //close .sub-caption
      $return .= '  </div>'; //close .thumbnail
      $return .= '</div>'; //close .col-sm-4
   }
   $return .= '</div>';  //close #performers
   //check if we should display a more maker button
   $cta_url = get_sub_field('cta_url');
   if ($cta_url) {
      $cta_text = (get_sub_field('cta_text') !== '' ? get_sub_field('cta_text') : 'More Makers');
      $return .= '<div class="row padbottom">
            <div class="col-xs-12 padbottom text-center">
              <a class="btn btn-outlined more-makers-link" href="' . $cta_url . '">' . $cta_text . '</a>
            </div>
          </div>';
   }
   $return .= '</div>'; //end div.container
   $return .= '</section>';
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

   $return .= '<div class="row padbottom">';

   //build event array
   $eventArr = array();
   if ($dynamic) {
      $formid = get_sub_field('enter_formid_here');
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
              <div class="event-img" style="background-image: url(' . $event['image'] . ');"></div>
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
   if (get_sub_field('all_events_button')) {
      $all_events_button = get_sub_field('all_events_button');
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

   $panelTitle = get_sub_field('panel_title');
   if ($panelTitle) {
      $return .= ' <div class="row">
                    <div class="col-xs-12 text-center padbottom">
                      <h2 class="panel-title yellow-underline">' . $panelTitle . '</h2>
                    </div>
                  </div>';
   }

   $return .= '   <div class="row">'; //start row
   //get requested data for each column
   $columns = get_sub_field('column');
   foreach ($columns as $column) {
      $return .= '   <div class="col-sm-4">'; //start column
      $data = $column['data'];
      $columnInfo = '';
      switch ($column['column_type']) {
         case 'image':     // Image with optional link
            $alignment = $data['column_list_alignment'];
            $imageArr = $data['column_image_field'];                  
            $image = '<img alt="'.$imageArr['alt'].'" class="img-responsive" src="' . $imageArr['url'] . '" />';
            
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

   $return .= '<section class="content-panel six-column">';

   $panelTitle = get_sub_field('panel_title');
   if ($panelTitle) {
      $return .= ' <div class="row">
                    <div class="col-xs-12 text-center padbottom">
                      <h2 class="panel-title yellow-underline">' . $panelTitle . '</h2>
                    </div>
                  </div>';
   }

   $return .= '   <div class="image-grid-row">'; //start row
   //get requested data for each column
   $columns = get_sub_field('column');
   //$columnsCount = count($columns);
   //print_r($columns);
   foreach ($columns as $column) {
      $return .= '   <div class="image-grid-col">'; //start column
      $data = $column['data'];
      
      $imageArr = $data['column_image_field'];
      //var_dump($imageArr['alt']);
            
      $columnInfo = '';                     
      //$image = '<img height="" width="" alt="'.$imageArr['alt'].'" class="ximg-responsive" src="' . $imageArr['url'] . '" />';
      //echo $imageArr['url'];

      $imgStyle = 'style="background-image: url('.$imageArr['url'].');"';

      $cta_link = $data['image_cta'];
      $ctaText = $data['image_cta_text'];
		
		$bgColor = $data['button_color'];

      if (!empty($cta_link)) {
			if(!empty($imageArr['url'])) {
         	$columnInfo = '<a class="six-col-img" href="' . $cta_link . '" '.$imgStyle.'></a>';
			}
         if (!empty($ctaText)) {
            $columnInfo .= '<p class="text-center sub-caption-bottom ' . $bgColor . '"><a href="' . $cta_link . '" target="_blank">' . $ctaText . '</a></p>';
         }
      } else {
         $columnInfo = $image;
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
  $return .=  '<section class="content-panel">
          <div class="container">';

  if(get_sub_field('title')) {
    $return .=  '  <div class="row">
              <div class="col-xs-12 text-center padbottom">
                <h2 class="panel-title yellow-underline">' . get_sub_field('title') . '</h2>
              </div>
            </div>';
  }

  $return .=  '    <div class="row">
              <div class="col-xs-12">' . $column_1 . '</div>
            </div>';

  if(get_sub_field('cta_button')) {
    $return .=  '  <div class="row text-center padtop">
              <a class="btn btn-b-ghost" href="' . $cta_button_url . '">' . $cta_button . '</a>
            </div>';
  }

  $return .=  '  </div>
          
        </section>';
  return $return;
}

/***************************************************** */
/*   Function to return 1_column_photo_and_text_panel  */
/***************************************************** */
function get1ColLayout() {
   //get data submitted on admin page
            
   //loop thru and randomly select an image.
   $hero_array = array();
   if (have_rows('hero_image_repeater')) {
      // loop through the rows of data
      
      while (have_rows('hero_image_repeater')) {
         the_row();
         // TODO add the URL wrapper
         $hero_image_random = get_sub_field('hero_image_random');
			$hero_image_url = $hero_image_random["url"];

         $image = '<div class="hero-img" style="background-image: url(\'' . $hero_image_url. '\');"></div>';
         $cta_link = get_sub_field('image_cta');
         
         if (!empty($cta_link)) {
            $columnInfo = '<a href="' . $cta_link . '">' . $image . '</a>';
         } else {
            $columnInfo = $image;
         }
         $hero_array[] = $columnInfo;
      }
      $randKey = array_rand($hero_array,1);      
      $hero_image = $hero_array[$randKey];              
   }

   $hero_text = get_sub_field('column_title');
   $cta_button = get_sub_field('cta_button');
   $cta_button_url = get_sub_field('cta_button_url');

   //build output
   $return = '';
   $return .= '<section class="hero-panel">';    // create content-panel section

   $return .= '   <div class="row">
                    <div class="col-xs-12">';
   if($hero_text) {
      $return .= '<div class="top_left"><img src="/wp-content/themes/makerfaire/img/TopLeftCorner.png"></div>'
			     .  '<div class="panel_title">'
              .  '   <div class="panel_text">' . $hero_text . '</div>'
              .  '   <div class="bottom_right"><img src="/wp-content/themes/makerfaire/img/BottomRightCorner.png"></div>'
              .  '</div>';
   }
   $return .=    '        '.$hero_image .
      '     </div>' .
      '   </div>';

   if (get_sub_field('cta_button')) {
      $return .= ' <div class="row text-center padtop">
                    <a class="btn btn-b-ghost" href="' . $cta_button_url . '">' . $cta_button . '</a>
                  </div>';
   }

   // Because of the aggressive caching on prod, it makes more sense to shuffle the array in javascript
   $return .= '</section><script type="text/javascript">var heroArray = ' . json_encode($hero_array) . ';heroArray.sort(function(a, b){return 0.5 - Math.random()});jQuery(document).ready(function(){jQuery(".hero-img").css("background-image","url("+heroArray[0]+")");bgSize( jQuery( ".hero-img" ), function( width, height ){if( height > 450 ) {jQuery(".hero-img").css( "height", "450px" );} else if( height > 320 ){jQuery( ".hero-img" ).css( "height", height + "px" );}});});</script>'; 
   return $return;
}

/***************************************************** */
/*   Function to return 2_column_video panel           */
/***************************************************** */
function getVideoPanel() {
   //get data submitted on admin page
            
   $return = '';
   $return .= '<section class="video-panel container-fluid">';    // create content-panel section

   //get requested data for each column
   $video_rows = get_sub_field('video_row');
	$vidowRowNum = 0;
   foreach ($video_rows as $video) {
		$videoRowNum += 1;
      if($videoRowNum % 2 != 0){ 
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
									 <iframe src="https://www.youtube.com/embed/' . $video['video_code'] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
								  </div>
			              </div>';
			$return .= '</div>';
		} else {
			$return .= '<div class="row">';
			$return .= '  <div class="col-sm-8 col-xs-12">
								  <div class="embed-youtube">
									 <iframe src="https://www.youtube.com/embed/' . $video['video_code'] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
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

/***************************************************** */
/*   Function to return 2_column_image panel           */
/***************************************************** */
function getImagePanel() {
   //get data submitted on admin page
            
   $return = '';
   $return .= '<section class="image-panel container-fluid">';    // create content-panel section

   //get requested data for each column
   $image_rows = get_sub_field('image_row');
	$imageRowNum = 0;
   foreach ($image_rows as $image) {
		$imageRowNum += 1;
		$imageObj = $image['image'];

      if($imageRowNum % 2 != 0){ 
			$return .= '<div class="row ' . $image['background_color'] . '">';
			$return .= '  <div class="col-sm-4 col-xs-12">
								 <h4>' . $image['image_title'] . '</h4>
								 <p>' . $image['image_text'] . '</p>';
			              if($image['image_links']) {
								  foreach ($image['image_links'] as $image_link) {
			$return .= '  	    <a href="' . $image_link['image_link_url'] . '">' . $image_link['image_link_text'] . '</a>';
								  }
							  }
			$return .= '  </div>';
			$return .= '  <div class="col-sm-8 col-xs-12">
			                 <div class="image-display">';
								  if ($image['image_overlay']['image_overlay_link']) {   
			$return .= ' 		  <a href="' . $image['image_overlay']['image_overlay_link'] . '">';
								  }
			$return .= '			 <img class="img-responsive" src="' . $imageObj['url'] . '" alt="' . $imageObj['alt'] .'" />';
											if ($image['image_overlay']['image_overlay_text']) {          
											  $return .= '  <div class="image-overlay-text">' . $image['image_overlay']['image_overlay_text'] . '</div>';;
											}
			                 if ($image['image_overlay']['image_overlay_link']) { 
			$return .= '        </a>';
								  }
			$return .= '		</div>
			              </div>';
			$return .= '</div>';
		} else {
			$return .= '<div class="row ' . $image['background_color'] . '">';
			$return .= '  <div class="col-sm-8 col-xs-12">
			                 <div class="image-display">';
								  if ($image['image_overlay']['image_overlay_link']) {   
			$return .= ' 		  <a href="' . $image['image_overlay']['image_overlay_link'] . '">';
								  }
			$return .= '			 <img class="img-responsive" src="' . $imageObj['url'] . '" alt="' . $imageObj['alt'] .'" />';
											if ($image['image_overlay']) {          
											  $return .= '  <div class="image-overlay-text">' . $image['image_overlay']['image_overlay_text'] . '</div>';;
											}
			                 if ($image['image_overlay']['image_overlay_link']) { 
			$return .= '        </a>';
								  }
			$return .= '  </div>';
			$return .= '</div>';
			$return .= '  <div class="col-sm-4 col-xs-12">
								 <h4>' . $image['image_title'] . '</h4>
								 <p>' . $image['image_text'] . '</p>';
			              if($image['image_links']) {
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

/* **************************************************** */
/* Function to return Buy Tickets Floating Banner       */
/* **************************************************** */
function getBuyTixPanel() {
   return '<a href="' . get_sub_field('buy_ticket_url') . '" target="_blank"><div class="floatBuyTix">'.get_sub_field('buy_ticket_text').'</div></a>';
}

/* **************************************************** */
/* Function to return Call to Action panel              */
/* **************************************************** */
function getCTApanel() {
   $return = '';
   $cta_title = get_sub_field('text');
   $cta_url = get_sub_field('url');
   $background_color = get_sub_field('background_color');
   $bg_color_class_map = array(
      "Blue" => '',
      "Light Blue" => ' light-blue-ribbon',
      "Red" => ' red-ribbon',
      "Orange" => ' orange-ribbon'
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


/* **************************************************** */
/* Function to return Ribbon Separator panel            */
/* **************************************************** */
function getRibbonSeparatorpanel() {
   $return = '';
   $background_color = get_sub_field('background_color');
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


/* **************************************************** */
/* Function to return News Block panel                  */
/* **************************************************** */
function getNewsBlockpanel() {
   $args = [
      'tag' => get_sub_field('tag'),
      'title' => get_sub_field('title'),
      'link' => get_sub_field('link')
   ];
   require_once 'MF-News-Block.php';
   return do_news_block($args);
}

/* **************************************************** */
/* Function to return Tint Social Block panel           */
/* **************************************************** */
function getTintSocialBlockpanel() {
   $args = [
      'personalization_id' => get_sub_field('personalization_id'),
      'title' => get_sub_field('title'),
      'hashtags' => get_sub_field('hashtags')
   ];
   require_once 'MF-Social-Block.php';
   return do_social_block($args);
}

/* **************************************************** */
/* Function to return IMAGE CAROUSEL (RECTANGLE)        */
/* **************************************************** */
function getImgCarousel() {
   $return = '';
   // IMAGE CAROUSEL (RECTANGLE)
   $width = get_sub_field('width');
   // check if the nested repeater field has rows of data
   if (have_rows('images')) {

      $return .= '<section class="rectangle-image-carousel ';
      if ($width == 'Content Width') {
         $return .= 'container">';
      } else {
         $return .= '">';
      }
      $return .= '<div id="carouselPanel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner" role="listbox">';
      $i = 0;

      // loop through the rows of data
      while (have_rows('images')) {
         the_row();

         $text = get_sub_field('text');
         $url = get_sub_field('url');
         $image = get_sub_field('image');

         if ($i == 0) {
            $return .= '
        <div class="item active">';
            if (get_sub_field('url')) {
               $return .= '<a href="' . $url . '">';
            }
            $return .= '
            <img src="' . $image['url'] . '" alt="' . $image['alt'] . '" />';
            if (get_sub_field('text')) {
               $return .= '
              <div class="carousel-caption">
                <h3>' . $text . '</h3>
              </div>';
            }
            if (get_sub_field('url')) {
               $return .= '</a>';
            }
            $return .= '
        </div>';
         } else {
            $return .= '<div class="item">
          <img src="' . $image['url'] . '" alt="' . $image['alt'] . '" />
          <div class="carousel-caption">
            <h3>' . $text . '</h3>
          </div>
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

/* **************************************************** */
/* Function to return IMAGE CAROUSEL (SQUARE)           */
/* **************************************************** */
function getImgCarouselSquare() {
   $return = '';
   // IMAGE CAROUSEL (SQUARE)
   $width = get_sub_field('width');

   if (have_rows('images')) {
      $return .= '<section class="square-image-carousel ' . ($width == 'Content Width' ? 'container nopad' : '') . '">';
      $return .= '<div class="mtm-carousel owl-carousel">';
      while (have_rows('images')) {
         the_row();

         $text = get_sub_field('text');
         $url = get_sub_field('url');
         $image = get_sub_field('image');
         $return .= '<div class="mtm-car-image" style="background: url(\'' . $image["url"] . '\') no-repeat center center;background-size: cover;"></div>';
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

/* **************************************************** */
/* Function to return News Letter Panel                 */
/* **************************************************** */
function getNewsletterPanel() {
   $return = '
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
                   <!--        <img class="img-responsive" src="/wp-content/themes/makerfaire/img/makey_outlined.svg" />-->
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
        <i class="fa fa-circle-thin fa-stack-2x"></i>
        <i class="fa fa-thumbs-o-up fa-stack-1x"></i>
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

/* **************************************************** */
/* Function to return Sponser Panel                     */
/* **************************************************** */
function getSponsorPanel() {
   $return = '';
   $url  = get_sub_field('sponsors_page_url');
   $year = get_sub_field('sponsors_page_year');
   $id = url_to_postid($url);

   // IF CUSTOM FIELD FOR SPONSOR SLIDER HAS A URL THEN SHOW THAT URL'S SPONSORS
   if (have_rows('goldsmith_sponsors', $id) || have_rows('silversmith_sponsors', $id) || have_rows('coppersmith_sponsors', $id) || have_rows('media_sponsors', $id)) {
      $return .= '
   <div class="sponsor-slide">
      <div class="container">
         <div class="row">
            <div class="col-xs-12 text-center padbottom">
               <h2 class="panel-title yellow-underline">Thank You To Our Sponsors!</h2>
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
               $return .= '            <img src="' . $sub_field_1 . '" alt="Maker Faire sponsor logo" />';
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
               <a class="btn btn-white more-makers-link" href="' . $url .'">Meet The Sponsors</a>
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

/************************************************** */
/*  Returns a list of featured faires based on data */
/*  entered in another page                         */
/************************************************** */
function getFeatFairePanel(){   
   $return = '';         
   $return .= '<section class="featured-panel white-back"> ';

   //build the container div
   $return .= '<div class="container featured-faire-landing">';
   
   // Display the panel title
   $title = (get_sub_field('featured_faires_title') ? get_sub_field('featured_faires_title') : '');   
   $return .= '<div class="row text-center">
                  <div class="panel-title title-w-border-y yellow-underline">
                    <h2>' . $title . '</h2>
                  </div>
                </div>';
   
   //get featured faires data
   $url  = get_sub_field('featured_faires_page_url');
   $cta_url = get_sub_field('more_faires_url');
   $cta_text = (get_sub_field('more_faires_text') !== '' ? get_sub_field('more_faires_text') : 'More Faires');
   
   //pull featured faire information based on entered url
   $id = url_to_postid($url);
   $faires_to_show = (int) get_sub_field('faires_to_show');
   
   $faires_shown = 0;
   // If the linked page has featured faires, then display data
   if (have_rows('featured_faires', $id)) {      
      $return .= ' <div class="row">';
      while( have_rows('featured_faires', $id) && $faires_shown < $faires_to_show ){          
         the_row();

         //don't display events that have passed
         if(!get_sub_field('past_event')){
            $faires_shown++;  
            
            $faire_title = get_sub_field('faire_title'); //Title            
            $faire_url   = get_sub_field('faire_url'); //URL
            $faire_photo = get_sub_field('faire_photo'); //Photo
            $faire_date  = get_sub_field('faire_date'); //Date
            
            $return .= '<div class="col-xs-12 col-sm-6 col-md-4">';
            $return .= '   <div class="featured-faire-box">';
            if($faire_url !=''){
              $return .= '<a href="' . $faire_url . '">';
            }
            $return .=   '<img src="' . $faire_photo['url'] . '" alt="Featured Maker Faire Image" class="img-responsive" />';
            //$return .=   '<p class="featured-faire-above-title">Maker Faire</p>';
            $return .=   '<h4 class="featured-faire-date">' . $faire_date . '</h4>';
            $return .=   '<h3 class="featured-faire-title clear">' . $faire_title . '</h3>';
            $return .=   '<div class="clearfix"></div>';
            if($faire_url !=''){
              $return .= '</a>';
            }
            $return .=   '   </div>';
            $return .=   '</div>';            
         }            
      }
      $return .= '   </div>';     
   }
   
   //add cta section
   if ($cta_url!='') {      
      $return .= '<div class="row padbottom">
                     <div class="col-xs-12 padbottom text-center">
                       <a class="btn btn-blue-universal cta-btn" href="' . $cta_url . '">' . $cta_text . '</a>
                     </div>
                   </div>';
   }
   $return .= '   </div>'  //close .container div
           .  '</section>';
   return $return;
}   

/************************************************** */
/*  Function to return Social Media Panel           */
/************************************************** */
function getSocialPanel() {
   $return = '';
   $panel_title = get_sub_field('panel_title');
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

/* **************************************************** */
/* Function to get Faire backlink                       */
/* **************************************************** */
function get_faire_backlink() {
   $back_link = get_field('back_link');
   $back_link_url  = $back_link['back_link_url'];
   $back_link_text = $back_link['back_link_text'];
   $back_link_html = '';
   if($back_link_url!='' && $back_link_text!=''){
      $back_link_html =
      '<div class="faire-backlink">
         <i class="far fa-chevron-left"></i>
         <a href="'. $back_link_url.'">'. $back_link_text.'</a>
      </div>';      
   }
   return $back_link_html;
}

/* **************************************************** */
/* Function to return flag banner panel                 */
/* **************************************************** */
function getFlagBannerPanel() {
 return '<div class="flag-banner"></div>';  
}


/* **************************************************** */
/* Function to return maker faire map call-to-action    */
/* **************************************************** */
function getMFMapCTAPanel() {
   $title = get_sub_field('title_link_text');
   $URL = get_sub_field('link_url');

   $content = '<div class="location-holder">';
   $content .= '   <div class="container">';
   $content .= '      <div class="picture-holder">';
   $content .= '         <img alt="Maker Robot" height="74" src="/wp-content/uploads/2015/04/maker-robot.png" width="53">';
   $content .= '      </div>';
   $content .= '      <a href="'.$URL.'">'.$title.' <i class="icon-arrow-right"></i></a>';
   $content .= '   </div>';
   $content .= '</div>';

   return $content;  
  }
