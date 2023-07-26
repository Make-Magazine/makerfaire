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

//process tasks when an entry is updated.
add_action('gform_after_submission', 'processTasks', 10, 2 ); //$entry, $form
add_action('gform_after_update_entry', 'processTasks_setup', 10, 2 ); //$form, $entry_id
add_action('gform_post_add_entry', 'processTasks', 10, 2 ); //$entry, $form

function processTasks_setup($form, $entry_id) {
  //need to set $entry and reset $form as gravity view removes admin only fields
  $entry = GFAPI::get_entry(esc_attr($entry_id));
  $form = GFAPI::get_form($entry['form_id']);
  processTasks( $entry, $form);
}

/*
 * Function to proces task rules and assign tasks to entries
 */
function processTasks( $entry, $form) {
  if(isset($form['tasks'])){
    global $wpdb;
    $tasks   = $form['tasks'];
    $form_id = $form['id'];
		foreach ( $tasks as $task ) {
      $lead_id = $entry['id'];
      $taskID   = $form_id.'-'.$task['id'];
      if ( GFCommon::evaluate_conditional_logic( $task['conditionalLogic'], $form, $entry ) ) {
        //Master forms use a EP token. all others use the old method
        if($form['form_type']=='Master'){
          $action_URL = $task['form2use'];
          $action_URL .= '?ep_token='.$entry['fg_easypassthrough_token'];
        }else{
          $action_URL = $task['form2use'];
          $action_URL .= '?entry-id='.$lead_id;
          $action_URL .= '&contact-email='.$entry['98'];
          $action_URL .= '&taskID='.$taskID;
        }
        

        //check if this task was previously set, if yes  do nothing
        //if no, set the task
        $sql = 'INSERT INTO wp_mf_entity_tasks '
                . 'set  lead_id="'.$lead_id.'",'
                . '     created=now(), '
                . '     description="'.$task['name'].'", '
                . '     required=1, '
                . '     action_url = "'.$action_URL.'", '
                . '     task_id="'.$taskID.'", '
                . '     form_id='. $task['formID']
             . ' ON DUPLICATE KEY UPDATE description="'.$task['name'].'"';
        $wpdb->get_row($sql);
			}else{
        //check if this task was previously set
        $sql = 'select count(*) from wp_mf_entity_tasks where lead_id = '.$lead_id .' and task_id="'.$taskID.'" and completed is NULL';
        $count = $wpdb->get_var($sql);
        //if found, delete
        if($count>0){
          $sql = 'delete from wp_mf_entity_tasks where lead_id = '.$lead_id .' and task_id="'.$taskID.'"';
          $wpdb->get_row($sql);
        }
      }
		}
  }
}

// add task ID to form to be used to mark tasks as completed
//add_filter( 'gform_pre_validation', 'mf_add_taskid' );
add_filter( 'gform_pre_render',  'mf_add_taskid' );

function mf_add_taskid( $form ) {
  $taskID = '';
  if(isset($_GET['taskID']))   $taskID = $_GET['taskID'];
  if(isset($_POST['taskID']))  $taskID = $_POST['taskID'];
  //if task ID is passed add this field to the form.
  if($taskID!='') {
    $props = array(
      'id'        => '9999',
      'label'     => 'Task ID',
      'type'      => 'text',
      'defaultValue'     => $taskID
    );

    $field = GF_Fields::create( $props );
    $field->cssClass = 'hidden';
    array_push( $form['fields'], $field );
  }
	return $form;
};

add_filter('gform_after_submission', 'maybeCompleteTasks', 10, 2 ); //$entry, $form
function maybeCompleteTasks ($entry, $form) {
  global $wpdb;

  //is this an additional form?
  $entryID       = get_value_by_label('entry-id', $form);
  if (!empty($entryID)) {
    global $wpdb;
    $sql = "";

    //let's check if this form has been assigned to the entry
    $entryid   = $entry[$entryID['id']]; //original entry id

    //was a task ID set?
    $taskID = '';
    if(isset($_GET['taskID']))   $taskID = $_GET['taskID'];
    if(isset($_POST['taskID']))  $taskID = $_POST['taskID'];
    //if task ID is passed, mark task as complete
    if($taskID!='') {
      //mark task as completed
      $sql = "UPDATE `wp_mf_entity_tasks` SET `completed`=now() WHERE lead_id = '".$entryid."' and task_id='".$taskID."'";
    }else{
      //check if this form has been assigned to the entry
      $sql = "UPDATE `wp_mf_entity_tasks` SET `completed`=now() WHERE lead_id = '".$entryid."' and form_id='".$form['id']."'";
    }
    if($sql!='') $wpdb->get_row($sql);
  }
}
