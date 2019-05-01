<?php 

/**
 * The about screen that appears from the menu of the plugin. The screen also holds
 * the system status information, how to get help and more
 * 
 * @since 5.8.5
 * @package EasySocialShareButtons
 */

$tabs = array( 'about' => '<i class="ti-sharethis"></i> About', 
		'help' => '<i class="fa fa-question"></i> Need Help?',
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

?>

<!-- Custom CSS styles that formats that about page -->
<style type="text/css">
.essb-wrap-about .essb-settings-panel-navigation { display: none; }
.essb-wrap-about .essb-settings-panel-options { width: 100%; }
.essb-wrap-about .essb-settings-panel { background-color: transparent; }

.essb-btn-blue2 {
	background: #2b4b80;
}

.intro-header { 
	background-color: #2b4b80;
	padding: 2% 0 0 0;
	color: #fff;
	background: #2b4b80 url(<?php echo ESSB3_PLUGIN_URL;?>/assets/images/about-pattern.svg);
	background-size: 350px;
}

.essb-page-logo {
	background: transparent;
	background-size: 64px;
	background-repeat: no-repeat;
	box-shadow: none;
	padding-top: 40px;
	float: right;
	background-position: top;
}

.essb-version { display: none; }

.intro {
	padding: 0 50px;
	color: #fff;
	margin-bottom: 0;
	display: inline-block;
	width: calc(100% - 100px);
}

.intro h3 { 
	color: #fff;
	display: inline-block;
	font-size: 30px;
	font-weight: 300;
	letter-spacing: -0.04em;
}

@media screen and (max-width: 1140px) {
	.intro h3 { font-size: 24px; }
}

@media screen and (max-width: 970px) {
	.intro h3 { font-size: 21px; }
}
	
.tab-list {
	padding: 0 50px;
	color: #fff;
	margin: 0;
	display: inline-block;
	width: calc(100% - 100px);
}
	
.tab-list li {
	display: inline-block;
	margin: 0 0 0 0;
}

.tab-list li:last-child {
	margin-right: 0;
	padding-right: 0;
}

.tab-list li a {
	font-size: 16px;
	text-decoration: none;
	padding: 20px 25px;
	display: inline-block;
	color: #fff;
}	

.tab-list li.current a {
	background: #fff;
	outline: none;
	border: none;
	box-shadow: none;
	border-top-right-radius: 3px;
	border-top-left-radius: 3px;
	color: #2b4b80;
}

.tab-list li a i {
	font-size: 16px;
	margin-right: 5px;
}

.panels {
	background: #fff;
	padding: 25px 50px;
	font-size: 16px;
	line-height: 1.5em;
}	

.panels p {
	font-size: 16px;
	line-height: 1.5em;
}

.panels .panel {
	display: none; 
}

.panels .panel h2, .panels .footer h2  {
	font-size: 28px;
	font-weight: 400;
	line-height: 1.3em;
	text-transform: capitalize;
}

.panels .panel.active { display: block; }
.panels .footer { margin-top: 4%; }
	
/* Status */
 mark.error {
	color: #a00;
	background: 0;
 	font-weight: bold;
}

 mark.yes {
	color: #7ad03a;
	background: 0;
 	font-weight: bold;
}

mark.message {
	background: 0;
	font-weight: bold;
}

mark.warning {
	background: 0;
	font-weight: bold;
	color: #FD5B03;
}

.panels .sub4 {
	font-weight: 600;
	width: 100%;
	padding: 15px 20px;
	text-transform: capitalize;
	color: #fff;
	background-color: #2b4b80;
}

.panels .sub4 div::before {
	font-family: 'FontAwesome';
	  content: "\f0c9";
	font-weight: normal;
	margin-right: 10px;
}

.left-col {
	display: inline-block;
	width: 49%;
	padding-right: 1%;
	vertical-align: top;
}

.right-col {
	display: inline-block;
	width: 48%;
	padding-left: 1%;
	vertical-align: top;
}
	
.essb-btn {
	font-size: 14px;
	text-transform: capitalize;
	padding: 15px 20px;
}

.essb-bg-red {
	background: #e74c3c;
}

a.essb-bg-red:hover {
	background: #d62c1a;
}


.essb-bg-green {
	background: #27ae60;
}

.essb-c-green { color: #27ae60; }
	
.essb-btn i:nth-child(1) { margin-left: 5px; }

.essb-dash-title-wrap { text-align: center; }
	
.essb-dash-feature {
    width: calc(31% - 90px);
    display: inline-block;
    margin: 1%;
    background: #fff;
    padding: 45px;
    box-shadow: 0 0 20px 0 rgba(0,0,0,0.07);
	vertical-align: top;
	min-height: 180px;
}
	
@media screen and (max-width: 1200px) {
	.essb-dash-feature { width: calc(47% - 90px); }
}
	
.essb-dash-feature .essb-feature-icon, .essb-feature-icon {
    text-align: center;
    font-size: 48px;
    margin-bottom: 30px;
    vertical-align: top;
    color:#3c8fc6;
}

.essb-dash-feature .essb-feature-text, .essb-feature-text { text-align: center; }

.essb-dash-feature .essb-feature-text b, .essb-feature-text b {
	display: block;
	font-size: 18px;
    margin-bottom: 5px;
}

.onethird {
	width: calc(31% - 90px);
    display: inline-block;
    margin: 1%;
    background: #fff;
    padding: 45px;
    vertical-align: top;
}
@media screen and (max-width:1100px) {
  .onethird { padding: 30px; }

}

@media screen and (max-width:1200px) {
  .panel-activate .left-col, .panel-activate .right-col {
  	width: auto;
  	display: block;
  }
}	
	
@media screen and (max-width:1000px) {
   .onethird { display: block; width: auto; }
}

.system-status td {
	font-size: 14px;
	padding: 8px 4px;
}

.faq .essb-portlet { border: 0px; border-bottom: 1px solid rgba(0,0,0,0.2); box-shadow: none; -webkit-box-shadow: none; }
.faq .essb-portlet .essb-portlet-heading h3 { font-size: 15px; text-transform: none; }

.system-status td .label {
	font-size: 13px;
}
</style>

<style type="text/css">
	.essb-options-header { padding-top: 20px; }
	.essb-options-title { text-align: center; }
	.essb-activation-wrap {
		max-width: 900px;
		width: 100%;	
		margin: 0 auto;		
	}
	
	.essb-activate-welcome {
		padding: 15px;
		background-color: #f5f6f7;
		font-size: 13px;
		line-height: 20px;	
		border-radius: 4px;
		margin-top: 30px;
	}
	
	.essb-button-backtotop { 
		display: none !important;	
	}
	
	.essb-activate-localhost {
		font-size: 14px;
		font-style: italic;
	}
	
	.color-notactivated {
		color: #e74c3c;
	}
	
	.background-notactivated {
		background-color: #e74c3c;	
	}
	
	.color-activated {
		color: #27ae60;	
	}
	
	.essb-options-hint-addonhint i {
		color: #27ae60;	
	}
	
	.background-activated {
		background-color: #27ae60;
	}
	
	.essb-activation-form {
		margin-top: 15px;	

	}
	
	.essb-activation-form-title {
		position: relative;
}
	
	.essb-activation-title {
		font-size: 19px;
		font-weight: 600;
		line-height: 32px;
	}
	
	.essb-activation-state {
		font-weight: 600;
		border-radius: 4px;
		padding: 0px 15px;
		line-height: 32px;
		color: #fff;
		font-size: 13px;
		position: absolute;
		right: 0px;
	}
	
	.essb-activation-title, .essb-activation-state {
		display: inline-block;
	}
		
	.essb-activation-form-code {
		clear: both;
		margin-top: 15px;
	}
	
	.essb-activation-buttons {
		clear: both;
		position: relative;
		margin-top: 15px;
}
	
	.essb-purchase-code {
		box-shadow: none !important;
		background-color: #eaeaea !important;
		border-radius: 4px !important;
		padding: 15px;
		font-size: 15px;
		font-weight: 600;
		color: #303133 !important;
		border: 0px !important;	
		width: 100%;
	}
	
	.essb-activation-button {
		font-weight: 600;
		border-radius: 4px;
		padding: 0px 25px;
		line-height: 40px;
		color: #fff;
		font-size: 14px;
		display: inline-block;
		text-decoration: none;
		cursor: pointer;
	}
	
	.essb-activation-button-default {
		background-color: #3498db;
	}
	.essb-activation-button-default:hover, .essb-activation-button-default:active, .essb-activation-button-default:focus {
		background-color: #2c8ac8;
		color: #fff;
		text-decoration: none !important;
	}

		.essb-activation-button-color1 {
		background-color: #BB3658;
	}
	
	.essb-activation-button-color2 {
		background-color: #FD5B03;
}
	
	.essb-activation-button-color1:hover, .essb-activation-button-color1:active, .essb-activation-button-color1:focus {
		background-color: #7E3661;
		color: #fff;
		text-decoration: none !important;
	}

		.essb-activation-button-color2:hover, .essb-activation-button-color2:active, .essb-activation-button-color2:focus {
		background-color: #F04903;
		color: #fff;
		text-decoration: none !important;
	}
	
	
	.essb-button-right {
		float: right;
}
	
.essb-purchase-code::-webkit-input-placeholder {
   color: #aaa;
}

.essb-purchase-code:-moz-placeholder { /* Firefox 18- */
   color: #aaa;  
}

.essb-purchase-code::-moz-placeholder {  /* Firefox 19+ */
   color: #aaa;  
}

.essb-purchase-code:-ms-input-placeholder {  
   color: #aaa;  
}
.essb-activation-form-header strong {
	font-size: 15px;
}
.essb-activation-form-header { margin-bottom: 5px; }
#essb-manual-registration { display: none; }
</style>

<!--  notifications -->
<script src="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/jquery.toast.js"></script> 
<link rel="stylesheet" type="text/css" href="<?php echo ESSB3_PLUGIN_URL?>/assets/admin/jquery.toast.css">
<!-- notifications -->

<div class="intro-header">
	<div class="intro">
		<h3>Welcome to <strong>Easy Social Share Buttons for WordPress</strong></h3>
		<div class="wp-badge essb-page-logo essb-logo">
			<span class="essb-version"><?php echo sprintf( __( 'Version %s', 'essb' ), ESSB3_VERSION )?></span>
		</div>
	</div>
	<ul class="tab-list">
	<?php 
	foreach ($tabs as $key => $title) {
		?>
		<li data-tab="<?php echo $key; ?>" <?php echo ($key == $active_tab ? 'class="current"' : ''); ?>><a href="#"><?php echo $title; ?></a>
		<?php 
	}
	?>
	</ul>
</div>
<div class="panels">
	<!-- about -->
	<div class="panel panel-about<?php echo ($active_tab == 'about' ? ' active' : ''); ?>">
		<div class="left-col" style="padding-top: 1%;">
			<h2><?php echo sprintf( __( 'Welcome to Easy Social Share Buttons for WordPress %s', 'essb' ), preg_replace( '/^(\d+)(\.\d+)?(\.\d)?/', '$1$2', ESSB3_VERSION ) ) ?></h1>

			<div class="about-text">
				<?php _e( 'Thank you for choosing the best social sharing plugin for WordPress. You are about to use most powerful social media plugin for WordPress ever - get ready to increase your social shares, followers and mail list subscribers. We hope you enjoy it!', 'essb' )?>
			</div>
			
			<div class="essb-welcome-button-container" style="margin-top: 2%;">
				<a href="<?php echo admin_url('admin.php?page=essb_options');?>" class="essb-btn essb-btn-blue" style="margin-right: 10px;margin-top: 10px;">Configure Plugin <i class="fa fa-cog"></i></a>
				<a href="https://codecanyon.net/downloads" target="_blank" class="essb-btn essb-btn-orange" style="margin-right: 10px;margin-top: 10px;">Rate us <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></a>
				<a href="https://socialsharingplugin.com/version-changes/" target="_blank" class="essb-btn essb-btn-green" style="margin-right: 10px;margin-top: 10px;">What's New In Version<i class="fa fa-bullhorn"></i></a>
				</div>
		</div>
		<div class="right-col" style="text-align: center;">
			<img src="<?php echo ESSB3_PLUGIN_URL;?>/assets/images/welcome-svg.svg" style="max-width: 350px;" />
		</div>

		<!-- widget activation -->
		<div class="essb-dash-widget essb-dash-shadow essb-dash-activate" style="background:#f5f4f6; padding: 3%; margin-top: 3%;">
			<div class="essb-dash-title-wrap">
				<div style="margin-bottom: 10px;" class="essb-dash-title"><h2 style="margin-bottom: 0;">Access to Premium Plugin Functions</h2></div>
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
				<a style="margin-bottom: 1%;" href="<?php echo admin_url('admin.php?page=essb_redirect_update');?>" class="essb-btn <?php if (ESSBActivationManager::isActivated()) { echo "essb-bg-green";} else { echo "essb-bg-red"; } ?>">
					<i class="fa <?php if (ESSBActivationManager::isActivated()) { echo "fa-check";} else { echo "fa-ban"; } ?>"></i>
					<?php 
						if (ESSBActivationManager::isActivated()) { echo __("Activated", 'essb');} 
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
		<a href="<?php echo admin_url('admin.php?page=essb_options');?>" class="essb-btn essb-btn-blue2" style="margin-right: 10px; float: right;">Return to Plugin Settings<i class="fa fa-cog"></i></a>
		<h2><?php _e('System Status', 'essb'); ?></h2>
		<?php include_once(ESSB3_PLUGIN_ROOT.'lib/admin/helpers/system-status.php'); ?>
	</div>
	<!-- help -->
	<div class="panel panel-help<?php echo ($active_tab == 'help' ? ' active' : ''); ?>">
		<div style="background:#f5f4f6; padding: 3%; margin-top: 3%;">		
			<div class="left-col" style="padding-top: 1%;">
				<a href="<?php echo admin_url('admin.php?page=essb_redirect_update');?>" class="essb-btn <?php if (ESSBActivationManager::isActivated()) { echo "essb-bg-green";} else { echo "essb-bg-red"; } ?>">
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
				
				<div class="essb-welcome-button-container" style="margin-top: 2%;">
					<a href="<?php echo admin_url('admin.php?page=essb_options');?>" class="essb-btn essb-btn-blue" style="margin-right: 10px;margin-top: 10px;">&larr; Back To Plugin Settings <i class="fa fa-cog"></i></a>
					
					<?php if (!ESSBActivationManager::isActivated()) { ?>
					<a href="<?php echo admin_url('admin.php?page=essb_redirect_update');?>" class="essb-btn essb-btn-green" style="margin-right: 10px;margin-top: 10px;">Activate Plugin License <i class="fa fa-key"></i></a>
					<?php } else { ?>
					<a href="https://support.creoworx.com/forums/forum/wordpress-plugins/easy-social-share-buttons/" target="_blank" class="essb-btn essb-btn-green" style="margin-right: 10px;margin-top: 10px;">Visit Support Board <i class="fa fa-key"></i></a>
					<?php } ?>
					</div>
			</div>
			<div class="right-col" style="text-align: center;">
				<img src="<?php echo ESSB3_PLUGIN_URL;?>/assets/images/support.svg" style="padding: 3%;" />
			</div>	
		</div>
		
		<div class="faq essb-options-container essb-dash-widget essb-dash-shadow">
<h3>FAQ</h3>

  		<?php
  		echo ESSBOptionsFramework::draw_panel_start('How to activate plugin?', '', 'fa32 fa fa-question', array('mode' =>'toggle', 'state' => 'closed'));
  		?>
  		<p>Activation of plugin will give you access to <a href="https://socialsharingplugin.com/direct-customer-benefits/" target="_blank">direct customer benefits</a> including automatic updates, access to extensions libarary, access to ready made styles and many more.</p>
  		<p>To activate plugin visit <a href="<?php echo admin_url('admin.php?page=essb_redirect_update&tab=update');?>">Activation screen</a> and fill your purchase code. You can learn how to find your purchase code <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">here</a>. A step by step tutorail you can find inside our knowledge base for <a href="https://docs.socialsharingplugin.com/activate-easy-social-share-buttons/" target="_blank"><b>How to activate Easy Social Share Buttons</b></a>.</p>
  		<p>Only versions of plugin that are purchased directly can be activated. All versions that are bundled inside theme will show a theme activated message but you cannot get access to direct customer benefits unless you purchase a direct plugin copy from us.</p>
  		<?php
  		echo ESSBOptionsFramework::draw_panel_end();
  		?>

  		<?php
  		echo ESSBOptionsFramework::draw_panel_start('How to activate/deactivate Ready Made Styles?', '', 'fa32 fa fa-question', array('mode' =>'toggle', 'state' => 'closed'));
  		?>
  		<p>Ready made styles allow to load preset configuration of plugin to a selected from you location. Activation of ready made styles will set personalized location based settings and since then global settings will not affect this location. <a href="https://docs.socialsharingplugin.com/activatedeactivate-ready-made-styles-easy-social-share-buttons/" target="_blank">Read here how to Activate/Deactivate ready made styles</a> on your site.
  		<?php
  		echo ESSBOptionsFramework::draw_panel_end();
  		?>

  		<?php
  		echo ESSBOptionsFramework::draw_panel_start('How to activate debug mode of WordPress?', '', 'fa32 fa fa-question', array('mode' =>'toggle', 'state' => 'closed'));
  		?>
  		<p>If for some reason you get a white screen on your site to find the cause you need to activate debug mode of WordPress. Debug mode will change that white screen to a message we can understand and navigate you to solve the problem. To activate mode <a href="https://docs.socialsharingplugin.com/activate-debug-mode-wordpress/" target="_blank"> follow this steps in our knowledge base</a>.</p>
  		<?php
  		echo ESSBOptionsFramework::draw_panel_end();
  		?>

  				<?php
  		echo ESSBOptionsFramework::draw_panel_start('How to make manual plugin update?', '', 'fa32 fa fa-question', array('mode' =>'toggle', 'state' => 'closed'));
  		?>
  		<p>If you need to manually update plugin here are the steps you need to complete:</p>.
  		<ol>
  			<li>Go to your Plugins screen and find current version of Easy Social Share Buttons for WordPress</li>
  			<li>Deactivate and then Delete the existing installation</li>
  			<li>Install the new version using WordPress plugin installer or via FTP</li>
  		</ol>
  		<p>All your settings will be saved and you will not loose them during manual plugin update</p>
  		<?php
  		echo ESSBOptionsFramework::draw_panel_end();
  		?>

  		<?php
  		echo ESSBOptionsFramework::draw_panel_start('How to deactivate additional social share optimization tags when Yoast SEO plugin is used?', '', 'fa32 fa fa-question', array('mode' =>'toggle', 'state' => 'closed'));
  		?>
  		<p>To optimize and control shared information over social networks we generate and set on site social share optimization tags. It is very important to have only one instance of those tags on your site - having more than one may cause a problem in sharing or loosing control over custom share data you set. If you use Yoast SEO plugin on your site and come in such situation <a href="https://docs.socialsharingplugin.com/disable-yoast-seo-social-share-optimization-tags-generation/" target="_blank"> read here is what you need to do</a>.</p>
  		<?php
  		echo ESSBOptionsFramework::draw_panel_end();
  		?>

  		<?php
  		echo ESSBOptionsFramework::draw_panel_start('How to deactivate frontend setup module?', '', 'fa32 fa fa-question', array('mode' =>'toggle', 'state' => 'closed'));
  		?>
  		<p>Easy Social Share Buttons for WordPress comes with active by default frontend assistant (Customize button on each instance of buttons and the blue icon at the bottom right corner). That assistant is visible for admin users only but if you wish to deactivate it please <a href="https://docs.socialsharingplugin.com/deactivate-frontend-assistant/" target="_blank"> read here</a>.</p>
  		<?php
  		echo ESSBOptionsFramework::draw_panel_end();
  		?>

  		<?php
  		echo ESSBOptionsFramework::draw_panel_start('How to activate share recovery when moved from http to https?', '', 'fa32 fa fa-question', array('mode' =>'toggle', 'state' => 'closed'));
  		?>
  		<p>If you recently move your site from http to https you may see a sudden counter removal. That is caused because social networks count all shares using URL as unique key and switching protocol is also changing that. To restore back your shares here are the steps you need to do:</p>
  		<ol>
  			<li>Ensure that your counter update mode is not set to Real time. To do this go to Social Sharing -> Share Counter Setup and on the update counter field you should see anything different than real time.</li>
  			<li>Inside Social Sharing -> Share Counter Setup activate share counter recovery and set a cause of change to be Change of protocol from http to https</li>
  			<li>If you expereince problem with Facebook counter please try the all 3 different API update points by changing one by one from Social Sharing -> Share Counter Setup -> Single Button Counter</li>
  		</ol>
  		<?php
  		echo ESSBOptionsFramework::draw_panel_end();
  		?>

  		<?php
  		echo ESSBOptionsFramework::draw_panel_start('How to activate fake share counters?', '', 'fa32 fa fa-question', array('mode' =>'toggle', 'state' => 'closed'));
  		?>
  		<p>Fake share counters (internal counters) can be easy activated with a setting inside plugin. Once switched ON all social network counters will start track shares internally (by click over share button). To do this visit Plugin Functions menu and under developer tools activate Fake (internal) share counters. Once that is done you will be able to control share counter value on each post and you will also see a new menu Developer Tools which you can use to setup minimal fake counter value for all posts.</p>
  		<p>Change of generated share counters is also available via hooks and filters. If you are developer you can <a href="https://docs.socialsharingplugin.com/create-dummy-fake-share-counters/" target="_blank">read here how to do it</a>.</p>
  		<?php
  		echo ESSBOptionsFramework::draw_panel_end();
  		?>
		
		</div>
	</div>
	<!-- activate -->
	<div class="panel panel-activate<?php echo ($active_tab == 'activate' ? ' active' : ''); ?>">
		<div class="left-col" style="padding-top: 1%;">
			<div class="essb-activation-form">
				<div class="essb-activation-form-title">
					<div class="essb-activation-title<?php if (ESSBActivationManager::isActivated()) { echo " color-activated"; } else { echo " color-notactivated"; } ?>"><?php echo __('Plugin Activation', 'essb');?></div>
					<div class="essb-activation-state<?php if (ESSBActivationManager::isActivated()) { echo " background-activated"; } else { echo " background-notactivated"; } ?>">
						<i class="fa fa-<?php if (ESSBActivationManager::isActivated() || ESSBActivationManager::isThemeIntegrated()) { echo "check"; } else { echo "ban"; } ?>"></i> <?php if (ESSBActivationManager::isActivated()) { echo __('Activated', 'essb'); } else { 
							if (ESSBActivationManager::isThemeIntegrated()) {
								echo __('Theme Integrated', 'essb');
							}
							else {
								echo __('Not activated', 'essb'); 
							}
						} ?>			
					</div>
				</div>
				
				<?php if (!ESSBActivationManager::isActivated() && !ESSBActivationManager::isThemeIntegrated() && ESSBActivationManager::isDevelopment()):?>
					<div class="essb-activate-localhost" style="margin: 2% 0; padding: 3%; background: #f3f4f5;">
						<?php _e('You are running plugin on development environment. Activation in this case is optional and it will allow you to use locked plugin features without reflecting activation on your real site.', 'essb'); ?>
					</div>
				<?php endif; ?>
				
				<div class="essb-activation-form-code">
					<div class="essb-activation-form-header">
						<strong><?php echo __('Purchase code', 'essb');?></strong>
						<br/>You can learn how to find your purchase code <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">here</a>
					
					</div>
					<input type="text" class="essb-purchase-code" id="essb-automatic-purchase-code" value="<?php echo ESSBActivationManager::getPurchaseCode(); ?>" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"/>			
				</div>
			
			
				<div class="essb-activation-buttons">
					<?php if (!ESSBActivationManager::isActivated()) { ?>
						<a href="#" id="essb-activate" class="essb-activation-button essb-activation-button-default essb-activate-plugin"><?php echo __('Register the code', 'essb'); ?></a>
					<?php } ?>
					<?php if (ESSBActivationManager::isActivated()) { ?>
						<a href="#" id="essb-deactivate" class="essb-activation-button essb-activation-button-default essb-deactivate-plugin"><?php echo __('Deregister the code', 'essb'); ?></a>
					<?php } ?>
					<a href="http://go.appscreo.com/activate-essb" target="_blank" id="essb-manager1" class="essb-activation-button essb-activation-button-color2 essb-manage-activation-plugin essb-button-right" style="margin-right: 5px;"><?php echo __('Need help with activation?', 'essb'); ?></a>
				</div>
				<div class="essb-activation-manager" style="margin: 3% 0;">
					<h4>Managing Plugin Activations</h4>
					<p>From the license manage control panel you can check your past code activations, deactivate current plugin activations or manually activate plugin for a domain. The access to activation manager require to fill your Envato username and the purchase code.</p>
					<a href="<?php echo ESSBActivationManager::getApiUrl('manager').'?purchase_code='.ESSBActivationManager::getPurchaseCode();?>" target="_blank" id="essb-manager" class="essb-activation-button essb-activation-button-color1 essb-manage-activation-plugin"><?php echo __('Manage my activations', 'essb'); ?></a>
				</div>
			</div>
			
			<!-- manual activation -->
			<?php if (!ESSBActivationManager::isActivated()): ?>
					<div class="essb-activation-form" style="margin-top:30px;">
			<div class="essb-activation-form-title">
				<div class="essb-activation-title<?php if (ESSBActivationManager::isActivated()) { echo " color-activated"; } else { echo " color-notactivated"; } ?>"><?php echo __('Manual Plugin Activation', 'essb');?></div>			
			</div>
			<div class="essb-activation-form-code">
				If you have problem with automatic plugin registration please <a href="#" id="essb-activate-manual-registration">click here to activate it manually</a>.
			</div>
			
			<div id="essb-manual-registration">
			<div class="essb-activation-form-code">
				<div class="essb-activation-form-header">
					<strong><?php echo __('Purchase code', 'essb');?></strong>
					<br/>You can learn how to find your purchase code <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">here</a>
					
				</div>
				<input type="text" id="essb-manual-purchase-code" class="essb-purchase-code" value="<?php echo ESSBActivationManager::getPurchaseCode(); ?>" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"/>			
			</div>
			<div class="essb-activation-form-code">
				<div class="essb-activation-form-header">
					<strong><?php echo __('Activation code', 'essb');?></strong>
					<br/><a href="<?php echo ESSBActivationManager::getApiUrl('activate_domain'); ?>" target="_blank">Go to our manual activation page and fill in all required details to receive your activation code</a>. In the domain field enter <b><?php echo ESSBActivationManager::domain();?></b>
					
				</div>
				<input type="text" id="essb-manual-activation-code" class="essb-purchase-code" value="" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"/>			
			</div>
			
			<div class="essb-activation-buttons">
				<?php if (!ESSBActivationManager::isActivated()) { ?>
				<a href="#" id="essb-manual-activate" class="essb-activation-button essb-activation-button-default essb-manual-activate-plugin"><?php echo __('Manual registration of code', 'essb'); ?></a>
				<?php } ?>
				
				
			</div>
			</div>
		</div>
			
			<?php endif; ?>
		</div>
		<div class="right-col">
				<a href="<?php echo admin_url('admin.php?page=essb_options');?>" class="essb-btn essb-btn-blue2" style="margin-right: 10px;">Return to Plugin Settings<i class="fa fa-cog"></i></a>
			<?php if (!ESSBActivationManager::isActivated()) { ?>
			
				<?php if (ESSBActivationManager::isThemeIntegrated()) { ?>
					<div class="license-desc" style="background: #F04903; color: #fff; padding: 3%; margin: 2% 0; font-size: 15px;">
					
					<h4 style="margin: 0; margin-bottom: 20px; font-size: 18px; letter-spacing: -0.01em;">Theme Integrated License Active</h4>
					
					
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
<p style="font-size: 13px; line-height: 1.3em;">Purchase of Easy Social Share Buttons for WordPress is $20 one time payment without year or month fees and including 6 months of premium support. Each license can be used on one site at same time (you can transfer the license to new site).</p>
					</div>
				<?php } else { ?>
					<div class="license-desc" style="background: #e74c3c; color: #fff; padding: 3%; margin: 2% 0; font-size: 15px;">
					
					<h4 style="margin: 0; margin-bottom: 20px; font-size: 18px; letter-spacing: -0.01em;">Plugin Activation Required</h4>
					
					Activate plugin to unlock the following premium features:
<ul>
<li><i class="fa fa-check"></i> Automatic plugin updates directly inside your WordPress dashboard (no need to wait - get instant updates);</li>
<li><i class="fa fa-check"></i> Access to Extensions Library: Download and install professional extensions to expand functionality of your social sharing plugin (updated regularly).</li>
<li><i class="fa fa-check"></i> Access to Ready Made Demo Configurations - install professional designed layouts with one click</li>
</ul>
					</div>				
				<?php } ?>
			
			<?php } else { ?>
					<div class="license-desc" style="background: #27ae60; color: #fff; padding: 3%; margin: 2% 0; font-size: 15px;">
					
					<h4 style="margin: 0; margin-bottom: 20px; font-size: 18px; letter-spacing: -0.01em;">Your Plugin is Fully Activated</h4>
					
					
In order to register your purchase code on another domain, deregister it first by clicking the button above or get another purchase code. You can also check and manage your activations via Manage my activations button. If you need to use plugin on multiple sites at same time than you need to have a separate license for each active domain.
	<p><a href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo&license=regular&open_purchase_for_item_id=6394476&purchasable=source" target="blank" class="essb-btn essb-btn-white">Purchase Another copy of Easy Social Share Buttons &rarr;</a></p>
<p style="font-size: 13px; line-height: 1.3em;">Purchase of Easy Social Share Buttons for WordPress is $20 one time payment without year or month fees and including 6 months of premium support. Each license can be used on one site at same time (you can transfer the license to new site).</p>
					</div>			
			<?php } ?>
		</div>
	</div>
	
			<div class="footer">
			<h2 style="text-align: center; margin-bottom: 0;">Useful Resources</h2>
			<div class="onethird">
					<div class="essb-feature-icon">
						<i class="ti-book"></i>
					</div>
					<div class="essb-feature-text">
						<b>Knowledge Base</b>
						<span>Read our knowledge base to get know how to use most common functions</span>
						<div style="margin-top:30px;">
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
						<div style="margin-top:30px;">
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
						<div style="margin-top:30px;">
						<a href="https://appscreo.com/?utm_source=about&amp;utm_campaign=panel&amp;utm_medium=button" class="essb-btn essb-btn-blue2" target="_blank">Visit Our Blog &rarr;</a>
						</div>
					</div>
			</div>
			<p class="essb-thank-you" style="text-align: center;">
				Thank you for choosing <b><a href="http://go.appscreo.com/essb" target="_blank">Easy Social Share Buttons for WordPress</a></b>.
				If you like our work please <a href="http://codecanyon.net/downloads" target="_blank">rate Easy Social Share Buttons for WordPress <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></a>
			</p>
		</div>
	
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
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
});
</script>

