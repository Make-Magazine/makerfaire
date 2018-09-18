<?php
/**
 * Template Name: Home page
 */
get_header();
?>
<main id="main" class="front-page" role="main">
   <!-- Custom Panels -->
   <div class="page-content featured-faire customPanels">
      <?php 

      // Are there custom panels to display?
      if( have_rows('content_panels')) {
         // loop through the rows of data      
         while ( have_rows('content_panels') ) {
           the_row();
           $row_layout = get_row_layout();           
           echo dispLayout($row_layout);
         }
      }

      ?>
      
   </div>
   
   <!-- static section -->
   <div class="location-holder">
      <div class="container">
         <div class="picture-holder">
            <img alt="image description" height="74" src="/wp-content/uploads/2015/04/maker-robot.png" width="53">
         </div>
         <a href="/map/">There are Maker Faires all over the world. Find one near you! <i class="icon-arrow-right"></i></a>
      </div>
   </div>
   
   <div class="container">
      <!-- Makerfaire news section -->
      <div class="mf-news">
         <div class="row">
            <div class="col-xs-12">
               <p class="see-all pull-right"><?php echo get_field("mf_news_title"); ?></p>
            </div>
         </div>
         <?php echo do_shortcode("[mf-news]"); ?>
      </div>
   </div>

   <?php $social_hashtags = get_field("social_hashtags"); ?>
   <div class="container mf-sumome">
      <h2>Share Your Maker Faire Experience</h2>
      <h4><?php echo $social_hashtags;?> on
         <a href="https://twitter.com/makerfaire"><i aria-hidden="true" class="fa fa-twitter"></i></a>
         <a href="https://www.instagram.com/makerfaire/"><i aria-hidden="true" class="fa fa-instagram"></i></a>
         <a href="https://www.facebook.com/makerfaire"><i class="fa fa-facebook" aria-hidden="true"></i></a></h4>
      <div class="clearfix"></div>
      <script async src="https://d36hc0p18k1aoc.cloudfront.net/public/js/modules/tintembed.js"></script>
      <div class="tintup" data-columns="" data-id="makerfaire" data-infinitescroll="true" data-mobilescroll="true" data-personalization-id="764268" style="height:600px;width:100%;"></div><!-- END TINT SCRIPT -->
   </div>

   <div class="question-holder">
      <div class="container">
         <div class="row">
            <div class="col-xs-12 col-lg-10 col-lg-offset-1">
               <div class="picture-holder"><img alt="image description" height="71" src="/wp-content/uploads/2015/04/maker-robot-textbox.png" width="66"></div>               
               <h1>What is Maker Faire?</h1>We call it the Greatest Show (& Tell) on Earth. As a celebration of the Maker Movement, it’s a family-friendly showcase of invention and creativity that gathers together tech enthusiasts, crafters, educators, tinkerers, food artisans, hobbyists, engineers, science clubs, artists, students, and commercial exhibitors. Makers come to show their creations. Attendees come to glimpse the future...and to learn to become makers themselves.
            </div>
         </div>
      </div>
   </div>

</main>
<?php get_footer(); ?>