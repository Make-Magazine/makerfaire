<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
/** Capability required to generate signs. */
if ( ! defined( 'MF_SIGN_CAP' ) ) {
	define( 'MF_SIGN_CAP', 'manage_options' );
}
 
/** Seconds of generation work performed per poll request. Keep well under max_execution_time. */
if ( ! defined( 'MF_SIGN_POLL_BUDGET' ) ) {
	define( 'MF_SIGN_POLL_BUDGET', 10 );
}
 
/** Seconds of generation work per headless (cron/CLI) pass. */
if ( ! defined( 'MF_SIGN_CRON_BUDGET' ) ) {
	define( 'MF_SIGN_CRON_BUDGET', 300 );
}
 
/** Per-request timeout when fetching one PDF from the generator scripts. */
if ( ! defined( 'MF_SIGN_HTTP_TIMEOUT' ) ) {
	define( 'MF_SIGN_HTTP_TIMEOUT', 45 );
}
 
/* -------------------------------------------------------------------------
 * Admin page
 * ---------------------------------------------------------------------- */
 
function build_faire_signs() {
	require_once get_template_directory() . '/adminPages/faire_signs.php';
}
 
/* -------------------------------------------------------------------------
 * Helpers
 * ---------------------------------------------------------------------- */
 
/**
 * Map the JS "type" onto the generator script and the output folder.
 * These have never matched ('signs' -> maker), so keep them together in one place.
 */
function mf_sign_type_map( $type ) {
	switch ( $type ) {
		case 'signs':
			return array( 'script' => 'makersigns', 'folder' => 'maker' );
		case 'presenter':
			return array( 'script' => 'presenterSigns', 'folder' => 'presenter' );
		default:
			return array( 'script' => 'tabletag', 'folder' => 'tabletags' );
	}
}
 
function mf_sign_progress_key( $faire, $type ) {
	return 'mf_sign_progress_' . sanitize_key( $faire ) . '_' . sanitize_key( $type );
}
 
function mf_sign_queue_key( $faire, $type ) {
	return 'mf_sign_queue_' . sanitize_key( $faire ) . '_' . sanitize_key( $type );
}
 
function mf_sign_lock_key( $faire, $type ) {
	return 'mf_sign_lock_' . sanitize_key( $faire ) . '_' . sanitize_key( $type );
}
 
function mf_sign_get_progress( $faire, $type ) {
	$p = get_option( mf_sign_progress_key( $faire, $type ), array() );
	return is_array( $p ) ? $p : array();
}
 
function mf_sign_set_progress( $faire, $type, array $data ) {
	update_option(
		mf_sign_progress_key( $faire, $type ),
		array_merge( mf_sign_get_progress( $faire, $type ), $data, array( 'updated' => time() ) ),
		false // never autoload
	);
}
 
/**
 * Prevents the browser poll and a headless run from generating the same signs twice.
 * Self-healing: the transient expires, so a fatal mid-batch cannot wedge the queue forever.
 */
function mf_sign_acquire_lock( $faire, $type ) {
	$key = mf_sign_lock_key( $faire, $type );
	if ( get_transient( $key ) ) {
		return false;
	}
	set_transient( $key, time(), 120 );
	return true;
}
 
function mf_sign_release_lock( $faire, $type ) {
	delete_transient( mf_sign_lock_key( $faire, $type ) );
}
 
/**
 * Shared guard for the AJAX handlers. Returns [faire, type] or sends JSON and dies.
 */
