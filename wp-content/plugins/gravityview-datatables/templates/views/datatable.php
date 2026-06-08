<?php
/**
 * @global \GV\Template_Context $gravityview
 */

ob_start();

$gravityview->template->get_template_part( 'datatable/datatable', 'header' );
$gravityview->template->get_template_part( 'datatable/datatable', 'footer' );

$content = ob_get_clean();

$anchor_id = $gravityview->view->get_anchor_id();

/**
 * Modify the wrapper container HTML markup used to render the View.
 *
 * @since 2.7
 *
 * @param string   $wrapper_container Wrapper container HTML markup.
 * @param string   $anchor_id         Unique anchor ID to identify the View.
 * @param \GV\View $view              The View being rendered.
 */
$wrapper_container = apply_filters(
	'gravityview/view/wrapper_container',
	'<div id="' . esc_attr( $anchor_id ) . '" class="gv-template-datatables">{content}</div>',
	$anchor_id,
	$gravityview->view
);

echo $wrapper_container ? str_replace( '{content}', $content, $wrapper_container ) : $content;
