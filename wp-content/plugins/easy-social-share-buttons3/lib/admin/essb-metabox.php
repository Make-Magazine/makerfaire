<?php

function essb_register_settings_metabox_optimize() {
	global $post;
	
	$essb_post_share_message = "";
	$essb_post_share_url = "";
	$essb_post_share_image = "";
	$essb_post_share_text = "";
	$essb_post_fb_url = "";
	$essb_post_plusone_url = "";
	
	$essb_post_og_desc = "";
	$essb_post_og_title = "";
	$essb_post_og_image = "";
	$essb_post_og_video = "";
	$essb_post_og_video_w = "";
	$essb_post_og_video_h = "";
	
	$essb_post_og_image1 = "";
	$essb_post_og_image2 = "";
	$essb_post_og_image3 = "";
	$essb_post_og_image4 = "";
	
	$essb_post_twitter_desc = "";
	$essb_post_twitter_title = "";
	$essb_post_twitter_image = "";
		
	$essb_post_twitter_hashtags = "";
	$essb_post_twitter_username = "";
	$essb_post_twitter_tweet = "";
	$essb_activate_sharerecovery = "";
	
	$essb_post_pin_image = "";
	$essb_post_pin_desc = '';
	
	$post_address = "";
	
	// the post is open into editor
	if (isset($_GET['action'])) {
		
		$custom = get_post_custom ( $post->ID );
		
		// custom post data sharing details
		$post_address = get_permalink ( $post->ID );
		
		$essb_post_share_message = isset ( $custom ["essb_post_share_message"] ) ? $custom ["essb_post_share_message"] [0] : "";
		$essb_post_share_url = isset ( $custom ["essb_post_share_url"] ) ? $custom ["essb_post_share_url"] [0] : "";
		$essb_post_share_image = isset ( $custom ["essb_post_share_image"] ) ? $custom ["essb_post_share_image"] [0] : "";
		$essb_post_share_text = isset ( $custom ["essb_post_share_text"] ) ? $custom ["essb_post_share_text"] [0] : "";
		$essb_post_fb_url = isset ( $custom ["essb_post_fb_url"] ) ? $custom ["essb_post_fb_url"] [0] : "";
		$essb_post_plusone_url = isset ( $custom ["essb_post_plusone_url"] ) ? $custom ["essb_post_plusone_url"] [0] : "";
		
		$essb_post_share_message = stripslashes ( $essb_post_share_message );
		$essb_post_share_text = stripslashes ( $essb_post_share_text );
		
		
		$essb_post_twitter_hashtags = isset ( $custom ['essb_post_twitter_hashtags'] ) ? $custom ['essb_post_twitter_hashtags'] [0] : "";
		$essb_post_twitter_username = isset ( $custom ['essb_post_twitter_username'] ) ? $custom ['essb_post_twitter_username'] [0] : "";
		$essb_post_twitter_tweet = isset ( $custom ['essb_post_twitter_tweet'] ) ? $custom ['essb_post_twitter_tweet'] [0] : "";
		$essb_activate_ga_campaign_tracking = isset($custom['essb_activate_ga_campaign_tracking']) ? $custom['essb_activate_ga_campaign_tracking'][0] : "";
		
		$essb_post_pin_image = isset ( $custom ["essb_post_pin_image"] ) ? $custom ["essb_post_pin_image"] [0] : "";
		$essb_post_pin_desc = isset ( $custom ["essb_post_pin_desc"] ) ? $custom ["essb_post_pin_desc"] [0] : "";
		$essb_post_pin_id = isset($custom['essb_post_pin_id']) ? $custom['essb_post_pin_id'][0] : '';
		
		$essb_activate_sharerecovery = isset($custom['essb_activate_sharerecovery']) ? $custom['essb_activate_sharerecovery'][0] : '';
			
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
		
		$essb_post_twitter_desc = isset ( $custom ["essb_post_twitter_desc"] ) ? $custom ["essb_post_twitter_desc"] [0] : "";
		$essb_post_twitter_title = isset ( $custom ["essb_post_twitter_title"] ) ? $custom ["essb_post_twitter_title"] [0] : "";
		$essb_post_twitter_image = isset ( $custom ["essb_post_twitter_image"] ) ? $custom ["essb_post_twitter_image"] [0] : "";
		$essb_post_twitter_desc = stripslashes ( $essb_post_twitter_desc );
		$essb_post_twitter_title = stripslashes ( $essb_post_twitter_title );
				
		$essb_post_og_author = isset($custom['essb_post_og_author']) ? $custom['essb_post_og_author'][0] : '';
		$essb_post_og_author = stripslashes($essb_post_og_author);
		
		// Metabox draw start
		ESSBMetaboxInterface::draw_form_start ( 'essb_social_share_optimize' );
		
		$sidebar_options = array();
		
		if (essb_options_bool_value('opengraph_tags') || essb_options_bool_value('twitter_card')) {
			$sidebar_options[] = array(
					'field_id' => 'opengraph',
					'title' => esc_html__('Social Media Message', 'essb'),
					'icon' => 'share-alt',
					'type' => 'menu_item',
					'action' => 'default',
					'default_child' => ''
			);
		}
		
		$sidebar_options[] = array(
				'field_id' => 'twittertag',
				'title' => esc_html__('Custom Tweet', 'essb').(essb_options_bool_value('twitter_card') ? ' & Card Data' : ''),
				'icon' => 'twitter',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
		
		if (essb_option_bool_value('pinterest_sniff_disable') || 
				essb_option_bool_value('pinterest_alwayscustom') || 
				essb_option_bool_value('pinterest_images')) {
			$sidebar_options[] = array(
					'field_id' => 'pinterest',
					'title' => esc_html__('Pinterest Image', 'essb'),
					'icon' => 'pinterest-p',
					'type' => 'menu_item',
					'action' => 'default',
					'default_child' => ''
			);
		}
		
		$sidebar_options[] = array(
				'field_id' => 'share',
				'title' => esc_html__('Share Parameters', 'essb'),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);

		if (essb_option_bool_value('activate_ga_tracking')) {
			$sidebar_options[] = array(
					'field_id' => 'ga',
					'title' => esc_html__('GA Campaign Tracking Options', 'essb'),
					'icon' => 'pie-chart',
					'type' => 'menu_item',
					'action' => 'default',
					'default_child' => ''
			);
		}
		
		if (defined('ESSB3_SHARED_COUNTER_RECOVERY')) {
			$sidebar_options[] = array(
					'field_id' => 'sharerecover',
					'title' => esc_html__('Share Recovery', 'essb'),
					'icon' => 'refresh',
					'type' => 'menu_item',
					'action' => 'default',
					'default_child' => ''
			);
		}
		
		if (defined('ESSB3_CACHED_COUNTERS')) {
			$sidebar_options[] = array(
					'field_id' => 'sharecounter',
					'title' => esc_html__('Social Shares', 'essb'),
					'icon' => 'sort-numeric-asc',
					'type' => 'menu_item',
					'action' => 'default',
					'default_child' => ''
			);
		}
		
		if (essb_option_bool_value('activate_fake')) {
			$sidebar_options[] = array(
					'field_id' => 'fakecounter',
					'title' => esc_html__('Fake/Dummy Share Counter', 'essb'),
					'icon' => 'magic',
					'type' => 'menu_item',
					'action' => 'default',
					'default_child' => ''
			);
		}
		
		if (has_filter('essb_customize_metabox_extra_sections')) {
		    $sidebar_options = apply_filters('essb_customize_metabox_extra_sections', $sidebar_options);
		}
		
		ESSBMetaboxInterface::draw_first_menu_activate('sso');
		
		ESSBMetaboxInterface::draw_sidebar($sidebar_options, 'sso');
		ESSBMetaboxInterface::draw_content_start('300', 'sso');
		
		if (essb_options_bool_value('opengraph_tags') || essb_options_bool_value('twitter_card')) {
			ESSBMetaboxInterface::draw_content_section_start('opengraph');
	
			if (essb_options_bool_value('opengraph_tags')) {
				
				essb_depend_load_function('essb_sso_metabox_interface_facebook', 'lib/admin/metabox/sso-tags.php');
				essb_sso_metabox_interface_facebook($post->ID);
				
			}
			
			ESSBMetaboxInterface::draw_content_section_end();
		}
		
		ESSBMetaboxInterface::draw_content_section_start('twittertag');
		ESSBOptionsFramework::reset_row_status();
		
		ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
		ESSBOptionsFramework::draw_help('', '', '', array('buttons' => array(
		    'How to Customize The Shared Information On Twitter (Custom Tweet)' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-customize-the-shared-information-on-twitter-custom-tweet/',
		    'Twitter Sharing Wrong URL' => 'https://docs.socialsharingplugin.com/knowledgebase/twitter-share-wrong-url-how-to-fix-it/'
		)));
		ESSBOptionsFramework::draw_options_row_end();
		
		
		ESSBOptionsFramework::draw_holder_start(array('class'=> 'essb-tweet-preview', 'user_id' => 'essb-tweet-preview'));
		ESSBOptionsFramework::draw_holder_end();
		
		ESSBOptionsFramework::draw_options_row_start(esc_html__('Tweet', 'essb'), esc_html__('Default Tweet is generated from post title. In this field you can easy define own personalized Tweet for better social network reach.', 'essb'));
		ESSBOptionsFramework::draw_textarea_field('essb_post_twitter_tweet', 'essb_metabox', $essb_post_twitter_tweet, $post->post_title);
		ESSBOptionsFramework::draw_options_row_end();
		
		ESSBOptionsFramework::draw_options_row_start(esc_html__('Hashtags', 'essb'), esc_html__('Set custom own tags for post or leave blank to use site defined', 'essb'));
		ESSBOptionsFramework::draw_input_field('essb_post_twitter_hashtags', true, 'essb_metabox', $essb_post_twitter_hashtags, '', '', '', essb_option_value('twitterhashtags'));
		ESSBOptionsFramework::draw_options_row_end();
		
		ESSBOptionsFramework::draw_options_row_start(esc_html__('Username', 'essb'), esc_html__('Change default user that will be mentioned into Tweet.', 'essb'));
		ESSBOptionsFramework::draw_input_field('essb_post_twitter_username', true, 'essb_metabox', $essb_post_twitter_username, '', '', '', essb_option_value('twitteruser'));
		ESSBOptionsFramework::draw_options_row_end();
		
		$short_url = wp_get_shortlink($post->ID);
		echo '<input type="hidden" id="essb_twitter_shorturl" value="'.esc_url($short_url).'"/>';
		
		if (essb_options_bool_value('twitter_card')) {
			essb_depend_load_function('essb_sso_metabox_interface_facebook', 'lib/admin/metabox/sso-tags.php');
			essb_sso_metabox_interface_twitter($post->ID);
		}
		
		ESSBMetaboxInterface::draw_content_section_end();
		
		$pin_mode_title = '';
		$pin_mode_desc = '';
		ESSBMetaboxInterface::draw_content_section_start('pinterest');
		if (essb_option_bool_value('pinterest_sniff_disable')) {
			$pin_mode_title = esc_html__('Your Pinterest Mode: Post Custom/Featured Image', 'essb');
			$pin_mode_desc = esc_html__('In this mode the Pin button will generate a custom share message with the post featured or custom image and description from the post title or custom you fill. Using this mode you have a deeper control over the work of button. You can combine it with the on media sharing or the additional Pin any image function if you need to Pin any existing image inside content.', 'essb');
		}
		else {
			$pin_mode_title = esc_html__('Your Pinterest Mode: Pin Any Image from Post/Page', 'essb');
			$pin_mode_desc = esc_html__('In this mode the button will show a dialog to choose any of content images for Pin. Using the Pinterest any image mode you cannot control the shared information - it is comming from the image optimizations you have inside WordPress. If you need to have a deeper image control you can change the mode of Pinterest button from Social Sharing -> Networks -> Additional Network Options ', 'essb');
		}

		ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
		ESSBOptionsFramework::draw_help($pin_mode_title, $pin_mode_desc);
		ESSBOptionsFramework::draw_options_row_end();
		
		if (essb_option_bool_value('pinterest_sniff_disable')) {			
			ESSBOptionsFramework::reset_row_status();

			if (essb_option_bool_value('sso_external_images')) {
				echo '<div class="media-visible">';
			}
			ESSBOptionsFramework::draw_options_row_start(esc_html__('Change default Pinterest image', 'essb'), esc_html__('Choose personalized image that will be used to when you press Pinterest share button. We recommend using an image that is formatted in a 2:3 aspect ratio like 735 x 1102.', 'essb'));
			ESSBOptionsFramework::draw_fileselect_image_field('essb_post_pin_image', 'essb_metabox', $essb_post_pin_image);
			ESSBOptionsFramework::draw_options_row_end();
			
			if (essb_option_bool_value('sso_external_images')) {				
				echo '</div>';
			}
			
			ESSBOptionsFramework::draw_options_row_start(esc_html__('Pinterest Description', 'essb'), esc_html__('Add a custom description to your Pin.', 'essb'));
			ESSBOptionsFramework::draw_textarea_field('essb_post_pin_desc', 'essb_metabox', $essb_post_pin_desc);
			ESSBOptionsFramework::draw_options_row_end();
			
			ESSBOptionsFramework::draw_options_row_start(esc_html__('Pin ID', 'essb'), esc_html__('Get more repins on your own Pins. The filled Pin ID will appear automatically on all images as data-pin-id attribute.', 'essb'));
			ESSBOptionsFramework::draw_input_field('essb_post_pin_id', true, 'essb_metabox', $essb_post_pin_id);
			ESSBOptionsFramework::draw_options_row_end();
		}
		else if (essb_option_bool_value('pinterest_alwayscustom') || essb_option_bool_value('pinterest_images') || essb_option_bool_value('pinterest_set_datamedia')) {
			
			if (essb_option_bool_value('pinterest_set_datamedia')) {
				if (essb_option_bool_value('sso_external_images')) {
					echo '<div class="media-visible">';
				}
				ESSBOptionsFramework::draw_options_row_start(esc_html__('Change default Pinterest image', 'essb'), esc_html__('Choose personalized image that will be used to when you press Pinterest share button. We recommend using an image that is formatted in a 2:3 aspect ratio like 735 x 1102.', 'essb'));
				ESSBOptionsFramework::draw_fileselect_image_field('essb_post_pin_image', 'essb_metabox', $essb_post_pin_image);
				ESSBOptionsFramework::draw_options_row_end();
					
				if (essb_option_bool_value('sso_external_images')) {
					echo '</div>';
				}
			}
			
			if (essb_option_bool_value('pinterest_alwayscustom') || essb_option_bool_value('pinterest_images')) {
				ESSBOptionsFramework::draw_options_row_start(esc_html__('Custom Pinterest Message', 'essb'), esc_html__('Add a custom Pinterest message that will automatically appear over your images added via the plugin shortcode or those via the Pin Over Images function', 'essb'));
				ESSBOptionsFramework::draw_textarea_field('essb_post_pin_desc', 'essb_metabox', $essb_post_pin_desc);
				ESSBOptionsFramework::draw_options_row_end();
	
				ESSBOptionsFramework::draw_options_row_start(esc_html__('Pin ID', 'essb'), esc_html__('Get more repins on your own Pins. The filled Pin ID will appear automatically on all images as data-pin-id attribute.', 'essb'));
				ESSBOptionsFramework::draw_input_field('essb_post_pin_id', true, 'essb_metabox', $essb_post_pin_id);
				ESSBOptionsFramework::draw_options_row_end();
			}
		}
		
		ESSBMetaboxInterface::draw_content_section_end();
		
		ESSBMetaboxInterface::draw_content_section_start('share');
		
		ESSBOptionsFramework::reset_row_status();
		
		ESSBOptionsFramework::draw_hint('', esc_html__('Sharing over social networks use a specific set of parameters. Each network may use one or all of them. Those parameters plugin always fills on autopilot with the default details from the post. Using the provided fields below you can change the values send via the share command. The import you need to know is that most of the social networks use the URL as the only parameter. All share details appear from the connection to this URL social share optimization tags.', 'essb'), '', 'glowhelp');
		
		ESSBOptionsFramework::draw_options_row_start(esc_html__('URL', 'essb'), esc_html__('Provide custom URL to be shared.', 'essb'));
		ESSBOptionsFramework::draw_input_field('essb_post_share_url', true, 'essb_metabox', $essb_post_share_url);
		ESSBOptionsFramework::draw_options_row_end();
		
		ESSBOptionsFramework::draw_options_row_start(esc_html__('Message', 'essb'), esc_html__('Provide custom message to be shared (not all social networks support that option)', 'essb'));
		ESSBOptionsFramework::draw_input_field('essb_post_share_message', true, 'essb_metabox', $essb_post_share_message);
		ESSBOptionsFramework::draw_options_row_end();
		
		ESSBOptionsFramework::draw_options_row_start(esc_html__('Image', 'essb'), esc_html__('Custom image is support by Facebook when advanced sharing is enabled and Pinterest when sniff for images is disabled', 'essb'));
		ESSBOptionsFramework::draw_fileselect_field('essb_post_share_image', 'essb_metabox', $essb_post_share_image);
		ESSBOptionsFramework::draw_options_row_end();
		
		ESSBOptionsFramework::draw_options_row_start(esc_html__('Description', 'essb'), esc_html__('Custom description is support by Facebook when advanced sharing is enabled and Pinterest when sniff for images is disabled', 'essb'));
		ESSBOptionsFramework::draw_textarea_field('essb_post_share_text', 'essb_metabox', $essb_post_share_text);
		ESSBOptionsFramework::draw_options_row_end();
		ESSBMetaboxInterface::draw_content_section_end();
			
		
		if (essb_option_bool_value('activate_ga_tracking')) {
			ESSBMetaboxInterface::draw_content_section_start('ga');
			
			ESSBOptionsFramework::reset_row_status();
			ESSBOptionsFramework::draw_heading(esc_html__('Customize Google Analytics Campaign Tracking Options', 'essb'), '5');
			
			ESSBOptionsFramework::draw_options_row_start(esc_html__('Add Custom Google Analytics Campaign parameters to your URLs', 'essb'), esc_html__('Paste your custom campaign parameters in this field and they will be automatically added to shared addresses on social networks. Please note as social networks count shares via URL as unique key this option is not compatible with active social share counters as it will make the start from zero.', 'essb'));
			ESSBOptionsFramework::draw_input_field('essb_activate_ga_campaign_tracking', true, 'essb_metabox', $essb_activate_ga_campaign_tracking);
			ESSBOptionsFramework::draw_options_row_end();
			
			ESSBOptionsFramework::draw_options_row_start('', '', '', '2', false);
			print "<span style='font-weight: 400;'>You can visit <a href='https://support.google.com/analytics/answer/1033867?hl=en' target='_blank'>this page</a> for more information on how to use and generate these parameters.
			To include the social network into parameters use the following code <b>{network}</b>. When that code is reached it will be replaced with the network name (example: facebook). An example campaign trakcing code include network will look like this utm_source=essb_settings&utm_medium=needhelp&utm_campaign={network} - in this configuration when you press Facebook button {network} will be replaced with facebook, if you press Twitter button it will be replaced with twitter.</span>";
			ESSBOptionsFramework::draw_options_row_end();
			
			ESSBMetaboxInterface::draw_content_section_end();
		}
			
		if (defined('ESSB3_SHARED_COUNTER_RECOVERY')) {
			ESSBMetaboxInterface::draw_content_section_start('sharerecover');
		
			ESSBOptionsFramework::reset_row_status();
			ESSBOptionsFramework::draw_heading(esc_html__('Share Counter Recovery', 'essb'), '5');
		
			ESSBOptionsFramework::draw_options_row_start(esc_html__('Previous post url address', 'essb'), esc_html__('Provide custom previous url address of post if the automatic share counter recovery is not possible to guess the previous post address.', 'essb'));
			ESSBOptionsFramework::draw_input_field('essb_activate_sharerecovery', true, 'essb_metabox', $essb_activate_sharerecovery);
			ESSBOptionsFramework::draw_options_row_end();
		
			ESSBMetaboxInterface::draw_content_section_end();
		}
		
		if (defined('ESSB3_CACHED_COUNTERS')) {
			ESSBMetaboxInterface::draw_content_section_start('sharecounter');
			
			ESSBOptionsFramework::reset_row_status();

			print '<div class="essb-dashboard" style="padding-top: 10px;">';
			$listOfNetworks = essb_available_social_networks();
			foreach ($listOfNetworks as $key => $data) {
				
				if ($key == 'facebook_like') {
					continue;
				}
				
				$value = isset ( $custom ["essb_c_".$key] ) ? $custom ["essb_c_".$key] [0] : "";
				
				if (intval($value) != 0) {
					?>
					
					<div
						class="essb-stats-panel essb-stat-network shadow panel20 widget-color-<?php echo $key; ?>">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">
								<i class="essb_icon_<?php echo $key; ?>"></i> <span
									class="details"><?php echo $data["name"]; ?></span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($value); ?>
						</div>
						</div>
					</div>
					<?php 
				}
			}
			print '</div>';
			
			$essb_cache_expire = isset ( $custom ['essb_cache_expire'] ) ? $custom ['essb_cache_expire'] [0] : "";
			ESSBOptionsFramework::draw_options_row_start_full();
			if ($essb_cache_expire != '') {
				ESSBOptionsFramework::draw_title('Next counter update will be at '.date(DATE_RFC822, $essb_cache_expire), 'If you wish to make a manual counter update you can use the top Easy Social Share Buttons menu where you have link when post is opened');
			}
			else {
				ESSBOptionsFramework::draw_title('Counter update information is not available', 'Time of counter update will appear once first time such is made when you load post. If you wish to make a manual counter update you can use the top Easy Social Share Buttons menu where you have link when post is opened');
			}
			
			echo '<div>';
			echo '<a class="essb-btn" href="'.esc_url(add_query_arg('essb_clear_cached_counters', 'true', $post_address)).'" target="_blank">Update Counters Now</a>&nbsp;';
			echo '<a class="essb-btn essb-btn-red" href="'.esc_url(add_query_arg('essb_clear_counters_history', 'true', $post_address)).'" target="_blank">Delete Stored Counters & Update Now</a>';
			echo '</div>';
			
			ESSBOptionsFramework::draw_options_row_end();
			
			ESSBMetaboxInterface::draw_content_section_end();
		}
		
		if (essb_option_bool_value('activate_fake')) {
			ESSBMetaboxInterface::draw_content_section_start('fakecounter');
		
			ESSBOptionsFramework::reset_row_status();
			ESSBOptionsFramework::draw_heading(esc_html__('Manage Fake/Dummy Share Counters', 'essb'), '5');
			ESSBOptionsFramework::draw_hint('', esc_html__('Using fields below you can manage your internal (fake, dummy) counters - change or set new values. If you choose a list of networks below you will see just them. Otherwise all networks will appear.', 'essb'));
				
			$listOfNetworks = essb_available_social_networks();
			
			$fake_networks = essb_option_value('fake_networks');
			if (!is_array($fake_networks)) {
				$fake_networks = array();
			}
			
			$minimal_fake = get_option('essb-fake');
			if (!is_array($minimal_fake)) {
				$minimal_fake = array();
			}
			
			foreach ($listOfNetworks as $key => $data) {
				
				if (count($fake_networks) > 0 && !in_array($key, $fake_networks)) {
					continue; 
				}
				
				$minimal_fake_shares = isset($minimal_fake['fake_'.$key]) ? $minimal_fake['fake_'.$key] : '0';
				
				$value = isset ( $custom ["essb_pc_".$key] ) ? $custom ["essb_pc_".$key] [0] : "";
				
				$desc = '';
				if (intval($value) < intval($minimal_fake_shares)) {
					$desc = 'The current value is lower than the global minial shares: '.$minimal_fake_shares.'. In this case the minimal value will appear in front till post value become greater';
				}
				
				ESSBOptionsFramework::draw_options_row_start($data["name"], $desc);
				ESSBOptionsFramework::draw_input_field("essb_pc_".$key, true, 'essb_metabox', $value);
				ESSBOptionsFramework::draw_options_row_end();
			}
			
		
			ESSBMetaboxInterface::draw_content_section_end();
		}
		
		do_action('essb_customize_metabox_extra_options');
		
		ESSBMetaboxInterface::draw_content_end();
		ESSBMetaboxInterface::draw_form_end();
	}
}

function essb_register_settings_metabox_visual() {
	global $post;
	
	if (isset ( $_GET ['action'] )) {
	
		$custom = get_post_custom ( $post->ID );		
		$essb_post_button_style = isset ( $custom ["essb_post_button_style"] ) ? $custom ["essb_post_button_style"] [0] : "";
		$essb_post_template = isset ( $custom ["essb_post_template"] ) ? $custom ["essb_post_template"] [0] : "";
		$essb_post_counters = isset ( $custom ["essb_post_counters"] ) ? $custom ["essb_post_counters"] [0] : "";
		$essb_post_counter_pos = isset ( $custom ["essb_post_counter_pos"] ) ? $custom ["essb_post_counter_pos"] [0] : "";
		$essb_post_total_counter_pos = isset ( $custom ["essb_post_total_counter_pos"] ) ? $custom ["essb_post_total_counter_pos"] [0] : "";
		$essb_post_customizer = isset ( $custom ["essb_post_customizer"] ) ? $custom ["essb_post_customizer"] [0] : "";
		$essb_post_animations = isset ( $custom ["essb_post_animations"] ) ? $custom ["essb_post_animations"] [0] : "";
		$essb_post_optionsbp = isset ( $custom ["essb_post_optionsbp"] ) ? $custom ["essb_post_optionsbp"] [0] : "";
		$essb_post_content_position = isset ( $custom ["essb_post_content_position"] ) ? $custom ["essb_post_content_position"] [0] : "";
		
		// update for PHP 7.4
		$essb_post_button_position_ = array();
		
		foreach (essb_available_button_positions() as $position => $name) {
			$essb_post_button_position_[$position] = isset ( $custom ["essb_post_button_position_".$position] ) ? $custom ["essb_post_button_position_".$position] [0] : "";
		}
		
		$essb_post_native = isset ( $custom ["essb_post_native"] ) ? $custom ["essb_post_native"] [0] : "";
		$essb_post_native_skin = isset ( $custom ["essb_post_native_skin"] ) ? $custom ["essb_post_native_skin"] [0] : "";
		
		ESSBMetaboxInterface::draw_form_start ( 'essb_social_share_visual' );
		$sidebar_options = array();
		
		$sidebar_options[] = array(
				'field_id' => 'visual1',
				'title' => esc_html__('Button Style', 'essb'),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
		
		$sidebar_options[] = array(
				'field_id' => 'visual2',
				'title' => esc_html__('Button Display', 'essb'),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
		
		$sidebar_options[] = array(
				'field_id' => 'visual3',
				'title' => esc_html__('Native Buttons', 'essb'),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
		
		$converted_button_styles = essb_avaiable_button_style();
		$converted_button_styles[""] = "Default style from settings";
		
		$converted_counter_pos = essb_avaliable_counter_positions();
		$converted_counter_pos[""] = "Default value from settings";

		$converted_total_counter_pos = essb_avaiable_total_counter_position();
		$converted_total_counter_pos[""] = "Default value from settings";
		
		$converted_content_position = array();
		$converted_content_position[""] = "Default value from settings";
		$converted_content_position["no"] = "No display inside content (deactivate content positions)";
		foreach (essb_avaliable_content_positions() as $position => $data) {
			$converted_content_position[$position] = $data["label"];
		}
		
		$animations_container = array ();
		$animations_container[""] = "Default value from settings";
		foreach (essb_available_animations() as $key => $text) {
			if ($key != '') {
				$animations_container[$key] = $text;
			}
			else {
				$animations_container['no'] = 'No amination';
			}
		}
		
		$yesno_object = array();
		$yesno_object[""] = "Default value from settings";
		$yesno_object["yes"] = "Yes";
		$yesno_object["no"] = "No";
		
		ESSBMetaboxInterface::draw_first_menu_activate('visual');
		ESSBMetaboxInterface::draw_sidebar($sidebar_options, 'visual');
		ESSBMetaboxInterface::draw_content_start('300', 'visual');		
		
		ESSBMetaboxInterface::draw_content_section_start('visual1');
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(esc_html__('Button Style', 'essb'), '3');
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Button style', 'essb'), esc_html__('Change default button style.', 'essb'));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_button_style', $converted_button_styles, false, 'essb_metabox', $essb_post_button_style);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Template', 'essb'), esc_html__('Change default template.', 'essb'));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_template', essb_available_tempaltes4(true), false, 'essb_metabox', $essb_post_template);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Counters', 'essb'), '');
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_counters', $yesno_object, false, 'essb_metabox', $essb_post_counters);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Counter position', 'essb'), '');
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_counter_pos', $converted_counter_pos, false, 'essb_metabox', $essb_post_counter_pos);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Total counter position', 'essb'), '');
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_total_counter_pos', $converted_total_counter_pos, false, 'essb_metabox', $essb_post_total_counter_pos);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Activate style customizer', 'essb'), '');
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_customizer', $yesno_object, false, 'essb_metabox', $essb_post_customizer);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Activate animations', 'essb'), '');
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_animations', $animations_container, false, 'essb_metabox', $essb_post_animations);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Activate options by button position', 'essb'), '');
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_optionsbp', $yesno_object, false, 'essb_metabox', $essb_post_optionsbp);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxInterface::draw_content_section_end();
		
		ESSBMetaboxInterface::draw_content_section_start('visual2');
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(esc_html__('Button Position', 'essb'), '3');
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Content position', 'essb'), esc_html__('Change default content position', 'essb'));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_content_position', $converted_content_position, false, 'essb_metabox', $essb_post_content_position);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		foreach (essb_available_button_positions() as $position => $name) {
			ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Activate '.$name["label"], 'essb'), esc_html__('Activate additional display position', 'essb'));
			ESSBMetaboxOptionsFramework::draw_select_field('essb_post_button_position_'.$position, $yesno_object, false, 'essb_metabox', $essb_post_button_position_[$position]);
			ESSBMetaboxOptionsFramework::draw_options_row_end();			
		}
		
		ESSBMetaboxInterface::draw_content_section_end();

		ESSBMetaboxInterface::draw_content_section_start('visual3');
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(esc_html__('Native Buttons', 'essb'), '3');
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Activate native buttons', 'essb'), '');
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_native', $yesno_object, false, 'essb_metabox', $essb_post_native);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(esc_html__('Activate native buttons skin', 'essb'), '');
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_native_skin', $yesno_object, false, 'essb_metabox', $essb_post_native_skin);
		ESSBMetaboxOptionsFramework::draw_options_row_end();		
		
		ESSBMetaboxInterface::draw_content_section_end();
		
		ESSBMetaboxInterface::draw_content_end();
		ESSBMetaboxInterface::draw_form_end ();
		
	}
}

