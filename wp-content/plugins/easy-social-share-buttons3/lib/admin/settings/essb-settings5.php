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

	<?php 
	ESSBControlCenter::convert_legacy_options_structure();
	ESSBControlCenter::set_active_section($current_tab);
	
	ESSBControlCenter::draw_header();
	ESSBControlCenter::draw_sidebar();
	?>

		<script type="text/javascript">
		var essb5_active_tag = "<?php echo esc_attr($current_tab); ?>";
		</script>

		<?php

	

		if ($current_tab != 'analytics' && $current_tab != 'shortcode' && $current_tab != 'status' && $current_tab != 'welcome' &&
				$current_tab != 'extensions' && $current_tab != 'about' && $current_tab != 'quick' && $current_tab != 'support' &&
				$current_tab != 'update' && $current_tab != 'test' && $current_tab != 'test2') {

			// drawing additional interface notifications
			add_action('essb_control_center_before_content', 'essb_show_interface_notifications');			

			// drawing additional notifications based on user actions
			add_action('essb_control_center_before_content', 'essb_settings5_status_notifications');

			ESSBControlCenter::draw_content();
			ESSBOptionsFramework::register_color_selector ();


		}
		else if ($current_tab == 'analytics') {
			ESSBControlCenter::draw_blank_content_start();
			include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/essb-analytics-dashboard.php';
			ESSBControlCenter::draw_blank_content_end();
		} else if ($current_tab == "shortcode") {
			ESSBControlCenter::draw_blank_content_start();
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-shortcode.php';
			ESSBControlCenter::draw_blank_content_end();
		}
		else if ($current_tab == 'status') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-about.php';
		}
		else if ($current_tab == 'about') {
			ESSBControlCenter::draw_blank_content_start();
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-about.php';
			ESSBControlCenter::draw_blank_content_end();
		}
		else if ($current_tab == 'support') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-about.php';
			
		}
		else if ($current_tab == 'extensions') {
			ESSBControlCenter::draw_blank_content_start();
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-addons.php';
			ESSBControlCenter::draw_blank_content_end();
		}
		else if ($current_tab == 'quick') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-wizard-helper.php';
		}
		else if ($current_tab == 'update') {
			ESSBControlCenter::draw_blank_content_start();
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-about.php';
			ESSBControlCenter::draw_blank_content_end();
		}
		else if ($current_tab == 'test') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/playground/test1.php';
		}
		else if ($current_tab == 'test2') {
			include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/playground/test2.php';
		}
		?>

		<?php 	
		ESSBControlCenter::draw_footer();
		?>
</div>
<!-- settings: end -->


<!-- test -->
<div class="essb-helper-popup-overlay"></div>
<div class="essb-helper-popup" id="essb-testpopup" data-width="auto" data-height="auto">
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

<!-- Boarding wizard -->

<?php 
/***
 * Load the boarding guide for the users
 */
