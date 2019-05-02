<?php
/**
 * Settings Framework Screen
 *
 * @version 3.0
 * @since 5.0
 * @package EasySocialShareButtons
 * @author appscreo
 */

/**
 * Loading the form designer functions but only inside the setup
 */
if (! function_exists ( 'essb5_get_form_designs' )) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/helpers/formdesigner-helper.php');
}

if (!function_exists('essb_display_status_message')) {
	function essb_display_status_message($title = '', $text = '', $icon = '', $additional_class = '') {
		echo '<div class="essb-header-status">';
		ESSBOptionsFramework::draw_hint($title, $text.'<span class="close-icon" onclick="essbCloseStatusMessage(this); return false;"><i class="fa fa-close"></i></span>', $icon, 'status '.$additional_class);
		echo '</div>';
	}
}

// Reset Settings
$reset_settings = isset ( $_REQUEST ['reset_settings'] ) ? $_REQUEST ['reset_settings'] : '';
if ($reset_settings == 'true') {
	$essb_admin_options = array ();
	$essb_options = array ();

	if (!function_exists('essb_generate_default_settings')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/default-options.php');
	}
	$options_base = essb_generate_default_settings();

	if ($options_base) {
		$essb_options = $options_base;
		$essb_admin_options = $options_base;
	}
	update_option ( ESSB3_OPTIONS_NAME, $essb_admin_options );
}

$rollback_settings = isset($_REQUEST['rollback_setup']) ? $_REQUEST['rollback_setup'] : '';
$rollback_key = isset($_REQUEST['rollback_key']) ? $_REQUEST['rollback_key'] : '';
if ($rollback_settings == 'true' && $rollback_key != '') {
	$history_container = get_option(ESSB5_SETTINGS_ROLLBACK);
	if (!is_array($history_container)) {
		$history_container = array();
	}

	if (isset($history_container[$rollback_key])) {
		$options_base = $history_container[$rollback_key];
		if ($options_base) {
			$essb_options = $options_base;
			$essb_admin_options = $options_base;
		}
		update_option ( ESSB3_OPTIONS_NAME, $essb_admin_options );
	}
}



global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options, $essb_sidebar_description;
global $current_tab;

global $essb_admin_options, $essb_options;
$essb_admin_options = get_option ( ESSB3_OPTIONS_NAME );
global $essb_networks;
$essb_networks = essb_available_social_networks ();

global $essb_admin_options_fanscounter;
$essb_admin_options_fanscounter = get_option ( ESSB3_OPTIONS_NAME_FANSCOUNTER );

if (! is_array ( $essb_admin_options_fanscounter )) {
	if (! class_exists ( 'ESSBSocialFollowersCounterHelper' )) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-helper.php');
	}

	$essb_admin_options_fanscounter = ESSBSocialFollowersCounterHelper::create_default_options_from_structure ( ESSBSocialFollowersCounterHelper::options_structure () );
	//update_option ( ESSB3_OPTIONS_NAME_FANSCOUNTER, $essb_admin_options_fanscounter );

	delete_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);
	update_option(ESSB3_OPTIONS_NAME_FANSCOUNTER, $essb_admin_options_fanscounter, 'no', 'no');
}

if (count ( $essb_navigation_tabs ) > 0) {
	$tab_1 = key ( $essb_navigation_tabs );
}

if ($tab_1 == '') {
	$tab_1 = 'social';
}

$current_tab = (empty ( $_GET ['tab'] )) ? $tab_1 : sanitize_text_field ( urldecode ( $_GET ['tab'] ) );
$active_settings_page = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : '';
if (strpos ( $active_settings_page, 'essb_redirect_' ) !== false) {
	$options_page = str_replace ( 'essb_redirect_', '', $active_settings_page );
	if ($options_page != '') {
		$current_tab = $options_page;
	}
}


$tabs = $essb_navigation_tabs;
$section = $essb_sidebar_sections [$current_tab];
$options = $essb_section_options [$current_tab];

/** Moving media load to allow plugin usage everywhere **/
if (function_exists ( 'wp_enqueue_media' )) {
	wp_enqueue_media ();
} else {
	wp_enqueue_style ( 'thickbox' );
	wp_enqueue_script ( 'media-upload' );
	wp_enqueue_script ( 'thickbox' );
}

?>

<div class="essb-admin-panel">

<?php
$drawing_tab = $current_tab;

if ($drawing_tab == 'update' || $drawing_tab == 'status') { $drawing_tab = 'about'; }

?>

