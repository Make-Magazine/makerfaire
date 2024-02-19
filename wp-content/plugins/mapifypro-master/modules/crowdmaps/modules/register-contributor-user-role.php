<?php

/**
 * Add custom user role named "mpfy_contributor".
 * Then add custom capabilities to it.
 * 
 * @return void
 */
function mpfy_register_contributor_user_role() {
	$mpfy_contributor = get_role( 'mpfy_contributor' );	
	$cap_type         = array(
		'singular' => 'map_location',
		'plural'   => 'map_locations',
	);

	// create the mpfy_contributor role if not exist
	if ( ! $mpfy_contributor ) {
		$mpfy_contributor = add_role( 'mpfy_contributor', 'MapifyPro Contributor', array( 
			'read' => true,
		) );
	}
	
	// add custom caps to mpfy_contributor role
	if ( $mpfy_contributor ) {
		$mpfy_contributor->add_cap( "read_{$cap_type['plural']}" );
		$mpfy_contributor->add_cap( "edit_{$cap_type['plural']}" ); 
		$mpfy_contributor->add_cap( "edit_{$cap_type['singular']}" ); 
		$mpfy_contributor->add_cap( "edit_published_{$cap_type['plural']}" ); 
		$mpfy_contributor->add_cap( "delete_{$cap_type['plural']}" ); 
		$mpfy_contributor->add_cap( "delete_{$cap_type['singular']}" );  
		$mpfy_contributor->add_cap( "delete_published_{$cap_type['plural']}" ); 
		$mpfy_contributor->add_cap( "upload_files" ); 
	}
}

mpfy_register_contributor_user_role();