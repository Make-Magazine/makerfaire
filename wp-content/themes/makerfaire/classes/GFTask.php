<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

Class GFTask {

	private static $supported_fields = array(
		'checkbox', 'radio', 'select', 'text', 'website', 'textarea', 'email', 'hidden', 'number', 'phone', 'multiselect', 'post_title',
		'post_tags', 'post_custom_field', 'post_content', 'post_excerpt',
	);

	private static function get_task( $form, $task_id ) {
    if(isset($form['tasks']) && is_array($form['tasks'])){
      foreach ( $form['tasks'] as $id => $task ) {
        if ( $id == $task_id ) {
          return $task;
        }
      }
		}

		return array();
	}

	public static function task_page() {
		$form_id         = rgget( 'id' );
		$task_id = rgget( 'tid' );
		if ( ! rgblank( $task_id ) ) {
			self::task_edit_page( $form_id, $task_id );
		} else {
			self::task_list_page( $form_id );
		}
	}

	public static function task_edit_page( $form_id, $task_id ) {

		if ( ! rgempty( 'gform_task_id' ) ) {
			$task_id = rgpost( 'gform_task_id' );
		}

		$form = RGFormsModel::get_form_meta( $form_id );

		$form = gf_apply_filters( array( 'gform_form_task_page', $form_id ), $form, $task_id );

		$task = ! $task_id ? array() : self::get_task( $form, $task_id );

		// added second condition to account for new tasks with errors as task ID will
		// be available in $_POST but the task has not actually been saved yet
		$is_new_task = empty( $task_id ) || empty( $task );

		$is_valid  = true;
		$is_update = false;
		if ( rgpost( 'save' ) ) {

			check_admin_referer( 'gforms_save_task', 'gforms_save_task' );

			//clear out task because it could have legacy data populated
			$task = array( 'isActive' => isset( $task['isActive'] ) ? rgar( $task, 'isActive' ) : true );

			$is_update = true;

			if ( $is_new_task ) {
				$task_id    = uniqid();
				$task['id'] = $task_id;
			} else {
				$task['id'] = $task_id;
			}

			$task['name']      = sanitize_text_field( rgpost( 'gform_task_name' ) );
			$task['form2use']  = sanitize_text_field( rgpost( 'gform_task_form2use' ) );
      $task['formID']    = sanitize_text_field( rgpost( 'gform_task_formID' ) );

			$conditional_logic  = ! rgempty( 'gform_conditional_logic_meta' ) ? GFCommon::json_decode( rgpost( 'gform_conditional_logic_meta' ), true ) : null;

			$task['conditionalLogic'] = GFFormsModel::sanitize_conditional_logic( $conditional_logic );


			if ( rgpost( 'gform_is_default' ) ) {
				$task['isDefault'] = true;
			}

			//validating input...
			$is_valid = self::validate_task();
			if ( $is_valid ) {
				//input valid, updating...

				// trim values
				$task = GFFormsModel::trim_conditional_logic_values_from_element( $task, $form );

				$form['tasks'][ $task_id ] = $task;
        RGFormsModel::update_form_meta( $form_id, $form);
				//RGFormsModel::save_form_tasks( $form_id, $form['tasks'] );
			}
		}

		if ( $is_update && $is_valid ) {
			$url = remove_query_arg( 'tid' );
			GFCommon::add_message( sprintf( esc_html__( 'Task saved successfully. %sBack to tasks.%s', 'gravityforms' ), '<a href="' . esc_url( $url ) . '">', '</a>' ) );
			gf_do_action( array( 'gform_post_task_save', $form_id ), $task, $form, $is_new_task );
		} else if ( $is_update && ! $is_valid ) {
			GFCommon::add_error_message( esc_html__( 'Task could not be updated. Please enter all required information below.', 'gravityforms' ) );
		}

		// moved page header loading here so the admin messages can be set upon saving and available for the header to print out
		GFFormSettings::page_header( esc_html__( 'Tasks', 'gravityforms' ) );

		$task_ui_settings = self::get_task_ui_settings( $task, $is_valid );

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		?>
		<link rel="stylesheet" href="<?php echo GFCommon::get_base_url() ?>/css/admin<?php echo $min; ?>.css?ver=<?php echo GFCommon::$version ?>" />

		<script type="text/javascript">
    gform.addFilter( 'gform_conditional_logic_description', 'TaskConditionalLogicDesc' );
    gform.addFilter('gform_conditional_object', 'TaskConditionalLogic');
		var gform_has_unsaved_changes = false;
		jQuery(document).ready(function () {
			jQuery("#entry_form input, #entry_form textarea, #entry_form select").change(function () {
				gform_has_unsaved_changes = true;
			});

			window.onbeforeunload = function () {
				if (gform_has_unsaved_changes) {
					return "You have unsaved changes.";
				}
			};

			ToggleConditionalLogic(true, 'task');

		});

		gform.addFilter("gform_merge_tags", "MaybeAddSaveLinkMergeTag");
		function MaybeAddSaveLinkMergeTag(mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option){
			var event = document.getElementById('gform_task_event').value;
			if ( event == 'form_saved' || event == 'form_save_email_requested' ) {
				mergeTags["other"].tags.push({ tag: '{save_link}', label: <?php echo json_encode( esc_html__( 'Save & Continue Link', 'gravityforms' ) ); ?> });
				mergeTags["other"].tags.push({ tag: '{save_token}', label: <?php echo json_encode( esc_html__( 'Save & Continue Token', 'gravityforms' ) ); ?> });
			}

			return mergeTags;
		}

    function TaskConditionalLogicDesc( description, descPieces, objectType, obj ) {
      //set descriptive text in conditional logic to say 'task'
      descPieces.objectDescription = "this task if";
      var descPiecesArr = makeArray( descPieces );

      return descPiecesArr.join(' ');
    }

    function TaskConditionalLogic(object, objectType){
      if(objectType=='task')  object = current_task;
      return object;
    }
		<?php
		if ( empty( $form['tasks'] ) ) {
			$form['tasks'] = array();
		}

		$entry_meta = GFFormsModel::get_entry_meta( $form_id );
		$entry_meta = apply_filters( 'gform_entry_meta_conditional_logic_tasks', $entry_meta, $form, $task_id );

		?>

		var form = <?php echo json_encode( $form ) ?>;
		var current_task = <?php echo GFCommon::json_encode( $task ) ?>;
		var entry_meta = <?php echo GFCommon::json_encode( $entry_meta ) ?>;

		//---------------------- generic ---------------
		function GetSelectableFields(selectedFieldId, labelMaxCharacters) {
			var str = "";
			var inputType;
			for (var i = 0; i < form.fields.length; i++) {
				inputType = form.fields[i].inputType ? form.fields[i].inputType : form.fields[i].type;
				//see if this field type can be used for conditionals
				if (IsTaskConditionalLogicField(form.fields[i])) {
					var selected = form.fields[i].id == selectedFieldId ? "selected='selected'" : "";
					str += "<option value='" + form.fields[i].id + "' " + selected + ">" + form.fields[i].label + "</option>";
				}
			}
			return str;
		}

		function IsTaskConditionalLogicField(field) {
			//this function is a duplicate of IsConditionalLogicField from form_editor.js
			inputType = field.inputType ? field.inputType : field.type;
			var supported_fields = ['checkbox', 'radio', 'select', 'text', 'website', 'textarea', 'email', 'hidden', 'number', 'phone', 'multiselect', 'post_title',
				'post_tags', 'post_custom_field', 'post_content', 'post_excerpt'];

			var index = jQuery.inArray(inputType, supported_fields);

			return index >= 0;
		}

		function GetFirstSelectableField() {
			var inputType;
			for (var i = 0; i < form.fields.length; i++) {
				inputType = form.fields[i].inputType ? form.fields[i].inputType : form.fields[i].type;
				if (IsTaskConditionalLogicField(form.fields[i])) {
					return form.fields[i].id;
				}
			}

			return 0;
		}

		function TruncateMiddle(text, maxCharacters) {
			if (!text)
				return "";

			if (text.length <= maxCharacters)
				return text;
			var middle = parseInt(maxCharacters / 2);
			return text.substr(0, middle) + "..." + text.substr(text.length - middle, middle);

		}

		//---------------------------------------------------------------------------------

		function SetConditionalLogic(isChecked) {
			current_task.conditionalLogic = isChecked ? new ConditionalLogic() : null;
		}

		function SaveJSMeta() {
			jQuery('#gform_conditional_logic_meta').val(jQuery.toJSON(current_task.conditionalLogic));
		}

		<?php GFFormSettings::output_field_scripts() ?>

		</script>

		<form method="post" id="gform_task_form" onsubmit="gform_has_unsaved_changes = false; SaveJSMeta();">

			<?php wp_nonce_field( 'gforms_save_task', 'gforms_save_task' ) ?>
			<?php
			if ( rgar( $task, 'isDefault' ) ) {
				echo '<input type="hidden" id="gform_is_default" name="gform_is_default" value="1"/>';
			}

			?>
			<input type="hidden" id="gform_conditional_logic_meta" name="gform_conditional_logic_meta" />
			<input type="hidden" id="gform_task_id" name="gform_task_id" value="<?php echo esc_attr( $task_id ) ?>" />

			<table class="form-table gform_task_edit">
				<?php array_map( array( 'GFFormSettings', 'output' ), $task_ui_settings ); ?>
			</table>

			<p class="submit">
				<?php
				$button_label = $is_new_task ? __( 'Save Task', 'gravityforms' ) : __( 'Update Task', 'gravityforms' );
				$task_button = '<input class="button-primary" type="submit" value="' . esc_attr( $button_label ) . '" name="save"/>';
				echo apply_filters( 'gform_save_task_button', $task_button );
				?>
			</p>
		</form>

		<?php

		GFFormSettings::page_footer();

	}

	public static function task_list_page( $form_id ) {

		// handle form actions
		self::maybe_process_task_list_action();

		$form = RGFormsModel::get_form_meta( $form_id );

		GFFormSettings::page_header( esc_html__( 'Tasks', 'makerfaire' ) );
		$add_new_url = add_query_arg( array( 'tid' => 0 ) );
		?>

		<h3><span><i class="fa fa-check-square-o"></i> <?php esc_html_e( 'Tasks', 'makerfaire' ) ?>
				<a id="add-new-confirmation" class="add-new-h2" href="<?php echo esc_url( $add_new_url ) ?>"><?php esc_html_e( 'Add New', 'gravityforms' ) ?></a></span>
		</h3>

		<script type="text/javascript">
			function ToggleActive(img, task_id) {
				var is_active = img.src.indexOf("active1.png") >= 0
				if (is_active) {
					img.src = img.src.replace("active1.png", "active0.png");
					jQuery(img).attr('title', <?php echo json_encode( esc_html__( 'Inactive', 'gravityforms' ) ); ?>).attr('alt', <?php echo json_encode( esc_html__( 'Inactive', 'gravityforms' ) );  ?>);
				}
				else {
					img.src = img.src.replace("active0.png", "active1.png");
					jQuery(img).attr('title', <?php echo json_encode( esc_html__( 'Active', 'gravityforms' ) ); ?>).attr('alt', <?php echo json_encode( esc_html__( 'Active', 'gravityforms' ) ); ?>);
				}

				var mysack = new sack("<?php echo admin_url( 'admin-ajax.php' )?>");
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar("action", "mf_update_task_active");
				mysack.setVar("mf_update_task_active", "<?php echo wp_create_nonce( 'mf_update_task_active' ) ?>");
				mysack.setVar("form_id", <?php echo intval( $form_id ) ?>);
				mysack.setVar("task_id", task_id);
				mysack.setVar("is_active", is_active ? 0 : 1);
				mysack.onError = function () {
					alert(<?php echo json_encode( esc_html__( 'Ajax error while updating task', 'gravityforms' ) ) ?>)
				};
				mysack.runAJAX();

				return true;
			}
      function DeleteTask(taskId) {
          jQuery('#action_argument').val(taskId);
          jQuery('#action').val('delete');
          jQuery('#task_list_form')[0].submit();
      }
      function DuplicateTask(taskId) {
          jQuery('#action_argument').val(taskId);
          jQuery('#action').val('duplicate');
          jQuery('#task_list_form')[0].submit();
      }
		</script>
		<?php
		$task_table = new GFTaskTable( $form );
		$task_table->prepare_items();
		?>

		<form id="task_list_form" method="post">

			<?php $task_table->display(); ?>

			<input id="action_argument" name="action_argument" type="hidden" />
			<input id="action" name="action" type="hidden" />

			<?php wp_nonce_field( 'gform_task_list_action', 'gform_task_list_action' ) ?>

		</form>

		<?php
		GFFormSettings::page_footer();
	}

	public static function maybe_process_task_list_action() {

		if ( empty( $_POST ) || ! check_admin_referer( 'gform_task_list_action', 'gform_task_list_action' ) ) {
			return;
		}

		$action    = rgpost( 'action' );
		$object_id = rgpost( 'action_argument' );

		switch ( $action ) {
			case 'delete':
				$task_deleted = GFTask::delete_task( $object_id, rgget( 'id' ) );
				if ( $task_deleted ) {
					GFCommon::add_message( esc_html__( 'Task deleted.', 'gravityforms' ) );
				} else {
					GFCommon::add_error_message( esc_html__( 'There was an issue deleting this task.', 'gravityforms' ) );
				}
				break;
			case 'duplicate':
				$task_duplicated = GFTask::duplicate_task( $object_id, rgget( 'id' ) );
				if ( $task_duplicated ) {
					GFCommon::add_message( esc_html__( 'Task duplicates.', 'gravityforms' ) );
				} else {
					GFCommon::add_error_message( esc_html__( 'There was an issue duplicating this task.', 'gravityforms' ) );
				}
				break;
		}

	}

	private static function get_task_ui_settings( $task, $is_valid = true ) {

		/**
		 * These variables are used to convenient "wrap" child form settings in the appropriate HTML.
		 */
		$subsetting_open  = '
            <td colspan="2" class="gf_sub_settings_cell">
                <div class="gf_animate_sub_settings">
                    <table>
                        <tr>';
		$subsetting_close = '
                        </tr>
                    </table>
                </div>
            </td>';

		$ui_settings = array();
		$form_id     = rgget( 'id' );
		$form        = RGFormsModel::get_form_meta( $form_id );
		$form        = gf_apply_filters( array( 'gform_admin_pre_render', $form_id ), $form );
		$is_valid    = empty( GFCommon::$errors );

		ob_start(); ?>

		<tr valign="top" <?php echo rgar( $task, 'isDefault' ) ? "style='display:none'" : '' ?> >
			<th scope="row">
				<label for="gform_task_name">
					<?php esc_html_e( 'Name', 'gravityforms' ); ?>
				</label>
			</th>
			<td>
				<input type="text" class="fieldwidth-2" name="gform_task_name" id="gform_task_name" value="<?php echo esc_attr( rgget( 'name', $task ) ) ?>" />
			</td>
		</tr> <!-- / name -->
		<?php $ui_settings['task_name'] = ob_get_contents();
		ob_clean();
    $forms = RGFormsModel::get_forms( null, 'title' );
		ob_start(); ?>
    <tr>
      <th scope="row">
				<label for="gform_task_formID">
					<?php _e( 'Associated Form ID', 'makerfaire' ); ?>
				</label>
			</th>
      <td>
        <input type="text" class="fieldwidth-2" name="gform_task_formID" id="gform_task_formID" value="<?php echo esc_attr( rgget( 'formID', $task ) ); ?>" />
      </td>
    </tr>
    <?php
    $ui_settings['task_formID'] = ob_get_contents();
		ob_clean();
		ob_start();
    ?>
    <tr>
      <th scope="row">
				<label for="gform_task_form2use">
					<?php _e( 'URL to Form to Complete', 'makerfaire' ); ?>
				</label>
			</th>
      <td>
        <input type="text" class="fieldwidth-2" name="gform_task_form2use" id="gform_task_form2use" value="<?php echo esc_attr( rgget( 'form2use', $task ) ); ?>" />
      </td>
    </tr>
    <?php
    $ui_settings['task_form2use'] = ob_get_contents();
		ob_clean();
    ob_start();

    ?>
		<tr valign="top" <?php echo rgar( $task, 'isDefault' ) ? 'style=display:none;' : ''; ?> >
			<th scope="row">
				<label for="gform_task_conditional_logic">
					<?php esc_html_e( 'Conditional Logic', 'gravityforms' ) ?><?php gform_tooltip( 'task_conditional_logic' ) ?>
				</label>
			</th>
			<td>
				<input type="checkbox" id="task_conditional_logic" onclick="SetConditionalLogic(this.checked); ToggleConditionalLogic(false, 'task');" <?php checked( is_array( rgar( $task, 'conditionalLogic' ) ), true ) ?> />
				<label for="task_conditional_logic" class="inline"><?php esc_html_e( 'Enable conditional logic', 'gravityforms' ) ?><?php gform_tooltip( 'task_conditional_logic' ) ?></label>
				<br />
			</td>
		</tr> <!-- / conditional logic -->
		<tr>
			<td colspan="2">
				<div id="task_conditional_logic_container" class="gf_animate_sub_settings" style="padding-left:10px;">
					<!-- content dynamically created from form_admin.js -->
				</div>
			</td>
		</tr>

		<?php $ui_settings['task_conditional_logic'] = ob_get_contents();
		ob_clean(); ?>

		<?php
		ob_end_clean();

		return $ui_settings;
	}

	private static function validate_task() {
    $name              = sanitize_text_field( rgpost( 'gform_task_name' ) );
    $form2use          = sanitize_text_field( rgpost( 'gform_task_form2use' ) );
    $formID            = sanitize_text_field( rgpost( 'gform_task_formID' ) );
    $is_valid = true;
    if($name == ''){
      $is_valid = false;
			GFCommon::add_error_message( esc_html__( 'Please enter a valid name for the task.', 'makerfaire' ) );
    }

    if($form2use == ''){
      $is_valid = false;
			GFCommon::add_error_message( esc_html__( 'Please select a form to apply for this task.', 'makerfaire' ) );
    }

    if($formID == ''){
      $is_valid = false;
			GFCommon::add_error_message( esc_html__( 'Please enter the formID this task is associated with.', 'makerfaire' ) );
    }

		return $is_valid;
	}

	/**
	 * Delete a form task by ID.
	 *
	 * @param mixed $task_id
	 * @param mixed $form_id Can pass a form ID or a form object
	 *
	 * @return mixed
	 */
	public static function delete_task( $task_id, $form_id ) {

		if ( ! $form_id ) {
			return false;
		}

		$form = ! is_array( $form_id ) ? RGFormsModel::get_form_meta( $form_id ) : $form_id;

		unset( $form['tasks'][ $task_id ] );

		// clear Form cache so next retrieval of form meta will reflect deleted task
		RGFormsModel::flush_current_forms();
    return RGFormsModel::update_form_meta( $form['id'], $form);
	}

	public static function duplicate_task( $task_id, $form_id ) {

		if ( ! $form_id ) {
			return false;
		}

		$form = ! is_array( $form_id ) ? RGFormsModel::get_form_meta( $form_id ) : $form_id;

		$new_task = $form['tasks'][ $task_id ];
		$name             = rgar( $new_task, 'name' );
		$new_id           = uniqid();

		$count    = 2;
		$new_name = $name . ' - Copy 1';
		while ( ! self::is_unique_name( $new_name, $form['tasks'] ) ) {
			$new_name = $name . " - Copy $count";
			$count ++;
		}
		$new_task['name'] = $new_name;
		$new_task['id']   = $new_id;
		unset( $new_task['isDefault'] );
		if ( $new_task['toType'] == 'hidden' ) {
			$new_task['toType'] = 'email';
		}

		$form['tasks'][ $new_id ] = $new_task;

		// clear Form cache so next retrieval of form meta will return duplicated task
		RGFormsModel::flush_current_forms();
    return RGFormsModel::update_form_meta( $form['id'], $form);
	}

	public static function is_unique_name( $name, $tasks ) {

		foreach ( $tasks as $task ) {
			if ( strtolower( rgar( $task, 'name' ) ) == strtolower( $name ) ) {
				return false;
			}
		}

		return true;
	}

}