<!-- settings: start -->
<div class="wrap essb-settings-wrap essb-wrap-<?php echo $drawing_tab; ?>">
	<div id="essb-scroll-top"></div>

	<!-- settings panel: start -->
	<div class="essb-settings-panel">
		<!-- settings navigation: start -->
		<div class="essb-settings-panel-navigation">
			<ul class="essb-plugin-menu">
				<li><div class="essb-logo essb-logo32 essb-new-color-logo">
					<a href="<?php echo admin_url('admin.php?page=essb_options');?>" class="no-hover"><div class="essb-version-logo"><?php echo ESSB3_VERSION;?></div></a>
				</div></li>

				<!-- navigation components: start -->
				<?php
				$is_first = true;
				$tab_has_nomenu = false;
				foreach ( $tabs as $name => $label ) {
					$tab_sections = isset ( $essb_sidebar_sections [$name] ) ? $essb_sidebar_sections [$name] : array ();
					$hidden_tab = isset ( $tab_sections ['hide_in_navigation'] ) ? $tab_sections ['hide_in_navigation'] : false;
					if ($hidden_tab) {
						continue;
					}


					$icon = isset ( $tab_sections ['icon'] ) ? $tab_sections ['icon'] : '';
					$align = isset($tab_sections['align']) ? $tab_sections['align'] : '';
					$description = isset($essb_sidebar_description[$name]) ? $essb_sidebar_description[$name] : '';
					$description = '';

					$desc_code = '';

					$options_handler = ($is_first) ? "essb_options" : 'essb_redirect_' . $name;

					$tab_classes = ($current_tab == $name) ? 'active': '';
					$tab_classes .= ($align == 'right') ? ' tab-right' : '';

					echo '<li class="'.$tab_classes.' essb-tabid-'.$name.'"><a href="' . admin_url ( 'admin.php?page=' . $options_handler . '&tab=' . $name ) . '" class="essb-nav-tab ';
					if ($current_tab == $name)
						echo 'active';

					echo ' essb-tab-'.$name;

					if ($description != '') {
						$desc_code .= '<span class="description">'.$description.'</span>';
					}

					echo '" title="'.$label.'">' . ($icon != '' ? '<i class="' . $icon . '"></i>' : '') . '<span>'.$label . '</span>'.$desc_code.($name == 'update' && !ESSBActivationManager::isActivated() && !ESSBActivationManager::isThemeIntegrated() ? '<span class="not-activated"></span>':'').'</a>';
					$is_first = false;

					if ($current_tab == $name) {
						ESSBOptionsInterface::draw_sidebar ( $section ['fields'] );
					}

					echo '</li>';

					if ($current_tab == $name) {
						$tab_has_nomenu = isset ( $tab_sections ['hide_menu'] ) ? $tab_sections ['hide_menu'] : false;
					}
				}

				?>
				<!-- navigation components: end -->
			</ul>

		</div>
		<!-- settings navigation: end -->

		<!-- settings options: start -->
		<div class="essb-settings-panel-options">

		<script type="text/javascript">

		var essb5_active_tag = "<?php echo $current_tab; ?>";

		</script>

		<?php

		$additional_buttons = '';



		$additional_buttons .= '<a href="'.admin_url ("admin.php?page=essb_redirect_modes&tab=modes").'"  text="' . __ ( 'Activate or Deactivate Plugin Features', 'essb' ) . '" class="essb-btn essb-btn-plain essb-btn-small essb-btn-noupper essb-head-modesbtn essb-headbutton'.($current_tab == 'modes' ? ' active': '').'" style="margin-right: 5px;" id="essb-head-modesbtn" title="Change between different plugin working modes to make plugin fits best into your needs"><i class="fa fa-magic"></i>&nbsp;' . __ ( 'Switch Plugin Mode', 'essb' ) . '</a>';
		$additional_buttons .= '<a href="'.admin_url ("admin.php?page=essb_redirect_functions&tab=functions").'"  text="' . __ ( 'Activate or Deactivate Plugin Features', 'essb' ) . '" class="essb-btn essb-btn-plain essb-btn-small essb-head-featuresbtn essb-btn-noupper essb-headbutton'.($current_tab == 'functions' ? ' active': '').'" id="essb-head-featuresbtn" style="margin-right: 5px;" title="Activate/deactivate functions of plugin"><i class="fa fa-cog"></i>&nbsp;' . __ ( 'Manage Plugin Features', 'essb' ) . '</a>';
		$additional_buttons .= '<a href="'.admin_url ("admin.php?page=essb_redirect_quick&tab=quick").'"  text="' . __ ( 'Quick Setup Wizard', 'essb' ) . '" class="essb-btn essb-btn-plain essb-btn-small essb-btn-noupper  essb-headbutton" style="margin-right: 5px;" title="Quick configuration wizard for the most common functions"><i class="fa fa-bolt"></i>&nbsp;' . __ ( 'Setup Wizard', 'essb' ) . '</a>';
		$additional_buttons .= '<a href="'.admin_url ("admin.php?page=essb_redirect_about&tab=about&about_tab=help").'" text="' . __ ( 'Need Help? Click here to visit our support center', 'essb' ) . '" class="essb-btn essb-btn-plain essb-btn-small essb-btn-noupper  essb-headbutton" title="Need a hand working with plugin?"><i class="fa fa-question"></i>&nbsp;' . __ ( 'Need Help?', 'essb' ) . '</a>';


		if ($current_tab != 'analytics' && $current_tab != 'shortcode' && $current_tab != 'status' && $current_tab != 'welcome' &&
				$current_tab != 'extensions' && $current_tab != 'about' && $current_tab != 'quick' && $current_tab != 'support' &&
				$current_tab != 'update') {
			ESSBOptionsInterface::draw_form_start (false, '', $tab_has_nomenu);

			// drawing additional interface notifications
			essb_show_interface_notifications();

			// drawing additional notifications based on user actions
			essb_settings5_status_notifications();

			//$advanced_settings = '<a href="#" class="essb-btn essb-btn-right" style="margin-top: -8px;"><i class="fa fa-sliders" aria-hidden="true" style="margin-right: 5px;"></i>'.__('Advanced Settings', 'essb').'</a>';
			$advanced_settings = '';

			$section_icon = isset($section['icon']) ? $section['icon'] : '';

			ESSBOptionsInterface::draw_header5 ( $section ['title'], $section ['hide_update_button'], $section ['wizard_tab'], $additional_buttons, $advanced_settings, $section_icon );


			ESSBOptionsInterface::draw_content ( $options );

			ESSBOptionsInterface::draw_form_end ();

			ESSBOptionsFramework::register_color_selector ();


		}
		else if ($current_tab == 'analytics') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/essb-analytics-dashboard.php';
		} else if ($current_tab == "shortcode") {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-shortcode.php';
		}
		else if ($current_tab == 'status') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-about.php';
		}
		else if ($current_tab == 'about') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-about.php';
		}
		else if ($current_tab == 'support') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-support.php';
		}
		else if ($current_tab == 'welcome') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/admin-options/essb-welcome.php';
		}
		else if ($current_tab == 'extensions') {
			ESSBOptionsInterface::draw_header5 ( $section ['title'], $section ['hide_update_button'], $section ['wizard_tab'], '', '' );
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-addons.php';
		}
		else if ($current_tab == 'quick') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-wizard-helper.php';
		}
		else if ($current_tab == 'update') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-about.php';
		}
		?>

		</div>

		<!-- settings-options: end; -->
	</div>
	<!-- settings panel: end; -->
