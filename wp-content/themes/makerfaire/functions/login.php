<?php

add_filter( 'login_url', 'custom_login_url', 10, 2 );

function custom_login_url( $login_url='', $redirect='') {
  if ( ! empty( $redirect ) ) {
		$login_url = remove_query_arg( 'redirect_to', $login_url );
		$redirect = add_query_arg( 'logged_in', 1, $redirect );
		$redirect = urlencode( $redirect );
		$login_url = add_query_arg( 'redirect_to', $redirect, $login_url );
	}

  return $login_url;
}