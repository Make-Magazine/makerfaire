<?php
/*
Template name: Wide Image Grid Template w/Left Nav
*/
get_header(); ?>

<div class="clear"></div>
<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
</div><!-- .post-thumbnail -->
<div class="page-leftnav container-fluid">
	<div class="row">
      <div class="left-hand-nav col-md-3">
         <?php    
         //display left hand nav?
            $displayNav = get_field('display_left_nav');
            
            if($displayNav){
               $template_to_display = get_field('template_to_display');               
               wp_nav_menu( array( 'theme_location' => $template_to_display ) );
            }
         ?>
      </div>
		<div class="content col-md-9">			
         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<?php the_content(); 
                     get_acf_content(); 
						   echo get_field('second_block'); ?>
				</article>
         <?php endwhile; ?>			
			<?php else: ?>
				<?php get_404_template(); ?>
			<?php endif; ?>
		</div><!--Content-->
	</div>
</div><!--Container-->
<?php get_footer(); ?>