function mf_sign_ajax_guard() {
	if ( ! current_user_can( MF_SIGN_CAP ) ) {
		wp_send_json_error( array( 'msg' => 'You do not have permission to generate signs.' ), 403 );
	}
 
	// The old JS sends no nonce key at all, so distinguish that from an expired one —
	// "missing" almost always means a stale cached copy of mf_fairesigns.js.
	if ( ! isset( $_POST['nonce'] ) ) {
		wp_send_json_error(
			array(
				'msg'  => 'No security token was sent. The browser is still running the OLD mf_fairesigns.js — hard-reload (Cmd+Shift+R), and enqueue the script with filemtime() as its version so this stops happening.',
				'code' => 'nonce_missing',
			),
			403
		);
	}
 
	if ( ! check_ajax_referer( 'mf_faire_signs', 'nonce', false ) ) {
		wp_send_json_error(
			array(
				'msg'  => 'Security token was sent but did not validate — your login session expired, or the page was cached. Reload the page and try again.',
				'code' => 'nonce_invalid',
			),
			403
		);
	}
 
	$faire = isset( $_POST['faire'] ) ? sanitize_text_field( wp_unslash( $_POST['faire'] ) ) : '';
	$type  = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
 
	if ( '' === $faire ) {
		wp_send_json_error( array( 'msg' => 'No faire specified.' ), 400 );
	}
 
	return array( $faire, $type );
}
 
/* -------------------------------------------------------------------------
 * CSV export
 * ---------------------------------------------------------------------- */
 
function createCSVfile() {
	if ( ! current_user_can( MF_SIGN_CAP ) ) {
		wp_die( 'Insufficient permissions.', 403 );
	}
 
	$form_id = ( isset( $_POST['exportForm'] ) && '' !== $_POST['exportForm'] ) ? absint( $_POST['exportForm'] ) : 0;
	if ( ! $form_id ) {
		$form_id = ( isset( $_GET['exForm'] ) && '' !== $_GET['exForm'] ) ? absint( $_GET['exForm'] ) : 0;
	}
	if ( ! $form_id ) {
		wp_die( 'Please select a form.' );
	}
 
	$entry_id = ( isset( $_GET['exEntry'] ) && '' !== $_GET['exEntry'] ) ? absint( $_GET['exEntry'] ) : 0;
 
	$form      = GFAPI::get_form( $form_id );
	$fieldData = array();
 
	foreach ( $form['fields'] as $field ) {
		if ( 'section' !== $field->type && 'html' !== $field->type && 'page' !== $field->type ) {
			$fieldData[ $field['id'] ] = $field;
		}
	}
 
	$entries = array();
	if ( ! $entry_id ) {
		$entries = GFAPI::get_entries(
			$form_id,
			array( 'status' => 'active' ),
			null,
			array( 'offset' => 0, 'page_size' => 9999 )
		);
	} else {
		$entries[] = GFAPI::get_entry( $entry_id );
	}
 
	$output = array( 'Entry ID', 'FormID' );
	foreach ( $fieldData as $field ) {
		$output[] = $field['label'];
	}
	$list = array( $output );
 
	foreach ( $entries as $entry ) {
		$fieldArray = array( $entry['id'], $form_id );
		foreach ( $fieldData as $field ) {
			if ( 320 == $field->id || 321 == $field->id ) {
				if ( in_array( $field->type, array( 'checkbox', 'select', 'radio' ), true ) ) {
					$currency = GFCommon::get_currency();
					$value    = RGFormsModel::get_lead_field_value( $entry, $field );
					array_push( $fieldArray, GFCommon::get_lead_field_display( $field, $value, $currency, true ) );
				}
			} else {
				array_push( $fieldArray, ( isset( $entry[ $field->id ] ) ? $entry[ $field->id ] : '' ) );
			}
		}
		$list[] = $fieldArray;
	}
 
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=form-' . $form_id . ( $entry_id ? '-' . $entry_id : '' ) . '.csv' );
 
	$file = fopen( 'php://output', 'w' );
	foreach ( $list as $line ) {
		fputcsv( $file, $line );
	}
	fclose( $file );
	die();
}
add_action( 'wp_ajax_createCSVfile', 'createCSVfile' );
add_action( 'admin_post_createCSVfile', 'createCSVfile' );
 
/* -------------------------------------------------------------------------
 * Table tag links (legacy helper)
 * ---------------------------------------------------------------------- */
 
