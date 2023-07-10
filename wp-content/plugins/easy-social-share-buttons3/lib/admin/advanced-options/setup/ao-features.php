<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

function ao_generate_feature_block($title = '', $desc = '', $icon = '', $field = '', $deactivate_mode = false) {

	$state = '';
	$field_value = essb_option_bool_value($field);
	$deactivation_tag = $deactivate_mode ? 'deactivation' : '';
	$value = '';

	if ($deactivate_mode) {
		if (!$field_value) {
			$state = 'active';
			$value = '';
		}
		else {
			$state = '';
			$value = 'true';
		}
	}
	else {
		if (!$field_value) {
			$state = '';
			$value = '';
		}
		else {
			$state = 'active';
			$value = 'true';
		}
	}

	?>
	<div class="single-feature <?php echo esc_attr($state); ?>" data-type="<?php echo esc_attr($deactivation_tag); ?>">
		<input type="hidden" name="essb_options[<?php echo esc_attr($field); ?>]" id="essb_<?php echo esc_attr($field); ?>" class="feature-value" value="<?php echo esc_attr($value); ?>" />
		<div class="header"><span class="tag tag-active">Active</span><span class="tag tag-notactive">Not Active</span></div>
		<i class="feature-icon <?php echo esc_attr($icon); ?>"></i>
		<h3><?php echo $title; ?></h3>
		<div class="desc"><?php echo $desc; ?></div>
		<div class="buttons">
			<a href="#" class="activate-btn feature-btn essb-btn"><i class="fa fa-check"></i>Activate</a><a href="#" class="deactivate-btn feature-btn essb-btn"><i class="fa fa-close"></i>Deactivate</a>
		</div>
	</div>
	<?php
}

