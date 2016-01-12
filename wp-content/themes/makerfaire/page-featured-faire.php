<?php
/*
* Template name: Featured Faire Landing Page
*/
get_header(); ?>

<div class="container featured-faire-landing">
  <div class="row padbottom">
    <div class="col-xs-12">
      <h1 class="pull-left"><?php echo get_the_title(); ?> </h1>
      <h2 class="featured-faire-subhead text-muted"><small><?php the_content(); ?></small></h2>
    </div>
  </div>

  <div class="row padbottom">
    
    <?php if( have_rows('featured_faires') ):
     
        while( have_rows('featured_faires') ): the_row();

          if( ! get_sub_field('past_event') ):
              
            $sub_field_1 = get_sub_field('faire_title'); //Title
            $sub_field_2 = get_sub_field('faire_url'); //URL
            $sub_field_3 = get_sub_field('faire_photo'); //Photo
            $sub_field_4 = get_sub_field('faire_date'); //Date
            
            echo '<div class="col-xs-12 col-sm-6 col-md-4">';
            echo '<div class="featured-faire-box">';
            if( get_sub_field('faire_url') ):
              echo '<a href="' . $sub_field_2 . '">';
            endif;
            echo '<img src="' . $sub_field_3['url'] . '" alt="Featured Maker Faire Image" class="img-responsive" />';
            echo '<p class="featured-faire-above-title">Maker Faire</p>';
            echo '<h3 class="featured-faire-title">' . $sub_field_1 . '</h3>';
            echo '<h4 class="featured-faire-date">' . $sub_field_4 . '</h4>';
            echo '<div class="clearfix"></div>';
            if( get_sub_field('faire_url') ):
              echo '</a>';
            endif;
            echo '</div>';
            echo '</div>';

          endif;
            
        endwhile; ?>
      
    <?php endif; ?>

  </div>


  <div class="row padtop padbottom">
    <div class="col-xs-12 padtop">
      <h2 class="featured-faire-past-event">Past Events</h2>
      <hr />
    </div>
    
    <?php if( have_rows('featured_faires') ):
     
        while( have_rows('featured_faires') ): the_row();

          if( get_sub_field('past_event') ):
              
            $sub_field_1 = get_sub_field('faire_title'); //Title
            $sub_field_2 = get_sub_field('faire_url'); //URL
            $sub_field_3 = get_sub_field('faire_photo'); //Photo
            $sub_field_4 = get_sub_field('faire_date'); //Date
            
            echo '<div class="col-xs-12 col-sm-6 col-md-4">';
            echo '<div class="featured-faire-box">';
            if( get_sub_field('faire_url') ):
              echo '<a href="' . $sub_field_2 . '">';
            endif;
            echo '<img src="' . $sub_field_3['url'] . '" alt="Featured Maker Faire Image" class="img-responsive" />';
            echo '<p class="featured-faire-above-title">Maker Faire</p>';
            echo '<h3 class="featured-faire-title">' . $sub_field_1 . '</h3>';
            echo '<h4 class="featured-faire-date">' . $sub_field_4 . '</h4>';
            echo '<div class="clearfix"></div>';
            if( get_sub_field('faire_url') ):
              echo '</a>';
            endif;
            echo '</div>';
            echo '</div>';

          endif;
            
        endwhile; ?>
      
    <?php endif; ?>

  </div>


</div>

<?php get_footer(); ?>