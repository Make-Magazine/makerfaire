<?php
/**
 * Bulk action selection summary for the table View.
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

$is_table_summary = 'table_body_before' === $bulk_actions->selection_summary_position;

if ( $bulk_actions->can_select_all ) {
	$select_all_label = strtr(
		/* translators: [count] is the total number of entries available across all pages. */
		_n( 'Select all [count]', 'Select all [count]', $bulk_actions->total_count, 'gk-gravityview' ),
		[
			'[count]' => number_format_i18n( $bulk_actions->total_count ),
		]
	);

	$select_all_aria_label = strtr(
		/* translators: [count] is the total number of entries available across all pages. */
		_n( 'Select all [count] entry.', 'Select all [count] entries.', $bulk_actions->total_count, 'gk-gravityview' ),
		[
			'[count]' => number_format_i18n( $bulk_actions->total_count ),
		]
	);

	$all_selected_label = strtr(
		/* translators: [count] is the total number of entries selected across all pages. */
		_n( 'All [count] selected.', 'All [count] selected.', $bulk_actions->total_count, 'gk-gravityview' ),
		[
			'[count]' => number_format_i18n( $bulk_actions->total_count ),
		]
	);
}

?>
<span
	class="gv-bulk-actions-count"
	data-gv-bulk-role="selected-count"
	data-gv-bulk-display="<?php echo esc_attr( $is_table_summary ? 'page-selection' : 'selection' ); ?>"
	data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
	data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
	aria-live="polite"
	hidden
></span>
<?php if ( $bulk_actions->can_select_all ) : ?>
	<span
		class="gv-bulk-actions-page-selection"
		data-gv-bulk-role="page-selection"
		data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
		data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
		hidden
	>
		<button
			type="button"
			class="gv-bulk-actions-select-all"
			data-gv-bulk-role="select-all"
			data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
			data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
			aria-label="<?php echo esc_attr( $select_all_aria_label ); ?>"
		>
			<?php echo esc_html( $select_all_label ); ?>
		</button>
	</span>
	<span
		class="gv-bulk-actions-all-selected"
		data-gv-bulk-role="all-selected"
		data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
		data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
		hidden
	>
		<?php echo esc_html( $all_selected_label ); ?>
	</span>
<?php endif; ?>
<?php if ( $bulk_actions->show_selected_enabled ) : ?>
	<button
		type="button"
		class="gv-bulk-actions-show-selected"
		data-gv-bulk-role="show-selected"
		data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
		data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
		hidden
	><?php esc_html_e( 'Show selected', 'gk-gravityview' ); ?></button>
	<a
		class="gv-bulk-actions-show-all"
		data-gv-bulk-role="show-all"
		data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
		data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
		href="<?php echo esc_url( $bulk_actions->show_all_url ); ?>"
		<?php echo $bulk_actions->is_selection_mode ? '' : 'hidden'; ?>
	><?php esc_html_e( 'Show all', 'gk-gravityview' ); ?></a>
<?php endif; ?>
<button
	type="button"
	class="gv-bulk-actions-clear"
	data-gv-bulk-role="clear"
	data-gv-bulk-display="<?php echo esc_attr( $is_table_summary ? 'page-selection' : 'selection' ); ?>"
	data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
	data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
	hidden
><?php esc_html_e( 'Clear', 'gk-gravityview' ); ?></button>
