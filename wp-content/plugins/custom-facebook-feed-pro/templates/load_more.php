<?php
/**
 * Custom Facebook Load More Button Template
 * Display the Facebook load more button
 *
 * @version 3.18 Custom Facebook Feed by Smash Balloon
 *
 */

use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Shortcode_Display;
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$load_more_attributes 	= CFF_Shortcode_Display::get_load_more_button_attr( $atts );
$cff_load_more_styles 	= $this_class->get_style_attribute( 'load_more' );
$cff_load_more_text 	= CFF_Utils::return_value( stripslashes( $atts['buttontext'] ),  esc_html__('Load more','custom-facebook-feed')) ;
?>
<input type="hidden" class="cff-pag-url" data-cff-pag-url="<?php echo $next_urls_arr_safe ?>" data-cff-prev-url="<?php echo $prev_urls_arr_safe ?>" data-transient-name="<?php echo $facebook_settings->get_transient_name(); ?>" data-post-id="<?php echo get_the_ID() ?>" data-feed-id="<?php echo $atts['id'] ?>"  value="">
<a href="javascript:void(0);" id="cff-load-more" class="cff-load-more" <?php echo $cff_load_more_styles ?> <?php echo $load_more_attributes ?>><span><?php echo $cff_load_more_text ?></span></a>