<?php 

/**
 * The about screen that appears from the menu of the plugin. The screen also holds
 * the system status information, how to get help and more
 * 
 * @since 5.8.5
 * @package EasySocialShareButtons
 */

$tabs = array( 'about' => '<i class="ti-sharethis"></i> About', 
		'help' => '<i class="fa fa-life-ring"></i> Get Support',
		'activate' => '<i class="ti-lock"></i> Activate',
		'status' => '<i class="ti-receipt"></i> System Status'
		);
$active_tab = isset($_REQUEST['about_tab']) ? $_REQUEST['about_tab'] : 'about';

$current_tab = (empty ( $_GET ['tab'] )) ? $tab_1 : sanitize_text_field ( urldecode ( $_GET ['tab'] ) );
$active_settings_page = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : '';
if (strpos ( $active_settings_page, 'essb_redirect_' ) !== false) {
	$options_page = str_replace ( 'essb_redirect_', '', $active_settings_page );
	if ($options_page != '') {
		$current_tab = $options_page;
	}
}

if ($current_tab == 'update') {
	$active_tab = 'activate';
}
if ($current_tab == 'status') {
	$active_tab = 'status';
}

// setting up the default tab if the selected is not existing in the list
if (!isset($tabs[$active_tab])) { $active_tab = 'about'; }

if (has_filter('essb_unset_activation_page')) {
    $result = false;
    $result = apply_filters('essb_unset_activation_page', $result);
    
    if ($result) {
        unset ($tabs['activate']);
        if ($active_tab == 'activate') { $active_tab = 'about'; }
    }
}



?>

<!--  notifications -->
<script src="<?php echo esc_url(ESSB3_PLUGIN_URL); ?>/assets/admin/jquery.toast.js"></script> 
<link rel="stylesheet" type="text/css" href="<?php echo esc_url(ESSB3_PLUGIN_URL); ?>/assets/admin/jquery.toast.css">
<!-- notifications -->

<div class="intro-header">
	<div class="intro">
		<h3>Welcome to <strong>Easy Social Share Buttons for WordPress</strong></h3>
		<div class="wp-badge essb-page-logo essb-logo">
			<span class="essb-version"><?php echo sprintf( esc_html__( 'Version %s', 'essb' ), ESSB3_VERSION )?></span>
		</div>
	</div>
	<ul class="tab-list">
	<?php 
	foreach ($tabs as $key => $title) {
		?>
		<li data-tab="<?php echo esc_attr($key); ?>" <?php echo ($key == $active_tab ? 'class="current"' : ''); ?>><a href="#"><?php echo $title; ?></a>
		<?php 
	}
	?>
	</ul>