?>
<div class="features-container">
	<div class="navigation">
		<a href="#" data-tab="sharing">Share Features <span class="small-tag">5/10</span></a>
		<a href="#" data-tab="display">Share Display Methods <span class="small-tag">2/12</span></a>
		<a href="#" data-tab="social">Social Features <span class="small-tag">2/12</span></a>
		<a href="#" data-tab="advanced">Advanced Features <span class="small-tag">2/12</span></a>
		<a href="#" data-tab="addons">Add-Ons</a>
		<a href="https://docs.socialsharingplugin.com/knowledgebase/how-to-enable-or-disable-additional-plugin-features-using-the-activate-deactivate-features-function/" target="_blank" class="help-link"><i class="ti-help-alt"></i>Need Help?</a>			
	</div>
	<div class="content">
		<div class="content-tab tab-sharing">
		<div class="features-deactivate">
		<?php 
		ao_generate_feature_block(esc_html__('Avoid Negative Social Proof', 'essb'), esc_html__('Hide social share counters before they reach a specific number of shares', 'essb'), 'ti-face-smile', 'deactivate_ansp', true);
		ao_generate_feature_block(esc_html__('Social Shares Recovery', 'essb'), esc_html__('Recover number of shares from specific URL changes', 'essb'), 'ti-reload', 'deactivate_ssr', true);
		ao_generate_feature_block(esc_html__('Fake Share Counters', 'essb'), esc_html__('Fake share counters allow you to increase the numbers with a multiplier. As an addition, you can also change the values to internal counters for all networks.', 'essb'), 'ti-loop', 'deactivate_fakecounters', true);
		ao_generate_feature_block(esc_html__('Expert Share Counter Options', 'essb'), esc_html__('Include expert level share counter update options for fine-tuning of the values and the update process.', 'essb'), 'ti-target', 'deactivate_expertcounters', true);
		ao_generate_feature_block(esc_html__('Sharable Quotes', 'essb'), esc_html__('Add click to Tweet quotes inside content with shortcode', 'essb'), 'ti-twitter', 'deactivate_ctt', true);
		ao_generate_feature_block(esc_html__('After Share Events', 'essb'), esc_html__('Show additional actions to user after sharing content', 'essb'), 'ti-share', 'deactivate_module_aftershare', true);
		ao_generate_feature_block(esc_html__('Share Optimizations', 'essb'), esc_html__('Add social share optimization tags for easy tune of the shared information', 'essb'), 'ti-receipt', 'deactivate_module_shareoptimize', true);
		ao_generate_feature_block(esc_html__('Plugin Analytics', 'essb'), esc_html__('Log share button clicks and generate report dashboard', 'essb'), 'ti-stats-up', 'deactivate_module_analytics', true);
		ao_generate_feature_block(esc_html__('Google Analytics Tracking', 'essb'), esc_html__('Generate UTM tracking code to the outgoing shared URLs or track events of sharing in Google Analytics.', 'essb'), 'ti-google', 'deactivate_module_google_analytics', true);
		ao_generate_feature_block(esc_html__('Pinterest Pro', 'essb'), esc_html__('Automatically add Pin button over images in content, include Pinterest sharing images or galleries', 'essb'), 'ti-pinterest', 'deactivate_module_pinterestpro', true);
		ao_generate_feature_block(esc_html__('Short URL', 'essb'), esc_html__('Generate short URLs for sharing on social networks', 'essb'), 'ti-new-window', 'deactivate_module_shorturl', true);
		ao_generate_feature_block(esc_html__('Affiliate & Point Integration', 'essb'), esc_html__('Integrate plugin work with myCred, AffiliateWP, SliceWP', 'essb'), 'ti-money', 'deactivate_module_affiliate', true);
		ao_generate_feature_block(esc_html__('Custom Share', 'essb'), esc_html__('Custom share feature makes possible to change the share URL that plugin will use', 'essb'), 'ti-share-alt', 'deactivate_module_customshare', true);
		ao_generate_feature_block(esc_html__('Message Before Buttons', 'essb'), esc_html__('Add a custom message before or above share buttons "ex: Share this"', 'essb'), 'fa fa-comment', 'deactivate_module_message', true);
		ao_generate_feature_block(esc_html__('Social Metrics Lite', 'essb'), esc_html__('Log the official share values into a dashboard to see the most popular posts', 'essb'), 'ti-dashboard', 'deactivate_module_metrics', true);
		ao_generate_feature_block(esc_html__('Style Library', 'essb'), esc_html__('Save and reuse again already configured styles and network list. Saved in the library you can also move the style to a new site. Try also one of 40+ already configured styles if you wonder how to start.', 'essb'), 'ti-paint-roller', 'deactivate_stylelibrary', true);
		
		?>	
		</div>
		</div>
		<div class="content-tab tab-display">
		<div class="features-deactivate">
		<?php 
		
		ao_generate_feature_block(esc_html__('Float From Above The Content', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_float', true);
		ao_generate_feature_block(esc_html__('Post Vertical Float', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_postfloat', true);
		ao_generate_feature_block(esc_html__('Sidebar', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_sidebar', true);
		ao_generate_feature_block(esc_html__('Top Bar', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_topbar', true);
		ao_generate_feature_block(esc_html__('Bottom Bar', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_bottombar', true);
		ao_generate_feature_block(esc_html__('Pop Up', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_popup', true);
		ao_generate_feature_block(esc_html__('Fly In', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_flyin', true);
		ao_generate_feature_block(esc_html__('Hero Share', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_heroshare', true);
		ao_generate_feature_block(esc_html__('Post Bar', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_postbar', true);
		ao_generate_feature_block(esc_html__('Point', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_point', true);
		ao_generate_feature_block(esc_html__('On Media', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_image', true);
		ao_generate_feature_block(esc_html__('Native Buttons', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_native', true);
		ao_generate_feature_block(esc_html__('Follow Me Bar', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_followme', true);
		ao_generate_feature_block(esc_html__('Corner Share', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_corner', true);		
		ao_generate_feature_block(esc_html__('Share Booster', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_booster', true);
		ao_generate_feature_block(esc_html__('Share Button', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_sharebutton', true);
		ao_generate_feature_block(esc_html__('Excerpt', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_except', true);
		ao_generate_feature_block(esc_html__('Widget', 'essb'), '', 'ti-layout-media-center-alt', 'deactivate_method_widget', true);
		ao_generate_feature_block(esc_html__('Advanced Mobile Options', 'essb'), '', 'ti-mobile', 'deactivate_method_advanced_mobile', true);
		//
		
		?>
		</div>		
		</div>
		<div class="content-tab tab-social">
		<div class="features-deactivate">
		<?php 
		ao_generate_feature_block(esc_html__('Social Followers Counter', 'essb'), esc_html__('Show the number of followers for 30+ social networks', 'essb'), 'ti-heart', 'deactivate_module_followers', true);
		ao_generate_feature_block(esc_html__('Social Profile Links', 'essb'), esc_html__('Add plain buttons for your social profiles with shortcode, widget or sidebar', 'essb'), 'ti-id-badge', 'deactivate_module_profiles', true);
		ao_generate_feature_block(esc_html__('Native Social Buttons', 'essb'), esc_html__('Use selected native social buttons along with your share buttons', 'essb'), 'ti-thumb-up', 'deactivate_module_natives', true);
		ao_generate_feature_block(esc_html__('Subscribe Forms', 'essb'), esc_html__('Add easy to use subscribe to mail list forms', 'essb'), 'ti-email', 'deactivate_module_subscribe', true);
		ao_generate_feature_block(esc_html__('Facebook Live Chat', 'essb'), esc_html__('Connect with your visitors using Facebook live chat', 'essb'), 'fa fa-facebook', 'deactivate_module_facebookchat', true);
		ao_generate_feature_block(esc_html__('Skype Live Chat', 'essb'), esc_html__('Connect with your visitors using Skype live chat', 'essb'), 'fa fa-skype', 'deactivate_module_skypechat', true);
		ao_generate_feature_block(esc_html__('Click 2 Chat', 'essb'), esc_html__('Add click to chat feature for WhatsApp and Viber', 'essb'), 'fa fa-comments', 'deactivate_module_clicktochat', true);
		ao_generate_feature_block(esc_html__('Instagram Feed', 'essb'), esc_html__('Enable generation of Instagram feed on site', 'essb'), 'ti-instagram', 'deactivate_module_instagram', true);
		ao_generate_feature_block(esc_html__('Social Proof Notifications Lite', 'essb'), esc_html__('Enable display of share counter social proof notification messages', 'essb'), 'ti-comment-alt', 'deactivate_module_proofnotifications', true);		
		?>
		</div>
		</div>
		<div class="content-tab tab-advanced">
		<div class="features-deactivate">
		<?php 
		ao_generate_feature_block(esc_html__('Functions Translate', 'essb'), esc_html__('Allow to translate preset plugin texts on your language', 'essb'), 'fa fa-language', 'deactivate_module_translate', true);
		ao_generate_feature_block(esc_html__('Custom Network Buttons', 'essb'), esc_html__('Enable the function to add custom network buttons in the sharing or following.', 'essb'), 'ti-layout-cta-center', 'deactivate_custombuttons', true);
		ao_generate_feature_block(esc_html__('Custom Display/Positions', 'essb'), esc_html__('The custom display/positions makes possible to create a custom position inside plugin. This position you can show with shortcode of functional call anywhere on site.', 'essb'), 'ti-layout-media-center-alt', 'deactivate_custompositions', true);
		ao_generate_feature_block(esc_html__('Conversion Tracking', 'essb'), esc_html__('Enable the tracking of share or subscribe conversions', 'essb'), 'ti-dashboard', 'deactivate_module_conversions', true);
		ao_generate_feature_block(esc_html__('Automatic Mobile Setup', 'essb'), esc_html__('Activate automatic responsive mobile setup of share buttons', 'essb'), 'ti-mobile', 'activate_mobile_auto');
		ao_generate_feature_block(esc_html__('WooCommerce', 'essb'), esc_html__('WooCommerce specific locations to show share buttons.', 'essb'), 'ti-shopping-cart', 'deactivate_method_woocommerce', true);
		ao_generate_feature_block(esc_html__('Integrations With Plugins', 'essb'), esc_html__('Additional integrations available with BuddyPress, bbPress and etc.', 'essb'), 'fa fa-plug', 'deactivate_method_integrations', true);
		ao_generate_feature_block(esc_html__('Settings by Post Type', 'essb'), esc_html__('Allow additional settings for different post types', 'essb'), 'ti-pencil-alt', 'deactivate_settings_post_type', true);
		
		ao_generate_feature_block(esc_html__('Internal Share Counters', 'essb'), esc_html__('The internal share counter option allows to change the generated counters on site and track them internally for all networks.', 'essb'), 'fa fa-retweet', 'activate_fake');
		ao_generate_feature_block(esc_html__('Hooks Integration', 'essb'), esc_html__('Easy assign share buttons to theme or plugin actions/filters. You can also use it to create a custom display methods.', 'essb'), 'fa fa-cog', 'activate_hooks');
		ao_generate_feature_block(esc_html__('Minimal Share Counters', 'essb'), esc_html__('Set a minimal share value that will be shown till the official value become greater', 'essb'), 'fa fa-sort-numeric-desc', 'activate_minimal');
		?>
		</div>
		</div>
		
		<div class="content-tab tab-addons">
		<?php 
		if (!class_exists ( 'ESSBAddonsHelper' )) {
		    include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/addons/essb-addons-helper4.php');
		}
		
		if ( ! function_exists( 'get_plugins' ) ) {
		    require_once wp_normalize_path( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		
        $current_addon_list = ESSBAddonsHelper::get_instance ()->get_addons ();
        $current_plugin_list = essb_get_site_plugins();
        
        foreach ($current_addon_list as $key => $data) {
            $price = isset($data['price']) ? $data['price'] : '';
            $is_free = ($price == 'free' || $price == 'Free' || $price == 'FREE');
            
            if ($is_free) {
                $url_install = wp_nonce_url(
                    add_query_arg(
                        array(
                            'plugin'           => urlencode( $key ),
                            'essb-tgmpa-install' => 'install-plugin',
                        ),
                        admin_url('admin.php?page=essb_redirect_addons')
                        ),
                    'essb-tgmpa-install',
                    'essb-tgmpa-nonce'
                    );
                
                
                $url_command = $url_install;
                $command_text = 'Install';
                $command_class = 'button-primary';
                
                if (isset($current_plugin_list[$key])) {
                    $addon_slug = $current_plugin_list[$key]['path'];
                    $url_activate = wp_nonce_url( "plugins.php?action=activate&plugin={$addon_slug}", "activate-plugin_{$addon_slug}" );
                    $url_deactivate = wp_nonce_url( "plugins.php?action=deactivate&plugin={$addon_slug}", "deactivate-plugin_{$addon_slug}" );
                    
                    $url_command = $current_plugin_list[$key]['active'] ? $url_deactivate : $url_activate;
                    $command_text = $current_plugin_list[$key]['active'] ? 'Deactivate' : 'Activate';
                    $command_class = $current_plugin_list[$key]['active'] ? 'button-deactivate' : 'button-activate';
                }		    
                
                
                echo '<div class="features-addon">';
                echo '<div class="features-addon-image"><img src="'.esc_url(ESSB3_PLUGIN_URL .'/assets/images/'.$data['icon'].'.svg' ).'"/></div>';
                echo '<div class="features-addon-data">';
                echo '<div class="details">';
                echo '<div class="title">'.$data['name'].'</div>';
                echo '<div class="desc">'.$data['description'].'</div>';	
                if (!ESSBActivationManager::isActivated()) {
                    echo '<span class="not-activated">'.ESSBAdminActivate::activateToUnlock(esc_html__('Activate plugin to download', 'essb')).'</span>';
                }
                echo '</div>'; // details
                if (ESSBActivationManager::isActivated()) {
                    echo '<div class="commands">';
                    echo '<a class="button '.esc_attr($command_class).'" href="'.esc_url($url_command).'">' . $command_text . '</a>';
                    echo '</div>';                
                }
                echo '</div>'; // features-addon-data
                echo '</div>';
            }
        }
		
		?>
		</div>
	</div>

</div>


<div class="features-deactivate">
	<?php
	
	
	

	?>
</div>
