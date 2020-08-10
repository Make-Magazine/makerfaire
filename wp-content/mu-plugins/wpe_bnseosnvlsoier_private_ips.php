<?php
/**
 * Plugin Name: Whitelist Private IPs
 * Plugin URI: https://gist.github.com/JPry/8d033462611e4dff36ea
 * Description: Whitelists the range of private IP addresses for the Limit Login Attempts plugin.
 * Version: 1.0
 * Author: Jeremy Pry <jeremy@wpengine.com>
 * Author URI: http://jeremypry.com
 * License GPL3
 */

add_filter( 'limit_login_whitelist_ip', 'wpe_bnseosnvlsoie', 10, 2 );
/**
 * Ensure that certain IP addresses are whitelisted.
 * 
 * This function compares the current IP address against a list of IPs to whitelist. If the current
 * IP is found among them, then it returns true.
 * 
 * Uses code from <link>http://stackoverflow.com/questions/594112/matching-an-ip-to-a-cidr-mask-in-php5</link>.
 * 
 * @author Jeremy Pry <jeremy@wpengine.com>
 * @param boolean $allow Whether the current IP is allowed.
 * @param string  $ip    The current IP address.
 * @return boolean True if IP is in the whitelist, otherwise the default <var>$allow</var> value.
 */
function wpe_bnseosnvlsoie( $allow, $ip ) {
	// Only process if the IP isn't already allowed
	if ( ! $allow ) {
		$ranges = array(
			'10.0.0.0/8',
			'127.0.0.0/8',
			'172.16.0.0/12',
			'192.168.0.0/16',
		);

		// Set up IP for checking
		$ip = ip2long( $ip );

		foreach ( $ranges as $range ) {
			// Break apart the range
			list ( $subnet, $bits ) = explode( '/', $range );

			// Set up the subnet for checking
			$subnet = ip2long( $subnet );

			// Bitwise check to see if the IP falls within the subnet
			$mask = -1 << ( 32 - $bits );
			$subnet &= $mask;

			// Final check for whether to allow the IP
			if ( ( $ip & $mask ) == $subnet ) {
				$allow = true;

				// If we have a match, don't bother continuing the loop
				break;
			}
		}
	}

	return $allow;
}