</div>
<div class="panels">
	<!-- about -->
	<div class="panel panel-about<?php echo ($active_tab == 'about' ? ' active' : ''); ?>">
		<div class="left-col">
			<h2><?php echo sprintf( esc_html__( 'Welcome to Easy Social Share Buttons for WordPress %s', 'essb' ), preg_replace( '/^(\d+)(\.\d+)?(\.\d)?/', '$1$2', ESSB3_VERSION ) ) ?></h1>

			<div class="about-text">
				<?php esc_html_e( 'Thank you for choosing the best social sharing plugin for WordPress. You are about to use most powerful social media plugin for WordPress ever - get ready to increase your social shares, followers and mail list subscribers. We hope you enjoy it!', 'essb' )?>
			</div>
			
			<div class="essb-welcome-button-container">
				<a href="https://codecanyon.net/downloads" target="_blank" class="essb-btn essb-btn-orange">Rate us <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></a>
				<a href="https://socialsharingplugin.com/version-changes/" target="_blank" class="essb-btn essb-btn-green">What's New In Version<i class="fa fa-bullhorn"></i></a>
			</div>
			<div class="essb-welcome-button-container">
				<a href="<?php echo esc_url(admin_url('admin.php?page=essb_options&tab=social&section=share-1&boarding=1')); ?>" class="essb-btn essb-about-boarding">Getting Started<i class="fa fa-info-circle"></i></a>
				<a href="https://socialsharingplugin.com/getting-started/" target="_blank" class="essb-btn essb-btn-purple">Video Quick Start Guide<i class="fa fa-play"></i></a>
			</div>
		</div>
		<div class="right-col">
			<img class="essb-right-logo" src="<?php echo ESSB3_PLUGIN_URL;?>/assets/images/welcome-svg.svg" />
		</div>

		<!-- widget activation -->
		<div class="essb-dash-widget essb-dash-shadow essb-dash-activate">
			<div class="essb-dash-title-wrap">
				<div class="essb-dash-title"><h2>Access to Premium Plugin Functions</h2></div>
				<?php 
				if (ESSBActivationManager::isActivated()) {
					?>
					<p>Thank you for your purchase. You have full access to all premium plugin features.</p>
					<?php 
				}
				else {
					if (ESSBActivationManager::isThemeIntegrated()) {
						?>
						<p>You are using a theme integrated version of plugin (bundled inside theme). The premium features are available for direct customers only. If you need access to all those features you can <a href="http://go.appscreo.com/essb" target="_blank">purchase a direct plugin license</a>.</p>
						<?php 						
					}
					else {
						?>
						<p>The premium features are available for direct customers only. To activate those functions you need to register plugin using the purchase code you receive with your order.</p>
						<?php 
					}
				}
				?>
				<a href="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_update'));?>" class="essb-btn <?php if (ESSBActivationManager::isActivated()) { echo "essb-bg-green";} else { echo "essb-bg-red"; } ?>">
					<i class="fa <?php if (ESSBActivationManager::isActivated()) { echo "fa-check";} else { echo "fa-ban"; } ?>"></i>
					<?php 
						if (ESSBActivationManager::isActivated()) { echo esc_html__("Activated", 'essb');} 
						else if (ESSBActivationManager::isThemeIntegrated()) { echo "Activate Plugin With Purchase Code To Transform The License"; }
						else { echo "Activate Plugin to Unlock"; } ?>
				</a>

			</div>
			<div class="essb-dash-widget-inner">
				<div class="essb-dash-feature">
					<div class="essb-feature-icon">
						<i class="ti-reload"></i>
					</div>
					<div class="essb-feature-text">
						<b>Automatic Updates</b>
						<span>Get new versions directly to your dashboard</span>
					</div>
				</div>
				<div class="essb-dash-feature">
					<div class="essb-feature-icon">
						<i class="ti-ruler-pencil"></i>
					</div>
					<div class="essb-feature-text">
						<b>Demo Styles</b>
						<span>One click pre-made styles to quick start with plugin usage</span>
					</div>
				</div>
				<div class="essb-dash-feature">
					<div class="essb-feature-icon">
						<i class="ti-package"></i>
					</div>
					<div class="essb-feature-text">
						<b>Extensions Library</b>
						<span>Exclusive add-ons for our direct buyers only</span>
					</div>
				</div>
				<div class="essb-dash-feature">
					<div class="essb-feature-icon">
						<i class="ti-help-alt"></i>
					</div>
					<div class="essb-feature-text">
						<b>Premium Support</b>
						<span>Receive premium assistance from our support team for everything you need to know about plugin work.</span>
					</div>
				</div>				
			</div>
		</div>
		<!-- end: widget activate -->
		
	</div>
	<!-- status -->
	<div class="panel panel-status<?php echo ($active_tab == 'status' ? ' active' : ''); ?>">
		<h2><?php esc_html_e('System Status', 'essb'); ?></h2>
		<?php include_once(ESSB3_PLUGIN_ROOT.'lib/admin/helpers/system-status.php'); ?>
	</div>
	<!-- help -->
	<div class="panel panel-help<?php echo ($active_tab == 'help' ? ' active' : ''); ?>">
		<div class="panel-help-inner">		
			<div class="left-col">
				<a href="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_update')); ?>" class="essb-btn <?php if (ESSBActivationManager::isActivated()) { echo "essb-bg-green";} else { echo "essb-bg-red"; } ?>">
					<i class="fa <?php if (ESSBActivationManager::isActivated()) { echo "fa-check";} else { echo "fa-ban"; } ?>"></i>
					<?php if (ESSBActivationManager::isActivated()) { echo "Activated";} else { echo "Activate Plugin to Unlock"; } ?>
				</a>
				
				<?php if (ESSBActivationManager::isActivated()) { ?>
				<h2>Getting Support</h2>
				
				<p>We understand all the importance of product support for our customers. That's why we are ready to solve all your issues and answer any questions related to our plugin.</p>
				
				<p>
				<h4>Before Submitting Your Ticket, Please Make Sure That:</h4>
				<ul>
					<li><i class="fa fa-check-circle-o essb-c-green" aria-hidden="true"></i> You are running the latest plugin version. <a href="https://socialsharingplugin.com/version-changes" target="_blank">Check which is the latest version &rarr;</a></li>
					<li><i class="fa fa-check-circle-o essb-c-green" aria-hidden="true"></i> Ensure that there are no errors on site. <a href="https://docs.socialsharingplugin.com/knowledgebase/how-to-activate-debug-mode-in-wordpress/" target="_blank">Activating WordPress Debug Mode &rarr;</a></li>
					<li><i class="fa fa-check-circle-o essb-c-green" aria-hidden="true"></i> Browse the knowledge base. <a href="https://docs.socialsharingplugin.com" target="_blank">Open Knowledge Base &rarr;</a></li>
				</ul>
				</p>
				
				<p>
				<h4>Item Support Includes:</h4>
				<ul>
					<li><i class="fa fa-check essb-c-green" aria-hidden="true"></i> Availability of the author to answer questions</li>
					<li><i class="fa fa-check essb-c-green" aria-hidden="true"></i> Answering technical questions about item's features</li>
					<li><i class="fa fa-check essb-c-green" aria-hidden="true"></i> Assistance with reported bugs and issues</li>
					<li><i class="fa fa-check essb-c-green" aria-hidden="true"></i> Lifetime plugin update</li>
					
					</ul>
				<h4>Item Support Does Not Include:</h4>
				<ul>
					<li><i class="fa fa-times" aria-hidden="true"></i> Customization services</li>
					<li><i class="fa fa-times" aria-hidden="true"></i> Installation services</li>
					</ul>
					</p>
				
				<?php } else { ?>
					<h2>Support is Availabe for Direct Plugin Customers Only</h2>
					
					<p>Easy Social Share Buttons for WordPress comes with 6 months of premium
        support for every <b>direct plugin license</b> you purchase. Support can be <a href="https://help.market.envato.com/hc/en-us/articles/207886473-Extending-and-Renewing-Item-Support" target="_blank">extended through subscriptions</a> via CodeCanyon.
        All support for Easy Social Share Buttons for WordPress is handled
        through our <a href="https://support.creoworx.com" target="_blank">support
          center on our company site</a>. To access it, you must first setup
        an account and verify your Easy Social Share Buttons for WordPress purchase code. If you are not sure where
        you purchase code is located <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">you can read here how to find it</a>.
   </p>
   <p></p><p>
     Access to our support system is limited to all direct customers of
     plugin. If the plugin version you are using is bundled inside theme
     you need to purchase <a href="http://go.appscreo.com/essb" target="_blank">a direct plugin license</a> to receive priority plugin support. Priority support
     is reserved for direct customers only. You can read more about <a href="https://socialsharingplugin.com/direct-customer-benefits/" target="_blank">direct customer benefirts here</a>.
   </p>
				<?php } ?>				
				
				<div class="essb-welcome-button-container">					
					<?php if (!ESSBActivationManager::isActivated()) { ?>
					<a href="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_update'));?>" class="essb-btn essb-btn-green essb-back-to-settings1">Activate Plugin License <i class="fa fa-key"></i></a>
					<?php } else { ?>
					<a href="https://support.creoworx.com/forums/forum/wordpress-plugins/easy-social-share-buttons/" target="_blank" class="essb-btn essb-btn-green essb-back-to-settings1">Submit a Topic<i class="fa fa-external-link"></i></a>
					<?php } ?>
					</div>
			</div>
			<div class="right-col essb-align-center">
				<img class="support-image" src="<?php echo ESSB3_PLUGIN_URL;?>/assets/images/support.svg" />
			</div>	
		</div>
	</div>
	<!-- activate -->
	<div class="panel panel-activate<?php echo ($active_tab == 'activate' ? ' active' : ''); ?>">
		<div class="left-col">
			<div class="essb-activation-form">
				<div class="essb-activation-form-title">
					<div class="essb-activation-title<?php if (ESSBActivationManager::isActivated()) { echo " color-activated"; } else { echo " color-notactivated"; } ?>"><?php echo esc_html__('Plugin Activation', 'essb');?></div>
					<div class="essb-activation-state<?php if (ESSBActivationManager::isActivated()) { echo " background-activated"; } else { echo " background-notactivated"; } ?>">
						<i class="fa fa-<?php if (ESSBActivationManager::isActivated() || ESSBActivationManager::isThemeIntegrated()) { echo "check"; } else { echo "ban"; } ?>"></i> <?php if (ESSBActivationManager::isActivated()) { echo esc_html__('Activated', 'essb'); } else { 
							if (ESSBActivationManager::isThemeIntegrated()) {
								echo esc_html__('Theme Integrated', 'essb');
							}
							else {
								echo esc_html__('Not activated', 'essb'); 
							}
						} ?>			
					</div>
				</div>
				
				<?php if (!ESSBActivationManager::isActivated() && !ESSBActivationManager::isThemeIntegrated() && ESSBActivationManager::isDevelopment()):?>
					<div class="essb-activate-localhost">
						<?php esc_html_e('You are running plugin on development environment. Activation in this case is optional and it will allow you to use locked plugin features without reflecting activation on your real site.', 'essb'); ?>
					</div>
				<?php endif; ?>
				
				<div class="essb-activation-form-code">
					<div class="essb-activation-form-header">
						<strong><?php echo esc_html__('Purchase code', 'essb');?></strong>
						<br/>You can learn how to find your purchase code <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">here</a>
					
					</div>
					<input type="text" class="essb-purchase-code" id="essb-automatic-purchase-code" value="<?php echo ESSBActivationManager::getPurchaseCode(); ?>" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"/>			
				</div>
			
			
				<div class="essb-activation-buttons">
					<?php if (!ESSBActivationManager::isActivated()) { ?>
						<a href="#" id="essb-activate" class="essb-activation-button essb-activation-button-default essb-activate-plugin"><?php echo esc_html__('Register the code', 'essb'); ?></a>
					<?php } ?>
					<?php if (ESSBActivationManager::isActivated()) { ?>
						<a href="#" id="essb-deactivate" class="essb-activation-button essb-activation-button-default essb-deactivate-plugin"><?php echo esc_html__('Deregister the code', 'essb'); ?></a>
					<?php } ?>
					<a href="http://go.appscreo.com/activate-essb" target="_blank" id="essb-manager1" class="essb-activation-button essb-activation-button-color2 essb-manage-activation-plugin essb-button-right"><?php echo esc_html__('Need help with activation?', 'essb'); ?></a>
				</div>
				<div class="essb-activation-manager">
					<h4>Managing Plugin Activations</h4>
					<p>From the license manage control panel you can check your past code activations, deactivate current plugin activations or manually activate plugin for a domain. The access to activation manager require to fill your Envato username and the purchase code.</p>
					<a href="<?php echo esc_url(ESSBActivationManager::getApiUrl('manager').'?purchase_code='.ESSBActivationManager::getPurchaseCode());?>" target="_blank" id="essb-manager" class="essb-activation-button essb-activation-button-color1 essb-manage-activation-plugin"><?php echo esc_html__('Manage my activations', 'essb'); ?></a>
				</div>
			</div>
			
			<!-- manual activation -->
			<?php if (!ESSBActivationManager::isActivated()): ?>
					<div class="essb-activation-form">
			<div class="essb-activation-form-title">
				<div class="essb-activation-title<?php if (ESSBActivationManager::isActivated()) { echo " color-activated"; } else { echo " color-notactivated"; } ?>"><?php echo esc_html__('Manual Plugin Activation', 'essb');?></div>			
			</div>
			<div class="essb-activation-form-code">
				If you have problem with automatic plugin registration please <a href="#" id="essb-activate-manual-registration">click here to activate it manually</a>.
			</div>
			
			<div id="essb-manual-registration">
			<div class="essb-activation-form-code">
				<div class="essb-activation-form-header">
					<strong><?php echo esc_html__('Purchase code', 'essb');?></strong>
					<br/>You can learn how to find your purchase code <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">here</a>
					
				</div>
				<input type="text" id="essb-manual-purchase-code" class="essb-purchase-code" value="<?php echo esc_attr(ESSBActivationManager::getPurchaseCode()); ?>" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"/>			
			</div>
			<div class="essb-activation-form-code">
				<div class="essb-activation-form-header">
					<strong><?php echo esc_html__('Activation code', 'essb');?></strong>
					<br/><a href="<?php echo esc_attr(ESSBActivationManager::getApiUrl('activate_domain')); ?>" target="_blank">Go to our manual activation page and fill in all required details to receive your activation code</a>. In the domain field enter <b><?php echo ESSBActivationManager::domain();?></b>
					
				</div>
				<input type="text" id="essb-manual-activation-code" class="essb-purchase-code" value="" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"/>			
			</div>
			
			<div class="essb-activation-buttons">
				<?php if (!ESSBActivationManager::isActivated()) { ?>
				<a href="#" id="essb-manual-activate" class="essb-activation-button essb-activation-button-default essb-manual-activate-plugin"><?php echo esc_html__('Manual registration of code', 'essb'); ?></a>
				<?php } ?>
				
				
			</div>
			</div>
		</div>
			
			<?php endif; ?>
		</div>
		<div class="right-col">
			<?php if (!ESSBActivationManager::isActivated()) { ?>
			
				<?php if (ESSBActivationManager::isThemeIntegrated()) { ?>
					<div class="license-desc theme-integrated">
					
					<h4>Theme Integrated License Active</h4>
					
					
					You are using a theme integrated version of Easy Social Share Buttons for WordPress. The bundled inside theme versions does not require activation with purchase code. The bundled inside theme versions does not have access to direct customer benefits. If you wish to use all the direct customer benefits (including support for your best social media plugin) you need to purchase a direct plugin license and activate plugin using it.
<ul>
<li><i class="fa fa-check"></i> Access official customer support (opening support tickets are available only for direct license owners);</li>
<li><i class="fa fa-check"></i> Automatic plugin updates directly inside your WordPress dashboard (no need to wait - get instant updates);</li>
<li><i class="fa fa-check"></i> Access to Extensions Library: Download and install professional extensions to expand functionality of your social sharing plugin (updated regularly).</li>
<li><i class="fa fa-check"></i> Access to Ready Made Styles Library with Demo Configurations - install professional designed layouts with one click</li>
<li><i class="fa fa-check"></i> Use Easy Social Share Buttons for WordPress with any theme (not just the one that got Easy Social Share Buttons for WordPress bundled);</li>
<li><i class="fa fa-check"></i> Support your beloved social media plugin for rapid development.</li>
</ul>
	<p><a href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo&license=regular&open_purchase_for_item_id=6394476&purchasable=source" target="blank" class="essb-btn essb-btn-white">Purchase copy of Easy Social Share Buttons &rarr;</a></p>
<p class="purchase-desc">Purchase of Easy Social Share Buttons for WordPress is $20 one time payment without year or month fees and including 6 months of premium support. Each license can be used on one site at same time (you can transfer the license to new site).</p>
					</div>
				<?php } else { ?>
					<div class="license-desc not-activated">
					
					<h4>Plugin Activation Required</h4>
					
					Activate plugin to unlock the following premium features:
<ul>
<li><i class="fa fa-check"></i> Automatic plugin updates directly inside your WordPress dashboard (no need to wait - get instant updates);</li>
<li><i class="fa fa-check"></i> Access to Extensions Library: Download and install professional extensions to expand functionality of your social sharing plugin (updated regularly).</li>
<li><i class="fa fa-check"></i> Access to Ready Made Demo Configurations - install professional designed layouts with one click</li>
</ul>
					</div>				
				<?php } ?>
			
			<?php } else { ?>
					<div class="license-desc activated">
					
					<h4>Your Plugin is Fully Activated</h4>
					
					
In order to register your purchase code on another domain, deregister it first by clicking the button above or get another purchase code. You can also check and manage your activations via Manage my activations button. If you need to use plugin on multiple sites at same time than you need to have a separate license for each active domain.
	<p><a href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo&license=regular&open_purchase_for_item_id=6394476&purchasable=source" target="blank" class="essb-btn essb-btn-white">Purchase Another copy of Easy Social Share Buttons &rarr;</a></p>
<p class="purchase-desc">Purchase of Easy Social Share Buttons for WordPress is $20 one time payment without year or month fees and including 6 months of premium support. Each license can be used on one site at same time (you can transfer the license to new site).</p>
					</div>			
			<?php } ?>
		</div>
	</div>
	
			<div class="footer">
			<h2>Useful Resources</h2>
			<div class="onethird">
					<div class="essb-feature-icon">
						<i class="ti-book"></i>
					</div>
					<div class="essb-feature-text">
						<b>Knowledge Base</b>
						<span>Read our knowledge base to get know how to use most common functions</span>
						<div class="mt30">
						<a href="https://docs.socialsharingplugin.com/?utm_source=about&amp;utm_campaign=panel&amp;utm_medium=button" class="essb-btn essb-btn-blue2" target="_blank">Visit Knowledge Base &rarr;</a>
						</div>
					</div>
			</div>
			<div class="onethird">
					<div class="essb-feature-icon">
						<i class="ti-email"></i>
					</div>
					<div class="essb-feature-text">
						<b>Get Notications in Your Inbox</b>
						<span>
						Join the newsletter to receive emails when we release plugin or theme updates, send out free resources, announce promotions and more!						
						</span>
						<div class="mt30">
						<form action="//appscreo.us13.list-manage.com/subscribe/post?u=a1d01670c240536f6a70e7778&amp;id=c896311986" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<input type="email" name="EMAIL" id="mce-EMAIL" autocomplete="off" placeholder="Enter your email" style="width: 90%; border-radius: 3px; padding: 10px; display: block; margin: 0 auto; margin-bottom: 10px;" />
						<input type="submit" name="subscribe" id="mc-embedded-subscribe" class="essb-btn essb-btn-blue2" value="Subscribe" style="box-shadow: none;">
						</form>
						</div>
					</div>
			</div>
			<div class="onethird">
					<div class="essb-feature-icon">
						<i class="ti-info-alt"></i>
					</div>
					<div class="essb-feature-text">
						<b>Social Media Blog</b>
						<span>Read our blog for get to know the latest plugin functions and useful WordPress tips and tricks</span>
						<div class="mt30">
						<a href="https://appscreo.com/?utm_source=about&amp;utm_campaign=panel&amp;utm_medium=button" class="essb-btn essb-btn-blue2" target="_blank">Visit Our Blog &rarr;</a>
						</div>
					</div>
			</div>
			<p class="essb-thank-you">
				Thank you for choosing <b><a href="http://go.appscreo.com/essb" target="_blank">Easy Social Share Buttons for WordPress</a></b>.
				If you like our work please <a href="http://codecanyon.net/downloads" target="_blank">rate Easy Social Share Buttons for WordPress <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></a>
			</p>
		</div>
	
