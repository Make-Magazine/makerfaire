<?php
/**
 * Instead of passing DataTables AJAX requests through admin-ajax.php, directly access the data
 *
 * @since 1.3
 *
 * @param boolean $use_direct_access Default false
 */
//add_filter( 'gravityview/datatables/direct-ajax', '__return_true' );

/* Rewrite rules */
function custom_rewrite_rule() {
	add_rewrite_rule('^mf/([^/]*)/([^/]*)/?','index.php?pagename=maker-faire-gravity-forms-display-page&makerfaire=$matches[1]&entryid=$matches[2]','top');
	add_rewrite_rule('^mfscheduler/([^/]*)/?','index.php?pagename=mfscheduler&faire_id=$matches[1]','top');
	add_rewrite_rule('^mfscheduler-tasks/?','index.php?pagename=mfscheduler-tasks','top');
	add_rewrite_rule('^mfarchives/([^/]*)/?','index.php?pagename=entry-archives&entryslug=$matches[1]','top');
	add_rewrite_rule('^mfapi/v3/([^/]*)/?','index.php?pagename=mfapi&api=true&type=$matches[1]','top');
}
add_action('init', 'custom_rewrite_rule', 10, 0);


function custom_rewrite_tag() {
	add_rewrite_tag('%faire_id%', '([^&]+)');
	add_rewrite_tag('%entryid%', '([^&]+)');
	add_rewrite_tag('%entryslug%', '([^&]+)');
	add_rewrite_tag('%makerfaire%', '([^&]+)');
}
add_action('init', 'custom_rewrite_tag', 10, 0);

/* Template Include */
/* Query Vars */

add_filter( 'query_vars', 'api_register_query_var' );
function api_register_query_var( $vars ) {
    $vars[] = 'type';
    $vars[] = 'api';
    $vars[] = 'faire_id';
    return $vars;
}

add_filter('template_include', 'api_include', 1, 1);
function api_include($template)
{
    global $wp_query; //Load $wp_query object
    $pagename_value = (isset($wp_query->query_vars['pagename'])?$wp_query->query_vars['pagename']:'');
    
    if ($pagename_value=="api") {
        return $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/makerfaire/page-api.php'; //Load your template or file
    }
    elseif ($pagename_value=="mfscheduler") {
        return $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/makerfaire/page-mfscheduler.php'; //Load your template or file
    }
    elseif ($pagename_value=="mfscheduler-tasks") {
        return $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/makerfaire/page-mfscheduler-tasks.php'; //Load your template or file
    }

    return $template; //Load normal template when $page_value != "true" as a fallback
}


/* Gravity Forms Specific Helper calls*/


function add_grav_forms(){
	$role = get_role('editor');
	$role->add_cap('gform_full_access');
}
add_action('admin_init','add_grav_forms');

add_filter( 'gform_next_button', 'gform_next_button_markup' );
function gform_next_button_markup( $next_button ) {

	$next_button = '<span class="container-gnb">'. $next_button . '</span>';

	return $next_button;
}

add_filter( 'gform_previous_button', 'gform_previous_button_markup' );
function gform_previous_button_markup( $previous_button ) {
	$previous_button = '<span class="container-gpb">'. $previous_button . '</span>';
	return $previous_button;
}



//add_filter('gform_submit_button','form_submit_button');
//function form_submit_button($button){
//	return '<input id="gform_submit_button_' . $form['id'] . '" class="gform_button gform_submit_button button" type="submit" onclick="if(window["gf_submitting_' . $form['id'] . '"]){return false;} if( !jQuery("#gform_' . $form['id'] . '")[0].checkValidity || jQuery("#gform_' . $form['id'] . '")[0].checkValidity()){window["gf_submitting_' . $form['id'] . '"]=true;} " value="Submit">';
//}

/*
 * After Submission Gravity Forms Action Handling
 */
