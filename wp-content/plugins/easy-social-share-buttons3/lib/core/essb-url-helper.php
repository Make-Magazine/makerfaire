<?php

/**
 * Add tracking code to the URL (essb_attach_tracking_code)
 * 
 * @param string $url
 * @param string $code
 * @return string
 */
function essb_attach_tracking_code($url, $code = '') {
	$posParamSymbol = strpos($url, '?');

	$code = str_replace('&', '%26', $code);

	if ($posParamSymbol === false) {
		$url .= '?';
	}
	else {
		$url .= '%26';
	}

	$url .= $code;
		
	return $url;
}

function essb_get_current_url($mode = 'base') {

	if (isset($_SERVER ['HTTP_HOST']) && isset($_SERVER ['REQUEST_URI'])) {
		$url = 'http' . (is_ssl () ? 's' : '') . '://' . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
	}
	else {
		global $wp;
		$url = home_url( add_query_arg( array(), $wp->request ) );
	}

	$return_url = $url;
	
	switch ($mode) {
		case 'raw' :
			$return_url = $url;
			break;
		case 'base' :
			$return_url = reset ( explode ( '?', $url ) );
			break;
		case 'uri' :
			$exp = explode ( '?', $url );
			$return_url = trim ( str_replace ( home_url (), '', reset ( $exp ) ), '/' );
			break;
		default :
			$return_url = '';
	}
	
	$return_url = esc_url(sanitize_text_field($return_url));
	
	return $return_url;
}


function essb_get_current_page_url() {
	if (isset($_SERVER['SERVER_PORT']) && isset($_SERVER['SERVER_NAME'])) {
		$pageURL = 'http';
		if(isset($_SERVER['HTTPS']))
			if ($_SERVER['HTTPS'] == 'on') {
			$pageURL .= 's';
		}
		$pageURL .= '://';
		$current_request_uri = $_SERVER['REQUEST_URI'];
		
		// this is made to escape possible blocking share parameters. We honor query string but those parameters
		// will block sharing
		$current_request_uri = str_replace('&u=', '&u0=', $current_request_uri);
		$current_request_uri = str_replace('&t=', '&t0=', $current_request_uri);
		$current_request_uri = str_replace('&title=', '&title0=', $current_request_uri);
		$current_request_uri = str_replace('&url=', '&url0=', $current_request_uri);
			
			
		if ($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') {
			$pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $current_request_uri;
		} else {
			$pageURL .= $_SERVER['SERVER_NAME'] . $current_request_uri;
		}
		
		$pageURL = esc_url(sanitize_text_field($pageURL));
	}
	else {
		global $wp;
		$pageURL = home_url( add_query_arg( array(), $wp->request ) );
	}
	return $pageURL;
}
?>