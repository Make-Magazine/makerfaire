<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
add_filter( 'wt_cli_third_party_scripts', 'wt_cli_twitter_feed_script' );
function wt_cli_twitter_feed_script( $tags ) {
	$tags[] = 'plugins/custom-twitter-feeds/js/ctf-scripts.js';
	$tags[] = 'plugins/custom-twitter-feeds/js/ctf-scripts.min.js';
	$tags[] = 'plugins/custom-twitter-feeds-pro/js/ctf-scripts.js';
	$tags[] = 'plugins/custom-twitter-feeds-pro/js/ctf-scripts.min.js';
	return $tags;
}