add_action( 'gform_after_submission', 'updateRMT', 10, 2 );
function updateRMT( $entry, $form ) {
  $result = GFRMTHELPER::gravityforms_makerInfo($entry,$form,'new');
}

/* This function will write all user changes to entries to a database table to create a change report */
add_action('gform_after_update_entry', 'GVupdate_changeRpt', 10, 3 );
function GVupdate_changeRpt($form,$entry_id,$orig_entry=array()){
  //get updated entry
  $updatedEntry = GFAPI::get_entry(esc_attr($entry_id));
  GFRMTHELPER::gravityforms_makerInfo($updatedEntry,$form);
  $updates = array();

  foreach($form['fields'] as $field){
    //send notification after entry is updated in maker admin
    $input_id = $field->id;

    //if field type is checkbox we need to compare each of the inputs for changes
    $inputs = $field->get_entry_inputs();
    $status_at_update = (isset($orig_entry['303'])?$orig_entry['303']:'');
    if ( is_array( $inputs ) ) {
      foreach ( $inputs as $input ) {
        $input_id = $input['id'];
        $origField    = (isset($orig_entry[$input_id])   ?  $orig_entry[$input_id ] : '');
        $updatedField = (isset($updatedEntry[$input_id]) ?  $updatedEntry[$input_id ] : '');
        $fieldLabel   = ($field['adminLabel']!=''?$field['adminLabel']:$field['label']);
        if($origField!=$updatedField){
          //update field id
          $updates[] = array('lead_id'=>$entry_id,
                            'field_id'=>$input_id,
                            'field_before'=>$origField,
                            'field_after'=>$updatedField,
                            'fieldLabel'=>$fieldLabel,
                            'status_at_update'=>$status_at_update);
        }
      }
    } else {
      $origField    = (isset($orig_entry[$input_id])   ?  $orig_entry[$input_id ] : '');
      $updatedField = (isset($updatedEntry[$input_id]) ?  $updatedEntry[$input_id ] : '');
      $fieldLabel   = ($field['adminLabel']!=''?$field['adminLabel']:$field['label']);
      if($origField!=$updatedField){
        //update field id
        $updates[] = array('lead_id'=>$entry_id,
                          'field_id'=>$input_id,
                          'field_before'=>$origField,
                          'field_after'=>$updatedField,
                          'fieldLabel'=>$fieldLabel,
                          'status_at_update'=>$status_at_update);
      }
    }
  }

  //check if there are any updates to process
  if(!empty($updates)){
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;//current user id
    $inserts = array();

    //update database with this information
    foreach($updates as $update){
      $inserts[] = array(
          'user_id'           => $user_id,
          'lead_id'           => $update['lead_id'],
          'form_id'           => $form['id'],
          'field_id'          => addslashes($update['field_id']),
          'field_before'      => addslashes($update['field_before']),
          'field_after'       => addslashes($update['field_after']),
          'fieldLabel'        => addslashes($update['fieldLabel']),
          'status_at_update'  => addslashes($update['status_at_update']));
    }
    updateChangeRPT($inserts);
  }
}

/* function to add record to change report */
function updateChangeRPT($updates){
  global $wpdb;
  $inserts = array();

  $sql = "insert into wp_rg_lead_detail_changes (user_id, lead_id, form_id, field_id, field_before, field_after,fieldLabel,status_at_update) values ";
  foreach($updates as $update){
    //fields 320 and 302 are set as category id's. look up the category name and save this as the before and after field
    if($update['field_id']==320 || strpos($update['field_id'], '302.')!== false){
      $update['field_before'] = get_CPT_name($update['field_before']);
      $update['field_after']  = get_CPT_name($update['field_after']);
    }
      $inserts[]= '('.$update['user_id']      . ', ' .
                      $update['lead_id']      . ', ' .
                      $update['form_id']      . ', ' .
                  '"'.$update['field_id']         . '", ' .
                  '"'.$update['field_before']     . '", ' .
                  '"'.$update['field_after']      . '", '.
                  '"'.$update['fieldLabel']       . '", '.
                  '"'.$update['status_at_update'] . '"'.
              ')';
    }
  $sql .= implode(", ",$inserts);
  $wpdb->get_results($sql);
}

