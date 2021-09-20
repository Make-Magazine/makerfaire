<?php
/**
 * Displays post entry content
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="clr">
	<?php
	$cptype = $post->post_type;
	the_content();
	?>

</div>
