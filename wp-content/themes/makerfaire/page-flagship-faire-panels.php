<?php
/*
* Template name: Flagship Faire Landing Page w/Panels
*/
get_header(); ?>

<div class="page-content featured-faire customPanels">

  <?php // theloop
  if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <?php the_content(); ?>
    <!-- <div class="container"> -->
   
      <div class="mf-full-hero">
            <div class="mf-left-text">
               <div class="mf-title-text">
                  <h1>Maker Faire Bay Area 2019 <span>May 17-19</span></h1>
               </div>
               <div class="mf-notched-banner-outer">
                  <div class="mf-notched-banner-inner">
                     <h2>Join Us Friday, Saturday &amp; Sunday <span>San Mateo County Event Center</span></h2>
                  </div>
               </div>
            </div>
            <img class="mf-right-hero-img" src="../wp-content/themes/makerfaire/images/hero_image_2019_test.png" alt="">
         </div>

    <!-- </div> -->
    <?php

    // check if the flexible content field has rows of data
    if( have_rows('content_panels')) {
      // loop through the rows of data
      while ( have_rows('content_panels') ) {
        the_row();
        $row_layout = get_row_layout();
        echo dispLayout($row_layout);
      }
    }
    ?>
    <?php wp_link_pages(); ?>
  <?php endwhile; ?>
  <?php else: ?>
    <?php get_404_template(); ?>
  <?php endif; ?>

</div>

<?php get_footer(); ?>