//action to modify field 320 to display the text instead of the taxonomy code
add_filter("gform_entry_field_value", "setCatName", 10, 4);
function setCatName($value, $field, $lead, $form){
  $field_type = RGFormsModel::get_input_type($field);
 	if( in_array( $field_type, array('checkbox',  'radio') ) ){
		$value = RGFormsModel::get_lead_field_value( $lead, $field );
		return GFCommon::get_lead_field_display( $field, $value, $lead["currency"], true );
	}
	else{
		return $value;
	}

}

add_filter( 'gform_export_field_value', 'set_export_values', 10, 4 );
function set_export_values( $value, $form_id, $field_id, $lead ) {
  if($field_id==320|| strpos($field_id, '302.')!== false){
    $value = get_CPT_name($value);
  }
  return $value;
}

function createGUID($id){

        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid($id, true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// "}"
        return $uuid;
}

remove_action('wp_ajax_gf_resend_notifications', 'resend_notifications');
add_action('wp_ajax_gf_resend_notifications', 'MF_resend_notifications');

function MF_resend_notifications() {
  	check_admin_referer( 'gf_resend_notifications', 'gf_resend_notifications' );
		$form_id = absint( rgpost( 'formId' ) );
		$leads   = rgpost( 'leadIds' ); // may be a single ID or an array of IDs
		if ( 0 == $leads ) {
			// get all the lead ids for the current filter / search
			$filter = rgpost( 'filter' );
			$search = rgpost( 'search' );
			$star   = $filter == 'star' ? 1 : null;
			$read   = $filter == 'unread' ? 0 : null;
			$status = in_array( $filter, array( 'trash', 'spam' ) ) ? $filter : 'active';

			$search_criteria['status'] = $status;

			if ( $star ) {
				$search_criteria['field_filters'][] = array( 'key' => 'is_starred', 'value' => (bool) $star );
			}
			if ( ! is_null( $read ) ) {
				$search_criteria['field_filters'][] = array( 'key' => 'is_read', 'value' => (bool) $read );
			}

			$search_field_id = rgpost( 'fieldId' );

			if ( isset( $_POST['fieldId'] ) && $_POST['fieldId'] !== '' ) {
				$key            = $search_field_id;
				$val            = $search;
				$strpos_row_key = strpos( $search_field_id, '|' );
				if ( $strpos_row_key !== false ) { //multi-row
					$key_array = explode( '|', $search_field_id );
					$key       = $key_array[0];
					$val       = $key_array[1] . ':' . $val;
				}
				$search_criteria['field_filters'][] = array(
					'key'      => $key,
					'operator' => rgempty( 'operator', $_POST ) ? 'is' : rgpost( 'operator' ),
					'value'    => $val,
				);
			}

			$leads = GFFormsModel::search_lead_ids( $form_id, $search_criteria );
		} else {
			$leads = ! is_array( $leads ) ? array( $leads ) : $leads;
		}

		/**
		 * Filters the notifications to be re-sent
		 *
		 * @since Unknown
		 *
		 * @param array $form_meta The Form Object
		 * @param array $leads     The entry IDs
		 */
		$form = gf_apply_filters( array( 'gform_before_resend_notifications', $form_id ), RGFormsModel::get_form_meta( $form_id ), $leads );

		if ( empty( $leads ) || empty( $form ) ) {
			esc_html_e( 'There was an error while resending the notifications.', 'gravityforms' );
			die();
		};

		$notifications = json_decode( rgpost( 'notifications' ) );
		if ( ! is_array( $notifications ) ) {
			die( esc_html__( 'No notifications have been selected. Please select a notification to be sent.', 'gravityforms' ) );
		}

		if ( ! rgempty( 'sendTo', $_POST ) && ! GFCommon::is_valid_email_list( rgpost( 'sendTo' ) ) ) {
			die( sprintf( esc_html__( 'The %sSend To%s email address provided is not valid.', 'gravityforms' ), '<strong>', '</strong>' ) );
		}

		foreach ( $leads as $lead_id ) {
			$lead = RGFormsModel::get_lead( $lead_id );
      foreach ( $notifications as $notification_id ) {
        $notification = $form['notifications'][ $notification_id ];
				if ( ! $notification ) {
					continue;
				}
        //always check conditional logic
        if (!GFCommon::evaluate_conditional_logic( rgar( $notification, 'conditionalLogic' ), $form, $lead ) ) {
          GFCommon::log_debug( "MF_send_notifications(): Notification conditional logic not met, not processing notification (#{$notification_id} - {$notification['name']})." );
          continue;
        }

				//overriding To email if one was specified
				if ( rgpost( 'sendTo' ) ) {
          GFCommon::log_debug( "MF_send_notifications(): sendTo changed to (".rgpost( 'sendTo' ).")." );
					$notification['to']     = rgpost( 'sendTo' );
					$notification['toType'] = 'email';
				}
        GFCommon::send_notification( $notification, $form, $lead );
        //GFCommon::send_notifications( $notifications, $form, $lead, true );
      }
		}

		die();
}

add_filter( 'template_include', 'wpm_load_script_for_template', 1000 );
function wpm_load_script_for_template( $template ){
  if ((strpos($template, 'page-mfscheduler.php') !== false) ) {
    wp_enqueue_script( 'kendoJs1', get_template_directory_uri() . '/lib/Kendo/woahbar/woahbar.js', array('jquery'));
    wp_enqueue_script( 'kendoJs2', get_template_directory_uri() . '/lib/Kendo/content/js/kendo.all.min.js', array('jquery'));
    wp_enqueue_script( 'kendoJs3', get_template_directory_uri() . '/lib/Kendo/content/js/kendo.timezones.min.js', array('jquery'));
    wp_enqueue_script( 'kendoJs4', get_template_directory_uri() . '/lib/Kendo/content/shared/js/console.js', array('jquery'));
    wp_enqueue_script( 'kendoJs6', get_template_directory_uri() . '/lib/Kendo/content/shared/js/prettify.js', array('jquery'));
    wp_enqueue_script( 'kendoJs7', get_template_directory_uri() . '/lib/Kendo//content/shared/js/pako.min.js', array('jquery'));

    wp_enqueue_style('kendo1-styles', get_template_directory_uri() . '/lib/Kendo', array());
    wp_enqueue_style('kendo2-styles', get_template_directory_uri() . '/lib/Kendo/content/css/web/kendo.common.min.css', array());
    wp_enqueue_style('kendo3-styles', get_template_directory_uri() . '/lib/Kendo/content/css/web/kendo.rtl.min.css', array());
    wp_enqueue_style('kendo4-styles', get_template_directory_uri() . '/lib/Kendo/content/css/web/kendo.default.min.css', array());
    wp_enqueue_style('kendo5-styles', get_template_directory_uri() . '/lib/Kendo/content/css/web/kendo.default.mobile.min.css', array());
    wp_enqueue_style('kendo6-styles', get_template_directory_uri() . '/lib/Kendo/content/css/dataviz/kendo.dataviz.min.css', array());
    wp_enqueue_style('kendo7-styles', get_template_directory_uri() . '/lib/Kendo/content/css/dataviz/kendo.dataviz.default.min.css', array());
    wp_enqueue_style('kendo8-styles', get_template_directory_uri() . '/lib/Kendo/styles/examples.css', array());
    wp_enqueue_style('kendo9-styles', get_template_directory_uri() . '/lib/Kendo/woahbar/woahbar.css', array());
  }
  return $template; 
}

    