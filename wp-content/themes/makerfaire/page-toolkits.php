<?php
/*
Template name: Toolkits
*/
get_header(); ?>

<div class="clear"></div>
<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
</div><!-- .post-thumbnail -->
<div class="page-leftnav container-fluid">
	<div class="row">
      <div class="left-hand-nav col-md-3">
         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php	
				if (have_rows('sections')) { 
					echo('<ul class="toolkit-nav menu" id="menu-ba-left-hand-nav">');
						while (have_rows('sections')) {
							the_row();
							if(get_sub_field('section_type') == "Header") {
								 echo('<li class="section-header">' . get_sub_field('header_text') . '</li>');
							}else{
								 echo('<li class="sub-section-header">' . get_sub_field('header_text') . '</li>');
							}
						}
			      echo('</ul>');
				}
			?>
			<?php endwhile; ?>			
			<?php else: ?>
				<?php get_404_template(); ?>
			<?php endif; ?>
      </div>
		<div class="content col-md-9">			
         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<?php the_content(); ?>
               <?php get_acf_content(); ?>
				</article>
         <?php endwhile; ?>			
			<?php else: ?>
				<?php get_404_template(); ?>
			<?php endif; ?>
		</div><!--Content-->
	</div>
</div><!--Container-->
<?php get_footer(); ?>