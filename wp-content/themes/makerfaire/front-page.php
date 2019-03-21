<?php
/**
 * Template Name: Home page
 */
get_header();
?>
<div id="main" class="front-page" role="main">
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
   
   <!-- standard news block -->
   <?php echo do_shortcode('[mf-news newstag="maker-faire" newstitle="Check out the latests News from <em>Make:</em>" newslink="'.htmlentities( get_field("mf_news_title") ).'"]'); ?>

   <?php 
      require_once 'functions/MF-Social-Block.php';
      $social_hashtags = get_field("social_hashtags");
      $args = [
         'title' => '',
         'personalization_id' => '',
         'hashtags' => $social_hashtags
      ];
      echo do_social_block($args);
   ?>

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

</div>
<?php get_footer(); ?>