</div>
<!-- settings: end -->


<!-- test -->
<div class="essb-helper-popup-overlay"></div>
<div class="essb-helper-popup" style="width: 400px; height: 400px; left: 150px; top: 150px;" id="essb-testpopup" data-width="auto" data-height="auto">
	<div class="essb-helper-popup-title">
		This is popup tittle
		<a href="#" class="essb-helper-popup-close"></a>
	</div>
	<div class="essb-helper-popup-content">
		asdaadasdadsa
	</div>
	<div class="essb-helper-popup-command">
		<a href="#" class="essb-btn essb-assign">Save Settings</a> <a href="#" class="essb-btn essb-assign-popupclose">Close Settings</a>
	</div>
</div>

<!-- Social Networks Selection -->
<div class="essb-helper-popup" id="essb-networkselect" data-width="800" data-height="auto">
	<div class="essb-helper-popup-title">
		Social Networks Selection
		<a href="#" class="essb-helper-popup-close"></a>
	</div>
	<div class="essb-helper-popup-content">

	</div>
	<div class="essb-helper-popup-command">
		<a href="#" class="essb-btn essb-btn-red" id="essb-button-confirm-select" data-close="#essb-networkselect"><i class="fa fa-check" aria-hidden="true" style="margin-right: 5px;"></i>Choose</a> <a href="#" class="essb-btn essb-assign-popupclose"><i class="fa fa-close" aria-hidden="true" style="margin-right: 5px;"></i>Close</a>
	</div>
</div>

<!-- Template Selection -->
<div class="essb-helper-popup" id="essb-templateselect" data-width="800" data-height="auto">
	<div class="essb-helper-popup-title">
		Template Selection
		<a href="#" class="essb-helper-popup-close"></a>
	</div>
	<div class="essb-helper-popup-content">
		<?php essb_component_base_template_selection('', 'style', 'style_text');?>
	</div>

</div>

<!-- Template Selection -->
<div class="essb-helper-popup" id="essb-pintemplateselect" data-width="800" data-height="auto">
	<div class="essb-helper-popup-title">
		Template Selection
		<a href="#" class="essb-helper-popup-close"></a>
	</div>
	<div class="essb-helper-popup-content">
		<?php essb_component_base_template_selection('', 'style', 'style_text', array('pinterest'), array('pinterest' => 'Pin'));?>
	</div>

</div>

<!-- Button Style Select -->
<div class="essb-helper-popup" id="essb-buttonstyleselect" data-width="800" data-height="auto">
	<div class="essb-helper-popup-title">
		Button Style
		<a href="#" class="essb-helper-popup-close"></a>
	</div>
	<div class="essb-helper-popup-content">
		<?php essb_component_base_button_style_selection('');?>
	</div>

</div>

<!-- Button Style Select -->
<div class="essb-helper-popup" id="essb-pinbuttonstyleselect" data-width="800" data-height="auto">
	<div class="essb-helper-popup-title">
		Button Style
		<a href="#" class="essb-helper-popup-close"></a>
	</div>
	<div class="essb-helper-popup-content">
		<?php essb_component_base_button_style_selection('', true);?>
	</div>

</div>

<!-- Share Counter Position Select -->
<div class="essb-helper-popup" id="essb-counterposselect" data-width="800" data-height="auto">
	<div class="essb-helper-popup-title">
		Single Button Share Counter Style
		<a href="#" class="essb-helper-popup-close"></a>
	</div>
	<div class="essb-helper-popup-content">
		<?php essb_component_base_counter_position_selection('');?>
	</div>

