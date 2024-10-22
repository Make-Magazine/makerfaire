<?php
add_filter('gravityview-inline-edit/edit-mode', 'modify_inline_display', 10, 1 );
function modify_inline_display( $mode = '' ) {
  return 'inline';
}

//hide the Approve/Reject Entry column in entry list
add_filter('gravityview/approve_entries/hide-if-no-connections', '__return_true');


add_filter( 'gravityview/edit_entry/form_fields', function ( $fields ) {
	$view_id = GravityView_View::getInstance()->getViewId();

	//BA24 edit public info
	if ( $view_id !== 687928 ) { 
		return $fields;
	}

	foreach ( $fields as &$field ) {
		if ( 'email' === $field->type && $field->emailConfirmEnabled ) {
			$field->emailConfirmEnabled = false;
		}
	}

	return $fields;
} );

add_filter( 'gravityview/edit_entry/field_value', function ( $value, $field ) {
	$view_id = GravityView_View::getInstance()->getViewId();

	//BA24 edit public info
	if ( $view_id !== 687928 ) { 
		return $value;
	}

	if ( 'email' === $field->type && is_array( $value ) ) {
		$value = implode( '', $value );
	}

	return $value;
}, 10, 2 );