<?php
/**
 * Social Share Optimization Tags Setup Interface
 *
 * @version 4.0
 * @since 5.9
 * @package EasySocialShareButtons
 * @author appscreo
 */

function essb_sso_metabox_interface_facebook($post_id) {
	$custom = get_post_custom ( $post_id );
	
	// post share optimizations
	$essb_post_og_desc = isset ( $custom ["essb_post_og_desc"] ) ? $custom ["essb_post_og_desc"] [0] : "";
	$essb_post_og_title = isset ( $custom ["essb_post_og_title"] ) ? $custom ["essb_post_og_title"] [0] : "";
	$essb_post_og_image = isset ( $custom ["essb_post_og_image"] ) ? $custom ["essb_post_og_image"] [0] : "";
	$essb_post_og_image1 = isset ( $custom ["essb_post_og_image1"] ) ? $custom ["essb_post_og_image1"] [0] : "";
	$essb_post_og_image2 = isset ( $custom ["essb_post_og_image2"] ) ? $custom ["essb_post_og_image2"] [0] : "";
	$essb_post_og_image3 = isset ( $custom ["essb_post_og_image3"] ) ? $custom ["essb_post_og_image3"] [0] : "";
	$essb_post_og_image4 = isset ( $custom ["essb_post_og_image4"] ) ? $custom ["essb_post_og_image4"] [0] : "";
	$essb_post_og_url = isset ( $custom ["essb_post_og_url"] ) ? $custom ["essb_post_og_url"] [0] : "";
	
	
	$essb_post_og_desc = stripslashes ( $essb_post_og_desc );
	$essb_post_og_title = stripslashes ( $essb_post_og_title );
	$essb_post_og_video = isset ( $custom ["essb_post_og_video"] ) ? $custom ["essb_post_og_video"] [0] : "";
	$essb_post_og_video_w = isset ( $custom ["essb_post_og_video_w"] ) ? $custom ["essb_post_og_video_w"] [0] : "";
	$essb_post_og_video_h = isset ( $custom ["essb_post_og_video_h"] ) ? $custom ["essb_post_og_video_h"] [0] : "";
	$essb_post_og_author = isset($custom['essb_post_og_author']) ? $custom['essb_post_og_author'][0] : '';
	$essb_post_og_author = stripslashes($essb_post_og_author);
	
	essb_depend_load_class('ESSB_FrontMetaDetails', 'lib/modules/social-share-optimization/class-metadetails.php');
	$sso_data = ESSB_FrontMetaDetails::get_instance();
	
	?>
	<div class="essb-flex-grid-r">
		<div class="essb-heading sub5"><span><i class="fa fa-share-alt"></i> Social Media Message</span></div>
	</div>
	<?php 
	ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
	ESSBOptionsFramework::draw_help(__('Optimize your social share message on all social networks', 'essb'), __('Social Sharing Optimization is important for each site. Without using it you have no control over shared information on social networks. We highly recommend to activate it (Facebook sharing tags are used on almost all social networks so they are the minimal required).', 'essb'), '', array('buttons' => array('How to customize shared information' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-customize-personalize-shared-information-on-social-networks/', 'I see wrong share information' => 'https://docs.socialsharingplugin.com/knowledgebase/facebook-is-showing-the-wrong-image-title-or-description/', 'Test & Fix Facebook Showing Wrong Information' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-test-and-fix-facebook-sharing-wrong-information-using-facebook-open-graph-debugger/')));
	ESSBOptionsFramework::draw_options_row_end();	
	?>

	<div class="essb-flex-grid-r">
		<div class="essb-flex-grid-c c12">
			<strong class="essb-title">Facebook Preview</strong>
			<br/>
			<span class="label">Recommended image size used for sharing is 1,200 x 628 pixels or image with an aspect ratio of 1.91:1</span>
		</div>
	</div>
	<div class="essb-flex-grid-r">
		<div class="essb-flex-grid-c c12">
			
			<div class="sso-preview">
				<?php 				
				ESSBOptionsFramework::draw_fileselect_image_field('essb_post_og_image', 'essb_metabox', $essb_post_og_image, '', '', $sso_data->single_image($post_id));
				?>
			
				<div class="sso-title carret-mark "><?php echo esc_html($sso_data->single_title($post_id)); ?></div>
				<div class="sso-description carret-mark "><?php echo esc_html($sso_data->single_description($post_id)); ?></div>
				<div style="display: none;">
					<div class="sso-title-original carret-mark "><?php echo esc_html($sso_data->single_title($post_id)); ?></div>
					<div class="sso-description-original carret-mark "><?php echo esc_html($sso_data->single_description($post_id)); ?></div>
				</div>
			</div>

		</div>
	</div>
	<?php if (!essb_option_bool_value('sso_deactivate_analyzer')) { ?>
	<div class="essb-flex-grid-r" style="margin-left: -240px; position: absolute; width: 220px;">
		<div class="essb-flex-grid-c c12">
			<div id="sso-calculated-score"></div>
		</div>
	</div>
	<?php } ?>
	
	<?php 
	ESSBOptionsFramework::draw_title(__('Social Media Title', 'essb'), __('Add a title that will populate the open graph meta tag which will be used when users share your content onto most social networks. If nothing is provided here, we will use the post title as a backup. We recommend usage of titles that does not exceed 60 characters', 'essb'), 'inner-row');
	ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
	ESSBOptionsFramework::draw_input_field('essb_post_og_title', true, 'essb_metabox', $essb_post_og_title);
	ESSBOptionsFramework::draw_options_row_end();
	
	
	ESSBOptionsFramework::draw_title(__('Social Media Description', 'essb'), __('Add a description that will populate the open graph meta tag which will be used when users share your content onto most social networks.<span class="essb-inner-recommend">We recommend usage of description that does not exceed 160 characters</span>', 'essb'), 'inner-row');
	ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
	ESSBOptionsFramework::draw_textarea_field('essb_post_og_desc', 'essb_metabox', $essb_post_og_desc);
	ESSBOptionsFramework::draw_options_row_end();

	ESSBOptionsFramework::draw_title(__('Article Author Profile', 'essb'), __('Add link to Facebook profile page of article author if you wish it to appear in shared information. Example: https://facebook.com/author', 'essb'), 'inner-row');
	ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
	ESSBOptionsFramework::draw_input_field('essb_post_og_author', true, 'essb_metabox', $essb_post_og_author, '', '', '', essb_option_value('opengraph_tags_fbauthor'));
	ESSBOptionsFramework::draw_options_row_end();
	
	ESSBOptionsFramework::draw_title(__('Customize Open Graph URL', 'essb'), __('Important! This field is needed only if you made a change in your URL structure and you need to customize og:url tag to preserve shares you have. Do not fill here anything unless you are completely sure you need it - not proper usage will lead to loose of your current social shares and comments.', 'essb'), 'inner-row');
	ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
	ESSBOptionsFramework::draw_input_field('essb_post_og_url', true, 'essb_metabox', $essb_post_og_url);
	ESSBOptionsFramework::draw_options_row_end();
	
	if (essb_option_bool_value('sso_multipleimages')) {
		ESSBOptionsFramework::draw_heading(__('Additional Facebook Images', 'essb'), '5');
			
		ESSBOptionsFramework::draw_title(__('Additional Social Media Image #1', 'essb'), __('Add an image that is optimized for maximum exposure on most social networks.<span class="essb-inner-recommend">We recommend 1200px by 628px</span>', 'essb'), 'inner-row');
		ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
		ESSBOptionsFramework::draw_fileselect_field('essb_post_og_image1', 'essb_metabox', $essb_post_og_image1);
		ESSBOptionsFramework::draw_options_row_end();
	
		ESSBOptionsFramework::draw_title(__('Additional Social Media Image #2', 'essb'), __('Add an image that is optimized for maximum exposure on most social networks.<span class="essb-inner-recommend">We recommend 1200px by 628px</span>', 'essb'), 'inner-row');
		ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
		ESSBOptionsFramework::draw_fileselect_field('essb_post_og_image2', 'essb_metabox', $essb_post_og_image2);
		ESSBOptionsFramework::draw_options_row_end();
			
		ESSBOptionsFramework::draw_title(__('Additional Social Media Image #3', 'essb'), __('Add an image that is optimized for maximum exposure on most social networks.<span class="essb-inner-recommend">We recommend 1200px by 628px</span>', 'essb'), 'inner-row');
		ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
		ESSBOptionsFramework::draw_fileselect_field('essb_post_og_image3', 'essb_metabox', $essb_post_og_image3);
		ESSBOptionsFramework::draw_options_row_end();
			
		ESSBOptionsFramework::draw_title(__('Additional Social Media Image #4', 'essb'), __('Add an image that is optimized for maximum exposure on most social networks.<span class="essb-inner-recommend">We recommend 1200px by 628px</span>', 'essb'), 'inner-row');
		ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
		ESSBOptionsFramework::draw_fileselect_field('essb_post_og_image4', 'essb_metabox', $essb_post_og_image4);
		ESSBOptionsFramework::draw_options_row_end();
	}
	
	
	?>
	<?php 
}

function essb_sso_metabox_interface_twitter($post_id) {
	$custom = get_post_custom ( $post_id );
	
	$essb_post_twitter_desc = isset ( $custom ["essb_post_twitter_desc"] ) ? $custom ["essb_post_twitter_desc"] [0] : "";
	$essb_post_twitter_title = isset ( $custom ["essb_post_twitter_title"] ) ? $custom ["essb_post_twitter_title"] [0] : "";
	$essb_post_twitter_image = isset ( $custom ["essb_post_twitter_image"] ) ? $custom ["essb_post_twitter_image"] [0] : "";
	$essb_post_twitter_desc = stripslashes ( $essb_post_twitter_desc );
	$essb_post_twitter_title = stripslashes ( $essb_post_twitter_title );
	
	essb_depend_load_class('ESSB_FrontMetaDetails', 'lib/modules/social-share-optimization/class-metadetails.php');
	$sso_data = ESSB_FrontMetaDetails::get_instance();
	

	
	$preview_title = $essb_post_twitter_title != '' ? $essb_post_twitter_title : $sso_data->single_title($post_id);
	$preview_desc = $essb_post_twitter_desc != '' ? $essb_post_twitter_desc : $sso_data->single_description($post_id);;
	?>
	<div class="essb-flex-grid-r">
		<div class="essb-flex-grid-c c12">
			<strong class="essb-title">Twitter Card Preview</strong>
			<br/>
			<span class="label">Recommended image size used for sharing is 800 x 418 pixels (or wider image used for Facebook 1,200 x 628 pixels) or image with an aspect ratio of 1.91:1</span>
		</div>
	</div>
	<div class="essb-flex-grid-r">
		<div class="essb-flex-grid-c c12">
		
			<div class="sso-twitter-preview">
			<?php
				ESSBOptionsFramework::draw_fileselect_image_field('essb_post_twitter_image', 'essb_metabox', $essb_post_twitter_image, '', '', $sso_data->single_image($post_id));
			?>
				
				<div class="sso-twitter-title carret-mark "><?php echo esc_html($preview_title); ?></div>
					<div class="sso-twitter-description carret-mark "><?php echo esc_html($preview_desc); ?></div>
					<div style="display: none;">
						<div class="sso-twitter-title-original carret-mark "><?php echo esc_html($preview_title); ?></div>
						<div class="sso-twitter-description-original carret-mark "><?php echo esc_html($preview_desc); ?></div>
					</div>
				</div>
	
			</div>
		</div>
		
	<?php 
	
	ESSBOptionsFramework::draw_title(__('Twitter Card Title', 'essb'), __('Add a title that will populate the Twitter card meta tag which will be used when users share your content on Twitter. If nothing is provided here, we will use the post title as a backup. We recommend usage of titles that does not exceed 60 characters', 'essb'), 'inner-row');
	ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
	ESSBOptionsFramework::draw_input_field('essb_post_twitter_title', true, 'essb_metabox', $essb_post_twitter_title);
	ESSBOptionsFramework::draw_options_row_end();
	
	
	ESSBOptionsFramework::draw_title(__('Twitter Card Description', 'essb'), __('Add a description that will populate the Twitter card tag which will be used when users share your content on Twitter.<span class="essb-inner-recommend">We recommend usage of description that does not exceed 160 characters</span>', 'essb'), 'inner-row');
	ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
	ESSBOptionsFramework::draw_textarea_field('essb_post_twitter_desc', 'essb_metabox', $essb_post_twitter_desc);
	ESSBOptionsFramework::draw_options_row_end();
	
}
