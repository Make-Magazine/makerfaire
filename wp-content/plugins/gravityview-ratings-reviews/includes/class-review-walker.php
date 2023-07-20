<?php
/**
 * Review Walker.
 *
 * @package   GravityView_Ratings_Reviews
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

class GravityView_Ratings_Reviews_Review_Walker extends Walker_Comment {

	/**
	 * @var GravityView_View
	 */
	private $gv_obj;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param \GravityView_View $gv_obj
	 */
	public function __construct( $gv_obj ) {
		$this->gv_obj = $gv_obj;
	}

	/**
	 * Output a single comment.
	 *
	 * @since 0.1.0
	 *
	 * @access protected
	 *
	 * @see wp_list_comments()
	 *
	 * @param object $comment Comment to display.
	 * @param int    $depth   Depth of comment.
	 * @param array  $args    An array of arguments.
	 *
	 * @return void
	 */
	protected function comment( $comment, $depth, $args ) {
		global $gv_ratings_reviews;

		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		}

		$review_rating_type = $this->gv_obj->atts['entry_review_type'];

		$template = $this->gv_obj->locate_template( 'review-item.php' );

		// We load here to enable $this scope in the template
		include $template;
	}

	/**
	 * Output a comment in the HTML5 format.
	 *
	 * @since 0.1.0
	 *
	 * @access protected
	 *
	 * @see wp_list_comments()
	 *
	 * @param object $comment Comment to display.
	 * @param int    $depth   Depth of comment.
	 * @param array  $args    An array of arguments.
	 *
	 * @return void
	 */
	protected function html5_comment( $comment, $depth, $args ) {
		$this->comment( $comment, $depth, $args );
	}
}