function genTableTags( $faire ) {
	global $wpdb;
 
	$formIds = $wpdb->get_var( $wpdb->prepare( "SELECT form_ids FROM wp_mf_faire WHERE faire = %s", $faire ) );
	$forms   = explode( ',', str_replace( ' ', '', $formIds ?? '' ) );
 
	foreach ( $forms as $formId ) {
		$formId = absint( $formId );
		if ( ! $formId ) {
			continue;
		}
 
		$form     = GFAPI::get_form( $formId );
		$formType = isset( $form['form_type'] ) ? $form['form_type'] : '';
 
		if ( ! in_array( $formType, array( 'Exhibit', 'Sponsor', 'Startup Sponsor' ), true ) ) {
			continue;
		}
 
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT wp_gf_entry.id AS lead_id, wp_gf_entry_meta.meta_value AS lead_status
				   FROM wp_gf_entry, wp_gf_entry_meta
				  WHERE status = 'active' AND meta_key = '303'
				    AND wp_gf_entry_meta.entry_id = wp_gf_entry.id
				    AND wp_gf_entry_meta.meta_value NOT IN ('Rejected','Cancelled')
				    AND wp_gf_entry.form_id = %d",
				$formId
			)
		);
 
		echo 'Form - ' . esc_html( $formId ) . '(' . count( $results ) . ' entries)';
		echo '<div class="container"><div class="row">';
		foreach ( $results as $entry ) {
			printf(
				'<div class="col-md-2"><a class="fairsign" target="_blank" id="%1$s" href="%2$s">%1$s</a></div>',
				esc_attr( $entry->lead_id ),
				esc_url( get_template_directory_uri() . '/generate_pdf/tabletag.php?eid=' . $entry->lead_id . '&faire=' . $faire )
			);
		}
		echo '</div></div>';
	}
}
add_action( 'gen_table_tags', 'genTableTags', 10, 1 );
 
/* -------------------------------------------------------------------------
 * Zip creation
 * ---------------------------------------------------------------------- */
 
