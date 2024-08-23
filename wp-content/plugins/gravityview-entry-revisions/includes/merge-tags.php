<?php

add_filter( 'gform_custom_merge_tags', 'gv_revisions_add_merge_tag_option', 10, 4 );

/**
 * Adds our merge tags to the "Custom" group of Gravity Forms Merge Tags
 *
 * @since 1.0
 *
 * @param array $custom_group Existing merge tags in the group (with `tag` and `label` keys)
 * @param int $form_id ID of the form
 * @param GF_Field[] $fields Form fields
 * @param string $element_id ID of the Merge Tag HTML DOM
 *
 * @return array
 */
function gv_revisions_add_merge_tag_option( $custom_group = array(), $form_id = 0, $fields = array(), $element_id = '' ) {

	$custom_group[] = array(
		'tag'   => '{entry_revision_list}',
		'label' => esc_html__( 'Entry Revisions List', 'gk-gravityrevisions' ),
	);

	$custom_group[] = array(
		'tag'   => '{entry_revision_diff}',
		'label' => esc_html__( 'Entry Changed Fields', 'gk-gravityrevisions' ),
	);

	$custom_group[] = array(
		'tag'   => '{entry_revision_all_fields}',
		'label' => esc_html__( 'All Revision Fields', 'gk-gravityrevisions' ),
	);

	// Only add {date_updated} for less than GF 2.5
	if( version_compare( '2.5-beta', \GFForms::$version, '>' ) ) {
		$custom_group[] = array(
			'tag'   => '{date_updated}',
			'label' => esc_html__( 'Date Updated', 'gk-gravityrevisions' ),
		);
	}

	return $custom_group;
}

add_filter( 'gform_pre_replace_merge_tags', 'gv_revisions_merge_tag_filter', 10, 7 );

/**
 * Replaces Entry Revisions Merge tags with their values
 *
 * @since 1.0
 *
 * @param string      $original_text The text in which merge tags are being processed.
 * @param false|array $form          The Form object if available or false.
 * @param false|array $entry         The Entry object if available or false.
 * @param bool        $url_encode    Indicates if the urlencode function should be applied.
 * @param bool        $esc_html      Indicates if the esc_html function should be applied.
 * @param bool        $nl2br         Indicates if the nl2br function should be applied.
 * @param string      $format        The format requested for the location the merge is being used. Possible values: html, text or url.
 */
function gv_revisions_merge_tag_filter( $original_text = '', $form = array(), $entry = array(), $url_encode = false, $esc_html = true, $nl2br = true, $format = 'html' ) {

	if ( empty( $entry['id'] ) || empty( $entry['form_id'] ) ) {
		return $original_text;
	}

	// "We don't need no / recursive functions / we don't need no / exception control"
	remove_filter( 'gform_pre_replace_merge_tags', __FUNCTION__ );

	$text = $original_text;

	// If in GravityView Edit Entry, the visible form fields will be modified. Fetch fresh.
	$form = GFAPI::get_form( $entry['form_id'] );

	$GV_Entry_Revisions = GV_Entry_Revisions::get_instance();

	if ( false !== strpos( $text, '{date_updated}' ) ) {
		$date_updated = rgar( $entry, 'date_updated' );
		$text         = str_replace( '{date_updated}', GFCommon::format_date( $date_updated, false, '', false ), $text );
	}

	if ( false !== strpos( $text, '{entry_revision_list}' ) ) {
		$text = str_replace( '{entry_revision_list}', $GV_Entry_Revisions->get_revisions_list_html( $entry['id'], array( 'container_css' => 'gv-entry-revisions' ) ), $text );
	}

	$matches = array();

	$is_entry_revision_diff = false !== strpos( $text, '{entry_revision_diff}' );

	// We need to catch {all_fields} as well; GV Edit Entry modifies the form if we let GF handle it later in the flow
	preg_match_all( "/{(entry_revision_)?all_fields(:(.*?))?}/", $text, $matches, PREG_SET_ORDER );

	// Return early; our work here is done. There are no more {entry_revision_*} fields or {all_fields}.
	// Returning here prevents the need to fetch the latest revision and loading in the form.
	if ( ! $matches && ! $is_entry_revision_diff ) {
		// Restore the filter now that the threat of recursion has passed.
		add_filter( 'gform_pre_replace_merge_tags', __FUNCTION__, 10, 7 );

		return $text;
	}

	if( function_exists( 'gravityview' ) && gravityview()->request->is_edit_entry( $entry['form_id'] ) ) {
		// If in GravityView Edit Entry, the visible form fields will be modified. Fetch fresh.
		$form = GFAPI::get_form( $entry['form_id'] );
	} elseif ( class_exists( '\GV\GF_Form' ) ) {
		// Otherwise, fetch from the primed cache in GravityView
		$form = \GV\GF_Form::by_id( $entry['form_id'] );
	} else {
		$form = GFFormsModel::get_form_meta( $entry['form_id'] ) ;
	}

	$last_revision = $GV_Entry_Revisions->get_latest_revision( $entry['id'] );

	foreach ( $matches as $match ) {
		$is_revision      = ! empty( $match[1] );
		$options          = explode( ',', rgar( $match, 3 ) );
		$merge_tag        = $is_revision ? 'entry_revision_all_fields' : 'all_fields';
		$entry_to_display = $is_revision ? $last_revision : $entry;
		$display_empty    = in_array( 'empty', $options );
		$use_value        = in_array( 'value', $options );
		$use_admin_label  = in_array( 'admin', $options );

		if ( $is_revision && ! $last_revision ) {
			$text = str_replace( $match[0], esc_html__( 'This entry has no revisions.', 'gk-gravityrevisions' ), $text );
		} else {
			$text = str_replace( $match[0], GFCommon::get_submitted_fields( $form, $entry_to_display, $display_empty, ! $use_value, $format, $use_admin_label, $merge_tag, rgar( $match, 3 ) ), $text );
		}
	}

	if ( $is_entry_revision_diff ) {

		if ( $last_revision ) {
			$text = str_replace( '{entry_revision_diff}', $GV_Entry_Revisions->get_diff_html( $entry, $last_revision ), $text );
		} else {
			$text = str_replace( '{entry_revision_diff}', esc_html__( 'This entry has no revisions.', 'gk-gravityrevisions' ), $text );
		}

		switch ( true ) {
			case is_admin():
			case did_action( 'gform_notification' ):
			case did_action( 'gform_format_email_to' ):
			case did_action( 'gform_notification_format' ):
			case did_action( 'gravityview-notifications/notification/data' ):
			case function_exists( 'gravityview' ) && gravityview()->request->is_edit_entry():
				$in_email = true;
				break;
			default:
				$in_email = false;
		}

		/**
		 * @filter `gravityview/entry-revisions/embed-css` Whether to include <style> with inline CSS rules with the output
		 * @since 1.0
		 * @param boolean $embed_css The default is determined by whether a notification is currently being sent
		 */
		$embed_css = apply_filters( 'gravityview/entry-revisions/embed-css', $in_email );

		// TODO: Should we load the CSS into a DB option?
		if ( $embed_css && 'html' === $format ) {

			$css = file_get_contents( GV_ENTRY_REVISIONS_DIR . 'assets/css/entry-revisions.css' );

			if ( $css ) {
				$text .= '<style>' . $css . '</style>';
			}
		}
	}

	// Restore the filter now that the threat of recursion has passed.
	add_filter( 'gform_pre_replace_merge_tags', __FUNCTION__, 10, 7 );

	return $text;
}
