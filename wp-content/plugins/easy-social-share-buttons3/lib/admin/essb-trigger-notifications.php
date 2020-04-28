<?php

if (function_exists('essb_dashboard_notification')) {
	essb_dashboard_notification('stumbleupon', 'StumbleUpon officially close at June 30th, 2018 - they are moving in with Mix. We are excited to announce that all versions since 5.6 support Mix.com as social sharing network. For now Mix.com does not have a share counter API - the button will use internal share counter (previous Stumble values cannot be moved).');

	if (essb_option_value('twitter_counters') == 'newsc') {
		$buttons = essb_dashboard_notification_dismiss_button();
		$buttons[] = array('text' => 'Visit share counter settings', 'url' => esc_url(admin_url('admin.php?page=essb_options&tab=social&section=sharecnt&subsection')));
		essb_dashboard_notification('newsharecounts', 'The current Twitter share counter provider you are using NewShareCounts.com has closed its work. It is highly recommended to make a change inside your share counter options.', $buttons, 'error');
	}
}

if (function_exists('essb_interface_notification')) {
	$buttons = essb_dashboard_notification_dismiss_button('<i class="dashicons dashicons-no-alt"></i>'. __('Hide this message', 'essb'));
	$buttons[] = array('text' => '<i class="dashicons dashicons-external"></i>' . __('Learn more for plugin modes', 'essb'), 'url' => 'https://docs.socialsharingplugin.com/knowledgebase/selecting-plugin-mode-which-mode-of-easy-social-share-buttons-for-wordpress-should-i-use/', "target" => "_blank");
	$buttons[] = array('text' => '<i class="dashicons dashicons-external"></i>' . __('Learn how to deactivate unused modules & functions', 'essb'), 'url' => 'https://appscreo.com/introducing-manage-plugin-features-control-plugin-features-functions-from-one-place/', 'target' => '_blank');
	essb_interface_notification('manage_features', '<strong>Easy Social Share Buttons for WordPress</strong> is packed with everything you need to improve your social media presens - social share buttons, followers counter, profile links, subscribe forms, social chat and etc. We know that not all the users will use all available modules and functions. You can easy deactivate the features you will not use from the <a href="#" class="essb-head-featuresbtn"><strong>Manage Plugin Features</strong></a> menu or select a pre-configured plugin operation mode from <a href="#" class="essb-head-modesbtn"><strong>Switch Plugin Mode</strong></a>', $buttons);
}
