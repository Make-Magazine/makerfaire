<?php
/**
 * Template Name: Makers List
 */

// Check what faire taxonomy we need to display
$current_faire_slug = get_post_meta( $post->ID, '_faire-tax-archive', true );
$faire = ( ! empty( $current_faire_slug ) || $current_faire_slug != 'none' ) ? get_term_by( 'slug', sanitize_text_field( $current_faire_slug ), 'faire' ) : '';

get_header(); ?>
<div class="clear"></div>

<div class="container">

	<div class="row">

		<div class="content col-md-8">

			<div class="page-header">

				<h1><?php the_title(); ?> <small><?php echo esc_html( $faire->name ); ?></small></h1>

			</div>

			<form role="search" method="get" class="form-search" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="text" value="<?php echo get_search_query( true ); ?>" name="s" id="s" class="input-medium search-query" />
				<input type="hidden" name="post_type" value="mf_form" />
				<input type="hidden" name="faire" value="<?php echo sanitize_title( $faire->slug ); ?>" />
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
				'paged'			=> intval( $paged ),
				'faire'			=> sanitize_title( $faire->slug ),
			);
                        
			$query = new WP_Query( $args );

			if( $query->have_posts() ):
				while($query->have_posts()):$query->the_post();
					the_mf_content();
					echo '<hr>';
				endwhile;
			?>
			<ul class="pager">
				<li class="previous"><?php previous_posts_link('&larr; Previous Page', intval( $query->max_num_pages ) ); ?></li>
				<li class="next"><?php next_posts_link('Next Page &rarr;', intval( $query->max_num_pages) ); ?></li>
			</ul>
			<?php endif; ?>
			<?php wp_reset_query(); ?>


		</div><!--Content-->

		<?php get_sidebar(); ?>

	</div>

</div><!--Container-->

<?php get_footer(); ?>