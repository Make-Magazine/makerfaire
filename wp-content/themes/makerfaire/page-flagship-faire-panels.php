<?php
/*
* Template name: Flagship Faire Landing Page w/Panels
*/
get_header(); ?>

<div class="page-content featured-faire customPanels">

  <?php // theloop
  if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <!-- <div class="container"> -->
   
      <?php the_content(); 

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


<script>
// NOTE (ts): Quick and dirty way of moving the floating Buy Tix button to the subnav
jQuery('document').ready(function() {
   var $tixButton = jQuery('.floatBuyTix').closest('a'),
      $subNav = jQuery('#nav-level-2 .container');
   $tixButton.detach();
   $tixButton.find('.floatBuyTix').removeClass('floatBuyTix').addClass('navBuyTix');
   $subNav.append($tixButton);
});
</script>

<?php get_footer(); ?>