<?php
/**
 * Detect the different possible states of activation
 */
$is_activated = ESSBActivationManager::isActivated();
$is_theme_integrated = !$is_activated && ESSBActivationManager::isThemeIntegrated();
$not_activated = !$is_activated && !$is_theme_integrated;

$activated_color_class = $is_activated ? 'color-activated' : 'color-notactivated';
$activated_background_class = $is_activated ? 'background-activated' : 'background-notactivated';

$activation_icon = $is_activated || $is_theme_integrated ? 'check' : 'ban';

$show_development_message = !$is_activated && !$is_theme_integrated && ESSBActivationManager::isDevelopment();
?>

<?php include_once(ESSB3_PLUGIN_ROOT.'lib/admin/helpers/about-page-header.php'); ?>

<div class="panel panel-activate active">
	<div class="left-col">
		<?php 
		if ($is_theme_integrated) {
		?>
		
		<div class="about-page-panel theme-integrated">		
			<div class="panel-content">
				<h4>Theme Integrated License Active</h4>
					<p><strong>You are using a theme integrated version of Easy Social Share Buttons for WordPress. The bundled inside theme versions do not require activation with a purchase code to work.</strong> The bundled inside theme versions do not have access to direct customer benefits - they can be unlocked with a purchase of a direct plugin license.</p>
                    <ul>
                        <li><i class="fa fa-check"></i> Access official customer support (opening support tickets are available only for direct license owners);</li>
                        <li><i class="fa fa-check"></i> Automatic plugin updates directly inside your WordPress dashboard (no need to wait - get instant updates);</li>
                        <li><i class="fa fa-check"></i> Access to free plugin extensions. You won't be able to download and activate the free plugin extensions from the library;</li>
                        <li><i class="fa fa-check"></i> Access to Styles' library. You won't be able to use the ready-made designs or the styles' library features;</li>
                        <li><i class="fa fa-check"></i> Use with any WordPress theme. The bundled version is licensed to use only with the theme you purchased. Switching the theme means that you can't use the plugin anymore;</li>
                        <li><i class="fa fa-check"></i> Access to multilingual translate menu for integration with WPML and Polylang;</li>
                    </ul>
                    <p>
                    	<a href="http://go.appscreo.com/get-essb-license" target="_blank" class="essb-activation-button essb-activation-button-purchase-theme">Purchase Easy Social Share Buttons for WordPress License</a>
                    </p>
					<p>
					You can activate the plugin only if you own a direct plugin purchase code. You can't activate the plugin with the purchase code of the theme it is bundled inside.
                    </p>
			</div> <!-- panel-content -->		
		</div> <!-- about-page-panel theme-integrated -->
		
		<?php 
		}
		
		if ($not_activated) {
		?>
	
		<div class="about-page-panel not-activated">		
			<div class="panel-content">
				<h4>Plugin Activation Required</h4>
					<p><strong>Activate plugin to unlock the following premium features:</strong></p>
                    <ul>
                        <li><i class="fa fa-check"></i> Access official customer support (opening support tickets are available only for direct license owners);</li>
                        <li><i class="fa fa-check"></i> Automatic plugin updates directly inside your WordPress dashboard (no need to wait - get instant updates);</li>
                        <li><i class="fa fa-check"></i> Access to free plugin extensions. You won't be able to download and activate the free plugin extensions from the library;</li>
                        <li><i class="fa fa-check"></i> Access to Styles' library. You won't be able to use the ready-made designs or the styles' library features;</li>
                        <li><i class="fa fa-check"></i> Access to multilingual translate menu for integration with WPML and Polylang;</li>
                        <li><i class="fa fa-check"></i> Access to custom networks, positions, or design-builders;</li>
                        <li><i class="fa fa-check"></i> Remove usage message visible inside the code only;</li>
                    </ul>
                    <p>
                    	<a href="http://go.appscreo.com/get-essb-license" target="_blank" class="essb-activation-button essb-activation-button-notactivated">Purchase Easy Social Share Buttons for WordPress License</a>
                    </p>
			</div> <!-- panel-content -->		
		</div> <!-- about-page-panel not-activated -->
			
		<?php } ?>
	
		<div class="about-page-panel">		
			<div class="panel-content">
				<div class="essb-activation-form">
    				<div class="essb-activation-form-title">
    					<div class="essb-activation-title <?php echo esc_attr($activated_color_class); ?>"><?php echo esc_html__('Plugin Activation', 'essb');?></div>
    					<div class="essb-activation-state <?php echo esc_attr($activated_background_class); ?>">
    						<i class="fa fa-<?php echo esc_attr($activation_icon); ?>"></i> 
    						<?php 
    						    if (ESSBActivationManager::isActivated()) { echo esc_html__('Activated', 'essb'); } 
    						    else { 
        							if (ESSBActivationManager::isThemeIntegrated()) {
        								echo esc_html__('Theme Integrated', 'essb');
        							}
        							else {
        								echo esc_html__('Not activated', 'essb'); 
        							}
    						    } ?>			
    					</div>
    				</div> <!--  essb-activation-form-title -->
    				
    				<?php 
    				if ($show_development_message) {
    				    echo '<div class="essb-activate-localhost">';
    				    esc_html_e('This is a development website. You can safely activate the plugin without being blocked from activating it on the real website.', 'essb');
    				    echo '</div>';
    				}
    				?>
    				
					<div class="essb-activation-form-code">
    					<div class="essb-activation-form-header">
    						<strong><?php echo esc_html__('Purchase code', 'essb');?></strong>
    						<label class="description">
    							<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><?php esc_html_e('Where is my purchase code?', 'essb'); ?></a>    					    						
    						</label>
    					</div>
    					<?php if ($is_activated) { echo '<div class="mask-activation">'; } ?>
    					<input type="text" class="essb-purchase-code" id="essb-automatic-purchase-code" value="<?php echo ESSBActivationManager::getPurchaseCode(); ?>" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"/>
    					
    					<?php if ($is_activated) { ?>
    					<input type="text" class="essb-masked-purchase-code" disabled value="<?php echo ESSBActivationManager::getMaskedPurchaseCode(); ?>" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"/>
    					<?php echo '</div>'; } ?>			
					</div> <!-- essb-activation-form-code -->
					
					<div class="essb-activation-buttons">
    					<?php if (!ESSBActivationManager::isActivated()) { ?>
    						<a href="#" id="essb-activate" class="essb-activation-button essb-activation-button-default essb-activate-plugin"><?php echo esc_html__('Register the code', 'essb'); ?></a>
    					<?php } ?>
    					<?php if (ESSBActivationManager::isActivated()) { ?>
    						<a href="#" id="essb-deactivate" class="essb-activation-button essb-activation-button-default essb-deactivate-plugin"><?php echo esc_html__('Deregister the code', 'essb'); ?></a>
    					<?php } ?>
    					
    					<a href="http://go.appscreo.com/activate-essb" target="_blank" class="essb-howto-activate"><i class="ti-help-alt"></i><span><?php esc_html_e('How to?', 'essb'); ?></span></a>
    					
						<?php if (!$is_activated) { ?>
						<div class="essb-activation-form-code">
							If you have problem with automatic plugin registration please <a href="#" id="essb-activate-manual-registration">click here to activate it manually</a>.
						</div>    					
						<?php } ?>
					</div> <!-- essb-activation-buttons -->
				</div> <!-- essb-activation-form  -->
			</div>	<!-- panel-content -->
		</div> <!-- about-page-panel -->
		
		<div class="about-page-panel manual-activation">
			<div class="panel-content">
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
			</div> <!-- panel-content -->
		</div> <!-- about-page-panel manual-activation -->
		
		<div class="about-page-panel manage-activations">
			<div class="panel-content">
				<h4>Managing Plugin Activations</h4>
				<p>From the license manage control panel you can check your past code activations, deactivate current plugin activations, or manually activate the plugin for a domain. The access to activation manager requires you to fill in your Envato username and the purchase code.</p>
				<a href="<?php echo esc_url(ESSBActivationManager::getApiUrl('manager').'?purchase_code='.ESSBActivationManager::getPurchaseCode());?>" target="_blank" id="essb-manager" class="essb-activation-button essb-activation-button-color1 essb-manage-activation-plugin"><?php echo esc_html__('Manage my activations', 'essb'); ?></a>
			</div> <!-- panel-content -->
		</div>	<!-- about-page-panel manage-activations -->	
	</div> <!-- left-col -->
