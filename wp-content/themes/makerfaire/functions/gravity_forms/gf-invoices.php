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

//* Send a notice to the user when CPT is created
add_action( 'acf/save_post', 'send_invoice_notice', 20 );
function send_invoice_notice( $post_id ) {
    if ( 'invoice' == get_post_type( $post_id ) ) {
      // Get client's first name
      $name = get_field( 'invoice_client_name', $post_id );
      $first_name = explode( ' ', $name );
      // Get client's email
      $to = get_field( 'invoice_client_email', $post_id );
      //$to = 'alicia@makermedia.com';
      // Get invoice link with client's email passed as query string
      $permalink = trailingslashit( get_permalink( $post_id ) ) . '?client_email=' . $to;
      // Set email subject
      $subject = 'Your MakerFaire Service Invoice';
      // Set email message
      $message = 'Hi, ' . $first_name[0] . '. An invoice was just created and assigned to you on MakerFaire. Click here to view invoice details: ' . $permalink;
      // Send email
      die($to.' '.$subject.' '.$message);
      wp_mail( $to, $subject, $message );
    }
}