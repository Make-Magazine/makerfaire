<?php
/**
 * @global \GV\Template_Context $gravityview
 */

ob_start();

$gravityview->template->get_template_part( 'datatable/datatable', 'header' );
$gravityview->template->get_template_part( 'datatable/datatable', 'footer' );

$content = ob_get_clean();

/**
 * @filter `gravityview/view/wrapper_container` Modify the wrapper container.
 * @since  2.7
 *
 * @param string   $wrapper_container Wrapper container HTML markup
 * @param string   $anchor_id         (optional) Unique anchor ID to identify the view.
 * @param \GV\View $view              The View.
 */
$wrapper_container = apply_filters(
	'gravityview/view/wrapper_container',
	'<div id="' . esc_attr( $gravityview->view->get_anchor_id() ) . '">{content}</div>',
	$gravityview->view->get_anchor_id(),
	$gravityview->view
);

echo $wrapper_container ? str_replace( '{content}', $content, $wrapper_container ) : $content;


