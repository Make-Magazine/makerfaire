<?php
/**
 *	Register Invoice Type
 */
add_action( 'init', 'mf_invoice_cpt' );
function mf_invoice_cpt() {
	$labels = array(
		'name'                => _x( 'Invoice', 'post type general name', 'makerfaire' ),
		'singular_name'       => _x( 'Invoice', 'post type singular name', 'makerfaire' ),
		'menu_name'           => _x( 'Invoices', 'admin menu', 'makerfaire' ),
		'name_admin_bar'      => _x( 'Invoice', 'add new on admin bar', 'makerfaire' ),
		'add_new'             => _x( 'Add New', 'Invoice', 'makerfaire' ),
		'add_new_item'        => __( 'Add New Invoice', 'makerfaire' ),
		'new_item'            => __( 'New Invoice', 'makerfaire' ),
		'edit_item'           => __( 'Edit Invoice', 'makerfaire' ),
		'view_item'           => __( 'View Invoice', 'makerfaire' ),
		'all_items'           => __( 'All Invoices', 'makerfaire' ),
		'search_items'        => __( 'Search Invoices', 'makerfaire' ),
		'parent_item_colon'		=> __( 'Parent Invoice:', 'makerfaire' ),
		'not_found'           => __( 'No Invoices found.', 'makerfaire' ),
		'not_found_in_trash'	=> __( 'No Invoices found in Trash.', 'makerfaire' )
	);
	$args = array(
		'description'         => __( 'Invoice', 'makerfaire' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'public'              => true,
		'publicly_queryable'	=> true,
		'query_var'           => true,
		'rewrite'             => array( 'slug'				=> 'invoice' ),
		'show_ui'             => true,
		'menu_icon'           => 'dashicons-media-spreadsheet',
		'show_in_menu'        => true,
		'show_in_nav_menus'		=> false,
		'show_in_admin_bar'		=> true,
		// 'menu_position'		=> 5,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search'	=> true,
		'capability_type'     => 'post',
	);
	register_post_type( 'invoice', $args );
	// Deny access to the post_type query arg
  if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'invoice' && !current_user_can( 'manage_options' ) ) {
    wp_die( 'Unauthorized' );
  }
}

//after form submission or entry update recreate invoices if need be
add_action( 'gform_after_submission', 'mf_createInvoice', 10, 2 );
add_action('gform_after_update_entry', 'mf_updateInvoice', 10, 3 );

function mf_createInvoice( $entry, $form ) {
  //Create Invoice option from form settings
  if(isset($form['create_invoice']) && $form['create_invoice']=='yes'){
    createInvoice($form, $entry);
  }
}

function mf_updateInvoice($form,$entry_id,$orig_entry=array()){
  //Create Invoice option from form settings
  if(isset($form['create_invoice']) && $form['create_invoice']=='yes'){
    $lead = GFAPI::get_entry(esc_attr($entry_id));
    createInvoice($form, $lead);
  }
}

/* Sponsor Invoice */
  function createInvoice($form, $lead) {
    $entry_id = $lead['id'];

    /*  see if this entry already has an invoice created */

    //invoice post id
    $fieldData       = get_value_by_label('inv_post_id', $form, $lead);
    $invPostFieldID  = (is_array($fieldData) && isset($fieldData['id']) ? $fieldData['id']:0);
    $post_id         = (is_array($fieldData) && $fieldData['value']!= '' ? $fieldData['value']:0);

    if($post_id == 0){ //create the invoice
      //Pull the original entry id
      $data         = get_value_by_label('entry-id', $form, $lead);
      $origEntryID  = (is_array($data) && $data['value'] != '' ? $data['value']:0);

      /* Build the Invoice Number
       * mf<order entryid><original entry id>
       */
      $invNum = 'MF' .$entry_id.$origEntryID;//create invoice number

      // Create new invoice post
      $new_invoice = array(
        'post_title'    => $invNum,
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'invoice'
      );
      $post_id = wp_insert_post($new_invoice);

      //update entry with invoice number
      $fieldData   = get_value_by_label('invoice_num', $form, $lead);
      $invNumFieldID = (is_array($fieldData) && isset($fieldData['id']) ? $fieldData['id']:0); //set invoice_num field id
      mf_update_entry_field($entry_id,$invNumFieldID,$invNum);

      //update entry with invoice post id
      mf_update_entry_field($entry_id,$invPostFieldID,$post_id);

      //set ACF fields for original entry ID and order form entry ID
      update_field('original_entry_id', $origEntryID, $post_id);
      update_field('order_form_entry_id', $entry_id, $post_id);
    }

    /* Set Invoice ACF fields */
    $invoiceFields = array(
        'billing_company_name','billing_email', 'billing_phone_num',
        'billing_address', 'billing_address2', 'billing_city', 'billing_state',
        'billing_zip', 'billing_country');
    foreach($invoiceFields as $field){
      $return = get_all_fieldby_name($field, $form, $lead);
      $fieldValue = '';
      if(!empty($return)){
        foreach($return as $name){
          if(!empty($name['value'])){
            $fieldValue = $name['value'];
          }
        }
      }

      update_field($field, $fieldValue, $post_id);
    }

    /* ACF - billing_contact_name
     * Parameter Names -
     *    billing_contact_fname
     *    billing_contact_lname
     */
    $return = get_all_fieldby_name('billing_contact_fname', $form, $lead);
    $fName = '';
    if(!empty($return)){
      foreach($return as $name){
        if(!empty($name['value'])){
          $fName = $name['value'];
        }
      }
    }

    $return = get_all_fieldby_name('billing_contact_lname', $form, $lead);
    $lName = '';
    if(!empty($return)){
      foreach($return as $name){
        if(!empty($name['value'])){
          $lName = $name['value'];
        }
      }
    }
    update_field('billing_contact_name', $fName.' '. $lName, $post_id);

    //build the repeater field data for invoice services
    $invoice_services = get_invoice_services($form, $lead);
    update_field('invoice_services', $invoice_services, $post_id);

  } //end function create_invoice

