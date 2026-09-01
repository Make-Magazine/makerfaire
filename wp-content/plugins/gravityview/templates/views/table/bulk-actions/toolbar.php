<?php
/**
 * Bulk actions toolbar for the table View.
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
<?php do_action( 'gk/gravityview/bulk-actions/toolbar/before', $bulk_actions, $gravityview ); ?>
<form
	id="<?php echo esc_attr( $bulk_actions->form_id ); ?>"
	method="post"
	action="<?php echo esc_url( $bulk_actions->form_action ); ?>"
	class="gv-bulk-actions"
	data-gv-bulk-role="form"
	data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
	data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
	data-storage-key="<?php echo esc_attr( $bulk_actions->storage_key ); ?>"
	data-clear-selection="<?php echo esc_attr( $bulk_actions->clear_selection ? '1' : '0' ); ?>"
	data-page-entries="<?php echo esc_attr( $bulk_actions->page_count ); ?>"
	data-total-entries="<?php echo esc_attr( $bulk_actions->total_count ); ?>"
	data-can-select-all="<?php echo esc_attr( $bulk_actions->can_select_all ? '1' : '0' ); ?>"
	data-selection-behavior="<?php echo esc_attr( $bulk_actions->selection_behavior ); ?>"
	data-selection-ttl="<?php echo esc_attr( $bulk_actions->selection_ttl ); ?>"
	data-selection-mode-active="<?php echo esc_attr( $bulk_actions->is_selection_mode ? '1' : '0' ); ?>"
	data-show-selected-enabled="<?php echo esc_attr( $bulk_actions->show_selected_enabled ? '1' : '0' ); ?>"
	data-background-job-active="<?php echo esc_attr( $bulk_actions->background_job_active ? '1' : '0' ); ?>"
>
	<?php
	do_action( 'gk/gravityview/bulk-actions/toolbar/before-controls', $bulk_actions, $gravityview );

	$controls_template = $gravityview->template->get_template_part( 'table/bulk-actions/controls', null, false );

	if ( $controls_template ) {
		include $controls_template;
	}

	if ( 'toolbar' === $bulk_actions->selection_summary_position ) {
		?>
		<span
			class="gv-bulk-actions-selection-summary"
			data-gv-bulk-role="selection-summary"
			data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
			data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
			hidden
		>
			<?php
			$summary_template = $gravityview->template->get_template_part( 'table/bulk-actions/selection-summary', null, false );

			if ( $summary_template ) {
				include $summary_template;
			}
			?>
		</span>
		<?php
	}

	$hidden_fields_template = $gravityview->template->get_template_part( 'table/bulk-actions/hidden-fields', null, false );

	if ( $hidden_fields_template ) {
		include $hidden_fields_template;
	}

	do_action( 'gk/gravityview/bulk-actions/toolbar/after-controls', $bulk_actions, $gravityview );
	?>
</form>
<?php do_action( 'gk/gravityview/bulk-actions/toolbar/after', $bulk_actions, $gravityview ); ?>
