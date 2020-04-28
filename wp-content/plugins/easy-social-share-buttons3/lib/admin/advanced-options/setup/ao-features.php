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
	<div class="single-feature <?php echo $state; ?>" data-type="<?php echo $deactivation_tag; ?>">
		<input type="hidden" name="essb_options[<?php echo $field; ?>]" id="essb_<?php echo $field; ?>" class="feature-value" value="<?php echo $value; ?>" />
		<div class="header"><span class="tag tag-active">Active</span><span class="tag tag-notactive">Not Active</span></div>
		<i class="feature-icon <?php echo $icon; ?>"></i>
		<h3><?php echo $title; ?></h3>
		<div class="desc"><?php echo $desc; ?></div>
		<div class="buttons">
			<a href="#" class="activate-btn feature-btn essb-btn"><i class="fa fa-check"></i>Activate</a><a href="#" class="deactivate-btn feature-btn essb-btn"><i class="fa fa-close"></i>Deactivate</a>
		</div>
	</div>
	<?php
}

?>
<style type="text/css">
	.features-deactivate { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; grid-gap: 15px; }
	.features-deactivate .single-feature { color: #777; padding: 10px; border-radius: 5px; text-align: center; }
	.features-deactivate .single-feature.active { background: #5867dd; color: #fff; }
	.features-deactivate .single-feature.active i.feature-icon, .features-deactivate .single-feature.active h3 { color: #fff; }
	.features-deactivate .single-feature i.feature-icon { font-size: 48px; display: block; margin-bottom: 10px; }
	.features-deactivate .single-feature h3 {
		margin: 0;
		font-size: 15px;
		margin-bottom: 5px;
	}

	.features-deactivate .header { margin-bottom: 15px; }

	.features-deactivate .single-feature .desc { font-size: 12px; }
	.features-deactivate .tag {
    	font-size: 11px;
    	text-transform: uppercase;
    	padding: 2px 5px;
    	border-radius: 3px;
    	font-weight: 600;
		background: #888;
		color: #fff;
	}

	.features-deactivate .active .tag {
		background: #fff;
    	color: #5867dd;
	}

	.features-deactivate .buttons {
		margin-top: 15px;
	}

	.features-deactivate .buttons .essb-btn { padding: 8px 16px; }
	.features-deactivate .buttons .deactivate-btn { display: none; }
	.features-deactivate .active .buttons .activate-btn { display: none; }
	.features-deactivate .active .buttons .deactivate-btn { display: inline-block; }
	.features-deactivate .buttons .feature-btn i { margin-right: 5px; }

	.features-deactivate .single-feature .tag-active { display: none; }
	.features-deactivate .single-feature.active .tag-active { display: inline; }
	.features-deactivate .single-feature.active .tag-notactive { display: none; }

	.features-deactivate .active .buttons .deactivate-btn { background: #fff; color: #5867dd !important; }
</style>

<script type="text/javascript">
jQuery(document).ready(function($){
	$('.features-deactivate .single-feature .activate-btn').click(function(e) {
		e.preventDefault();
		var rootElement = $(this).parent().parent(),
			rootType = $(rootElement).data('type') || '';

		$(rootElement).addClass('active');

		if (rootType == 'deactivation') $(rootElement).find('.feature-value').val('');
		else $(rootElement).find('.feature-value').val('true');
	});

	$('.features-deactivate .single-feature .deactivate-btn').click(function(e) {
		e.preventDefault();

		e.preventDefault();
		var rootElement = $(this).parent().parent(),
			rootType = $(rootElement).data('type') || '';

		$(rootElement).removeClass('active');

		if (rootType == 'deactivation') $(rootElement).find('.feature-value').val('true');
		else $(rootElement).find('.feature-value').val('');
	});
});
</script>

<div class="features-deactivate">
	<?php
	ao_generate_feature_block(__('Sharable Quotes', 'essb'), __('Add click to Tweet quotes inside content with shortcode', 'essb'), 'ti-twitter', 'deactivate_ctt', true);
	ao_generate_feature_block(__('After Share Events', 'essb'), __('Show additional actions to user after sharing content', 'essb'), 'ti-share', 'deactivate_module_aftershare', true);
	ao_generate_feature_block(__('Share Optimizations', 'essb'), __('Add social share optimization tags for easy tune of the shared information', 'essb'), 'ti-receipt', 'deactivate_module_shareoptimize', true);
	ao_generate_feature_block(__('Plugin Analytics', 'essb'), __('Log share button clicks and generate report dashboard', 'essb'), 'ti-stats-up', 'deactivate_module_analytics', true);
	ao_generate_feature_block(__('Pinterest Pro', 'essb'), __('Automatically add Pin button over images in content, include Pinterest sharing images or galleries', 'essb'), 'ti-pinterest', 'deactivate_module_pinterestpro', true);
	ao_generate_feature_block(__('Short URL', 'essb'), __('Generate short URLs for sharing on social networks', 'essb'), 'ti-new-window', 'deactivate_module_shorturl', true);
	ao_generate_feature_block(__('Affiliate & Point Integration', 'essb'), __('Integrate plugin work with myCred, AffiliateWP', 'essb'), 'ti-money', 'deactivate_module_affiliate', true);
	ao_generate_feature_block(__('Custom Share', 'essb'), __('Custom share feature makes possible to change the share URL that plugin will use', 'essb'), 'ti-share-alt', 'deactivate_module_customshare', true);
	ao_generate_feature_block(__('Message Before Buttons', 'essb'), __('Add a custom message before or above share buttons "ex: Share this"', 'essb'), 'fa fa-comment', 'deactivate_module_message', true);
	ao_generate_feature_block(__('Social Metrics Lite', 'essb'), __('Log the official share values into a dashboard to see the most popular posts', 'essb'), 'ti-dashboard', 'deactivate_module_metrics', true);
	ao_generate_feature_block(__('Functions Translate', 'essb'), __('Allow to translate preset plugin texts on your language', 'essb'), 'fa fa-language', 'deactivate_module_translate', true);
	ao_generate_feature_block(__('Conversions Lite', 'essb'), __('Conversions lite allows tracking of share or subscribe conversions', 'essb'), 'ti-dashboard', 'deactivate_module_conversions', true);
	ao_generate_feature_block(__('Custom Display/Positions', 'essb'), __('The custom display/positions makes possible to create a custom position inside plugin. This position you can show with shortcode of functional call anywhere on site.', 'essb'), 'ti-layout-media-center-alt', 'deactivate_custompositions', true);
	ao_generate_feature_block(__('Automatic Mobile Setup', 'essb'), __('Activate automatic responsive mobile setup of share buttons', 'essb'), 'ti-mobile', 'activate_mobile_auto');
	ao_generate_feature_block(__('Integrations With Plugins', 'essb'), __('Additional integrations available with WooCommerce, bbPress and etc.', 'essb'), 'fa fa-plug', 'deactivate_method_integrations', true);

	ao_generate_feature_block(__('Social Followers Counter', 'essb'), __('Show the number of followers for 30+ social networks', 'essb'), 'ti-heart', 'deactivate_module_followers', true);
	ao_generate_feature_block(__('Social Profile Links', 'essb'), __('Add plain buttons for your social profiles with shortcode, widget or sidebar', 'essb'), 'ti-id-badge', 'deactivate_module_profiles', true);
	ao_generate_feature_block(__('Native Social Buttons', 'essb'), __('Use selected native social buttons along with your share buttons', 'essb'), 'ti-thumb-up', 'deactivate_module_natives', true);
	ao_generate_feature_block(__('Subscribe Forms', 'essb'), __('Add easy to use subscribe to mail list forms', 'essb'), 'ti-email', 'deactivate_module_subscribe', true);
	ao_generate_feature_block(__('Facebook Live Chat', 'essb'), __('Connect with your visitors using Facebook live chat', 'essb'), 'fa fa-facebook', 'deactivate_module_facebookchat', true);
	ao_generate_feature_block(__('Skype Live Chat', 'essb'), __('Connect with your visitors using Skype live chat', 'essb'), 'fa fa-skype', 'deactivate_module_skypechat', true);
	ao_generate_feature_block(__('Click 2 Chat', 'essb'), __('Add click to chat feature for WhatsApp and Viber', 'essb'), 'fa fa-comments', 'deactivate_module_clicktochat', true);

	ao_generate_feature_block(__('Fake Share Counters', 'essb'), __('The fake share counter option allows to change the generated counters on site', 'essb'), 'fa fa-retweet', 'activate_fake');
	ao_generate_feature_block(__('Hooks Integration', 'essb'), __('Easy assign share buttons to theme or plugin actions/filters. You can also use it to create a custom display methods.', 'essb'), 'fa fa-cog', 'activate_hooks');
	ao_generate_feature_block(__('Minimal Share Counters', 'essb'), __('Set a minimal share value that will be shown till the official value become greater', 'essb'), 'fa fa-sort-numeric-desc', 'activate_minimal');

	?>
</div>