</div>

<!-- Total Share Counter Position Select -->
<div class="essb-helper-popup" id="essb-totalcounterposselect" data-width="800" data-height="auto">
	<div class="essb-helper-popup-title">
		Single Button Share Counter Style
		<a href="#" class="essb-helper-popup-close"></a>
	</div>
	<div class="essb-helper-popup-content">
		<?php essb_component_base_total_counter_position_selection();?>
	</div>

</div>

<!-- Total Share Counter Position Select -->
<div class="essb-helper-popup" id="essb-animationsselect" data-width="800" data-height="auto">
	<div class="essb-helper-popup-title">
		Animations
		<a href="#" class="essb-helper-popup-close"></a>
	</div>
	<div class="essb-helper-popup-content">
		<?php essb_component_base_animation_selection();?>
	</div>

</div>

<?php

$template_list = essb_available_tempaltes4();
$templates = array();

foreach ($template_list as $key => $name) {
	$templates[$key] = essb_template_folder($key);
}

?>

<script type="text/javascript">
var essbAdminSettings = {
		'networks': <?php echo json_encode(essb_available_social_networks()); ?>,
		'templates': <?php echo json_encode($templates); ?>
};

function essbCloseStatusMessage(sender) {
	jQuery(sender).closest('.essb-options-hint-status').fadeOut();
}
</script>

</div>

