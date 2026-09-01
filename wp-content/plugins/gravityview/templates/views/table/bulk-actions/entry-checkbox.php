<?php
/**
 * Bulk action table entry checkbox.
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
<td class="gv-bulk-actions-column" data-label="<?php echo esc_attr_x( 'Select', 'Bulk actions table column label', 'gk-gravityview' ); ?>">
	<label>
		<span class="screen-reader-text">
			<?php
			echo esc_html(
				strtr(
					/* translators: [entry_id] is the ID of the entry being selected. */
					__( 'Select entry [entry_id]', 'gk-gravityview' ),
					[
						'[entry_id]' => $bulk_actions->entry_id,
					]
				)
			);
			?>
		</span>
		<input
			type="checkbox"
			class="gv-bulk-actions-entry"
			value="<?php echo esc_attr( $bulk_actions->entry_id ); ?>"
			data-gv-bulk-role="entry-checkbox"
			data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
			data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
			<?php disabled( $bulk_actions->background_job_active ); ?>
		>
	</label>
</td>
