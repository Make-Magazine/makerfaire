<?php
/*
Template name: Press Center Template w/Left Nav
*/
get_header();
$layout_type = get_field('layout_type');
$layout_class_name = str_replace('_','-',$layout_type); // replace underscores with dashes for class name
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
                     <p>Maker Faire : <a href="mailto:pr@makerfaire.com">pr@makerfaire.com</a></p>
                     <p>Make : <a href="mailto:pr@makerfaire.com">pr@makerfaire.com</a></p>
                  </span>
               </header>
            </div>
         </div>
      </div>
   </div>

   <div class="container">
      <div class="row">
         <div class="left-hand-nav press-nav col-sm-3">
            <?php           
               $displayNav = get_field('display_left_nav');
               if($displayNav){
                  $template_to_display = get_field('template_to_display');               
                  wp_nav_menu( array( 'theme_location' => $template_to_display ) );
               }
            ?>
         </div>
         <div class="<?php echo $content_class; ?>">
<?php 

if($layout_type === 'wysiwyg') {
   echo get_field('freeform_content');
}

elseif($layout_type === 'press_releases') {
   $release_collection = get_field('press_release_collection');
   if($release_collection) {
      foreach($release_collection as $group) {
         echo '<h2>' . $group["press_release_collection_group_header"] . '</h2>';
         foreach($group["press_release_collection_group"] as $release) {
            $release_text = $release['release_date'] ? $release['release_date'] . ' &mdash; ' : '';
            $release_text .= '<a href="'.$release['release_link'].'">' . $release['release_link_text'] . '</a>';
            $release_text .= $release['release_source'] ? ' - ' . $release['release_source'] : '';
            echo '<div class="press-release-instance">'.$release_text.'</div>';
         }
      }
   }
} 

elseif($layout_type === 'photo_video') {
   $header_image = get_field('header_image');
   if($header_image) {
      echo '<img class="page-header-image" src="'.$header_image.'" />';
   }

   $photo_collection = get_field('photo_collection');
   if($photo_collection) {
      // NOTE (ts): no photos for Make: in design, so no need for header unless there's actually photos to show (especially since there's no Flickr Gallery for this either... see below)
      echo '<div class="row"><div class="col-md-12"><h2>Photos</h2></div></div>';
      //var_dump($photo_collection);
      $count = 0;
      echo '<div class="row">';
      foreach($photo_collection as $photo) {
         echo '<div class="col-md-4 col-sm-6"><div class="photo-square"><a href="'.$photo['external_link'].'" title="View this image on Flickr to download a larger version" target="_blank"><img src="'.$photo['photo_instance'].'" alt="'.$photo['photo_alt_text'].'" /></a></div></div>';
         $count++;
         if($count % 3 === 0) {
            echo '</div><div class="row">';
         }
      }
      echo '</div>';
   }
   if($current_slug === 'press-center/photos-videos') {
      echo '<div class="pull-right"><a href="https://www.flickr.com/photos/146635418@N02/albums/with/72157677029830411" title="View our Flickr Galleries" target="_blank">View our Flickr Galleries <i class="fa fa-external-link" aria-hidden="true"></i></a></div>';
   } // TBD (ts): no link yet for Make: photos, but when there is add here in an else/elseif

   // NOTE (ts): Because we show the footer YouTube link for either page here, we show the header too
   echo '<div class="row"><div class="col-md-12"><h2>Videos</h2></div></div>';
 
   $video_collection = get_field('video_collection');
   if($video_collection) {
      //var_dump($video_collection);
      $count = 0;
      echo '<div class="row">';
      foreach($video_collection as $video) {
         $video_id_match = preg_match('/(?:https\:\/\/youtu\.be\/)([A-Za-z0-9\-\_]{11,12})/i',$video["video_instance"],$video_id);
         // $video_id[1] (second array element) will be first paranthesized match, i.e. the video ID
         // This regex will work for the time being, see https://webapps.stackexchange.com/questions/54443/format-for-id-of-youtube-video
         // TBD add some validation in the authoring side to prevent any issues here?
         if($video_id_match && $video_id[1]) {
            echo '<div class="col-md-4 col-sm-6"><div class="video-square"><iframe width="262" height="240" src="https://www.youtube.com/embed/'.$video_id[1].'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe></div></div>';
            $count++;
         }
         if($count % 3 === 0) {
            echo '</div><div class="row">';
         }
      }
      echo '</div>';
   }
   if($current_slug === 'press-center/photos-videos') {
      echo '<div class="pull-right"><a href="https://www.youtube.com/channel/UCN3c64s76jBT3yPO_o1BZtA" title="View Maker Faire&rsquo;s Channel on YouTube" target="_blank">View Maker Faire&rsquo;s Channel on YouTube  <i class="fa fa-external-link" aria-hidden="true"></i></a></div>';
   }
   elseif($current_slug === 'press-center/make-photos-videos') {
      echo '<div class="pull-right"><a href="https://www.youtube.com/user/makemagazine" title="View Make: Magazine&rsquo;s Channel on YouTube" target="_blank">View Make: Magazine&rsquo;s Channel on YouTube  <i class="fa fa-external-link" aria-hidden="true"></i></a></div>';
   }
   

}


elseif($layout_type === 'brand_assets') {
   $header_image = get_field('header_image');
   if($header_image) {
      echo '<img class="page-header-image" src="'.$header_image.'" />';
   }
   $page_subheader = get_field('page_subheader');
   if($page_subheader) {
      echo '<h2>'.$page_subheader.'</h2>';
   }
   $intro_text = get_field('intro_text');
   if($intro_text) {
      echo $intro_text;
   }
   $download_buttons = get_field('download_buttons');
   echo '<div class="row"><div class="col-sm-6">';

   if($download_buttons) {
      foreach($download_buttons as $button) {
         $button_markup = '<div class="download-button-container"><a class="btn btn-default" href="'.$button['downloadable_file'].'" title="Download '.$button['button_text'].'" target="_blank">'.$button['button_text'].' <i class="fa fa-download" aria-hidden="true"></i></a></div>';
         echo $button_markup;
      }
   }
   echo '</div><div class="col-sm-6">';

   $example_images = get_field('example_images');
   if($example_images) {
      foreach($example_images as $image) {
         $image_markup = '<div><img src="'.$image['image_instance'].'" />';
         echo $image_markup;
      }
   }

   echo '</div></div>';

}
?>
         </div><!--Content-->
      </div> <!-- end row -->
   </div> <!-- end container -->

</div><!--end page-leftnav-->

<?php get_footer(); ?>
