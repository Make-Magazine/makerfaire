<?php
/**
 * Custom Facebook Feed Item : Post Text Template
 * Displays the custom feed item post text 
 *
 * @version 3.18 Custom Facebook Feed by Smash Balloon
 *
 */
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Autolink;
use CustomFacebookFeed\CFF_Shortcode_Display;
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<<?php echo $cff_title_format.' '.$cff_title_styles ?> class="cff-post-text">
	<span class="cff-text" data-color="<?php echo $cff_posttext_link_color ?>"><?php 
			if ( $cff_title_link && !empty($post_text) ): 
				$text_link = ($cff_post_type == 'link' || $cff_post_type == 'video') ? "https://www.facebook.com/" . $page_id . "/posts/" . $PostID[1] : $link;			
		?>
			<a class="cff-post-text-link" <?php echo $cff_title_styles ?> href="<?php echo esc_url($text_link) ?>" <?php echo $target.$cff_nofollow ?>>
				<?php 
					endif; 
					$post_text = preg_replace("/\r\n|\r|\n/",$cff_linebreak_el, $post_text);
					$post_text = apply_filters( 'cff_post_text', $post_text );
					if ($cff_title_link):
						$result = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $post_text);
						echo CFF_Utils::cff_wrap_span( $result ) . ' ';
					else:
						echo CFF_Autolink::cff_autolink( $post_text ) . ' ';
					endif;
			if ( $cff_title_link && !empty($post_text) ): 
				?>
			</a>
		<?php endif; ?>
	</span>
	<span class="cff-expand">... <a href="#" <?php echo $cff_posttext_link_color_html; ?>><span class="cff-more"><?php echo stripslashes(__( $cff_see_more_text, 'custom-facebook-feed' ) )  ?></span><span class="cff-less"><?php echo stripslashes(__( $cff_see_less_text, 'custom-facebook-feed' ) ); ?></span></a></span>
</<?php echo $cff_title_format; ?>>