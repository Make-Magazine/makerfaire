<?php
/**
 * Check to see if an email is valid.
 * Test $input[$k['id']]
 * Set $k['error_msg'] and $is_valid
 */



if ( !empty( $input[ $k[ 'id' ] ] ) ) {
	$input[ $k[ 'id' ] ] = esc_url($input[ $k[ 'id' ] ]);
}