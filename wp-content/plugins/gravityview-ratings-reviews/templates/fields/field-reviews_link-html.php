<?php
/**
 * Display the reviews link field type.
 *
 * @package GravityView_Ratings_Reviews
 * @since 0.1.0
 */
global $post;
defined( 'ABSPATH' ) || exit;
?><span class="reviews-link"><?php GravityView_Ratings_Reviews_Helper::the_reviews_link(); ?></span>
