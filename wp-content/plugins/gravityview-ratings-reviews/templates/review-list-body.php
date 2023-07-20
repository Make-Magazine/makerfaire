<?php
/**
 * The template for displaying review list body.
 *
 * The global post in current context refers to the post bridge.
 *
 * @package GravityView_Ratings_Reviews
 * @since 0.1.0
 */
global $post, $gravityview_view;

defined( 'ABSPATH' ) || exit;
?>

<div class="gv-review-list-body">
	<ol class="gv-review-list comment-list">
		<?php

			wp_list_comments(
				array(
					'style'       => 'ol',
					'avatar_size' => 34,
					'walker'      => new GravityView_Ratings_Reviews_Review_Walker( $gravityview_view ),
					'max_depth'   => 2,
					'type'        => 'all',
				),
				GravityView_Ratings_Reviews_Helper::get_reviews( $post )
			);
		?>
	</ol><!-- /.gv-review-list -->
</div><!-- /.gv-review-list-body -->
