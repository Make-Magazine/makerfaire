<?php
/*
Template name: Toolkits
*/
get_header(); ?>
<div class="clear"></div>
<div class="main-content" id="main">
  <div class="toolkit-header container-fluid">
	<div class="row">
		<div class="toolkit-header-title col-md-3 col-sm-4 col-xs-12">
			<img src="/wp-content/themes/makerfaire/images/toolkit-icon.png" width="40px" height="40px" />
			<?php the_title( '<h1>', '</h1>' ); ?>
		</div>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<?php	
				if (have_rows('top_tabs')) { 
					echo('<div class="toolkit-tabs col-md-9 col-sm-8 col-xs-12"><ul class="nav nav-tabs">');
					while (have_rows('top_tabs')) {
						the_row();
						echo('<li><a href="' . get_sub_field('tab_link') . '">' . get_sub_field('tab_text') . '</a></li>');
					}
					echo('</ul></div>');
				}
		?>
		<?php endwhile; ?>			
		<?php endif; ?>
	</div>
  </div>
  <div class="page-leftnav container-fluid">
	<div class="row">
      <div class="col-md-3 left-hand-nav">
         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php	
				if (have_rows('sections')) { 
					echo('<ul class="toolkit-nav menu" id="menu-toolkit-left-hand-nav">');
						while (have_rows('sections')) {
							the_row();
							if(get_sub_field('section_type') == "Header") {
								 $leftLinkClass = "section-header";
							}else{
								 $leftLinkClass = "sub-section-header";
							}
							echo('<li class="' . $leftLinkClass . '"><a href="#' .  strtolower(str_replace(" ","-",get_sub_field('header_text'))) . '">' . get_sub_field('header_text') . '</a></li>');
						}
			      echo('</ul>');
				}
			?>
			<?php endwhile; ?>			
			<?php endif; ?>
      </div>
		<div class="content col-md-9">			
         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php	
				if (have_rows('sections')) { 
					echo('<div class="toolkit-section-wrapper">');
						while (have_rows('sections')) {
							the_row();
							if(get_sub_field('section_type')) {
								echo('<div class="toolkit-section image_grid">'); //image grid is here for the list styles until it's made universal
									 echo('<a class="toolkit-anchor" name="' . strtolower(str_replace(" ","-",get_sub_field('header_text'))) . '"></a>');
									 echo('<h2>' .  get_sub_field('header_text') . '</h2>');
									 echo(get_sub_field('section_body'));
								echo('</div>');
							}
						}
			      echo('</div>');
				}
			?>
         <?php endwhile; ?>			
			<?php else: ?>
				<?php get_404_template(); ?>
			<?php endif; ?>
		</div><!--Content-->
	</div>
  </div><!--Container-->
</div>
<?php get_footer(); ?>