include_once ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-boarding.php';
?>

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
	}

	$cache_plugin_message = "";
	$reset_settings = isset ( $_REQUEST ['reset_settings'] ) ? $_REQUEST ['reset_settings'] : '';
	if (ESSBCacheDetector::is_cache_plugin_detected ()) {
		$cache_plugin_message = esc_html__(" Cache plugin detected running on site: ", "essb") . ESSBCacheDetector::cache_plugin_name ();
	}

	$settings_update = isset ( $_REQUEST ['settings-updated'] ) ? $_REQUEST ['settings-updated'] : '';
	if ($settings_update == "true") {
		essb_display_status_message(esc_html__('Options are saved!', 'essb'), 'Your new setup is ready to use. If you use cache plugin (example: W3 Total Cache, WP Super Cache, WP Rocket) or optimization plugin (example: Autoptimize, BWP Minify) it is highly recommended to clear cache or you may not see the changes. '.$cache_plugin_message, 'fa fa-info-circle', 'essb-status-update');

	}

	$settings_update = isset ( $_REQUEST ['wizard-updated'] ) ? $_REQUEST ['wizard-updated'] : '';
	if ($settings_update == "true") {
		essb_display_status_message(esc_html__('Your new settings are saved!', 'essb'), 'The initial setup of plugin via quick setup wizard is done. You can make additional adjustments using plugin menu, import ready made styles or just use it that way. If you use cache plugin (example: W3 Total Cache, WP Super Cache, WP Rocket) or optimization plugin (example: Autoptimize, BWP Minify) it is highly recommended to clear cache or you may not see the changes. '.$cache_plugin_message, 'fa fa-info-circle', 'essb-status-update');

	}

	$settings_imported = isset ( $_REQUEST ['settings-imported'] ) ? $_REQUEST ['settings-imported'] : '';
	if ($settings_imported == "true") {
		essb_display_status_message(esc_html__('Options are imported!', 'essb'), 'If you use cache plugin (example: W3 Total Cache, WP Super Cache, WP Rocket) or optimization plugin (example: Autoptimize, BWP Minify) it is highly recommended to clear cache or you may not see the changes. '.$cache_plugin_message, 'fa fa-info-circle');

	}
	if ($reset_settings == 'true') {
		essb_display_status_message(esc_html__('Options are reset to default!', 'essb'), 'If you use cache plugin (example: W3 Total Cache, WP Super Cache, WP Rocket) or optimization plugin (example: Autoptimize, BWP Minify) it is highly recommended to clear cache or you may not see the changes. '.$cache_plugin_message, 'fa fa-info-circle');

	}

	// cache is running
	$general_cache_active = essb_option_bool_value('essb_cache');
	$general_cache_active_static = essb_option_bool_value('essb_cache_static');
	$general_cache_active_static_js = essb_options_bool_value('essb_cache_static_js');
	$general_cache_mode = essb_option_bool_value('essb_cache_mode');
	$is_cache_active = false;

	$general_precompiled_resources = essb_options_bool_value('precompiled_resources');

	$backup = isset ( $_REQUEST ['backup'] ) ? $_REQUEST ['backup'] : '';

	$display_cache_mode = "";
	if ($general_cache_active) {
		if ($general_cache_mode == "full" || $general_cache_mode == '') {
			$display_cache_mode = esc_html__("Cache button render and dynamic resources", "essb");
		} else if ($general_cache_mode == "resource") {
			$display_cache_mode = esc_html__("Cache only dynamic resources", "essb");
		} else {
			$display_cache_mode = esc_html__("Cache only button render", "essb");
		}
		$is_cache_active = true;
	}

	if ($general_cache_active_static || $general_cache_active_static_js) {
		if ($display_cache_mode != '') {
			$display_cache_mode .= ", ";
		}
		$display_cache_mode .= esc_html__("Combine into sigle file all plugin static CSS files", "essb");
		$is_cache_active = true;
	}

	if ($is_cache_active) {
		$cache_clear_address = esc_url_raw ( add_query_arg ( array ('purge-cache' => 'true' ), essb_get_current_page_url () ) );

		$dismiss_addons_button = '<a href="' . $cache_clear_address . '"  text="' . esc_html__( 'Purge Cache', 'essb' ) . '" class="status_button float_right" style="margin-right: 5px;"><i class="fa fa-close"></i>&nbsp;' . esc_html__( 'Purge Cache', 'essb' ) . '</a>';
		essb_display_status_message(esc_html__('Plugin Cache is Running!', 'essb'), sprintf('%2$s %1$s', $dismiss_addons_button, $display_cache_mode), 'fa fa-database');
	}

	if ($general_precompiled_resources) {
		$cache_clear_address = esc_url_raw ( add_query_arg ( array ('rebuild-resource' => 'true' ), essb_get_current_page_url () ) );

		$dismiss_addons_button = '<a href="' . $cache_clear_address . '"  text="' . esc_html__ ( 'Rebuild Resources', 'essb' ) . '" class="status_button float_right" style="margin-right: 5px;"><i class="fa fa-close"></i>&nbsp;' . esc_html__ ( 'Rebuild Resources', 'essb' ) . '</a>';
		essb_display_status_message(esc_html__('Precompiled Resource Mode is Active!', 'essb'), sprintf('In precompiled mode plugin will load default setup into single static files that will run on entire site. %1$s', $dismiss_addons_button), 'fa fa-history');
	}

	if ($backup == 'true') {
		essb_display_status_message(esc_html__('Backup is ready!', 'essb'), 'Backup of your current settings is generated. Copy generated configuration string and save it on your computer. You can use it to restore settings or transfer them to other site.', 'fa fa-gear');
	}


	$rollback_settings = isset($_REQUEST['rollback_setup']) ? $_REQUEST['rollback_setup'] : '';
	$rollback_key = isset($_REQUEST['rollback_key']) ? $_REQUEST['rollback_key'] : '';
	if ($rollback_settings == 'true' && $rollback_key != '') {
		essb_display_status_message(esc_html__('Settings Rollback Completed!', 'essb'), 'Your setup from '.date(DATE_RFC822, $rollback_key).' is restored!', 'fa fa-gear');

	}

	if ($purge_cache == 'true') {
		if (class_exists ( 'ESSBDynamicCache' )) {
			ESSBDynamicCache::flush ();
		}
		if (function_exists ( 'purge_essb_cache_static_cache' )) {
			purge_essb_cache_static_cache ();
		}
		essb_display_status_message(esc_html__('Cache is Cleared!', 'essb'), 'Build in cache of plugin is fully cleared!', 'fa fa-info-circle');

	}

	if ($rebuild_resource == "true") {
		if (class_exists ( 'ESSBPrecompiledResources' )) {
			ESSBPrecompiledResources::flush ();
		}
	}

	if (function_exists('essb3_apply_readymade_style')) {
		essb3_apply_readymade_style();
	}
}
?>

<?php

$deactivate_ajaxsubmit = essb_option_bool_value('deactivate_ajaxsubmit');

if ($current_tab == 'developer') {
	$deactivate_ajaxsubmit = true;
}

?>


<?php

// including the new styles core library
include_once ESSB3_PLUGIN_ROOT . 'lib/admin/styles-library/styles-core.php';

if (essb_admin_advanced_options()) {
	include_once ESSB3_PLUGIN_ROOT . 'lib/admin/advanced-options/advancedoptions-core.php';
}
?>
