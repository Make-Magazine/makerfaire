<?php
/**
 * Custom Facebook Feed Item : Date Template
 * Displays the item date
 *
 * @version 3.18 Custom Facebook Feed by Smash Balloon
 *
 */
use CustomFacebookFeed\CFF_Shortcode_Display;
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$cff_date_styles = $this_class->get_style_attribute( 'date' );
$cff_date_classes = CFF_Shortcode_Display::get_date_classes( $cff_date_position,$cff_show_author );
?>

<div class="cff-date <?php echo $cff_date_classes ?>" <?php echo $cff_date_styles ?>>
	<?php echo CFF_Shortcode_Display::get_date( $feed_options, $news ); ?>
</div>