function createSignZip() {
	global $wpdb;
 
	list( $faire, $signType ) = mf_sign_ajax_guard();
 
	if ( '' === $signType ) {
		$signType = 'signs';
	}
 
	$statusFilter = isset( $_POST['selstatus'] ) ? sanitize_text_field( wp_unslash( $_POST['selstatus'] ) ) : '';
	$type         = isset( $_POST['seltype'] ) ? sanitize_text_field( wp_unslash( $_POST['seltype'] ) ) : '';
	$filterError  = isset( $_POST['error'] ) ? sanitize_text_field( wp_unslash( $_POST['error'] ) ) : '';
	$filterFormId = isset( $_POST['filform'] ) ? wp_unslash( $_POST['filform'] ) : '';
 
	if ( is_array( $filterFormId ) ) {
		$filterFormId = array_values( array_filter( array_map( 'absint', $filterFormId ) ) );
	} elseif ( '' !== $filterFormId ) {
		$filterFormId = absint( $filterFormId );
	}
 
	error_log( 'Start zip creation. Group forms by ' . $type . ( 'faire' === $type ? ' ' . $faire : '' ) );
 
	$appendFormId = '';
	if ( ! empty( $filterFormId ) ) {
		foreach ( (array) $filterFormId as $formId ) {
			$appendFormId .= '_' . $formId;
		}
	}
	if ( ! empty( $filterError ) ) {
		$appendFormId = '_' . $filterError;
	}
 
	$entries = array();
 
	$results = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT wp_gf_entry.ID AS entry_id, wp_gf_entry.form_id,
			        (SELECT GROUP_CONCAT(meta_value) FROM wp_gf_entry_meta meta2
			          WHERE meta2.entry_id = wp_gf_entry.id AND meta2.meta_key LIKE '339.%%') AS exhibit_type,
			        (SELECT meta_value FROM wp_gf_entry_meta
			          WHERE meta_key = '303' AND wp_gf_entry_meta.entry_id = wp_gf_entry.ID) AS entry_status,
			        wp_mf_faire_subarea.area_id, wp_mf_faire_area.area,
			        wp_mf_location.subarea_id, wp_mf_faire_subarea.subarea, wp_mf_location.location
			   FROM wp_mf_faire, wp_gf_entry
			        LEFT OUTER JOIN wp_mf_location      ON wp_gf_entry.ID              = wp_mf_location.entry_id
			        LEFT OUTER JOIN wp_mf_faire_subarea ON wp_mf_location.subarea_id   = wp_mf_faire_subarea.id
			        LEFT OUTER JOIN wp_mf_faire_area    ON wp_mf_faire_subarea.area_id = wp_mf_faire_area.id
			  WHERE faire = %s
			    AND wp_gf_entry.status = 'active'
			    AND FIND_IN_SET (wp_gf_entry.form_id, wp_mf_faire.form_ids) > 0
			    AND FIND_IN_SET (wp_gf_entry.form_id, wp_mf_faire.non_public_forms) <= 0",
			$faire
		)
	);
 
	foreach ( $results as $row ) {
		if ( 'maker' === $signType ) {
			if ( isset( $row->exhibit_type )
				&& false === stripos( $row->exhibit_type, 'exhibit' )
				&& false === stripos( $row->exhibit_type, 'sponsor' ) ) {
				continue;
			}
		} elseif ( 'presenter' === $signType ) {
			if ( isset( $row->exhibit_type ) && false === stripos( $row->exhibit_type, 'present' ) ) {
				continue;
			}
		}
 
		if ( 'accepted' === $statusFilter && 'Accepted' !== $row->entry_status ) {
			continue;
		}
		if ( 'accAndProp' === $statusFilter && 'Accepted' !== $row->entry_status && 'Proposed' !== $row->entry_status ) {
			continue;
		}
 
		$area    = ( null !== $row->area ) ? $row->area : 'No-Area';
		$subarea = ( null !== $row->subarea ) ? $row->subarea : 'No-subArea';
 
		if ( empty( $filterFormId ) ) {
			setGrouping( $row, $entries, $area, $subarea, $type, $filterError );
		} else {
			foreach ( (array) $filterFormId as $formId ) {
				filterByForm( $formId, $row, $entries, $area, $subarea, $type, $filterError );
			}
		}
	}
 
	$base    = get_template_directory() . '/signs/' . $faire . '/';
	$written = array();
 
	foreach ( $entries as $typeKey => $entType ) {
		// FIXED: the error branch used to compute a path and discard it, leaving $filepath
		// holding the PREVIOUS iteration's value.
		$filepath = ( 'error' === $typeKey )
			? $base . 'error/' . $signType . '/'
			: $base . $signType . '/';
 
		if ( ! file_exists( $filepath . 'zip' ) ) {
			wp_mkdir_p( $filepath . 'zip' );
		}
 
		$filename = $faire . '-' . $typeKey . $appendFormId . '-faire' . $signType . '.zip';
		$zipPath  = $filepath . 'zip/' . $filename;
 
		$zip = new ZipArchive();
		// FIXED: OVERWRITE was commented out, so re-creating a zip appended to the old one
		// and stale entries were never removed.
		if ( true !== $zip->open( $zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
			error_log( "createSignZip: cannot open $zipPath" );
			continue;
		}
 
		$added   = 0;
		$missing = 0;
 
		foreach ( $entType as $status ) {
			if ( ! is_array( $status ) ) {
				continue;
			}
			foreach ( $status as $entryID ) {
				$file = $entryID . '.pdf';
				if ( file_exists( $filepath . $file ) ) {
					$zip->addFile( $filepath . $file, $file );
					$added++;
				} else {
					$missing++;
					if ( 'error' !== $typeKey ) {
						error_log( 'Missing PDF for entry Id ' . $entryID );
					}
				}
			}
		}
 
		// FIXED: close() was commented out — the archive only flushed via the destructor.
		if ( ! $zip->close() ) {
			error_log( "createSignZip: failed to write $zipPath" );
			continue;
		}
 
		$written[] = sprintf( '%s (%d files%s)', $filename, $added, $missing ? ", $missing missing" : '' );
	}
 
	error_log( 'End Zip creation' );
 
	if ( empty( $written ) ) {
		wp_send_json_error(
			array( 'msg' => 'No zip files were created — no entries matched those filters, or no PDFs exist yet. Generate the signs first.' )
		);
	}
 
	wp_send_json_success(
		array(
			'msg'   => sprintf( 'Created %d zip file%s.', count( $written ), 1 === count( $written ) ? '' : 's' ),
			'files' => $written,
		)
	);
}
add_action( 'wp_ajax_createSignZip', 'createSignZip' );
 
