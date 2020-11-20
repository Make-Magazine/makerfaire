<?php
/*
Template name: Authenticated content
*/
get_header();
if (!is_user_logged_in()) { ?>
<div class="clear"></div>

<div class="page-content container">
	<div class="row">
		<div class="content col-md-12 text-center">
			<h1>You must be logged in to access your Maker Faire Entries</h1>
		</div>
	</div>
</div>
	
<?php } else { ?>

<div class="clear"></div>

<div class="page-content container">

	<div class="row">

		<div class="content col-md-12">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<article <?php post_class(); ?>>

					<?php the_content(); ?>

				</article>

			<?php endwhile; ?>
                	<?php else: ?>

				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

			<?php endif; ?>

		</div><!--Content-->

	</div>

</div><!--Container-->

<?php }
get_footer(); ?>