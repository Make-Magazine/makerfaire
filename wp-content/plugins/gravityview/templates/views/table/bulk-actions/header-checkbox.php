<?php
/**
 * Bulk action table header checkbox.
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
<th class="gv-bulk-actions-column gv-bulk-actions-column-header" scope="col">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Select all entries on this page', 'gk-gravityview' ); ?></span>
		<input
			type="checkbox"
			class="gv-bulk-actions-toggle-page"
			data-gv-bulk-role="page-toggle"
			data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
			data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
			<?php disabled( $bulk_actions->background_job_active ); ?>
		>
	</label>
</th>
