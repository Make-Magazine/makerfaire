<?php
/**
 * Customer on-boarding wizard
 * 
 * @since 7.0
 * @package EasySocialShareButtons
 * @author appscreo
 */

$total_steps = 15;
$current_step = 1;

if (!essb_option_bool_value('deactivate_module_pinterestpro')) {
	$total_steps++;
}

if (!essb_option_bool_value('deactivate_module_followers')) {
	$total_steps++;
}

if (!essb_option_bool_value('deactivate_module_profiles')) {
	$total_steps++;
}

if (!essb_option_bool_value('deactivate_module_subscribe')) {
	$total_steps++;
}

if (!essb_option_bool_value('deactivate_module_instagram')) {
	$total_steps++;
}

if (!essb_option_bool_value('deactivate_module_proofnotifications')) {
	$total_steps++;
}

?>

<div class="essb-customer-boarding" data-steps="<?php echo esc_attr($total_steps); ?>">
	<i class="boarding-close ti-close"></i>
	<div class="boarding-cards">
		<!-- Customer Boarding Steps -->
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?> active" data-tab="<?php echo esc_attr($current_step); ?>" data-url="">
			<div class="heading">Welcome to Easy Social Share Buttons</div>
			<div class="content">
				<p>Welcome on board. Let us guide you through the main plugin features. During the process, the plugin will load the related setup screen. If you make any changes don't forget to press the <strong class="highlight">Save Settings</strong> button before moving on the next step.</p>
				<p>If you need help with the plugin feature or you face a difficulty, don't hesitate to check the knowledge base or contact our support team.</p>
				<ul class="link-list">
					<li class="highlight"><a href="https://socialsharingplugin.com/getting-started/" target="_blank">Video quick start guide<i class="fa fa-external-link"></i></a>
					<li><a href="https://docs.socialsharingplugin.com" target="_blank">Open knowledge base<i class="fa fa-external-link"></i></a>
					<li><a href="https://support.creoworx.com/forums/forum/wordpress-plugins/easy-social-share-buttons/" target="_blank">Visit support board<i class="fa fa-external-link"></i></a>
				</ul>
			</div>
			
		</div>
		<?php $current_step++; ?>
	
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="">
			<div class="heading">Enable / Disable Plugin Features</div>
			<div class="content">
				<p>At any time you can enable or disable features that you will use or not inside the plugin. Disabling features you don't need will make it easier for you to operate with settings. You can do this from the top menu "<strong>Activate/Deactivate Features</strong>".</p>
				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-manage-features.gif"/>
				</p>
				<p>You can easily the number of active features from the total. Another quick and easy place to enable features of the plugin is using the <strong>Additional Features</strong> menu. This menu you can find below Social Sharing or Additional Social Media features.</p>
				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-additional-features.gif"/>
				</p>
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/managing-plugin-features-enable-or-disable-additional-plugin-functions/" target="_blank">Managing Plugin Features - Enable or Disable Additional Plugin Functions<i class="fa fa-external-link"></i></a>
				</ul>
			</div>
		</div>
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_options&tab=social&section=share-1&boarding='.$current_step));?>">
			<div class="heading">Configure Social Share Networks</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			<div class="content">
				<p>Select and configure the networks you will use on your site. You are still able to select a personalized list of networks on each location of share buttons (including for mobiles). Here you can also configure additional network-specific options not available anywhere. </p>
				
				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-social-networks-basic-operations.gif"/>
				</p>
				
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/social-sharing-setup-social-networks/" target="_blank">Learn more for network setup<i class="fa fa-external-link"></i></a>
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/additional-network-options/" target="_blank">Configure additional network options <i class="fa fa-external-link"></i></a>
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/manage-available-installed-share-networks/" target="_blank">Manage the available networks<i class="fa fa-external-link"></i></a>
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/manage-network-device-visibility-mobile-tablet-desktop/" target="_blank">Configure responsive buttons visibility<i class="fa fa-external-link"></i></a>
					
				</ul>
			</div>
		</div>
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_options&tab=social&section=share-2&boarding='.$current_step));?>">
			<div class="heading">Select The Style of Your Share Buttons</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			<div class="content">
				<p>The style is the look of the buttons on your site. The setup you do here will be used in all places where share buttons appear. You are still able to make a personalized style via location settings too.</p>
				
				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-button-styles.gif"/>
				</p>
				
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/social-sharing-share-buttons-style/" target="_blank">Learn more about Configure and Change Share Buttons' Style<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>
		
		<?php if (!essb_option_bool_value('deactivate_module_pinterestpro')) { ?>
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_options&tab=social&section=pinpro&boarding='.$current_step));?>">
			<div class="heading">Make Images From Your Site Pinnable</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			<div class="content">
				<p>Do you have great images on your site? Then you can use the Pinterest Pro static Pin button for images.</p>
				
				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-pinterest-pro.gif"/>
				</p>
				
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/sharing-images-with-pinterest-pro-setup-tools/" target="_blank">Sharing Images with Pinterest Pro - Setup and Tools<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>
		<?php } ?>
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_where&tab=where&section=posts&boarding='.$current_step));?>">
			<div class="heading">Selecting Post Types for Automatic Share Buttons Display</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			<div class="content">
				<p>Choose the post types you need to show automatically share buttons. You can select multiple at once. There is a separate option at the end of the list that you can enable to show buttons on archive pages for categories, tags or homepage (when it is not a static page).</p>
								
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/automatic-display-of-share-buttons-on-different-post-types-plugins-and-automatic-deactivate-of-display/" target="_blank">Automatic Display of Share Buttons on Different Post Types, Plugins (and Automatic Deactivate of Display)<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_where&tab=where&section=positions&boarding='.$current_step));?>">
			<div class="heading">Select Automatic Display Positions for Social Share Buttons</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			<div class="content">
				<p>
				Select one or multiple locations where the plugin will show automatically share buttons. That will happen on the post tyles you select on the previous step. In case you do not want to have share buttons attached to the content choose "Manual display with shortcode only".
				</p>
				<p>
				You can choose the automatic display positions for each post type too.
				</p>
				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-different-positions-posttype.gif"/>
				</p>
				<p>
				Each of the share button locations has additional display options where you can select a custom style, responsive appearance or modify additional options related to position display.
				</p>
				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-share-button-positions.gif"/>
				</p>
			
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/working-with-automatic-display-positions-for-social-share-buttons-and-plugin-integrations/" target="_blank">Working With Automatic Display Positions for Social Share Buttons (and Plugin Integrations)<i class="fa fa-external-link"></i></a>					
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/additional-display-options-for-the-automatic-share-button-positions/" target="_blank">Additional Display Options for The Automatic Share Button Positions<i class="fa fa-external-link"></i></a>					
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/limit-appearance-of-automatic-display-locations-on-devices/" target="_blank">Limit Appearance of Automatic Display Locations on Devices (Responsive Display)<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_where&tab=where&section=mobile&boarding='.$current_step));?>">
			<div class="heading">Configuration of Mobile Display for Share Buttons</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			<div class="content">
				<p>
				Do you need a specific mobile setup of your share buttons? In the mobile section, you can easily optimize the mobile sharing buttons.
				</p>
			
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/step-by-step-guide-for-setting-up-responsive-buttons-without-dealing-with-mobile-options/" target="_blank">Step By Step Guide For Setting Up Responsive Buttons Without Dealing With Mobile Options<i class="fa fa-external-link"></i></a>					
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/configure-share-buttons-for-mobile/" target="_blank">Configure Share Buttons for Mobile<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="">
			<div class="heading">Additional Sharing Functionality</div>
			<div class="content">
				<p>
				Social sharing in Easy Social Share Buttons has additional awesome features that you may need on your site.
				</p>
				<p>
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/after-share-events/" target="_blank">After Share Events<i class="fa fa-external-link"></i></a>					
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/setup-short-urls-or-sharing/" target="_blank">Setup Short URLs for Sharing<i class="fa fa-external-link"></i></a>					
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/working-with-different-analytics-features-of-wordpress-social-share-buttons/" target="_blank">Working With Different Analytics Features of WordPress Social Share Buttons<i class="fa fa-external-link"></i></a>					
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/affiliate-point-plugins-integration-in-easy-social-share-buttons-for-wordpress/" target="_blank">Affiliate & Point Plugins Integration in Easy Social Share Buttons for WordPress<i class="fa fa-external-link"></i></a>					
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/configure-and-add-sharable-quotes-on-your-site-a-k-a-click-to-tweet/" target="_blank">Configure and Add Sharable Quotes on Your Site (a.k.a. Click to Tweet)<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>
		
		<?php if (!essb_option_bool_value('deactivate_module_followers')) { ?>
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_follow&tab=follow&boarding='.$current_step));?>">
			<div class="heading">Build & Display Your Social Following With Style</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			<div class="content">
				<p>No matter how big is your site it is becoming more and more important to build and retain a social following. Easy Social Share Buttons for WordPress lets you display all of your social profiles follow counts.</p>
				<p>In the plugin, you also have simple social profile links. They are similar to the followers' counter but without setup of access details and showing the numbers.</p>
				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-social-followers.gif"/>
				</p>
				
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/social-followers-counter-general-setup/" target="_blank">Social Followers Counter General Setup<i class="fa fa-external-link"></i></a>					
					<li><a href="https://docs.socialsharingplugin.com/kb/social-followers-counter/" target="_blank">Social Followers Counter Usage<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>
		<?php } ?>
		
		<?php if (!essb_option_bool_value('deactivate_module_profiles')) { ?>
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_profiles&tab=profiles&boarding='.$current_step));?>">
			<div class="heading">Static Social Profile Links</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			<div class="content">
				<p>Add static and privacy safe social profile links. It is similar to the social followers counter but without the need to configure any access details and does not show numbers. If you need eye-catching social profile links without any values this is the best solution for you.</p>
				
				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-social-profiles.gif"/>
				</p>
				
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/how-to-add-social-profile-links-on-your-site/" target="_blank">How to Add Social Profile Links on Your Site<i class="fa fa-external-link"></i></a>					
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/social-profile-links-general-setup/" target="_blank">Social Profile Links - General Setup<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>
		<?php } ?>
		
		<?php if (!essb_option_bool_value('deactivate_module_subscribe')) { ?>
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_subscribe&tab=subscribe&boarding='.$current_step));?>">
			<div class="heading">Subscribe Forms</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			<div class="content">
				<p>If you need to add subscribe to mailing list forms than you can do the setup here. With the plugin, you can add automatically subscribe forms below content, as fly-out or pop-up. You can also add manually forms with widgets, shortcodes or page builder elements.</p>
								
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/kb/subscribe-forms/" target="_blank">Subscribe Forms Setup and Usage<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>
		<?php } ?>				
		
		<?php if (!essb_option_bool_value('deactivate_module_instagram')) { ?>
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_instagram&tab=instagram&boarding='.$current_step));?>">
			<div class="heading">Instagram Feed</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			<div class="content">
				<p>The Instagram Feed is a new module of Easy Social Share Buttons. You can add a profile or hashtag feed on your site using shortcode, automatically below content, as a pop-up.</p>

				<p>You can also use it to embed a single Instagram image with a shortcode too. And that is privacy safe as the image is statically loaded.</p>

				<p>The feed can show up to 12 latest images and does not require any painful token generation process or authorizations.</p>
				
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/kb/instagram-feed/" target="_blank">Instagram Feed Setup and Usage<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>
		<?php } ?>	
		
		<?php if (!essb_option_bool_value('deactivate_module_proofnotifications')) { ?>
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_proof-notifications&tab=proof-notifications&boarding='.$current_step));?>">
			<div class="heading">Social Proof Notifications Lite for Sharing</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			<div class="content">
				<p>Social proof and FOMO ("fear of missing out") leverage psychology to get your site's visitors to take action by showing them the actions that other visitors are taking.</p>

				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-social-proof-notifications.gif"/>
				</p>
				
				<p>Add FOMO notifications for showing shares on your site (with actionable click on message).</p>
				
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/kb/social-proof-notifications/" target="_blank">Social Proof Notifications Setup and Usage<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>
		<?php } ?>	
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_advanced&tab=advanced&section=optimization&boarding='.$current_step));?>">
			<div class="heading">Optimization Over Plugin Resource Loading</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			
			<div class="content">
				<p>
				When you enable plugin we set the default optimization options. This setup is suitable for any site. If you do not use an optimization/cache plugin then you may also consider enabling the option for "Pre-compiled mode".
				</p>
				<p>
				To be easy for you there are two buttons below that will reconfigure the optimization options if you are using or not cache/optimization plugin.
				</p>
				<p>
					<a href="#" class="ao-options-btn ao-boarding-btn ao-boarding-optimize-nocache">Optimizations without a cache plugin</a>
					<a href="#" class="ao-options-btn ao-boarding-btn ao-boarding-optimize-cache">Optimizations with a cache plugin</a>
				</p>
				<p>
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/optimizations-how-to-select-the-working-optimization-options-for-your-site/" target="_blank">Optimizations - How to Select The Working Optimization Options for Your Site<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>	
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_advanced&tab=advanced&section=advanced&boarding='.$current_step));?>">
			<div class="heading">Additional Advanced Options</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>			
			<div class="content">
				<p>
				The additional advanced options section contains fixes for specific issues that may appear on site. They appear rarely but if you have such then you don't need to deal with the code - you have ready to use option for this.
				</p>
				<p>
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/advanced-plugin-settings-integrations/" target="_blank">Advanced Plugin Settings & Integrations<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>	
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_advanced&tab=advanced&section=advanced&boarding='.$current_step));?>">
			<div class="heading">Integrations With Other Plugins</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			
			<div class="content">
				<p>
				The screen contains additional integrations with plugins that you may use on your site. On this screen, you will also find options to enable if you migrate from the Social Warfare plugin (for previous customizations).
				</p>
				<p>
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/advanced-plugin-settings-integrations/#Integrations" target="_blank">Advanced Plugin Settings & Integrations<i class="fa fa-external-link"></i></a>					
				</ul>
			</div>
		</div>	
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_style&tab=style&boarding='.$current_step));?>">
			<div class="heading">Modify Styles</div>
			<div class="save-message">
			If you do a change in this screen do not forget to press the "Save Settings" button before going to the next step.</div>
			
			<div class="content">
				<p>The style settings make possible to modify the current plugin designs without touching code (or code knowledge).</p> 

