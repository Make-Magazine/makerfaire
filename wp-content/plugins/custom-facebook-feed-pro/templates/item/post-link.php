<?php
/**
 * Custom Facebook Feed Item : Post Link Template
 * Displays the item post link
 *
 * @version 3.18 Custom Facebook Feed by Smash Balloon
 *
 */
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Shortcode_Display;
use CustomFacebookFeed\CFF_Display_Elements_Pro;
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


$cff_link_styles 			= $this_class->get_style_attribute( 'post_link' );
$cff_show_facebook_link 	= CFF_Utils::check_if_on( $feed_options['showfacebooklink'] );
$cff_show_facebook_share 	= CFF_Utils::check_if_on( $feed_options['showsharelink'] );
$cff_post_text_to_share 	= CFF_Shortcode_Display::get_post_link_text_to_share( $cff_post_text );
$link_text 					= CFF_Shortcode_Display::get_post_link_text_link( $feed_options, $cff_post_type );
$social_share_links 		= CFF_Shortcode_Display::get_post_link_social_links( $link, $cff_post_text_to_share );
$cff_facebook_share_text 	= CFF_Shortcode_Display::get_post_link_fb_share_text( $feed_options );
if( $cff_show_facebook_link || $cff_show_facebook_share ):
	?>
<div class="cff-post-links">
	<?php if( $cff_show_facebook_link ):
        ?>
		<a class="cff-viewpost-facebook" href="<?php echo esc_url($link) ?>" title="<?php echo esc_attr($link_text) ?>" <?php echo $target. '' .$cff_nofollow . ' '. $cff_link_styles; ?>><?php echo esc_html( $link_text ); ?></a>
	<?php endif; ?>
	<?php if( $cff_show_facebook_share ): ?>
		<div class="cff-share-container">
			<?php if( $cff_show_facebook_share ):
                if( $cff_show_facebook_link && $cff_show_facebook_share ):?>
				<span class="cff-dot" <?php echo $cff_link_styles ?>>&middot;</span>
				 <?php endif; ?>
				<a class="cff-share-link" href="<?php echo esc_url($social_share_links['facebook']['share_link']); ?>" title="<?php echo esc_attr($cff_facebook_share_text) ?>" <?php echo $cff_link_styles ?>><?php echo esc_html( $cff_facebook_share_text ) ?></a>
				<div class="cff-share-tooltip">
					<?php foreach ($social_share_links as $social_key => $social) : ?>
						<a href="<?php echo esc_url($social['share_link']) ?>" target="_blank" class="cff-<?php echo $social_key ?>-icon">                        
							<?php echo CFF_Display_Elements_Pro::get_icon( $social['icon'] ); ?>
							<span class="cff-screenreader"><?php echo $social['text'] ?></span>
						</a>
					<?php endforeach; ?>
                <?php echo CFF_Display_Elements_Pro::get_icon( 'play' ) ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
<?php
endif;