function setGrouping( $row, array &$entries, $area, $subarea, $type, $filterError ) {
	if ( 'area' === $type ) {
		$entries[ str_replace( ' ', '_', $area ?? '' ) ][ $row->entry_status ][] = $row->entry_id;
	} elseif ( 'subarea' === $type ) {
		$key = str_replace( ' ', '_', $area ?? '' ) . '-' . str_replace( ' ', '_', $subarea ?? '' );
		$entries[ $key ][ $row->entry_status ][] = $row->entry_id;
	} elseif ( 'faire' === $type ) {
		$entries['faire'][ $row->entry_status ][] = $row->entry_id;
	} elseif ( 'error' === $filterError ) {
		$entries['error'][ $row->entry_status ][] = $row->entry_id;
	}
}
 
function filterByForm( $form, $row, array &$entries, $area, $subarea, $type, $filterError ) {
	// Loose-typed compare: $_POST values arrive as strings and absint() makes them ints,
	// while $wpdb columns are strings. The original === matched nothing once cast.
	if ( (int) $form === (int) $row->form_id ) {
		setGrouping( $row, $entries, $area, $subarea, $type, $filterError );
	}
}
 
/* -------------------------------------------------------------------------
 * Sign generation — start (AJAX)
 * ---------------------------------------------------------------------- */
 
/**
 * Builds the entry list synchronously and marks the run as running. Generation itself
 * happens in the poll handler below. Named for the wp_ajax_createEntList action it has
 * always been hooked to.
 */
function cronCreateEntList() {
	list( $faire, $type ) = mf_sign_ajax_guard();
 
	mf_sign_release_lock( $faire, $type );
	delete_option( mf_sign_progress_key( $faire, $type ) );
	delete_transient( mf_sign_queue_key( $faire, $type ) );
 
	$entList = mf_sign_build_entry_list( $faire, $type );
 
	if ( empty( $entList ) ) {
		mf_sign_set_progress(
			$faire,
			$type,
			array( 'state' => 'done', 'total' => 0, 'done' => 0, 'ok' => 0, 'fail' => 0, 'offset' => 0 )
		);
		wp_send_json_error(
			array( 'msg' => 'No entries matched for this faire and sign type — nothing to generate. Check the entry statuses and the form_type values on the faire\'s forms.' )
		);
	}
 
	set_transient( mf_sign_queue_key( $faire, $type ), $entList, 12 * HOUR_IN_SECONDS );
 
	mf_sign_set_progress(
		$faire,
		$type,
		array(
			'state'   => 'running',
			'total'   => count( $entList ),
			'done'    => 0,
			'ok'      => 0,
			'fail'    => 0,
			'offset'  => 0,
			'started' => time(),
		)
	);
 
	error_log( sprintf( 'Start mass generate signs for %s - %s (%d signs to generate)', $faire, $type, count( $entList ) ) );
 
	wp_send_json_success(
		array(
			'msg'   => sprintf( 'Found %d entries. Generating&hellip;', count( $entList ) ),
			'total' => count( $entList ),
		)
	);
}
add_action( 'wp_ajax_createEntList', 'cronCreateEntList' );
 
/* -------------------------------------------------------------------------
 * Sign generation — poll + do work (AJAX)
 * ---------------------------------------------------------------------- */
 
/**
 * Each poll does a slice of the work, then reports progress. This is what replaces the
 * WP-Cron dependency: no loopback request, no DISABLE_WP_CRON, no self-signed-cert problem
 * on local, and the admin can see it happening.
 */
function mf_ajax_sign_status() {
	list( $faire, $type ) = mf_sign_ajax_guard();
 
	$progress = mf_sign_get_progress( $faire, $type );
 
	if ( empty( $progress ) ) {
		wp_send_json_success( array( 'state' => 'idle' ) );
	}
 
	if ( 'running' === ( isset( $progress['state'] ) ? $progress['state'] : '' ) ) {
		if ( mf_sign_acquire_lock( $faire, $type ) ) {
			try {
				mf_run_sign_batch( $faire, $type, MF_SIGN_POLL_BUDGET );
			} finally {
				mf_sign_release_lock( $faire, $type );
			}
			$progress = mf_sign_get_progress( $faire, $type );
		} else {
			// Another tab or a headless run holds the lock; just report.
			$progress['locked'] = true;
		}
	}
 
	wp_send_json_success( $progress );
}
add_action( 'wp_ajax_mf_signStatus', 'mf_ajax_sign_status' );
 
