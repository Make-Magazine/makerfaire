<?php
/**
 * Custom Feeds for Instagram Header Boxed Template
 * Adds account information and an avatar to the top of the feed
 *
 * @version 5.12 Custom Feeds for Instagram Pro by Smash Balloon
 *
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$username                = SB_Instagram_Parse_Pro::get_username( $header_data );
$avatar                  = SB_Instagram_Parse_Pro::get_avatar( $header_data, $settings );
$instagram_cdn_avatar    = SB_Instagram_Parse_Pro::get_avatar( $header_data, $settings, true );
$name                    = SB_Instagram_Parse_Pro::get_name( $header_data );
$header_text_color_style = SB_Instagram_Display_Elements_Pro::get_header_text_color_styles( $settings ); // style="color: #517fa4;"

// Pro Elements
$type_class        = SB_Instagram_Display_Elements_Pro::get_feed_type_class( $settings );
$size_class        = SB_Instagram_Display_Elements_Pro::get_header_size_class( $settings );
$stories_delay     = SB_Instagram_Display_Elements_Pro::get_stories_delay( $settings );
$story_data        = SB_Instagram_Parse_Pro::get_story_data( $header_data );
$should_show_story = ! empty( $story_data ) ? SB_Instagram_Display_Elements_Pro::should_show_element( 'headerstory', $settings ) : false;
$story_data_att    = $should_show_story ? ' data-story-wait="'. (int)$stories_delay . '" data-story-data="' . esc_attr( sbi_json_encode( $story_data ) ) . '" data-story-avatar="'.$avatar.'"' : '';
$post_count        = SB_Instagram_Parse_Pro::get_post_count( $header_data );
$follower_count    = SB_Instagram_Parse_Pro::get_follower_count( $header_data );
$bio               = SB_Instagram_Parse_Pro::get_bio( $header_data, $settings );

// Pro Styles
$should_show_bio = $bio !== '' ? SB_Instagram_Display_Elements_Pro::should_show_element( 'headerbio', $settings ) : false;
$bio_class       = ! $should_show_bio ? ' sbi_no_bio' : '';
$has_info = $should_show_bio || SB_Instagram_Display_Elements_Pro::should_show_element( 'headerfollowers', $settings );
$info_class       = ! $has_info ? ' sbi_no_info' : '';
$avatar_class = $avatar !== '' ? '' : ' sbi_no_avatar';

// Boxed Header Specific
$follow_button_text = __( $settings['followtext'], 'instagram-feed' );
$header_style       = SB_Instagram_Display_Elements_Pro::get_boxed_header_styles( $settings ); // style="background: #517fa4;" already escaped
$header_bar_style   = SB_Instagram_Display_Elements_Pro::get_header_bar_styles( $settings ); // style="background: #eeeeee;" already escaped
$header_info_style  = SB_Instagram_Display_Elements_Pro::get_header_info_styles( $settings ); // style="color: #517fa4;" already escaped
$follow_btn_style   = SB_Instagram_Display_Elements_Pro::get_follow_styles( $settings ); // style="background: rgb();color: rgb();" already escaped
$follow_btn_classes = strpos( $follow_btn_style, 'background' ) !== false ? ' sbi_custom' : '';
?>
<div class="sb_instagram_header sbi_header_style_boxed <?php echo esc_attr( $type_class ); ?><?php echo esc_attr( $size_class ) . esc_attr( $avatar_class ); ?>" <?php echo $header_style; ?>
    <?php echo $story_data_att; ?>>
    <a href="<?php echo esc_url( 'https://www.instagram.com/' . $username . '/' ); ?>" target="_blank" rel="nofollow noopener"
       title="@<?php echo esc_attr( $username ); ?>"
       class="sbi_header_link">
        <div class="sbi_header_text<?php echo esc_attr( $bio_class ) . esc_attr( $info_class ); ?>">
            <h3 <?php echo $header_text_color_style; ?>><?php echo esc_html( $username ); ?></h3>
            <?php if ( $should_show_bio ) : ?>
                <p class="sbi_bio" <?php echo $header_text_color_style; ?>><?php echo str_replace( '&lt;br /&gt;', '<br>', esc_html( nl2br( $bio ) ) ); ?></p>
            <?php endif; ?>
        </div>
        <?php if ( $avatar === '' ) : ?>
            <div class="sbi_header_img">
                <div class="sbi_header_hashtag_icon"><?php echo SB_Instagram_Display_Elements_Pro::get_icon( 'newlogo', $icon_type ); ?></div>
            </div>
        <?php else: ?>
            <div class="sbi_header_img" data-avatar-url="<?php echo esc_attr( $instagram_cdn_avatar ); ?>">
                <div class="sbi_header_img_hover"><?php echo SB_Instagram_Display_Elements_Pro::get_icon( 'newlogo', $icon_type ); ?></div>
                <img src="<?php echo esc_url( $avatar ); ?>"
                     alt="<?php echo esc_attr( $name ); ?>" width="50" height="50">
            </div>
        <?php endif; ?>
        <div class="sbi_header_bar" <?php echo $header_bar_style; ?>>
            <p class="sbi_bio_info" <?php echo $header_info_style; ?>>
                <span class="sbi_posts_count"><?php echo SB_Instagram_Display_Elements_Pro::get_icon( 'photo', $icon_type ) . number_format_i18n( (int)$post_count, 0 ); ?></span>
                <?php if ( $follower_count !== '' ) : // basic display API does not include follower counts as of January 2020 ?>
                <span class="sbi_followers"><?php echo SB_Instagram_Display_Elements_Pro::get_icon( 'user', $icon_type ) . number_format_i18n( (int)$follower_count, 0 ); ?></span>
                <?php endif; ?>
            </p>
            <a class="sbi_header_follow_btn<?php echo esc_attr( $follow_btn_classes ); ?>" href="<?php echo esc_url( 'https://www.instagram.com/' . $username . '/' ); ?>" target="_blank" rel="nofollow noopener" <?php echo $follow_btn_style; ?>>
                <?php echo SB_Instagram_Display_Elements_Pro::get_icon( 'instagram', $icon_type ); ?>
                <span><?php echo esc_html( $follow_button_text ); ?></span>
            </a>
        </div>
    </a>
</div>