class GFTaskTable extends WP_List_Table {

	public $form;

	function __construct( $form ) {

		$this->form = $form;

		$this->_column_headers = array(
			array(
				'cb'        => '',
				'name'      => esc_html__( 'Name', 'makerfaire' ),
				'form2use'  => esc_html__( 'Form URL', 'makerfaire' ),
        'formID'    => esc_html__( 'Form to Apply', 'makerfaire' )
			),
			array(),
			array(),
			'name',
		);

		parent::__construct();
	}

  /*
   * Loop thru the tasks for this form.
   * If a form is set for this task to use, set the display form and return the title.
   * If the form to use is invalid, return blank for the title
   */
	function prepare_items() {
		//check if tasks are part of the form object
    if(isset($this->form['tasks'])){
      foreach ($this->form['tasks'] as $task){
        if(is_numeric($task['form2use'])){
          $dispForm = GFAPI::get_form($task['form2use']);
          $task['form2use'] = $dispForm['title'];
        }else{
          $task['form2use'] = '';
        }
        $this->items[] = $task;
      }
    }
	}

	function display() {
		$singular = rgar( $this->_args, 'singular' );
		?>

		<table class="wp-list-table <?php echo esc_attr( implode( ' ', $this->get_table_classes() ) ); ?>" cellspacing="0">
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>

			<tfoot>
			<tr>
				<?php $this->print_column_headers( false ); ?>
			</tr>
			</tfoot>

			<tbody id="the-list"<?php if ( $singular ) {
				echo " class='list:$singular'";
			} ?>>

			<?php $this->display_rows_or_placeholder(); ?>

			</tbody>
		</table>

	<?php
	}