function get_invoice_services($form, $lead) {
  $invoice_services = array();

  $key =  'invoice_calc';
  foreach ($form['fields'] as $field) {
    $lead_key = $field['inputName'];

    //if this field is set in the entry, process it
    if(isset($lead[$field['id']]) && $lead[$field['id']]!=''){
      $orderedQty = 0;

      //if the parameter name is set to invoice_calc, process it
      if ($lead_key == $key) {
        $result = GFCommon::calculate( $field, $form, $lead );
        $calcString = $field['calculationFormula'];

        //field data
        $field_data_start = strpos($calcString, '{');
        $field_data_end   = strpos($calcString, '}');

        //if the field contains {} then pull the formula data from it
        if($field_data_start!== false && $field_data_end!==false){

          $field_data_length = $field_data_end - $field_data_start +1;
          $field_data = substr($calcString, $field_data_start, $field_data_length);

          //Qty
          $qty_field_start = strrpos($field_data,':');
          if($qty_field_start!== false){
            $qty_length = $field_data_end - $qty_field_start-1;
          }
          $qty = substr($field_data, $qty_field_start+1, $qty_length);

          //$service Name
          $service_length = $field_data_length - $qty_length-3; //3 is to account for the {} and the : separator
          $service_name = substr($field_data, $field_data_start+1, $service_length);
          //use field label if the service name is blank
          if($service_name==''){
            $service_name = $field['label'];
          }

          //field id (for qty) - using data in {}, numeric value after :
          $amt = str_replace($field_data, '', $calcString);//numeric data after removing {} and *
          $amt = str_replace('*', '', $amt);//numeric data after removing {} and *
          //is the amt field numeric or do we need to pull the value from a field
          if(!is_numeric($amt)){
            $fieldID = $amt;
            $fieldID = str_replace('{', '', $fieldID);
            $fieldID = str_replace('}', '', $fieldID);
            $amt = (isset($lead[$fieldID])?$lead[$fieldID]:0);
          }
          /*
           * determine amt field
           * Look for price in the label field.  Should be in this format:
           *  30” x 72” Folding Banquet Table: $60.00
           */

          $orderedQty = (!empty($lead) ? $lead[$qty]:0);
        }else{
          //no field data
          $service_name = $field['label'];
          $amt = $calcString;
          $orderedQty = 1;
        }

        if($orderedQty!=0){
          $invoice_services[] =
          array(
            "invoice_service_name"      => $service_name,
            "invoice_service_amount"    => $amt,
            "invoice_service_quantity"  => $orderedQty
          );
        }
      } //end check for parameter name
    } //end check if field set in lead
  } //end for each

  //hardcoding for Custom Order field TBD - find a better way to do this
  if(isset($lead['750']) && $lead['750'] != 0 &&
     isset($lead['749']) && $lead['749'] != ''){
    $invoice_services[] =
          array(
            "invoice_service_name"      => 'Custom Order: <br/>'.$lead['749'],
            "invoice_service_amount"    => $lead['750'],
            "invoice_service_quantity"  => 1
          );
  }
  if(isset($lead['717']) && $lead['717'] != 0) {
    $invoice_services[] =
          array(
            "invoice_service_name"      => 'Basic Internet',
            "invoice_service_amount"    => $lead['717'],
            "invoice_service_quantity"  => 1
          );
  }
  if(isset($lead['710']) && $lead['710'] != 0) {
    $invoice_services[] =
          array(
            "invoice_service_name"      => 'Fast Internet',
            "invoice_service_amount"    => $lead['710'],
            "invoice_service_quantity"  => 1
          );
  }
  if(isset($lead['704']) && $lead['704'] != 0) {
    $invoice_services[] =
          array(
            "invoice_service_name"      => 'Faster Internet',
            "invoice_service_amount"    => $lead['704'],
            "invoice_service_quantity"  => 1
          );
  }
  if(isset($lead['686']) && $lead['686'] != 0) {
    $invoice_services[] =
          array(
            "invoice_service_name"      => 'Fastest Internet',
            "invoice_service_amount"    => $lead['686'],
            "invoice_service_quantity"  => 1
          );
  }

  return $invoice_services;

} //end function get_invoice_services