<?php
function essb_settings5_status_notifications() {
	global $essb_admin_options, $current_tab;

	$purge_cache = isset ( $_REQUEST ['purge-cache'] ) ? $_REQUEST ['purge-cache'] : '';
	$rebuild_resource = isset ( $_REQUEST ['rebuild-resource'] ) ? $_REQUEST ['rebuild-resource'] : '';

	$dismiss_addon = isset ( $_REQUEST ['dismiss'] ) ? $_REQUEST ['dismiss'] : '';
	if ($dismiss_addon == "true") {
		$dismiss_addon = isset ( $_REQUEST ['addon'] ) ? $_REQUEST ['addon'] : '';
		$addons = ESSBAddonsHelper::get_instance ();

		$addons->dismiss_addon_notice ( $dismiss_addon );
	}


	if (class_exists ( 'ESSBAdminActivate' )) {

		$dismissactivate = isset ( $_REQUEST ['dismissactivate'] ) ? $_REQUEST ['dismissactivate'] : '';
		if ($dismissactivate == "true") {
			ESSBAdminActivate::dismiss_notice ();
		} else {
			if (! ESSBAdminActivate::is_activated () && ESSBAdminActivate::should_display_notice ()) {
				ESSBAdminActivate::notice_activate ();
			}
		}

		ESSBAdminActivate::notice_manager();

		//$deactivate_appscreo = essb_options_bool_value('deactivate_appscreo');
		//if (!$deactivate_appscreo) {
		//	ESSBAdminActivate::notice_new_addons();
		//}
	}

	$cache_plugin_message = "";
	$reset_settings = isset ( $_REQUEST ['reset_settings'] ) ? $_REQUEST ['reset_settings'] : '';
	if (ESSBCacheDetector::is_cache_plugin_detected ()) {
		$cache_plugin_message = __(" Cache plugin detected running on site: ", "essb") . ESSBCacheDetector::cache_plugin_name ();
	}

	$settings_update = isset ( $_REQUEST ['settings-updated'] ) ? $_REQUEST ['settings-updated'] : '';
	if ($settings_update == "true") {
		essb_display_status_message(__('Options are saved!', 'essb'), 'Your new setup is ready to use. If you use cache plugin (example: W3 Total Cache, WP Super Cache, WP Rocket) or optimization plugin (example: Autoptimize, BWP Minify) it is highly recommended to clear cache or you may not see the changes. '.$cache_plugin_message, 'fa fa-info-circle', 'essb-status-update');

	}

	$settings_update = isset ( $_REQUEST ['wizard-updated'] ) ? $_REQUEST ['wizard-updated'] : '';
	if ($settings_update == "true") {
		essb_display_status_message(__('Your new settings are saved!', 'essb'), 'The initial setup of plugin via quick setup wizard is done. You can make additional adjustments using plugin menu, import ready made styles or just use it that way. If you use cache plugin (example: W3 Total Cache, WP Super Cache, WP Rocket) or optimization plugin (example: Autoptimize, BWP Minify) it is highly recommended to clear cache or you may not see the changes. '.$cache_plugin_message, 'fa fa-info-circle', 'essb-status-update');

	}

	$settings_imported = isset ( $_REQUEST ['settings-imported'] ) ? $_REQUEST ['settings-imported'] : '';
	if ($settings_imported == "true") {
		essb_display_status_message(__('Options are imported!', 'essb'), 'If you use cache plugin (example: W3 Total Cache, WP Super Cache, WP Rocket) or optimization plugin (example: Autoptimize, BWP Minify) it is highly recommended to clear cache or you may not see the changes. '.$cache_plugin_message, 'fa fa-info-circle');

	}
	if ($reset_settings == 'true') {
		essb_display_status_message(__('Options are reset to default!', 'essb'), 'If you use cache plugin (example: W3 Total Cache, WP Super Cache, WP Rocket) or optimization plugin (example: Autoptimize, BWP Minify) it is highly recommended to clear cache or you may not see the changes. '.$cache_plugin_message, 'fa fa-info-circle');

	}

	// cache is running
	$general_cache_active = ESSBOptionValuesHelper::options_bool_value ( $essb_admin_options, 'essb_cache' );
	$general_cache_active_static = ESSBOptionValuesHelper::options_bool_value ( $essb_admin_options, 'essb_cache_static' );
	$general_cache_active_static_js = ESSBOptionValuesHelper::options_bool_value ( $essb_admin_options, 'essb_cache_static_js' );
	$general_cache_mode = ESSBOptionValuesHelper::options_value ( $essb_admin_options, 'essb_cache_mode' );
	$is_cache_active = false;

	$general_precompiled_resources = ESSBOptionValuesHelper::options_bool_value ( $essb_admin_options, 'precompiled_resources' );

	$backup = isset ( $_REQUEST ['backup'] ) ? $_REQUEST ['backup'] : '';


	$display_cache_mode = "";
	if ($general_cache_active) {
		if ($general_cache_mode == "full") {
			$display_cache_mode = __("Cache button render and dynamic resources", "essb");
		} else if ($general_cache_mode == "resource") {
			$display_cache_mode = __("Cache only dynamic resources", "essb");
		} else {
			$display_cache_mode = __("Cache only button render", "essb");
		}
		$is_cache_active = true;
	}

	if ($general_cache_active_static || $general_cache_active_static_js) {
		if ($display_cache_mode != '') {
			$display_cache_mode .= ", ";
		}
		$display_cache_mode .= __("Combine into sigle file all plugin static CSS files", "essb");
		$is_cache_active = true;
	}

	if ($is_cache_active) {
		$cache_clear_address = esc_url_raw ( add_query_arg ( array ('purge-cache' => 'true' ), essb_get_current_page_url () ) );

		$dismiss_addons_button = '<a href="' . $cache_clear_address . '"  text="' . __ ( 'Purge Cache', 'essb' ) . '" class="status_button float_right" style="margin-right: 5px;"><i class="fa fa-close"></i>&nbsp;' . __ ( 'Purge Cache', 'essb' ) . '</a>';
		essb_display_status_message(__('Plugin Cache is Running!', 'essb'), sprintf('%2$s %1$s', $dismiss_addons_button, $display_cache_mode), 'fa fa-database');
	}

	if ($general_precompiled_resources) {
		$cache_clear_address = esc_url_raw ( add_query_arg ( array ('rebuild-resource' => 'true' ), essb_get_current_page_url () ) );

		$dismiss_addons_button = '<a href="' . $cache_clear_address . '"  text="' . __ ( 'Rebuild Resources', 'essb' ) . '" class="status_button float_right" style="margin-right: 5px;"><i class="fa fa-close"></i>&nbsp;' . __ ( 'Rebuild Resources', 'essb' ) . '</a>';
		essb_display_status_message(__('Precompiled Resource Mode is Active!', 'essb'), sprintf('In precompiled mode plugin will load default setup into single static files that will run on entire site. %1$s', $dismiss_addons_button), 'fa fa-history');
	}

	if ($backup == 'true') {
		essb_display_status_message(__('Backup is ready!', 'essb'), 'Backup of your current settings is generated. Copy generated configuration string and save it on your computer. You can use it to restore settings or transfer them to other site.', 'fa fa-gear');
	}


	$rollback_settings = isset($_REQUEST['rollback_setup']) ? $_REQUEST['rollback_setup'] : '';
	$rollback_key = isset($_REQUEST['rollback_key']) ? $_REQUEST['rollback_key'] : '';
	if ($rollback_settings == 'true' && $rollback_key != '') {
		essb_display_status_message(__('Settings Rollback Completed!', 'essb'), 'Your setup from '.date(DATE_RFC822, $rollback_key).' is restored!', 'fa fa-gear');

	}

	if (essb_option_value('counter_mode') == '' && essb_option_value('show_counter')) {
		essb_display_status_message(__('Real time share counters warning!', 'essb'), __('You are using real time share counters update on your site. It is highly recommended to avoid that on a production site because you may cause overload of server or send too many requests to social API which will lead to missing share counters for a period of time', 'essb'), 'fa fa-exclamation-circle');
	}

	if ($purge_cache == 'true') {
		if (class_exists ( 'ESSBDynamicCache' )) {
			ESSBDynamicCache::flush ();
		}
		if (function_exists ( 'purge_essb_cache_static_cache' )) {
			purge_essb_cache_static_cache ();
		}
		essb_display_status_message(__('Cache is Cleared!', 'essb'), 'Build in cache of plugin is fully cleared!', 'fa fa-info-circle');

	}

	if ($rebuild_resource == "true") {
		if (class_exists ( 'ESSBPrecompiledResources' )) {
			ESSBPrecompiledResources::flush ();
		}
	}

	if (function_exists('essb3_apply_readymade_style')) {
		essb3_apply_readymade_style();
	}

	$reset_analytics = isset($_REQUEST['reset_analytics']) ? $_REQUEST['reset_analytics'] : '';
	if ($reset_analytics == 'true') {
		essb_display_status_message(__('Analytics data is cleared!', 'essb'), 'All current analytics data is removed and stats will start again. This data is not connected to share counters on your site.', 'fa fa-info-circle');
		global $wpdb;
		$table  = $wpdb->prefix . ESSB3_TRACKER_TABLE;
		$delete = $wpdb->query("TRUNCATE TABLE $table");
	}

	$reset_short = isset($_REQUEST['reset_short']) ? $_REQUEST['reset_short'] : '';
	if ($reset_short == 'true') {
		essb_display_status_message(__('Short URL cache is cleared!', 'essb'), 'All stored short URLs are removed from post meta information.', 'fa fa-info-circle');
		delete_post_meta_by_key('essb_shorturl_googl');
		delete_post_meta_by_key('essb_shorturl_post');
		delete_post_meta_by_key('essb_shorturl_bitly');
		delete_post_meta_by_key('essb_shorturl_ssu');
		delete_post_meta_by_key('essb_shorturl_rebrand');
	}

	$reset_counterupdate = isset($_REQUEST['reset_counterupdate']) ? $_REQUEST['reset_counterupdate'] : '';
	if ($reset_counterupdate == 'true') {
		essb_display_status_message(__('Share Counter Update is Scheduled!', 'essb'), 'You have successfully clear the counter update time. All share coutners will update as soon as post/page is loaded.', 'fa fa-info-circle');
		delete_post_meta_by_key('essb_cache_expire');
	}

	$reset_alldata = isset($_REQUEST['reset_alldata']) ? $_REQUEST['reset_alldata'] : '';
	if ($reset_alldata == 'true') {
		essb_display_status_message(__('Plugin Information is Removed!', 'essb'), 'You remove all data that was stored by plugin', 'fa fa-info-circle');

		// short URLs
		delete_post_meta_by_key('essb_shorturl_googl');
		delete_post_meta_by_key('essb_shorturl_post');
		delete_post_meta_by_key('essb_shorturl_bitly');
		delete_post_meta_by_key('essb_shorturl_ssu');
		delete_post_meta_by_key('essb_shorturl_rebrand');

		// share counters
		delete_post_meta_by_key('essb_cache_expire');
		$networks = essb_available_social_networks();

		foreach ($networks as $key => $data) {
			delete_post_meta_by_key('essb_c_'.$key);
			delete_post_meta_by_key('essb_pc_'.$key);
		}
		delete_post_meta_by_key('_essb_love');
		delete_post_meta_by_key('essb_metrics_data');

		delete_post_meta_by_key('essb_cached_image');

		// post setup data
		delete_post_meta_by_key('essb_off');
		delete_post_meta_by_key('essb_post_button_style');
		delete_post_meta_by_key('essb_post_template');
		delete_post_meta_by_key('essb_post_counters');
		delete_post_meta_by_key('essb_post_counter_pos');
		delete_post_meta_by_key('essb_post_total_counter_pos');
		delete_post_meta_by_key('essb_post_customizer');
		delete_post_meta_by_key('essb_post_animations');
		delete_post_meta_by_key('essb_post_optionsbp');
		delete_post_meta_by_key('essb_post_content_position');
		foreach ( essb_available_button_positions() as $position => $name ) {
			delete_post_meta_by_key("essb_post_button_position_{$position}");
		}
		delete_post_meta_by_key('essb_post_native');
		delete_post_meta_by_key('essb_post_native_skin');
		delete_post_meta_by_key('essb_post_share_message');
		delete_post_meta_by_key('essb_post_share_url');
		delete_post_meta_by_key('essb_post_share_image');
		delete_post_meta_by_key('essb_post_share_text');
		delete_post_meta_by_key('essb_post_pin_image');
		delete_post_meta_by_key('essb_post_fb_url');
		delete_post_meta_by_key('essb_post_plusone_url');
		delete_post_meta_by_key('essb_post_twitter_hashtags');
		delete_post_meta_by_key('essb_post_twitter_username');
		delete_post_meta_by_key('essb_post_twitter_tweet');
		delete_post_meta_by_key('essb_activate_ga_campaign_tracking');
		delete_post_meta_by_key('essb_post_og_desc');
		delete_post_meta_by_key('essb_post_og_author');
		delete_post_meta_by_key('essb_post_og_title');
		delete_post_meta_by_key('essb_post_og_image');
		delete_post_meta_by_key('essb_post_og_video');
		delete_post_meta_by_key('essb_post_og_video_w');
		delete_post_meta_by_key('essb_post_og_video_h');
		delete_post_meta_by_key('essb_post_og_url');
		delete_post_meta_by_key('essb_post_twitter_desc');
		delete_post_meta_by_key('essb_post_twitter_title');
		delete_post_meta_by_key('essb_post_twitter_image');
		delete_post_meta_by_key('essb_post_google_desc');
		delete_post_meta_by_key('essb_post_google_title');
		delete_post_meta_by_key('essb_post_google_image');
		delete_post_meta_by_key('essb_activate_sharerecovery');
		delete_post_meta_by_key('essb_post_og_image1');
		delete_post_meta_by_key('essb_post_og_image2');
		delete_post_meta_by_key('essb_post_og_image3');
		delete_post_meta_by_key('essb_post_og_image4');

		// removing plugin saved possible options
		delete_option('essb3_addons');
		delete_option('essb3_addons_announce');
		delete_option(ESSB3_OPTIONS_NAME);
		delete_option('essb_dismissed_notices');

		delete_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);
		delete_option(ESSB3_FIRST_TIME_NAME);
		delete_option('essb-shortcodes');
		delete_option('essb-hook');
		delete_option('essb3-translate-notice');
		delete_option('essb3-subscribe-notice');
		delete_option(ESSB3_EASYMODE_NAME);
		delete_option(ESSB5_SETTINGS_ROLLBACK);
		delete_option('essb-admin-settings-token');
		delete_option('essb_cache_static_cache_ver');
		delete_option('essb4-activation');
		delete_option('essb4-latest-version');
		delete_option('essb-conversions-lite');
		delete_option('essb-subscribe-conversions-lite');
		delete_option('essbfcounter_cached');
		delete_option('essbfcounter_expire');
		delete_option(ESSB3_MAIL_SALT);

		global $wpdb;
		$table  = $wpdb->prefix . ESSB3_TRACKER_TABLE;
		$wpdb->query( $wpdb->prepare("DROP TABLE IF EXISTS ".$table) );
	}
}
?>