function essb_register_settings_metabox_onoff() {
	global $post;
	
	if (isset ( $_GET ['action'] )) {

		$custom = get_post_custom ( $post->ID );
		$essb_off = isset ( $custom ["essb_off"] ) ?  $custom ["essb_off"] [0]: "false";
		$essb_pc_twitter = isset ( $custom ["essb_pc_twitter"] ) ?  $custom ["essb_pc_twitter"] [0]: "";
		
		$twitter_counters = essb_option_value('twitter_counters');
		
		ESSBMetaboxInterface::draw_form_start ( 'essb_global_metabox' );
		
		ESSBOptionsFramework::draw_section_start ();
		
		ESSBOptionsFramework::draw_options_row_start_full('inner-row');
		ESSBOptionsFramework::draw_title( esc_html__( 'Turn off Easy Social Share Buttons', 'essb' ), esc_html__ ( 'Turn off automatic button display for that post/page of social share buttons', 'essb' ), 'inner-row' );
		ESSBOptionsFramework::draw_options_row_end ();
		
		ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
		ESSBOptionsFramework::draw_switch_field ( 'essb_off', 'essb_metabox', $essb_off, esc_html__ ( 'Yes', 'essb' ), esc_html__ ( 'No', 'essb' ) );
		ESSBOptionsFramework::draw_options_row_end ();

		if ($twitter_counters == "self") {
			ESSBOptionsFramework::draw_options_row_start_full('inner-row');
			ESSBOptionsFramework::draw_title ( esc_html__ ( 'Twitter Internal Share Counter', 'essb' ), esc_html__ ( 'Customize value of Twitter internal share counter', 'essb' ), 'inner-row' );
			ESSBOptionsFramework::draw_options_row_end ();
			
			ESSBOptionsFramework::draw_options_row_start_full('inner-row-small');
			ESSBOptionsFramework::draw_input_field('essb_pc_twitter', true, 'essb_metabox', $essb_pc_twitter);
			ESSBOptionsFramework::draw_options_row_end ();
		}
		
		
		ESSBOptionsFramework::draw_section_end ();
		
		ESSBMetaboxInterface::draw_form_end ();
	}
}



