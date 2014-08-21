<?php
/**
 * Tag/Category/Faire Archive Page
 * Template Name: Faire Tax Archive Page
 */

// Let's setup some variables, yo.
$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$cat = get_query_var( 'category_name' ) ? sanitize_title_for_query( get_query_var( 'category_name' ) ) : '';
$tag = get_query_var( 'tag' ) ? sanitize_title_for_query( get_query_var( 'tag' ) ) : '';
$tag_obj = wpcom_vip_get_term_by( 'slug', $tag, 'post_tag' );

// Check what faire taxonomy we need to display
$current_faire_slug = get_post_meta( $post->ID, '_faire-tax-archive', true );
$faire = ( ! empty( $current_faire_slug ) || $current_faire_slug != 'none' ) ? wpcom_vip_get_term_by( 'slug', sanitize_text_field( $current_faire_slug ), 'faire' ) : '';

$args = array(
	'post_type'		=> 'mf_form',
	'post_status'	=> 'accepted',
	'orderby' 		=> 'title',
	'order'			=> 'asc',
	'posts_per_page'=> 20,
	'paged'			=> $paged,
  'category_name'	=> $cat,
  'tax_query' => array('relation' => 'AND',
      (!empty($tag)) ? array('taxonomy' => 'post_tag', 'field' => 'slug', 'terms' => array($tag)) : '',
      array('taxonomy' => 'faire', 'field' => 'slug', 'terms' => array(( ! empty( $faire ) ? $faire->slug : '' )))
    )
);
$query = new WP_Query( $args );
get_header(); ?>

<div class="clear"></div>

<div class="container">

	<div class="row">

		<div class="content span8">

			<div class="page-header">

				<h1><?php the_title(); ?> <small><?php echo ( ! empty( $faire ) ) ? esc_html( $faire->name ) : 'All Faires'; ?></small></h1>
				<h2><?php echo esc_html( $tag_obj->name ); ?></h2>
				<ul class="pager">
					<li class="previous"><?php previous_posts_link('&larr; Previous Page', $query->max_num_pages); ?></li>
					<li class="next"><?php next_posts_link('Next Page &rarr;', $query->max_num_pages); ?></li>
				</ul>

			</div>
			<p class="pull-right button"><a href="<?php echo get_permalink( absint( $post->post_parent ) ); ?>maker-info">Back to Meet the Makers</a></p>
			<form role="search" method="get" class="form-search" id="searchform" action="<?php echo home_url( '/' ); ?>">
				<input type="text" value="<?php echo get_search_query( true ); ?>" name="s" id="s" class="input-medium search-query" />
				<input type="hidden" name="post_type" value="mf_form" />
				<input type="hidden" name="faire" value="<?php echo ( ! empty( $current_faire_slug ) || $current_faire_slug != 'none' ) ? esc_attr( $current_faire_slug ) : ''; ?>" />
				<input type="submit" id="searchsubmit" class="btn btn-primary" value="Search" />
			</form>

			<?php

			if( $query->have_posts() ):
				while($query->have_posts()):$query->the_post();
					the_mf_content();
					echo '<hr>';
				endwhile;
			?>
			<ul class="pager">
				<li class="previous"><?php previous_posts_link('&larr; Previous Page', $query->max_num_pages); ?></li>
				<li class="next"><?php next_posts_link('Next Page &rarr;', $query->max_num_pages); ?></li>
			</ul>
			<?php endif; ?>
			<?php wp_reset_query(); ?>


		</div><!--Content-->

		<?php get_sidebar(); ?>

	</div>

</div><!--Container-->

<?php get_footer(); ?>