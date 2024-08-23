<?php

add_filter( 'gform_search_criteria_entry_list', 'gv_revisions_filter_entry_list_search_criteria', 10, 2 );

/**
 * When entry list filtered by "gv-revisions" status, override Gravity Forms hard-coded values
 *
 * @param array $search_criteria
 * @param int $form_id
 *
 * @return array
 */
function gv_revisions_filter_entry_list_search_criteria( $search_criteria = array(), $form_id = 0 ) {

	if( GV_Entry_Revisions::revision_status_key !== rgget( 'filter' ) ) {
		return $search_criteria;
	}

	$search_criteria['status'] = GV_Entry_Revisions::revision_status_key;

	return $search_criteria;
}

add_filter( 'gform_filter_links_entry_list', 'gv_revisions_filter_links_entry_list', 20, 3 );

/**
 * Add filter links to the Entries page
 *
 * Can be disabled by returning false on the `gravityview/approve_entries/show_filter_links_entry_list` filter
 *
 * @since 1.17.1
 *
 * @param array $filter_links Array of links to include in the subsubsub filter list. Includes `id`, `field_filters`, `count`, and `label` keys
 * @param array $form GF Form object of current form
 * @param bool $include_counts Whether to include counts in the output
 *
 * @return array Filter links, with GravityView approved/disapproved links added
 */
function gv_revisions_filter_links_entry_list( $filter_links = array(), $form = array(), $include_counts = true ) {

	/**
	 * @filter `gravityview/entry-revisions/show-filter-links` Disable filter links by returning false
	 * @since 1.0
	 * @param bool $show_filter_links True: show the "approved"/"disapproved" filter links. False: hide them.
	 * @param array $form GF Form object of current form
	 */
	if( false === apply_filters( 'gravityview/entry-revisions/show-filter-links', true, $form ) ) {
		return $filter_links;
	}

	$total_count = null;

	if ( $include_counts ) {
		GFAPI::get_entry_ids( $form['id'], array( 'status' => GV_Entry_Revisions::revision_status_key ), null, null, $total_count );
	}

	// If there are no revisions, don't add the link
	if ( $include_counts && ! $total_count ) {
		return $filter_links;
	}

	$filter_links[] = array(
		'id'            => GV_Entry_Revisions::revision_status_key,
		'field_filters' => array(),
		'count'         => $total_count,
		'label'         => esc_html__( 'Entry Revisions', 'gk-gravityrevisions' ),
	);

	return $filter_links;
}

add_filter( 'gform_entry_list_columns', 'gv_revisions_add_parent_column', 10, 2 );

function gv_revisions_add_parent_column( $table_columns = array(), $form_id = 0 ) {

	if( GV_Entry_Revisions::revision_status_key !== rgget( 'filter' ) ) {
		return $table_columns;
	}

	$table_columns['field_id-gv_revision_parent_id'] = esc_html__( 'Parent Entry ID', 'gk-gravityrevisions' );

	return $table_columns;
}

add_filter( 'gform_entries_column_filter', 'gv_revisions_parent_id_entry_column_filter', 10, 5 );

/**
 * Used to inject markup and replace the value of any non-first column in the entry list grid.
 *
 * @param string $value        The value of the field
 * @param int    $form_id      The ID of the current form
 * @param int    $field_id     The ID of the field
 * @param array  $entry        The Entry object
 * @param string $query_string The current page's query string
 */
function gv_revisions_parent_id_entry_column_filter( $value = '', $form_id = 0, $field_id = '', $revision = array(), $query_string = '' ) {

	if ( 'gv_revision_parent_id' !== $field_id ) {
		return $value;
	}

	$entry = GFAPI::get_entry( $revision['gv_revision_parent_id'] );

	if ( is_wp_error( $entry ) ) {

		GravityKitFoundation::logger()->error( sprintf( '[%s] Entry not found at ID #%d', __METHOD__, $revision['gv_revision_parent_id'] ) );

		return esc_html( $entry->get_error_message() );
	}

	$table = new GF_Entry_List_Table( array( 'form_id' => $form_id ) );

	return sprintf( '<a href="%s">%s</a>', esc_url( $table->get_detail_url( $entry ) ), $value );
}
