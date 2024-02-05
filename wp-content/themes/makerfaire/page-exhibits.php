<?php
/**
 * Template Name: Bay Area 2013 Makers
 */
get_header(); ?>

<div class="clear"></div>

<div class="container">

	<div class="row">

		<div class="content col-md-8">

			<div class="page-header">

				<h1><?php the_title(); ?> <small>Maker Faire Bay Area 2013</small></h1>

			</div>

			<form role="search" method="get" class="form-search" id="searchform" action="<?php echo home_url( '/' ); ?>">
				<input type="text" value="<?php echo get_search_query( true ); ?>" name="s" id="s" class="input-medium search-query" />
				<input type="hidden" name="post_type" value="mf_form" />
				<input type="hidden" name="faire" value="maker-faire-bay-area-2013" />
				<input type="submit" id="searchsubmit" class="btn btn-primary" value="Search" />
			</form>

			<?php
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args = array(
				'post_type'		=> 'mf_form',
				'post_status'	=> 'accepted',
				'orderby' 		=> 'title',
				'order'			=> 'asc',
				'posts_per_page'=> 20,
				'paged'			=> $paged,
				'faire'			=> 'maker-faire-bay-area-2013',
			);
			$my_query = new WP_Query($args);

			if($my_query->have_posts()):
				while($my_query->have_posts()):$my_query->the_post();
					the_mf_content();
					echo '<hr>';
				endwhile;
			?>
			<ul class="pager">
				<li class="previous"><?php previous_posts_link('&larr; Previous Page', $my_query->max_num_pages); ?></li>
				<li class="next"><?php next_posts_link('Next Page &rarr;', $my_query->max_num_pages); ?></li>
			</ul>
			<?php endif; ?>
			<?php wp_reset_query(); ?>


		</div><!--Content-->

		<?php get_sidebar(); ?>

	</div>

</div><!--Container-->

<?php get_footer(); ?>