function essb_register_settings_metabox_stats() {
	global $post, $essb_networks;
	
	if (isset ( $_GET ['action'] )) {
	
		$post_id = $post->ID;
		ESSBSocialShareAnalyticsBackEnd::init_addional_settings();
		
		// overall stats by social network
		$overall_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks ('', $post_id);
		$position_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_position('', $post_id);
		
		$calculated_total = 0;
		$networks_with_data = array ();
		
		if (isset ( $overall_stats )) {
			$cnt = 0;
			foreach ( $essb_networks as $k => $v ) {
		
				$calculated_total += intval ( $overall_stats->{$k} );
				if (intval ( $overall_stats->{$k} ) != 0) {
					$networks_with_data [$k] = $k;
				}
			}
		}
		
		$device_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_device ('', $post_id);
		
		$essb_date_to = "";
		$essb_date_from = "";
		
		if ($essb_date_to == '') {
			$essb_date_to = date ( "Y-m-d" );
		}
		
		if ($essb_date_from == '') {
			$essb_date_from = date ( "Y-m-d", strtotime ( date ( "Y-m-d", strtotime ( date ( "Y-m-d" ) ) ) . "-1 month" ) );
		}
		
		$sqlMonthsData = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks_by_date_for_post($essb_date_from, $essb_date_to, $post_id);
		
		
		include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/dashboard/template-metabox-post.php';
	}
}

?>