/* -------------------------------------------------------------------------
 * Sign generation — headless entry point (optional)
 * ---------------------------------------------------------------------- */
 
/**
 * Kept so a run can be driven without a browser:
 *     wp cron event run create_mf_signs
 *     do_action( 'create_mf_signs', 'BA26', 'signs' );
 * Shares the lock with the browser path, so the two cannot double-generate.
 */
function createEntList( $faire, $type ) {
	if ( ! mf_sign_acquire_lock( $faire, $type ) ) {
		error_log( "createEntList: a run for $faire/$type is already in progress; skipping." );
		return;
	}
 
	try {
		$entList = get_transient( mf_sign_queue_key( $faire, $type ) );
		$progress = mf_sign_get_progress( $faire, $type );
 
		// Resume an unfinished queue rather than rebuilding it.
		$resuming = is_array( $entList )
			&& ! empty( $entList )
			&& isset( $progress['state'] )
			&& 'running' === $progress['state'];
 
		if ( ! $resuming ) {
			$entList = mf_sign_build_entry_list( $faire, $type );
			set_transient( mf_sign_queue_key( $faire, $type ), $entList, 12 * HOUR_IN_SECONDS );
			mf_sign_set_progress(
				$faire,
				$type,
				array(
					'state'   => 'running',
					'total'   => count( $entList ),
					'done'    => 0,
					'ok'      => 0,
					'fail'    => 0,
					'offset'  => 0,
					'started' => time(),
				)
			);
		}
 
		error_log( sprintf( 'Headless sign run for %s - %s (%d in queue)', $faire, $type, count( $entList ) ) );
 
		if ( empty( $entList ) ) {
			mf_sign_finish_run( $faire, $type );
			return;
		}
 
		mf_run_sign_batch( $faire, $type, MF_SIGN_CRON_BUDGET );
	} finally {
		mf_sign_release_lock( $faire, $type );
	}
}
add_action( 'create_mf_signs', 'createEntList', 10, 2 );
 
/* -------------------------------------------------------------------------
 * Sign generation — the work
 * ---------------------------------------------------------------------- */
 
/**
 * Builds the list of entry ids to generate signs for.
 */
function mf_sign_build_entry_list( $faire, $type ) {
	global $wpdb;
 
	$entList = array();
 
	if ( 'presenter' !== $type ) {
		$formIds = $wpdb->get_var( $wpdb->prepare( "SELECT form_ids FROM wp_mf_faire WHERE faire = %s", $faire ) );
		$forms   = explode( ',', str_replace( ' ', '', $formIds ?? '' ) );
 
		foreach ( $forms as $formId ) {
			$formId = absint( $formId );
			if ( ! $formId ) {
				continue;
			}
 
			$form     = GFAPI::get_form( $formId );
			$formType = isset( $form['form_type'] ) ? $form['form_type'] : '';
 
			if ( ! in_array( $formType, array( 'Master', 'Exhibit', 'Sponsor', 'Startup Sponsor' ), true ) ) {
				continue;
			}
 
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT wp_gf_entry.id AS lead_id,
					        wp_gf_entry_meta.meta_value AS lead_status,
					        (SELECT GROUP_CONCAT(meta_value) FROM wp_gf_entry_meta meta2
					          WHERE meta2.entry_id = wp_gf_entry.id AND meta2.meta_key LIKE '339.%%') AS exhibit_type
					   FROM wp_gf_entry, wp_gf_entry_meta
					  WHERE status = 'active' AND meta_key = '303'
					    AND wp_gf_entry_meta.entry_id = wp_gf_entry.id
					    AND wp_gf_entry_meta.meta_value NOT IN ('Rejected','Cancelled','No Response')
					    AND wp_gf_entry.form_id = %d",
					$formId
				)
			);
 
			foreach ( $results as $entry ) {
				if ( isset( $entry->exhibit_type )
					&& false === stripos( $entry->exhibit_type, 'exhibit' )
					&& false === stripos( $entry->exhibit_type, 'sponsor' ) ) {
					// Table tags also cover show-management records.
					if ( 'tabletags' === $type && false === stripos( $entry->exhibit_type, 'show' ) ) {
						continue;
					}
				}
				$entList[] = (int) $entry->lead_id;
			}
		}
	} else {
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT entity.lead_id AS entry_id
				   FROM wp_mf_schedule schedule, wp_mf_entity entity
				  WHERE schedule.entry_id = entity.lead_id
				    AND type = 'presentation'
				    AND entity.status = 'Accepted'
				    AND schedule.faire = %s
				  GROUP BY entity.lead_id",
				$faire
			)
		);
		foreach ( $results as $entry ) {
			$entList[] = (int) $entry->entry_id;
		}
	}
 
	return array_values( array_unique( $entList ) );
}
 
