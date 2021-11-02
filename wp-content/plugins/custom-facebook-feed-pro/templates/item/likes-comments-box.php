<?php
/**
 * Custom Facebook Feed Item : likes-comments-box Template
 * Displays the item meta Likes & Comments
 *
 * @version 3.18 Custom Facebook Feed by Smash Balloon
 *
 */
use CustomFacebookFeed\CFF_Shortcode_Display;
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$btn_class 	= CFF_Shortcode_Display::get_like_comment_btn_classes( $cff_lightbox_comments , $cff_show_meta );

?>
<div class="cff-view-comments-wrap">
	<a href="javaScript:void(0);" <?php echo $btn_class.''.$cff_meta_styles ?> id="<?php echo $orig_post_id ?>">
		<span class="cff-screenreader"><?php echo esc_html__('View Comments','custom-facebook-feed') ?></span>
		<ul class="cff-meta <?php echo $cff_icon_style ?>">
			<li class="cff-likes">
				<span class="cff-icon cff-like">
					<span class="cff-screenreader"><?php echo esc_html__('Likes:','custom-facebook-feed') ?></span>
					<?php echo $l_c_s_info['like']['icon']; ?>
				</span>
				<span class="cff-count"><?php echo $l_c_s_info['like']['count']; ?></span>
			</li>	
			<li class="cff-shares">
				<span class="cff-icon cff-share">
					<span class="cff-screenreader"><?php echo esc_html__('Shares:','custom-facebook-feed') ?></span>
					<?php echo $l_c_s_info['share']['icon']; ?>
				</span>
				<span class="cff-count"><?php echo $l_c_s_info['share']['count']; ?></span>
			</li>
			<li class="cff-comments">
				<span class="cff-icon cff-comment">
					<span class="cff-screenreader"><?php echo esc_html__('Comments:','custom-facebook-feed') ?></span>
					<?php echo $l_c_s_info['comment']['icon']; ?>
				</span>
				<span class="cff-count"><?php echo $l_c_s_info['comment']['count']; ?></span>
			</li>
		</ul>
	</a>
</div>
