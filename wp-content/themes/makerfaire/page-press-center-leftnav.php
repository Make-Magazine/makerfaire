<?php
/*
Template name: Press Center Template w/Left Nav
*/
get_header();
$layout_type = get_field('layout_type');
$layout_class_name = str_replace('_','-',$layout_type ?? ''); // replace underscores with dashes for class name
$content_class = "content col-sm-9 " . $layout_class_name; // generate a string to use as out class for the content div
// global $wp; // doesn't seem to be needed... delete?
$current_slug = add_query_arg( array(), $wp->request );
?>

<div class="clear"></div>
<!--<div class="post-thumbnail">
   <?php //the_post_thumbnail(); ?>
</div>--><!-- .post-thumbnail -->
<div class="page-leftnav">

   <div class="top-bar">
      <div class="container">
         <div class="row">
            <div class="col-lg-offset-3 col-lg-9">
               <header class="page-header">
                  <h1><?php echo get_the_title(); ?></h1>
                  <span class="email-links">
                     <p>Press inquiries: <a href="mailto:pr@makerfaire.com">pr@makerfaire.com</a></p>
                  </span>
               </header>
            </div>
         </div>
      </div>
   </div>

   <div class="container">
      <div class="row">
         
         <div class="col-sm-9 col-sm-push-3 <?php echo $content_class; ?>">
         <?php 
            if($layout_type === 'wysiwyg') {
               echo get_field('freeform_content');
            }

            elseif($layout_type === 'press_releases') {
               $release_collection = get_field('press_release_collection');
               if($release_collection) {
                  $press_releases_markup = '';
                  foreach($release_collection as $group) {
                     $press_releases_markup .= '<h2>' . $group["press_release_collection_group_header"] . '</h2>';
                     foreach($group["press_release_collection_group"] as $release) {
                        $release_text = $release['release_date'] ? $release['release_date'] . ' &mdash; ' : '';
                        $release_text .= '<a href="'.$release['release_link'].'">' . $release['release_link_text'] . '</a>';
                        $release_text .= $release['release_source'] ? ' - ' . $release['release_source'] : '';
                        $press_releases_markup .=  '<div class="press-release-instance">'.$release_text.'</div>';
                     }
                  }
                  echo $press_releases_markup;
               }
            } 

            elseif($layout_type === 'photo_video') {

               $photo_collection_header = get_field('photo_collection_header');
               if($photo_collection_header) {
                  echo '<div class="row"><div class="col-md-12"><h2>'.$photo_collection_header.'</h2></div></div>';
               }
               $photo_collection_description = get_field('photo_collection_description');
               if($photo_collection_description) {
                  echo '<div class="row"><div class="col-md-12">'.$photo_collection_description.'</div></div>';
               }

               $photo_collection = get_field('photo_collection');
               if($photo_collection) {
                  // NOTE (ts): no photos for Make: in design, so no need for header unless there's actually photos to show (especially since there's no Flickr Gallery for this either... see below)
                  $photo_collection_markup = '<div class="row">';
                  foreach($photo_collection as $photo) {
                     $caption_link_text = $photo['photo_caption_link_text'] ? $photo['photo_caption_link_text'] : '';
                     $caption_markup = '';
                     if($caption_link_text) {
                        $caption_markup = '<div class="photo-caption">';
                        $caption_link_url = $photo['photo_caption_link_url'] ? $photo['photo_caption_link_url'] : '';
                        if($caption_link_url) {
                           $caption_markup .= '<p><a href="'.$caption_link_url.'" target="_blank" title="'.$caption_link_text.'">'.$caption_link_text.'</a></p>';
                        } else {
                           $caption_markup .= '<p>'.$caption_link_text.'</p>';
                        }
                        $caption_markup .= '</div>';
                     }
                     $photo_collection_markup .= '<div class="col-sm-4"><div class="photo-square"><a href="'.$photo['external_link'].'" title="View this image on Flickr to download a larger version" target="_blank"><img src="'.$photo['photo_instance'].'" alt="'.$photo['photo_alt_text'].'" /></a></div>'.$caption_markup.'</div>';
                  }
                  $photo_collection_markup .= '</div>';
                  echo $photo_collection_markup;
               }

               $photo_collection_view_more_link_text = get_field('photo_collection_view_more_link_text') ? get_field('photo_collection_view_more_link_text') : 'View More';
               $photo_collection_view_more_link_url = get_field('photo_collection_view_more_link_url');
               if( ($current_slug === 'press-center/photos-videos' || $current_slug === 'press-center/make-photos-videos') && $photo_collection_view_more_link_url) {
                  echo '<div class="pull-right"><a href="'.$photo_collection_view_more_link_url.'" title="'.$photo_collection_view_more_link_text.'" target="_blank">'.$photo_collection_view_more_link_text.' <i class="fas fa-external-link-alt" aria-hidden="true"></i></a></div>';
               }

               $video_collection_header = get_field('video_collection_header');
               if($video_collection_header) {
                  echo '<div class="row"><div class="col-md-12"><h2>'.$video_collection_header.'</h2></div></div>';
               }
               $video_collection_description = get_field('video_collection_description');
               if($video_collection_description) {
                  echo '<div class="row"><div class="col-md-12">'.$video_collection_description.'</div></div>';
               }
            
               $video_collection = get_field('video_collection');
               if($video_collection) {
                  $video_collection_markup = '<div class="row">';
                  foreach($video_collection as $video) {
                     $caption_link_text = $video['video_caption_link_text'] ? $video['video_caption_link_text'] : '';
                     $caption_markup = '';
                     if($caption_link_text) {
                        $caption_markup = '<div class="video-caption">';
                        $caption_link_url = $video['video_caption_link_url'] ? $video['video_caption_link_url'] : '';
                        if($caption_link_url) {
                           $caption_markup .= '<p><a href="'.$caption_link_url.'" target="_blank" title="'.$caption_link_text.'">'.$caption_link_text.'</a></p>';
                        } else {
                           $caption_markup .= '<p>'.$caption_link_text.'</p>';
                        }
                        $caption_markup .= '</div>';
                     }
                     $video_id_match = preg_match('/(?:https\:\/\/youtu\.be\/)([A-Za-z0-9\-\_]{11,12})/i',$video["video_instance"],$video_id);
                     // $video_id[1] (second array element) will be first paranthesized match, i.e. the video ID
                     // This regex will work for the time being, see https://webapps.stackexchange.com/questions/54443/format-for-id-of-youtube-video
                     // TBD add some validation in the authoring side to prevent any issues here?
                     if($video_id_match && $video_id[1]) {
                        $video_collection_markup .= '<div class="col-sm-4"><div class="video-square"><div class="iframe-container"><iframe width="640" height="360" src="https://www.youtube.com/embed/'.$video_id[1].'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe></div></div>'.$caption_markup.'</div>';
                     }
                  }
                  $video_collection_markup .= '</div>';
                  echo $video_collection_markup;
               }
               $video_collection_view_more_link_text = get_field('video_collection_view_more_link_text') ? get_field('video_collection_view_more_link_text') : 'View More';
               $video_collection_view_more_link_url = get_field('video_collection_view_more_link_url');
               if( ($current_slug === 'press-center/photos-videos' || $current_slug === 'press-center/make-photos-videos') && $video_collection_view_more_link_url) {
                  echo '<div class="pull-right"><a href="'.$video_collection_view_more_link_url.'" title="'.$video_collection_view_more_link_text.'" target="_blank">'.$video_collection_view_more_link_text.'  <i class="fas fa-external-link-alt" aria-hidden="true"></i></a></div>';
               }
               
               $asset_card = get_field('asset_card');
               if($asset_card) {
                  $asset_cards_markup = '<div class="row brand-assets-container"><div class="col-sm-12">';
                  foreach($asset_card as $asset) {
                     $button_link = $asset['asset_card_button_action'] === 'external' ? $asset['asset_card_button_url'] : $asset['asset_card_button_file'];
                     $asset_markup = '<div class="asset-card-container">';
                     if($asset['asset_card_image']) {
                        $asset_markup .= '<div class="sample-image"><img src="'.$asset['asset_card_image'].'" /></div>';
                     }
                     if($asset['asset_card_caption']) {
                        $asset_markup .= '<div class="download-caption">'.$asset['asset_card_caption'].'</div>';
                     }
                     $asset_markup .= '</div>';
                     $asset_markup .= '<a class="asset-button" href="'.$button_link.'" title="Download '.$asset['asset_card_button_text'].'" target="_blank">'.$asset['asset_card_button_text'].'</a>';
                     $asset_cards_markup .= $asset_markup;
                  }
                  $asset_cards_markup .= '</div></div>'; // end brand-assets-container
                  echo $asset_cards_markup;
               }
            }


            elseif($layout_type === 'brand_assets') {
               $page_subheader = get_field('page_subheader');
               if($page_subheader) {
                  echo '<h2>'.$page_subheader.'</h2>';
               }
               $intro_text = get_field('intro_text');
               if($intro_text) {
                  echo $intro_text;
               }
               
               $asset_card = get_field('asset_card');
               if($asset_card) {
                  $asset_cards_markup = '<div class="row brand-assets-container"><div class="col-sm-12">';
                  foreach($asset_card as $asset) {
                     $button_link = $asset['asset_card_button_action'] === 'external' ? $asset['asset_card_button_url'] : $asset['asset_card_button_file'];
                     $asset_markup = '<div class="asset-card-container">';
                     if($asset['asset_card_image']) {
                        $asset_markup .= '<div class="sample-image"><img src="'.$asset['asset_card_image'].'" /></div>';
                     }
                     if($asset['asset_card_caption']) {
                        $asset_markup .= '<div class="download-caption">'.$asset['asset_card_caption'].'</div>';
                     }
                     $asset_markup .= '</div>';
                     $asset_markup .= '<a class="asset-button" href="'.$button_link.'" title="Download '.$asset['asset_card_button_text'].'" target="_blank">'.$asset['asset_card_button_text'].'</a>';
                     $asset_cards_markup .= $asset_markup;
                  }
                  $asset_cards_markup .= '</div></div>'; // end brand-assets-container
                  echo $asset_cards_markup;
               }

            }
            the_content();
            ?>
         </div> <!-- end content -->

         <div class="left-hand-nav press-nav col-sm-3 col-sm-pull-9">
            <?php
               wp_nav_menu(array( 'theme_location' => 'press-center-left-hand-nav' ));
            ?>
         </div>

      </div> <!-- end row -->
   </div> <!-- end container -->

</div><!-- end page-leftnav -->

<?php get_footer(); ?>
