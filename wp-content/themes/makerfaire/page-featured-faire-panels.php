<?php
/*
* Template name: Featured Faire Landing Page w/Panels
*/
get_header(); ?>

<div class="page-content featured-faire">

  <?php // theloop
  if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="container">
      <?php the_content(); ?>
    </div>
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