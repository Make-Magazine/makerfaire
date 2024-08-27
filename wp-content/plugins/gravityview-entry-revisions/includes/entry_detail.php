<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\GFCommon' ) ) {
	return;
}

/**
 * When rendering entry detail, add hooks to hide metaboxes if we're showing a revision.
 */
add_action( 'gform_pre_entry_detail', function() {

	if ( ! isset( $_GET['revision'] ) ) {
		return;
	}

	// If showing a revision, get rid of all metaboxes and lingering HTML stuff
	add_action( 'gform_entry_detail_sidebar_before', '_gk_gravityrevisions_ob_start' );
	add_action( 'gform_entry_detail_content_before', '_gk_gravityrevisions_ob_start' );

	add_action( 'gform_entry_detail', '_gk_gravityrevisions_ob_get_clean' );
	add_action( 'gform_entry_detail_sidebar_after', '_gk_gravityrevisions_ob_get_clean' );
});

add_filter( 'gform_entry_detail_meta_boxes', 'gv_revisions_entry_detail_add_meta_box' );

/**
 * Allow custom meta boxes to be added to the entry detail page.
 *
 * @since 1.0
 *
 * @param array $meta_boxes The properties for the meta boxes.
 * @param array $entry The entry currently being viewed/edited.
 * @param array $form The form object used to process the current entry.
 *
 * @return array $meta_boxes, with the Versions box added
 */
function gv_revisions_entry_detail_add_meta_box( $meta_boxes = array(), $entry = array(), $form = array() ) {

	$revision_id = rgget( 'revision' );

	if ( ! empty( $revision_id ) ) {
		return array(
			'gv_revisions' => array(
				'title'    => esc_html__( 'Compare Revisions', 'gk-gravityrevisions' ),
				'callback' => 'gv_revisions_meta_box_diff',
				'context'  => 'normal',
			),
		);
	}

	$meta_boxes['gv_revisions'] = array(
		'title'    => esc_html__( 'Entry Revisions', 'gk-gravityrevisions' ),
		'callback' => 'gv_revisions_meta_box_revisions_list',
		'context'  => 'normal',
	);

	return $meta_boxes;
}

/**
 * Display entry content comparison and restore button
 *
 * @since 1.0
 *
 * @param array $data Array with entry/form/mode keys.
 *
 * @return void
 */
function gv_revisions_meta_box_diff( $data = array() ) {

	$GV_Entry_Revisions = GV_Entry_Revisions::get_instance();
	$entry = rgar( $data, 'entry' );
	$revision = $GV_Entry_Revisions->get_revision( rgget( 'revision'), $entry['id'] );

	if( is_wp_error( $revision ) ) {
		echo '<h3>' . esc_html__( 'This revision no longer exists.', 'gk-gravityrevisions' ) . '</h3>';
		?><a href="<?php echo esc_url( remove_query_arg( 'revision', 'screen_mode' ) ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Return to Entry', 'gk-gravityrevisions' ); ?></a><?php
		return;
	}

	wp_enqueue_style( 'gv-revisions' );

	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_script( 'gv-revisions', plugins_url( 'assets/js/admin'.$min.'.js', GV_ENTRY_REVISIONS_FILE ), array( 'jquery' ), GV_ENTRY_REVISIONS_VERSION, true );

	wp_localize_script( 'gv-revisions', 'gvRevisions', array(
		'confirm' => esc_attr__( 'Are you sure? If you continue, the current entry will be updated with the selected field values.', 'gk-gravityrevisions' ),
		'restore' => array(
			'singular' => esc_attr__( 'Restore This Value', 'gk-gravityrevisions' ),
			'plural' => esc_attr__( 'Restore These Values', 'gk-gravityrevisions' ),
		),
	) );

	$output = $GV_Entry_Revisions->get_diff_html( $entry, $revision, true );

	if ( is_wp_error( $output ) ) {
		return;
	}

	echo $output;
}

/**
 * Display the meta box for the list of revisions
 *
 * @since 1.0
 *
 * @param array $data Array of data with entry, form, mode keys
 *
 * @return void
 */
function gv_revisions_meta_box_revisions_list( $data ) {

	$entry_id = rgars( $data, 'entry/id' );

	echo GV_Entry_Revisions::get_instance()->get_revisions_list_html( $entry_id, array( 'container_css' =>  'post-revisions' ) );
}

add_action( 'admin_notices', 'gv_revisions_maybe_print_restore_message' );

function gv_revisions_maybe_print_restore_message() {

	if ( ! rgget( 'restore-success' ) && ! rgget( 'restore-error' ) ) {
		return;
	}

	if( rgget( 'restore-success' ) ) {
		$message = esc_html__( 'Success: the selected revision fields were restored.', 'gk-gravityrevisions' );
		printf( '<div class="notice notice-success is-dismissible"><p><strong>%s</strong></p></div>', $message );
		return;
	}

	$error_code = rgget( 'restore-error' );

	if ( empty( $error_code ) ) {
		return;
	}

	switch ( $error_code ) {
		case 'mismatch':
		case 'not_found':
			$error = esc_html__( 'Revision not found', 'gk-gravityrevisions' );
			break;
		case 'identical':
		case 'no_changes':
			$error = esc_html__( 'No changes were made to the current entry.', 'gk-gravityrevisions' );
			break;
		default:
			$error = esc_html__( 'There was a problem restoring the revision.', 'gk-gravityrevisions' );
	}

	// translators: %s is the error message.
	printf( '<div class="notice notice-error is-dismissible"><h3>%s</h3></div>', sprintf( esc_html__( 'Error: %s', 'gk-gravityrevisions' ), $error ) );

}
