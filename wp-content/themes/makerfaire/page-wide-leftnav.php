<?php
/*
Template name: Wide Template w/Left Nav
*/
get_header(); ?>

<div class="clear"></div>
<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
</div><!-- .post-thumbnail -->
<div class="">

	<div class="row">
      <div class="content col-md-2 left-hand-nav">
         <?php           
            $displayNav = get_field('display_left_nav');
            
            if($displayNav){
               $template_to_display = get_field('template_to_display');               
               wp_nav_menu( array( 'theme_location' => $template_to_display ) );
            }
         ?>
      </div>
		<div class="content col-md-10">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<article <?php post_class(); ?>>

					<?php the_content(); ?>

				</article>

			<?php endwhile; ?>

				<ul class="pager">

					<li class="previous"><?php previous_posts_link('&larr; Previous Page'); ?></li>
					<li class="next"><?php next_posts_link('Next Page &rarr;'); ?></li>

				</ul>

			<?php else: ?>

				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

			<?php endif; ?>

		</div><!--Content-->

	</div>

</div><!--Container-->

<?php get_footer(); ?>