<?php

$deactivate_ajaxsubmit = essb_option_bool_value('deactivate_ajaxsubmit');

if ($current_tab == 'developer') {
	$deactivate_ajaxsubmit = true;
}

if (!$deactivate_ajaxsubmit) {

?>

<div class="preloader-holder">
<div class="preloader"></div>
<div class="preloader-message">Please Wait a Moment ...</div>
</div>


<script type="text/javascript">

	// assign ajax submit on form
jQuery(document).ready(function($){
	if ($('#essb-btn-update').length && $('#essb_options_form').length) {
		var frmSettings = $('#essb_options_form');

		$(frmSettings).submit(function (e) {

			if (typeof(essbWizardIsRunning) == 'undefined') var essbWizardIsRunning = false;
			
			if (essbWizardIsRunning) {
				e.preventDefault();
				return;
			}

			if (typeof(essb_disable_ajax_submit) == "undefined") essb_disable_ajax_submit = false;
			if (!essb_disable_ajax_submit) {
		        e.preventDefault();

		        if ($('.tmce-active').length) {
			        try {
				        console.log('MCE calling save');
			         	tinyMCE.triggerSave();
			        }
			        catch (e) {
			        }
		        }

				// updating codemirror before save
				$('.is-code-editor').each(function(){
					var elementId = $(this).attr('data-editor-key') || '';
					if (typeof(loadedEditorControls[elementId] != 'undefined')) {
						try {
							loadedEditorControls[elementId].save();
						}
						catch (e) {
						}
					}
				});
				
				$.ajax({
		            type: frmSettings.attr('method'),
		            url: frmSettings.attr('action'),
		            data: frmSettings.serialize(),
		            success: function (data) {
		                $('.preloader-holder').fadeOut(400);
		                //swal("Your settings are saved!", "Your new setup is ready to use. If you use cache plugin (example: W3 Total Cache, WP Super Cache, WP Rocket) or optimization plugin (example: Autoptimize, BWP Minify) it is highly recommended to clear cache or you may not see the changes.", "success")
		                swal({
			                title: '<?php _e('Your Settings Are Saved!', 'essb'); ?>',
					        icon: 'success',
					        text: '<?php _e('Your new setup is ready to use. If you use cache plugin (example: W3 Total Cache, WP Super Cache, WP Rocket) or optimization plugin (example: Autoptimize, BWP Minify) it is highly recommended to clear cache or you may not see the changes.', 'essb');?>',
					        className: "essb-swal",
		                });

		            }
		        });

			}
		});



		$('#essb-btn-update').on('click', function(e) {
			$('.preloader-holder').fadeIn(100);
		});
	}

});

</script>
<?php } ?>