	function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr id="task-' . esc_attr( $item['id'] ) . '" ' . $row_class . '>';
		echo $this->single_row_columns( $item );
		echo '</tr>';
	}

	function get_columns() {
		return $this->_column_headers[0];
	}

	function column_default( $item, $column ) {
		echo rgar( $item, $column );
	}

	function column_cb( $item ) {
		if ( rgar( $item, 'isDefault' ) ) {
			return;
		}
		$is_active = isset( $item['isActive'] ) ? $item['isActive'] : true;
		?>
		<img src="<?php echo GFCommon::get_base_url() ?>/images/active<?php echo intval( $is_active ) ?>.png" style="cursor: pointer;margin:-5px 0 0 8px;" alt="<?php $is_active ? esc_attr__( 'Active', 'gravityforms' ) : esc_attr__( 'Inactive', 'gravityforms' ); ?>" title="<?php echo $is_active ? esc_attr__( 'Active', 'gravityforms' ) : esc_attr__( 'Inactive', 'gravityforms' ); ?>" onclick="ToggleActive(this, '<?php echo esc_js( $item['id'] ) ?>'); " />
	<?php
	}

	function column_name( $item ) {
		$edit_url = add_query_arg( array( 'tid' => $item['id'] ) );
		$actions  = apply_filters(
			'gform_task_actions', array(
				'edit'      => '<a title="' . esc_attr__( 'Edit this item', 'gravityforms' ) . '" href="' . esc_url( $edit_url ) . '">' . esc_html__( 'Edit', 'gravityforms' ) . '</a>',
				'duplicate' => '<a title="' . esc_attr__( 'Duplicate this task', 'gravityforms' ) . '" onclick="javascript: DuplicateTask(\'' . esc_js( $item['id'] ) . '\');" style="cursor:pointer;">' . esc_html__( 'Duplicate', 'gravityforms' ) . '</a>',
				'delete'    => '<a title="' . esc_attr__( 'Delete this task', 'gravityforms' ) . '" class="submitdelete" onclick="javascript: if(confirm(\'' . esc_js( esc_html__( 'WARNING: You are about to delete this task.', 'gravityforms' ) ) . esc_js( esc_html__( "'Cancel' to stop, 'OK' to delete.", 'gravityforms' ) ) . '\')){ DeleteTask(\'' . esc_js( $item['id'] ) . '\'); }" style="cursor:pointer;">' . esc_html__( 'Delete', 'gravityforms' ) . '</a>'
			)
		);

		if ( isset( $item['isDefault'] ) && $item['isDefault'] ) {
			unset( $actions['delete'] );
		}

		?>

		<a href="<?php echo esc_url( $edit_url ); ?>"><strong><?php echo esc_html( rgar( $item, 'name' ) ); ?></strong></a>
		<div class="row-actions">

			<?php
			if ( is_array( $actions ) && ! empty( $actions ) ) {
				$keys     = array_keys( $actions );
				$last_key = array_pop( $keys );
				foreach ( $actions as $key => $html ) {
					$divider = $key == $last_key ? '' : ' | ';
					?>
					<span class="<?php echo $key; ?>">
                        <?php echo $html . $divider; ?>
                    </span>
				<?php
				}
			}
			?>

		</div>

	<?php
	}


	function no_items() {
		$url = add_query_arg( array( 'tid' => 0 ) );
		printf( esc_html__( "This form doesn't have any tasks. Let's go %screate one%s.", 'gravityforms' ), "<a href='" . esc_url( $url ) . "'>", '</a>' );
	}
}