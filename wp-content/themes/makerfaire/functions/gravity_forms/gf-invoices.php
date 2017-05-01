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
  if($form['create_invoice']=='yes'){
    createInvoice($form, $lead);
  }
}

function mf_updateInvoice($form,$entry_id,$orig_entry=array()){
  //Create Invoice option from form settings
  if($form['create_invoice']=='yes'){
    $lead = GFAPI::get_entry(esc_attr($entry_id));
    createInvoice($form, $lead);
  }
}

/* Sponsor Invoice */
  function createInvoice($form, $lead) {
    $entry_id = $lead['id'];
    //see if this entry already has an invoice created
    $invoice_num   = get_value_by_label('invoice_num', $form, $lead);
    $invNumFieldID = (is_array($invoice_num) && isset($invoice_num['id'])?$invoice_num['id']:0);
    //invoice post id
    $inv_post_id   = get_value_by_label('inv_post_id', $form, $lead);
    $invPostFieldID = (is_array($inv_post_id) && isset($inv_post_id['id'])?$inv_post_id['id']:0);

    $post_id       = (is_array($inv_post_id) && $inv_post_id['value']!=''?$inv_post_id['value']:0);
    if($post_id == 0){ //create the invoice
      $invNum = 'MF' .$entry_id.date("mdy");//create invoice number

      // Create new invoice post
      $new_invoice = array(
        'post_title'    => $invNum,
        'post_content'  => '',
        'post_type'     => 'invoice'
      );
      $post_id = wp_insert_post($new_invoice);
      //update entry with invoice number
      mf_update_entry_field($entry_id,$invNumFieldID,$invNum);
      //update entry with invoice post id
      mf_update_entry_field($entry_id,$invPostFieldID,$post_id);
    }

    /*    Set Client Name */
    $return = get_all_fieldby_name('client_name', $form,$lead);
    if(!empty($return)){
      foreach($return as $name){
        if(!empty($name['value']))
          $invoice_client_name = $name['value'];
      }
    }else{
      $invoice_client_name = 'client_name not found';
    }
    update_field('invoice_client_name', $invoice_client_name, $post_id);

    /*    Set Client Email */
    $return = get_all_fieldby_name('client_email', $form,$lead);
    if(!empty($return)){
      foreach($return as $email){
        if(!empty($email['value']))
          $invoice_client_email = $email['value'];
      }
    }else{
      $invoice_client_email = 'client_email not found';
    }
    update_field('invoice_client_email', $invoice_client_email, $post_id);

    $invoice_services = get_invoice_services($form, $lead);

    //$selector would be the field name
    update_field('invoice_services', $invoice_services, $post_id);

  } //end function create_invoice

function get_invoice_services($form, $lead) {
  $invoice_services = array();

  $key =  'invoice_calc';
  foreach ($form['fields'] as $field) {
    $lead_key = $field['inputName'];
    if($field['id']==710) echo '710='.$lead[$field['id']];
    if(isset($lead[$field['id']]) && $lead[$field['id']]!=''){
      if ($lead_key == $key) {  //process the calculation data
        $calcString = $field['calculationFormula'];
        //var_dump($field);echo '<br/><br/>';
        //field data
        $field_data_start = strpos($calcString, '{');
        $field_data_end   = strpos($calcString, '}');
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

        if($orderedQty!=0) {
          $invoice_services[] =
          array(
            "invoice_service_name"      => $service_name,
            "invoice_service_amount"    => $amt,
            "invoice_service_quantity"  => $orderedQty
          );
        }
      }
    }
  } //end for each
  return $invoice_services;

} //end function get_invoice_services