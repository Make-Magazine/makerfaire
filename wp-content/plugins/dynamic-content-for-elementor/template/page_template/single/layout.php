<?php
/**
 * Single post layout
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<article id="post-<?php the_ID(); ?>" itemprop="portfolioPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
	<?php
	// Get posts format
	$format = get_post_format();

	// Get elements
	$elements = oceanwp_blog_single_elements_positioning();

	// Loop through elements
	foreach ( $elements as $element ) {

		// Featured Image
		if ( 'featured_image' == $element
			&& ! post_password_required() ) {
			$format = $format ? $format : 'thumbnail';

			get_template_part( 'partials/single/media/blog-single', $format );
		}

		// Title
		if ( 'title' == $element ) {
			include plugin_dir_path( __FILE__ ) . 'header.php';
		}

		// Meta
		if ( 'meta' == $element ) {
			include plugin_dir_path( __FILE__ ) . 'meta.php';
		}
		// Content
		if ( 'content' == $element ) {
			include plugin_dir_path( __FILE__ ) . 'content.php';
		}



		// Social Share
		if ( 'social_share' == $element
			&& OCEAN_EXTRA_ACTIVE ) {
			do_action( 'ocean_social_share' );
		}

		// Next/Prev
		if ( 'next_prev' == $element ) {
			include plugin_dir_path( __FILE__ ) . 'next-prev.php';
		}

		// Author Box
		if ( 'author_box' == $element ) {
			include plugin_dir_path( __FILE__ ) . 'author-bio.php';
		}

		// Related Posts
		if ( 'related_posts' == $element ) {
			include plugin_dir_path( __FILE__ ) . 'related-posts.php';
		}

		// Comments
		if ( 'single_comments' == $element ) {
			comments_template();
		}
	} ?>

</article>
