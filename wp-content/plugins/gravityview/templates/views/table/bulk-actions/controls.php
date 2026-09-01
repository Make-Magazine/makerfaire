<?php
/**
 * Bulk action form controls for the table View.
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
<label class="screen-reader-text" for="<?php echo esc_attr( $bulk_actions->action_select_id ); ?>"><?php esc_html_e( 'Bulk action', 'gk-gravityview' ); ?></label>
<select
	id="<?php echo esc_attr( $bulk_actions->action_select_id ); ?>"
	name="<?php echo esc_attr( $bulk_actions->post_action ); ?>"
	class="gv-bulk-actions-select"
	data-gv-bulk-role="action-select"
	data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
	data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
	form="<?php echo esc_attr( $bulk_actions->form_id ); ?>"
	<?php disabled( $bulk_actions->background_job_active ); ?>
>
	<option value=""><?php esc_html_e( 'Bulk Actions', 'gk-gravityview' ); ?></option>
	<?php foreach ( $bulk_actions->actions as $key => $action_config ) : ?>
		<?php $confirmation = $action_config['_confirmation']; ?>
			<option
				value="<?php echo esc_attr( $key ); ?>"
				data-confirm="<?php echo esc_attr( $confirmation['message'] ); ?>"
				data-confirm-enabled="<?php echo esc_attr( $confirmation['enabled'] ? '1' : '0' ); ?>"
				data-confirm-title="<?php echo esc_attr( $confirmation['title'] ); ?>"
				data-confirm-title-singular="<?php echo esc_attr( $confirmation['title_singular'] ); ?>"
				data-confirm-message="<?php echo esc_attr( $confirmation['message'] ); ?>"
				data-confirm-message-singular="<?php echo esc_attr( $confirmation['message_singular'] ); ?>"
				data-confirm-action-label="<?php echo esc_attr( $confirmation['action_label'] ); ?>"
				data-confirm-action-label-singular="<?php echo esc_attr( $confirmation['action_label_singular'] ); ?>"
				data-typed-confirm-enabled="<?php echo esc_attr( ! empty( $action_config['_typed_confirmation_enabled'] ) ? '1' : '0' ); ?>"
				data-typed-confirm-threshold="<?php echo esc_attr( (int) ( $action_config['_typed_confirmation_threshold'] ?? 0 ) ); ?>"
				data-action-data="<?php echo esc_attr( wp_json_encode( $action_config['_frontend_data'] ?? [] ) ); ?>"
			><?php echo esc_html( $action_config['label'] ); ?></option>
		<?php endforeach; ?>
	</select>
<button
	type="submit"
	class="button gv-bulk-actions-apply"
	data-gv-bulk-role="apply"
	data-view-id="<?php echo esc_attr( $bulk_actions->view_id ); ?>"
	data-render-instance="<?php echo esc_attr( $bulk_actions->render_instance_id ); ?>"
	form="<?php echo esc_attr( $bulk_actions->form_id ); ?>"
	disabled
><?php esc_html_e( 'Apply', 'gk-gravityview' ); ?></button>
