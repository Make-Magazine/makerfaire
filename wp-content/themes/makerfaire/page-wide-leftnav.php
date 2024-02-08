<?php
/*
Template name: Wide Template (old left nav template)
*/
get_header(); ?>

<div class="clear"></div>
<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
</div><!-- .post-thumbnail -->
<div class="page-leftnav container-fluid<?php if( have_rows('content_panels')) { echo(" customPanels" ); } ?>">
	<div class="row">
		<div class="content col-md-12">			
         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<?php the_content(); ?>
               <?php get_acf_content(); 
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
				</article>
         <?php endwhile; ?>			
			<?php else: ?>
				<?php get_404_template(); ?>
			<?php endif; ?>
		</div><!--Content-->
	</div>
</div><!--Container-->
<?php get_footer(); ?>