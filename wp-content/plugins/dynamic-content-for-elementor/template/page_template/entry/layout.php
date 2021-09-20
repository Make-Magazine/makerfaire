<?php
/**
 * Default post entry layout
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get post format
$format = get_post_format();

// Quote format is completely different
if ( 'quote' == $format ) {

	// Get quote entry content
	include plugin_dir_path( __FILE__ ) . 'entry/quote.php';
	return;
}

// Add classes to the blog entry post class
$classes = oceanwp_post_entry_classes(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>

	<div class="clr">

		<?php
		// Get elements
		$elements = oceanwp_blog_entry_elements_positioning();

		// Loop through elements
		foreach ( $elements as $element ) {

			// Featured Image
			if ( 'featured_image' == $element ) {
				include plugin_dir_path( __FILE__ ) . 'media/blog-entry.php';
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

			// Read more button
			if ( 'read_more' == $element ) {
				include plugin_dir_path( __FILE__ ) . 'readmore.php';
			}
		} ?>

	</div>

</article><!-- #post-## -->
