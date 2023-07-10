<?php include_once(ESSB3_PLUGIN_ROOT.'lib/admin/helpers/about-page-header.php'); ?>

<?php 

$is_activated = ESSBActivationManager::isActivated();
$heading = $is_activated ? esc_html__('Premium features are activated', 'essb') : esc_html__('Activate plugin to unlock the premium features', 'essb');
$activated_class = $is_activated ? 'activated' : 'not-activated';

?>

<div class="panels">
	<div class="panel panel-about active">
		<div class="left-col">
			<h2><?php echo sprintf( esc_html__( 'Welcome to Easy Social Share Buttons for WordPress %s', 'essb' ), preg_replace( '/^(\d+)(\.\d+)?(\.\d)?/', '$1$2', ESSB3_VERSION ) ) ?></h1>

			<div class="about-text">
				<?php esc_html_e( 'Thank you for choosing the best WordPress social media plugin. We hope you enjoy it!', 'essb' )?>
			</div>
			
			<div class="essb-welcome-button-container">
				<a href="https://codecanyon.net/downloads" target="_blank" class="essb-btn essb-btn-orange">Rate us <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></a>
				<a href="https://socialsharingplugin.com/version-changes/" target="_blank" class="essb-btn essb-btn-green">What's New In Version<i class="fa fa-bullhorn"></i></a>
				<a href="https://socialsharingplugin.com/getting-started/" target="_blank" class="essb-btn essb-btn-purple">Getting Started<i class="fa fa-play"></i></a>
			</div>
			
			<div class="essb-welcome-button-container">
						<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/6WvGcToLmQM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			
		</div> <!-- left-col -->
		<div class="right-col">
			<img class="essb-right-logo" src="<?php echo ESSB3_PLUGIN_URL;?>/assets/images/welcome-svg.svg" />
		</div> <!-- right-col -->
		
		<div class="about-page-panel" style="display: none;">
			<div class="panel-content">
				<h4>Get Notications in Your Inbox</h4>
				<p>
				Join the newsletter to receive emails when we release plugin or theme updates, send out free resources, announce promotions and more!
				</p>
				<div>
				<form action="//appscreo.us13.list-manage.com/subscribe/post?u=a1d01670c240536f6a70e7778&amp;id=c896311986" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<input type="email" name="EMAIL" id="mce-EMAIL" autocomplete="off" placeholder="Enter your email" style="width: 90%; border-radius: 3px; padding: 10px; display: block; margin: 0 auto; margin-bottom: 10px;" />
						<input type="submit" name="subscribe" id="mc-embedded-subscribe" class="essb-btn essb-btn-blue2" value="Subscribe" style="box-shadow: none;">
				</form>
				</div>			
			</div>
		</div>
				
		<!-- premium features -->
		<div class="about-page-panel <?php echo esc_attr($activated_class); ?>">		
			<div class="panel-content">
				<h4><?php echo $heading; ?></h4>
                    <ul>
                        <li><i class="fa fa-check"></i> Access official customer support (opening support tickets are available only for direct license owners);</li>
                        <li><i class="fa fa-check"></i> Automatic plugin updates directly inside your WordPress dashboard (no need to wait - get instant updates);</li>
                        <li><i class="fa fa-check"></i> Access to free plugin extensions. You won't be able to download and activate the free plugin extensions from the library;</li>
                        <li><i class="fa fa-check"></i> Access to Styles' library. You won't be able to use the ready-made designs or the styles' library features;</li>
                        <li><i class="fa fa-check"></i> Access to multilingual translate menu for integration with WPML and Polylang;</li>
                        <li><i class="fa fa-check"></i> Access to custom networks, positions, or design-builders;</li>
                        <li><i class="fa fa-check"></i> Remove usage message visible inside the code only;</li>
                    </ul>
                    <?php if (!$is_activated) { ?>
                    <p>
                    	<a href="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_update'));?>" class="essb-activation-button essb-activation-button-notactivated">Activate</a>
                    </p>
                    <?php } ?>
			</div> <!-- panel-content -->		
		</div> <!-- about-page-panel not-activated -->
		
		<!-- premium features -->
		<div class="about-page-panel getting-support">		
			<div class="panel-content">
				<div class="left-col">
				<h4>Getting Support</h4>
                <p>We understand all the importance of product support for our customers. That's why we are ready to solve all your issues and answer any questions related to our plugin.</p>
				
				<p>
				<h5>Before Submitting Your Ticket, Please Make Sure That:</h5>
				<ul>
					<li><i class="fa fa-check-circle-o essb-c-green" aria-hidden="true"></i> You are running the latest plugin version. <a href="https://socialsharingplugin.com/version-changes" target="_blank">Check which is the latest version &rarr;</a></li>
					<li><i class="fa fa-check-circle-o essb-c-green" aria-hidden="true"></i> Ensure that there are no errors on site. <a href="https://docs.socialsharingplugin.com/knowledgebase/how-to-activate-debug-mode-in-wordpress/" target="_blank">Activating WordPress Debug Mode &rarr;</a></li>
					<li><i class="fa fa-check-circle-o essb-c-green" aria-hidden="true"></i> Browse the knowledge base. <a href="https://docs.socialsharingplugin.com" target="_blank">Open Knowledge Base &rarr;</a></li>
				</ul>
				</p>
				
				<p>
				<h5>Item Support Includes:</h5>
				<ul>
					<li><i class="fa fa-check essb-c-green" aria-hidden="true"></i> Availability of the author to answer questions</li>
					<li><i class="fa fa-check essb-c-green" aria-hidden="true"></i> Answering technical questions about item's features</li>
					<li><i class="fa fa-check essb-c-green" aria-hidden="true"></i> Assistance with reported bugs and issues</li>
					<li><i class="fa fa-check essb-c-green" aria-hidden="true"></i> Lifetime plugin update</li>
					<li><i class="fa fa-check essb-c-green" aria-hidden="true"></i> Assistance with initial setup and configuration</li>
					
					</ul>
				<h5>Item Support Does Not Include:</h5>
				<ul>
					<li><i class="fa fa-times" aria-hidden="true"></i> Customization services</li>
					<li><i class="fa fa-times" aria-hidden="true"></i> Installation services</li>
					</ul>
				</p>
				<p>
				<?php if ($is_activated) { ?>
					<a href="https://support.creoworx.com/forums/forum/wordpress-plugins/easy-social-share-buttons/" target="_blank" class="essb-btn essb-btn-green essb-back-to-settings1 open-support-topic">Submit a New Support Topic<i class="fa fa-external-link"></i></a>
				<?php } else { ?>
				<a href="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_update')); ?>" class="essb-btn <?php if (ESSBActivationManager::isActivated()) { echo "essb-bg-green";} else { echo "essb-bg-red"; } ?>">
					<i class="fa <?php if (ESSBActivationManager::isActivated()) { echo "fa-check";} else { echo "fa-ban"; } ?>"></i>
					<?php if (ESSBActivationManager::isActivated()) { echo "Activated";} else { echo "Activate Plugin to Unlock"; } ?>
				</a>
				<?php } ?>
				</p>
				</div> <!-- left-col -->
				<div class="right-col essb-align-center">
					<img class="support-image" src="<?php echo ESSB3_PLUGIN_URL;?>/assets/images/support.svg" />
				</div> <!-- right-col -->	
			</div> <!-- panel-content -->		
		</div> <!-- about-page-panel not-activated -->
		
	</div> <!-- panel-about -->

</div>