<?php
/**
 * Detect first time run to suggest visitor run a plugin wizard
 */

$is_for_firsttime = get_option ( ESSB3_FIRST_TIME_NAME );
if (!$is_for_firsttime) { $is_for_firsttime = 'false'; }

if ($current_tab != 'about' && $is_for_firsttime == 'true') {

	// first time wizard displayed
	update_option(ESSB3_FIRST_TIME_NAME, 'false');

	?>


	<script type="text/javascript">
	var essbWizardIsRunning = window.essbWizardIsRunning = false;
	jQuery(document).ready(function($){
		var essbWelcomeWizard =  new Anno([
			{
				target: '.essb-settings-panel-navigation',
				content: 'Welcome to Easy Social Share Buttons for WordPress. Let us show you a quick plugin introduction of most common functions. This wizard will show just once. You can interrupt it at any time by pressing the Close button ',
				position: 'center-right',
				buttons: [AnnoButton.NextButton, {
      		text: 'Close',
      		className: 'anno-btn-low-importance',
      		click: function(anno, evt){
						essbWizardIsRunning = false;
        		anno.hide()
      		}
    		}]
			},
			{
				target: '#essb-btn-update',
				content: 'To save changes inside settings you need to press the "Update Options" button. You should also press that button before navigating between different global settings screen or changes will be lost.',
				position: 'left',
				buttons: [AnnoButton.NextButton, {
					text: 'Close',
					className: 'anno-btn-low-importance',
					click: function(anno, evt){
						essbWizardIsRunning = false;
						anno.hide()
					}
				}]
			},
			{
				target: '#essb-head-modesbtn',
				content: 'You can easy change the mode of plugin from the Switch Plugin Mode menu. The different modes comes with different active functions inside plugin. For example if you plan to use just simple share buttons you can activate the Light mode. Everything that you will not use will disappear from plugin menu.',
				position: 'bottom',
				buttons: [AnnoButton.NextButton, {
					text: 'Close',
					className: 'anno-btn-low-importance',
					click: function(anno, evt){
						essbWizardIsRunning = false;
						anno.hide()
					}
				}]
			},
			{
				target: '#essb-head-featuresbtn',
				content: 'Instead of selecting a plugin mode you can manage the active plugin features. Each component you deactivate will disappear from code and setup. You can activate it back again at any time from the same screen.',
				position: 'bottom',
				buttons: [AnnoButton.NextButton, {
					text: 'Close',
					className: 'anno-btn-low-importance',
					click: function(anno, evt){
						essbWizardIsRunning = false;
						anno.hide()
					}
				}]
			},
			{
				target: '.essb-tabid-social',
				content: 'Here you can find your global share buttons setup - networks, global styles, share counter setup, social share optimization, affiliate integrations, short URLs and etc. A parts of share button display along with active networks you can also activate from the positions screen.',
				position: 'right',
				buttons: [AnnoButton.NextButton, {
					text: 'Close',
					className: 'anno-btn-low-importance',
					click: function(anno, evt){
						essbWizardIsRunning = false;
						anno.hide()
					}
				}]
			},
			{
				target: '.essb-tabid-where',
				content: 'In this setup screen you can control the post types and positions where share buttons will appear. Here you will also find the mobile setup options for share buttons.',
				position: 'right',
				buttons: [AnnoButton.NextButton, {
					text: 'Close',
					className: 'anno-btn-low-importance',
					click: function(anno, evt){
						essbWizardIsRunning = false;
						anno.hide()
					}
				}]
			},

			{
				target: '.essb-tabid-display',
				content: 'In this setup screen you can find the social followers counter setup, social profile links and the chat features of plugin',
				position: 'right',
				buttons: [AnnoButton.NextButton, {
					text: 'Close',
					className: 'anno-btn-low-importance',
					click: function(anno, evt){
						essbWizardIsRunning = false;
						anno.hide()
					}
				}]
			},

			{
				target: '.essb-tabid-optin',
				content: 'In this setup screen you can find the settings for integrating a subscribe form on site and also the design customizations and form builder. You will also have access to the additional automated subscribe form display features: below content, fly-in or booster (popup).',
				position: 'right',
				buttons: [AnnoButton.NextButton, {
					text: 'Close',
					className: 'anno-btn-low-importance',
					click: function(anno, evt){
						essbWizardIsRunning = false;
						anno.hide()
					}
				}]
			},
			{
				target: '.essb-tabid-advanced',
				content: 'All optimization options or advanced and administrative options are located here',
				position: 'right',
				buttons: [AnnoButton.NextButton, {
					text: 'Close',
					className: 'anno-btn-low-importance',
					click: function(anno, evt){
						essbWizardIsRunning = false;
						anno.hide()
					}
				}]
			},
			{
				target: '.essb-tabid-import',
				content: 'You can import or save an existing plugin configuration. If you need to reset the settings or plugin data you can find everything you need here.',
				position: 'right',
				buttons: [AnnoButton.NextButton, {
					text: 'Close',
					className: 'anno-btn-low-importance',
					click: function(anno, evt){
						essbWizardIsRunning = false;
						anno.hide()
					}
				}]
			},
			{
				target: '.essb-settings-panel-navigation',
				content: 'Thank you for choosing Easy Social Share Buttons for WordPress. If you need assitance with plugin work refer to the official support board <a href="https://support.creoworx.com" target="_blank">https://support.creoworx.com</a>',
				position: 'center-right',
				buttons: [ {
					text: 'Close',
					className: '',
					click: function(anno, evt){
						essbWizardIsRunning = false;
						anno.hide()
					}
				}]
			},

]);

		essbWelcomeWizard.show();
		essbWizardIsRunning = true;
	});
	</script>
	<?php
}

// including the new styles core library
include_once ESSB3_PLUGIN_ROOT . 'lib/admin/styles-library/styles-core.php';

if (essb_admin_advanced_options()) {
	include_once ESSB3_PLUGIN_ROOT . 'lib/admin/advanced-options/advancedoptions-core.php';
}
?>
