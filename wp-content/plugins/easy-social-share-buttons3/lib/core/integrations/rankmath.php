<?php

/**
 * Deactivate Rank Math generated social share optimization tags
 * 
 * @package EasySocialShareButtons
 * @since 6.3
 */

/**
* Hook to remove og:tags. Snippet provided by the Rank Math support team
*/
add_action( 'rank_math/head', function() {
	remove_all_actions( 'rank_math/opengraph/facebook' );
	remove_all_actions( 'rank_math/opengraph/twitter' );
});