<p>The color customizer for share buttons, followers counter or social profile links will do a change of the style of currently used template. No matter which you choose it will always apply the custom color.</p>

<p>You can also build own custom Click To Tweet template. The custom template is already assigned in the menu for selection.</p>

<p>Generate a custom share buttons template. This a powerful share button customization. Without being a designer or have any coding knowledge you can build an awesome looking template that will perfectly fit on your site.</p>

<p>And lastly, here you have a place to put a custom CSS code that will work only when the plugin is running. The generation of your custom code is fully compatible with other plugin styles (and allows to replace correctly code without any issues).</p>
			</div>
		</div>	
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_import&tab=import&boarding='.$current_step));?>">
			<div class="heading">Import / Export / Reset Settings</div>
			
			<div class="content">
				<p>
				Using the import/export option of the plugin it is easy to migrate the ready to use settings from one site to another. You will also find here buttons to reset the settings or additional collected data.
				</p>
				<p>
				<ul class="link-list">
					<li><a href="https://docs.socialsharingplugin.com/knowledgebase/import-export-plugin-settings-reset-plugin-settings-or-data/" target="_blank">Import/Export Plugin Settings, Reset Plugin Settings or Data<i class="fa fa-external-link"></i></a>					
				</ul>

			</div>
		</div>	
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="">
			<div class="heading">Using Shortcodes</div>
			
			<div class="content">
				<p>
				In the shortcode generator, you will find all plugin shortcodes for usage. Using the generator you can prepare a code that can be pasted in content or with a function call used in the theme. You can also find a button showing all options for a shortcode.
				</p>
				<p>
				If you are in plugin settings you can start the generator by pressing the "Shortcode Generator" menu.
				</p>
				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-shortcode-generator.gif"/>
				</p>
				<p>
				You can also start the shortcode generator from the plugin top menu too.
				</p>
				<p class="image-center stretched">
					<img class="lazyloading lightbox-zoom" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="https://docs.socialsharingplugin.com/wp-content/uploads/2019/12/boarding-shortcode-generator-top.gif"/>
				</p>
			</div>
		</div>
		
		<?php $current_step++; ?>
		<div class="boarding-card boarding-<?php echo esc_attr($current_step); ?>" data-tab="<?php echo esc_attr($current_step); ?>" data-url="">
			<div class="heading">Getting Help</div>			
			<div class="content">
				<p>
				If you can't find an option, can't configure a display you need or have trouble with plugin work, don't hesitate to contact our support team. We usually respond in just a few hours.
				</p>
				<p>
					<a href="https://support.creoworx.com/forums/forum/wordpress-plugins/easy-social-share-buttons/" target="_blank" class="ao-options-btn ao-boarding-btn ao-gethelp-btn">Go to plugin support</a>
				</p>
			</div>
		</div>	
	
		<!-- End: Customer Boarding Steps -->
		<div class="footer boarding-nav">
			<div class="left boarding-part"><a href="#" class="boarding-progress boarding-back">Back</a></div>
			<div class="right boarding-part"><a href="#" class="boarding-progress boarding-next">Next</a></div>
		</div>
	</div>
	
</div>

<!-- Image preview modal -->
<div id="essb-boarding-modal" class="essb-boarding-modal">

  <!-- The Close Button -->
  <span class="close">&times;</span>

  <!-- Modal Content (The Image) -->
  <img class="modal-content" id="img01">
</div>