</div> <!-- panel-activate -->


	<script type="text/javascript">

	var essb_api_activate_domain = "<?php echo esc_attr(ESSBActivationManager::domain()); ?>";
	var essb_api_activate_url = "<?php echo esc_url(ESSBActivationManager::getSiteURL()); ?>";
	var essb_api_url = "<?php echo esc_url(ESSBActivationManager::getApiUrl('api')); ?>";
	var essb_ajax_url = "<?php echo esc_url(admin_url ('admin-ajax.php')); ?>";

	var essb_used_purchasecode = "<?php echo esc_url(ESSBActivationManager::getPurchaseCode()); ?>";
	var essb_used_activationcode = "<?php echo esc_url(ESSBActivationManager::getActivationCode()); ?>";
	
	jQuery(document).ready(function($){
		"use strict";
		
		if ($('#essb-activate-manual-registration').length) {
			$('#essb-activate-manual-registration').click(function(e) {
				e.preventDefault();

				if (!$('#essb-activate-manual-registration').hasClass('opened')) {
					$('#essb-manual-registration').fadeIn('200');
					$('.about-page-panel.manual-activation').fadeIn(200);
					$('#essb-activate-manual-registration').addClass('opened');
				}
				else {
					$('#essb-manual-registration').fadeOut('200');
					$('.about-page-panel.manual-activation').fadeOut(200);
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
