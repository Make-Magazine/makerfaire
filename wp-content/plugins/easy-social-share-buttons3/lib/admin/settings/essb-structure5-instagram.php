<?php
ESSBControlCenter::register_sidebar_section_menu('instagram', 'instagram', esc_html__('Setup', 'essb'));
ESSBControlCenter::register_sidebar_section_menu('instagram', 'content', esc_html__('Feed Below Content', 'essb'));
ESSBControlCenter::register_sidebar_section_menu('instagram', 'popup', esc_html__('Pop-up Feed', 'essb'));

ESSBOptionsStructureHelper::help('instagram', 'instagram', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/instagram-feed-basic-setup/', 'Add Feed or Image' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-add-instagram-feed-on-your-website-automatic-or-manual/'));

if (defined('SBIVER')) {
    ESSBOptionsStructureHelper::hint('instagram', 'instagram', '', esc_html__('Smash Balloon Instagram Feed plugin detected. The plugin shortcode for feed generation can be used with the name [essb-instagram-feed] instead of [instagram-feed].', 'essb'), '', 'blue');
}

ESSBOptionsStructureHelper::field_select_panel('instagram', 'instagram', 'instagram_open_as', esc_html__('Open items', 'essb'), '', array('' => esc_html__('Pop-up', 'essb'), 'link' => esc_html__('Direct Link', 'essb')));
ESSBOptionsStructureHelper::field_select_panel('instagram', 'instagram', 'instagram_postinfo_style', esc_html__('Show item information on hover', 'essb'), esc_html__('You can also personalize this from the shortcode or widget settings', 'essb'), array('' => esc_html__('Yes', 'essb'), 'false' => esc_html__('No', 'essb')));
ESSBOptionsStructureHelper::field_switch_panel('instagram', 'instagram', 'instagram_widget', esc_html__('Enable widget', 'essb'), esc_html__('Enable also the widget for Instagram that you can add to any sidebar. This option does not reflect regular shortcode or automated usage.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::field_switch_panel('instagram', 'instagram', 'instagram_styles', esc_html__('Always load styles', 'essb'), esc_html__('Always load Instagram feed styles on site. If not active the styles will load just when feed is added on-page.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::field_textbox_panel('instagram', 'instagram', 'instagram_cache', esc_html__('Default cache expiration time (hours)', 'essb'), esc_html__('Fill 0 if you wish the feed to update without using cache (default is 6 hours)', 'essb') );
ESSBOptionsStructureHelper::field_component('instagram', 'instagram', 'essb5_advanced_instagram_shortcode', 'false');

ESSBOptionsStructureHelper::help('instagram', 'content', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-add-instagram-feed-on-your-website-automatic-or-manual/'));
ESSBOptionsStructureHelper::panel_start('instagram', 'content', esc_html__('Enable automatic feed display below the content of selected post types', 'essb'), '', 'fa21 fa fa-instagram', array("mode" => "switch", 'switch_id' => 'instagramfeed_content'));
ESSBOptionsStructureHelper::field_checkbox_list('instagram', 'content', 'instagramfeed_content_types', esc_html__('Post types', 'essb'), '', essb_get_post_types(), '', array('source' => 'post_types'));
ESSBOptionsStructureHelper::field_textbox('instagram', 'content', 'instagramfeed_content_user', esc_html__('Username', 'essb'), '' );
ESSBOptionsStructureHelper::field_textbox('instagram', 'content', 'instagramfeed_content_images', esc_html__('Number of images', 'essb'), esc_html__('Choose between 1 to 12 images appearing on the Instagram widget below content.', 'essb'));

$columns = array( '1col' => esc_html__('1 Column', 'essb'), 
					'2cols' => esc_html__('2 Columns', 'essb'), 
					'3cols' => esc_html__('3 Columns', 'essb'), 
					'4cols' => esc_html__('4 Columns', 'essb'), 
					'5cols' => esc_html__('5 Columns', 'essb'),
					'row' => esc_html__('Row', 'essb'));

ESSBOptionsStructureHelper::field_select('instagram', 'content', 'instagramfeed_content_columns', esc_html__('Number of columns', 'essb'), '', $columns);

$yesno_options = array('false' => esc_html__('No', 'essb'), 'true' => esc_html__('Yes', 'essb'));
ESSBOptionsStructureHelper::field_select('instagram', 'content', 'instagramfeed_content_profile', esc_html__('Show profile information', 'essb'), '', $yesno_options);
ESSBOptionsStructureHelper::field_select('instagram', 'content', 'instagramfeed_content_followbtn', esc_html__('Show profile follow button', 'essb'), '', $yesno_options);

$profile_size = array(
		'normal' => esc_html__('Normal', 'essb'),
		'small' => esc_html__('Small', 'essb'));

ESSBOptionsStructureHelper::field_select('instagram', 'content', 'instagramfeed_content_profile_size', esc_html__('Profile size', 'essb'), '', $profile_size);


ESSBOptionsStructureHelper::field_select('instagram', 'content', 'instagramfeed_content_masonry', esc_html__('Masonry', 'essb'), '', $yesno_options);

$image_space = array(
						'' => esc_html__('Without space', 'essb'),
						'small' => esc_html__('Small', 'essb'),
						'medium' => esc_html__('Medium', 'essb'),
						'large' => esc_html__('Large', 'essb'),
						'xlarge' => esc_html__('Extra Large', 'essb'),
						'xxlarge' => esc_html__('Extra Extra Large', 'essb'));

ESSBOptionsStructureHelper::field_select('instagram', 'content', 'instagramfeed_content_space', esc_html__('Space between images', 'essb'), '', $image_space);
ESSBOptionsStructureHelper::panel_end('instagram', 'content');

/**
 * Pop-up
 */
ESSBOptionsStructureHelper::help('instagram', 'popup', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-add-instagram-feed-on-your-website-automatic-or-manual/'));
ESSBOptionsStructureHelper::panel_start('instagram', 'popup', esc_html__('Enable automatic feed display as pop-up', 'essb'), '', 'fa21 fa fa-instagram', array("mode" => "switch", 'switch_id' => 'instagramfeed_popup'));
ESSBOptionsStructureHelper::field_checkbox_list('instagram', 'popup', 'instagramfeed_popup_types', esc_html__('Post types', 'essb'), '', essb_get_post_types());
ESSBOptionsStructureHelper::field_textbox('instagram', 'popup', 'instagramfeed_popup_delay', esc_html__('Delay display (seconds)', 'essb'), '' );
ESSBOptionsStructureHelper::field_textbox('instagram', 'popup', 'instagramfeed_popup_width', esc_html__('Custom pop-up width', 'essb'), esc_html__('Numeric value only', 'essb') );
ESSBOptionsStructureHelper::field_textbox('instagram', 'popup', 'instagramfeed_popup_appear_again', esc_html__('Appear again after x days', 'essb'), esc_html__('Leave blank or enter 0 to make it appear all the time. Otherwise fill a numeric value for the number of days (example: 7)', 'essb') );

ESSBOptionsStructureHelper::field_textbox('instagram', 'popup', 'instagramfeed_popup_user', esc_html__('Username', 'essb'), '' );
ESSBOptionsStructureHelper::field_textbox('instagram', 'popup', 'instagramfeed_popup_images', esc_html__('Number of images', 'essb'), esc_html__('Choose between 1 to 12 images appearing on the Instagram widget below content.', 'essb'));

$columns = array( '1col' => esc_html__('1 Column', 'essb'),
		'2cols' => esc_html__('2 Columns', 'essb'),
		'3cols' => esc_html__('3 Columns', 'essb'),
		'4cols' => esc_html__('4 Columns', 'essb'),
		'5cols' => esc_html__('5 Columns', 'essb'),
		'row' => esc_html__('Row', 'essb'));

ESSBOptionsStructureHelper::field_select('instagram', 'popup', 'instagramfeed_popup_columns', esc_html__('Number of columns', 'essb'), '', $columns);

$yesno_options = array('false' => esc_html__('No', 'essb'), 'true' => esc_html__('Yes', 'essb'));
$profile_size = array(
		'normal' => esc_html__('Normal', 'essb'),
		'small' => esc_html__('Small', 'essb'));

ESSBOptionsStructureHelper::field_select('instagram', 'popup', 'instagramfeed_popup_profile_size', esc_html__('Profile size', 'essb'), '', $profile_size);


ESSBOptionsStructureHelper::field_select('instagram', 'popup', 'instagramfeed_popup_masonry', esc_html__('Masonry', 'essb'), '', $yesno_options);

$image_space = array(
		'' => esc_html__('Without space', 'essb'),
		'small' => esc_html__('Small', 'essb'),
		'medium' => esc_html__('Medium', 'essb'),
		'large' => esc_html__('Large', 'essb'),
		'xlarge' => esc_html__('Extra Large', 'essb'),
		'xxlarge' => esc_html__('Extra Extra Large', 'essb'));

ESSBOptionsStructureHelper::field_select('instagram', 'popup', 'instagramfeed_popup_space', esc_html__('Space between images', 'essb'), '', $image_space);
ESSBOptionsStructureHelper::panel_end('instagram', 'popup');

function essb5_advanced_instagram_shortcode() {
	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('Generate Instagram Feed Shortcode [instagram-feed]', 'essb'),
			esc_html__('Generate shortcode for username or hashtag feed. The shortcode can be embedded anywhere inside the content.', 'essb'),
			'instagramfeed-shortcode', 'ao-shortcode', esc_html__('Generate', 'essb'), 'fa fa-code', 'no', '500', '', 'ti-instagram', esc_html__('[instagram-feed] Code Generation', 'essb'), true);

	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('Generate Instagram Image Shortcode [instagram-image]', 'essb'),
			esc_html__('Embed a single Instagram image on site.', 'essb'),
			'instagramimage-shortcode', 'ao-shortcode', esc_html__('Generate', 'essb'), 'fa fa-code', 'no', '500', '', 'ti-image', esc_html__('[instagram-image] Code Generation', 'essb'), true);
}