/**
 * Generates signs until the time budget is spent or the queue is exhausted.
 * The offset lives in the progress option, so any caller can resume.
 */
function mf_run_sign_batch( $faire, $type, $budget ) {
	$entList = get_transient( mf_sign_queue_key( $faire, $type ) );
 
	if ( ! is_array( $entList ) ) {
		error_log( "mf_run_sign_batch: queue for $faire/$type expired or missing." );
		mf_sign_set_progress(
			$faire,
			$type,
			array( 'state' => 'error', 'msg' => 'The queue expired before the run finished. Click Generate again to restart it.' )
		);
		return;
	}
 
	$progress = mf_sign_get_progress( $faire, $type );
	$total    = count( $entList );
	$offset   = isset( $progress['offset'] ) ? (int) $progress['offset'] : 0;
	$ok       = isset( $progress['ok'] ) ? (int) $progress['ok'] : 0;
	$fail     = isset( $progress['fail'] ) ? (int) $progress['fail'] : 0;
	$start    = microtime( true );
 
	while ( $offset < $total && ( microtime( true ) - $start ) < $budget ) {
		$result = mf_generate_one_sign( $entList[ $offset ], $type, $faire );
 
		if ( $result['ok'] ) {
			$ok++;
		} else {
			$fail++;
			error_log(
				sprintf(
					'Sign generation FAILED for entry %d (%s/%s): %s',
					$entList[ $offset ],
					$faire,
					$type,
					$result['error']
				)
			);
			// Surface the first failure in the UI — usually every failure has one cause.
			if ( 1 === $fail ) {
				mf_sign_set_progress( $faire, $type, array( 'firstError' => $result['error'] ) );
			}
		}
 
		$offset++;
	}
 
	mf_sign_set_progress(
		$faire,
		$type,
		array(
			'state'  => $offset < $total ? 'running' : 'done',
			'total'  => $total,
			'done'   => $offset,
			'offset' => $offset,
			'ok'     => $ok,
			'fail'   => $fail,
		)
	);
 
	if ( $offset >= $total ) {
		mf_sign_finish_run( $faire, $type );
	}
}
 
/**
 * Fetch one PDF from the generator script.
 *
 * The generator scripts bootstrap WordPress themselves and write the file server-side, so
 * this has to be an HTTP request. It leaves the origin and comes back in through Cloudflare —
 * the old IE 6 user agent was a reliable Bot Fight Mode trigger and the result was discarded,
 * so every 403 was invisible.
 */
