<?php
/**
 * Template Name: Exhibits
 *
 */
get_header(); ?>

<div class="clear"></div>

<div class="container">

	<div class="row">

		<div class="content col-md-8">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<?php
					$content = get_the_content();
					// Adding the Ohm for one Maker... Will probably pull this out at somepoint.
					$json =  stripslashes(urldecode( $content )) ;
					$json = json_decode($json);
				?>

				

			<?php endwhile; ?>

				<ul class="pager">

					<li class="previous"><?php previous_posts_link('&larr; Previous Page'); ?></li>
					<li class="next"><?php next_posts_link('Next Page &rarr;'); ?></li>

				</ul>

			<?php else: ?>

				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

			<?php endif; ?>

		</div><!--Content-->

		<?php get_sidebar(); ?>

	</div>

</div><!--Container-->

<?php get_footer(); ?>