</div>

<!-- plugin activations screen -->
	<script type="text/javascript">

	var essb_api_activate_domain = "<?php echo esc_attr(ESSBActivationManager::domain()); ?>";
	var essb_api_activate_url = "<?php echo esc_url(ESSBActivationManager::getSiteURL()); ?>";
	var essb_api_url = "<?php echo esc_url(ESSBActivationManager::getApiUrl('api')); ?>";
	var essb_ajax_url = "<?php echo esc_url(admin_url ('admin-ajax.php')); ?>";

	var essb_used_purchasecode = "<?php echo esc_url(ESSBActivationManager::getPurchaseCode()); ?>";
	var essb_used_activationcode = "<?php echo esc_url(ESSBActivationManager::getActivationCode()); ?>";
	
	jQuery(document).ready(function($){
		"use strict";

		$('.tab-list li').each(function() {
			$(this).click(function(e) {
				e.preventDefault();

				$('.tab-list li').removeClass('current');
				$(this).addClass('current');

				$('.panels .panel').removeClass('active');
				var tab = $(this).attr('data-tab') || '';

				$('.panels .panel-' + tab).addClass('active');
			});
		});
		
		if ($('#essb-activate-manual-registration').length) {
			$('#essb-activate-manual-registration').click(function(e) {
				e.preventDefault();

				if (!$('#essb-activate-manual-registration').hasClass('opened')) {
					$('#essb-manual-registration').fadeIn('200');
					$('#essb-activate-manual-registration').addClass('opened');
				}
				else {
					$('#essb-manual-registration').fadeOut('200');
					$('#essb-activate-manual-registration').removeClass('opened');
				}
			});
		}

		if ($('#essb-manual-activate').length) {
			$('#essb-manual-activate').click(function(e) {
				e.preventDefault();

				var purchase_code = $('#essb-manual-purchase-code').val();
				var activation_code = $('#essb-manual-activation-code').val();

				if (purchase_code == '' || activation_code == '') {
					$.toast({
					    heading: 'Missing Activation Data',
					    text: 'Please fill purchase code and activation code before processing with activation',
					    showHideTransition: 'fade',
					    icon: 'error',
					    position: 'bottom-right',
					    hideAfter: 5000
					});

					return;
				}

				$('#essb-cc-preloader').fadeIn(100);

				$.ajax({
		            type: "POST",
		            url: essb_ajax_url,
		            data: { 'action': 'essb_process_activation', 'purchase_code': purchase_code, 'activation_code': activation_code, 'activation_state': 'manual', 'domain': essb_api_activate_domain},
		            success: function (data) {
		            	$('#essb-cc-preloader').fadeOut(400);
    		            console.log(data);
    		            if (typeof(data) == "string")
		                	data = JSON.parse(data);

						var code = data['code'] || '';

	                	if (code != '100') {
	                		sweetAlert({
			            	    title: "Activation Error",
			            	    text: "Purchase code and activation code did not match. Please check them again and if problem exists contact us.",
			            	    type: "error"
			            	});
	                	}
	                	else {
	                		sweetAlert({
    		            	    title: "Activation Successful",
    		            	    text: "Thank you for activating Easy Social Share Buttons for WordPress.",
    		            	    type: "success"
    		            	}).then((value) => {
	    		            	  if (value) window.location.reload();
	    		            	});
	                	}
		            }
            	});
			});
		}
		
		if ($('#essb-activate').length) {
			$('#essb-activate').click(function(e) {
				e.preventDefault();

				var purchase_code = $('#essb-automatic-purchase-code').val();

				if (purchase_code == '') {
					$.toast({
					    heading: 'Missing Purchase Code',
					    text: 'Please fill purchase code before processing with activation',
					    showHideTransition: 'fade',
					    icon: 'error',
					    position: 'bottom-right',
					    hideAfter: 5000
					});

					return;
				}

				$('#essb-cc-preloader').fadeIn(100);
				console.log(purchase_code + '-'+essb_api_activate_domain);
				console.log({ 'code': purchase_code, 'domain': essb_api_activate_domain, 'url': essb_api_activate_url});
				console.log(essb_api_url);
				$.ajax({
		            type: "POST",
		            url: essb_api_url,
		            data: { 'code': purchase_code, 'domain': essb_api_activate_domain, 'url': essb_api_activate_url},
		            success: function (data) {
		                $('#essb-cc-preloader').fadeOut(400);
		                console.log(data);
		                if (typeof(data) == "string")
		                	data = JSON.parse(data);
		                
		                var code = data['code'] || '';
		                var activation_message = data['message'] || '';
		                var activation_code = data['hash'] || '';
		                
		                console.log('code = '+ code);
		                console.log('activation_message = '+ activation_message);
		                console.log('activation_code = ' + activation_code);
		                
		                if (parseInt(code) > 0 && parseInt(code) < 10) {
		                	$.ajax({
		    		            type: "POST",
		    		            url: essb_ajax_url,
		    		            data: { 'action': 'essb_process_activation', 'purchase_code': purchase_code, 'activation_code': activation_code, 'activation_state': 'activate'},
		    		            success: function (data) {
			    		            console.log(data);

			    		            sweetAlert({
		    		            	    title: "Activation Successful",
		    		            	    text: "Thank you for activating Easy Social Share Buttons for WordPress.",
		    		            	    type: "success"
		    		            	}).then((value) => {
			    		            	  if (value) window.location.reload();
			    		            	});
		    		            }
		                	});

		                }
		                else {
		                	swal("Activation Error", ''+activation_message+'', "error");
		                }

		                
		            },
		            error: function(data) {
		            	 $('#essb-cc-preloader').fadeOut(400);
		            	 $.toast({
							    heading: 'Connection Error',
							    text: 'Cannot connection to registration server. Please try again and if problem still exist proceed with manual activation.',
							    showHideTransition: 'fade',
							    icon: 'error',
							    position: 'bottom-right',
							    hideAfter: 5000
							});
		            }
		        });
			});
		}
		
		if ($('#essb-deactivate').length) {
			$('#essb-deactivate').click(function(e) {
				e.preventDefault();

				var purchase_code = essb_used_purchasecode;

				if (purchase_code == '') {
					$.toast({
					    heading: 'Missing Purchase Code',
					    text: 'Please fill purchase code before processing with activation',
					    showHideTransition: 'fade',
					    icon: 'error',
					    position: 'bottom-right',
					    hideAfter: 5000
					});

					return;
				}

				$('#essb-cc-preloader').fadeIn(100);
				console.log(purchase_code + '-'+essb_api_activate_domain);
				$.ajax({
		            type: "POST",
		            url: essb_api_url + 'deactivate.php',
		            data: { 'hash': essb_used_activationcode, 'code': essb_used_purchasecode },
		            success: function (data) {
		                $('#essb-cc-preloader').fadeOut(400);
		                console.log(data);
		                if (typeof(data) == "string")
		                	data = JSON.parse(data);
		                
		                var code = data['code'] || '';
		                var activation_message = data['message'] || '';
		                var activation_code = data['hash'] || '';
		                
		                console.log('code = '+ code);
		                console.log('activation_message = '+ activation_message);
		                console.log('activation_code = ' + activation_code);
		                
		                if (parseInt(code) > 0 && parseInt(code) < 10) {
		                	$.ajax({
		    		            type: "POST",
		    		            url: essb_ajax_url,
		    		            data: { 'action': 'essb_process_activation', 'activation_state': 'deactivate'},
		    		            success: function (data) {
		    		            	window.location.reload();
		    		            }
		                	});

		                }
		                else {
		                	swal("Deactivation Error", '<b>'+activation_message+'</b>', "error");
		                }

		                
		            },
		            error: function(data) {
		            	 $('#essb-cc-preloader').fadeOut(400);
		            	 $.toast({
							    heading: 'Connection Error',
							    text: 'Cannot connection to registration server. Please try again and if problem still exist proceed with manual activation.',
							    showHideTransition: 'fade',
							    icon: 'error',
							    position: 'bottom-right',
							    hideAfter: 5000
							});
		            }
		        });
			});
		}
		

	});

	</script>
