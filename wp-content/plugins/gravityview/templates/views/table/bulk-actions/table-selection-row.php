<?php
/**
 * Bulk action selection summary row for the table View.
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
<tr
	class="gv-bulk-actions-selection-row"
	data-gv-bulk-role="selection-summary"
	data-gv-bulk-display="page-selection"
	data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
	data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
	hidden
>
	<td colspan="<?php echo esc_attr( $bulk_actions->column_count ? $bulk_actions->column_count : 1 ); ?>">
		<div class="gv-bulk-actions-selection-summary">
			<?php
			$summary_template = $gravityview->template->get_template_part( 'table/bulk-actions/selection-summary', null, false );

			if ( $summary_template ) {
				include $summary_template;
			}
			?>
		</div>
	</td>
</tr>
