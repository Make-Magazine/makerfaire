<?php
//add form type header to form list
add_action('gform_form_list_columns', 'mfform_type_header', 10, 1 ); //$columns
function mfform_type_header($columns){
  $columns['mfform_type'] = esc_html__( 'Form Type', 'gravityforms' );
  return $columns;
}

//add form type to form list
add_action('gform_form_list_column_mfform_type',  'mfform_type_detail',10, 1);
function mfform_type_detail($item){
  $form = GFAPI::get_form($item->id);
  echo (isset($form['form_type'])?$form['form_type']: '');
}

//sort admin form list by newest first
add_action( 'init', function() {
	if ( ! class_exists( 'GFForms' ) ) {
		return;
	}
	if ( GFForms::get_page() === 'form_list' ) {
        error_log('sorting form list');
		$params = array();

		if ( ! isset( $_GET['sort'] ) ) {
			$params = array(
				'sort'    => 'id',
				'dir'     => 'desc',
				'orderby' => 'id',
				'order'   => 'desc',
			);
		}		

		if ( ! empty( $params ) ) {
			wp_redirect( add_query_arg( $params ) );
			exit;
		}
	}

} );
