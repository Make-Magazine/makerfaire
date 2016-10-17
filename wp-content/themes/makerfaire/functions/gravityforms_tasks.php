<?php
//task list ajax operations
add_action( 'wp_ajax_mf_update_task_active', 'mf_update_task_active' );

function mf_update_task_active() {
	check_ajax_referer( 'mf_update_task_active', 'mf_update_task_active' );
  $form_id = $_POST['form_id'];
  $task_id = $_POST['task_id'];
  $is_active = $_POST['is_active'];

  $form = GFFormsModel::get_form_meta( $form_id );

  if ( ! isset( $form['tasks'][ $task_id ] ) ) {
    return new WP_Error( 'not_found', __( 'Task not found', 'makerfaire' ) );
  }

  $form['tasks'][ $task_id ]['isActive'] = (bool) $is_active;
  $result = RGFormsModel::update_form_meta( $form_id, $form);

	return $result;
}
