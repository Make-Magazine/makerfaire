<?php
/**
 * Bulk action hidden form fields for the table View.
 *
 * @global \GV\Template_Context $gravityview
 * @var object                  $bulk_actions Bulk actions template data.
 *
 * @since 3.0.0
 */

if ( ! isset( $gravityview, $bulk_actions ) || empty( $gravityview->template ) ) {
	gravityview()->log->error( '{file} template loaded without context', array( 'file' => __FILE__ ) );
	return;
}

?>
<input type="hidden" name="<?php echo esc_attr( $bulk_actions->post_entries ); ?>" value="">
<input type="hidden" name="<?php echo esc_attr( $bulk_actions->post_excluded ); ?>" value="">
<input type="hidden" name="<?php echo esc_attr( $bulk_actions->post_select_all ); ?>" value="">
<input type="hidden" name="<?php echo esc_attr( $bulk_actions->post_show_selected ); ?>" value="">
<input type="hidden" name="<?php echo esc_attr( $bulk_actions->post_view_id ); ?>" value="<?php echo esc_attr( $bulk_actions->view_id ); ?>">
<input type="hidden" name="<?php echo esc_attr( $bulk_actions->post_render_instance ); ?>" value="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>">
<input type="hidden" name="<?php echo esc_attr( $bulk_actions->post_nonce ); ?>" value="<?php echo esc_attr( $bulk_actions->nonce ); ?>">
