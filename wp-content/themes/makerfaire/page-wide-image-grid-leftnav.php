<?php
/*
Template name: Wide Image Grid Template
*/
get_header(); ?>

<div class="clear"></div>
<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
</div><!-- .post-thumbnail -->
<div class="page-leftnav container-fluid" style="margin-bottom: 30px;">
	<div class="row">
		<div class="content col-md-12">			
         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<?php the_content(); 
                     get_acf_content(); 
					      if(!empty(get_field('second_block'))){
						   	echo( '<div class="second-block">' . get_field('second_block') . '</div>' ); 
							}
					      ?>
				</article>
         <?php endwhile; ?>			
			<?php else: ?>
				<?php get_404_template(); ?>
			<?php endif; ?>
		</div><!--Content-->
	</div>
</div><!--Container-->
<?php get_footer(); ?>