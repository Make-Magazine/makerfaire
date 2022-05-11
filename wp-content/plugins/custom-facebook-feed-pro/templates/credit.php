<?php
/**
 * Custom Facebook Feed : Credit
 * Show a credit message to Smashballoon
 *
 * @version 3.18 Custom Facebook Feed by Smash Balloon
 *
*/
use CustomFacebookFeed\CFF_Utils;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$cff_show_credit = CFF_Utils::check_if_on( $atts['credit'] );

if($cff_show_credit) :
?>
<p class="cff-credit"><a href="https://smashballoon.com/custom-facebook-feed/" target="_blank" style="color: #<?php echo $cff_posttext_link_color ?>" title="<?php echo esc_attr('Smash Balloon Custom Facebook Feed WordPress Plugin') ?>"><span class="cff-credit-logo"></span>The Custom Facebook Feed plugin</a></p>
<?php
endif; 