function mf_generate_one_sign( $entryID, $type, $faire ) {
	$map = mf_sign_type_map( $type );
 
	$url = add_query_arg(
		array( 'eid' => (int) $entryID, 'type' => 'save', 'faire' => $faire ),
		get_template_directory_uri() . '/generate_pdf/' . $map['script'] . '.php'
	);
 
	$args = array(
		'timeout'     => MF_SIGN_HTTP_TIMEOUT,
		'redirection' => 0,
		'user-agent'  => 'MakerFaire-SignGenerator/1.0 (+https://makerfaire.com)',
		'headers'     => array(),
		'cookies'     => array(),
	);
 
	// Shared secret so a Cloudflare skip rule can let this through without opening the
	// path to the world. Define MF_SIGN_TOKEN in wp-config.php.
	if ( defined( 'MF_SIGN_TOKEN' ) && MF_SIGN_TOKEN ) {
		$args['headers']['X-MF-Sign-Token'] = MF_SIGN_TOKEN;
	}
 
	// Local dev runs on a self-signed cert.
	if ( false !== strpos( home_url(), '.local' ) ) {
		$args['sslverify'] = false;
	}
 
	$response = wp_remote_get( $url, $args );
 
	if ( is_wp_error( $response ) ) {
		return array( 'ok' => false, 'error' => 'HTTP error: ' . $response->get_error_message() );
	}
 
	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = wp_remote_retrieve_body( $response );
 
	if ( 200 !== $code ) {
		$hint = '';
		if ( 403 === $code ) {
			$hint = ' — almost certainly Cloudflare blocking the origin calling itself. Add a WAF skip rule for /wp-content/themes/makerfaire/generate_pdf/ matching the X-MF-Sign-Token header.';
		} elseif ( 301 === $code || 302 === $code ) {
			$hint = ' — the generator URL is redirecting (http/https or a trailing-slash rule). Check get_template_directory_uri().';
		}
		return array(
			'ok'    => false,
			'error' => 'HTTP ' . $code . $hint . ' | body: ' . substr( wp_strip_all_tags( $body ), 0, 200 ),
		);
	}
 
	// A PHP fatal or a challenge page shows up as HTML here.
	if ( false !== stripos( $body, '<html' ) ) {
		return array(
			'ok'    => false,
			'error' => 'Got an HTML page instead of a PDF write: ' . substr( trim( wp_strip_all_tags( $body ) ), 0, 200 ),
		);
	}
 
	// Confirm the file actually landed on disk — a 200 alone proves nothing here.
	$dir      = get_template_directory() . '/signs/' . $faire . '/' . $map['folder'] . '/';
	$expected = $dir . (int) $entryID . '.pdf';
	$errPath  = $dir . 'error/' . (int) $entryID . '.pdf';
 
	if ( ! file_exists( $expected ) && ! file_exists( $errPath ) ) {
		return array(
			'ok'    => false,
			'error' => 'Script returned 200 but no PDF was written to ' . $expected
				. ( '' !== trim( $body ) ? ' | output: ' . substr( trim( $body ), 0, 200 ) : ' | no output' ),
		);
	}
 
	return array( 'ok' => true, 'error' => '' );
}
 
/**
 * Write lastrun.txt with counts. The old version wrote a bare timestamp unconditionally, so
 * a run where every sign 403'd still looked like a success.
 */
function mf_sign_finish_run( $faire, $type ) {
	$map      = mf_sign_type_map( $type );
	$progress = mf_sign_get_progress( $faire, $type );
 
	$ok    = isset( $progress['ok'] ) ? (int) $progress['ok'] : 0;
	$fail  = isset( $progress['fail'] ) ? (int) $progress['fail'] : 0;
	$total = isset( $progress['total'] ) ? (int) $progress['total'] : 0;
 
	$dir = get_template_directory() . '/signs/' . $faire . '/' . $map['folder'];
	if ( ! file_exists( $dir ) ) {
		wp_mkdir_p( $dir );
	}
 
	$stamp = wp_date( 'm-d-y  h:i:s A T' );
	$line  = $fail
		? sprintf( '%s — %d of %d generated, %d FAILED (see error log)', $stamp, $ok, $total, $fail )
		: sprintf( '%s — %d of %d generated', $stamp, $ok, $total );
 
	file_put_contents( $dir . '/lastrun.txt', $line );
 
	mf_sign_set_progress( $faire, $type, array( 'state' => 'done', 'msg' => $line ) );
	delete_transient( mf_sign_queue_key( $faire, $type ) );
 
	error_log( sprintf( 'End mass generate signs for %s - %s. %s', $faire, $type, $line ) );
}
 