<!-- plugin activations screen -->
	<script type="text/javascript">

	var essb_api_activate_domain = "<?php echo ESSBActivationManager::domain(); ?>";
	var essb_api_activate_url = "<?php echo ESSBActivationManager::getSiteURL(); ?>";
	var essb_api_url = "<?php echo ESSBActivationManager::getApiUrl('api'); ?>";
	var essb_ajax_url = "<?php echo admin_url ('admin-ajax.php'); ?>";

	var essb_used_purchasecode = "<?php echo ESSBActivationManager::getPurchaseCode(); ?>";
	var essb_used_activationcode = "<?php echo ESSBActivationManager::getActivationCode(); ?>";
	
	jQuery(document).ready(function($){

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

				$('.preloader-holder').fadeIn(100);

				$.ajax({
		            type: "POST",
		            url: essb_ajax_url,
		            data: { 'action': 'essb_process_activation', 'purchase_code': purchase_code, 'activation_code': activation_code, 'activation_state': 'manual', 'domain': essb_api_activate_domain},
		            success: function (data) {
		            	$('.preloader-holder').fadeOut(400);
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

				$('.preloader-holder').fadeIn(100);
				console.log(purchase_code + '-'+essb_api_activate_domain);
				console.log({ 'code': purchase_code, 'domain': essb_api_activate_domain, 'url': essb_api_activate_url});
				console.log(essb_api_url);
				$.ajax({
		            type: "POST",
		            url: essb_api_url,
		            data: { 'code': purchase_code, 'domain': essb_api_activate_domain, 'url': essb_api_activate_url},
		            success: function (data) {
		                $('.preloader-holder').fadeOut(400);
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
		    		            	/*sweetAlert({
		    		            	    title: "Activation Successful",
		    		            	    text: "Thank you for activating Easy Social Share Buttons for WordPress.",
		    		            	    type: "success"
		    		            	},

		    		            	function () {
		    		            	    window.location.reload();
		    		            	});*/

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
		            	 $('.preloader-holder').fadeOut(400);
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

				$('.preloader-holder').fadeIn(100);
				console.log(purchase_code + '-'+essb_api_activate_domain);
				$.ajax({
		            type: "POST",
		            url: essb_api_url + 'deactivate.php',
		            data: { 'hash': essb_used_activationcode, 'code': essb_used_purchasecode },
		            success: function (data) {
		                $('.preloader-holder').fadeOut(400);
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
		            	 $('.preloader-holder').fadeOut(400);
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
